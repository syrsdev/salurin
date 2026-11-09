<?php

use App\Http\Controllers\Api\PenerimaPersetujuanController;
use App\Http\Controllers\Api\PenyaluranController;
use App\Http\Controllers\JenisBantuanController;
use App\Http\Controllers\PenerimaController;
use App\Http\Controllers\PengaduanController;
use App\Http\Controllers\ProfileController;
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

    Route::middleware(['role:petugas'])->group(function () {
        Route::resource('penerima', PenerimaController::class)->except(['show']);
    });
    // Route::middleware('role:petugas')->group(function () {

    // });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Halaman frontend persetujuan data penerima untuk Kepala Desa.
Route::get('/dashboard/persetujuan-penerima', function () {
    return view('pages.persetujuan.index');
})->middleware(['auth', 'verified', 'role:kepala_desa'])
  ->name('persetujuan-penerima.page');

Route::middleware('auth')->prefix('dashboard')->group(function () {
    Route::resource('jenis-bantuan', JenisBantuanController::class)->except(['show']);
});


Route::middleware('auth')->prefix('api')->name('api.')->group(function () {

    // Kepala Desa: setujui / tolak data penerima
    Route::middleware('role:kepala_desa')->prefix('persetujuan-penerima')->name('persetujuan.')->group(function () {
        Route::get('/', [PenerimaPersetujuanController::class, 'index'])->name('index');
        Route::get('/{id}', [PenerimaPersetujuanController::class, 'show'])->name('show');
        Route::patch('/{id}/setujui', [PenerimaPersetujuanController::class, 'setujui'])->name('setujui');
        Route::patch('/{id}/tolak', [PenerimaPersetujuanController::class, 'tolak'])->name('tolak');
    });

    // Petugas Desa: daftar penyaluran + ubah status
    Route::middleware('role:petugas')->prefix('penyaluran')->name('penyaluran.')->group(function () {
        Route::get('/', [PenyaluranController::class, 'index'])->name('index');
        Route::get('/{id}', [PenyaluranController::class, 'show'])->name('show');
        Route::patch('/{id}/status', [PenyaluranController::class, 'ubahStatus'])->name('status');
    });
});

require __DIR__.'/auth.php';
