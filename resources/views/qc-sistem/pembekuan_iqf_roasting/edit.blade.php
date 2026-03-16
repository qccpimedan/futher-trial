@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Data Pembekuan IQF Roasting</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pembekuan-iqf-roasting.index') }}">Pembekuan IQF Roasting</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Form Edit Pembekuan IQF Roasting</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('pembekuan-iqf-roasting.update', $pembekuanIqfRoasting->uuid) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <!-- Hidden UUID fields for process chain -->
                                <input type="hidden" name="frayer_uuid" value="{{ $pembekuanIqfRoasting->frayer_uuid }}">
                                <input type="hidden" name="breader_uuid" value="{{ $pembekuanIqfRoasting->breader_uuid }}">
                                <input type="hidden" name="battering_uuid" value="{{ $pembekuanIqfRoasting->battering_uuid }}">
                                <input type="hidden" name="predust_uuid" value="{{ $pembekuanIqfRoasting->predust_uuid }}">
                                <input type="hidden" name="penggorengan_uuid" value="{{ $pembekuanIqfRoasting->penggorengan_uuid }}">
                                <input type="hidden" name="hasil_proses_roasting_uuid" value="{{ $pembekuanIqfRoasting->hasil_proses_roasting_uuid }}">
                                <input type="hidden" name="proses_roasting_fan_uuid" value="{{ $pembekuanIqfRoasting->proses_roasting_fan_uuid }}">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                                            <input type="datetime-local" 
                                                   class="form-control @error('tanggal') is-invalid @enderror" 
                                                   id="tanggal" 
                                                   name="tanggal" 
                                                   value="{{ old('tanggal', \Carbon\Carbon::parse($pembekuanIqfRoasting->tanggal)->format('Y-m-d\TH:i')) }}" 
                                                   readonly>
                                            @error('tanggal')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="suhu_ruang_iqf">Suhu Ruang IQF <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('suhu_ruang_iqf') is-invalid @enderror" 
                                                   id="suhu_ruang_iqf" name="suhu_ruang_iqf" 
                                                   value="{{ old('suhu_ruang_iqf', $pembekuanIqfRoasting->suhu_ruang_iqf) }}" 
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
                                                   value="{{ old('holding_time', $pembekuanIqfRoasting->holding_time) }}" 
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
                                        <i class="fas fa-save"></i> Update Data
                                    </button>
                                    <a href="{{ route('pembekuan-iqf-roasting.index') }}" class="btn btn-secondary">
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