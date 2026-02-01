<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use Yajra\DataTables\Facades\DataTables;

class AbsensiController extends Controller
{
    public function index()
    {
        return view('admin.absensi.index');
    }

    public function getData(Request $request)
    {
        $tanggal = $request->get('tanggal', date('Y-m-d'));

        $absensi = Absensi::with('siswa')
            ->whereDate('tanggal', $tanggal)
            ->orderBy('jam', 'desc')
            ->get();

        return DataTables::of($absensi)
            ->addIndexColumn()
            ->editColumn('keterangan', function ($row) {
                $badge = '';
                if ($row->keterangan == 'hadir') {
                    $badge = '<span class="badge bg-success">Hadir</span>';
                } elseif ($row->keterangan == 'izin') {
                    $badge = '<span class="badge bg-warning">Izin</span>';
                } else {
                    $badge = '<span class="badge bg-danger">Sakit</span>';
                }
                return $badge;
            })
            ->editColumn('jam', function ($row) {
                return $row->jam->format('H:i:s');
            })
            ->addColumn('foto_preview', function ($row) {
                if ($row->foto) {
                    return '<a href="' . asset('storage/' . $row->foto) . '" target="_blank"><img src="' . asset('storage/' . $row->foto) . '" width="50"></a>';
                }
                return '-';
            })
            ->rawColumns(['keterangan', 'foto_preview'])
            ->make(true);
    }
}
