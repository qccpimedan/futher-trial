{{-- filepath: d:\laragon\www\paperless_futher\resources\views\qc-sistem\bahan_baku_tumbling\edit.blade.php --}}
@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h1 class="m-0">Edit Bahan Baku Tumbling</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('bahan-baku-tumbling.index') }}">Bahan Baku Tumbling</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

  <section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-12 col-md-10">
                <div class="card card-primary">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-edit mr-2"></i>Edit Data Bahan Baku Tumbling</h5>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger rounded">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('bahan-baku-tumbling.update', $bahanBakuTumbling->uuid) }}" method="POST" autocomplete="off" class="form-horizontal">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                {{-- Stepper Indikator Proses --}}
                                @include('components.stepper-tumbling', [
                                    'step' => 1,
                                    'bahanBakuUuid' => $bahanBakuTumbling->uuid,
                                    'prosesTumblingUuid' => $bahanBakuTumbling->prosesTumblings->first()->uuid ?? null
                                ])

                                {{-- Informasi Dasar --}}
                                <div class="card card-outline card-warning mb-3">
                                    <div class="card-header py-2">
                                        <h6 class="mb-0">Informasi Dasar</h6>
                                    </div>
                                    <div class="card-body pt-3 pb-1">
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                                    </div>
                                                    <input type="text" name="tanggal" id="tanggal"
                                                        class="form-control @error('tanggal') is-invalid @enderror"
                                                        value="{{ old('tanggal', $bahanBakuTumbling->tanggal ? $bahanBakuTumbling->tanggal->format('d-m-Y H:i:s') : '') }}" readonly>
                                                    @error('tanggal')
                                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="shift_id">Shift <span class="text-danger">*</span></label>
                                                <select name="shift_id" id="shift_id" class="form-control select2 @error('shift_id') is-invalid @enderror" required>
                                                    <option value="">Pilih Shift</option>
                                                    @foreach($shifts as $shift)
                                                        <option value="{{ $shift->id }}" {{ old('shift_id', $bahanBakuTumbling->shift_id) == $shift->id ? 'selected' : '' }}>
                                                            {{ $shift->shift }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('shift_id')
                                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Detail Bahan Baku --}}
                                <div class="card card-outline card-warning">
                                    <div class="card-header py-2">
                                        <h6 class="mb-0">Detail Bahan Baku</h6>
                                    </div>
                                    <div class="card-body pt-3">
                                        <div class="form-row mb-3">
                                            <div class="form-group col-md-6">
                                                <label for="id_produk">Nama Produk <span class="text-danger">*</span></label>
                                                <select name="id_produk" id="id_produk" class="form-control @error('id_produk') is-invalid @enderror" required>
                                                    <option value="">Pilih Produk</option>
                                                    @foreach($produks as $produk)
                                                        <option value="{{ $produk->id }}" {{ old('id_produk', $bahanBakuTumbling->id_produk) == $produk->id ? 'selected' : '' }}>
                                                            {{ $produk->nama_produk }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('id_produk')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="kode_produksi">Kode Produksi <span class="text-danger">*</span></label>
                                                <input type="text" name="kode_produksi" id="kode_produksi"
                                                    class="form-control @error('kode_produksi') is-invalid @enderror"
                                                    value="{{ old('kode_produksi', $bahanBakuTumbling->kode_produksi) }}" placeholder="Masukkan kode produksi" required>
                                                @error('kode_produksi')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Tabel Data Bahan Baku --}}
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th>Nama Bahan Baku</th>
                                                        <th>Jumlah (kg)</th>
                                                        <th>Kode Produksi Bahan Baku</th>
                                                        <th>Suhu (°C)</th>
                                                        <th>Kondisi Daging</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if($bahanBakuTumbling->manual_bahan_data && is_array($bahanBakuTumbling->manual_bahan_data))
                                                        @forelse($bahanBakuTumbling->manual_bahan_data as $index => $bahan)
                                                            <tr>
                                                                 <td>
                                                                    <input type="text" 
                                                                        class="form-control form-control-sm" 
                                                                        value="{{ $bahan['nama_bahan_baku'] ?? '' }}" disabled>
                                                                    <!-- Hidden input untuk submit -->
                                                                    <input type="hidden" 
                                                                        name="manual_bahan[{{ $index }}][nama_bahan_baku]"
                                                                        value="{{ $bahan['nama_bahan_baku'] ?? '' }}">
                                                                </td>
                                                                 <td>
                                                                    <input type="text" 
                                                                        class="form-control form-control-sm" 
                                                                        name="manual_bahan[{{ $index }}][jumlah]"
                                                                        value="{{ $bahan['jumlah'] ?? '' }}" required>
                                                                </td>
                                                                <td>
                                                                    <input type="text" 
                                                                        class="form-control form-control-sm" 
                                                                        name="manual_bahan[{{ $index }}][kode_produksi_bahan_baku]"
                                                                        value="{{ $bahan['kode_produksi_bahan_baku'] ?? '' }}" required>
                                                                </td>
                                                                <td>
                                                                    <input type="text" 
                                                                        class="form-control form-control-sm" 
                                                                        name="manual_bahan[{{ $index }}][suhu]"
                                                                        value="{{ $bahan['suhu'] ?? '' }}" required>
                                                                </td>
                                                                <td>
                                                                    <select class="form-control form-control-sm" 
                                                                        name="manual_bahan[{{ $index }}][kondisi_daging]" required>
                                                                        <option value="✓" {{ ($bahan['kondisi_daging'] ?? '') == '✓' ? 'selected' : '' }}>✓</option>
                                                                        <option value="✘" {{ ($bahan['kondisi_daging'] ?? '') == '✘' ? 'selected' : '' }}>✘</option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="5" class="text-center text-muted">Tidak ada data bahan baku</td>
                                                            </tr>
                                                        @endforelse
                                                    @else
                                                        <tr>
                                                            <td colspan="5" class="text-center text-muted">Tidak ada data bahan baku</td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="form-row mt-3">
                                            <div class="form-group col-md-4">
                                                <label for="salinity">Salinity <span class="text-danger">*</span></label>
                                                <input type="text" name="salinity" id="salinity"
                                                    class="form-control @error('salinity') is-invalid @enderror"
                                                    value="{{ old('salinity', $bahanBakuTumbling->salinity) }}" placeholder="Masukkan salinity" required>
                                                @error('salinity')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="hasil_pencampuran">Hasil Pencampuran <span class="text-danger">*</span></label>
                                                <select class="form-control @error('hasil_pencampuran') is-invalid @enderror" name="hasil_pencampuran" id="hasil_pencampuran" required>
                                                    <option value="">Pilih Hasil Pencampuran</option>
                                                    <option value="✓" {{ old('hasil_pencampuran', $bahanBakuTumbling->hasil_pencampuran) == '✓' ? 'selected' : '' }}>✓</option>
                                                    <option value="✘" {{ old('hasil_pencampuran', $bahanBakuTumbling->hasil_pencampuran) == '✘' ? 'selected' : '' }}>✘</option>
                                                </select>
                                                @error('hasil_pencampuran')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Update Data
                                </button>
                                <a href="{{ route('bahan-baku-tumbling.index') }}" class="btn btn-secondary ml-2">
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