@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Bahan Baku Roasting</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('bahan-baku-roasting.index') }}">Bahan Baku Roasting</a></li>
                        <li class="breadcrumb-item active">Edit</li>
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

                                    <form action="{{ route('bahan-baku-roasting.update', $bahanBakuRoasting->uuid) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                                                    <input type="text" name="tanggal" id="tanggal" 
                                                           class="form-control @error('tanggal') is-invalid @enderror" 
                                                           value="{{ old('tanggal', \Carbon\Carbon::parse($bahanBakuRoasting->tanggal)->format('d-m-Y H:i:s')) }}" 
                                                           readonly>
                                                    @error('tanggal')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="id_produk">Produk <span class="text-danger">*</span></label>
                                                    <select name="id_produk" id="id_produk" class="form-control @error('id_produk') is-invalid @enderror" required>
                                                        <option value="">Pilih Produk</option>
                                                        @foreach($produks as $produk)
                                                            <option value="{{ $produk->id }}" {{ (old('id_produk') ?? $bahanBakuRoasting->id_produk) == $produk->id ? 'selected' : '' }}>
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
                                                    <input type="text" name="kode_produksi_rm" id="kode_produksi_rm" 
                                                           class="form-control @error('kode_produksi_rm') is-invalid @enderror" 
                                                           value="{{ old('kode_produksi_rm') ?? $bahanBakuRoasting->kode_produksi_rm }}" 
                                                           placeholder="Masukkan kode produksi raw material" required>
                                                    @error('kode_produksi_rm')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="standart_suhu_rm">Standar Suhu RM <span class="text-danger">*</span></label>
                                                    <input type="text" name="standart_suhu_rm" id="standart_suhu_rm" 
                                                           class="form-control @error('standart_suhu_rm') is-invalid @enderror" 
                                                           value="{{ old('standart_suhu_rm') ?? $bahanBakuRoasting->standart_suhu_rm }}" 
                                                           placeholder="Contoh: -18°C" required>
                                                    @error('standart_suhu_rm')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="aktual_suhu_rm">Aktual Suhu RM <span class="text-danger">*</span></label>
                                                    <input type="text" name="aktual_suhu_rm" id="aktual_suhu_rm" 
                                                           class="form-control @error('aktual_suhu_rm') is-invalid @enderror" 
                                                           value="{{ old('aktual_suhu_rm') ?? $bahanBakuRoasting->aktual_suhu_rm }}" 
                                                           placeholder="Contoh: -17°C" required>
                                                    @error('aktual_suhu_rm')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <button type="submit" class="btn btn-warning">
                                                <i class="fas fa-save"></i> Update Data
                                            </button>
                                            <a href="{{ route('bahan-baku-roasting.index') }}" class="btn btn-secondary ml-2">
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
@endsection
