<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Absensi;

class DashboardController extends Controller
{
  public function index()
  {
    $totalSiswa = Siswa::count();
    $totalHadir = Absensi::whereDate('tanggal', today())
      ->where('keterangan', 'hadir')
      ->count();
    $totalIzin = Absensi::whereDate('tanggal', today())
      ->where('keterangan', 'izin')
      ->count();
    $totalSakit = Absensi::whereDate('tanggal', today())
      ->where('keterangan', 'sakit')
      ->count();

    return view('admin.dashboard', compact('totalSiswa', 'totalHadir', 'totalIzin', 'totalSakit'));
  }
}
