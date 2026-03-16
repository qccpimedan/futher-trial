{{-- filepath: resources/views/qc-sistem/frayer_3/edit.blade.php --}}
@extends('layouts.app')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h1 class="m-0">Edit Data Frayer 3</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('frayer-3.index') }}">Frayer 3</a></li>
                            <li class="breadcrumb-item active">Edit Data</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-warning">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-edit"></i> Form Edit Frayer 3
                                </h3>
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form class="form-horizontal mb-4" method="POST" action="{{ route('frayer-3.update', ['uuid' => $frayer3->uuid]) }}">
                                @csrf
                                @method('PUT')
                                
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <!-- Informasi Dasar Card -->
                                            <div class="card card-primary card-outline">
                                                <div class="card-header">
                                                    <h3 class="card-title">
                                                        <i class="fas fa-info-circle"></i> Informasi Dasar
                                                    </h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group row">
                                                        <label for="tanggal" class="col-sm-4 col-form-label">
                                                            <i class="fas fa-calendar"></i> Tanggal
                                                        </label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control" id="tanggal" name="tanggal" value="{{ \Carbon\Carbon::parse($frayer3->tanggal)->format('d-m-Y H:i:s') }}" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="id_produk" class="col-sm-4 col-form-label">
                                                            <i class="fas fa-box"></i> Produk
                                                        </label>
                                                        <div class="col-sm-8">
                                                            <select class="form-control" id="id_produk" name="id_produk" required>
                                                                <option value="">Pilih Produk</option>
                                                                @foreach ($products as $product)
                                                                    <option value="{{ $product->id }}" {{ $frayer3->id_produk == $product->id ? 'selected' : '' }}>
                                                                        {{ $product->nama_produk }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <!-- Parameter Proses Card -->
                                            <div class="card card-success card-outline">
                                                <div class="card-header">
                                                    <h3 class="card-title">
                                                        <i class="fas fa-fire"></i> Parameter Proses
                                                    </h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group row">
                                                        <label for="id_suhu_frayer" class="col-sm-4 col-form-label">
                                                            <i class="fas fa-thermometer-half"></i> Standart Suhu Frayer
                                                        </label>
                                                        <div class="col-sm-8">
                                                            <select class="form-control" id="id_suhu_frayer" name="id_suhu_frayer" required>
                                                                <option value="">Pilih Suhu Frayer</option>
                                                                @foreach ($suhuFrayers as $suhu)
                                                                    <option value="{{ $suhu->id }}" {{ $frayer3->id_suhu_frayer == $suhu->id ? 'selected' : '' }}>
                                                                        {{ $suhu->suhu_frayer }}°C
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="aktual_suhu_penggorengan" class="col-sm-4 col-form-label">
                                                            <i class="fas fa-thermometer-half"></i> Aktual Suhu Frayer
                                                        </label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control" id="aktual_suhu_penggorengan" name="aktual_suhu_penggorengan" value="{{ $frayer3->aktual_suhu_penggorengan }}" placeholder="Masukkan suhu aktual (°C)">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="id_waktu_penggorengan" class="col-sm-4 col-form-label">
                                                            <i class="fas fa-stopwatch"></i> Standart Waktu Penggorengan
                                                        </label>
                                                        <div class="col-sm-8">
                                                            <select class="form-control" id="id_waktu_penggorengan" name="id_waktu_penggorengan" required>
                                                                <option value="">Pilih Waktu Penggorengan</option>
                                                                @foreach ($waktuPenggorengans as $waktu)
                                                                    <option value="{{ $waktu->id }}" {{ $frayer3->id_waktu_penggorengan == $waktu->id ? 'selected' : '' }}>
                                                                        {{ $waktu->waktu_penggorengan }} detik
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="aktual_penggorengan" class="col-sm-4 col-form-label">
                                                            <i class="fas fa-chart-line"></i> Aktual Penggorengan
                                                        </label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control" id="aktual_penggorengan" name="aktual_penggorengan" value="{{ $frayer3->aktual_penggorengan }}" required>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="tpm_minyak" class="col-sm-4 col-form-label">
                                                            <i class="fas fa-tint"></i> TPM Minyak
                                                        </label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control" id="tpm_minyak" name="tpm_minyak" value="{{ $frayer3->tpm_minyak }}" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-warning">
                                                <i class="fas fa-save"></i> Update Data
                                            </button>
                                            <a href="{{ route('proses-frayer.index') }}" class="btn btn-secondary ml-2">
                                                <i class="fas fa-arrow-left"></i> Kembali
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection