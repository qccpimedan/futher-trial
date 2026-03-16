{{-- filepath: resources/views/super-admin/produk/create.blade.php --}}
@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Tambah Jenis Produk</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('produk.index') }}">Data Jenis Produk</a></li>
                        <li class="breadcrumb-item active">Tambah Data</li>
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
                            <h3 class="card-title">Form Tambah Jenis Produk</h3>
                        </div>
                        <form action="{{ route('produk.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="id_plan">Nama Plan</label>
                                    <select name="id_plan" id="id_plan" class="form-control" required>
                                        <option value="">Pilih Plan</option>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}">{{ $plan->nama_plan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="nama_produk">Nama Produk</label>
                                    <input type="text" name="nama_produk" id="nama_produk" class="form-control" placeholder="Masukkan Nama Produk" required>
                                </div>
                                <div class="form-group">
                                    <label for="nama_produk">Jenis Produk</label>
                                     <select name="status_bahan" id="status_bahan" class="form-control" required>
                                         <option value="" selected disabled hidden>Pilih Jenis Produk</option>
                                        <option value="forming">Forming</option>
                                        <option value="non-forming">Non Forming</option>
                                   </select>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                                <a href="{{ route('produk.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection