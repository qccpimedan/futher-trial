@extends('layouts.app')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h1 class="m-0">Tambah Data Pembekuan IQF Roasting</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('pembekuan-iqf-roasting.index') }}">Pembekuan IQF Roasting</a></li>
                            <li class="breadcrumb-item active">Tambah Data</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-plus"></i> Form Tambah Pembekuan IQF Roasting
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

                                <!-- Display Related Process Data -->
                                @if($inputRoastingData || $hasilRoastingData)
                                    <div class="alert alert-info">
                                        <h5><i class="fas fa-info-circle"></i> Informasi Data Terkait</h5>
                                        
                                        @if($inputRoastingData)
                                        <div class="mb-2">
                                            <strong>Input Roasting:</strong>
                                            <span class="badge badge-primary">{{ $inputRoastingData->produk->nama_produk ?? 'N/A' }}</span>
                                            <span class="badge badge-info">{{ $inputRoastingData->shift->shift ?? 'N/A' }}</span>
                                            <small class="text-muted">({{ $inputRoastingData->tanggal ? \Carbon\Carbon::parse($inputRoastingData->tanggal)->format('d-m-Y H:i') : 'N/A' }})</small>
                                        </div>
                                        @endif
                                        
                                        @if($hasilRoastingData)
                                        <div class="mb-2">
                                            <strong>Hasil Proses Roasting:</strong>
                                            <span class="badge badge-primary">{{ $hasilRoastingData->produk->nama_produk ?? 'N/A' }}</span>
                                            <span class="badge badge-info">{{ $hasilRoastingData->shift->shift ?? 'N/A' }}</span>
                                            <small class="text-muted">({{ $hasilRoastingData->tanggal ? \Carbon\Carbon::parse($hasilRoastingData->tanggal)->format('d-m-Y H:i') : 'N/A' }})</small>
                                            - <strong>Suhu:</strong> {{ $hasilRoastingData->aktual_suhu_pusat }}°C
                                            @php
                                                $sensoriValue = $hasilRoastingData->sensori ?? null;
                                                if (is_array($sensoriValue)) {
                                                    $sensoriValue = implode(', ', array_filter(array_map('strval', $sensoriValue)));
                                                }
                                            @endphp
                                            - <strong>Sensori:</strong> {{ $sensoriValue !== null && $sensoriValue !== '' ? $sensoriValue : 'N/A' }}
                                        </div>
                                        @endif
                                    </div>
                                @endif

                                @if($roastingFanData)
                                    <div class="alert alert-warning">
                                        <h5><i class="fas fa-fan"></i> Data Roasting Fan Terkait</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($roastingFanData->tanggal)->format('d-m-Y H:i:s') }}<br>
                                                <strong>Produk:</strong> {{ $roastingFanData->produk->nama_produk ?? '-' }}<br>
                                                <strong>Shift:</strong> {{ $roastingFanData->shift->shift ?? '-' }}<br>
                                            </div>
                                            <div class="col-md-6">
                                                <strong>User:</strong> {{ $roastingFanData->user->name ?? '-' }}<br>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <form action="{{ route('pembekuan-iqf-roasting.store') }}" method="POST">
                                    @csrf                           
                                <!-- Hidden UUID fields -->
                                <input type="hidden" name="input_roasting_uuid" value="{{ $inputRoastingUuid ?? '' }}">
                                <input type="hidden" name="hasil_proses_roasting_uuid" value="{{ $hasilProsesRoastingUuid ?? '' }}">
                                <input type="hidden" name="proses_roasting_fan_uuid" value="{{ $prosesRoastingFanUuid ?? '' }}">
                                <input type="hidden" name="frayer_uuid" value="{{ $frayerUuid ?? '' }}">
                                <input type="hidden" name="breader_uuid" value="{{ $breaderUuid ?? '' }}">
                                <input type="hidden" name="battering_uuid" value="{{ $batteringUuid ?? '' }}">
                                <input type="hidden" name="predust_uuid" value="{{ $predustUuid ?? '' }}">
                                <input type="hidden" name="penggorengan_uuid" value="{{ $penggorenganUuid ?? '' }}">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="tanggal" class="font-weight-bold">Tanggal <span class="text-danger">*</span></label>
                                                @php
                                                    $userRole = auth()->user()->id_role ?? null;
                                                    $showTime = in_array($userRole, [1, 2, 5]); // superadmin, admin, spv
                                                    $displayFormat = $showTime ? 'd-m-Y H:i:s' : 'd-m-Y';
                                                    $submitFormat = 'd-m-Y H:i:s'; // Always submit with H:i:s
                                                    $now = \Carbon\Carbon::now('Asia/Jakarta');
                                                    $displayValue = $now->format($displayFormat);
                                                    $submitValue = $now->format($submitFormat);
                                                @endphp
                                                <input type="hidden" name="tanggal" id="tanggal_hidden" 
                                                        value="{{ old('tanggal', $submitValue) }}">
                                                <input type="text" class="form-control" id="tanggal_display" 
                                                        value="{{ old('tanggal', $displayValue) }}" readonly required>
                                                @error('tanggal')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="jam" class="font-weight-bold"><i class="fas fa-clock"></i> Jam <span class="text-danger">*</span></label>
                                                <input type="time" 
                                                       class="form-control @error('jam') is-invalid @enderror" 
                                                       id="jam" 
                                                       name="jam" 
                                                       value="{{ old('jam', \Carbon\Carbon::now('Asia/Jakarta')->format('H:i')) }}" 
                                                       required>
                                                @error('jam')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="suhu_ruang_iqf">Suhu Ruang IQF <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('suhu_ruang_iqf') is-invalid @enderror" 
                                                       id="suhu_ruang_iqf" name="suhu_ruang_iqf" 
                                                       value="{{ old('suhu_ruang_iqf') }}" 
                                                       placeholder="Masukkan suhu ruang IQF" required>
                                                @error('suhu_ruang_iqf')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="holding_time">Holding Time <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('holding_time') is-invalid @enderror" 
                                                       id="holding_time" name="holding_time" 
                                                       value="{{ old('holding_time') }}" 
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
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Simpan
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