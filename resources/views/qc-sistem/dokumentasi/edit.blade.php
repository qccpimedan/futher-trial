@extends('layouts.app')
@php
    $isLocalhost = in_array(request()->getHost(), ['localhost', '127.0.0.1', '10.68.1.37']);
    $assetPath = $isLocalhost ? 'public/' : '';
@endphp
@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">
                            <i class="fas fa-edit text-warning mr-2"></i>
                            Edit Dokumentasi
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dokumentasi.index') }}">
                                    <i class="fas fa-home"></i> Dashboard
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('dokumentasi.index') }}">Dokumentasi</a>
                            </li>
                            <li class="breadcrumb-item active">Edit Data</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        
        <section class="content">
            <div class="container-fluid">
                <form action="{{ route('dokumentasi.update', $item->uuid) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                      @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @method('PUT')
                    
                    <!-- Card for Basic Information -->
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle"></i>
                                Informasi Dasar
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
                                    <div class="form-group">
                                        <label for="shift_id">
                                            <i class="fas fa-clock text-primary"></i>
                                            Shift <span class="text-danger">*</span>
                                        </label>
                                        <select name="shift_id" id="shift_id" class="form-control" required>
                                            <option value="">Pilih Shift</option>
                                            @foreach($shifts as $shift)
                                                <option value="{{ $shift->id }}" {{ $item->id_shift == $shift->id ? 'selected' : '' }}>
                                                    {{ $shift->shift }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tanggal">
                                            <i class="fas fa-calendar text-danger"></i>
                                            Tanggal <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="tanggal" id="tanggal" 
                                            class="form-control" value="{{ old('tanggal', $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y H:i:s') : '') }}" readonly>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>

                    <!-- Card for Product Information -->
                    <div class="card card-success card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-box"></i>
                                Informasi Produk
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="id_produk">
                                            <i class="fas fa-cube text-info"></i>
                                            Nama Produk <span class="text-danger">*</span>
                                        </label>

                                        <input type="text" 
        class="form-control" 
        value="{{$item->pengemasanProduk->kode_produksi ?? 'data kosong' }} -{{ $item->pengemasanProduk->produk->nama_produk ?? 'data kosong' }} {{ $item->pengemasanProduk->berat ?? 'data kosong' }} gram" 
        readonly>

                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>

                    <!-- Card for Quality Control -->
                    <div class="card card-warning card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-clipboard-check"></i>
                                Hasil Dokumentasi
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                   <div class="form-group mb-3">
                                        <label for="foto_kode_produksi" class="font-weight-bold">Foto Kode Produksi dan Best Before</label>
                                        @if(!empty($item->foto_kode_produksi))
                                       
                                            <div class="mb-2">
                                                <img src="{{ asset($assetPath . 'storage/' . $item->foto_kode_produksi) }}" alt="Foto Kode Produksi" style="width:80px; height:80px; object-fit:cover; border-radius:4px;">
                                            </div>
                                        @endif
                                        <input type="file" name="foto_kode_produksi" id="foto_kode_produksi" class="form-control-file" accept="image/*" capture="camera">
                                        <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah foto.</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                   <div class="form-group mb-3">
                                        <label for="qr_code" class="font-weight-bold">QR Code</label>
                                        @if(!empty($item->qr_code))
                                            <div class="mb-2">
                                                <img src="{{ asset($assetPath . 'storage/' . $item->qr_code) }}" alt="QR Code" style="width:80px; height:80px; object-fit:cover; border-radius:4px;">
                                            </div>
                                        @endif
                                        <input type="file" name="qr_code" id="qr_code" class="form-control-file" accept="image/*" capture="camera">
                                        <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah foto.</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                   <div class="form-group mb-4">
                                        <label for="label_polyroll" class="font-weight-bold">Foto Label Polyroll yang digunakan</label>
                                        @if(!empty($item->label_polyroll))
                                            <div class="mb-2">
                                                <img src="{{ asset($assetPath . 'storage/' . $item->label_polyroll) }}" alt="Label Polyroll" style="width:80px; height:80px; object-fit:cover; border-radius:4px;">
                                            </div>
                                        @endif
                                        <input type="file" name="label_polyroll" id="label_polyroll" class="form-control-file" accept="image/*" capture="camera">
                                        <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah foto.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                   

                    <!-- Action Buttons -->
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-primary btn-md mr-3">
                                        <i class="fas fa-save"></i>
                                        Update Data
                                    </button>
                                    <a href="{{ route('dokumentasi.index') }}" class="btn btn-secondary btn-md">
                                        <i class="fas fa-arrow-left"></i>
                                        Kembali
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </form>
            </div>
        </section>
    </div>
</div>
@endsection
