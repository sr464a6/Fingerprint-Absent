@extends('layouts.app')

@section('title', 'Enroll Siswa')
@section('page-title', 'Enroll Siswa Baru')

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Form Enroll Siswa</h3>
                </div>
                <form id="enrollForm">
                    @csrf
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Siswa <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" placeholder="Masukkan nama siswa"
                                required style="text-transform: uppercase;" value="{{ request('nama') }}">
                            <small class="form-text text-muted">Nama akan otomatis menjadi huruf kapital</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">NISN <span class="text-danger">*</span></label>
                            <input type="number" name="nisn" class="form-control" placeholder="Masukkan NISN" required
                                value="{{ request('nisn') }}">
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Setelah klik tombol Enroll, silakan tempelkan jari siswa pada
                            sensor fingerprint
                        </div>

                        <div id="enrollStatus" class="alert" style="display: none;"></div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" id="btnEnroll">
                            <i class="bi bi-fingerprint"></i> Enroll
                        </button>
                        <button type="button" onclick="window.location.href='{{ route('enroll.index') }}'"
                            class="btn btn-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Petunjuk Enroll</h3>
                </div>
                <div class="card-body">
                    <ol>
                        <li>Isi nama siswa (akan otomatis kapital)</li>
                        <li>Isi NISN siswa (angka)</li>
                        <li>Klik tombol <strong>Enroll</strong></li>
                        <li>Tunggu hingga muncul instruksi</li>
                        <li>Tempelkan jari pada sensor fingerprint</li>
                        <li>Ikuti instruksi hingga selesai</li>
                    </ol>

                    <div class="alert alert-warning mt-3">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Penting!</strong> Pastikan sudah menyala dan terhubung dengan sistem
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var currentSiswaId = null;
            var checkInterval = null;

            $('#enrollForm').on('submit', function(e) {
                e.preventDefault();

                var nama = $('input[name="nama"]').val();
                var nisn = $('input[name="nisn"]').val();

                // Disable button
                $('#btnEnroll').prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> Processing...');

                $.ajax({
                    url: "{{ route('enroll.store') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        nama: nama,
                        nisn: nisn
                    },
                    success: function(response) {
                        if (response.success) {
                            currentSiswaId = response.siswa_id;

                            if (response.status === 'exists') {
                                $('#enrollStatus')
                                    .removeClass('alert-success')
                                    .addClass('alert-danger')
                                    .html(
                                        '<i class="bi bi-exclamation-circle"></i> ' + response
                                        .message
                                    )
                                    .show();
                                $('#btnEnroll').prop('disabled', false).html(
                                    '<i class="bi bi-fingerprint"></i> Enroll');
                                return;
                            }

                            if (response.status === 'pending') {
                                $('#enrollStatus')
                                    .removeClass('alert-success')
                                    .addClass('alert-warning')
                                    .html(
                                        '<i class="bi bi-hourglass-split"></i> ' + response
                                        .message
                                    )
                                    .show();
                                $('#btnEnroll').prop('disabled', false).html(
                                    '<i class="spinner-border spinner-border-sm" role="status"></i> Processing...'
                                );

                            }

                            if (response.status === 'terdaftar') {
                                $('#enrollStatus')
                                    .removeClass('alert-danger')
                                    .addClass('alert-success')
                                    .html('<i class="bi bi-check-circle"></i> ' + response
                                        .message + '<br><strong>Nama:</strong> ' + response
                                        .nama +
                                        '<br><strong>NISN:</strong> ' + response.nisn +
                                        '<br><br><div class="spinner-border spinner-border-sm" role="status"></div> Menunggu...'
                                    )
                                    .show();
                            }

                            // Start checking ESP32 status (in real implementation)
                            // This is a placeholder - you would poll an endpoint to check if ESP32 has completed enrollment
                            let checkInterval = setInterval(function() {

                                    $.ajax({
                                        url: "{{ route('enroll.check-enroll') }}",
                                        type: 'GET',
                                        data: {
                                            _token: '{{ csrf_token() }}',
                                            nisn: nisn
                                        },
                                        success: function(response) {
                                            console.log(response.status);
                                            if (response.status === 'enroll') {
                                                clearInterval(checkInterval);
                                                $('#enrollStatus')
                                                    .removeClass(
                                                        'alert-warning')
                                                    .addClass('alert-success')
                                                    .html(
                                                        '<i class="bi bi-check-circle"></i> Enroll berhasil! Silakan reset form untuk siswa berikutnya.'
                                                    );
                                                $('#btnEnroll').prop('disabled',
                                                    false).html(
                                                    '<i class="bi bi-fingerprint"></i> Enroll'
                                                );
                                                $('#enrollForm')[0].reset();

                                                return;

                                            }

                                        }

                                    });


                                    // $('#enrollStatus')
                                    // .html(
                                    //     '<i class="bi bi-check-circle"></i> Enroll berhasil! Silakan reset form untuk siswa berikutnya.'
                                    // ); $('#btnEnroll').prop('disabled', false).html(
                                    //     '<i class="bi bi-fingerprint"></i> Enroll'); $(
                                    //     '#enrollForm')[0].reset();
                                },
                                1000);
                        }
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors;
                        var errorMsg = '';

                        if (errors) {
                            $.each(errors, function(key, value) {
                                errorMsg += value[0] + '<br>';
                            });
                        } else {
                            errorMsg = 'Terjadi kesalahan';
                        }

                        $('#enrollStatus')
                            .removeClass('alert-success')
                            .addClass('alert-danger')
                            .html('<i class="bi bi-exclamation-circle"></i> ' + errorMsg)
                            .show();

                        $('#btnEnroll').prop('disabled', false).html(
                            '<i class="bi bi-fingerprint"></i> Enroll');
                    }
                });

            });

            // Auto uppercase nama
            $('input[name="nama"]').on('input', function() {
                this.value = this.value.toUpperCase();
            });
        });
    </script>
@endpush
