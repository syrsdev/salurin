<?php

use App\Http\Controllers\PenerimaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\JenisBantuanController;
use App\Http\Controllers\PublicDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/transparansi', [PublicDashboardController::class, 'index'])->name('transparansi');

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'verified']], function () {
    Route::get('/', function () {
        return view('pages.dashboard');
    })->name('dashboard');

    // Route::middleware('role:petugas')->group(function () {

    // });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->prefix('dashboard')->group(function () {
    Route::resource('jenis-bantuan', JenisBantuanController::class)->except(['show']);
});

Route::get('/dashboard/penerima', [PenerimaController::class, 'index'])
    ->middleware(['auth', 'role:petugas'])
    ->name('penerima.index');

require __DIR__.'/auth.php';
