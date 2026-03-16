@extends('layouts.app')

@section('title', 'Edit Pembuatan Predust')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Edit Pembuatan Predust</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('pembuatan-predust.index') }}">Pembuatan Predust</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <form action="{{ route('pembuatan-predust.update', $pembuatanPredust->uuid) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-edit mr-2"></i>Form Edit Pembuatan Predust
                            </h3>
                        </div>
                        <div class="card-body">
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
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="id_produk">Produk <span class="text-danger">*</span></label>
                                        <select name="id_produk" id="id_produk" class="form-control @error('id_produk') is-invalid @enderror" required>
                                            <option value="">Pilih Produk</option>
                                            @foreach($produks as $produk)
                                                <option value="{{ $produk->id }}" {{ (old('id_produk', $pembuatanPredust->id_produk) == $produk->id) ? 'selected' : '' }}>
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
                                        <label for="id_jenis_predust">Jenis Predust <span class="text-danger">*</span></label>
                                        <select name="id_jenis_predust" id="id_jenis_predust" class="form-control @error('id_jenis_predust') is-invalid @enderror" required>
                                            <option value="">Pilih Jenis Predust</option>
                                            @foreach($jenisPredust as $predust)
                                                <option value="{{ $predust->id }}" {{ (old('id_jenis_predust', $pembuatanPredust->id_jenis_predust) == $predust->id) ? 'selected' : '' }}>
                                                    {{ $predust->jenis_predust }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_jenis_predust')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="tanggal" class="font-weight-bold">Tanggal <span class="text-danger">*</span></label>
                                        <input type="datetime-local" 
                                               class="form-control @error('tanggal') is-invalid @enderror" 
                                               id="tanggal" 
                                               name="tanggal" 
                                               value="{{ old('tanggal') ?? ($pembuatanPredust->tanggal ? $pembuatanPredust->tanggal->format('Y-m-d\TH:i') : '') }}" 
                                               readonly>
                                        @error('tanggal')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="kondisi_predust">Kondisi Predust <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               name="kondisi_predust" 
                                               id="kondisi_predust" 
                                               class="form-control @error('kondisi_predust') is-invalid @enderror" 
                                               value="{{ old('kondisi_predust', $pembuatanPredust->kondisi_predust) }}" 
                                               placeholder="Masukkan kondisi predust"
                                               required>
                                        @error('kondisi_predust')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="hasil_pencetakan">Hasil Pencetakan <span class="text-danger">*</span></label>
                                        <select name="hasil_pencetakan" id="hasil_pencetakan" class="form-control @error('hasil_pencetakan') is-invalid @enderror" required>
                                            <option value="">Pilih Hasil Pencetakan</option>
                                            <option value="oke" {{ old('hasil_pencetakan', $pembuatanPredust->hasil_pencetakan) == 'oke' ? 'selected' : '' }}>
                                                <i class="fas fa-check"></i> OK
                                            </option>
                                            <option value="tidak ok" {{ old('hasil_pencetakan', $pembuatanPredust->hasil_pencetakan) == 'tidak ok' ? 'selected' : '' }}>
                                                <i class="fas fa-times"></i> Tidak OK
                                            </option>
                                        </select>
                                        @error('hasil_pencetakan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="kode_produksi">Kode Produksi <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               name="kode_produksi" 
                                               id="kode_produksi" 
                                               class="form-control @error('kode_produksi') is-invalid @enderror" 
                                               value="{{ old('kode_produksi', $pembuatanPredust->kode_produksi) }}" 
                                               placeholder="Masukkan kode produksi"
                                               required>
                                        @error('kode_produksi')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Data
                            </button>
                            <a href="{{ route('pembuatan-predust.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Handle product selection change
    $('#id_produk').change(function() {
        var produkId = $(this).val();
        getJenisPredustByProduk(produkId, 'id_jenis_predust');
    });
});
</script>
@endpush