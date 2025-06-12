<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\SubKriteriaController;
use App\Http\Controllers\AlternatifController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\HasilController;
use App\Http\Controllers\AccountController;

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Routes untuk user yang sudah login tapi belum tentu verified
Route::middleware('auth')->group(function () {
    // Logout route
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Email verification routes
    Route::get('/email/verify', [AuthController::class, 'showVerificationNotice'])
        ->name('verification.notice');
    
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
        
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

// Protected routes (requires authentication + email verification)
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard route
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Kriteria routes
    Route::resource('kriteria', KriteriaController::class)->parameters([
        'kriteria' => 'kriteria'
    ]);
    
    // Sub-kriteria routes 
    Route::resource('sub-kriteria', SubKriteriaController::class)
        ->except(['show'])
        ->parameters(['sub-kriteria' => 'subKriteria']);
    
    // Alternatif routes dengan custom route untuk sync
    Route::get('alternatif/sync', [AlternatifController::class, 'syncWithKriteria'])->name('alternatif.sync');
    Route::resource('alternatif', AlternatifController::class);
    
    // Single action controllers
    Route::get('penilaian', [PenilaianController::class, 'index'])->name('penilaian.index');
    Route::get('hasil', [HasilController::class, 'index'])->name('hasil.index');
    
    // Account management routes
    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/', [AccountController::class, 'index'])->name('index');
        Route::put('/profile', [AccountController::class, 'updateProfile'])->name('update-profile');
        Route::put('/password', [AccountController::class, 'updatePassword'])->name('update-password');
    });
});