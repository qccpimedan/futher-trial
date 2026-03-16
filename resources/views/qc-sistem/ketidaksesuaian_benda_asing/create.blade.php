@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Tambah Ketidaksesuaian Benda Asing</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('ketidaksesuaian-benda-asing.index') }}">Ketidaksesuaian Benda Asing</a></li>
                        <li class="breadcrumb-item active">Tambah</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item"><a class="nav-link active" href="#ketidaksesuaian" data-toggle="tab">Ketidaksesuaian Benda Asing</a></li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="active tab-pane" id="ketidaksesuaian">
                                    <form action="{{ route('ketidaksesuaian-benda-asing.store') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
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
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="id_shift" class="font-weight-bold">Shift <span class="text-danger">*</span></label>
                                                    <select name="shift_id" id="id_shift" class="form-control" required>
                                                        <option value="">Pilih Shift</option>
                                                        @foreach($shifts as $shift)
                                                            <option value="{{ $shift->id }}" {{ old('id_shift') == $shift->id ? 'selected' : '' }}>
                                                                {{ $shift->shift }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="id_produk" class="font-weight-bold">Produk <span class="text-danger">*</span></label>
                                                    <select name="id_produk" id="id_produk" class="form-control @error('id_produk') is-invalid @enderror" required>
                                                        <option value="">Pilih Produk</option>
                                                        @foreach($produks as $produk)
                                                            <option value="{{ $produk->id }}" {{ old('id_produk') == $produk->id ? 'selected' : '' }}>
                                                                {{ $produk->nama_produk }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('id_produk')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
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
                                                <div class="form-group mb-3">
                                                    <label for="jam" class="font-weight-bold">Jam <span class="text-danger">*</span></label>
                                                    <input type="time" name="jam" id="jam" class="form-control @error('jam') is-invalid @enderror" 
                                                           value="{{ old('jam', date('H:i')) }}" required>
                                                    @error('jam')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="kode_produksi" class="font-weight-bold">Kode Produksi <span class="text-danger">*</span></label>
                                                    <input type="text" name="kode_produksi" id="kode_produksi" class="form-control @error('kode_produksi') is-invalid @enderror" 
                                                           value="{{ old('kode_produksi') }}" placeholder="Masukkan kode produksi" required>
                                                    @error('kode_produksi')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="jenis_kontaminan" class="font-weight-bold">Jenis Kontaminan <span class="text-danger">*</span></label>
                                                    <input type="text" name="jenis_kontaminan" id="jenis_kontaminan" class="form-control @error('jenis_kontaminan') is-invalid @enderror" 
                                                           value="{{ old('jenis_kontaminan') }}" placeholder="Masukkan jenis kontaminan" required>
                                                    @error('jenis_kontaminan')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="jumlah_produk_terdampak" class="font-weight-bold">Jumlah Produk Terdampak <span class="text-danger">*</span></label>
                                                    <input type="number" name="jumlah_produk_terdampak" id="jumlah_produk_terdampak" class="form-control @error('jumlah_produk_terdampak') is-invalid @enderror" 
                                                           value="{{ old('jumlah_produk_terdampak') }}" placeholder="Masukkan jumlah produk terdampak" min="1" required>
                                                    @error('jumlah_produk_terdampak')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="tahapan" class="font-weight-bold">Tahapan <span class="text-danger">*</span></label>
                                                    <input type="text" name="tahapan" id="tahapan" class="form-control @error('tahapan') is-invalid @enderror" 
                                                           value="{{ old('tahapan') }}" placeholder="Masukkan tahapan proses" required>
                                                    @error('tahapan')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="dokumentasi" class="font-weight-bold">Dokumentasi <span class="text-danger">*</span></label>
                                            <input type="file" name="dokumentasi" id="dokumentasi" 
                                                   class="form-control-file @error('dokumentasi') is-invalid @enderror" accept="image/*" capture="camera" required>
                                            <small class="form-text text-muted">Format: JPG, JPEG, PNG, GIF. File akan dikompresi otomatis</small>
                                            @error('dokumentasi')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group mt-4">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Simpan
                                            </button>
                                            <a href="{{ route('ketidaksesuaian-benda-asing.index') }}" class="btn btn-secondary btn-md ml-2">
                                                <i class="fas fa-arrow-left"></i> Kembali
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Auto-fill current time
        if (!$('#jam').val()) {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            $('#jam').val(`${hours}:${minutes}`);
        }
    });
</script>
@endsection