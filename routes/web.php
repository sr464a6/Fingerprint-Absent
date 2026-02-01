<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\AbsensiController;
use App\Http\Controllers\Admin\IzinController;
use App\Http\Controllers\Admin\EnrollController;

// Redirect root ke login
Route::get('/', function () {
    return redirect('/login');
});

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes (Protected)
Route::middleware(['auth.check'])->group(function () {
    // Enroll (auto clear queue)
    Route::middleware(['clear.enroll.queue'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Siswa
        Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa.index');
        Route::get('/siswa/data', [SiswaController::class, 'getData'])->name('siswa.data');
        Route::delete('/siswa/{id}', [SiswaController::class, 'destroy'])->name('siswa.destroy');

        // Absensi
        Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
        Route::get('/absensi/data', [AbsensiController::class, 'getData'])->name('absensi.data');

        // Izin
        Route::get('/izin', [IzinController::class, 'index'])->name('izin.index');
        Route::post('/izin', [IzinController::class, 'store'])->name('izin.store');


        Route::get('/enroll', [EnrollController::class, 'index'])->name('enroll.index');
        Route::post('/enroll', [EnrollController::class, 'store'])->name('enroll.store');
        Route::put('/enroll/{id}/fingerprint', [EnrollController::class, 'updateFingerprint'])->name('enroll.update.fingerprint');
        // Cek apakah ada siswa baru yang perlu dienroll
    });
    Route::get('/check-enroll', [EnrollController::class, 'checkEnroll'])->name('enroll.check-enroll');
});

