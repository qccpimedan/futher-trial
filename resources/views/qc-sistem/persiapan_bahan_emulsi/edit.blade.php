{{-- filepath: resources/views/qc-sistem/persiapan_bahan_emulsi/edit.blade.php --}}
@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-edit text-primary"></i>
                        Edit Data Pembuatan Emulsi
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('persiapan-bahan-emulsi.index') }}">Persiapan Bahan Emulsi</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <form action="{{ route('persiapan-bahan-emulsi.update', $item->uuid) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Card for Basic Information -->
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i>
                            Informasi Dasar
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="nama_produk">
                                        <i class="fas fa-box text-info"></i>
                                        Nama Produk
                                    </label>
                                    <input type="text" id="nama_produk" 
                                        class="form-control" 
                                        value="{{ $item->produk->nama_produk ?? '-' }}" 
                                        readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="kode_produksi_emulsi">
                                        <i class="fas fa-barcode text-warning"></i>
                                        Kode Produksi Emulsi <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="kode_produksi_emulsi" id="kode_produksi_emulsi" 
                                           class="form-control @error('kode_produksi_emulsi') is-invalid @enderror" 
                                           value="{{ old('kode_produksi_emulsi', $item->kode_produksi_emulsi) }}" 
                                           placeholder="Masukkan kode produksi emulsi" required>
                                    @error('kode_produksi_emulsi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="shift_id">
                                        <i class="fas fa-clock text-primary"></i>
                                        Shift <span class="text-danger">*</span>
                                    </label>
                                    <select name="shift_id" id="shift_id" class="form-control @error('shift_id') is-invalid @enderror" required>
                                        <option value="">Pilih Shift</option>
                                        @foreach($shifts as $shift)
                                            <option value="{{ $shift->id }}" {{ $item->shift_id == $shift->id ? 'selected' : '' }}>
                                                {{ $shift->shift }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('shift_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tanggal">
                                        <i class="fas fa-calendar text-danger"></i>
                                        Tanggal <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="tanggal" id="tanggal" 
                                           class="form-control @error('tanggal') is-invalid @enderror" 
                                           value="{{ old('tanggal', $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y H:i:s') : '') }}" readonly>
                                    @error('tanggal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card for Bahan Emulsi & Suhu -->
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-thermometer-half"></i>
                            Bahan Emulsi & Suhu
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($item->suhuEmulsi && $item->suhuEmulsi->count() > 0)
                            @php
                                // Group data by proses_ke
                                $groupedData = $item->suhuEmulsi->groupBy('proses_ke');
                                $kondisiArray = json_decode($item->kondisi, true) ?? [];
                                $hasilArray = json_decode($item->hasil_emulsi, true) ?? [];
                            @endphp
                            
                            @foreach($groupedData as $prosesKe => $items)
                                <div class="mt-4">
                                    <h5 class="text-primary">Proses Emulsi ke-{{ $prosesKe }}</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-hover">
                                            <thead class="bg-gradient-success">
                                                <tr class="text-white">
                                                    <th class="text-center" width="5%">No</th>
                                                    <th class="text-center" width="30%">
                                                        <i class="fas fa-flask"></i> Nama RM
                                                    </th>
                                                    <th class="text-center" width="15%">
                                                        <i class="fas fa-weight"></i> Berat (gram)
                                                    </th>
                                                    <th class="text-center" width="25%">
                                                        <i class="fas fa-barcode"></i> Kode Produksi Bahan
                                                    </th>
                                                    <th class="text-center" width="25%">
                                                        <i class="fas fa-thermometer-half"></i> Kondisi
                                                    </th>
                                                </tr>
                                            </thead>
                                            
                                            <tbody>
                                            @foreach($items as $index => $suhu)
                                            <tr>
                                                <td class="text-center align-middle">
                                                    {{ $index + 1 }}
                                                </td>
                                                <td class="align-middle">
                                                    <strong>{{ $suhu->bahanEmulsi->nama_rm ?? '-' }}</strong>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <div class="input-group input-group-sm">
                                                        <select class="form-control berat-source-select-edit" data-index="{{ $index }}" data-proses="{{ $prosesKe }}" data-db-value="{{ $suhu->bahanEmulsi->berat_rm ?? '' }}">
                                                            <option value="db">Dari DB ({{ $suhu->bahanEmulsi->berat_rm ?? '-' }})</option>
                                                            <option value="manual" {{ $suhu->berat_bahan ? 'selected' : '' }}>Manual Input</option>
                                                        </select>
                                                        <input type="number" name="berat_rm_edit[{{ $prosesKe - 1 }}][]" class="form-control berat-input-edit" 
                                                            value="{{ $suhu->berat_bahan ?? $suhu->bahanEmulsi->berat_rm ?? '' }}" 
                                                            step="0.1" placeholder="Berat" 
                                                            {{ !$suhu->berat_bahan ? 'disabled' : '' }}>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="text" name="kode_produksi_bahan[]" class="form-control" 
                                                        value="{{ $suhu->kode_produksi_bahan ?? '' }}" 
                                                        placeholder="Masukkan Kode Produksi">
                                                </td>
                                                <td>
                                                    <input type="hidden" name="suhu_emulsi_id[]" value="{{ $suhu->id }}">
                                                    <select name="suhu[]" class="form-control" required>
                                                        <option value="">Pilih Status</option>
                                                        <option value="✔" {{ $suhu->suhu == '✔' ? 'selected' : '' }}>✔ OK</option>
                                                        <option value="✘" {{ $suhu->suhu == '✘' ? 'selected' : '' }}>✘ Tidak OK</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            @endforeach
                                            
                                            <!-- TAMBAHAN: Row untuk Suhu per Proses -->
                                            <tr class="bg-light">
                                                <td colspan="5">
                                                    <div class="row align-items-center">
                                                        <label class="col-sm-2 font-weight-bold mb-0"><i class="fas fa-thermometer-half mr-1"></i>Suhu</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="kondisi_proses[{{ $prosesKe - 1 }}]" class="form-control" 
                                                                   value="{{ $kondisiArray[$prosesKe - 1] ?? '' }}" 
                                                                   placeholder="Masukkan Suhu" required>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- TAMBAHAN: Row untuk Hasil Emulsi per Proses -->
                                            <tr class="bg-light">
                                                <td colspan="5">
                                                    <div class="row align-items-center">
                                                        <label class="col-sm-2 font-weight-bold mb-0"><i class="fas fa-check-circle mr-1"></i>Hasil Emulsi</label>
                                                        <div class="col-sm-10">
                                                            <select name="hasil_emulsi_proses[{{ $prosesKe - 1 }}]" class="form-control" required>
                                                                <option value="">Pilih Hasil</option>
                                                                <option value="✔" {{ ($hasilArray[$prosesKe - 1] ?? '') == '✔' ? 'selected' : '' }}>✔ OK</option>
                                                                <option value="✘" {{ ($hasilArray[$prosesKe - 1] ?? '') == '✘' ? 'selected' : '' }}>✘ Tidak OK</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                Tidak ada data detail bahan emulsi yang tersedia.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-warning btn-md mr-3">
                                    <i class="fas fa-save"></i>
                                    Update Data
                                </button>
                                <a href="{{ route('persiapan-bahan-emulsi.index') }}" class="btn btn-secondary btn-md">
                                    <i class="fas fa-arrow-left"></i>
                                    Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
            </form>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    // Event handler untuk toggle berat source di halaman edit
    $(document).on('change', '.berat-source-select-edit', function() {
        const index = $(this).data('index');
        const proses = $(this).data('proses');
        const dbValue = $(this).data('db-value');
        const sourceValue = $(this).val();
        const inputField = $(this).closest('td').find('.berat-input-edit');
        
        if (sourceValue === 'db') {
            inputField.val(dbValue).prop('disabled', true);
        } else {
            inputField.val('').prop('disabled', false).focus();
        }
    });
});
</script>
@endsection