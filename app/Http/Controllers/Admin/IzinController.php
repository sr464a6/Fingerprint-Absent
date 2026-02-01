<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Absensi;

class IzinController extends Controller
{
  public function index()
  {
    $siswa = Siswa::orderBy('nama', 'asc')->get();
    return view('admin.izin.index', compact('siswa'));
  }

  public function store(Request $request)
  {
    $request->validate([
      'siswa_id' => 'required|exists:siswa,id',
      'keterangan' => 'required|in:izin,sakit',
      'alasan' => 'required|string',
      'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
    ]);

    $siswa = Siswa::findOrFail($request->siswa_id);

    $data = [
      'siswa_id' => $siswa->id,
      'nama' => $siswa->nama,
      'keterangan' => $request->keterangan,
      'alasan' => $request->alasan,
      'tanggal' => date('Y-m-d'),
      'jam' => now()
    ];

    if ($request->hasFile('foto')) {
      $path = $request->file('foto')->store('izin', 'public');
      $data['foto'] = $path;
    }

    Absensi::create($data);

    return redirect()->back()->with('success', 'Data izin berhasil disimpan');
  }
}
