@extends('layouts.app')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">
                            <i class="fas fa-edit text-warning mr-2"></i>
                            Edit Data Proses Battering
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a href="#">
                                    <i class="fas fa-home"></i> Dashboard
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('proses-battering.index') }}">Proses Battering</a>
                            </li>
                            <li class="breadcrumb-item active">Edit Data</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-clipboard-list text-warning mr-2"></i>
                            Form Edit Proses Battering
                        </h3>
                    </div>
                    
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h5><i class="icon fas fa-ban"></i> Error!</h5>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <div class="card-body">
                        <form action="{{ route('proses-battering.update', $item->uuid) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-box text-primary mr-1"></i>
                                            <strong>Produk</strong>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select name="id_produk" class="form-control" required>
                                            <option value="">-- Pilih Produk --</option>
                                            @foreach($produks as $produk)
                                                <option value="{{ $produk->id }}" {{ old('id_produk', $item->id_produk) == $produk->id ? 'selected' : '' }}>
                                                    {{ $produk->nama_produk }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-calendar text-success mr-1"></i>
                                            <strong>Tanggal</strong>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="tanggal" class="form-control" value="{{ old('tanggal', $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y H:i:s') : '') }}" readonly>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="id_jenis_better">
                                            <i class="fas fa-flask text-success"></i>
                                            <strong>Jenis Better</strong>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-control" name="id_jenis_better" required>
                                            <option value="">-- Pilih Jenis Better --</option>
                                            @foreach($jenis_batters as $jenis)
                                                <option value="{{ $jenis->id }}" {{ old('id_jenis_better', $item->id_jenis_better) == $jenis->id ? 'selected' : '' }}>
                                                    {{ $jenis->nama_better }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="kode_produksi_better">
                                            <i class="fas fa-barcode text-primary"></i>
                                            <strong>Kode Produksi Better</strong>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="kode_produksi_better" class="form-control" 
                                               value="{{ old('kode_produksi_better', $item->kode_produksi_better) }}" 
                                               placeholder="Masukkan kode produksi better" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="hasil_better">
                                            <i class="fas fa-check-circle text-warning"></i>
                                            <strong>Hasil Better</strong>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-control" name="hasil_better" required>
                                            <option value="">-- Pilih Hasil --</option>
                                            <option value="✔" {{ old('hasil_better', $item->hasil_better) == '✔' ? 'selected' : '' }}>
                                            ✔
                                            </option>
                                            <option value="✘" {{ old('hasil_better', $item->hasil_better) == '✘' ? 'selected' : '' }}>
                                            ✘
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-warning btn-md mr-3">
                                            <i class="fas fa-save mr-2"></i>
                                            Update Data
                                        </button>
                                        <a href="{{ route('proses-battering.index') }}" class="btn btn-secondary btn-md">
                                            <i class="fas fa-arrow-left mr-2"></i>
                                            Kembali
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection