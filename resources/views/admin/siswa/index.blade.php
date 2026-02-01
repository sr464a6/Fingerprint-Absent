@extends('layouts.app')

@section('title', 'Daftar Siswa')
@section('page-title', 'Daftar Siswa')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Data Siswa Terdaftar</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="siswaTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>NISN</th>
                            <th>ID Fingerprint</th>
                            <th>Waktu Daftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var table = $('#siswaTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('siswa.data') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'nisn',
                        name: 'nisn'
                    },
                    {
                        data: 'id_fingerprint',
                        name: 'id_fingerprint',
                        render: function(data) {
                            return data ? data :
                                '<span class="badge bg-warning">Belum Enroll</span>';
                        }
                    },
                    {
                        data: 'waktu_daftar',
                        name: 'waktu_daftar',
                        render: function(data) {
                            return new Date(data).toLocaleString('id-ID');
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        render: function(data, type, row) {
                            let buttons = '';

                            // Show Enroll button only if not enrolled yet
                            if (!row.id_fingerprint) {
                                buttons += `
                                    <a href="/enroll?nama=${encodeURIComponent(row.nama)}&nisn=${row.nisn}"
                                       class="btn btn-primary btn-sm me-1">
                                        <i class="bi bi-fingerprint"></i> Enroll
                                    </a>
                                `;
                            }

                            buttons += `
                                <button class="btn btn-danger btn-sm delete-btn" data-id="${row.id}">
                                    <i class="bi bi-trash-fill"></i> Hapus
                                </button>
                            `;

                            return buttons;
                        },
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [1, 'asc']
                ]
            });

            // Delete siswa
            $('#siswaTable').on('click', '.delete-btn', function() {
                var id = $(this).data('id');
                if (confirm('Yakin ingin menghapus data siswa ini?')) {
                    $.ajax({
                        url: '/siswa/' + id,
                        type: 'DELETE',
                        success: function(response) {
                            table.ajax.reload();
                            alert('Data berhasil dihapus');
                        },
                        error: function(xhr) {
                            alert('Error: ' + xhr.responseText);
                        }
                    });
                }
            });
        });
    </script>
@endpush
