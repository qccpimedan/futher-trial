@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Data GMP Karyawan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('gmp-karyawan.index') }}">GMP Karyawan</a></li>
                        <li class="breadcrumb-item active">Edit Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item"><a class="nav-link active" href="#edit-gmp-karyawan" data-toggle="tab">Edit GMP Karyawan</a></li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="active tab-pane" id="edit-gmp-karyawan">
                                    <!-- Card Informasi Keterangan GMP Karyawan -->
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <div class="card card-info card-outline">
                                                <div class="card-header">
                                                    <h3 class="card-title">
                                                        <i class="fas fa-info-circle"></i> Keterangan Penilaian GMP Karyawan
                                                    </h3>
                                                    <div class="card-tools">
                                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="alert alert-warning">
                                                                <h5><i class="fas fa-clipboard-list"></i> Kriteria Penilaian:</h5>
                                                                <ol class="mb-0">
                                                                    <li><strong>Perlengkapan</strong> (booth, ceragam, masker, sarung tangan)</li>
                                                                    <li><strong>Kuku</strong></li>
                                                                    <li><strong>Perhiasan</strong></li>
                                                                    <li><strong>Luka</strong></li>
                                                                </ol>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="info-box bg-success">
                                                                <span class="info-box-icon">
                                                                    <i class="fas fa-check-circle" style="font-size: 2rem;"></i>
                                                                </span>
                                                                <div class="info-box-content">
                                                                    <span class="info-box-text">✓ (Sesuai)</span>
                                                                    <span class="info-box-number">Memenuhi Standar GMP</span>
                                                                    <div class="progress">
                                                                        <div class="progress-bar bg-success" style="width: 100%"></div>
                                                                    </div>
                                                                    <span class="progress-description">
                                                                        Karyawan sudah menerapkan GMP dengan baik
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="info-box bg-danger">
                                                                <span class="info-box-icon">
                                                                    <i class="fas fa-times-circle" style="font-size: 2rem;"></i>
                                                                </span>
                                                                <div class="info-box-content">
                                                                    <span class="info-box-text">✗ (Tidak Sesuai)</span>
                                                                    <span class="info-box-number">Belum Memenuhi Standar</span>
                                                                    <div class="progress">
                                                                        <div class="progress-bar bg-danger" style="width: 100%"></div>
                                                                    </div>
                                                                    <span class="progress-description">
                                                                        Perlu perbaikan dalam penerapan GMP
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="alert alert-info mt-2">
                                                        <i class="fas fa-lightbulb"></i>
                                                        <strong>Petunjuk:</strong> Periksa setiap aspek GMP karyawan berdasarkan 4 kriteria di atas. Gunakan tanda centang (✓) jika sesuai standar, dan tanda silang (✗) jika perlu perbaikan.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <form action="{{ route('gmp-karyawan.update', $gmpKaryawan->uuid) }}" method="POST">
                                        @csrf
                                        @method('PUT')

                                            <div class="row">
                                                <!-- Area -->
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="id_area">Area <span class="text-danger">*</span></label>
                                                        <select class="form-control @error('id_area') is-invalid @enderror" 
                                                                id="id_area" name="id_area" required>
                                                            <option value="">Pilih Area</option>
                                                            @foreach($areas as $area)
                                                                <option value="{{ $area->id }}" 
                                                                    {{ (old('id_area', $gmpKaryawan->id_area) == $area->id) ? 'selected' : '' }}>
                                                                    {{ $area->area }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('id_area')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <!-- Shift -->
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="shift_id">Shift <span class="text-danger">*</span></label>
                                                        <select class="form-control @error('shift_id') is-invalid @enderror" 
                                                                id="shift_id" name="shift_id" required>
                                                            <option value="">Pilih Shift</option>
                                                            @foreach($shifts as $shift)
                                                                <option value="{{ $shift->id }}" 
                                                                    {{ (old('shift_id', $gmpKaryawan->shift_id) == $shift->id) ? 'selected' : '' }}>
                                                                    {{ $shift->shift }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('shift_id')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <!-- Tanggal -->
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="tanggal">Tanggal & Waktu <span class="text-danger">*</span></label>
                                                        <input type="datetime-local" 
                                                            class="form-control @error('tanggal') is-invalid @enderror" 
                                                            id="tanggal" 
                                                            name="tanggal" 
                                                            value="{{ old('tanggal', \Carbon\Carbon::parse($gmpKaryawan->tanggal)->format('Y-m-d\TH:i')) }}" 
                                                            readonly>
                                                        @error('tanggal')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <!-- Nama Karyawan -->
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="nama_karyawan">Nama Karyawan <span class="text-danger">*</span></label>
                                                        <input type="text" 
                                                            class="form-control @error('nama_karyawan') is-invalid @enderror" 
                                                            id="nama_karyawan" 
                                                            name="nama_karyawan" 
                                                            value="{{ old('nama_karyawan', $gmpKaryawan->nama_karyawan) }}" 
                                                            placeholder="Masukkan nama karyawan"
                                                            required>
                                                        @error('nama_karyawan')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Temuan Ketidaksesuaian -->
                                            <div class="form-group">
                                                <label for="temuan_ketidaksesuaian">Temuan Ketidaksesuaian <span class="text-danger">*</span></label>
                                                <select class="form-control @error('temuan_ketidaksesuaian') is-invalid @enderror" 
                                                        id="temuan_ketidaksesuaian" 
                                                        name="temuan_ketidaksesuaian" 
                                                        required>
                                                    <option value="">Pilih Temuan Ketidaksesuaian</option>
                                                    @foreach($temuanOptions as $key => $label)
                                                        <option value="{{ $key }}" 
                                                            {{ (old('temuan_ketidaksesuaian', $gmpKaryawan->temuan_ketidaksesuaian) == $key) ? 'selected' : '' }}>
                                                            {{ $label }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('temuan_ketidaksesuaian')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Keterangan -->
                                            <div class="form-group">
                                                <label for="keterangan">Keterangan</label>
                                                <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                                        id="keterangan" 
                                                        name="keterangan" 
                                                        rows="3" 
                                                        placeholder="Masukkan keterangan (opsional)">{{ old('keterangan', $gmpKaryawan->keterangan) }}</textarea>
                                                @error('keterangan')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Tindakan Koreksi -->
                                            <div class="form-group">
                                                <label for="tindakan_koreksi">Tindakan Koreksi</label>
                                                <textarea class="form-control @error('tindakan_koreksi') is-invalid @enderror" 
                                                        id="tindakan_koreksi" 
                                                        name="tindakan_koreksi" 
                                                        rows="3" 
                                                        placeholder="Masukkan tindakan koreksi (opsional)">{{ old('tindakan_koreksi', $gmpKaryawan->tindakan_koreksi) }}</textarea>
                                                @error('tindakan_koreksi')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Verifikasi -->
                                            <div class="form-group">
                                                <label for="verifikasi">Verifikasi</label>
                                                <select class="form-control @error('verifikasi') is-invalid @enderror" id="verifikasi" name="verifikasi">
                                                    <option value="">-- Pilih --</option>
                                                    <option value="tidak_ok" {{ old('verifikasi', $gmpKaryawan->verifikasi) == 'tidak_ok' ? 'selected' : '' }}>Tidak OK</option>
                                                    <option value="ok" {{ old('verifikasi', $gmpKaryawan->verifikasi) == 'ok' ? 'selected' : '' }}>OK</option>
                                                </select>
                                                @error('verifikasi')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div id="koreksi-lanjutan-section" style="display: none;">
                                                <div class="form-group">
                                                    <label for="koreksi_lanjutan">Koreksi Lanjutan #1</label>
                                                    <select class="form-control @error('koreksi_lanjutan') is-invalid @enderror" id="koreksi_lanjutan" name="koreksi_lanjutan">
                                                        <option value="">-- Pilih --</option>
                                                        <option value="tidak_ok" {{ old('koreksi_lanjutan', $gmpKaryawan->koreksi_lanjutan) == 'tidak_ok' ? 'selected' : '' }}>Tidak OK</option>
                                                        <option value="ok" {{ old('koreksi_lanjutan', $gmpKaryawan->koreksi_lanjutan) == 'ok' ? 'selected' : '' }}>OK</option>
                                                    </select>
                                                    @error('koreksi_lanjutan')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                        </div>

                                        <div class="form-group">
                                            <button type="submit" class="btn btn-warning">
                                                <i class="fas fa-save"></i> Update
                                            </button>
                                            <a href="{{ route('gmp-karyawan.index') }}" class="btn btn-secondary">
                                                <i class="fas fa-arrow-left"></i> Kembali
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var verifikasiEl = document.getElementById('verifikasi');
    var koreksiSectionEl = document.getElementById('koreksi-lanjutan-section');

    function toggleKoreksi() {
        var value = (verifikasiEl && verifikasiEl.value) ? verifikasiEl.value : '';
        if (!koreksiSectionEl) return;
        koreksiSectionEl.style.display = value === 'tidak_ok' ? 'block' : 'none';
    }

    if (verifikasiEl) {
        verifikasiEl.addEventListener('change', toggleKoreksi);
        toggleKoreksi();
    }
});
</script>
@endpush
@endsection