@extends('layouts.app')
@php
    $isLocalhost = in_array(request()->getHost(), ['localhost', '127.0.0.1', '10.68.1.37']);
    $assetPath = $isLocalhost ? 'public/' : '';
@endphp
@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Ketidaksesuaian Plastik</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('ketidaksesuaian-plastik.index') }}">Ketidaksesuaian Plastik</a></li>
                        <li class="breadcrumb-item active">Edit</li>
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
                                <li class="nav-item"><a class="nav-link active" href="#ketidaksesuaian" data-toggle="tab">Edit Ketidaksesuaian Plastik</a></li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="active tab-pane" id="ketidaksesuaian">
                                    <form action="{{ route('ketidaksesuaian-plastik.update', $ketidaksesuaianPlastik->uuid) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
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
                                                    <select name="id_shift" id="id_shift" class="form-control" required>
                                                        <option value="">Pilih Shift</option>
                                                        @foreach($shifts as $shift)
                                                            <option value="{{ $shift->id }}" 
                                                                {{ (old('id_shift', $ketidaksesuaianPlastik->id_shift) == $shift->id) ? 'selected' : '' }}>
                                                                {{ $shift->shift }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="tanggal" class="font-weight-bold">Tanggal <span class="text-danger">*</span></label>
                                                    <input type="datetime-local" name="tanggal" id="tanggal" class="form-control" 
                                                           value="{{ old('tanggal', $ketidaksesuaianPlastik->tanggal ? $ketidaksesuaianPlastik->tanggal->format('Y-m-d\TH:i') : '') }}" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="nama_plastik" class="font-weight-bold">Nama Plastik <span class="text-danger">*</span></label>
                                            <input type="text" name="nama_plastik" id="nama_plastik" class="form-control" 
                                                   value="{{ old('nama_plastik', $ketidaksesuaianPlastik->nama_plastik) }}" placeholder="Masukkan nama plastik" required>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="alasan_hold" class="font-weight-bold">Alasan Hold <span class="text-danger">*</span></label>
                                            <textarea name="alasan_hold" id="alasan_hold" class="form-control" rows="4" 
                                                      placeholder="Masukkan alasan hold" required>{{ old('alasan_hold', $ketidaksesuaianPlastik->alasan_hold) }}</textarea>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="hold_data" class="font-weight-bold">Hold Data <span class="text-danger">*</span></label>
                                            <textarea name="hold_data" id="hold_data" class="form-control" rows="4" 
                                                      placeholder="Masukkan hold data" required>{{ old('hold_data', $ketidaksesuaianPlastik->hold_data) }}</textarea>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="dokumentasi_tagging" class="font-weight-bold">Dokumentasi Tagging</label>
                                                    @if($ketidaksesuaianPlastik->dokumentasi_tagging)
                                                        <div class="mb-2">
                                                            <img src="{{ asset($assetPath . 'storage/' . $ketidaksesuaianPlastik->dokumentasi_tagging) }}" 
                                                                 alt="Dokumentasi Tagging" class="img-thumbnail" style="max-width: 200px;">
                                                            <p class="text-muted small">File saat ini: {{ $ketidaksesuaianPlastik->dokumentasi_tagging }}</p>
                                                        </div>
                                                    @endif
                                                    <input type="file" name="dokumentasi_tagging" id="dokumentasi_tagging" 
                                                           class="form-control-file" accept="image/*" capture="camera">
                                                    <small class="form-text text-muted">Format: JPG, JPEG, PNG, GIF. Maksimal 2MB. Kosongkan jika tidak ingin mengubah.</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="dokumentasi_penyimpangan_plastik" class="font-weight-bold">Dokumentasi Penyimpangan Plastik</label>
                                                    @if($ketidaksesuaianPlastik->dokumentasi_penyimpangan_plastik)
                                                        <div class="mb-2">
                                                            <img src="{{ asset($assetPath . 'storage/' . $ketidaksesuaianPlastik->dokumentasi_penyimpangan_plastik) }}" 
                                                                 alt="Dokumentasi Penyimpangan" class="img-thumbnail" style="max-width: 200px;">
                                                            <p class="text-muted small">File saat ini: {{ $ketidaksesuaianPlastik->dokumentasi_penyimpangan_plastik }}</p>
                                                        </div>
                                                    @endif
                                                    <input type="file" name="dokumentasi_penyimpangan_plastik" id="dokumentasi_penyimpangan_plastik" 
                                                           class="form-control-file" accept="image/*" capture="camera">
                                                    <small class="form-text text-muted">Format: JPG, JPEG, PNG, GIF. Maksimal 2MB. Kosongkan jika tidak ingin mengubah.</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group mt-4">
                                            <button type="submit" class="btn btn-warning">
                                                <i class="fas fa-save"></i> Update Data
                                            </button>
                                            <a href="{{ route('ketidaksesuaian-plastik.index') }}" class="btn btn-secondary btn-md ml-2">
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