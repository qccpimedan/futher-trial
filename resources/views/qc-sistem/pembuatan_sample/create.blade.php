@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Tambah Data Pembuatan Sample</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('pembuatan-sample') }}">Pembuatan Sample</a></li>
                        <li class="breadcrumb-item active">Tambah</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Form Tambah Data</h3>
                </div>
                <form action="{{ route('pembuatan-sample.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_shift">Shift</label>
                                    <select name="id_shift" id="id_shift" class="form-control" required>
                                        <option value="">Pilih Shift</option>
                                        @foreach ($shifts as $shift)
                                            <option value="{{ $shift->id }}" {{ old('id_shift') == $shift->id ? 'selected' : '' }}>{{ $shift->shift }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="id_produk">Produk</label>
                                    <select name="id_produk" id="id_produk_pembuatan_sampel" class="form-control" required>
                                        <option value="">Pilih Produk</option>
                                        @foreach ($produks as $produk)
                                            <option value="{{ $produk->id }}" {{ old('id_produk') == $produk->id ? 'selected' : '' }}>{{ $produk->nama_produk }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="berat">Berat Produk (gram)</label>
                                    <!-- <input type="number" step="0.01" name="berat" id="berat" class="form-control" value="{{ old('berat') }}" required> -->
                                    <select name="berat" id="nilai_select_berat" class="form-control" data-nilai-berat data-selected="{{ old('berat') }}"></select>
                                </div>
                                <div class="form-group">
                                    <label for="kode_produksi">Kode Produksi</label>
                                    <input type="text" name="kode_produksi" id="kode_produksi" class="form-control" value="{{ old('kode_produksi') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="tanggal_expired">Tanggal Expired</label>
                                    <input type="date" name="tanggal_expired" id="tanggal_expired" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal">
                                        <i class="fas fa-calendar-alt"></i> Tanggal
                                    </label>
                                    
                                    @php
                                        $user = auth()->user();
                                        $roleId = $user->id_role ?? $user->role ?? 0;
                                    @endphp

                                    <div class="input-group date" id="datetimepicker1" data-target-input="nearest">
                                        @if($roleId == 2 || $roleId == 3)
                                            <input type="text" name="tanggal" class="form-control datetimepicker-input" 
                                                data-target="#datetimepicker1" value="{{ old('tanggal', date('d-m-Y')) }}" readonly/>
                                        @else
                                            <input type="text" name="tanggal" class="form-control datetimepicker-input" 
                                                data-target="#datetimepicker1" value="{{ old('tanggal', date('d-m-Y H:i:s')) }}" readonly/>
                                        @endif
                                        <div class="input-group-append" data-target="#datetimepicker1" data-toggle="datetimepicker">
                                            <!-- <div class="input-group-text"><i class="fa fa-calendar"></i></div> -->
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="jam">
                                        <i class="fas fa-clock"></i> Jam
                                    </label>
                                    <input type="time" name="jam" id="jam" class="form-control" 
                                        value="{{ old('jam', date('H:i')) }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="jumlah">Jumlah</label>
                                    <input type="number" name="jumlah" id="jumlah" class="form-control" value="{{ old('jumlah') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="berat">Gramase (gram)</label>
                                    <!-- <input type="number" step="0.01" name="berat" id="berat" class="form-control" value="{{ old('berat') }}" required> -->
                                    <select name="berat_sampling" id="nilai_select_berat_sampling" class="form-control" data-nilai-berat data-selected="{{ old('berat_sampling') }}"></select>
                                </div>
                                <div class="form-group">
                                    <label for="jenis_sample">Jenis Sample</label>
                                    <select name="jenis_sample" id="jenis_sample" class="form-control" required>
                                        <option value="">Pilih Jenis Sample</option>
                                        <option value="sample rnd" {{ old('jenis_sample') == 'sample rnd' ? 'selected' : '' }}>Sample RND</option>
                                       
                                        <option value="sample trial" {{ old('jenis_sample') == 'sample trial' ? 'selected' : '' }}>Sample Trial</option>
                                        <option value="sample retain" {{ old('jenis_sample') == 'sample retain' ? 'selected' : '' }}>Sample Retain</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Data</button>
                        <a href="{{ route('pembuatan-sample.index') }}" class="btn btn-secondary ml-2"><i class="fas fa-arrow-left"></i> Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Setup hubungan kode produksi dan tanggal expired
    // Fungsi parseKodeProduksi dan setupKodeProduksi sudah tersedia di app.blade.php
    setupKodeProduksi('#kode_produksi', '#tanggal_expired');
    
    // Tambahkan tooltip/helper text
    $('#kode_produksi').attr('placeholder', 'Contoh: PA01 (P=Tahun, A=Bulan, 01=Tanggal)');
    $('#kode_produksi').after('<small class="form-text text-muted">Format: [Huruf Tahun][Huruf Bulan][2 Digit Tanggal]. Contoh: PA01 = 01 Januari 2026</small>');
});
</script>
@endpush

@endsection