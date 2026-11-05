<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentDocumentActivity;
use App\Models\AssessmentItem;
use App\Models\Clinic;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AssessmentController extends Controller
{
    /**
     * Menampilkan daftar assessment.
     */
    public function index(): View|RedirectResponse
    {
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | Klinik: buka assessment milik klinik sendiri
        |--------------------------------------------------------------------------
        */
        if ($user->role === 'klinik') {
            abort_if($user->clinic_id === null, 403);

            $clinic = Clinic::findOrFail($user->clinic_id);

            $assessment = Assessment::where('clinic_id', $clinic->id)
                ->latest()
                ->first();

            /*
             * Buat assessment jika belum tersedia.
             */
            if (! $assessment) {
                $asesor = User::where('role', 'asesor')->first();

                abort_if(
                    ! $asesor,
                    403,
                    'Akun asesor belum tersedia.'
                );

                $assessment = DB::transaction(function () use (
                    $clinic,
                    $asesor
                ) {
                    $assessment = Assessment::firstOrCreate([
                        'clinic_id' => $clinic->id,
                        'asesor_id' => $asesor->id,
                    ]);

                    if ($assessment->wasRecentlyCreated) {
                        $assessment->generateItems();
                    }

                    return $assessment;
                });
            }

            return redirect()->route('assessments.show', $assessment);
        }

        /*
        |--------------------------------------------------------------------------
        | Asesor: daftar klinik yang ditugaskan
        |--------------------------------------------------------------------------
        */
        abort_unless($user->role === 'asesor', 403);

        // Pertahankan pembuatan assessment untuk klinik aktif.
        $clinics = Clinic::where('is_active', true)
            ->orderBy('name')
            ->get();

        foreach ($clinics as $clinic) {
            $assessment = Assessment::firstOrCreate([
                'clinic_id' => $clinic->id,
                'asesor_id' => $user->id,
            ]);

            if ($assessment->wasRecentlyCreated) {
                $assessment->generateItems();
            }
        }

        /*
         * Statistik total assessment.
         * Tidak lagi membaca status assessment.
         */
        $totalAssessments = Assessment::where(
            'asesor_id',
            $user->id
        )->count();

        /*
         * Pencarian nama klinik.
         */
        $search = trim((string) request()->query('search', ''));

        $assessmentQuery = Assessment::with([
            'clinic',
            'asesor',
        ])
            ->where('asesor_id', $user->id);

        if ($search !== '') {
            $assessmentQuery->whereHas('clinic', function ($query) use ($search) {
                $query->where('name', 'ilike', '%'.$search.'%');
            });
        }

        /*
         * Pagination 5 data per halaman.
         * Query pencarian tetap dipertahankan saat pindah halaman.
         */
        $assessments = $assessmentQuery
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->paginate(5)
            ->withQueryString();

        return view('asesor.assessments.index', compact(
            'assessments',
            'search',
            'totalAssessments',
        ));
    }

    /**
     * Membuat assessment baru.
     *
     * Hanya asesor yang dapat membuat assessment
     * untuk klinik yang aktif.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        abort_unless($user->role === 'asesor', 403);

        $validated = $request->validate([
            'clinic_id' => [
                'required',
                'exists:clinics,id',
            ],
        ]);

        /*
         * Ambil klinik yang dipilih.
         */
        $clinic = Clinic::findOrFail($validated['clinic_id']);

        /*
         * Klinik nonaktif tidak boleh mendapatkan
         * assessment baru.
         */
        if (! $clinic->is_active) {
            return redirect()
                ->route('assessments.index')
                ->with(
                    'error',
                    'Assessment baru tidak dapat dibuat karena klinik sedang nonaktif.'
                );
        }

        /*
         * Cegah pembuatan assessment duplikat
         * untuk asesor dan klinik yang sama.
         */
        $existingAssessment = Assessment::where(
            'clinic_id',
            $clinic->id
        )
            ->where('asesor_id', $user->id)
            ->first();

        if ($existingAssessment) {
            return redirect()
                ->route('assessments.show', $existingAssessment)
                ->with(
                    'info',
                    'Assessment untuk klinik ini sudah tersedia.'
                );
        }

        /*
         * Buat assessment beserta seluruh item
         * dalam satu transaksi database.
         */
        $assessment = DB::transaction(function () use (
            $clinic,
            $user
        ) {
            $assessment = Assessment::create([
                'clinic_id' => $clinic->id,
                'asesor_id' => $user->id,
            ]);

            $assessment->generateItems();

            return $assessment;
        });

        return redirect()
            ->route('assessments.show', $assessment)
            ->with('success', 'Assessment berhasil dibuat.');
    }

    /**
     * Menampilkan detail assessment
     * beserta seluruh item.
     */
    public function show(Assessment $assessment): View
    {
        $this->authorizeAssessmentAccess($assessment);

        $assessment->load([
            'clinic',
            'asesor',
            'items.element.chapter',
            'items.documents.reviews',
        ]);

        $chapters = $assessment->items
            ->groupBy(fn ($item) => $item->element->chapter_id)
            ->sortKeys();

        /*
         * Klinik menggunakan halaman detail khusus.
         */
        if (auth()->user()->role === 'klinik') {
            return view('klinik.assessments.show', compact(
                'assessment',
                'chapters'
            ));
        }

        /*
         * Asesor tetap menggunakan halaman detail
         * yang sudah ada.
         */
        return view('asesor.assessments.show', compact(
            'assessment',
            'chapters'
        ));
    }

    /**
     * Menampilkan detail satu item assessment.
     */
    public function item(
        Assessment $assessment,
        AssessmentItem $item
    ): View {
        $this->authorizeAssessmentAccess($assessment);

        /*
         * Pastikan item merupakan bagian dari assessment ini.
         */
        abort_unless(
            (int) $item->assessment_id === (int) $assessment->id,
            404
        );

        /*
         * Muat detail elemen, dokumen, pengunggah,
         * serta review terbaru dan identitas asesor.
         */
        $item->load([
            'element.chapter',
            'documents.uploader',
            'documents.latestReview.reviewer',
            'documents.reviews.reviewer',
            'documents.activities.user',
        ]);

        /*
         * Klinik tetap menggunakan halaman detail miliknya.
         */
        if (auth()->user()->role === 'klinik') {
            return view('klinik.assessments.item', compact(
                'assessment',
                'item'
            ));
        }

        /*
         * Asesor menggunakan halaman detail dokumen.
         */
        return view('asesor.assessments.item', compact(
            'assessment',
            'item'
        ));
    }

    /**
     * Menampilkan form penilaian satu item.
     */
    public function evaluate(
        Assessment $assessment,
        AssessmentItem $item
    ): View {
        $this->authorizeAssessmentAccess($assessment);

        // Pastikan item memang milik assessment yang sedang dibuka.
        abort_unless(
            (int) $item->assessment_id === (int) $assessment->id,
            404
        );

        // Halaman evaluasi hanya digunakan oleh asesor.
        abort_unless(
            auth()->user()->role === 'asesor',
            403
        );

        // Muat dokumen, review, dan timeline aktivitas beserta pelakunya.
        $item->load([
            'element.chapter',
            'documents.uploader',
            'documents.reviews',
            'documents.activities.user',
        ]);

        return view('asesor.assessments.evaluate', compact(
            'assessment',
            'item'
        ));
    }

    /**
     * Menyimpan hasil penilaian elemen sekaligus
     * memperbarui review dokumen yang terkait.
     */
    public function updateItem(
        Request $request,
        Assessment $assessment,
        AssessmentItem $item
    ): RedirectResponse {
        $user = auth()->user();

        /*
         * Hanya asesor yang dapat melakukan penilaian.
         */
        abort_unless(
            $user->role === 'asesor',
            403
        );

        /*
         * Pastikan asesor yang login memang
         * ditugaskan pada assessment ini.
         */
        abort_unless(
            (int) $assessment->asesor_id === (int) $user->id,
            403
        );

        /*
         * Pastikan item merupakan bagian dari assessment.
         */
        abort_unless(
            (int) $item->assessment_id === (int) $assessment->id,
            404
        );

        $validated = $request->validate([
            'status' => [
                'required',
                'in:pending,sesuai,tidak_sesuai,na',
            ],
            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        DB::transaction(function () use (
            $item,
            $user,
            $validated
        ) {
            $notes = $validated['notes'] ?? null;

            /*
             * 1. Simpan hasil penilaian elemen.
             */
            $item->update([
                'status' => $validated['status'],
                'notes' => $notes,
            ]);

            /*
             * 2. Petakan status EP ke status review dokumen.
             *
             * sesuai        -> approved
             * tidak_sesuai  -> revision
             * na            -> approved
             * pending       -> pending
             */
            $reviewStatus = match ($validated['status']) {
                'sesuai', 'na' => 'approved',
                'tidak_sesuai' => 'revision',
                default => 'pending',
            };

            /*
             * 3. Buat review terbaru untuk setiap dokumen
             * pada item ini. Riwayat review sebelumnya
             * tetap dipertahankan.
             */
            $documents = $item->documents()->get();

            foreach ($documents as $document) {
                // Simpan review terbaru untuk dokumen.
                $document->reviews()->create([
                    'reviewer_id' => $user->id,
                    'status' => $reviewStatus,
                    'notes' => $notes,
                ]);

                // Catat aktivitas review agar masuk ke timeline bersama.
                AssessmentDocumentActivity::create([
                    'assessment_document_id' => $document->id,
                    'user_id' => $user->id,
                    'type' => 'reviewed',
                    'file_name' => $document->original_name,
                    'status' => $reviewStatus,
                    'notes' => $notes,
                ]);
            }

            // Status assessment tidak lagi disinkronkan di sini.
        });

        return redirect()
            ->route('assessments.items.show', [
                'assessment' => $assessment,
                'item' => $item,
            ])
            ->with('success', 'Penilaian berhasil disimpan.');
    }

    /**
     * Memastikan user memiliki akses
     * terhadap assessment.
     */
    private function authorizeAssessmentAccess(
        Assessment $assessment
    ): void {
        $user = auth()->user();

        /*
         * Asesor hanya dapat mengakses assessment
         * yang ditugaskan kepadanya.
         */
        if ($user->role === 'asesor') {
            abort_unless(
                (int) $assessment->asesor_id === (int) $user->id,
                403
            );

            return;
        }

        /*
         * Pengguna klinik hanya dapat mengakses
         * assessment milik klinik yang terhubung.
         * Status nonaktif tidak menghapus akses
         * ke assessment yang sudah ada.
         */
        if ($user->role === 'klinik') {
            abort_unless(
                $user->clinic_id !== null
                    && (int) $assessment->clinic_id === (int) $user->clinic_id,
                403
            );

            return;
        }

        abort(403);
    }
}
