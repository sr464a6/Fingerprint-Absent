<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\EnrollQueue;

class EnrollController extends Controller
{
    public function index()
    {

        return view('admin.enroll.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nisn' => 'required|numeric'
        ]);


        // where nisn and id_fingerprint is null
        if ($siswa = Siswa::where('nisn', $request->nisn)->whereNull('id_fingerprint')->first()) {
            return response()->json([
                'success' => true,
                'status' => 'pending',
                'nama' => $siswa->nama,
                'nisn' => $siswa->nisn,
                'message' => 'Siswa dengan NISN tersebut sudah terdaftar dan menunggu proses enroll'
            ], 200);
        }

        if (Siswa::where('nisn', $request->nisn)->whereNotNull('id_fingerprint')->exists()) {
            return response()->json([
                'success' => true,
                'status' => 'exists',
                'message' => 'NISN sudah terdaftar dengan fingerprint yang aktif'
            ], 200);
        }

        $siswa = Siswa::create([
            'nama' => $request->nama, // Auto capitalize dari mutator
            'nisn' => $request->nisn,
            'waktu_daftar' => now()
        ]);

        // Kirim signal ke untuk mulai enroll
        // ID siswa akan digunakan sebagai ID fingerprint
        return response()->json([
            'success' => true,
            'status' => 'terdaftar',
            'message' => 'Siswa berhasil didaftarkan. Silakan tempelkan jari pada sensor fingerprint',
            'siswa_id' => $siswa->id,
            'nama' => $siswa->nama,
            'nisn' => $siswa->nisn
        ]);
    }


    public function checkEnroll(Request $request)
    {
        // Cari siswa yang belum punya id_fingerprint
        $siswa = Siswa::where('nisn', $request->nisn)->first();

        if (!$siswa) {
            return response()->json([
                'status' => 'belum_terdaftar'
            ], 404);
        }

        if ($siswa->id_fingerprint === null) {


            if (EnrollQueue::count() > 1) {
                EnrollQueue::truncate();
            }

            EnrollQueue::firstOrCreate([
                'nisn' => $siswa->nisn,
            ]);

            return response()->json([
                'status' => 'idle',
                'siswa_id' => $siswa->id,
                'nama' => $siswa->nama,
                'nisn' => $siswa->nisn
            ]);
        }

        return response()->json([
            'status' => 'enroll'
        ]);
    }

    public function updateFingerprint(Request $request, $id)
    {
        $request->validate([
            'id_fingerprint' => 'required|integer|unique:siswa,id_fingerprint'
        ]);

        $siswa = Siswa::findOrFail($id);
        $siswa->update([
            'id_fingerprint' => $request->id_fingerprint
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Fingerprint berhasil didaftarkan'
        ]);
    }
}
