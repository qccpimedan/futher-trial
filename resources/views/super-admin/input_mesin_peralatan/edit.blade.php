@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Input Mesin/Peralatan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('input-mesin-peralatan.index') }}">Input Mesin/Peralatan</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-edit text-warning mr-2"></i>
                                Form Edit Input Mesin/Peralatan
                            </h3>
                        </div>

                        <form action="{{ route('input-mesin-peralatan.update', $item->uuid) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="card-body">
                                @if($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="id_area">Area <span class="text-danger">*</span></label>
                                            <select class="form-control @error('id_area') is-invalid @enderror" id="id_area" name="id_area" required>
                                                <option value="">Pilih Area</option>
                                                @foreach($areas as $area)
                                                    <option value="{{ $area->id }}" {{ old('id_area', $item->id_area) == $area->id ? 'selected' : '' }}>
                                                        {{ $area->area }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('id_area')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nama_mesin">Nama Mesin/Peralatan <span class="text-danger">*</span></label>
                                            <input type="text" id="nama_mesin" name="nama_mesin" class="form-control @error('nama_mesin') is-invalid @enderror" value="{{ old('nama_mesin', $item->nama_mesin) }}" required>
                                            @error('nama_mesin')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <a href="{{ route('input-mesin-peralatan.index') }}" class="btn btn-md btn-secondary">
                                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                                </a>
                                <button type="submit" class="btn btn-md btn-primary">
                                    <i class="fas fa-save mr-2"></i>Update Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
