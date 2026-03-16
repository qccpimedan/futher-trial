@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1> Tambah Data Penyimpanan Bahan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('penyimpanan-bahan.index') }}"><i class="fas fa-warehouse"></i> Penyimpanan Bahan</a></li>
                        <li class="breadcrumb-item active">Tambah Data</li>
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
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-edit"></i> Form Input Penyimpanan Bahan</h3>
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
                            <!-- Card Informasi Simbol -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="card card-info card-outline">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="fas fa-info-circle"></i> Keterangan Simbol
                                            </h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-minus"></i>
                                                </button>
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
                                    </div>
                                </div>
                            </div>
                            <form class="form-horizontal" id="main-chillroom-form" method="POST" action="{{ route('penyimpanan-bahan.store') }}">
                                @csrf
                                <div id="form-container-chillroom">
                                    <div class="chillroom-form card card-outline card-info shadow-sm">
                                        <div class="card-header">
                                            <h3 class="card-title"><i class="fas fa-clipboard-check"></i> Data Pemeriksaan</h3>
                                        </div>
                                        <div class="card-body">                                            
                                            <h5 class="text-info mb-3"><i class="fas fa-info-circle"></i> Informasi Tambahan</h5>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label"><i class="fas fa-clock text-info"></i> Shift <span class="text-danger">*</span></label>
                                                        <select class="form-control form-control-border select2" name="shift_id" required>
                                                            <option value="">Pilih Shift</option>
                                                            @foreach($shifts as $shift)
                                                                <option value="{{ $shift->id }}">{{ $shift->shift }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label"><i class="fas fa-calendar text-info"></i> Tanggal <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control form-control-border" value="{{ old('tanggal', \Carbon\Carbon::now('Asia/Jakarta')->format('d-m-Y H:i:s')) }}" name="tanggal" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-12">
                                                    <h5 class="text-info mb-3"><i class="fas fa-search"></i> Pemeriksaan Kondisi</h5>
                                                </div>
                                            </div>
                                            <!-- Pemeriksaan Section -->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label"><i class="fas fa-box text-warning"></i> Pemeriksaan Kondisi dan Penempatan Barang <span class="text-danger">*</span></label>
                                                        <select class="form-control form-control-border select2" name="pemeriksaan_kondisi[]" required>
                                                            <option value="">Pilih Status</option>
                                                            <option value="✔">✓ </option>
                                                            <option value="✘">✗ </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label"><i class="fas fa-broom text-warning"></i> Pemeriksaan Kebersihan Ruangan <span class="text-danger">*</span></label>
                                                        <select class="form-control form-control-border select2" name="pemeriksaan_kebersihan[]" required>
                                                            <option value="">Pilih Status</option>
                                                            <option value="✔">✓</option>
                                                            <option value="✘">✗</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label"><i class="fas fa-thermometer-half text-warning"></i> Pemeriksaan Suhu Ruang (°C) <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" 
                                                            name="kebersihan_ruang[]" 
                                                            required>                                
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="card card-outline card-secondary">
                                            <div class="card-body">
                                                <button type="submit" class="btn btn-primary btn-md mr-2">
                                                    <i class="fas fa-save"></i> Simpan Semua
                                                </button>
                                                <a href="{{ route('penyimpanan-bahan.index') }}" class="btn btn-secondary btn-md">
                                                    <i class="fas fa-arrow-left"></i> Kembali
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection