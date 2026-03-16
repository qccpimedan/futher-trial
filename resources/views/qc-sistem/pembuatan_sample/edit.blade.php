@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Data Pembuatan Sample</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('pembuatan-sample') }}">Pembuatan Sample</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Form Edit Data</h3>
                </div>
                <form action="{{ route('pembuatan-sample.update', $pembuatanSample->uuid) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_shift">Shift</label>
                                    <select name="id_shift" id="id_shift" class="form-control select2" required>
                                        <option value="">Pilih Shift</option>
                                        @foreach ($shifts as $shift)
                                            <option value="{{ $shift->id }}" {{ $pembuatanSample->id_shift == $shift->id ? 'selected' : '' }}>{{ $shift->shift }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="id_produk">Produk</label>
                                    <select name="id_produk" id="id_produk" class="form-control select2" required>
                                        <option value="">Pilih Produk</option>
                                        @foreach ($produks as $produk)
                                            <option value="{{ $produk->id }}" {{ $pembuatanSample->id_produk == $produk->id ? 'selected' : '' }}>{{ $produk->nama_produk }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="kode_produksi">Kode Produksi</label>
                                    <input type="text" name="kode_produksi" id="kode_produksi" class="form-control" value="{{ $pembuatanSample->kode_produksi }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="tanggal_expired">Tanggal Expired</label>
                                    <input type="date" name="tanggal_expired" id="tanggal_expired" class="form-control" value="{{ $pembuatanSample->tanggal_expired }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal">Tanggal Produksi</label>
                                    <div class="input-group date" id="datetimepicker1" data-target-input="nearest">
                                        <input type="text" name="tanggal" class="form-control datetimepicker-input" data-target="#datetimepicker1" value="{{ old('tanggal', \Carbon\Carbon::parse($pembuatanSample->tanggal)->format('d-m-Y H:i:s')) }}" readonly/>
                                        <div class="input-group-append" data-target="#datetimepicker1" data-toggle="datetimepicker">
                                            <!-- <div class="input-group-text"><i class="fa fa-calendar"></i></div> -->
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="jumlah">Jumlah</label>
                                    <input type="number" name="jumlah" id="jumlah" class="form-control" value="{{ $pembuatanSample->jumlah }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="berat">Berat (gram)</label>
                                    <input type="number" step="0.01" name="berat" id="berat" class="form-control" value="{{ rtrim(rtrim(number_format($pembuatanSample->berat, 2), '0'), '.') }}" required>                                </div>
                                <div class="form-group">
                                    <label for="berat_sampling">Gramase (g)</label>
                                    <select name="berat_sampling" id="edit_nilai_select_berat_sampling_pembuatan_sample" class="form-control" data-nilai-berat data-selected="{{ old('berat_sampling', $pembuatanSample->berat_sampling) }}"></select>
                                </div>
                                <div class="form-group">
                                    <label for="jenis_sample">Jenis Sample</label>
                                    <select name="jenis_sample" id="jenis_sample" class="form-control" required>
                                        <option value="">Pilih Jenis Sample</option>
                                        <option value="sample rnd" {{ $pembuatanSample->jenis_sample == 'sample rnd' ? 'selected' : '' }}>Sample RND</option>
                                      
                                        <option value="sample trial" {{ $pembuatanSample->jenis_sample == 'sample trial' ? 'selected' : '' }}>Sample Trial</option>
                                        <option value="sample retain" {{ $pembuatanSample->jenis_sample == 'sample retain' ? 'selected' : '' }}>Sample Retain</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning"><i class="fa fa-save"></i> Update Data</button>
                        <a href="{{ route('pembuatan-sample.index') }}" class="ml-2 btn btn-secondary"><i class="fa fa-arrow-left"></i> Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        //Date and time picker
        $('#datetimepicker1').datetimepicker({ 
            format: 'DD-MM-YYYY HH:mm:ss',
            icons: { time: 'far fa-clock' }
        });
        
        // Setup hubungan kode produksi dan tanggal expired
        // Fungsi parseKodeProduksi dan setupKodeProduksi sudah tersedia di app.blade.php
        setupKodeProduksi('#kode_produksi', '#tanggal_expired');
        
        // Tambahkan tooltip/helper text
        $('#kode_produksi').attr('placeholder', 'Contoh: PA01 (P=Tahun, A=Bulan, 01=Tanggal)');
    });
</script>
@endpush