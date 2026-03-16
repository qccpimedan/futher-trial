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
                            Edit Proses Breader
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a href="{{ route('proses-breader.index') }}">
                                    <i class="fas fa-home"></i> Dashboard
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('proses-breader.index') }}">Proses Breader</a>
                            </li>
                            <li class="breadcrumb-item active">Edit Data</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        
        <section class="content">
            <div class="container-fluid">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-edit text-warning mr-2"></i>
                            Edit Proses Breader
                        </h3>
                    </div>
                    <div class="card-body">
                    <form action="{{ route('proses-breader.update', $item->uuid) }}" method="POST">
                        @csrf
                        @method('PUT') 
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_produk" class="form-label">
                                        <i class="fas fa-box text-primary"></i>
                                        Nama Produk
                                    </label>
                                    <select name="id_produk" id="id_produk" class="form-control" required>
                                        <option value="">Pilih Produk</option>
                                        @foreach($produks as $produk)
                                            <option value="{{ $produk->id }}" {{ $item->id_produk == $produk->id ? 'selected' : '' }}>
                                                {{ $produk->nama_produk }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
    <div class="form-group">
        <label for="id_jenis_breader" class="form-label">
            <i class="fas fa-layer-group text-info"></i>
            Jenis Breader
        </label>
      <select name="id_jenis_breader[]" id="id_jenis_breader_breader" multiple="multiple" class="form-control" required>
  @php
      // Get selected breader IDs from database
    $selectedBreaderIds = [];
    if($item->id_jenis_breader) {
        $selectedBreaderIds = explode(',', $item->id_jenis_breader);
        $selectedBreaderIds = array_map('trim', $selectedBreaderIds); // Remove whitespace
    }
    
    // Get only breader options from database that match selected IDs
    $availableBreaders = $jenis_breader->filter(function($breader) use ($selectedBreaderIds) {
        return in_array($breader->id, $selectedBreaderIds); // Only show selected breaders
    });
@endphp
    
    @foreach($availableBreaders as $breader)
    <option value="{{ $breader->id }}" selected>
        {{ $breader->jenis_breader }}
    </option>
@endforeach
</select>
    </div>
</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal" class="form-label">
                                        <i class="fas fa-calendar text-warning"></i>
                                        Tanggal
                                    </label>
                                    <input type="text" name="tanggal" id="tanggal" class="form-control" value="{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y H:i:s') }}" readonly>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="kode_produksi" class="form-label">
                                        <i class="fas fa-barcode text-dark"></i>
                                        Kode Produksi
                                    </label>
                                    <input type="text" name="kode_produksi" id="kode_produksi" class="form-control" value="{{ $item->kode_produksi }}" placeholder="Masukkan kode produksi" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="hasil_breader" class="form-label">
                                        <i class="fas fa-clipboard-check text-success"></i>
                                        Hasil Breader
                                    </label>
                                    <select name="hasil_breader" id="hasil_breader" class="form-control" required>
                                        <option value="✔" {{ $item->hasil_breader == '✔' ? 'selected' : '' }}>✔</option>
                                        <option value="✘" {{ $item->hasil_breader == '✘' ? 'selected' : '' }}>✘</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-warning btn-md mr-3">
                                        <i class="fas fa-save mr-2"></i>
                                        Update Data
                                    </button>
                                    <a href="{{ route('proses-breader.index') }}" class="btn btn-secondary btn-md">
                                        <i class="fas fa-arrow-left mr-2"></i>
                                        Kembali
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                    </div>
                </div>
        </section>
    </div>
</div>

<script>
$(document).ready(function() {
 $('#id_jenis_breader').select2({
 placeholder: "Pilih Jenis Breader",
 allowClear: true,
 width: '100%',
 multiple: true,
 closeOnSelect: false
 });
});
</script>

@endsection
