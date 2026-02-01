<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Absensi;
use App\Models\EnrollQueue;

class FingerprintController extends Controller
{


    public function getEnrollInQueue()
    {
        // Mengambil data enroll queue
        $enrolls = EnrollQueue::orderBy('created_at', 'desc')->first();

        if (!$enrolls) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data enroll dalam antrian'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'nisn' => $enrolls->nisn
        ]);
    }

    /**
     * API untuk konfirmasi enroll berhasil
     * Mengirim id_fingerprint yang sudah tersimpan di sensor
     */
    public function confirmEnroll(Request $request)
    {
        $request->validate([
            'nisn' => 'required|exists:siswa,nisn',
            'id_fingerprint' => 'required|integer'
        ]);

        $siswa = Siswa::where('nisn', $request->nisn)->firstOrFail();
        // Cek apakah id_fingerprint sudah dipakai
        $exists = Siswa::where('id_fingerprint', $request->id_fingerprint)
            ->where('id', '!=', $siswa->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'ID Fingerprint sudah terdaftar'
            ], 400);
        }

        $siswa->update([
            'id_fingerprint' => $request->id_fingerprint
        ]);

        EnrollQueue::truncate();

        return response()->json([
            'success' => true,
            'message' => 'Fingerprint berhasil didaftarkan',
            'siswa' => $siswa
        ]);
    }

    /**
     * API untuk scan absensi
     * mengirim id_fingerprint yang terdeteksi
     */
    public function scanAbsensi(Request $request)
    {
        $request->validate([
            'id_fingerprint' => 'required|integer'
        ]);

        // Cari siswa berdasarkan id_fingerprint
        $siswa = Siswa::where('id_fingerprint', $request->id_fingerprint)->first();

        if (!$siswa) {
            return response()->json([
                'success' => false,
                'message' => 'Fingerprint tidak terdaftar'
            ], 404);
        }

        // Cek apakah sudah absen hari ini
        $sudahAbsen = Absensi::where('siswa_id', $siswa->id)
            ->whereDate('tanggal', today())
            ->where('keterangan', 'hadir')
            ->exists();

        if ($sudahAbsen) {
            return response()->json([
                'success' => false,
                'message' => 'Sudah absen hari ini',
                'siswa' => [
                    'nama' => $siswa->nama,
                    'nisn' => $siswa->nisn
                ]
            ], 400);
        }

        // Simpan absensi
        $absensi = Absensi::create([
            'siswa_id' => $siswa->id,
            'nama' => $siswa->nama,
            'keterangan' => 'hadir',
            'tanggal' => date('Y-m-d'),
            'jam' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil',
            'siswa' => [
                'nama' => $siswa->nama,
                'nisn' => $siswa->nisn
            ],
            'waktu' => $absensi->jam->format('H:i:s')
        ]);
    }

    /**
     * API untuk mendapatkan daftar semua fingerprint yang terdaftar
     * Untuk sync data ke sensor
     */
    public function getFingerprintList()
    {
        $siswa = Siswa::whereNotNull('id_fingerprint')
            ->select('id', 'id_fingerprint', 'nama', 'nisn')
            ->get();

        return response()->json([
            'success' => true,
            'total' => $siswa->count(),
            'data' => $siswa
        ]);
    }
}
