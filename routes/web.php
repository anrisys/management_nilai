<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\SiswaController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('auth/Login');
})->name('home');

// Route::get('dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');
Route::get('dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('siswa', SiswaController::class);
    Route::get('/nilai/export', [NilaiController::class, 'export'])->name('nilai.export');
    Route::post('/nilai/import', [NilaiController::class, 'import'])->name('nilai.import');
    Route::get('/nilai/template', [NilaiController::class, 'downloadTemplate'])->name('nilai.template');
    Route::resource('nilai', NilaiController::class);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
