@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Produk Forming</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="">Produk Forming</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Form Edit Produk Forming</h3>
                        </div>
                
                        <!-- /.card-header -->
                        <form action="{{ route('produk-forming.update', $produkForming->uuid) }}" method="POST">
                            @csrf
                            @method('PUT')
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
                                <!-- Card Informasi Keterangan -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="card card-info card-outline">
                                            <div class="card-header">
                                                <h3 class="card-title">
                                                    <i class="fas fa-info-circle"></i> Keterangan Pengecekan & Kriteria Penilaian
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
                                                        <h5 class="text-primary"><i class="fas fa-clipboard-check"></i> Keterangan Pengecekan:</h5>
                                                        <ul class="list-unstyled">
                                                            <li class="mb-2">
                                                                <i class="fas fa-dot-circle text-info"></i>
                                                                <strong>Pengecekan Kondisi Bahan baku, bahan penunjang, dan kemasan (1 dan 2):</strong> nomer 1-6
                                                            </li>
                                                            <li class="mb-2">
                                                                <i class="fas fa-dot-circle text-info"></i>
                                                                <strong>Pengecekan Kemasan (3 dan 4):</strong> nomer 1-2
                                                            </li>
                                                            <li class="mb-2">
                                                                <i class="fas fa-dot-circle text-info"></i>
                                                                <strong>Pengecekan Kondisi Mesin dan Peralatan:</strong> nomer 3-8
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h5 class="text-success"><i class="fas fa-star"></i> Kriteria Penilaian:</h5>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <small class="text-muted">
                                                                    <ol class="mb-0" style="font-size: 1.0em;">
                                                                        <li>Sesuai spesifikasi</li>
                                                                        <li>Tidak sesuai spesifikasi</li>
                                                                        <li>Bebas dari kontaminan dan bahan sebelumnya</li>
                                                                        <li>Ada kontaminan atau sisa bahan sebelumnya</li>
                                                                        <li>Bebas dari potensi kontaminasi allergen</li>
                                                                        <li>Ada potensi kontaminasi allergen</li>
                                                                        <li>Bersih, tidak ada kontaminan atau kotoran, tidak tercium bau menyimpang</li>
                                                                        <li>Tidak bersih, ada kontaminan atau kotoran, tercium bau menyimpang</li>
                                                                    </ol>
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="alert alert-warning mt-3">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                    <strong>Petunjuk:</strong> Gunakan nomor kriteria penilaian di atas untuk mengisi form penilaian pada setiap aspek pemeriksaan.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <!-- Tanggal -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                                            <input type="datetime-local" class="form-control @error('tanggal') is-invalid @enderror" 
                                                   id="tanggal" name="tanggal" 
                                                   value="{{ old('tanggal', \Carbon\Carbon::parse($produkForming->tanggal)->format('Y-m-d\TH:i')) }}" required>
                                            @error('tanggal')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Produk -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="id_produk">Produk <span class="text-danger">*</span></label>
                                            <select class="form-control @error('id_produk') is-invalid @enderror" 
                                                    id="id_produk" name="id_produk" required>
                                                <option value="">Pilih Produk</option>
                                                @foreach($produks as $produk)
                                                    <option value="{{ $produk->id }}" 
                                                            {{ old('id_produk', $produkForming->id_produk) == $produk->id ? 'selected' : '' }}>
                                                        {{ $produk->nama_produk }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('id_produk')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Shift -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="id_shift">Shift <span class="text-danger">*</span></label>
                                            <select class="form-control @error('id_shift') is-invalid @enderror" 
                                                    id="id_shift" name="id_shift" required>
                                                <option value="">Pilih Shift</option>
                                                @foreach($shifts as $shift)
                                                    <option value="{{ $shift->id }}" 
                                                            {{ old('id_shift', $produkForming->id_shift) == $shift->id ? 'selected' : '' }}>
                                                        {{ $shift->shift }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('id_shift')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- A. Bahan Baku -->
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-cubes mr-2"></i>A. Bahan Baku
                                        </h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-success btn-sm add-bahan-baku">
                                                <i class="fas fa-plus"></i> Tambah
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div id="bahan-baku-container">
                                            @if(old('bahan_baku') && count(old('bahan_baku')) > 0)
                                                @foreach(old('bahan_baku') as $index => $bahan)
                                                    <div class="bahan-baku-item row mb-3">
                                                        <div class="col-md-5">
                                                            <label>Nama Bahan Baku</label>
                                                            <input type="text" 
                                                                   name="bahan_baku[{{ $index }}][nama]" 
                                                                   class="form-control" 
                                                                   placeholder="Nama Bahan Baku"
                                                                   value="{{ $bahan['nama'] ?? '' }}">
                                                        </div>
                                                        <div class="col-md-5">
                                                            <label>Penilaian</label>
                                                            <select name="bahan_baku[{{ $index }}][penilaian]" class="form-control">
                                                                <option value="">-- Pilih --</option>
                                                                @foreach(App\Models\ProdukForming::getPenilaianOptions() as $key => $value)
                                                                    <option value="{{ $key }}" {{ ($bahan['penilaian'] ?? '') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2 d-flex align-items-end">
                                                            <button type="button" class="btn btn-danger btn-sm remove-bahan-baku">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @elseif($produkForming->bahan_baku && count($produkForming->bahan_baku) > 0)
                                                @foreach($produkForming->bahan_baku as $index => $bahan)
                                                    <div class="bahan-baku-item row mb-3">
                                                        <div class="col-md-5">
                                                            <label>Nama Bahan Baku</label>
                                                            <input type="text" 
                                                                   name="bahan_baku[{{ $index }}][nama]" 
                                                                   class="form-control" 
                                                                   placeholder="Nama Bahan Baku"
                                                                   value="{{ $bahan['nama'] ?? '' }}">
                                                        </div>
                                                        <div class="col-md-5">
                                                            <label>Penilaian</label>
                                                            <select name="bahan_baku[{{ $index }}][penilaian]" class="form-control">
                                                                <option value="">-- Pilih --</option>
                                                                @foreach(App\Models\ProdukForming::getPenilaianOptions() as $key => $value)
                                                                    <option value="{{ $key }}" {{ ($bahan['penilaian'] ?? '') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2 d-flex align-items-end">
                                                            <button type="button" class="btn btn-danger btn-sm remove-bahan-baku">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="bahan-baku-item row mb-3">
                                                    <div class="col-md-5">
                                                        <label>Nama Bahan Baku</label>
                                                        <input type="text" 
                                                               name="bahan_baku[0][nama]" 
                                                               class="form-control" 
                                                               placeholder="Nama Bahan Baku">
                                                    </div>
                                                    <div class="col-md-5">
                                                        <label>Penilaian</label>
                                                        <select name="bahan_baku[0][penilaian]" class="form-control">
                                                            <option value="">-- Pilih --</option>
                                                            @foreach(App\Models\ProdukForming::getPenilaianOptions() as $key => $value)
                                                                <option value="{{ $key }}">{{ $value }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2 d-flex align-items-end">
                                                        <button type="button" class="btn btn-danger btn-sm remove-bahan-baku">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- B. Bahan Penunjang -->
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-tools mr-2"></i>B. Bahan Penunjang
                                        </h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-success btn-sm add-bahan-penunjang">
                                                <i class="fas fa-plus"></i> Tambah
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div id="bahan-penunjang-container">
                                            @if(old('bahan_penunjang') && count(old('bahan_penunjang')) > 0)
                                                @foreach(old('bahan_penunjang') as $index => $bahan)
                                                    <div class="bahan-penunjang-item row mb-3">
                                                        <div class="col-md-5">
                                                            <label>Nama Bahan Penunjang</label>
                                                            <input type="text" 
                                                                   name="bahan_penunjang[{{ $index }}][nama]" 
                                                                   class="form-control" 
                                                                   placeholder="Nama Bahan Penunjang"
                                                                   value="{{ $bahan['nama'] ?? '' }}">
                                                        </div>
                                                        <div class="col-md-5">
                                                            <label>Penilaian</label>
                                                            <select name="bahan_penunjang[{{ $index }}][penilaian]" class="form-control">
                                                                <option value="">-- Pilih --</option>
                                                                @foreach(App\Models\ProdukForming::getPenilaianOptions() as $key => $value)
                                                                    <option value="{{ $key }}" {{ ($bahan['penilaian'] ?? '') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2 d-flex align-items-end">
                                                            <button type="button" class="btn btn-danger btn-sm remove-bahan-penunjang">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @elseif($produkForming->bahan_penunjang && count($produkForming->bahan_penunjang) > 0)
                                                @foreach($produkForming->bahan_penunjang as $index => $bahan)
                                                    <div class="bahan-penunjang-item row mb-3">
                                                        <div class="col-md-5">
                                                            <label>Nama Bahan Penunjang</label>
                                                            <input type="text" 
                                                                   name="bahan_penunjang[{{ $index }}][nama]" 
                                                                   class="form-control" 
                                                                   placeholder="Nama Bahan Penunjang"
                                                                   value="{{ $bahan['nama'] ?? '' }}">
                                                        </div>
                                                        <div class="col-md-5">
                                                            <label>Penilaian</label>
                                                            <select name="bahan_penunjang[{{ $index }}][penilaian]" class="form-control">
                                                                <option value="">-- Pilih --</option>
                                                                @foreach(App\Models\ProdukForming::getPenilaianOptions() as $key => $value)
                                                                    <option value="{{ $key }}" {{ ($bahan['penilaian'] ?? '') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2 d-flex align-items-end">
                                                            <button type="button" class="btn btn-danger btn-sm remove-bahan-penunjang">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="bahan-penunjang-item row mb-3">
                                                    <div class="col-md-5">
                                                        <label>Nama Bahan Penunjang</label>
                                                        <input type="text" 
                                                               name="bahan_penunjang[0][nama]" 
                                                               class="form-control" 
                                                               placeholder="Nama Bahan Penunjang">
                                                    </div>
                                                    <div class="col-md-5">
                                                        <label>Penilaian</label>
                                                        <select name="bahan_penunjang[0][penilaian]" class="form-control">
                                                            <option value="">-- Pilih --</option>
                                                            @foreach(App\Models\ProdukForming::getPenilaianOptions() as $key => $value)
                                                                <option value="{{ $key }}">{{ $value }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2 d-flex align-items-end">
                                                        <button type="button" class="btn btn-danger btn-sm remove-bahan-penunjang">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- C. Kemasan -->
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-box mr-2"></i>C. Kemasan
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="kemasan_plastik">Kemasan Plastik (1-2)</label>
                                                    <select class="form-control" id="kemasan_plastik" name="kemasan_plastik">
                                                        <option value="">-- Pilih Penilaian --</option>
                                                        @foreach(App\Models\ProdukForming::getKemasanOptions() as $key => $value)
                                                            <option value="{{ $key }}" {{ old('kemasan_plastik', $produkForming->kemasan_plastik) == $key ? 'selected' : '' }}>
                                                                {{ $value }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="kemasan_karton">Kemasan Karton (1-2)</label>
                                                    <select class="form-control" id="kemasan_karton" name="kemasan_karton">
                                                        <option value="">-- Pilih Penilaian --</option>
                                                        @foreach(App\Models\ProdukForming::getKemasanOptions() as $key => $value)
                                                            <option value="{{ $key }}" {{ old('kemasan_karton', $produkForming->kemasan_karton) == $key ? 'selected' : '' }}>
                                                                {{ $value }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- D. Labelisasi -->
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-tags mr-2"></i>D. Labelisasi
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="labelisasi_plastik">Labelisasi Plastik (1-2)</label>
                                                    <select class="form-control" id="labelisasi_plastik" name="labelisasi_plastik">
                                                        <option value="">-- Pilih Penilaian --</option>
                                                        @foreach(App\Models\ProdukForming::getLabelisasiOptions() as $key => $value)
                                                            <option value="{{ $key }}" {{ old('labelisasi_plastik', $produkForming->labelisasi_plastik) == $key ? 'selected' : '' }}>
                                                                {{ $value }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="labelisasi_karton">Labelisasi Karton (1-2)</label>
                                                    <select class="form-control" id="labelisasi_karton" name="labelisasi_karton">
                                                        <option value="">-- Pilih Penilaian --</option>
                                                        @foreach(App\Models\ProdukForming::getLabelisasiOptions() as $key => $value)
                                                            <option value="{{ $key }}" {{ old('labelisasi_karton', $produkForming->labelisasi_karton) == $key ? 'selected' : '' }}>
                                                                {{ $value }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- E. Mesin dan Peralatan -->
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-cogs mr-2"></i>E. Mesin dan Peralatan
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @php
                                                $mesinFields = [
                                                    'autogrind' => 'Autogrind',
                                                    'bowlcutter' => 'Bowlcutter',
                                                    'ayakan_seasoning' => 'Ayakan Seasoning',
                                                    'unimix' => 'Unimix',
                                                    'revoformer' => 'Revoformer',
                                                    'better_mixer' => 'Better Mixer',
                                                    'wet_coater' => 'Wet Coater',
                                                    'breader' => 'Breader',
                                                    'frayer_1' => 'Frayer 1',
                                                    'frayer_2' => 'Frayer 2',
                                                    'iqf_jbt' => 'IQF JBT',
                                                    'keranjang' => 'Keranjang',
                                                    'timbangan' => 'Timbangan',
                                                    'mhw' => 'MHW',
                                                    'foot_sealer' => 'Foot Sealer',
                                                    'metal_detector' => 'Metal Detector',
                                                    'rotary_table' => 'Rotary Table',
                                                    'carton_sealer' => 'Carton Sealer',
                                                    'meatcar' => 'Meatcar',
                                                    'check_weigher_bag' => 'Check Weigher Bag',
                                                    'check_weigher_box' => 'Check Weigher Box'
                                                ];
                                            @endphp
                                            
                                            @foreach($mesinFields as $field => $label)
                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group">
                                                        <label for="{{ $field }}">{{ $label }} (3-8)</label>
                                                        <select name="{{ $field }}" id="{{ $field }}" class="form-control">
                                                            <option value="">-- Pilih Penilaian --</option>
                                                            @foreach(App\Models\ProdukForming::getMesinOptions() as $key => $value)
                                                                <option value="{{ $key }}" {{ old($field, $produkForming->$field) == $key ? 'selected' : '' }}>{{ $value }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <!-- Tindakan Koreksi -->
                                <div class="form-group mt-3">
                                    <label for="tindakan_koreksi">Tindakan Koreksi</label>
                                    <textarea class="form-control @error('tindakan_koreksi') is-invalid @enderror" 
                                              id="tindakan_koreksi" name="tindakan_koreksi" rows="3" 
                                              placeholder="Masukkan tindakan koreksi jika diperlukan">{{ old('tindakan_koreksi', $produkForming->tindakan_koreksi) }}</textarea>
                                    @error('tindakan_koreksi')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <!-- Verifikasi -->
                                <div class="form-group">
                                    <label for="verifikasi">Verifikasi <span class="text-danger">*</span></label>
                                    <select class="form-control @error('verifikasi') is-invalid @enderror" id="verifikasi" name="verifikasi" required>
                                        <option value="">Pilih Verifikasi</option>
                                        @foreach($produkForming->getVerifikasiOptions() as $value => $label)
                                            <option value="{{ $value }}" {{ old('verifikasi', $produkForming->verifikasi) == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('verifikasi')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Update Data
                                </button>
                                <a href="{{ route('produk-forming.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let bahanBakuIndex = {{ count(old('bahan_baku', $produkForming->bahan_baku ?? [])) }};
    let bahanPenunjangIndex = {{ count(old('bahan_penunjang', $produkForming->bahan_penunjang ?? [])) }};

    // Add Bahan Baku
    $(document).on('click', '.add-bahan-baku', function() {
        const container = document.getElementById('bahan-baku-container');
        const newItem = document.createElement('div');
        newItem.className = 'bahan-baku-item row mb-3';
        newItem.innerHTML = `
            <div class="col-md-5">
                <label>Nama Bahan Baku</label>
                <input type="text" name="bahan_baku[${bahanBakuIndex}][nama]" class="form-control" placeholder="Nama Bahan Baku">
            </div>
            <div class="col-md-5">
                <label>Penilaian</label>
                <select name="bahan_baku[${bahanBakuIndex}][penilaian]" class="form-control">
                    <option value="">-- Pilih --</option>
                    @foreach(App\Models\ProdukForming::getPenilaianOptions() as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-danger btn-sm remove-bahan-baku">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(newItem);
        bahanBakuIndex++;
    });

    // Add Bahan Penunjang
    $(document).on('click', '.add-bahan-penunjang', function() {
        const container = document.getElementById('bahan-penunjang-container');
        const newItem = document.createElement('div');
        newItem.className = 'bahan-penunjang-item row mb-3';
        newItem.innerHTML = `
            <div class="col-md-5">
                <label>Nama Bahan Penunjang</label>
                <input type="text" name="bahan_penunjang[${bahanPenunjangIndex}][nama]" class="form-control" placeholder="Nama Bahan Penunjang">
            </div>
            <div class="col-md-5">
                <label>Penilaian</label>
                <select name="bahan_penunjang[${bahanPenunjangIndex}][penilaian]" class="form-control">
                    <option value="">-- Pilih --</option>
                    @foreach(App\Models\ProdukForming::getPenilaianOptions() as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-danger btn-sm remove-bahan-penunjang">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(newItem);
        bahanPenunjangIndex++;
    });

    // Remove items with validation
    $(document).on('click', '.remove-bahan-baku, .remove-bahan-penunjang', function() {
        const isBahanBaku = $(this).hasClass('remove-bahan-baku');
        const itemClass = isBahanBaku ? '.bahan-baku-item' : '.bahan-penunjang-item';
        const itemName = isBahanBaku ? 'bahan baku' : 'bahan penunjang';
        
        if ($(itemClass).length > 1) {
            $(this).closest(itemClass).remove();
        } else {
            alert(`Minimal harus ada satu ${itemName}`);
        }
    });
});
</script>
@endsection