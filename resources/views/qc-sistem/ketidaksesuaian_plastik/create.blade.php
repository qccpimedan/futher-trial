@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Tambah Ketidaksesuaian Plastik</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('ketidaksesuaian-plastik.index') }}">Ketidaksesuaian Plastik</a></li>
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
                                <li class="nav-item"><a class="nav-link active" href="#ketidaksesuaian" data-toggle="tab">Ketidaksesuaian Plastik</a></li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="active tab-pane" id="ketidaksesuaian">
                                    <form action="{{ route('ketidaksesuaian-plastik.store') }}" method="POST" enctype="multipart/form-data">
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
                                                    <select name="id_shift" id="id_shift" class="form-control" required>
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


                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group mb-3">
                                                    <label for="jam">Jam <span class="text-danger">*</span></label>
                                                    <input type="time" name="jam" id="jam" 
                                                        class="form-control @error('jam') is-invalid @enderror" 
                                                        value="{{ old('jam', date('H:i')) }}" required>
                                                    @error('jam')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="nama_plastik" class="font-weight-bold">Nama Plastik <span class="text-danger">*</span></label>
                                            <input type="text" name="nama_plastik" id="nama_plastik" class="form-control" 
                                                   value="{{ old('nama_plastik') }}" placeholder="Masukkan nama plastik" required>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="alasan_hold" class="font-weight-bold">Alasan Hold <span class="text-danger">*</span></label>
                                            <textarea name="alasan_hold" id="alasan_hold" class="form-control" rows="4" 
                                                      placeholder="Masukkan alasan hold" required>{{ old('alasan_hold') }}</textarea>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="hold_data" class="font-weight-bold">Hold Number <span class="text-danger">*</span></label>
                                            <textarea name="hold_data" id="hold_data" class="form-control" rows="4" 
                                                      placeholder="Masukkan hold data" required>{{ old('hold_data') }}</textarea>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="dokumentasi_tagging" class="font-weight-bold">Dokumentasi Tagging</label>
                                                    <input type="file" name="dokumentasi_tagging" id="dokumentasi_tagging" 
                                                           class="form-control-file" accept="image/*" capture="camera" required>
                                                    <small class="form-text text-muted">Format: JPG, JPEG, PNG, GIF. Maksimal 2MB</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="dokumentasi_penyimpangan_plastik" class="font-weight-bold">Dokumentasi Penyimpangan Plastik</label>
                                                    <input type="file" name="dokumentasi_penyimpangan_plastik" id="dokumentasi_penyimpangan_plastik" 
                                                           class="form-control-file" accept="image/*" capture="camera" required>
                                                    <small class="form-text text-muted">Format: JPG, JPEG, PNG, GIF. Maksimal 2MB</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group mt-4">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Simpan
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