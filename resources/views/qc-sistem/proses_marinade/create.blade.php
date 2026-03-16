@extends('layouts.app')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h1 class="m-0">Tambah Proses Marinade</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('proses-marinade.index') }}">Proses Marinade</a></li>
                            <li class="breadcrumb-item active">Tambah Data</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <form method="POST" action="{{ route('proses-marinade.store') }}{{ request('bahan_baku_uuid') ? '?bahan_baku_uuid=' . request('bahan_baku_uuid') : '' }}">
                            @csrf
                            @if(request('bahan_baku_uuid'))
                                <input type="hidden" name="bahan_baku_uuid" value="{{ request('bahan_baku_uuid') }}">
                            @endif
                            @if(isset($bahanBakuTumbling))
                                <div class="card card-info card-outline mb-3">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="fas fa-link"></i> Informasi Relasi
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            @if(isset($bahanBakuTumbling))
                                                <div class="alert alert-info">
                                                    <h6><i class="fas fa-seedling"></i> Data Bahan Baku Tumbling:</h6>
                                                    <ul class="mb-0">
                                                        <li><strong>Nama Produk:</strong> {{ $bahanBakuTumbling->produk->nama_produk ?? '-' }}</li>
                                                        <li><strong>Kode Produksi:</strong> {{ $bahanBakuTumbling->kode_produksi }}</li>
                                                        <!-- <li><strong>Berat:</strong> {{ $bahanBakuTumbling->berat }} kg</li> -->
                                                        <li><strong>Tanggal:</strong> {{ $bahanBakuTumbling->tanggal->format('d-m-Y H:i:s') }}</li>
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-flask"></i> Form Tambah Proses Marinade
                                    </h3>
                                </div>
                                <div class="card-body">
                                    @if($errors->any())
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <ul class="mb-0">
                                                @foreach($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif

                                    @if(session('error'))
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            {{ session('error') }}
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="id_shift">
                                                    <i class="fas fa-clock"></i> Shift
                                                </label>
                                                <select name="id_shift" id="id_shift" class="form-control @error('id_shift') is-invalid @enderror" required>
                                                    <option value="">-- Pilih Shift --</option>
                                                    @foreach($shifts as $shift)
                                                        <option value="{{ $shift->id }}" {{ old('id_shift') == $shift->id ? 'selected' : '' }}>
                                                            {{ $shift->shift }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('id_shift')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="tanggal">
                                                    <i class="fas fa-calendar"></i> Tanggal
                                                </label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                                    </div>
                                                    <input type="text" name="tanggal" id="tanggal" 
                                                           class="form-control @error('tanggal') is-invalid @enderror" 
                                                           value="{{ old('tanggal', now('Asia/Jakarta')->format('d-m-Y H:i:s')) }}" readonly>
                                                </div>
                                                @error('tanggal')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="id_produk">
                                                    <i class="fas fa-box"></i> Nama Produk
                                                </label>
                                                <select name="id_produk" id="id_produk" class="form-control @error('id_produk') is-invalid @enderror" required>
                                                    <option value="">-- Pilih Nama Produk --</option>
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
                                                <label for="id_jenis_marinade">
                                                    <i class="fas fa-flask"></i> Jenis Marinade
                                                </label>
                                                <select name="id_jenis_marinade" id="id_jenis_marinade" class="form-control @error('id_jenis_marinade') is-invalid @enderror" required>
                                                    <option value="">-- Pilih Produk Terlebih Dahulu --</option>
                                                </select>
                                                @error('id_jenis_marinade')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="kode_produksi">
                                                    <i class="fas fa-barcode"></i> Kode Produksi
                                                </label>
                                                <input type="text" name="kode_produksi" id="kode_produksi" 
                                                       class="form-control @error('kode_produksi') is-invalid @enderror" 
                                                       value="{{ old('kode_produksi') }}" placeholder="Masukkan kode produksi..." required>
                                                @error('kode_produksi')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="jumlah">
                                                    <i class="fas fa-weight"></i> Jumlah (Kg)
                                                </label>
                                                <input type="number" step="0.01" name="jumlah" id="jumlah" 
                                                       class="form-control @error('jumlah') is-invalid @enderror" 
                                                       value="{{ old('jumlah') }}" required>
                                                @error('jumlah')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="hasil_pencampuran">
                                            <i class="fas fa-clipboard-check"></i> Hasil Pencampuran
                                        </label>
                                        <select name="hasil_pencampuran" id="hasil_pencampuran" class="form-control @error('hasil_pencampuran') is-invalid @enderror" required>
                                            <option value="">-- Pilih Hasil Pencampuran --</option>
                                            <option value="✔" {{ old('hasil_pencampuran') == '✔' ? 'selected' : '' }}>✔</option>
                                            <option value="✘" {{ old('hasil_pencampuran') == '✘' ? 'selected' : '' }}>✘</option>
                                        </select>
                                        @error('hasil_pencampuran')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Simpan Data
                                    </button>
                                    <a href="{{ route('proses-marinade.index') }}" class="ml-2 btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
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
