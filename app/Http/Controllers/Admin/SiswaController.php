<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use Yajra\DataTables\Facades\DataTables;

class SiswaController extends Controller
{
    public function index()
    {
        return view('admin.siswa.index');
    }

    public function getData()
    {
        $siswa = Siswa::orderBy('nama', 'asc')->get();

        return DataTables::of($siswa)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '"><i class="fas fa-trash"></i></button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->delete();

        return response()->json(['success' => 'Data siswa berhasil dihapus']);
    }
}
