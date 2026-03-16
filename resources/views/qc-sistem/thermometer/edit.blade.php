@extends('layouts.app')

@section('title', 'Edit Data Thermometer')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Edit Data Thermometer</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Home</a></li>
                            <!-- <li class="breadcrumb-item"><a href="#">QC Sistem</a></li> -->
                            <li class="breadcrumb-item"><a href="{{ route('thermometer.index') }}">Data Thermometer</a></li>
                            <li class="breadcrumb-item active">Edit Data</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-warning">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-thermometer-half mr-1"></i>
                                    Form Edit Data Thermometer
                                </h3>
                            </div>
                            <!-- /.card-header -->
                            
                            <form action="{{ route('thermometer.update', $data->uuid) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="card-body">
                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <div class="row">
                                        <!-- Kolom Kiri -->
                                        <div class="col-md-6">
                                            <div class="card card-outline card-info">
                                                <div class="card-header">
                                                    <h3 class="card-title">
                                                        <i class="fas fa-info-circle"></i>
                                                        Informasi Dasar
                                                    </h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="shift_id">
                                                            <i class="fas fa-clock"></i>
                                                            Shift <span class="text-danger">*</span>
                                                        </label>
                                                        <select name="shift_id" id="shift_id" class="form-control @error('shift_id') is-invalid @enderror" required>
                                                            <option value="">-- Pilih Shift --</option>
                                                            @foreach($shifts as $shift)
                                                                <option value="{{ $shift->id }}" {{ (old('shift_id', $data->shift_id) == $shift->id) ? 'selected' : '' }}>
                                                                    {{ $shift->shift }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('shift_id')
                                                            <span class="invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="tanggal">
                                                            <i class="fas fa-calendar-alt"></i>
                                                            Tanggal <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="date"
                                                            name="tanggal"
                                                            id="tanggal"
                                                            class="form-control @error('tanggal') is-invalid @enderror"
                                                            value="{{ old('tanggal', $data->tanggal ? \Carbon\Carbon::parse($data->tanggal)->format('Y-m-d') : '') }}"
                                                            required>
                                                        @error('tanggal')
                                                            <span class="invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="jam">
                                                            <i class="fas fa-clock"></i>
                                                            Pukul <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="time"
                                                            name="jam"
                                                            id="jam"
                                                            class="form-control @error('jam') is-invalid @enderror"
                                                            value="{{ old('jam', $data->jam ? \Carbon\Carbon::parse($data->jam)->format('H:i') : '') }}"
                                                            required>
                                                        @error('jam')
                                                            <span class="invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="data_thermo_select">
                                                            <i class="fas fa-thermometer-half"></i>
                                                            Jenis dan Kode Thermometer <span class="text-danger">*</span>
                                                        </label>
                                                        <select name="data_thermo_select" 
                                                                id="id_produk_select" 
                                                                class="form-control @error('jenis') is-invalid @enderror">
                                                            <option value="">-- Pilih Thermometer --</option>
                                                            @foreach($dataThermo as $thermo)
                                                                @php
                                                                    $isSelected = ($thermo->nama_thermo === $data->jenis && $thermo->kode_thermo === $data->kode_thermometer);
                                                                @endphp
                                                                <option value="{{ $thermo->id }}" 
                                                                        data-jenis="{{ $thermo->nama_thermo }}"
                                                                        data-kode="{{ $thermo->kode_thermo }}"
                                                                        {{ $isSelected ? 'selected' : '' }}>
                                                                    {{ $thermo->nama_thermo }} - {{ $thermo->kode_thermo }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <input type="hidden" name="jenis" id="jenis" value="{{ old('jenis', $data->jenis) }}" required>
                                                        <input type="hidden" name="kode_thermometer" id="kode_thermometer" value="{{ old('kode_thermometer', $data->kode_thermometer) }}" required>
                                                        @error('jenis')
                                                            <span class="invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Kolom Kanan -->
                                        <div class="col-md-6">
                                            <div class="card card-outline card-warning">
                                                <div class="card-header">
                                                    <h3 class="card-title">
                                                        <i class="fas fa-thermometer-half"></i>
                                                        Parameter Pengecekan
                                                    </h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="kode_thermometer">
                                                            <i class="fas fa-barcode"></i>
                                                            Kode Thermometer <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="text" 
                                                            name="kode_thermometer" 
                                                            id="kode_thermometer" 
                                                            class="form-control @error('kode_thermometer') is-invalid @enderror" 
                                                            value="{{ old('kode_thermometer', $data->kode_thermometer) }}" 
                                                            placeholder="Masukkan kode thermometer"
                                                            required>
                                                        @error('kode_thermometer')
                                                            <span class="invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="hasil_verifikasi_0">
                                                            <i class="fas fa-thermometer-half"></i>
                                                            Hasil Verifikasi 0&deg;C
                                                        </label>
                                                        <input type="text"
                                                            name="hasil_verifikasi_0"
                                                            id="hasil_verifikasi_0"
                                                            class="form-control @error('hasil_verifikasi_0') is-invalid @enderror"
                                                            value="{{ old('hasil_verifikasi_0', $data->hasil_verifikasi_0) }}"
                                                            placeholder="Masukkan hasil verifikasi 0°C">
                                                        @error('hasil_verifikasi_0')
                                                            <span class="invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="hasil_verifikasi_100">
                                                            <i class="fas fa-thermometer-half"></i>
                                                            Hasil Verifikasi 100&deg;C
                                                        </label>
                                                        <input type="text"
                                                            name="hasil_verifikasi_100"
                                                            id="hasil_verifikasi_100"
                                                            class="form-control @error('hasil_verifikasi_100') is-invalid @enderror"
                                                            value="{{ old('hasil_verifikasi_100', $data->hasil_verifikasi_100) }}"
                                                            placeholder="Masukkan hasil verifikasi 100°C">
                                                        @error('hasil_verifikasi_100')
                                                            <span class="invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="hasil_pengecekan">
                                                            <i class="fas fa-check-circle"></i>
                                                            Hasil Pengecekan <span class="text-danger">*</span>
                                                        </label>
                                                        <select name="hasil_pengecekan" id="hasil_pengecekan" class="form-control @error('hasil_pengecekan') is-invalid @enderror" required>
                                                            <option value="">-- Pilih Hasil Pengecekan --</option>
                                                            @foreach($hasilPengecekanOptions as $value => $label)
                                                                <option value="{{ $value }}" {{ (old('hasil_pengecekan', $data->hasil_pengecekan) == $value) ? 'selected' : '' }}>
                                                                    {!! $label !!}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('hasil_pengecekan')
                                                            <span class="invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>

                                                    <div class="alert alert-info">
                                                        <i class="fas fa-info-circle"></i>
                                                        <strong>Informasi:</strong>
                                                        <ul class="mb-0 mt-2">
                                                            <li>✓ OK = Hasil pengecekan sesuai standar</li>
                                                            <li>✗ Tidak OK = Hasil pengecekan tidak sesuai standar</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <button type="submit" class="btn btn-warning">
                                                <i class="fas fa-save"></i> Update Data
                                            </button>
                                            <a href="{{ route('thermometer.index') }}" class="btn btn-secondary ml-2">
                                                <i class="fas fa-arrow-left"></i> Kembali
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
</div>
@endsection