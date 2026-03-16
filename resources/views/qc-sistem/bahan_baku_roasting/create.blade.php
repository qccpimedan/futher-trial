@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Tambah Bahan Baku Roasting</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('bahan-baku-roasting.index') }}">Bahan Baku Roasting</a></li>
                        <li class="breadcrumb-item active">Tambah</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item"><a class="nav-link active" href="#bahan-baku" data-toggle="tab">Bahan Baku</a></li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane active" id="bahan-baku">
                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if($inputRoastingData)
                                        <div class="alert alert-info">
                                            <h5><i class="icon fas fa-info"></i> Data Input Roasting Terkait</h5>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($inputRoastingData->tanggal)->format('d-m-Y H:i:s') }}<br>
                                                    <strong>Produk:</strong> {{ $inputRoastingData->produk->nama_produk ?? '-' }}<br>
                                                    <strong>Kode Produksi:</strong> {{ $inputRoastingData->kode_produksi }}<br>
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Shift:</strong> {{ $inputRoastingData->shift->shift ?? '-' }}<br>
                                                    <strong>Waktu Pemasakan:</strong> {{ $inputRoastingData->waktu_pemasakan }} menit<br>
                                                    <strong>User:</strong> {{ $inputRoastingData->user->name ?? '-' }}<br>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <form action="{{ route('bahan-baku-roasting.store') }}" method="POST">
                                        @csrf
                                        
                                        <!-- Hidden field untuk input_roasting_uuid -->
                                        @if($inputRoastingUuid)
                                            <input type="hidden" name="input_roasting_uuid" value="{{ $inputRoastingUuid }}">
                                        @endif
                                        
                                        <div class="card card-primary card-outline">
                                            <div class="card-header">
                                                <h3 class="card-title">Informasi Dasar</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                                                            <input type="text" name="tanggal" id="tanggal" 
                                                                    class="form-control @error('tanggal') is-invalid @enderror" 
                                                                    value="{{ old('tanggal', \Carbon\Carbon::now('Asia/Jakarta')->format('d-m-Y H:i:s')) }}" readonly>
                                                            @error('tanggal')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card card-primary card-outline mt-4">
                                            <div class="card-header">
                                                <h3 class="card-title">Detail Produksi</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">  
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="id_produk">Produk <span class="text-danger">*</span></label>
                                                            <select name="id_produk" id="id_produk" 
                                                                    class="form-control select2 @error('id_produk') is-invalid @enderror" 
                                                                    style="width: 100%;" required>
                                                                <option value="">Pilih Produk</option>
                                                                @foreach($produks as $produk)
                                                                    <option value="{{ $produk->id }}" {{ old('id_produk') == $produk->id ? 'selected' : '' }}>
                                                                        {{ $produk->nama_produk }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('id_produk')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="kode_produksi_rm">Kode Produksi RM <span class="text-danger">*</span></label>
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                                                                </div>
                                                                <input type="text" name="kode_produksi_rm" id="kode_produksi_rm" 
                                                                       class="form-control @error('kode_produksi_rm') is-invalid @enderror" 
                                                                       value="{{ old('kode_produksi_rm') }}" 
                                                                       placeholder="Masukkan kode produksi" required>
                                                                @error('kode_produksi_rm')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card card-primary card-outline mt-4">
                                            <div class="card-header">
                                                <h3 class="card-title">Parameter Kualitas</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="standart_suhu_rm">Standar Suhu RM <span class="text-danger">*</span></label>
                                                            <div class="input-group">
                                                                <input type="number" step="0.01" name="standart_suhu_rm" id="standart_suhu_rm" 
                                                                       class="form-control @error('standart_suhu_rm') is-invalid @enderror" 
                                                                       value="{{ old('standart_suhu_rm') }}" 
                                                                       placeholder="Masukkan standar suhu" required>
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">°C</span>
                                                                </div>
                                                                @error('standart_suhu_rm')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="aktual_suhu_rm">Aktual Suhu RM <span class="text-danger">*</span></label>
                                                            <div class="input-group">
                                                                <input type="number" step="0.01" name="aktual_suhu_rm" id="aktual_suhu_rm" 
                                                                       class="form-control @error('aktual_suhu_rm') is-invalid @enderror" 
                                                                       value="{{ old('aktual_suhu_rm') }}" 
                                                                       placeholder="Masukkan suhu aktual" required>
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">°C</span>
                                                                </div>
                                                                @error('aktual_suhu_rm')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-4">
                                            <div class="col-12">
                                                <div class="">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-save mr-1"></i> Simpan Data
                                                    </button>
                                                    <a href="{{ route('bahan-baku-roasting.index') }}" class="btn btn-secondary ml-2">
                                                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                                                    </a>
                                                </div>
                                            </div>
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
@endsection
