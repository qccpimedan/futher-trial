@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Tambah Data Pengemasan Produk</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pengemasan-produk.index') }}">Pengemasan Produk</a></li>
                        <li class="breadcrumb-item active">Tambah Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Form Tambah Data</h3>
                </div>
                     <!-- /.card-header -->
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

                <form action="{{ route('pengemasan-produk.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal">
                                        <i class="fas fa-calendar-alt"></i> Tanggal
                                    </label>

                                    @php
    $user = auth()->user();
    $roleId = $user->id_role ?? $user->role ?? 0; // Mencoba beberapa kemungkinan nama field
@endphp



                            @if($roleId == 2 || $roleId == 3)
    <input type="text" class="form-control" 
           id="tanggal" name="tanggal" 
           value="{{ old('tanggal', now()->format('d-m-Y')) }}" readonly>
@else
    <input type="text" class="form-control" 
           id="tanggal" name="tanggal" 
           value="{{ old('tanggal', now()->format('d-m-Y H:i:s')) }}" readonly>
@endif
                                </div>
                                <div class="form-group">
        <label for="jam">Jam</label>
        <input type="time" class="form-control" id="jam" name="jam" value="{{ old('jam', date('H:i')) }}" required>
    </div>

                                <div class="form-group">
                                    <label for="id_shift">Shift</label>
                                    <select name="id_shift" id="id_shift" class="form-control select2" required>
                                        <option value="">Pilih Shift</option>
                                        @foreach ($shifts as $shift)
                                            <option value="{{ $shift->id }}">{{ $shift->shift }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="id_produk">Produk</label>
                                    <select name="id_produk" id="id_produk" class="form-control select2" required>
                                        <option value="">Pilih Produk</option>
                                        @foreach ($produks as $produk)
                                            <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-2">
                                    <label class="form-label">Berat Produk (gr) <span class="text-danger">*</span></label>
                                    <select id="nilai_select_berat" class="form-control" name="berat_produk"></select>
                                    
                                </div>
                                <div class="form-group">
                                    <label for="tanggal_expired">Tanggal Expired</label>
                                    <input type="date" name="tanggal_expired" class="form-control" id="tanggal_expired" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="kode_produksi">Kode Produksi</label>
                                    <input type="text" name="kode_produksi" class="form-control" id="kode_produksi" placeholder="Masukkan Kode Produksi" required>
                                </div>
                                <div class="form-group">
                                    <label for="std_suhu_produk_iqf">Standar Suhu Produk IQF</label>
                                    <input type="text" name="std_suhu_produk_iqf" class="form-control" id="std_suhu_produk_iqf" value="-18°C" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="aktual_suhu_produk">Aktual Suhu Produk (°C)</label>
                                    <div id="suhu-produk-container">
                                        <div class="input-group mb-2">
                                            <input type="number" step="0.1" name="aktual_suhu_produk[]" class="form-control" placeholder="Masukkan Suhu Aktual" required>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-success" id="btn-add-suhu">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="waktu_awal_packing">Waktu Packing</label>
                                    <input type="text" name="waktu_awal_packing" class="form-control" id="waktu_awal_packing" required>
                                </div>
                                <!-- <div class="form-group">
                                    <label for="waktu_selesai_packing">Waktu Selesai Packing</label>
                                    <input type="text" name="waktu_selesai_packing" class="form-control" id="waktu_selesai_packing" required>
                                </div> -->
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                        <a href="{{ route('pengemasan-produk.index') }}" class="btn btn-secondary ml-2"><i class="fas fa-arrow-left"></i> Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#btn-add-suhu').click(function() {
            const html = `
                <div class="input-group mb-2 suhu-item">
                    <input type="number" step="0.1" name="aktual_suhu_produk[]" class="form-control" placeholder="Masukkan Suhu Aktual" required>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger btn-remove-suhu">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            $('#suhu-produk-container').append(html);
        });

        $(document).on('click', '.btn-remove-suhu', function() {
            $(this).closest('.suhu-item').remove();
        });
    });
</script>
@endpush