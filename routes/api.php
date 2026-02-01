<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FingerprintController;

/*
|--------------------------------------------------------------------------
| API Routes for ESP32 Fingerprint
|--------------------------------------------------------------------------
*/

// API untuk ESP32
Route::prefix('fingerprint')->group(function () {

    Route::get('/get-nisn-enroll', [FingerprintController::class, 'getEnrollInQueue'])->name('api.fingerprint.get-enroll-queue');

    // Konfirmasi enroll berhasil
    Route::post('/confirm-enroll', [FingerprintController::class, 'confirmEnroll']);

    // Scan absensi dari ESP32
    Route::post('/scan', [FingerprintController::class, 'scanAbsensi']);

    // Get daftar semua fingerprint (untuk sync)
    Route::get('/list', [FingerprintController::class, 'getFingerprintList']);
});
