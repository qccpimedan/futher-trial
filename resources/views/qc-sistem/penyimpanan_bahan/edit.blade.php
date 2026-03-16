{{-- filepath: resources/views/qc-sistem/seasoning/edit.blade.php --}}
@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        @isset($twoHour)
                            Edit Data per 2 Jam (Tambah Riwayat)
                        @else
                            Edit Data Penyimpanan Bahan
                        @endisset
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('penyimpanan-bahan.index') }}"><i class="fas fa-warehouse"></i> Penyimpanan Bahan</a></li>
                        <li class="breadcrumb-item active">@isset($twoHour) Edit per 2 Jam @else Edit Data @endisset</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline shadow">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-edit"></i> @isset($twoHour) Form Edit per 2 Jam (membuat record baru) @else Form Edit Penyimpanan Bahan @endisset</h3>
                        </div>
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h5><i class="icon fas fa-ban"></i> Terjadi Kesalahan!</h5>
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if(isset($twoHour) && $twoHour)
                                <div class="alert alert-info">
                                    <i class="fas fa-clock"></i>
                                    <strong>Mode Edit Per 2 Jam:</strong> Data akan disimpan sebagai record baru dengan waktu yang diperbarui. Data asli tetap tersimpan.
                                </div>
                            @endif
                            <form action="@isset($twoHour) {{ route('penyimpanan-bahan.twohour.store', $penyimpanan->uuid) }} @else {{ route('penyimpanan-bahan.update', $penyimpanan->uuid) }} @endisset" method="POST">
                                @csrf
                                @empty($twoHour)
                                    @method('PUT')
                                @endempty
                                {{-- Hidden required fields to satisfy validation --}}
                                <input type="hidden" name="id_plan" value="{{ $penyimpanan->id_plan }}">
                                @isset($twoHour)
                                    <input type="hidden" name="tanggal" value="{{ \Carbon\Carbon::now('Asia/Jakarta')->format('d-m-Y H:i:s') }}">
                                @endisset
                                
                                <div class="card card-outline card-info shadow-sm">
                                    <div class="card-header bg-gradient-info">
                                        <h3 class="card-title"><i class="fas fa-database"></i> Data Utama</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label"><i class="fas fa-clock text-info"></i> Shift <span class="text-danger">*</span></label>
                                                    <select name="shift_id" class="form-control form-control-border select2" required>
                                                        <option value="">Pilih Shift</option>
                                                        @foreach($shifts as $shift)
                                                            <option value="{{ $shift->id }}" {{ $penyimpanan->shift_id == $shift->id ? 'selected' : '' }}>
                                                                {{ $shift->shift }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label"><i class="fas fa-calendar text-info"></i> Tanggal <span class="text-danger">*</span></label>
                                                    @isset($twoHour)
                                                        <input type="text" name="tanggal_view" class="form-control form-control-border" value="{{ \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s') }}" disabled>
                                                    @else
                                                        <div class="input-group">
                                                            <input type="text" id="tanggalEditable" name="tanggal" class="form-control form-control-border" value="{{ old('tanggal', $penyimpanan->tanggal ? \Carbon\Carbon::parse($penyimpanan->tanggal)->timezone('Asia/Jakarta')->format('d-m-Y H:i:s') : \Carbon\Carbon::now('Asia/Jakarta')->format('d-m-Y H:i:s')) }}" readonly>
                                                            <div class="input-group-append">
                                                                <button class="btn btn-outline-secondary" type="button" onclick="setNowTanggal()">Sekarang</button>
                                                            </div>
                                                        </div>
                                                    @endisset
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                        </div>


                                        <hr class="my-4">
                                        <h5 class="text-info mb-3"><i class="fas fa-search"></i> Data Pemeriksaan</h5>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label"><i class="fas fa-box text-warning"></i> Pemeriksaan Kondisi dan Penempatan Barang <span class="text-danger">*</span></label>
                                                   
                                                    <select name="pemeriksaan_kondisi" class="form-control form-control-border select2" required>
                                                        <option value="">Pilih Status</option>
                                                        <option value="✔" {{ $penyimpanan->pemeriksaan_kondisi == '✔' ? 'selected' : '' }}>✓ </option>
                                                        <option value="✘" {{ $penyimpanan->pemeriksaan_kondisi == '✘' ? 'selected' : '' }}>✗ </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label"><i class="fas fa-broom text-warning"></i> Pemeriksaan Kebersihan Ruangan <span class="text-danger">*</span></label>
                                                   
                                                    <select name="pemeriksaan_kebersihan" class="form-control form-control-border select2" required>
                                                        <option value="">Pilih Status</option>
                                                        <option value="✔" {{ $penyimpanan->pemeriksaan_kebersihan == '✔' ? 'selected' : '' }}>✓ </option>
                                                        <option value="✘" {{ $penyimpanan->pemeriksaan_kebersihan == '✘' ? 'selected' : '' }}>✗ </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label"><i class="fas fa-thermometer-half text-warning"></i> Pemeriksaan Suhu Ruang (°C) <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" 
                                            name="kebersihan_ruang" value="{{ $penyimpanan->kebersihan_ruang }}"
                                           required>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box bg-success">
                                                <span class="info-box-icon">
                                                    <i class="fas fa-check" style="font-size: 2rem;"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Tanda Centang (✓)</span>
                                                    <span class="info-box-number">OK / Sesuai tertata rapi, tagging sesuai, bebas kontaminan, pemisahan antar allergen
                                                    </span>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-success" style="width: 100%"></div>
                                                    </div>
                                                    <span class="progress-description">
                                                        Kondisi baik dan memenuhi standar
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box bg-danger">
                                                <span class="info-box-icon">
                                                    <i class="fas fa-times" style="font-size: 2rem;"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Tanda Silang (✗)</span>
                                                    <span class="info-box-number">Tidak OK</span>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-danger" style="width: 100%"></div>
                                                    </div>
                                                    <span class="progress-description">
                                                        Kondisi tidak sesuai standar
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-info mt-2">
                                        <i class="fas fa-lightbulb"></i>
                                        <strong>Petunjuk:</strong> Pilih tanda centang (✓) jika kondisi sesuai standar, dan tanda silang (✗) jika ditemukan ketidaksesuaian yang perlu diperbaiki.
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="card card-outline card-secondary">
                                            <div class="card-body">
                                                <button type="submit" class="btn btn-warning btn-md mr-2">
                                                    <i class="fas fa-save"></i>
                                                    @isset($twoHour) Simpan Data per 2 Jam (buat riwayat) @else Update Data @endisset
                                                </button>
                                                <a href="{{ route('penyimpanan-bahan.index') }}" class="btn btn-secondary btn-md">
                                                    <i class="fas fa-arrow-left"></i> Kembali
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            @push('scripts')
                            <script>
                            function pad(n){return n<10?'0'+n:n}
                            function setNowTanggal(){
                                const now = new Date();
                                // Convert to Asia/Jakarta (UTC+7) by adjusting offset
                                const utc = now.getTime() + (now.getTimezoneOffset()*60000);
                                const wib = new Date(utc + (7*60*60000));
                                const d = pad(wib.getDate());
                                const m = pad(wib.getMonth()+1);
                                const y = wib.getFullYear();
                                const hh = pad(wib.getHours());
                                const mm = pad(wib.getMinutes());
                                const ss = pad(wib.getSeconds());
                                const formatted = `${d}-${m}-${y} ${hh}:${mm}:${ss}`;
                                const input = document.getElementById('tanggalEditable');
                                if (input) input.value = formatted;
                            }
                            </script>
                            @endpush
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection