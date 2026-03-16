@extends('layouts.app')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h1 class="m-0">Edit Data Defect</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('data-defect.index') }}">Data Defect</a></li>
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
                        <div class="card card-warning card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Form Edit Data Defect</h3>
                            </div>
                            <form action="{{ route('data-defect.update', $data->uuid) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="card-body">
                                    @if ($errors->any())
                                        <div class="alert alert-danger alert-dismissible fade show">
                                            <h5><i class="icon fas fa-ban"></i> Error!</h5>
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif

                                    @if(session('error'))
                                        <div class="alert alert-danger alert-dismissible fade show">
                                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif

                                    <div class="form-group">
                                        <label for="id_plan">Plan <span class="text-danger">*</span></label>
                                        <select name="id_plan" id="id_plan" class="form-control @error('id_plan') is-invalid @enderror" required>
                                            <option value="">Pilih Plan</option>
                                            @foreach($plans as $plan)
                                                <option value="{{ $plan->id }}" {{ old('id_plan', $data->id_plan) == $plan->id ? 'selected' : '' }}>
                                                    {{ $plan->nama_plan }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_plan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="jenis_defect">Jenis Defect <span class="text-danger">*</span></label>
                                        <input type="text" name="jenis_defect" id="jenis_defect" 
                                               class="form-control @error('jenis_defect') is-invalid @enderror" 
                                               placeholder="Masukkan Jenis Defect" 
                                               value="{{ old('jenis_defect', $data->jenis_defect) }}" required>
                                        @error('jenis_defect')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="spec_defect">Spec Defect</label>
                                        <input type="text" name="spec_defect" id="spec_defect" 
                                               class="form-control @error('spec_defect') is-invalid @enderror" 
                                               placeholder="Masukkan Spec Defect" 
                                               value="{{ old('spec_defect', $data->spec_defect) }}">
                                        @error('spec_defect')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save"></i> Update
                                    </button>
                                    <a href="{{ route('data-defect.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
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