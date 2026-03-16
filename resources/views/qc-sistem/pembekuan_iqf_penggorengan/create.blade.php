@extends('layouts.app')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h1 class="m-0">Tambah Data Pembekuan IQF Penggorengan</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('pembekuan-iqf-penggorengan.index') }}">Pembekuan IQF Penggorengan</a></li>
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
                                    <i class="fas fa-plus"></i> Form Tambah Pembekuan IQF Penggorengan
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
                                @if($hasilPenggorenganData)
                                    <div class="alert alert-info">
                                        <h5><i class="fas fa-fire"></i> Data Hasil Penggorengan Terkait</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($hasilPenggorenganData->tanggal)->format('d-m-Y H:i:s') }}<br>
                                                <strong>Produk:</strong> {{ $hasilPenggorenganData->produk->nama_produk ?? '-' }}<br>
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Suhu Pusat:</strong> {{ $hasilPenggorenganData->aktual_suhu_pusat }} &deg;C<br>
                                                <strong>Sensori:</strong> {{ $hasilPenggorenganData->sensori }}<br>
                                                <strong>User:</strong> {{ $hasilPenggorenganData->user->name ?? '-' }}<br>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if($frayer2Data)
                                    <div class="alert alert-warning">
                                        <h5><i class="fas fa-fire"></i> Data Frayer-2 Terkait</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($frayer2Data->tanggal)->format('d-m-Y H:i:s') }}<br>
                                                <strong>Produk:</strong> {{ $frayer2Data->produk->nama_produk ?? '-' }}<br>
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Suhu Frayer:</strong> {{ $frayer2Data->suhuFrayer->suhu_frayer ?? '-' }} &deg;C<br>
                                                <strong>Waktu Penggorengan:</strong> {{ $frayer2Data->waktuPenggorengan->waktu_penggorengan ?? '-' }} Menit<br>
                                                <strong>Aktual Penggorengan:</strong> {{ $frayer2Data->aktual_penggorengan }} Menit<br>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if($frayerData && !$frayer2Data)
                                    <div class="alert alert-primary">
                                        <h5><i class="fas fa-fire"></i> Data Frayer Terkait</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($frayerData->tanggal)->format('d-m-Y H:i:s') }}<br>
                                                <strong>Produk:</strong> {{ $frayerData->produk->nama_produk ?? '-' }}<br>
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Suhu Frayer:</strong> {{ $frayerData->suhuFrayer->suhu_frayer ?? '-' }} &deg;C<br>
                                                <strong>Waktu Penggorengan:</strong> {{ $frayerData->waktuPenggorengan->waktu_penggorengan ?? '-' }} Menit<br>
                                                <strong>Aktual Penggorengan:</strong> {{ $frayerData->aktual_penggorengan }} Menit<br>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if($breaderData)
                                    <div class="alert alert-secondary">
                                        <h5><i class="fas fa-bread-slice"></i> Data Breader Terkait</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($breaderData->tanggal)->format('d-m-Y H:i:s') }}<br>
                                                <strong>Produk:</strong> {{ $breaderData->produk->nama_produk ?? '-' }}<br>
                                                <strong>User:</strong> {{ $breaderData->user->name ?? '-' }}<br>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if($batteringData)
                                    <div class="alert alert-success">
                                        <h5><i class="fas fa-layer-group"></i> Data Battering Terkait</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($batteringData->tanggal)->format('d-m-Y H:i:s') }}<br>
                                                <strong>Produk:</strong> {{ $batteringData->produk->nama_produk ?? '-' }}<br>
                                                <strong>User:</strong> {{ $batteringData->user->name ?? '-' }}<br>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if($predustData)
                                    <div class="alert alert-dark">
                                        <h5><i class="fas fa-powder"></i> Data Predust Terkait</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($predustData->tanggal)->format('d-m-Y H:i:s') }}<br>
                                                <strong>Produk:</strong> {{ $predustData->produk->nama_produk ?? '-' }}<br>
                                                <strong>User:</strong> {{ $predustData->user->name ?? '-' }}<br>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if($penggorenganData)
                                    <div class="alert alert-danger">
                                        <h5><i class="fas fa-fire-alt"></i> Data Penggorengan Terkait</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($penggorenganData->tanggal)->format('d-m-Y H:i:s') }}<br>
                                                <strong>Produk:</strong> {{ $penggorenganData->produk->nama_produk ?? '-' }}<br>
                                                <strong>User:</strong> {{ $penggorenganData->user->name ?? '-' }}<br>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <form action="{{ route('pembekuan-iqf-penggorengan.store') }}" method="POST">
                                    @csrf
                                    
                                    <!-- Hidden UUID fields -->
                                    <input type="hidden" name="hasil_penggorengan_uuid" value="{{ request('hasil_penggorengan_uuid') }}">
                                    <input type="hidden" name="frayer_uuid" value="{{ request('frayer_uuid') }}">
                                    <input type="hidden" name="frayer2_uuid" value="{{ request('frayer2_uuid') }}">
                                    <input type="hidden" name="breader_uuid" value="{{ request('breader_uuid') }}">
                                    <input type="hidden" name="battering_uuid" value="{{ request('battering_uuid') }}">
                                    <input type="hidden" name="predust_uuid" value="{{ request('predust_uuid') }}">
                                    <input type="hidden" name="penggorengan_uuid" value="{{ request('penggorengan_uuid') }}">

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
                                                <label for="jam" class="font-weight-bold">
                                                    <i class="fas fa-clock"></i> Jam <span class="text-danger">*</span>
                                                </label>
                                                <input type="time" class="form-control @error('jam') is-invalid @enderror" 
                                                       id="jam" name="jam" 
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
                                    </div>

                                    <div class="row">
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
