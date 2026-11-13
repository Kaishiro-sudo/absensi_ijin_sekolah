<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GuruBkController;
use App\Http\Controllers\PerizinanController;
use App\Http\Controllers\SatpamController;
use Illuminate\Support\Facades\Route;

// Halaman Utama
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return match (auth()->user()->role) {
        'siswa' => redirect()->route('perizinan.create'),
        'guru_bk' => redirect()->route('guru.dashboard'),
        'satpam' => redirect()->route('satpam.dashboard'),
        default => abort(403, 'Akses ditolak.'),
    };
})->middleware('auth')->name('dashboard');

// Autentikasi (Login & Logout)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Fitur Perizinan Siswa
Route::middleware(['auth', 'role:siswa'])->group(function () {
    Route::get('/perizinan/create', [PerizinanController::class, 'create'])->name('perizinan.create');
    Route::post('/perizinan', [PerizinanController::class, 'store'])->name('perizinan.store');
});

// Dashboard Guru BK / Piket
Route::middleware(['auth', 'role:guru_bk'])->group(function () {
    Route::get('/guru/dashboard', [GuruBkController::class, 'index'])->name('guru.dashboard');
    Route::put('/guru/perizinan/{id}', [GuruBkController::class, 'updateStatus'])->name('guru.perizinan.update');
});

// Halaman Satpam
Route::middleware(['auth', 'role:satpam'])->group(function () {
    Route::get('/satpam/dashboard', [SatpamController::class, 'index'])->name('satpam.dashboard');
    Route::get('/satpam/scan', [SatpamController::class, 'index'])->name('satpam.scan');
    Route::post('/satpam/perizinan/{perizinan}/keluar', [SatpamController::class, 'scanKeluar'])
        ->name('satpam.perizinan.keluar');
    Route::post('/satpam/perizinan/{perizinan}/masuk', [SatpamController::class, 'scanMasuk'])
        ->name('satpam.perizinan.masuk');
});
