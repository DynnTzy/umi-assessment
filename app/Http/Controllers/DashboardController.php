<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentDocument;
use App\Models\Clinic;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Dashboard Asesor
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'asesor') {
            $assessments = Assessment::where('asesor_id', $user->id);

            $totalAssessments = (clone $assessments)->count();

            $latestAssessment = (clone $assessments)
                ->with('clinic')
                ->latest()
                ->first();

            // Normalisasi parameter pencarian dan filter.
            $search = trim($request->input('search', ''));

            $status = $request->input('status', '');
            $instrument = $request->input('instrument', '');

            // Batasi pilihan filter pada nilai yang valid.
            $allowedStatuses = [
                'pending',
                'approved',
                'revision',
            ];

            $allowedInstruments = [
                'regulasi',
                'dokumen',
            ];

            if (! in_array($status, $allowedStatuses, true)) {
                $status = '';
            }

            if (! in_array($instrument, $allowedInstruments, true)) {
                $instrument = '';
            }

            // Ambil dokumen dari assessment yang ditugaskan kepada asesor.
            $documents = AssessmentDocument::query()
                ->whereHas('assessmentItem.assessment', function ($query) use ($user) {
                    $query->where('asesor_id', $user->id);
                })

                // Pencarian nama dokumen, kode EP, atau nama klinik.
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('original_name', 'ilike', "%{$search}%")
                            ->orWhereHas('assessmentItem.element', function ($query) use ($search) {
                                $query->where('code', 'ilike', "%{$search}%");
                            })
                            ->orWhereHas('assessmentItem.assessment.clinic', function ($query) use ($search) {
                                $query->where('name', 'ilike', "%{$search}%");
                            });
                    });
                })

                // Filter berdasarkan status review terbaru.
                ->when($status !== '', function ($query) use ($status) {
                    if ($status === 'pending') {
                        $query->where(function ($query) {
                            $query->whereDoesntHave('latestReview')
                                ->orWhereHas('latestReview', function ($query) {
                                    $query->whereIn('status', [
                                        'pending',
                                        'menunggu_review',
                                        'menunggu review',
                                    ]);
                                });
                        });
                    }

                    if ($status === 'approved') {
                        $query->whereHas('latestReview', function ($query) {
                            $query->whereIn('status', [
                                'approved',
                                'disetujui',
                            ]);
                        });
                    }

                    if ($status === 'revision') {
                        $query->whereHas('latestReview', function ($query) {
                            $query->whereIn('status', [
                                'revision',
                                'revisi',
                                'rejected',
                                'ditolak',
                            ]);
                        });
                    }
                })

                // Filter berdasarkan jenis instrumen.
                ->when($instrument !== '', function ($query) use ($instrument) {
                    $query->where('instrument_type', $instrument);
                })

                // Muat relasi yang digunakan tabel agar tidak terjadi N+1 query.
                ->with([
                    'assessmentItem.assessment.clinic',
                    'assessmentItem.element',
                    'uploader',
                    'latestReview',
                    'activities.user',
                ])

                // Urutkan berdasarkan aktivitas terbaru.
                // Dokumen tanpa aktivitas menggunakan created_at.
                ->orderByRaw(
                    'COALESCE(
                        (
                            SELECT MAX(ada.created_at)
                            FROM assessment_document_activities AS ada
                            WHERE ada.assessment_document_id = assessment_documents.id
                        ),
                        assessment_documents.created_at
                    ) DESC'
                )
                ->orderByDesc('assessment_documents.id')

                // Pagination lima dokumen per halaman.
                ->paginate(5)
                ->withQueryString();

            return view('asesor.dashboard', compact(
                'totalAssessments',
                'latestAssessment',
                'documents',
                'search',
                'status',
                'instrument',
            ));
        }

        /*
        |--------------------------------------------------------------------------
        | Dashboard Klinik
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'klinik') {
            // Pengguna belum dihubungkan ke klinik oleh admin.
            if ($user->clinic_id === null) {
                return view('klinik.pending');
            }

            $clinic = Clinic::findOrFail($user->clinic_id);

            // Cari assessment terbaru untuk klinik ini.
            $latestAssessment = Assessment::where(
                'clinic_id',
                $clinic->id
            )
                ->latest()
                ->first();

            // Jika belum ada assessment, buat untuk asesor yang tersedia.
            if (! $latestAssessment) {
                $asesor = User::where('role', 'asesor')
                    ->orderBy('id')
                    ->first();

                if ($asesor) {
                    $latestAssessment = DB::transaction(
                        function () use ($clinic, $asesor) {
                            $assessment = Assessment::firstOrCreate([
                                'clinic_id' => $clinic->id,
                                'asesor_id' => $asesor->id,
                            ]);

                            if ($assessment->wasRecentlyCreated) {
                                $assessment->generateItems();
                            }

                            return $assessment;
                        }
                    );
                }
            }

            // Statistik total assessment klinik.
            $assessments = Assessment::where(
                'clinic_id',
                $clinic->id
            );

            $totalAssessments = (clone $assessments)->count();

            // Normalisasi parameter pencarian dan filter.
            $search = trim($request->input('search', ''));

            $status = $request->input('status', '');
            $instrument = $request->input('instrument', '');

            // Batasi pilihan filter pada nilai yang valid.
            $allowedStatuses = [
                'pending',
                'approved',
                'revision',
            ];

            $allowedInstruments = [
                'regulasi',
                'dokumen',
            ];

            if (! in_array($status, $allowedStatuses, true)) {
                $status = '';
            }

            if (! in_array($instrument, $allowedInstruments, true)) {
                $instrument = '';
            }

            // Ambil dokumen bukti milik klinik ini.
            $documents = AssessmentDocument::query()
                ->whereHas('assessmentItem.assessment', function ($query) use ($clinic) {
                    $query->where('clinic_id', $clinic->id);
                })

                // Pencarian nama dokumen atau kode EP.
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('original_name', 'ilike', "%{$search}%")
                            ->orWhereHas('assessmentItem.element', function ($query) use ($search) {
                                $query->where('code', 'ilike', "%{$search}%");
                            });
                    });
                })

                // Filter berdasarkan status review terbaru.
                ->when($status !== '', function ($query) use ($status) {
                    if ($status === 'pending') {
                        $query->where(function ($query) {
                            $query->whereDoesntHave('latestReview')
                                ->orWhereHas('latestReview', function ($query) {
                                    $query->whereIn('status', [
                                        'pending',
                                        'menunggu_review',
                                        'menunggu review',
                                    ]);
                                });
                        });
                    }

                    if ($status === 'approved') {
                        $query->whereHas('latestReview', function ($query) {
                            $query->whereIn('status', [
                                'approved',
                                'disetujui',
                            ]);
                        });
                    }

                    if ($status === 'revision') {
                        $query->whereHas('latestReview', function ($query) {
                            $query->whereIn('status', [
                                'revision',
                                'revisi',
                                'rejected',
                                'ditolak',
                            ]);
                        });
                    }
                })

                // Filter berdasarkan jenis instrumen.
                ->when($instrument !== '', function ($query) use ($instrument) {
                    $query->where('instrument_type', $instrument);
                })

                // Muat relasi yang digunakan tabel agar tidak terjadi N+1 query.
                ->with([
                    'assessmentItem.assessment.clinic',
                    'assessmentItem.element',
                    'uploader',
                    'latestReview',
                    'activities.user',
                ])

                // Urutkan berdasarkan aktivitas terbaru.
                // Dokumen tanpa aktivitas menggunakan created_at.
                ->orderByRaw(
                    'COALESCE(
                        (
                            SELECT MAX(ada.created_at)
                            FROM assessment_document_activities AS ada
                            WHERE ada.assessment_document_id = assessment_documents.id
                        ),
                        assessment_documents.created_at
                    ) DESC'
                )
                ->orderByDesc('assessment_documents.id')

                // Pagination lima dokumen per halaman.
                ->paginate(5, ['*'], 'documents_page')
                ->withQueryString();

            return view('klinik.dashboard', compact(
                'clinic',
                'totalAssessments',
                'latestAssessment',
                'documents',
                'search',
                'status',
                'instrument',
            ));
        }

        /*
        |--------------------------------------------------------------------------
        | Dashboard Admin
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'admin') {
            $totalUsers = User::count();

            $pendingClinicUsers = User::where('role', 'klinik')
                ->whereNull('clinic_id')
                ->count();

            $totalClinics = Clinic::count();

            $pendingUsers = User::where('role', 'klinik')
                ->whereNull('clinic_id')
                ->latest()
                ->get();

            return view('admin.dashboard-admin', compact(
                'totalUsers',
                'pendingClinicUsers',
                'totalClinics',
                'pendingUsers',
            ));
        }

        /*
        |--------------------------------------------------------------------------
        | Unknown Role
        |--------------------------------------------------------------------------
        */

        abort(403);
    }
}
