{{-- filepath: resources/views/qc-sistem/proses_penggorengan/edit.blade.php --}}
@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-fire text-danger"></i>
                        Edit Proses Penggorengan
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('penggorengan.index') }}">Penggorengan</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i>
                Terjadi kesalahan saat menyimpan data:
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i>
                        Form Edit Data
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('penggorengan.update', $item->uuid) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <!-- Tanggal -->
                                
                                <!-- Shift -->
                                <div class="form-group">
                                    <label>Shift</label>
                                    <select name="shift_id" class="form-control">
                                        <option value="">Pilih Shift</option>
                                        @foreach($shifts as $shift)
                                        <option value="{{ $shift->id }}" {{ old('shift_id', $item->shift_id) == $shift->id ? 'selected' : '' }}>
                                            {{ $shift->shift }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Tanggal</label>
                                    <input type="text" name="tanggal" class="form-control" value="{{ old('tanggal', $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y H:i:s') : '') }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label>No Of Strokes</label>
                                    <input type="number" name="no_of_strokes" class="form-control" value="{{ old('no_of_strokes', $item->no_of_strokes) }}">
                                </div>
                                <div class="form-group">
                                    <label>Hasil Pencetakan</label>
                                    <select name="hasil_pencetakan" class="form-control">
                                        <option value="✔" {{ old('hasil_pencetakan', $item->hasil_pencetakan) == '✔' ? 'selected' : '' }}>✔</option>
                                        <option value="✘" {{ old('hasil_pencetakan', $item->hasil_pencetakan) == '✘' ? 'selected' : '' }}>✘</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <!-- Produk -->
                                <div class="form-group">
                                    <label>Nama Produk</label>
                                    <input type="text" class="form-control" value="{{ $item->produk->nama_produk ?? '-' }}" readonly>
                                    <!-- Submit id_produk via hidden field to satisfy validation while keeping name readonly -->
                                    <input type="hidden" name="id_produk" value="{{ $item->id_produk }}">
                                </div>
                                
                                <!-- Kode Produksi -->
                                <div class="form-group">
                                    <label>Kode Produksi</label>
                                    <input type="text" name="kode_produksi" class="form-control" value="{{ old('kode_produksi', $item->kode_produksi) }}">
                                </div>
                                
                                <!-- Berat Produk -->
                                <div class="form-group mb-2">
                                    <label class="form-label">Berat Produk (gr) <span class="text-danger">*</span></label>
                                    <select id="edit_nilai_select_berat_penggorengan" class="form-control" name="berat_produk">  
                                    </select>
                                </div>
                                
                                <!-- Waktu Pemasakan -->
                                <div class="form-group">
                                    <label>Waktu Pemasakan</label>
                                    <input type="text" name="waktu_pemasakan" class="form-control" value="{{ old('waktu_pemasakan', $item->waktu_pemasakan) }}">
                                </div>
                                
                                <!-- Waktu Selesai Pemasakan -->
                                <!-- <div class="form-group">
                                    <label>Waktu Selesai Pemasakan</label>
                                    <input type="text" name="waktu_selesai_pemasakan" class="form-control" value="{{ old('waktu_selesai_pemasakan', $item->waktu_selesai_pemasakan) }}">
                                </div> -->
                            </div>
                        </div>
                        
                        <div class="form-group text-right">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save mr-2"></i>Update Data
                            </button>
                            <a href="{{ route('penggorengan.index') }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-arrow-left mr-2"></i>Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection