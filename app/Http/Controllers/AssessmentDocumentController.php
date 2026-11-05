<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentDocument;
use App\Models\AssessmentDocumentActivity;
use App\Models\AssessmentDocumentReview;
use App\Models\AssessmentItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class AssessmentDocumentController extends Controller
{
    /**
     * Klinik mengunggah dokumen bukti baru untuk sebuah EP.
     * Setiap dokumen baru menjadi satu record tersendiri.
     */
    public function store(
        Request $request,
        Assessment $assessment,
        AssessmentItem $item
    ): RedirectResponse {
        $user = $request->user();

        abort_unless($user->role === 'klinik', 403);

        abort_unless(
            $user->clinic_id !== null
            && (int) $assessment->clinic_id === (int) $user->clinic_id,
            403
        );

        abort_unless(
            (int) $item->assessment_id === (int) $assessment->id,
            404
        );

        $assessment->loadMissing('clinic');

        abort_unless(
            $assessment->clinic?->is_active,
            403,
            'Klinik nonaktif tidak dapat mengunggah dokumen.'
        );

        $validated = $request->validate([
            'instrument_type' => [
                'required',
                'in:regulasi,dokumen',
            ],
            'document' => [
                'required',
                'file',
                'max:10240',
                'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
            ],
            'upload_note' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ], [
            'instrument_type.required' => 'Silakan pilih jenis instrumen.',
            'instrument_type.in' => 'Jenis instrumen yang dipilih tidak valid.',
            'document.required' => 'Silakan pilih file dokumen.',
            'document.max' => 'Ukuran dokumen maksimal 10 MB.',
            'document.mimes' => 'Format dokumen harus PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, atau PNG.',
            'upload_note.max' => 'Catatan upload maksimal 2000 karakter.',
        ]);

        $file = $validated['document'];

        $path = $file->store(
            "assessments/{$assessment->id}/items/{$item->id}",
            'local'
        );

        try {
            DB::transaction(function () use (
                $assessment,
                $item,
                $user,
                $file,
                $path,
                $validated
            ) {
                $document = AssessmentDocument::create([
                    'assessment_item_id' => $item->id,
                    'upload_action' => 'initial',
                    'upload_note' => $validated['upload_note'] ?? null,
                    'uploaded_by' => $user->id,
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'instrument_type' => $validated['instrument_type'],
                ]);

                // Catat aktivitas upload awal.
                AssessmentDocumentActivity::create([
                    'assessment_document_id' => $document->id,
                    'user_id' => $user->id,
                    'type' => 'uploaded',
                    'file_name' => $file->getClientOriginalName(),
                    'status' => 'pending',
                    'notes' => $validated['upload_note'] ?? null,
                ]);

                $this->createPendingReview($document, $assessment);
            });
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($path);

            throw $exception;
        }

        return redirect()
            ->route('assessments.items.show', [
                'assessment' => $assessment,
                'item' => $item,
            ])
            ->with('success', 'Dokumen berhasil diupload.');
    }

    /**
     * Mengganti file dokumen yang sama.
     * Tidak membuat record dokumen baru.
     */
    public function reupload(
        Request $request,
        AssessmentDocument $document
    ): RedirectResponse {
        return $this->replaceDocumentFile(
            $request,
            $document,
            'reupload'
        );
    }

    /**
     * Mengganti file setelah asesor meminta revisi.
     * Tidak membuat record dokumen baru.
     */
    public function revision(
        Request $request,
        AssessmentDocument $document
    ): RedirectResponse {
        return $this->replaceDocumentFile(
            $request,
            $document,
            'revision'
        );
    }

    /**
     * Mengganti file pada record yang sama.
     *
     * Riwayat review lama tetap tersimpan.
     * File lama baru dihapus setelah penggantian berhasil.
     */
    private function replaceDocumentFile(
        Request $request,
        AssessmentDocument $document,
        string $action
    ): RedirectResponse {
        $user = $request->user();

        abort_unless($user->role === 'klinik', 403);

        $document->loadMissing([
            'assessmentItem.assessment.clinic',
        ]);

        $item = $document->assessmentItem;
        $assessment = $item?->assessment;

        abort_unless(
            $item !== null && $assessment !== null,
            404,
            'Dokumen tidak ditemukan.'
        );

        abort_unless(
            $user->clinic_id !== null
            && (int) $assessment->clinic_id === (int) $user->clinic_id,
            403
        );

        $assessment->loadMissing('clinic');

        abort_unless(
            $assessment->clinic?->is_active,
            403,
            'Klinik nonaktif tidak dapat mengunggah dokumen.'
        );

        $validated = $request->validate([
            'document' => [
                'required',
                'file',
                'max:10240',
                'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
            ],
            'upload_note' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ], [
            'document.required' => 'Silakan pilih file dokumen.',
            'document.max' => 'Ukuran dokumen maksimal 10 MB.',
            'document.mimes' => 'Format dokumen harus PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, atau PNG.',
            'upload_note.max' => 'Catatan upload maksimal 2000 karakter.',
        ]);

        $file = $validated['document'];

        $newPath = $file->store(
            "assessments/{$assessment->id}/items/{$item->id}",
            'local'
        );

        $oldPath = null;

        try {
            DB::transaction(function () use (
                $document,
                $assessment,
                $user,
                $file,
                $newPath,
                $validated,
                $action,
                &$oldPath
            ) {
                // Kunci dokumen untuk mencegah dua penggantian
                // file berlangsung bersamaan.
                $lockedDocument = AssessmentDocument::query()
                    ->whereKey($document->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                // Periksa ulang status terbaru di dalam transaksi.
                $latestReview = AssessmentDocumentReview::query()
                    ->where(
                        'assessment_document_id',
                        $lockedDocument->id
                    )
                    ->orderByDesc('id')
                    ->lockForUpdate()
                    ->first();

                $currentStatus = $latestReview?->status ?? 'pending';

                // Dokumen yang sudah disetujui terkunci.
                abort_unless(
                    $currentStatus !== 'approved',
                    403,
                    'Dokumen yang sudah disetujui terkunci dan tidak dapat diubah.'
                );

                // Revisi hanya boleh dilakukan jika asesor
                // memang meminta perbaikan.
                if ($action === 'revision') {
                    abort_unless(
                        in_array(
                            $currentStatus,
                            ['revision', 'rejected'],
                            true
                        ),
                        403,
                        'Unggah revisi hanya dapat dilakukan ketika dokumen membutuhkan revisi.'
                    );
                }

                $oldPath = $lockedDocument->file_path;

                // Perbarui record dokumen yang sama.
                $lockedDocument->update([
                    'upload_action' => $action,
                    'upload_note' => $validated['upload_note'] ?? null,
                    'uploaded_by' => $user->id,
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $newPath,
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);

                // Catat apakah tindakan merupakan upload ulang
                // atau revisi setelah permintaan asesor.
                AssessmentDocumentActivity::create([
                    'assessment_document_id' => $lockedDocument->id,
                    'user_id' => $user->id,
                    'type' => $action === 'revision'
                        ? 'revision_uploaded'
                        : 'reuploaded',
                    'file_name' => $file->getClientOriginalName(),
                    'status' => 'pending',
                    'notes' => $validated['upload_note'] ?? null,
                ]);

                // Review sebelumnya tetap tersimpan.
                // Review pending baru menjadi status terkini.
                $this->createPendingReview(
                    $lockedDocument,
                    $assessment
                );
            });
        } catch (Throwable $exception) {
            // Jika transaksi gagal, pertahankan file lama.
            Storage::disk('local')->delete($newPath);

            throw $exception;
        }

        // Hapus file lama setelah transaksi database berhasil.
        if (
            $oldPath
            && $oldPath !== $newPath
            && Storage::disk('local')->exists($oldPath)
        ) {
            Storage::disk('local')->delete($oldPath);
        }

        return redirect()
            ->route('assessments.items.show', [
                'assessment' => $assessment,
                'item' => $item,
            ])
            ->with(
                'success',
                $action === 'revision'
                    ? 'Dokumen berhasil direvisi dan menunggu review asesor.'
                    : 'Dokumen berhasil diupload ulang dan menunggu review asesor.'
            );
    }

    /**
     * Mencatat review pending setelah upload awal atau
     * penggantian file jika asesor sudah ditugaskan.
     */
    private function createPendingReview(
        AssessmentDocument $document,
        Assessment $assessment
    ): void {
        if ($assessment->asesor_id === null) {
            return;
        }

        AssessmentDocumentReview::create([
            'assessment_document_id' => $document->id,
            'reviewer_id' => $assessment->asesor_id,
            'status' => 'pending',
            'notes' => null,
        ]);
    }

    /**
     * Mengunduh dokumen secara privat.
     */
    public function download(AssessmentDocument $document)
    {
        $document->loadMissing([
            'assessmentItem.assessment',
        ]);

        $item = $document->assessmentItem;
        $assessment = $item?->assessment;
        $user = request()->user();

        abort_unless($assessment !== null, 404);

        if ($user->role === 'asesor') {
            abort_unless(
                (int) $assessment->asesor_id === (int) $user->id,
                403
            );
        } elseif ($user->role === 'klinik') {
            abort_unless(
                $user->clinic_id !== null
                && (int) $assessment->clinic_id === (int) $user->clinic_id,
                403
            );
        } else {
            abort(403);
        }

        $disk = Storage::disk('local');

        abort_unless(
            $disk->exists($document->file_path),
            404,
            'File dokumen tidak ditemukan.'
        );

        return $disk->download(
            $document->file_path,
            $document->original_name
        );
    }

    /**
     * Menampilkan preview PDF secara privat.
     */
    public function preview(AssessmentDocument $document)
    {
        $document->loadMissing([
            'assessmentItem.assessment',
        ]);

        $item = $document->assessmentItem;
        $assessment = $item?->assessment;
        $user = request()->user();

        abort_unless($assessment !== null, 404);

        if ($user->role === 'asesor') {
            abort_unless(
                (int) $assessment->asesor_id === (int) $user->id,
                403
            );
        } elseif ($user->role === 'klinik') {
            abort_unless(
                $user->clinic_id !== null
                && (int) $assessment->clinic_id === (int) $user->clinic_id,
                403
            );
        } else {
            abort(403);
        }

        abort_unless(
            strtolower($document->mime_type ?? '') === 'application/pdf',
            415,
            'Preview saat ini hanya mendukung file PDF.'
        );

        $disk = Storage::disk('local');

        abort_unless(
            $disk->exists($document->file_path),
            404,
            'File dokumen tidak ditemukan.'
        );

        $path = $disk->path($document->file_path);

        $filename = str_replace(
            ['"', "\r", "\n"],
            '',
            basename($document->original_name)
        );

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    /**
     * Menghapus dokumen bukti.
     */
    public function destroy(
        AssessmentDocument $document
    ): RedirectResponse {
        $user = request()->user();

        abort_unless($user->role === 'klinik', 403);

        $document->loadMissing([
            'assessmentItem.assessment.clinic',
        ]);

        $item = $document->assessmentItem;
        $assessment = $item?->assessment;

        abort_unless(
            $assessment !== null && $item !== null,
            404
        );

        abort_unless(
            $user->clinic_id !== null
            && (int) $assessment->clinic_id === (int) $user->clinic_id,
            403
        );

        abort_unless(
            $assessment->clinic?->is_active,
            403,
            'Klinik nonaktif tidak dapat menghapus dokumen.'
        );

        $path = $document->file_path;

        DB::transaction(function () use ($document, $item) {
            $lockedItem = AssessmentItem::query()
                ->whereKey($item->id)
                ->lockForUpdate()
                ->firstOrFail();

            AssessmentDocumentReview::where(
                'assessment_document_id',
                $document->id
            )->delete();

            $document->delete();

            if (! $lockedItem->documents()->exists()) {
                $lockedItem->update([
                    'status' => 'pending',
                    'notes' => null,
                ]);
            }
        });

        if ($path && Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }

        return redirect()
            ->route('assessments.items.show', [
                'assessment' => $assessment,
                'item' => $item,
            ])
            ->with('success', 'Dokumen berhasil dihapus.');
    }

    /**
     * Menampilkan halaman penilaian untuk satu dokumen.
     */
    public function evaluate(
        AssessmentDocument $document
    ): View {
        $user = request()->user();

        abort_unless($user->role === 'asesor', 403);

        $document->load([
            'assessmentItem.assessment.clinic',
            'assessmentItem.element.chapter',
            'uploader',
            'reviews.reviewer',
            'activities.user',
        ]);

        $item = $document->assessmentItem;
        $assessment = $item?->assessment;

        abort_unless(
            $item !== null && $assessment !== null,
            404,
            'Dokumen tidak ditemukan.'
        );

        abort_unless(
            (int) $assessment->asesor_id === (int) $user->id,
            403
        );

        $reviews = $document->reviews
            ->sortByDesc('id')
            ->values();

        $latestReview = $reviews->first();

        $activities = $document->activities;

        return view('asesor.assessments.evaluate', [
            'assessment' => $assessment,
            'item' => $item,
            'document' => $document,
            'reviews' => $reviews,
            'latestReview' => $latestReview,
            'activities' => $activities,
        ]);
    }

    /**
     * Menyimpan keputusan review untuk satu dokumen saja.
     */
    public function updateReview(
        Request $request,
        AssessmentDocument $document
    ): RedirectResponse {
        $user = $request->user();

        abort_unless($user->role === 'asesor', 403);

        $document->loadMissing([
            'assessmentItem.assessment',
        ]);

        $item = $document->assessmentItem;
        $assessment = $item?->assessment;

        abort_unless(
            $item !== null && $assessment !== null,
            404,
            'Dokumen tidak ditemukan.'
        );

        abort_unless(
            (int) $assessment->asesor_id === (int) $user->id,
            403
        );

        $validated = $request->validate([
            'status' => [
                'required',
                'in:pending,approved,revision',
            ],
            'notes' => [
                'nullable',
                'string',
                'required_if:status,revision',
                'max:2000',
            ],
        ], [
            'status.required' => 'Silakan pilih status review dokumen.',
            'status.in' => 'Status review yang dipilih tidak valid.',
            'notes.required_if' => 'Catatan wajib diisi jika dokumen membutuhkan revisi.',
            'notes.max' => 'Catatan review maksimal 2000 karakter.',
        ]);

        DB::transaction(function () use (
            $document,
            $user,
            $validated
        ) {
            $lockedDocument = AssessmentDocument::query()
                ->whereKey($document->id)
                ->lockForUpdate()
                ->firstOrFail();

            AssessmentDocumentReview::create([
                'assessment_document_id' => $lockedDocument->id,
                'reviewer_id' => $user->id,
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);

            AssessmentDocumentActivity::create([
                'assessment_document_id' => $lockedDocument->id,
                'user_id' => $user->id,
                'type' => 'reviewed',
                'file_name' => $lockedDocument->original_name,
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return redirect()
            ->route('assessments.documents.evaluate', [
                'document' => $document,
            ])
            ->with('success', 'Review dokumen berhasil disimpan.');
    }

    /**
     * Asesor menyimpan keputusan review baru.
     * Review sebelumnya tetap tersimpan sebagai histori.
     */
    public function review(
        Request $request,
        AssessmentDocument $document
    ): RedirectResponse {
        $user = $request->user();

        abort_unless($user->role === 'asesor', 403);

        $document->loadMissing([
            'assessmentItem.assessment',
        ]);

        $item = $document->assessmentItem;
        $assessment = $item?->assessment;

        abort_unless(
            $assessment !== null && $item !== null,
            404
        );

        abort_unless(
            (int) $assessment->asesor_id === (int) $user->id,
            403
        );

        $validated = $request->validate([
            'status' => [
                'required',
                'in:pending,approved,revision',
            ],
            'notes' => [
                'nullable',
                'string',
                'required_if:status,revision',
                'max:2000',
            ],
        ], [
            'status.required' => 'Silakan pilih status review dokumen.',
            'status.in' => 'Status review yang dipilih tidak valid.',
            'notes.required_if' => 'Catatan wajib diisi jika dokumen membutuhkan revisi.',
            'notes.max' => 'Catatan review maksimal 2000 karakter.',
        ]);

        DB::transaction(function () use (
            $document,
            $user,
            $validated
        ) {
            $lockedDocument = AssessmentDocument::query()
                ->whereKey($document->id)
                ->lockForUpdate()
                ->firstOrFail();

            // Simpan keputusan review tanpa menghapus review sebelumnya.
            AssessmentDocumentReview::create([
                'assessment_document_id' => $lockedDocument->id,
                'reviewer_id' => $user->id,
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Catat keputusan asesor sebagai aktivitas.
            AssessmentDocumentActivity::create([
                'assessment_document_id' => $lockedDocument->id,
                'user_id' => $user->id,
                'type' => 'reviewed',
                'file_name' => $lockedDocument->original_name,
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return redirect()
            ->route('assessments.items.show', [
                'assessment' => $assessment,
                'item' => $item,
            ])
            ->with('success', 'Review dokumen berhasil disimpan.');
    }

    /**
     * Menampilkan satu dokumen, histori review, dan aktivitasnya.
     */
    public function showReview(
        AssessmentDocument $document
    ): View {
        $document->loadMissing([
            'assessmentItem.assessment.clinic',
            'assessmentItem.element.chapter',
            'uploader',
            'reviews.reviewer',
            'activities.user',
        ]);

        $item = $document->assessmentItem;
        $assessment = $item?->assessment;
        $user = request()->user();

        abort_unless(
            $item !== null && $assessment !== null,
            404,
            'Dokumen tidak ditemukan.'
        );

        if ($user->role === 'asesor') {
            abort_unless(
                (int) $assessment->asesor_id === (int) $user->id,
                403
            );
        } elseif ($user->role === 'klinik') {
            abort_unless(
                $user->clinic_id !== null
                && (int) $assessment->clinic_id === (int) $user->clinic_id,
                403
            );
        } else {
            abort(403);
        }

        $reviews = $document->reviews
            ->sortByDesc('id')
            ->values();

        $activities = $document->activities;

        return view('klinik.assessments.document-review', [
            'document' => $document,
            'item' => $item,
            'assessment' => $assessment,
            'reviews' => $reviews,
            'latestReview' => $reviews->first(),
            'activities' => $activities,
        ]);
    }
}
