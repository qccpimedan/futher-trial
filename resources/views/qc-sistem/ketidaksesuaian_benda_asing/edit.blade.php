@extends('layouts.app')
@php
    $isLocalhost = in_array(request()->getHost(), ['localhost', '127.0.0.1', '10.68.1.37']);
    $assetPath = $isLocalhost ? 'public/' : '';
@endphp
@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Ketidaksesuaian Benda Asing</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('ketidaksesuaian-benda-asing.index') }}">Ketidaksesuaian Benda Asing</a></li>
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
                                <li class="nav-item"><a class="nav-link active" href="#ketidaksesuaian" data-toggle="tab">Edit Ketidaksesuaian Benda Asing</a></li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="active tab-pane" id="ketidaksesuaian">
                                    <form action="{{ route('ketidaksesuaian-benda-asing.update', $ketidaksesuaianBendaAsing->uuid) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
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
                                        <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="id_shift" class="font-weight-bold">Shift <span class="text-danger">*</span></label>
                                                    <select name="shift_id" id="id_shift" class="form-control" required>
                                                        <option value="">Pilih Shift</option>
                                                        @foreach($shifts as $shift)
                                                            <option value="{{ $shift->id }}" 
                                                                {{ (old('shift_id', $ketidaksesuaianBendaAsing->shift_id) == $shift->id) ? 'selected' : '' }}>
                                                                {{ $shift->shift }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="id_produk" class="font-weight-bold">Produk <span class="text-danger">*</span></label>
                                                    <select name="id_produk" id="id_produk" class="form-control @error('id_produk') is-invalid @enderror" required>
                                                        <option value="">Pilih Produk</option>
                                                        @foreach($produks as $produk)
                                                            <option value="{{ $produk->id }}" {{ (old('id_produk', $ketidaksesuaianBendaAsing->id_produk) == $produk->id) ? 'selected' : '' }}>
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
                                                <div class="form-group mb-3">
                                                    <label for="tanggal" class="font-weight-bold">Tanggal <span class="text-danger">*</span></label>
                                                    <input type="datetime-local" name="tanggal" id="tanggal" class="form-control @error('tanggal') is-invalid @enderror" 
                                                           value="{{ old('tanggal', $ketidaksesuaianBendaAsing->tanggal ? $ketidaksesuaianBendaAsing->tanggal->format('Y-m-d\TH:i') : '') }}" readonly>
                                                    @error('tanggal')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="jam" class="font-weight-bold">Jam <span class="text-danger">*</span></label>
                                                    <input type="time" name="jam" id="jam" class="form-control @error('jam') is-invalid @enderror" 
                                                           value="{{ old('jam', $ketidaksesuaianBendaAsing->jam) }}" required>
                                                    @error('jam')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="kode_produksi" class="font-weight-bold">Kode Produksi <span class="text-danger">*</span></label>
                                                    <input type="text" name="kode_produksi" id="kode_produksi" class="form-control @error('kode_produksi') is-invalid @enderror" 
                                                           value="{{ old('kode_produksi', $ketidaksesuaianBendaAsing->kode_produksi) }}" placeholder="Masukkan kode produksi" required>
                                                    @error('kode_produksi')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="jenis_kontaminan" class="font-weight-bold">Jenis Kontaminan <span class="text-danger">*</span></label>
                                                    <input type="text" name="jenis_kontaminan" id="jenis_kontaminan" class="form-control @error('jenis_kontaminan') is-invalid @enderror" 
                                                           value="{{ old('jenis_kontaminan', $ketidaksesuaianBendaAsing->jenis_kontaminan) }}" placeholder="Masukkan jenis kontaminan" required>
                                                    @error('jenis_kontaminan')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="jumlah_produk_terdampak" class="font-weight-bold">Jumlah Produk Terdampak <span class="text-danger">*</span></label>
                                                    <input type="number" name="jumlah_produk_terdampak" id="jumlah_produk_terdampak" class="form-control @error('jumlah_produk_terdampak') is-invalid @enderror" 
                                                           value="{{ old('jumlah_produk_terdampak', $ketidaksesuaianBendaAsing->jumlah_produk_terdampak) }}" placeholder="Masukkan jumlah produk terdampak" min="1" required>
                                                    @error('jumlah_produk_terdampak')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="tahapan" class="font-weight-bold">Tahapan <span class="text-danger">*</span></label>
                                                    <input type="text" name="tahapan" id="tahapan" class="form-control @error('tahapan') is-invalid @enderror" 
                                                           value="{{ old('tahapan', $ketidaksesuaianBendaAsing->tahapan) }}" placeholder="Masukkan tahapan proses" required>
                                                    @error('tahapan')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="dokumentasi" class="font-weight-bold">Dokumentasi</label>
                                            @if($ketidaksesuaianBendaAsing->dokumentasi)
                                                <div class="mb-2">
                                                    <img src="{{ asset($assetPath . 'storage/' . $ketidaksesuaianBendaAsing->dokumentasi) }}" 
                                                         alt="Dokumentasi" class="img-thumbnail" style="max-width: 200px;">
                                                    <p class="text-muted small">File saat ini: {{ $ketidaksesuaianBendaAsing->dokumentasi }}</p>
                                                </div>
                                            @endif
                                            <input type="file" name="dokumentasi" id="dokumentasi" 
                                                   class="form-control-file @error('dokumentasi') is-invalid @enderror" accept="image/*" capture="camera">
                                            <small class="form-text text-muted">Format: JPG, JPEG, PNG, GIF. File akan dikompresi otomatis. Kosongkan jika tidak ingin mengubah.</small>
                                            @error('dokumentasi')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group mt-4">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Update
                                            </button>
                                            <a href="{{ route('ketidaksesuaian-benda-asing.index') }}" class="btn btn-secondary btn-md ml-2">
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