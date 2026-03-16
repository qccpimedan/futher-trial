@extends('layouts.app')

@section('container')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Edit Berat Produk - Box</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('berat-produk.index') }}">Berat Produk</a></li>
                            <li class="breadcrumb-item active">Edit Box</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Edit Berat Produk (Box)</h3>
                                <div class="card-tools">
                                    <!-- <a href="{{ route('berat-produk.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a> -->
                                </div>
                            </div>
                            <form action="{{ route('berat-produk.update_box', $beratProdukBox->uuid) }}" method="POST">
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box bg-success">
                                                <span class="info-box-icon">
                                                    <i class="fas fa-check" style="font-size: 2rem;"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Tanda Centang (✓)</span>
                                                    <span class="info-box-number">Sesuai Spesifikasi (printing jelas, terbaca; seal ok, tidak bocor dan tidak sobek)</span>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-success" style="width: 100%"></div>
                                                    </div>
                                                    <!-- <span class="progress-description">
                                                        Kondisi baik dan memenuhi standar
                                                    </span> -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box bg-danger">
                                                <span class="info-box-icon">
                                                    <i class="fas fa-times" style="font-size: 2rem;"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Tanda Silang (✗)</span>
                                                    <span class="info-box-number">Tidak Sesuai Spesifikasi</span>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-danger" style="width: 100%"></div>
                                                    </div>
                                                    <!-- <span class="progress-description">
                                                        Kondisi tidak sesuai standar
                                                    </span> -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-info mt-2">
                                        <i class="fas fa-lightbulb"></i>
                                        <strong>Petunjuk:</strong> Pilih tanda centang (✓) jika kondisi sesuai standar, dan tanda silang (✗) jika ditemukan ketidaksesuaian yang perlu diperbaiki.
                                    </div>
                                </div>
                                @method('PUT')
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="tanggal" class="col-sm-4 col-form-label">Tanggal</label>
                                                 <div class="col-sm-8">
                                                    <input type="text" name="tanggal" id="tanggal" class="form-control" value="{{ old('tanggal', \Carbon\Carbon::parse($beratProdukBox->tanggal)->format('d-m-Y H:i:s')) }}" readonly>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label for="id_produk" class="col-sm-4 col-form-label">Produk</label>
                                                <div class="col-sm-8">
                                                <input type="text"
                                                    class="form-control"
                                                    name="id_produk"
                                                    value="{{$produk->nama_produk ?? '-' }} {{$kode_produksi->berat ?? '-' }} gram"
                                                    readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="id_produk" class="col-sm-4 col-form-label">Kode Produksi</label>
                                                <div class="col-sm-8">
                                                <input type="text"
                                                    class="form-control"    
                                                    value="{{$kode_produksi->kode_produksi ?? '-' }}"
                                                    readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="id_shift" class="col-sm-4 col-form-label">Shift</label>
                                                <div class="col-sm-8">
                                                    <select name="id_shift" id="id_shift" class="form-control select2" required>
                                                        @foreach ($shifts as $shift)
                                                            <option value="{{ $shift->shift }}" {{ old('id_shift', $beratProdukBox->id_shift) == $shift->shift ? 'selected' : '' }}>{{ $shift->shift }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="id_data_box" class="col-sm-4 col-form-label">Data Box</label>
                                                <div class="col-sm-8">
                                                    <select name="id_data_box" id="id_data_box" class="form-control select2" required>
                                                        @foreach ($data_boxes as $data_box)
                                                             <option value="{{ $data_box->id }}" {{ old('id_data_box', $beratProdukBox->id_data_box) == $data_box->id ? 'selected' : '' }}>{{ $data_box->std_box }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="berat_aktual_1" class="col-sm-4 col-form-label">Berat Aktual 1</label>
                                                <div class="col-sm-8">
                                                    <input type="number" step="0.01" name="berat_aktual_1" id="berat_aktual_1" class="form-control" value="{{ old('berat_aktual_1', $beratProdukBox->berat_aktual_1) }}" required>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="berat_aktual_2" class="col-sm-4 col-form-label">Berat Aktual 2</label>
                                                <div class="col-sm-8">
                                                    <input type="number" step="0.01" name="berat_aktual_2" id="berat_aktual_2" class="form-control" value="{{ old('berat_aktual_2', $beratProdukBox->berat_aktual_2) }}" required>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="berat_aktual_3" class="col-sm-4 col-form-label">Berat Aktual 3</label>
                                                <div class="col-sm-8">
                                                    <input type="number" step="0.01" name="berat_aktual_3" id="berat_aktual_3" class="form-control" value="{{ old('berat_aktual_3', $beratProdukBox->berat_aktual_3) }}" required>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="rata_rata_berat" class="col-sm-4 col-form-label">Rata-rata Berat Aktual</label>
                                                <div class="col-sm-8">
                                                    <input type="number" step="0.01" name="rata_rata_berat" id="rata_rata_berat" class="form-control" value="{{ old('rata_rata_berat', $beratProdukBox->rata_rata_berat) }}" placeholder="Otomatis terisi" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Update Data</button>
                                    <a href="{{ route('berat-produk.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
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
        function hitungRataRataBerat() {
            let b1 = parseFloat(document.getElementById('berat_aktual_1').value) || 0;
            let b2 = parseFloat(document.getElementById('berat_aktual_2').value) || 0;
            let b3 = parseFloat(document.getElementById('berat_aktual_3').value) || 0;
            let avg = (b1 + b2 + b3) / 3;
            document.getElementById('rata_rata_berat').value = avg ? avg.toFixed(2) : '';
        }
        
        document.getElementById('berat_aktual_1').addEventListener('input', hitungRataRataBerat);
        document.getElementById('berat_aktual_2').addEventListener('input', hitungRataRataBerat);
        document.getElementById('berat_aktual_3').addEventListener('input', hitungRataRataBerat);
        
        // Hitung saat halaman dimuat
        hitungRataRataBerat();
    });
    </script>
@endsection
