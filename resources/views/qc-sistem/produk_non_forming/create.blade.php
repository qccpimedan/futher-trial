@extends('layouts.app')

@section('title', 'Tambah Data Produk Non Forming')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Tambah Data Produk Non Forming</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="">Produk Non Forming</a></li>
                            <li class="breadcrumb-item active">Tambah</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <form action="{{ route('produk-non-forming.store') }}" method="POST">
                    @csrf
                    <!-- Basic Information -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle mr-2"></i>Informasi Dasar
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="id_produk">
                                            <i class="fas fa-box mr-1"></i>Produk Non Forming
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select name="id_produk" id="id_produk" class="form-control @error('id_produk') is-invalid @enderror" required>
                                            <option value="">-- Pilih Produk --</option>
                                            @foreach($produks as $item)
                                                <option value="{{ $item->id }}" {{ old('id_produk') == $item->id ? 'selected' : '' }}>
                                                    {{ $item->nama_produk }}
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
                                        <label for="id_shift">
                                            <i class="fas fa-clock mr-1"></i>Shift
                                            <span class="text-danger">*</span>
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
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                                        
                                        @php
                                            $user = auth()->user();
                                            $roleId = $user->id_role ?? $user->role ?? 0;
                                        @endphp

                                        @if($roleId == 2 || $roleId == 3)
                                            <input type="text" name="tanggal" id="tanggal" 
                                                class="form-control @error('tanggal') is-invalid @enderror" 
                                                value="{{ old('tanggal', now()->format('d-m-Y')) }}" readonly required>
                                        @else
                                            <input type="text" name="tanggal" id="tanggal" 
                                                class="form-control @error('tanggal') is-invalid @enderror" 
                                                value="{{ old('tanggal', now()->format('d-m-Y H:i:s')) }}" readonly required>
                                        @endif
                                        @error('tanggal')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                     <div class="form-group">
                                        <label for="jam">Jam <span class="text-danger">*</span></label>
                                        <input type="time" name="jam" id="jam" 
                                            class="form-control @error('jam') is-invalid @enderror" 
                                            value="{{ old('jam', date('H:i')) }}" required>
                                        @error('jam')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
                    <!-- A. Bahan Baku -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-cubes mr-2"></i>A. Bahan Baku
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row" id="bahan-baku-container">
                                <div class="col-md-6 mb-3 bahan-baku-item">
                                    <label>Nama Bahan Baku 1</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               name="bahan_baku[0][nama]" 
                                               class="form-control" 
                                               placeholder="Nama Bahan Baku">
                                        <div class="input-group-append">
                                            <select name="bahan_baku[0][penilaian]" class="form-control">
                                                <option value="">-- Pilih --</option>
                                                @foreach(\App\Models\ProdukNonForming::getBahanOptions() as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                            <button type="button" class="btn btn-success btn-sm add-bahan-baku">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- B. Bahan Penunjang -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-tools mr-2"></i>B. Bahan Penunjang
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row" id="bahan-penunjang-container">
                                <div class="col-md-6 mb-3 bahan-penunjang-item">
                                    <label>Nama Bahan Penunjang 1</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               name="bahan_penunjang[0][nama]" 
                                               class="form-control" 
                                               placeholder="Nama Bahan Penunjang">
                                        <div class="input-group-append">
                                            <select name="bahan_penunjang[0][penilaian]" class="form-control">
                                                <option value="">-- Pilih --</option>
                                                @foreach(\App\Models\ProdukNonForming::getBahanOptions() as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                            <button type="button" class="btn btn-success btn-sm add-bahan-penunjang">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
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
                                        <label for="kemasan_plastik">Kemasan Plastik</label>
                                        <select name="kemasan_plastik" id="kemasan_plastik" class="form-control">
                                            <option value="">-- Pilih --</option>
                                            @foreach(\App\Models\ProdukNonForming::getKemasanOptions() as $key => $value)
                                                <option value="{{ $key }}" {{ old('kemasan_plastik') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="kemasan_karton">Kemasan Karton</label>
                                        <select name="kemasan_karton" id="kemasan_karton" class="form-control">
                                            <option value="">-- Pilih --</option>
                                            @foreach(\App\Models\ProdukNonForming::getKemasanOptions() as $key => $value)
                                                <option value="{{ $key }}" {{ old('kemasan_karton') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="labelisasi_plastik">Labelisasi Plastik</label>
                                        <select name="labelisasi_plastik" id="labelisasi_plastik" class="form-control">
                                            <option value="">-- Pilih --</option>
                                            @foreach(\App\Models\ProdukNonForming::getLabelisasiOptions() as $key => $value)
                                                <option value="{{ $key }}" {{ old('labelisasi_plastik') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="labelisasi_karton">Labelisasi Karton</label>
                                        <select name="labelisasi_karton" id="labelisasi_karton" class="form-control">
                                            <option value="">-- Pilih --</option>
                                            @foreach(\App\Models\ProdukNonForming::getLabelisasiOptions() as $key => $value)
                                                <option value="{{ $key }}" {{ old('labelisasi_karton') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- D. Mesin Dan Peralatan -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-cogs mr-2"></i>D. Mesin Dan Peralatan
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @php
                                    $mesinFields = [
                                        'tumbler' => 'Tumbler',
                                        'frayer' => 'Frayer',
                                        'hicook' => 'Hicook',
                                        'iqf_advance_1' => 'IQF Advance 1',
                                        'iqf_advance_2' => 'IQF Advance 2',
                                        'keranjang' => 'Keranjang',
                                        'palet' => 'Palet',
                                        'meatcar' => 'Meatcar',
                                        'timbangan' => 'Timbangan',
                                        'mhw' => 'MHW',
                                        'foot_sealer' => 'Foot Sealer',
                                        'metal_detector' => 'Metal Detector',
                                        'check_weigher_bag' => 'Check Weigher Bag',
                                        'check_weigher_box' => 'Check Weigher Box',
                                        'karton_sealer' => 'Karton Sealer'
                                    ];
                                @endphp
                                
                                @foreach($mesinFields as $field => $label)
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group">
                                            <label for="{{ $field }}">{{ $label }}</label>
                                            <select name="{{ $field }}" id="{{ $field }}" class="form-control">
                                                <option value="">-- Pilih --</option>
                                                @foreach(\App\Models\ProdukNonForming::getMesinOptions() as $key => $value)
                                                    <option value="{{ $key }}" {{ old($field) == $key ? 'selected' : '' }}>{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Tindakan Koreksi dan Verifikasi -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-clipboard-check mr-2"></i>Tindakan Koreksi & Verifikasi
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="tindakan_koreksi">Tindakan Koreksi</label>
                                        <textarea name="tindakan_koreksi" 
                                                  id="tindakan_koreksi" 
                                                  class="form-control @error('tindakan_koreksi') is-invalid @enderror" 
                                                  rows="4" 
                                                  placeholder="Masukkan tindakan koreksi jika diperlukan">{{ old('tindakan_koreksi') }}</textarea>
                                        @error('tindakan_koreksi')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="verifikasi">Verifikasi</label>
                                        <select name="verifikasi" id="verifikasi" class="form-control @error('verifikasi') is-invalid @enderror">
                                            <option value="">-- Pilih --</option>
                                            @foreach(\App\Models\ProdukNonForming::getVerifikasiOptions() as $key => $value)
                                                <option value="{{ $key }}" {{ old('verifikasi') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                            @endforeach
                                        </select>
                                        @error('verifikasi')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-1"></i>Simpan Data
                                    </button>
                                    <a href="{{ route('produk-non-forming.index') }}" class="btn btn-secondary ml-2">
                                        <i class="fas fa-arrow-left mr-1"></i>Kembali
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    let bahanBakuIndex = 1;
    let bahanPenunjangIndex = 1;

    // Add Bahan Baku
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('add-bahan-baku') || e.target.parentElement.classList.contains('add-bahan-baku')) {
            e.preventDefault();
            
            const container = document.getElementById('bahan-baku-container');
            const newItem = document.createElement('div');
            newItem.className = 'col-md-6 mb-3 bahan-baku-item';
            newItem.innerHTML = `
                <label>Nama Bahan Baku ${bahanBakuIndex + 1}</label>
                <div class="input-group">
                    <input type="text" 
                           name="bahan_baku[${bahanBakuIndex}][nama]" 
                           class="form-control" 
                           placeholder="Nama Bahan Baku">
                    <div class="input-group-append">
                        <select name="bahan_baku[${bahanBakuIndex}][penilaian]" class="form-control">
                            <option value="">-- Pilih --</option>
                            @foreach(\App\Models\ProdukNonForming::getBahanOptions() as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-danger btn-sm remove-bahan-baku">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
            `;
            
            container.appendChild(newItem);
            bahanBakuIndex++;
        }
    });

    // Remove Bahan Baku
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-bahan-baku') || e.target.parentElement.classList.contains('remove-bahan-baku')) {
            e.preventDefault();
            e.target.closest('.bahan-baku-item').remove();
        }
    });

    // Add Bahan Penunjang
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('add-bahan-penunjang') || e.target.parentElement.classList.contains('add-bahan-penunjang')) {
            e.preventDefault();
            
            const container = document.getElementById('bahan-penunjang-container');
            const newItem = document.createElement('div');
            newItem.className = 'col-md-6 mb-3 bahan-penunjang-item';
            newItem.innerHTML = `
                <label>Nama Bahan Penunjang ${bahanPenunjangIndex + 1}</label>
                <div class="input-group">
                    <input type="text" 
                           name="bahan_penunjang[${bahanPenunjangIndex}][nama]" 
                           class="form-control" 
                           placeholder="Nama Bahan Penunjang">
                    <div class="input-group-append">
                        <select name="bahan_penunjang[${bahanPenunjangIndex}][penilaian]" class="form-control">
                            <option value="">-- Pilih --</option>
                            @foreach(\App\Models\ProdukNonForming::getBahanOptions() as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-danger btn-sm remove-bahan-penunjang">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
            `;
            
            container.appendChild(newItem);
            bahanPenunjangIndex++;
        }
    });

    // Remove Bahan Penunjang
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-bahan-penunjang') || e.target.parentElement.classList.contains('remove-bahan-penunjang')) {
            e.preventDefault();
            e.target.closest('.bahan-penunjang-item').remove();
        }
    });
});
</script>
@endsection