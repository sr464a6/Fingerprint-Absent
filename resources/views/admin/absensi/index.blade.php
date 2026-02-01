@extends('layouts.app')

@section('title', 'Absensi')
@section('page-title', 'Data Absensi')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Data Absensi Siswa</h3>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label>Pilih Tanggal:</label>
                    <input type="text" id="tanggalPicker" class="form-control" value="{{ date('Y-m-d') }}">
                </div>
            </div>

            <div class="table-responsive">
                <table id="absensiTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Keterangan</th>
                            <th>Alasan</th>
                            <th>Foto</th>
                            <th>Jam</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        $(document).ready(function() {
            var table = $('#absensiTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('absensi.data') }}",
                    data: function(d) {
                        d.tanggal = $('#tanggalPicker').val();
                    }
                },
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
                        data: 'keterangan',
                        name: 'keterangan',

                    },
                    {
                        data: 'alasan',
                        name: 'alasan',
                        render: function(data) {
                            return data ? data : '-';
                        }
                    },
                    {
                        data: 'foto_preview',
                        name: 'foto_preview',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'jam',
                        name: 'jam'
                    }
                ],
                order: [
                    [5, 'desc']
                ]
            });

            // Flatpickr date picker
            flatpickr("#tanggalPicker", {
                dateFormat: "Y-m-d",
                defaultDate: "{{ date('Y-m-d') }}",
                onChange: function(selectedDates, dateStr, instance) {
                    table.ajax.reload();
                }
            });
        });
    </script>
@endpush
