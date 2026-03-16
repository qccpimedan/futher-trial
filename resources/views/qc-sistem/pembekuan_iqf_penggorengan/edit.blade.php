@extends('layouts.app')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h1 class="m-0">Edit Data Pembekuan IQF Penggorengan</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('pembekuan-iqf-penggorengan.index') }}">Pembekuan IQF Penggorengan</a></li>
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
                                    <i class="fas fa-edit"></i> Form Edit Pembekuan IQF Penggorengan
                                </h3>
                            </div>
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

                                <form action="{{ route('pembekuan-iqf-penggorengan.update', $data->uuid) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control datetimepicker-input @error('tanggal') is-invalid @enderror" 
                                                       id="tanggal" name="tanggal" 
                                                       value="{{ old('tanggal', \Carbon\Carbon::parse($data->tanggal)->format('d-m-Y H:i:s')) }}" 
                                                       data-toggle="datetimepicker" data-target="#tanggal" required>
                                                @error('tanggal')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="suhu_ruang_iqf">Suhu Ruang IQF <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('suhu_ruang_iqf') is-invalid @enderror" 
                                                       id="suhu_ruang_iqf" name="suhu_ruang_iqf" 
                                                       value="{{ old('suhu_ruang_iqf', $data->suhu_ruang_iqf) }}" 
                                                       placeholder="Masukkan suhu ruang IQF" required>
                                                @error('suhu_ruang_iqf')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="holding_time">Holding Time <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('holding_time') is-invalid @enderror" 
                                                       id="holding_time" name="holding_time" 
                                                       value="{{ old('holding_time', $data->holding_time) }}" 
                                                       placeholder="Masukkan holding time" required>
                                                @error('holding_time')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-warning">
                                            <i class="fas fa-save"></i> Update
                                        </button>
                                        <a href="{{ route('pembekuan-iqf-penggorengan.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left"></i> Kembali
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(function () {
        $('#tanggal').datetimepicker({
            format: 'DD-MM-YYYY HH:mm:ss',
            locale: 'id'
        });
    });
</script>
@endsection