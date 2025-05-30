<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\SubKriteriaController;
use App\Http\Controllers\AlternatifController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\HasilController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('kriteria', [KriteriaController::class, 'index'])->name('kriteria.index');
Route::get('kriteria/create', [KriteriaController::class, 'create'])->name('kriteria.create');
Route::post('kriteria', [KriteriaController::class, 'store'])->name('kriteria.store');
Route::get('kriteria/{kriteria}', [KriteriaController::class, 'show'])->name('kriteria.show');
Route::get('kriteria/{kriteria}/edit', [KriteriaController::class, 'edit'])->name('kriteria.edit');
Route::put('kriteria/{kriteria}', [KriteriaController::class, 'update'])->name('kriteria.update');
Route::delete('kriteria/{kriteria}', [KriteriaController::class, 'destroy'])->name('kriteria.destroy');

Route::get('sub-kriteria', [SubKriteriaController::class, 'index'])->name('sub-kriteria.index');
Route::get('sub-kriteria/create', [SubKriteriaController::class, 'create'])->name('sub-kriteria.create');
Route::post('sub-kriteria', [SubKriteriaController::class, 'store'])->name('sub-kriteria.store');
Route::get('sub-kriteria/{subKriteria}/edit', [SubKriteriaController::class, 'edit'])->name('sub-kriteria.edit');
Route::put('sub-kriteria/{subKriteria}', [SubKriteriaController::class, 'update'])->name('sub-kriteria.update');
Route::delete('sub-kriteria/{subKriteria}', [SubKriteriaController::class, 'destroy'])->name('sub-kriteria.destroy');

// TAMBAHKAN ROUTE INI - Route untuk sinkronisasi alternatif (harus SEBELUM resource route)
Route::get('alternatif/sync', [AlternatifController::class, 'syncWithKriteria'])->name('alternatif.sync');

// Resource route untuk alternatif
Route::resource('alternatif', AlternatifController::class);
// Tambahkan ke dalam web.php

Route::get('/penilaian', [PenilaianController::class, 'index'])->name('penilaian.index');

Route::get('hasil', [HasilController::class, 'index'])->name('hasil.index');
