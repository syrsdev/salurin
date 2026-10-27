<?php

use App\Http\Controllers\PenerimaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\JenisBantuanController;
use App\Http\Controllers\PengaduanController;
use App\Http\Controllers\PublicDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/pengaduan', [PengaduanController::class, 'create'])->name('pengaduan.create');
Route::post('/pengaduan', [PengaduanController::class, 'store'])->middleware('throttle:10,1')->name('pengaduan.store');
Route::get('/pengaduan/cari', [PengaduanController::class, 'cari'])->middleware('throttle:30,1')->name('pengaduan.cari');
Route::get('/transparansi', [PublicDashboardController::class, 'index'])->name('transparansi');
Route::get('/pengaduan/sukses/{kode}', [PengaduanController::class, 'sukses'])->name('pengaduan.sukses');

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
