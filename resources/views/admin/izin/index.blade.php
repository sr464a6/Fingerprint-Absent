@extends('layouts.app')

@section('title', 'Form Izin/Sakit')
@section('page-title', 'Form Izin/Sakit')

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Input Izin/Sakit Siswa</h3>
                </div>
                <form action="{{ route('izin.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Siswa <span class="text-danger">*</span></label>
                            <select name="siswa_id" class="form-select @error('siswa_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Siswa --</option>
                                @foreach ($siswa as $s)
                                    <option value="{{ $s->id }}" {{ old('siswa_id') == $s->id ? 'selected' : '' }}>
                                        {{ $s->nama }} - {{ $s->nisn }}
                                    </option>
                                @endforeach
                            </select>
                            @error('siswa_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan <span class="text-danger">*</span></label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="keterangan" id="izin"
                                        value="izin" {{ old('keterangan') == 'izin' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="izin">Izin</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="keterangan" id="sakit"
                                        value="sakit" {{ old('keterangan') == 'sakit' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="sakit">Sakit</label>
                                </div>
                            </div>
                            @error('keterangan')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alasan <span class="text-danger">*</span></label>
                            <textarea name="alasan" class="form-control @error('alasan') is-invalid @enderror" rows="3" required>{{ old('alasan') }}</textarea>
                            @error('alasan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Upload Bukti (Opsional)</label>
                            <input type="file" class="form-control @error('foto') is-invalid @enderror" id="foto"
                                name="foto" accept="image/*">
                            <small class="form-text text-muted">Format: JPG, JPEG, PNG. Maks 2MB</small>
                            @error('foto')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
