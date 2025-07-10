<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\SesiController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\ReviewerDashboardController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserHistoryController;
use App\Http\Controllers\UserPanduanController;
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
Route::get('/', function () {
    return view('beranda');
})->name('beranda');

Route::get('/pencipta', function () {
    return view('pencipta');
})->name('pencipta');

// Guest routes (not authenticated)
Route::middleware(['guest'])->group(function () {
    // Standardize login routes
    Route::get('/login', [SesiController::class, 'index'])->name('login');
    Route::post('/login', [SesiController::class, 'login'])->name('login.post');
    
    // Keep legacy routes for backward compatibility
    Route::get('/sesi', [SesiController::class, 'index'])->name('sesi');
    Route::post('/sesi', [SesiController::class, 'login']);
});

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    // Logout route (available for all authenticated users)
    Route::get('/logout', [SesiController::class, 'logout'])->name('logout');
    
    // Legacy routes (keep for backward compatibility)
    Route::get('/admin', [AdminController::class, 'admin'])->middleware('userAkses:admin');
    Route::get('/pengguna', [AdminController::class, 'pengguna'])->middleware('userAkses:pengguna');
    Route::get('/home', function () {
        return redirect('/admin');
    });
    
    // User routes (role: user)
    Route::middleware(['auth', 'role:user'])->group(function () {
        Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
        
        // HKI Submissions CRUD
        Route::prefix('user/submissions')->name('user.submissions.')->group(function () {
            Route::get('/', [SubmissionController::class, 'index'])->name('index');
            Route::get('/create', [SubmissionController::class, 'create'])->name('create');
            Route::post('/', [SubmissionController::class, 'store'])->name('store');
            Route::get('/{submission}', [SubmissionController::class, 'show'])->name('show');
            Route::get('/{submission}/edit', [SubmissionController::class, 'edit'])->name('edit');
            Route::put('/{submission}', [SubmissionController::class, 'update'])->name('update');
            Route::delete('/{submission}', [SubmissionController::class, 'destroy'])->name('destroy');
            
            // Document routes
            Route::get('/documents/{document}/download', [SubmissionController::class, 'downloadDocument'])->name('documents.download');
            Route::delete('/documents/{document}', [SubmissionController::class, 'deleteDocument'])->name('documents.delete');
        });
        
        // History routes
        Route::prefix('user/history')->name('user.history.')->group(function () {
            Route::get('/', [UserHistoryController::class, 'index'])->name('index');
            Route::get('/export', [UserHistoryController::class, 'exportHistory'])->name('export');
            Route::get('/{submission}/certificate', [UserHistoryController::class, 'downloadCertificate'])->name('certificate');
            Route::get('/{submission}/document/{document}', [UserHistoryController::class, 'downloadDocument'])->name('document');
        });
        
        // Update the main history route
        Route::get('/user/history', [UserHistoryController::class, 'index'])->name('user.history');
        
        // Panduan
        Route::get('/user/panduan', [UserPanduanController::class, 'index'])->name('user.panduan');
    });
    
    // Reviewer routes (role: reviewer)
    Route::middleware('role:reviewer')->group(function () {
        Route::get('/reviewer/dashboard', [ReviewerDashboardController::class, 'index'])->name('reviewer.dashboard');
        
        // Review Management
        Route::prefix('reviewer/reviews')->name('reviewer.reviews.')->group(function () {
            Route::get('/', [ReviewController::class, 'index'])->name('index');
            Route::get('/{submission}', [ReviewController::class, 'show'])->name('show');
            Route::post('/{submission}/review', [ReviewController::class, 'review'])->name('review');
            Route::post('/{submission}/approve', [ReviewController::class, 'approve'])->name('approve');
            Route::post('/{submission}/reject', [ReviewController::class, 'reject'])->name('reject');
            Route::post('/{submission}/request-revision', [ReviewController::class, 'requestRevision'])->name('request-revision');
        });
    });
    
    // Admin routes (role: admin)
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        
        // User Management
        Route::prefix('admin/users')->name('admin.users.')->group(function () {
            Route::get('/', [AdminController::class, 'users'])->name('index');
            Route::get('/create', [AdminController::class, 'createUser'])->name('create');
            Route::post('/', [AdminController::class, 'storeUser'])->name('store');
            Route::get('/{user}', [AdminController::class, 'showUser'])->name('show');
            Route::get('/{user}/edit', [AdminController::class, 'editUser'])->name('edit');
            Route::put('/{user}', [AdminController::class, 'updateUser'])->name('update');
            Route::delete('/{user}', [AdminController::class, 'destroyUser'])->name('destroy');
        });
        
        // Submission Management
        Route::prefix('admin/submissions')->name('admin.submissions.')->group(function () {
            Route::get('/', [AdminController::class, 'submissions'])->name('index');
            Route::get('/{submission}', [AdminController::class, 'showSubmission'])->name('show');
            Route::post('/{submission}/assign-reviewer', [AdminController::class, 'assignReviewer'])->name('assign-reviewer');
        });
        
        // Department Management
        Route::prefix('admin/departments')->name('admin.departments.')->group(function () {
            Route::get('/', [AdminController::class, 'departments'])->name('index');
            Route::get('/create', [AdminController::class, 'createDepartment'])->name('create');
            Route::post('/', [AdminController::class, 'storeDepartment'])->name('store');
            Route::get('/{department}/edit', [AdminController::class, 'editDepartment'])->name('edit');
            Route::put('/{department}', [AdminController::class, 'updateDepartment'])->name('update');
            Route::delete('/{department}', [AdminController::class, 'destroyDepartment'])->name('destroy');
        });
        
        // Reports
        Route::prefix('admin/reports')->name('admin.reports.')->group(function () {
            Route::get('/', [AdminController::class, 'reports'])->name('index');
            Route::get('/export', [AdminController::class, 'exportReport'])->name('export');
        });
    });
});
