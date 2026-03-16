@extends('layouts.app')

@section('title', 'Edit Data Timbangan')

@section('container')
<!-- Content Header (Page header) -->
 <div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Edit Data Timbangan</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Home</a></li>
                            <!-- <li class="breadcrumb-item"><a href="#">QC Sistem</a></li> -->
                            <li class="breadcrumb-item"><a href="{{ route('timbangan.index') }}">Data Timbangan</a></li>
                            <li class="breadcrumb-item active">Edit Data</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-balance-scale mr-1"></i>
                                Form Edit Data Timbangan
                            </h3>
                        </div>
                        <!-- /.card-header -->
                        
                        <form action="{{ route('timbangan.update', $data->uuid) }}" method="POST">
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

                                <div class="row">
                                    <!-- Kolom Kiri -->
                                    <div class="col-md-6">
                                        <div class="card card-outline card-info">
                                            <div class="card-header">
                                                <h3 class="card-title">
                                                    <i class="fas fa-info-circle"></i>
                                                    Informasi Dasar
                                                </h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label for="shift_id">
                                                        <i class="fas fa-clock"></i>
                                                        Shift <span class="text-danger">*</span>
                                                    </label>
                                                    <select name="shift_id" id="shift_id" class="form-control @error('shift_id') is-invalid @enderror" required>
                                                        <option value="">-- Pilih Shift --</option>
                                                        @foreach($shifts as $shift)
                                                            <option value="{{ $shift->id }}" {{ (old('shift_id', $data->shift_id) == $shift->id) ? 'selected' : '' }}>
                                                                {{ $shift->shift }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('shift_id')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <div class="form-group">
                                                    <label for="tanggal">
                                                        <i class="fas fa-calendar-alt"></i>
                                                        Tanggal & Waktu <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="datetime-local" 
                                                        name="tanggal" 
                                                        id="tanggal" 
                                                        class="form-control @error('tanggal') is-invalid @enderror" 
                                                        value="{{ old('tanggal', \Carbon\Carbon::parse($data->tanggal)->format('Y-m-d\TH:i')) }}" 
                                                        required>
                                                    @error('tanggal')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <div class="form-group">
                                                    <label for="jam">
                                                        <i class="fas fa-clock"></i>
                                                        Jam <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="time"
                                                        name="jam"
                                                        id="jam"
                                                        class="form-control @error('jam') is-invalid @enderror"
                                                        value="{{ old('jam', $data->jam ? \Carbon\Carbon::parse($data->jam)->format('H:i') : '') }}"
                                                        required>
                                                    @error('jam')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <div class="form-group">
                                                    <label for="data_timbangan_id">
                                                        <i class="fas fa-balance-scale"></i>
                                                        Jenis dan Kode Timbangan <span class="text-danger">*</span>
                                                    </label>
                                                    <select name="data_timbangan_id" 
                                                            id="id_produk_select" 
                                                            class="form-control select-timbangan-edit @error('data_timbangan_id') is-invalid @enderror" 
                                                            required>
                                                        <option value="">-- Pilih Timbangan --</option>
                                                        @foreach($dataTimbangan as $timbangan)
                                                            <option value="{{ $timbangan->id }}" 
                                                                    data-nama="{{ $timbangan->nama_timbangan }}"
                                                                    data-kode="{{ $timbangan->kode_timbangan }}"
                                                                    {{ old('data_timbangan_id', $data->jenis == $timbangan->nama_timbangan && $data->kode_timbangan == $timbangan->kode_timbangan ? $timbangan->id : '') == $timbangan->id ? 'selected' : '' }}>
                                                                {{ $timbangan->nama_timbangan }} - {{ $timbangan->kode_timbangan }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('data_timbangan_id')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                    <small class="form-text text-muted">
                                                        <i class="fas fa-info-circle"></i> 
                                                        Pilih timbangan dari master data.
                                    @if(auth()->user()->hasPermissionTo('create-data-timbangan')) 
                                                        <a href="{{ route('data-timbangan.create') }}" target="_blank" class="text-primary">
                                                            <i class="fas fa-plus"></i> Tambah Data Timbangan Baru
                                                        </a>
                                                    @endif
</small>
                                                </div>

                                                <!-- Hidden fields untuk menyimpan jenis dan kode -->
                                                <input type="hidden" name="jenis" id="jenis" value="{{ old('jenis', $data->jenis) }}">
                                                <input type="hidden" name="kode_timbangan" id="kode_timbangan" value="{{ old('kode_timbangan', $data->kode_timbangan) }}">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Kolom Kanan -->
                                    <div class="col-md-6">
                                        <div class="card card-outline card-warning">
                                            <div class="card-header">
                                                <h3 class="card-title">
                                                    <i class="fas fa-check-circle"></i>
                                                    Parameter Pengecekan
                                                </h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label for="hasil_pengecekan">
                                                        <i class="fas fa-clipboard-check"></i>
                                                        Hasil Pengecekan <span class="text-danger">*</span>
                                                    </label>
                                                    <select name="hasil_pengecekan" id="hasil_pengecekan" class="form-control @error('hasil_pengecekan') is-invalid @enderror" required>
                                                        <option value="">-- Pilih Hasil Pengecekan --</option>
                                                        @foreach($hasilPengecekanOptions as $value => $label)
                                                            <option value="{{ $value }}" {{ (old('hasil_pengecekan', $data->hasil_pengecekan) == $value) ? 'selected' : '' }}>
                                                                {!! $label !!}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('hasil_pengecekan')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <div class="form-group">
                                                    <input type="hidden" name="gram" id="gram" value="{{ old('gram', $data->gram) }}">
                                                </div>

                                                <div class="form-group">
                                                    <label for="hasil_verifikasi_500">Hasil Verifikasi 500 Gr</label>
                                                    <input type="text" 
                                                        name="hasil_verifikasi_500" 
                                                        id="hasil_verifikasi_500" 
                                                        class="form-control @error('hasil_verifikasi_500') is-invalid @enderror" 
                                                        value="{{ old('hasil_verifikasi_500', $data->hasil_verifikasi_500) }}" 
                                                        placeholder="Isi hasil verifikasi 500 gr">
                                                    @error('hasil_verifikasi_500')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <div class="form-group">
                                                    <label for="hasil_verifikasi_1000">Hasil Verifikasi 1000 Gr</label>
                                                    <input type="text" 
                                                        name="hasil_verifikasi_1000" 
                                                        id="hasil_verifikasi_1000" 
                                                        class="form-control @error('hasil_verifikasi_1000') is-invalid @enderror" 
                                                        value="{{ old('hasil_verifikasi_1000', $data->hasil_verifikasi_1000) }}" 
                                                        placeholder="Isi hasil verifikasi 1000 gr">
                                                    @error('hasil_verifikasi_1000')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <div class="alert alert-info">
                                                    <i class="fas fa-info-circle"></i>
                                                    <strong>Informasi:</strong>
                                                    <ul class="mb-0 mt-2">
                                                        <li><strong>Hasil Pengecekan:</strong></li>
                                                        <li>✓ OK = Hasil pengecekan sesuai standar</li>
                                                        <li>✗ Tidak OK = Hasil pengecekan tidak sesuai standar</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-warning">
                                            <i class="fas fa-save"></i> Update Data
                                        </button>
                                        <a href="{{ route('timbangan.index') }}" class="btn btn-secondary ml-2">
                                            <i class="fas fa-arrow-left"></i> Kembali
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
    </div>
 </div>
 @push('scripts')
 <script>
$(document).ready(function() {
    // Auto-fill jenis dan kode_timbangan saat memilih timbangan di halaman edit
    $('.select-timbangan-edit').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var nama = selectedOption.data('nama');
        var kode = selectedOption.data('kode');
        
        // Set hidden fields
        $('#jenis').val(nama);
        $('#kode_timbangan').val(kode);
    });
    
    // Trigger change saat halaman load untuk set initial values
    $('.select-timbangan-edit').trigger('change');
});
</script>
@endpush
@endsection
