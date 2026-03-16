{{-- filepath: resources/views/qc-sistem/persiapan_bahan_forming/edit.blade.php --}}
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
                            Edit Persiapan Bahan Forming
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a href="{{ route('persiapan-bahan-forming.index') }}">
                                    <i class="fas fa-home"></i> Dashboard
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('persiapan-bahan-forming.index') }}">Persiapan Bahan Forming</a>
                            </li>
                            <li class="breadcrumb-item active">Edit Data</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        
        <section class="content">
            <div class="container-fluid">
                <!-- Alert Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Terjadi kesalahan:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-clipboard-list mr-2"></i>
                                    Form Edit Data
                                </h3>
                                <!-- <div class="card-tools">
                                    <a href="{{ route('persiapan-bahan-forming.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                                    </a>
                                </div> -->
                            </div>
                            
                            <form action="{{ route('persiapan-bahan-forming.update', $item->uuid) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Kolom Kiri -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="kode_produksi_emulsi" class="font-weight-bold">
                                                    <i class="fas fa-barcode text-primary mr-1"></i>
                                                    Kode Produksi Emulsi
                                                </label>
                                                <input type="text" 
                                                       id="kode_produksi_emulsi"
                                                       name="kode_produksi_emulsi" 
                                                       class="form-control @error('kode_produksi_emulsi') is-invalid @enderror" 
                                                       value="{{ old('kode_produksi_emulsi', $item->kode_produksi_emulsi) }}"
                                                       placeholder="Masukkan kode produksi emulsi">
                                                @error('kode_produksi_emulsi')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="id_suhu_adonan" class="font-weight-bold">
                                                    <i class="fas fa-thermometer-half text-danger mr-1"></i>
                                                    Suhu Adonan (STD) <span class="text-danger">*</span>
                                                </label>
                                                <select name="id_suhu_adonan" id="id_suhu_adonan" class="form-control @error('id_suhu_adonan') is-invalid @enderror" required>
                                                    <option value="">-- Pilih Suhu Adonan --</option>
                                                    @isset($suhu_adonan)
                                                        @foreach($suhu_adonan as $sa)
                                                            <option value="{{ $sa->id }}" {{ old('id_suhu_adonan', $item->id_suhu_adonan) == $sa->id ? 'selected' : '' }}>
                                                                STD: {{ $sa->std_suhu }}
                                                            </option>
                                                        @endforeach
                                                    @endisset
                                                </select>
                                                @error('id_suhu_adonan')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="waktu_mulai_mixing" class="font-weight-bold">
                                                        <i class="fas fa-play text-success mr-1"></i>
                                                        Waktu Mulai Mixing
                                                    </label>
                                                    <input type="text" 
                                                           id="waktu_mulai_mixing" 
                                                           name="waktu_mulai_mixing" 
                                                           class="form-control @error('waktu_mulai_mixing') is-invalid @enderror"
                                                           value="{{ old('waktu_mulai_mixing', $item->waktu_mulai_mixing) }}">
                                                    @error('waktu_mulai_mixing')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="waktu_selesai_mixing" class="font-weight-bold">
                                                        <i class="fas fa-stop text-danger mr-1"></i>
                                                        Waktu Selesai Mixing
                                                    </label>
                                                    <input type="text" 
                                                           id="waktu_selesai_mixing" 
                                                           name="waktu_selesai_mixing" 
                                                           class="form-control @error('waktu_selesai_mixing') is-invalid @enderror"
                                                           value="{{ old('waktu_selesai_mixing', $item->waktu_selesai_mixing) }}">
                                                    @error('waktu_selesai_mixing')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="kondisi" class="font-weight-bold">
                                                    <i class="fas fa-check-circle text-success mr-1"></i>
                                                    Kondisi <span class="text-danger">*</span>
                                                </label>
                                                <select name="kondisi" 
                                                        id="kondisi"
                                                        class="form-control @error('kondisi') is-invalid @enderror" 
                                                        required>
                                                    <option value="">-- Pilih Kondisi --</option>
                                                    <option value="✔" {{ old('kondisi', $item->kondisi) == '✔' ? 'selected' : '' }}>
                                                        ✔ Baik
                                                    </option>
                                                    <option value="✘" {{ old('kondisi', $item->kondisi) == '✘' ? 'selected' : '' }}>
                                                        ✘ Tidak Baik
                                                    </option>
                                                </select>
                                                @error('kondisi')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label>Kode Produksi Emulsi Oil</label>
                                                <div id="kode-emulsi-oil-container">
                                                    @if(is_array($item->kode_produksi_emulsi_oil) && count($item->kode_produksi_emulsi_oil) > 0)
                                                        @foreach($item->kode_produksi_emulsi_oil as $index => $kode)
                                                            <div class="input-group mb-2 kode-emulsi-oil-item">
                                                                <input type="text" name="kode_produksi_emulsi_oil[]" class="form-control" 
                                                                    value="{{ old('kode_produksi_emulsi_oil.'.$index, $kode) }}" 
                                                                    placeholder="Masukkan kode produksi emulsi oil">
                                                                <div class="input-group-append">
                                                                    <button type="button" class="btn btn-success btn-sm add-kode-emulsi-oil">
                                                                        <i class="fas fa-plus"></i>
                                                                    </button>
                                                                    <button type="button" class="btn btn-danger btn-sm remove-kode-emulsi-oil">
                                                                        <i class="fas fa-minus"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="input-group mb-2 kode-emulsi-oil-item">
                                                            <input type="text" name="kode_produksi_emulsi_oil[]" class="form-control" 
                                                                value="{{ old('kode_produksi_emulsi_oil.0', is_string($item->kode_produksi_emulsi_oil) ? $item->kode_produksi_emulsi_oil : '') }}" 
                                                                placeholder="Masukkan kode produksi emulsi oil">
                                                            <div class="input-group-append">
                                                                <button type="button" class="btn btn-success btn-sm add-kode-emulsi-oil">
                                                                    <i class="fas fa-plus"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-danger btn-sm remove-kode-emulsi-oil">
                                                                    <i class="fas fa-minus"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Kolom Kanan -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="rework" class="font-weight-bold">
                                                    <i class="fas fa-recycle text-info mr-1"></i>
                                                    Rework
                                                </label>
                                                <input type="text" 
                                                       id="rework"
                                                       name="rework" 
                                                       class="form-control @error('rework') is-invalid @enderror" 
                                                       value="{{ old('rework', $item->rework) }}"
                                                       placeholder="Masukkan rework">
                                                @error('rework')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="shift_id" class="font-weight-bold">
                                                    <i class="fas fa-clock text-primary mr-1"></i>
                                                    Shift <span class="text-danger">*</span>
                                                </label>
                                                <select name="shift_id" 
                                                        id="shift_id"
                                                        class="form-control @error('shift_id') is-invalid @enderror" 
                                                        required>
                                                    <option value="">-- Pilih Shift --</option>
                                                    @foreach($shifts as $shift)
                                                        <option value="{{ $shift->id }}" 
                                                                {{ old('shift_id', $item->shift_id) == $shift->id ? 'selected' : '' }}>
                                                             {{ $shift->shift }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('shift_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="tanggal" class="font-weight-bold">
                                                    <i class="fas fa-calendar-alt text-success mr-1"></i>
                                                    Tanggal <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" 
                                                       id="tanggal"
                                                       name="tanggal" 
                                                       class="form-control @error('tanggal') is-invalid @enderror" 
                                                     value="{{ old('tanggal', $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y H:i:s') : '') }}"
                                                       readonly>
                                                @error('tanggal')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label class="font-weight-bold">
                                                    <i class="fas fa-thermometer-three-quarters text-danger mr-1"></i>
                                                    Suhu Aktual
                                                </label>
                                                <div class="row">
                                                    @php($akt = $item->aktualSuhuAdonan)
                                                    @for($i=1;$i<=5;$i++)
                                                        @php($field = 'aktual_suhu_'.$i)
                                                        <div class="col-md-4 mb-2">
                                                        <input type="number" step="0.1" name="aktual_suhu_{{ $i }}" class="form-control" placeholder="Aktual {{ $i }}" value="{{ old('aktual_suhu_'.$i, $akt->$field ?? '') }}" id="aktual_suhu_{{ $i }}">                                                        </div>
                                                    @endfor
                                                    <div class="col-md-4 mb-2">
                                                        <label>Total/Rata-rata Suhu Aktual</label>
                                                        <input type="number" step="0.01" name="total_aktual_suhu" class="form-control" id="total_aktual_suhu" placeholder="Otomatis terisi" value="{{ old('total_aktual_suhu', $akt->total_aktual_suhu ?? '') }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="catatan" class="font-weight-bold">
                                                    <i class="fas fa-sticky-note text-warning mr-1"></i>
                                                    Catatan
                                                </label>
                                                <textarea name="catatan" 
                                                          id="catatan"
                                                          class="form-control @error('catatan') is-invalid @enderror" 
                                                          rows="3"
                                                          placeholder="Masukkan catatan tambahan...">{{ old('catatan', $item->catatan) }}</textarea>
                                                @error('catatan')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Detail Bahan Forming & Suhu -->
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h5 class="mb-3">
                                                <i class="fas fa-thermometer-half text-danger mr-2"></i>
                                                Detail Bahan Forming & Suhu
                                            </h5>
                                            
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <thead class="bg-primary text-white">
                                                        <tr>
                                                            <th width="10%" class="text-center">
                                                                <i class="fas fa-hashtag mr-1"></i>No
                                                            </th>
                                                            <th width="30%">
                                                                <i class="fas fa-flask mr-1"></i>Bahan Forming
                                                            </th>
                                                            <th width="30%" class="text-center">
                                                                <i class="fas fa-thermometer-half mr-1"></i>Kode Produksi Bahan
                                                            </th>
                                                            <th width="30%" class="text-center">
                                                                <i class="fas fa-thermometer-half mr-1"></i>Suhu RM (°C)
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($item->suhuForming as $idx => $suhu)
                                                        <tr>
                                                            <td class="text-center align-middle">
                                                                <span class="badge badge-secondary">{{ $idx + 1 }}</span>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fas fa-vial text-success mr-2"></i>
                                                                    <strong>{{ $suhu->bahanForming->nama_rm ?? '-' }}</strong>
                                                                </div>
                                                                <input type="hidden" name="id_suhu_forming[]" value="{{ $suhu->id }}">
                                                            </td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <input type="text" 
                                                                    name="kode_produksi_bahan[]" 
                                                                    class="form-control text-center @error('kode_produksi_bahan.' . $idx) is-invalid @enderror" 
                                                                    value="{{ old('kode_produksi_bahan.' . $idx, $suhu->kode_produksi_bahan) }}"
                                                                    placeholder="Kode Produksi Bahan"
                                                                    required>
                                                                    @error('kode_produksi_bahan.' . $idx)
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <input type="text" 
                                                                           name="suhu[]" 
                                                                           class="form-control text-center @error('suhu.' . $idx) is-invalid @enderror" 
                                                                           value="{{ old('suhu.' . $idx, $suhu->suhu) }}"
                                                                           placeholder="0"
                                                                           step="0.1"
                                                                           required>
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">°C</span>
                                                                    </div>
                                                                    @error('suhu.' . $idx)
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @empty
                                                        <tr>
                                                            <td colspan="3" class="text-center text-muted py-4">
                                                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                                                <p>Tidak ada data bahan forming</p>
                                                            </td>
                                                        </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="mt-4 text-center">
                                                <button type="submit" class="btn btn-warning btn-md">
                                                    <i class="fas fa-save mr-2"></i>
                                                    Update Data
                                                </button>
                                                <a href="{{ route('persiapan-bahan-forming.index') }}" class="btn btn-secondary btn-md ml-2">
                                                    <i class="fas fa-arrow-left mr-2"></i>
                                                    Kembali
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    function hitungTotalSuhuAktual() {
        let total = 0;
        let count = 0;
        
        for (let i = 1; i <= 5; i++) {  // PERBAIKAN: Ubah dari 6 ke 5
            let inputElement = document.getElementById('aktual_suhu_' + i);
            if (inputElement && inputElement.value !== '') {
                let val = parseFloat(inputElement.value);
                if (!isNaN(val)) {  // PERBAIKAN: Hapus kondisi val > 0
                    total += val;
                    count++;
                }
            }
        }
        
        let avg = count > 0 ? (total / count) : 0;
        let totalElement = document.getElementById('total_aktual_suhu');
        if (totalElement) {
            totalElement.value = avg.toFixed(2);  // PERBAIKAN: Selalu tampilkan 2 desimal
        }
    }
    
    // Event listener untuk setiap input
    for (let i = 1; i <= 5; i++) {  // PERBAIKAN: Ubah dari 6 ke 5
        let inputElement = document.getElementById('aktual_suhu_' + i);
        if (inputElement) {
            inputElement.addEventListener('input', hitungTotalSuhuAktual);
            inputElement.addEventListener('change', hitungTotalSuhuAktual);
        }
    }
    
    // Hitung saat halaman dimuat
    hitungTotalSuhuAktual();
});

document.addEventListener('DOMContentLoaded', function() {
    let kodeEmulsiOilContainer = document.getElementById('kode-emulsi-oil-container');
    
    // Event delegation untuk tombol add dan remove
    kodeEmulsiOilContainer.addEventListener('click', function(e) {
        if (e.target.closest('.add-kode-emulsi-oil')) {
            // Clone item pertama
            let firstItem = kodeEmulsiOilContainer.querySelector('.kode-emulsi-oil-item');
            let newItem = firstItem.cloneNode(true);
            
            // Reset value input
            newItem.querySelector('input').value = '';
            
            // Tambahkan ke container
            kodeEmulsiOilContainer.appendChild(newItem);
            
            // Update visibility tombol remove
            updateRemoveButtonVisibility();
        }
        
        if (e.target.closest('.remove-kode-emulsi-oil')) {
            let items = kodeEmulsiOilContainer.querySelectorAll('.kode-emulsi-oil-item');
            if (items.length > 1) {
                e.target.closest('.kode-emulsi-oil-item').remove();
                updateRemoveButtonVisibility();
            }
        }
    });
    
    function updateRemoveButtonVisibility() {
        let items = kodeEmulsiOilContainer.querySelectorAll('.kode-emulsi-oil-item');
        let removeButtons = kodeEmulsiOilContainer.querySelectorAll('.remove-kode-emulsi-oil');
        
        removeButtons.forEach(function(btn) {
            if (items.length <= 1) {
                btn.style.display = 'none';
            } else {
                btn.style.display = 'inline-block';
            }
        });
    }
    
    // Initial setup
    updateRemoveButtonVisibility();
});
</script>
@endsection
