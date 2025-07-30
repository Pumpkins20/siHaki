<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\SesiController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\AdminSubmissionController;
use App\Http\Controllers\UserHistoryController;
use App\Http\Controllers\UserPanduanController;
use App\Http\Controllers\PublicSearchController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Public routes
Route::get('/', [PublicSearchController::class, 'searchOnBeranda'])->name('beranda');

Route::get('/pencipta', [PublicSearchController::class, 'searchPencipta'])->name('pencipta');

Route::get('/jenis_ciptaan', [PublicSearchController::class, 'searchJenisCiptaan'])->name('jenis_ciptaan');

// ✅ UPDATED: Detail pages with proper controller methods
Route::get('/detail_pencipta/{id}', [PublicSearchController::class, 'detailPencipta'])->name('detail_pencipta');

Route::get('/detail_ciptaan/{id}', [PublicSearchController::class, 'detailCiptaan'])->name('detail_ciptaan');

// Add this route if not exists
Route::get('/sertifikat/view/{submission}', [PublicSearchController::class, 'viewCertificate'])->name('public.certificate.view');

Route::get('/detail_jenis', function () {
    return view('detail_jenis');
})->name('detail_jenis');


// Search route
Route::post('/search', [PublicSearchController::class, 'search'])->name('public.search');

// Guest routes (not authenticated)
Route::middleware(['guest'])->group(function () {
    // Standardize login routes
    Route::get('/login', [SesiController::class, 'index'])->name('login');
    Route::post('/login', [SesiController::class, 'login'])->name('login.post');
    
});

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [SesiController::class, 'logout'])->name('logout');
    
    // User routes (role: user) - Dosen
    Route::middleware(['role:user'])->prefix('user')->name('user.')->group(function () {
        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
        

        // Profil Route
        Route::get('/profile', [UserDashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [UserDashboardController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/photo', [UserDashboardController::class, 'updatePhoto'])->name('profile.photo');
        Route::post('/change-password', [UserDashboardController::class, 'changePassword'])->name('change-password');
        
        // HKI Submissions
        Route::prefix('submissions')->name('submissions.')->group(function () {
            Route::get('/', [SubmissionController::class, 'index'])->name('index');
            Route::get('/create', [SubmissionController::class, 'create'])->name('create');
            Route::post('/', [SubmissionController::class, 'store'])->name('store');
            Route::get('/{submission}', [SubmissionController::class, 'show'])->name('show');
            Route::get('/{submission}/edit', [SubmissionController::class, 'edit'])->name('edit');
            Route::put('/{submission}', [SubmissionController::class, 'update'])->name('update');
            Route::delete('/{submission}', [SubmissionController::class, 'destroy'])->name('destroy');
            
            // Document downloads
            Route::get('/documents/{document}/download', [SubmissionController::class, 'downloadDocument'])->name('documents.download');
            Route::delete('/documents/{document}', [SubmissionController::class, 'deleteDocument'])->name('documents.delete');
            
            // KTP download
            Route::get('/{submission}/ktp/{member}', [SubmissionController::class, 'downloadKtp'])->name('ktp.download');
        });
        
        // History
        Route::get('/history', [UserHistoryController::class, 'index'])->name('history');
        Route::prefix('history')->name('history.')->group(function () {
            Route::get('/', [UserHistoryController::class, 'index'])->name('index');
            Route::get('/export', [UserHistoryController::class, 'exportHistory'])->name('export');
            Route::get('/{submission}/certificate', [UserHistoryController::class, 'downloadCertificate'])->name('certificate');
            Route::get('/{submission}/document/{document}', [UserHistoryController::class, 'downloadDocument'])->name('document');
        });
        
        // Panduan
        Route::get('/panduan', [UserPanduanController::class, 'index'])->name('panduan');
        Route::prefix('panduan')->name('panduan.')->group(function () {
            Route::get('/', [UserPanduanController::class, 'index'])->name('index');
            Route::get('/download/{filename}', [UserPanduanController::class, 'downloadGuide'])->name('download');
            Route::get('/export-faq', [UserPanduanController::class, 'exportFaq'])->name('export-faq');
        });
    });
    
    // Admin routes (role: admin) - Super Admin yang juga Reviewer
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // User Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [AdminController::class, 'users'])->name('index');
            Route::get('/create', [AdminController::class, 'createUser'])->name('create');
            Route::post('/', [AdminController::class, 'storeUser'])->name('store');
            Route::get('/{user}', [AdminController::class, 'showUser'])->name('show');
            Route::get('/{user}/edit', [AdminController::class, 'editUser'])->name('edit');
            Route::put('/{user}', [AdminController::class, 'updateUser'])->name('update');
            Route::delete('/{user}', [AdminController::class, 'destroyUser'])->name('destroy');
            
            // Additional user actions
            Route::post('/{user}/reset-password', [AdminController::class, 'resetPassword'])->name('reset-password');
            Route::post('/{user}/toggle-status', [AdminController::class, 'toggleStatus'])->name('toggle-status');
        });
        
        // Submission Management
        Route::prefix('submissions')->name('submissions.')->group(function () {
            Route::get('/', [AdminController::class, 'submissions'])->name('index');
            Route::get('/{submission}', [AdminController::class, 'showSubmission'])->name('show');
            Route::post('/{submission}/assign-to-self', [AdminController::class, 'assignToSelf'])->name('assign-to-self');
            Route::post('/{submission}/approve', [AdminController::class, 'approveSubmission'])->name('approve');
            Route::post('/{submission}/revision', [AdminController::class, 'revisionSubmission'])->name('revision');
            Route::post('/{submission}/reject', [AdminController::class, 'rejectSubmission'])->name('reject');
            
            // Template generation routes
            Route::post('/{submission}/generate-template', [AdminController::class, 'generateTemplate'])->name('generate-template');

            // Bulk actions - Add these new routes
            Route::post('/bulk-assign', [AdminController::class, 'bulkAssignToSelf'])->name('bulk-assign');

            
            Route::get('/{submission}/documents/{document}/download', [AdminController::class, 'downloadDocument'])->name('document-download');
            Route::get('/{submission}/documents/{document}/preview', [AdminController::class, 'previewDocument'])->name('document-preview');
       
            Route::get('/{submission}/members/{member}/ktp', [AdminController::class, 'viewMemberKtp'])->name('member-ktp');
            Route::get('/{submission}/members/{member}/ktp/preview', [AdminController::class, 'previewMemberKtp'])->name('member-ktp-preview');

            
            // Export
            Route::get('/export/excel', [AdminController::class, 'exportSubmissions'])->name('export');
        });
        
        // Department Management
        Route::prefix('departments')->name('departments.')->group(function () {
            Route::get('/', [AdminController::class, 'departments'])->name('index');
            Route::get('/create', [AdminController::class, 'createDepartment'])->name('create');
            Route::post('/', [AdminController::class, 'storeDepartment'])->name('store');
            Route::get('/{department}/edit', [AdminController::class, 'editDepartment'])->name('edit');
            Route::put('/{department}', [AdminController::class, 'updateDepartment'])->name('update');
            Route::delete('/{department}', [AdminController::class, 'destroyDepartment'])->name('destroy');
        });
        
        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [AdminController::class, 'reports'])->name('index');
            Route::get('/export', [AdminController::class, 'exportReports'])->name('export');
            Route::get('/analytics-api', [AdminController::class, 'analyticsApi'])->name('analytics-api');
        });
        
        // Certificate Management
        Route::prefix('certificates')->name('certificates.')->group(function () {
            Route::get('/', [AdminController::class, 'certificatesIndex'])->name('index');
            Route::get('/{submission}', [AdminController::class, 'certificatesShow'])->name('show');
            Route::post('/{submission}/send', [AdminController::class, 'sendCertificate'])->name('send');
            Route::get('/{submission}/documents/{document}/download', [AdminController::class, 'downloadSubmissionDocument'])->name('document-download');
        });
        
        // Review History
        Route::prefix('review-history')->name('review-history.')->group(function () {
            Route::get('/', [AdminController::class, 'reviewHistoryIndex'])->name('index');
            Route::get('/export', [AdminController::class, 'exportReviewHistory'])->name('export');
        });
    });
});

// ✅ ADD: Route untuk detail jenis yang menerima parameter type
Route::get('/detail_jenis/{type?}', [PublicSearchController::class, 'detailJenis'])->name('detail_jenis');
