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

// Logout route (available for authenticated users)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected routes (requires authentication)
Route::middleware('auth')->group(function () {
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