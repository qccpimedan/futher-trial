@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Proses Roasting</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('input-roasting.index') }}">Input Roasting</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Form Edit Proses Roasting</h3>
                            <div class="card-tools">
                                <!-- <a href="{{ route('input-roasting.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a> -->
                            </div>
                        </div>
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

                            <form action="{{ route('input-roasting.update', $inputRoasting->uuid) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                                            <input type="text" name="tanggal" id="tanggal" 
                                                   class="form-control @error('tanggal') is-invalid @enderror" 
                                                   value="{{ old('tanggal') ?? \Carbon\Carbon::parse($inputRoasting->tanggal)->format('d-m-Y H:i:s') }}" readonly>
                                            @error('tanggal')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="jam"><i class="fas fa-clock"></i> Jam <span class="text-danger">*</span></label>
                                            <input type="time" name="jam" id="jam" 
                                                   class="form-control @error('jam') is-invalid @enderror" 
                                                   value="{{ old('jam') ?? \Carbon\Carbon::parse($inputRoasting->jam)->format('H:i') }}" required>
                                            @error('jam')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="shift_id">Shift <span class="text-danger">*</span></label>
                                            <select name="shift_id" id="shift_id" class="form-control @error('shift_id') is-invalid @enderror" required>
                                                <option value="">Pilih Shift</option>
                                                @foreach($shifts as $shift)
                                                    <option value="{{ $shift->id }}" {{ (old('shift_id') ?? $inputRoasting->shift_id) == $shift->id ? 'selected' : '' }}>
                                                        {{ $shift->shift }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('shift_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="id_produk">Produk <span class="text-danger">*</span></label>
                                            <select name="id_produk" id="id_produk" class="form-control @error('id_produk') is-invalid @enderror" required>
                                                <option value="">Pilih Produk</option>
                                                @foreach($produks as $produk)
                                                    <option value="{{ $produk->id }}" {{ (old('id_produk') ?? $inputRoasting->id_produk) == $produk->id ? 'selected' : '' }}>
                                                        {{ $produk->nama_produk }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('id_produk')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="kode_produksi">Kode Produksi <span class="text-danger">*</span></label>
                                            <input type="text" name="kode_produksi" id="kode_produksi" 
                                                   class="form-control @error('kode_produksi') is-invalid @enderror" 
                                                   value="{{ old('kode_produksi') ?? $inputRoasting->kode_produksi }}" 
                                                   placeholder="Masukkan kode produksi" required>
                                            @error('kode_produksi')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Berat Produk (gr) <span class="text-danger">*</span></label>
                                            <select id="edit_nilai_select_berat_roasting" class="form-control" name="berat_produk">  
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="std_suhu_sebelum">Std Suhu Sebelum Masak<span class="text-danger">*</span></label>
                                            <input type="text" name="std_suhu_sebelum" id="std_suhu_sebelum" 
                                                   class="form-control @error('std_suhu_sebelum') is-invalid @enderror" 
                                                   value="{{ old('std_suhu_sebelum') ?? $inputRoasting->std_suhu_sebelum }}" 
                                                   placeholder="Masukkan std suhu sebelum" required>
                                            @error('std_suhu_sebelum')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="aktual_suhu_sesudah">Aktual Suhu Sebelum Masak <span class="text-danger">*</span></label>
                                            <input type="text" name="aktual_suhu_sesudah" id="aktual_suhu_sesudah" 
                                                   class="form-control @error('aktual_suhu_sesudah') is-invalid @enderror" 
                                                   value="{{ old('aktual_suhu_sesudah') ?? $inputRoasting->aktual_suhu_sesudah }}" 
                                                   placeholder="Masukkan aktual suhu sesudah" required>
                                            @error('aktual_suhu_sesudah')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save"></i> Update Data
                                    </button>
                                    <a href="{{ route('input-roasting.index') }}" class="ml-2 btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
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