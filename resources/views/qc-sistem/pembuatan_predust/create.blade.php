@extends('layouts.app')

@section('title', 'Tambah Pembuatan Predust')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Tambah Pembuatan Predust</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('pembuatan-predust.index') }}">Pembuatan Predust</a></li>
                            <li class="breadcrumb-item active">Tambah</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <form action="{{ route('pembuatan-predust.store') }}" method="POST">
                    @csrf
                    @if($penggorenganData)
                        <input type="hidden" name="penggorengan_uuid" value="{{ request('penggorengan_uuid') }}">
                    @endif
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-plus mr-2"></i>Form Tambah Pembuatan Predust
                            </h3>
                        </div>
                        <div class="card-body">
                            @if($penggorenganData)
                                <div class="alert alert-info">
                                    <h5><i class="fas fa-info-circle"></i> Data Penggorengan Sebelumnya</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Produk:</strong> {{ $penggorenganData->produk->nama_produk ?? '-' }}<br>
                                            <strong>Kode Produksi:</strong> {{ $penggorenganData->kode_produksi }}<br>
                                            <strong>Tanggal:</strong> {{ $penggorenganData->tanggal ? \Carbon\Carbon::parse($penggorenganData->tanggal)->format('d/m/Y H:i') : '-' }}
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Waktu Pemasakan:</strong> {{ $penggorenganData->waktu_pemasakan ?? '-' }}<br>
                                            <strong>No Of Strokes:</strong> {{ $penggorenganData->no_of_strokes }}<br>
                                            <strong>Hasil Pencetakan:</strong> {{ $penggorenganData->hasil_pencetakan }}
                                        </div>
                                    </div>
                                </div>
                            @endif


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
                                                <option value="{{ $produk->id }}" 
                                                    {{ old('id_produk', $penggorenganData->id_produk ?? '') == $produk->id ? 'selected' : '' }}>
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
                                        @php
                                            $userRole = auth()->user()->id_role ?? null;
                                            $showTime = in_array($userRole, [1, 2, 5]); // superadmin, admin, spv
                                            $displayFormat = $showTime ? 'd-m-Y H:i:s' : 'd-m-Y';
                                            $submitFormat = 'd-m-Y H:i:s'; // Always submit with H:i:s
                                            $now = \Carbon\Carbon::now('Asia/Jakarta');
                                            $displayValue = $now->format($displayFormat);
                                            $submitValue = $now->format($submitFormat);
                                        @endphp
                                        <input type="hidden" name="tanggal" id="tanggal_hidden" 
                                                value="{{ old('tanggal', $submitValue) }}">
                                        <input type="text" class="form-control" id="tanggal_display" 
                                                value="{{ old('tanggal', $displayValue) }}" readonly required>
                                        @error('tanggal')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="jam" class="font-weight-bold">Jam <span class="text-danger">*</span></label>
                                        <input type="time" 
                                               class="form-control @error('jam') is-invalid @enderror" 
                                               id="jam" 
                                               name="jam" 
                                               value="{{ old('jam') ?? \Carbon\Carbon::now('Asia/Jakarta')->format('H:i') }}" 
                                               required>
                                        @error('jam')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="kondisi_predust">Kondisi Predust <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               name="kondisi_predust" 
                                               id="kondisi_predust" 
                                               class="form-control @error('kondisi_predust') is-invalid @enderror" 
                                               value="{{ old('kondisi_predust') }}" 
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
                                            <option value="oke" {{ old('hasil_pencetakan') == 'oke' ? 'selected' : '' }}>
                                                <i class="fas fa-check"></i> OK
                                            </option>
                                            <option value="tidak ok" {{ old('hasil_pencetakan') == 'tidak ok' ? 'selected' : '' }}>
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
                                               value="{{ old('kode_produksi') }}" 
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
                                <i class="fas fa-save"></i> Simpan Data
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