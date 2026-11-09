<?php

use App\Http\Controllers\Admin\ClinicController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\AssessmentDocumentController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/login')
    ->name('home');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Assessment
    |--------------------------------------------------------------------------
    */

    // Hanya asesor yang dapat membuat assessment baru.
    Route::post('/assessments', [AssessmentController::class, 'store'])
        ->middleware('role:asesor')
        ->name('assessments.store');

    // Asesor dan klinik dapat melihat daftar assessment.
    Route::get('/assessments', [AssessmentController::class, 'index'])
        ->middleware('role:asesor,klinik')
        ->name('assessments.index');

    // Asesor dan klinik dapat membuka detail assessment.
    Route::get('/assessments/{assessment}', [AssessmentController::class, 'show'])
        ->middleware('role:asesor,klinik')
        ->name('assessments.show');

    // Asesor dan klinik dapat membuka detail EP.
    Route::get(
        '/assessments/{assessment}/items/{item}',
        [AssessmentController::class, 'item']
    )
        ->middleware('role:asesor,klinik')
        ->name('assessments.items.show');

    // Asesor dan klinik dapat membuka halaman penilaian EP.
    Route::get(
        '/assessments/{assessment}/items/{item}/evaluate',
        [AssessmentController::class, 'evaluate']
    )
        ->middleware('role:asesor,klinik')
        ->name('assessments.items.evaluate');

    // Hanya asesor yang dapat menyimpan hasil penilaian.
    Route::post(
        '/assessments/{assessment}/items/{item}',
        [AssessmentController::class, 'updateItem']
    )
        ->middleware('role:asesor')
        ->name('assessments.items.update');

    /*
    |--------------------------------------------------------------------------
    | Assessment Documents
    |--------------------------------------------------------------------------
    */

    // Klinik dapat mengunggah dokumen baru.
    Route::post(
        '/assessments/{assessment}/items/{item}/documents',
        [AssessmentDocumentController::class, 'store']
    )
        ->middleware('role:klinik')
        ->name('assessments.documents.store');

    // Klinik dapat mengunggah ulang dokumen sebagai versi baru.
    Route::post(
        '/assessment-documents/{document}/reupload',
        [AssessmentDocumentController::class, 'reupload']
    )
        ->middleware('role:klinik')
        ->name('assessments.documents.reupload');

    // Klinik dapat mengunggah versi revisi sebagai versi baru.
    Route::post(
        '/assessment-documents/{document}/revision',
        [AssessmentDocumentController::class, 'revision']
    )
        ->middleware('role:klinik')
        ->name('assessments.documents.revision');

    // Klinik dan asesor dapat mengunduh dokumen.
    Route::get(
        '/assessment-documents/{document}/download',
        [AssessmentDocumentController::class, 'download']
    )
        ->middleware('role:klinik,asesor')
        ->name('assessments.documents.download');

    // Klinik dan asesor dapat melihat halaman hasil review dokumen.
    Route::get(
        '/assessment-documents/{document}/review',
        [AssessmentDocumentController::class, 'showReview']
    )
        ->middleware('role:klinik,asesor')
        ->name('assessments.documents.review.show');

    // Klinik dan asesor dapat melihat preview PDF dokumen.
    Route::get(
        '/assessment-documents/{document}/preview',
        [AssessmentDocumentController::class, 'preview']
    )
        ->middleware('role:klinik,asesor')
        ->name('assessments.documents.preview');

    // Hanya klinik yang dapat menghapus dokumen melalui route ini.
    Route::delete(
        '/assessment-documents/{document}',
        [AssessmentDocumentController::class, 'destroy']
    )
        ->middleware('role:klinik')
        ->name('assessments.documents.destroy');

    // Hanya asesor yang dapat melakukan review dokumen.
    Route::post(
        '/assessment-documents/{document}/review',
        [AssessmentDocumentController::class, 'review']
    )
        ->middleware('role:asesor')
        ->name('assessments.documents.review');

    // Asesor membuka halaman penilaian untuk satu dokumen.
    Route::get(
        '/assessment-documents/{document}/evaluate',
        [AssessmentDocumentController::class, 'evaluate']
    )
        ->middleware('role:asesor')
        ->name('assessments.documents.evaluate');

    // Asesor menyimpan hasil penilaian satu dokumen.
    Route::post(
        '/assessment-documents/{document}/evaluate',
        [AssessmentDocumentController::class, 'updateReview']
    )
        ->middleware('role:asesor')
        ->name('assessments.documents.evaluate.update');

    /*
    |--------------------------------------------------------------------------
    | Manajemen Klinik
    |--------------------------------------------------------------------------
    */

    // Admin dan asesor dapat melihat daftar klinik.
    Route::get('/admin/clinics', [ClinicController::class, 'index'])
        ->middleware('role:admin,asesor')
        ->name('admin.clinics.index');

    Route::post('/admin/clinics', [ClinicController::class, 'store'])
        ->middleware('role:admin,asesor')
        ->name('admin.clinics.store');

    // Hanya admin yang dapat mengedit klinik.
    Route::get('/admin/clinics/{clinic}/edit', [ClinicController::class, 'edit'])
        ->middleware('role:admin')
        ->name('admin.clinics.edit');

    Route::put('/admin/clinics/{clinic}', [ClinicController::class, 'update'])
        ->middleware('role:admin')
        ->name('admin.clinics.update');

    // Hanya admin yang dapat mengubah status klinik.
    Route::patch(
        '/admin/clinics/{clinic}/toggle-status',
        [ClinicController::class, 'toggleStatus']
    )
        ->middleware('role:admin')
        ->name('admin.clinics.toggle-status');

    // Admin dan asesor dapat melihat detail klinik.
    Route::get('/admin/clinics/{clinic}', [ClinicController::class, 'show'])
        ->middleware('role:admin,asesor')
        ->name('admin.clinics.show');

    // Admin dan asesor dapat menetapkan pengguna ke klinik.
    Route::post(
        '/admin/clinics/{clinic}/assign-users',
        [ClinicController::class, 'assignUser']
    )
        ->middleware('role:admin,asesor')
        ->name('admin.clinics.assign-user');

    /*
    |--------------------------------------------------------------------------
    | Admin - Manajemen User
    |--------------------------------------------------------------------------
    */

    Route::prefix('admin')
        ->middleware('role:admin')
        ->name('admin.')
        ->group(function () {

            // Daftar user.
            Route::get('/users', [UserController::class, 'index'])
                ->name('users.index');

            // Detail user.
            Route::get('/users/{user}', [UserController::class, 'show'])
                ->name('users.show');

            // Form edit user.
            Route::get('/users/{user}/edit', [UserController::class, 'edit'])
                ->name('users.edit');

            // Simpan perubahan user.
            Route::put('/users/{user}', [UserController::class, 'update'])
                ->name('users.update');

        });

});

/*
|--------------------------------------------------------------------------
| Settings
|--------------------------------------------------------------------------
*/

require __DIR__.'/settings.php';
