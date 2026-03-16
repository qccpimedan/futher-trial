@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            @if(request('from_breader'))
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    Melanjutkan proses dari Breader ke Line {{ request('line') }}
                </div>
            @endif
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
              @if(request('from_breader'))
                <li class="breadcrumb-item"><a href="{{ route('proses-breader.index') }}">Proses Breader</a></li>
              @endif
              <li class="breadcrumb-item active">Frayer</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- /.col -->
          <div class="col-md-12">
            <div class="card card-primary card-tabs">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link" href="#frayer_1" data-toggle="tab">Line 1</a></li>
                  <li class="nav-item"><a class="nav-link" href="#frayer_2" data-toggle="tab">Line 2</a></li>
                  <!-- <li class="nav-item"><a class="nav-link" href="#frayer_3" data-toggle="tab">Line 3</a></li> -->
                  <li class="nav-item"><a class="nav-link" href="#frayer_4" data-toggle="tab">Line 3</a></li>
                  <li class="nav-item"><a class="nav-link" href="#frayer_5" data-toggle="tab">Line 4</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                    <!-- line 1 -->
                    <div class="tab-pane" id="frayer_1">
                        <h4><b>Frayer 1</b></h4>
                        <form class="form-horizontal mb-4" method="POST" action="{{ route('proses-frayer.store') }}{{ request('penggorengan_uuid') || request('predust_uuid') || request('battering_uuid') || request('breader_uuid') ? '?' : '' }}{{ request('penggorengan_uuid') ? 'penggorengan_uuid=' . request('penggorengan_uuid') : '' }}{{ (request('penggorengan_uuid') && request('predust_uuid')) ? '&' : '' }}{{ request('predust_uuid') ? 'predust_uuid=' . request('predust_uuid') : '' }}{{ ((request('penggorengan_uuid') || request('predust_uuid')) && request('battering_uuid')) ? '&' : '' }}{{ request('battering_uuid') ? 'battering_uuid=' . request('battering_uuid') : '' }}{{ ((request('penggorengan_uuid') || request('predust_uuid') || request('battering_uuid')) && request('breader_uuid')) ? '&' : '' }}{{ request('breader_uuid') ? 'breader_uuid=' . request('breader_uuid') : '' }}">
                            @csrf
                            {{-- Hidden UUID Fields untuk Relasi --}}
                            @if(request('penggorengan_uuid'))
                                <input type="hidden" name="penggorengan_uuid" value="{{ request('penggorengan_uuid') }}">
                            @endif
                            @if(request('predust_uuid'))
                                <input type="hidden" name="predust_uuid" value="{{ request('predust_uuid') }}">
                            @endif
                            @if(request('battering_uuid'))
                                <input type="hidden" name="battering_uuid" value="{{ request('battering_uuid') }}">
                            @endif
                            @if(request('breader_uuid'))
                                <input type="hidden" name="breader_uuid" value="{{ request('breader_uuid') }}">
                            @endif

                            {{-- Informasi Relasi dari Proses Sebelumnya --}}
                            @if(isset($penggorenganData) || isset($predustData) || isset($batteringData) || isset($breaderData))
                                <div class="card card-info card-outline mb-3">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-link"></i> Informasi Relasi Proses
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        @if(isset($penggorenganData))
                                            <div class="alert alert-info">
                                                <h6><i class="fas fa-fire"></i> Data Penggorengan:</h6>
                                                <ul class="mb-0">
                                                    <li><strong>Nama Produk:</strong> {{ $penggorenganData->produk->nama_produk ?? '-' }}</li>
                                                    <li><strong>Kode Produksi:</strong> {{ $penggorenganData->kode_produksi }}</li>
                                                    <li><strong>Tanggal:</strong> {{ $penggorenganData->tanggal->format('d-m-Y H:i:s') }}</li>
                                                </ul>
                                            </div>
                                        @endif
                                        
                                        @if(isset($predustData))
                                            <div class="alert alert-warning">
                                                <h6><i class="fas fa-layer-group"></i> Data Predust:</h6>
                                                <ul class="mb-0">
                                                    <li><strong>Nama Produk:</strong> {{ $predustData->produk->nama_produk ?? '-' }}</li>
                                                    <li><strong>Kode Produksi:</strong> {{ $predustData->kode_produksi }}</li>
                                                    <li><strong>Tanggal:</strong> {{ $predustData->tanggal->format('d-m-Y H:i:s') }}</li>
                                                </ul>
                                            </div>
                                        @endif
                                        
                                        @if(isset($batteringData))
                                            <div class="alert alert-success">
                                                <h6><i class="fas fa-tint"></i> Data Battering:</h6>
                                                <ul class="mb-0">
                                                    <li><strong>Nama Produk:</strong> {{ $batteringData->produk->nama_produk ?? '-' }}</li>
                                                    <li><strong>Kode Produksi:</strong> {{ $batteringData->kode_produksi_better }}</li>
                                                    <li><strong>Tanggal:</strong> {{ $batteringData->tanggal->format('d-m-Y H:i:s') }}</li>
                                                </ul>
                                            </div>
                                        @endif
                                        
                                        @if(isset($breaderData))
                                            <div class="alert alert-primary">
                                                <h6><i class="fas fa-cookie-bite"></i> Data Breader:</h6>
                                                <ul class="mb-0">
                                                    <li><strong>Nama Produk:</strong> {{ $breaderData->produk->nama_produk ?? '-' }}</li>
                                                    <li><strong>Kode Produksi:</strong> {{ $breaderData->kode_produksi }}</li>
                                                    <li><strong>Tanggal:</strong> {{ $breaderData->tanggal->format('d-m-Y H:i:s') }}</li>
                                                </ul>
                                            </div>
                                        @endif
                                        
                                    </div>
                                </div>
                            @endif

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
                                                    <input type="text" class="form-control" name="tanggal" value="{{ old('tanggal', \Carbon\Carbon::now('Asia/Jakarta')->format('d-m-Y H:i:s')) }}" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="jam" class="col-sm-4 col-form-label">
                                                    <i class="fas fa-clock"></i> Jam <span class="text-danger">*</span>
                                                </label>
                                                <div class="col-sm-8">
                                                    <input type="time" class="form-control @error('jam') is-invalid @enderror" name="jam" value="{{ old('jam', \Carbon\Carbon::now('Asia/Jakarta')->format('H:i')) }}" required>
                                                    @error('jam')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
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
                                                <label for="id_produk" class="col-sm-4 col-form-label">
                                                    <i class="fas fa-box"></i> Produk
                                                </label>
                                                <div class="col-sm-8">
                                                <select class="form-control" id="id_produk_f2" name="id_produk" required>
                                                    <option value="">Pilih Produk</option>
                                                    @foreach ($products as $product)
                                                        <option value="{{ $product->id }}">{{ $product->nama_produk }}</option>
                                                    @endforeach
                                                </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="id_suhu_frayer_1" class="col-sm-4 col-form-label">
                                                    <i class="fas fa-thermometer-half"></i> Standard Suhu Frayer
                                                </label>
                                                <div class="col-sm-8">
                                                <select class="form-control" id="id_suhu_frayer_1_f2" name="id_suhu_frayer_1" required>
                                                    <option value="">Pilih Suhu Frayer</option>
                                                </select>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="aktual_suhu_penggorengan" class="col-sm-4 col-form-label">
                                                    <i class="fas fa-thermometer-half"></i> Aktual Suhu Frayer
                                                </label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="aktual_suhu_penggorengan" name="aktual_suhu_penggorengan" placeholder="Masukkan suhu aktual (°C)">
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label for="id_waktu_penggorengan" class="col-sm-4 col-form-label">
                                                    <i class="fas fa-stopwatch"></i>Standart Waktu Penggorengan
                                                </label>
                                                <div class="col-sm-8">
                                                <select class="form-control" id="id_waktu_penggorengan_f2" name="id_waktu_penggorengan" required>
                                                    <option value="">Pilih Waktu Penggorengan</option>
                                                </select>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="aktual_penggorengan" class="col-sm-4 col-form-label">
                                                    <i class="fas fa-chart-line"></i> Aktual Waktu Penggorengan
                                                </label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="aktual_penggorengan_f2" name="aktual_penggorengan" required>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="tpm_minyak" class="col-sm-4 col-form-label">
                                                    <i class="fas fa-tint"></i> TPM Minyak
                                                </label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="tpm_minyak_f2" name="tpm_minyak" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-12">
                                        <!-- Tombol untuk semua line -->
                                        <button type="submit" class="btn btn-primary mr-2" id="submitBtnFrayer1">
                                            <i class="fas fa-save"></i> Simpan Frayer 1
                                        </button>
                                        
                                        <!-- Tombol khusus Line 1 untuk simpan dan lanjut ke Frayer 2 -->
                                        <button type="button" class="btn btn-success mr-2 line1-continue-btn" id="saveAndContinueBtn">
                                            <i class="fas fa-save mr-2"></i>Simpan & Lanjut Frayer 2
                                        </button>

                                        <button type="button" class="btn btn-warning mr-2" id="skipFrayer1Btn">
                                            <i class="fas fa-forward mr-2"></i>Skip Frayer 1
                                        </button>
                                        
                                        <a href="{{ route('proses-frayer.index') }}" class="btn btn-secondary ml-2" id="backBtnFrayer1">
                                            <i class="fas fa-arrow-left"></i> Kembali
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div id="frayer-2-section" style="display: none;">
                        <h4><b>Frayer 2</b></h4>
                        <form class="form-horizontal mb-4" method="POST" action="{{ route('frayer-2.store') }}{{ request('frayer_uuid') ? '?frayer_uuid=' . request('frayer_uuid') : '' }}">
                            @csrf
                            <!-- Hidden input untuk UUID relasi -->
                            <input type="hidden" name="frayer_uuid" value="{{ request('frayer_uuid') }}">

                            @if(request('penggorengan_uuid'))
                                <input type="hidden" name="penggorengan_uuid" value="{{ request('penggorengan_uuid') }}">
                            @endif
                            @if(request('predust_uuid'))
                                <input type="hidden" name="predust_uuid" value="{{ request('predust_uuid') }}">
                            @endif
                            @if(request('battering_uuid'))
                                <input type="hidden" name="battering_uuid" value="{{ request('battering_uuid') }}">
                            @endif
                            @if(request('breader_uuid'))
                                <input type="hidden" name="breader_uuid" value="{{ request('breader_uuid') }}">
                            @endif
                            
                            @if(isset($frayerData) && $frayerData)
                            <!-- Card untuk menampilkan informasi Proses Frayer yang terkait -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <h5><i class="fas fa-link"></i> <strong>Data Proses Frayer Terkait</strong></h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($frayerData->tanggal)->format('d-m-Y H:i:s') }}</p>
                                                <p><strong>Produk:</strong> {{ $frayerData->produk->jenis_produk ?? '-' }}</p>
                                              
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Suhu Frayer 1:</strong> {{ $frayerData->suhuFrayer->suhu_frayer_1 ?? '-' }}°C</p>
                                                <p><strong>Waktu Penggorengan:</strong> {{ $frayerData->waktuPenggorengan->waktu_penggorengan ?? '-' }} detik</p>
                                                <p><strong>TPM Minyak:</strong> {{ $frayerData->tpm_minyak ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
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
                                                    <input type="text" class="form-control" name="tanggal" value="{{ old('tanggal', \Carbon\Carbon::now('Asia/Jakarta')->format('d-m-Y H:i:s')) }}" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="jam" class="col-sm-4 col-form-label">
                                                    <i class="fas fa-clock"></i> Jam <span class="text-danger">*</span>
                                                </label>
                                                <div class="col-sm-8">
                                                    <input type="time" class="form-control @error('jam') is-invalid @enderror" name="jam" value="{{ old('jam', \Carbon\Carbon::now('Asia/Jakarta')->format('H:i')) }}" required>
                                                    @error('jam')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
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
                                                <label for="id_produk_f3" class="col-sm-4 col-form-label">
                                                    <i class="fas fa-box"></i> Produk
                                                </label>
                                                <div class="col-sm-8">
                                                    <select class="form-control" id="id_produk_f3" name="id_produk" required>
                                                        <option value="">Pilih Produk</option>
                                                        @foreach ($products as $product)
                                                            <option value="{{ $product->id }}">{{ $product->nama_produk }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="id_suhu_frayer_2" class="col-sm-4 col-form-label">
                                                    <i class="fas fa-thermometer-half"></i>Standard Suhu Frayer 2
                                                </label>
                                                <div class="col-sm-8">
                                                    <select class="form-control" id="id_suhu_frayer_2" name="id_suhu_frayer_2" required>
                                                        <option value="">Pilih Suhu Frayer 2</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="aktual_suhu_penggorengan" class="col-sm-4 col-form-label">
                                                    <i class="fas fa-thermometer-half"></i> Aktual Suhu Frayer 2
                                                </label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="aktual_suhu_penggorengan" name="aktual_suhu_penggorengan" placeholder="Masukkan suhu aktual (°C)">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="id_waktu_penggorengan_2" class="col-sm-4 col-form-label">
                                                    <i class="fas fa-stopwatch"></i>Standart Waktu Penggorengan 2
                                                </label>
                                                <div class="col-sm-8">
                                                <select class="form-control" id="id_waktu_penggorengan_2" name="id_waktu_penggorengan_2" required>
                                                    <option value="">Pilih Waktu Penggorengan</option>
                                                </select>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="aktual_penggorengan" class="col-sm-4 col-form-label">
                                                    <i class="fas fa-chart-line"></i> Aktual Penggorengan
                                                </label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="aktual_penggorengan_f2" name="aktual_penggorengan" required>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="tpm_minyak" class="col-sm-4 col-form-label">
                                                    <i class="fas fa-tint"></i> TPM Minyak
                                                </label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="tpm_minyak_f2" name="tpm_minyak" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Simpan Data
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
                    
                    <!-- line 2 -->
                    <div class="tab-pane" id="frayer_2">
                        <h4><b>Frayer 3</b></h4>
                        <form class="form-horizontal mb-4" method="POST" action="{{ route('frayer-3.store') }}{{ request('penggorengan_uuid') || request('predust_uuid') || request('battering_uuid') || request('breader_uuid') ? '?' : '' }}{{ request('penggorengan_uuid') ? 'penggorengan_uuid=' . request('penggorengan_uuid') : '' }}{{ (request('penggorengan_uuid') && request('predust_uuid')) ? '&' : '' }}{{ request('predust_uuid') ? 'predust_uuid=' . request('predust_uuid') : '' }}{{ ((request('penggorengan_uuid') || request('predust_uuid')) && request('battering_uuid')) ? '&' : '' }}{{ request('battering_uuid') ? 'battering_uuid=' . request('battering_uuid') : '' }}{{ ((request('penggorengan_uuid') || request('predust_uuid') || request('battering_uuid')) && request('breader_uuid')) ? '&' : '' }}{{ request('breader_uuid') ? 'breader_uuid=' . request('breader_uuid') : '' }}">
                            @csrf
                            <!-- Hidden input untuk UUID relasi -->
                            @if(request('penggorengan_uuid'))
                                <input type="hidden" name="penggorengan_uuid" value="{{ request('penggorengan_uuid') }}">
                            @endif
                            @if(request('predust_uuid'))
                                <input type="hidden" name="predust_uuid" value="{{ request('predust_uuid') }}">
                            @endif
                            @if(request('battering_uuid'))
                                <input type="hidden" name="battering_uuid" value="{{ request('battering_uuid') }}">
                            @endif
                            @if(request('breader_uuid'))
                                <input type="hidden" name="breader_uuid" value="{{ request('breader_uuid') }}">
                            @endif
                            
                            @if(isset($penggorenganData) || isset($predustData) || isset($batteringData) || isset($breaderData))
                            <!-- Card untuk menampilkan informasi relasi -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="card card-info card-outline">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="fas fa-link"></i> Informasi Relasi
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            @if(isset($penggorenganData) && $penggorenganData)
                                                <div class="alert alert-info">
                                                    <h6><i class="fas fa-fire"></i> Data Penggorengan:</h6>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <ul class="mb-0">
                                                                <li><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($penggorenganData->tanggal)->format('d-m-Y H:i:s') }}</li>
                                                                <li><strong>Produk:</strong> {{ $penggorenganData->produk->nama_produk ?? '-' }}</li>
                                                             
                                                            </ul>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <ul class="mb-0">
                                                                <li><strong>Suhu:</strong> {{ $penggorenganData->suhu_penggorengan ?? '-' }}°C</li>
                                                                <li><strong>Waktu:</strong> {{ $penggorenganData->waktu_penggorengan ?? '-' }} detik</li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if(isset($predustData) && $predustData)
                                                <div class="alert alert-warning">
                                                    <h6><i class="fas fa-layer-group"></i> Data Predust:</h6>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <ul class="mb-0">
                                                                <li><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($predustData->tanggal)->format('d-m-Y H:i:s') }}</li>
                                                                <li><strong>Produk:</strong> {{ $predustData->produk->nama_produk ?? '-' }}</li>
                                                               
                                                            </ul>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <ul class="mb-0">
                                                                <li><strong>Jenis Predust:</strong> {{ $predustData->jenisPredust->jenis_predust ?? '-' }}</li>
                                                                <li><strong>Berat:</strong> {{ $predustData->berat_predust ?? '-' }} kg</li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if(isset($batteringData) && $batteringData)
                                                <div class="alert alert-success">
                                                    <h6><i class="fas fa-tint"></i> Data Battering:</h6>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <ul class="mb-0">
                                                                <li><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($batteringData->tanggal)->format('d-m-Y H:i:s') }}</li>
                                                                <li><strong>Produk:</strong> {{ $batteringData->produk->nama_produk ?? '-' }}</li>
                                                        
                                                            </ul>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <ul class="mb-0">
                                                                <li><strong>Berat Adonan:</strong> {{ $batteringData->berat_adonan ?? '-' }} kg</li>
                                                                <li><strong>Suhu Adonan:</strong> {{ $batteringData->suhu_adonan ?? '-' }}°C</li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if(isset($breaderData) && $breaderData)
                                                <div class="alert alert-primary">
                                                    <h6><i class="fas fa-bread-slice"></i> Data Breader:</h6>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <ul class="mb-0">
                                                                <li><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($breaderData->tanggal)->format('d-m-Y H:i:s') }}</li>
                                                                <li><strong>Produk:</strong> {{ $breaderData->produk->nama_produk ?? '-' }}</li>
                                                            </ul>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <ul class="mb-0">
                                                                <li><strong>Jenis Breader:</strong> {{ $breaderData->jenisBreader->jenis_breader ?? '-' }}</li>
                                                                <li><strong>Berat:</strong> {{ $breaderData->berat_breader ?? '-' }} kg</li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
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
                                                    <input type="text" class="form-control" name="tanggal" value="{{ old('tanggal', \Carbon\Carbon::now('Asia/Jakarta')->format('d-m-Y H:i:s')) }}" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="jam" class="col-sm-4 col-form-label">
                                                    <i class="fas fa-clock"></i> Jam <span class="text-danger">*</span>
                                                </label>
                                                <div class="col-sm-8">
                                                    <input type="time" class="form-control @error('jam') is-invalid @enderror" name="jam" value="{{ old('jam', \Carbon\Carbon::now('Asia/Jakarta')->format('H:i')) }}" required>
                                                    @error('jam')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
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
                                                <label for="id_produk" class="col-sm-4 col-form-label">
                                                    <i class="fas fa-box"></i> Produk
                                                </label>
                                                <div class="col-sm-8">
                                                <select class="form-control" id="id_produk_f4" name="id_produk" required>
                                                    <option value="">Pilih Produk</option>
                                                    @foreach ($products as $product)
                                                        <option value="{{ $product->id }}">{{ $product->nama_produk }}</option>
                                                    @endforeach
                                                </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="id_suhu_frayer" class="col-sm-4 col-form-label">
                                                    <i class="fas fa-thermometer-half"></i> Standart Suhu Frayer
                                                </label>
                                                <div class="col-sm-8">
                                                <select class="form-control" id="id_suhu_frayer" name="id_suhu_frayer" required>
                                                    <option value="">Pilih Suhu Frayer</option>
                                                </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="aktual_suhu_penggorengan" class="col-sm-4 col-form-label">
                                                    <i class="fas fa-thermometer-half"></i> Aktual Suhu Frayer
                                                </label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="aktual_suhu_penggorengan" name="aktual_suhu_penggorengan" placeholder="Masukkan suhu aktual (°C)">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="id_waktu_penggorengan" class="col-sm-4 col-form-label">
                                                    <i class="fas fa-stopwatch"></i>Standart Waktu Penggorengan
                                                </label>
                                                <div class="col-sm-8">
                                                <select class="form-control" id="id_waktu_penggorengan" name="id_waktu_penggorengan" required>
                                                    <option value="">Pilih Waktu Penggorengan</option>
                                                </select>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="aktual_penggorengan" class="col-sm-4 col-form-label">
                                                    <i class="fas fa-chart-line"></i> Aktual Penggorengan
                                                </label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="aktual_penggorengan" name="aktual_penggorengan" required>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="tpm_minyak" class="col-sm-4 col-form-label">
                                                    <i class="fas fa-tint"></i> TPM Minyak
                                                </label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="tpm_minyak" name="tpm_minyak" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Simpan Data
                                        </button>
                                        <a href="{{ route('proses-frayer.index') }}" class="btn btn-secondary ml-2">
                                            <i class="fas fa-arrow-left"></i> Kembali
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- line 3 -->
                    <div class="tab-pane" id="frayer_4">
                        <h4><b>Frayer 4</b></h4>
                        <form class="form-horizontal mb-4" method="POST" action="{{ route('frayer-4.store') }}{{ request('penggorengan_uuid') || request('predust_uuid') || request('battering_uuid') || request('breader_uuid') ? '?' : '' }}{{ request('penggorengan_uuid') ? 'penggorengan_uuid=' . request('penggorengan_uuid') : '' }}{{ (request('penggorengan_uuid') && request('predust_uuid')) ? '&' : '' }}{{ request('predust_uuid') ? 'predust_uuid=' . request('predust_uuid') : '' }}{{ ((request('penggorengan_uuid') || request('predust_uuid')) && request('battering_uuid')) ? '&' : '' }}{{ request('battering_uuid') ? 'battering_uuid=' . request('battering_uuid') : '' }}{{ ((request('penggorengan_uuid') || request('predust_uuid') || request('battering_uuid')) && request('breader_uuid')) ? '&' : '' }}{{ request('breader_uuid') ? 'breader_uuid=' . request('breader_uuid') : '' }}">
                            @csrf
                            <!-- Hidden input untuk UUID relasi -->
                            @if(request('penggorengan_uuid'))
                                <input type="hidden" name="penggorengan_uuid" value="{{ request('penggorengan_uuid') }}">
                            @endif
                            @if(request('predust_uuid'))
                                <input type="hidden" name="predust_uuid" value="{{ request('predust_uuid') }}">
                            @endif
                            @if(request('battering_uuid'))
                                <input type="hidden" name="battering_uuid" value="{{ request('battering_uuid') }}">
                            @endif
                            @if(request('breader_uuid'))
                                <input type="hidden" name="breader_uuid" value="{{ request('breader_uuid') }}">
                            @endif
                            
                            @if(isset($penggorenganData) || isset($predustData) || isset($batteringData) || isset($breaderData))
                            <!-- Card untuk menampilkan informasi relasi -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="card card-info card-outline">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="fas fa-link"></i> Informasi Relasi
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            @if(isset($penggorenganData) && $penggorenganData)
                                                <div class="alert alert-info">
                                                    <h6><i class="fas fa-fire"></i> Data Penggorengan:</h6>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <ul class="mb-0">
                                                                <li><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($penggorenganData->tanggal)->format('d-m-Y H:i:s') }}</li>
                                                                <li><strong>Produk:</strong> {{ $penggorenganData->produk->nama_produk ?? '-' }}</li>
                                                             
                                                            </ul>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <ul class="mb-0">
                                                                <li><strong>Suhu:</strong> {{ $penggorenganData->suhu_penggorengan ?? '-' }}°C</li>
                                                                <li><strong>Waktu:</strong> {{ $penggorenganData->waktu_penggorengan ?? '-' }} detik</li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if(isset($predustData) && $predustData)
                                                <div class="alert alert-warning">
                                                    <h6><i class="fas fa-layer-group"></i> Data Predust:</h6>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <ul class="mb-0">
                                                                <li><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($predustData->tanggal)->format('d-m-Y H:i:s') }}</li>
                                                                <li><strong>Produk:</strong> {{ $predustData->produk->nama_produk ?? '-' }}</li>
                                                               
                                                            </ul>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <ul class="mb-0">
                                                                <li><strong>Jenis Predust:</strong> {{ $predustData->jenisPredust->jenis_predust ?? '-' }}</li>
                                                                <li><strong>Berat:</strong> {{ $predustData->berat_predust ?? '-' }} kg</li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if(isset($batteringData) && $batteringData)
                                                <div class="alert alert-success">
                                                    <h6><i class="fas fa-tint"></i> Data Battering:</h6>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <ul class="mb-0">
                                                                <li><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($batteringData->tanggal)->format('d-m-Y H:i:s') }}</li>
                                                                <li><strong>Produk:</strong> {{ $batteringData->produk->nama_produk ?? '-' }}</li>
                                                                <li><strong>Shift:</strong> {{ $batteringData->shift->shift ?? '-' }}</li>
                                                            </ul>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <ul class="mb-0">
                                                                <li><strong>Berat Adonan:</strong> {{ $batteringData->berat_adonan ?? '-' }} kg</li>
                                                                <li><strong>Suhu Adonan:</strong> {{ $batteringData->suhu_adonan ?? '-' }}°C</li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if(isset($breaderData) && $breaderData)
                                                <div class="alert alert-primary">
                                                    <h6><i class="fas fa-bread-slice"></i> Data Breader:</h6>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <ul class="mb-0">
                                                                <li><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($breaderData->tanggal)->format('d-m-Y H:i:s') }}</li>
                                                                <li><strong>Produk:</strong> {{ $breaderData->produk->nama_produk ?? '-' }}</li>
                                                            </ul>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <ul class="mb-0">
                                                                <li><strong>Jenis Breader:</strong> {{ $breaderData->jenisBreader->jenis_breader ?? '-' }}</li>
                                                                <li><strong>Berat:</strong> {{ $breaderData->berat_breader ?? '-' }} kg</li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
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
                                                    <input type="text" class="form-control" name="tanggal" value="{{ old('tanggal', \Carbon\Carbon::now('Asia/Jakarta')->format('d-m-Y H:i:s')) }}" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="jam" class="col-sm-4 col-form-label">
                                                    <i class="fas fa-clock"></i> Jam <span class="text-danger">*</span>
                                                </label>
                                                <div class="col-sm-8">
                                                    <input type="time" class="form-control @error('jam') is-invalid @enderror" name="jam" value="{{ old('jam', \Carbon\Carbon::now('Asia/Jakarta')->format('H:i')) }}" required>
                                                    @error('jam')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
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
                                                <label for="id_produk" class="col-sm-4 col-form-label">
                                                    <i class="fas fa-box"></i> Produk
                                                </label>
                                                <div class="col-sm-8">
                                                <select class="form-control" id="id_produk_f4_tab" name="id_produk" required>
                                                    <option value="">Pilih Produk</option>
                                                    @foreach ($products as $product)
                                                        <option value="{{ $product->id }}">{{ $product->nama_produk }}</option>
                                                    @endforeach
                                                </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="id_suhu_frayer" class="col-sm-4 col-form-label">
                                                    <i class="fas fa-thermometer-half"></i> Standart Suhu Frayer
                                                </label>
                                                <div class="col-sm-8">
                                                <select class="form-control" id="id_suhu_frayer_f4_tab" name="id_suhu_frayer" required>
                                                    <option value="">Pilih Suhu Frayer</option>
                                                </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="aktual_suhu_penggorengan" class="col-sm-4 col-form-label">
                                                    <i class="fas fa-thermometer-half"></i> Aktual Suhu Frayer
                                                </label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="aktual_suhu_penggorengan" name="aktual_suhu_penggorengan" placeholder="Masukkan suhu aktual (°C)">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="id_waktu_penggorengan" class="col-sm-4 col-form-label">
                                                    <i class="fas fa-stopwatch"></i>Standart Waktu Penggorengan
                                                </label>
                                                <div class="col-sm-8">
                                                <select class="form-control" id="id_waktu_penggorengan_f4_tab" name="id_waktu_penggorengan" required>
                                                    <option value="">Pilih Waktu Penggorengan</option>
                                                </select>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="aktual_penggorengan" class="col-sm-4 col-form-label">
                                                    <i class="fas fa-chart-line"></i> Aktual Penggorengan
                                                </label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="aktual_penggorengan_f4" name="aktual_penggorengan" required>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="tpm_minyak" class="col-sm-4 col-form-label">
                                                    <i class="fas fa-tint"></i> TPM Minyak
                                                </label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="tpm_minyak_f4" name="tpm_minyak" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Simpan Data
                                        </button>
                                        <a href="{{ route('proses-frayer.index') }}" class="btn btn-secondary ml-2">
                                            <i class="fas fa-arrow-left"></i> Kembali
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- line 4 -->
                    <div class="tab-pane" id="frayer_5">
                        <h4><b>Frayer 5</b></h4>
                        <form class="form-horizontal mb-4" method="POST" action="{{ route('frayer-5.store') }}{{ request('penggorengan_uuid') || request('predust_uuid') || request('battering_uuid') || request('breader_uuid') ? '?' : '' }}{{ request('penggorengan_uuid') ? 'penggorengan_uuid=' . request('penggorengan_uuid') : '' }}{{ (request('penggorengan_uuid') && request('predust_uuid')) ? '&' : '' }}{{ request('predust_uuid') ? 'predust_uuid=' . request('predust_uuid') : '' }}{{ ((request('penggorengan_uuid') || request('predust_uuid')) && request('battering_uuid')) ? '&' : '' }}{{ request('battering_uuid') ? 'battering_uuid=' . request('battering_uuid') : '' }}{{ ((request('penggorengan_uuid') || request('predust_uuid') || request('battering_uuid')) && request('breader_uuid')) ? '&' : '' }}{{ request('breader_uuid') ? 'breader_uuid=' . request('breader_uuid') : '' }}">
                            @csrf
                            <!-- Hidden input untuk UUID relasi -->
                            @if(request('penggorengan_uuid'))
                                <input type="hidden" name="penggorengan_uuid" value="{{ request('penggorengan_uuid') }}">
                            @endif
                            @if(request('predust_uuid'))
                                <input type="hidden" name="predust_uuid" value="{{ request('predust_uuid') }}">
                            @endif
                            @if(request('battering_uuid'))
                                <input type="hidden" name="battering_uuid" value="{{ request('battering_uuid') }}">
                            @endif
                            @if(request('breader_uuid'))
                                <input type="hidden" name="breader_uuid" value="{{ request('breader_uuid') }}">
                            @endif
                            
                            @if(isset($penggorenganData) || isset($predustData) || isset($batteringData) || isset($breaderData))
                            <!-- Card untuk menampilkan informasi relasi -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="card card-info card-outline">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="fas fa-link"></i> Informasi Relasi
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            @if(isset($penggorenganData) && $penggorenganData)
                                                <div class="alert alert-info">
                                                    <h6><i class="fas fa-fire"></i> Data Penggorengan:</h6>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <ul class="mb-0">
                                                                <li><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($penggorenganData->tanggal)->format('d-m-Y H:i:s') }}</li>
                                                                <li><strong>Produk:</strong> {{ $penggorenganData->produk->nama_produk ?? '-' }}</li>
                                                                <li><strong>Shift:</strong> {{ $penggorenganData->shift->shift ?? '-' }}</li>
                                                            </ul>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <ul class="mb-0">
                                                                <li><strong>Suhu:</strong> {{ $penggorenganData->suhu_penggorengan ?? '-' }}°C</li>
                                                                <li><strong>Waktu:</strong> {{ $penggorenganData->waktu_penggorengan ?? '-' }} detik</li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if(isset($predustData) && $predustData)
                                                <div class="alert alert-warning">
                                                    <h6><i class="fas fa-layer-group"></i> Data Predust:</h6>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <ul class="mb-0">
                                                                <li><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($predustData->tanggal)->format('d-m-Y H:i:s') }}</li>
                                                                <li><strong>Produk:</strong> {{ $predustData->produk->nama_produk ?? '-' }}</li>
           
                                                            </ul>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <ul class="mb-0">
                                                                <li><strong>Jenis Predust:</strong> {{ $predustData->jenisPredust->jenis_predust ?? '-' }}</li>
                                                                <li><strong>Berat:</strong> {{ $predustData->berat_predust ?? '-' }} kg</li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if(isset($batteringData) && $batteringData)
                                                <div class="alert alert-success">
                                                    <h6><i class="fas fa-tint"></i> Data Battering:</h6>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <ul class="mb-0">
                                                                <li><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($batteringData->tanggal)->format('d-m-Y H:i:s') }}</li>
                                                                <li><strong>Produk:</strong> {{ $batteringData->produk->nama_produk ?? '-' }}</li>
                                                                <li><strong>Shift:</strong> {{ $batteringData->shift->shift ?? '-' }}</li>
                                                            </ul>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <ul class="mb-0">
                                                                <li><strong>Berat Adonan:</strong> {{ $batteringData->berat_adonan ?? '-' }} kg</li>
                                                                <li><strong>Suhu Adonan:</strong> {{ $batteringData->suhu_adonan ?? '-' }}°C</li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if(isset($breaderData) && $breaderData)
                                                <div class="alert alert-primary">
                                                    <h6><i class="fas fa-bread-slice"></i> Data Breader:</h6>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <ul class="mb-0">
                                                                <li><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($breaderData->tanggal)->format('d-m-Y H:i:s') }}</li>
                                                                <li><strong>Produk:</strong> {{ $breaderData->produk->nama_produk ?? '-' }}</li>
                                                            </ul>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <ul class="mb-0">
                                                                <li><strong>Jenis Breader:</strong> {{ $breaderData->jenisBreader->jenis_breader ?? '-' }}</li>
                                                                <li><strong>Berat:</strong> {{ $breaderData->berat_breader ?? '-' }} kg</li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
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
                                                    <input type="text" class="form-control" name="tanggal" value="{{ old('tanggal', \Carbon\Carbon::now('Asia/Jakarta')->format('d-m-Y H:i:s')) }}" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="jam" class="col-sm-4 col-form-label">
                                                    <i class="fas fa-clock"></i> Jam <span class="text-danger">*</span>
                                                </label>
                                                <div class="col-sm-8">
                                                    <input type="time" class="form-control @error('jam') is-invalid @enderror" name="jam" value="{{ old('jam', \Carbon\Carbon::now('Asia/Jakarta')->format('H:i')) }}" required>
                                                    @error('jam')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
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
                                                <label for="id_produk" class="col-sm-4 col-form-label">
                                                    <i class="fas fa-box"></i> Produk
                                                </label>
                                                <div class="col-sm-8">
                                                <select class="form-control" id="id_produk_f5_tab" name="id_produk" required>
                                                    <option value="">Pilih Produk</option>
                                                    @foreach ($products as $product)
                                                        <option value="{{ $product->id }}">{{ $product->nama_produk }}</option>
                                                    @endforeach
                                                </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="id_suhu_frayer" class="col-sm-4 col-form-label">
                                                    <i class="fas fa-thermometer-half"></i> Standard Suhu Frayer
                                                </label>
                                                <div class="col-sm-8">
                                                <select class="form-control" id="id_suhu_frayer_f5_tab" name="id_suhu_frayer" required>
                                                    <option value="">Pilih Suhu Frayer</option>
                                                </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="aktual_suhu_penggorengan" class="col-sm-4 col-form-label">
                                                    <i class="fas fa-thermometer-half"></i> Aktual Suhu Frayer
                                                </label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="aktual_suhu_penggorengan" name="aktual_suhu_penggorengan" placeholder="Masukkan suhu aktual (°C)">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="id_waktu_penggorengan" class="col-sm-4 col-form-label">
                                                    <i class="fas fa-stopwatch"></i>Standart Waktu Penggorengan
                                                </label>
                                                <div class="col-sm-8">
                                                <select class="form-control" id="id_waktu_penggorengan_f5_tab" name="id_waktu_penggorengan" required>
                                                    <option value="">Pilih Waktu Penggorengan</option>
                                                </select>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="aktual_penggorengan" class="col-sm-4 col-form-label">
                                                    <i class="fas fa-chart-line"></i> Aktual Penggorengan
                                                </label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="aktual_penggorengan_f5" name="aktual_penggorengan" required>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="tpm_minyak" class="col-sm-4 col-form-label">
                                                    <i class="fas fa-tint"></i> TPM Minyak
                                                </label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="tpm_minyak_f5" name="tpm_minyak" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Simpan Data
                                        </button>
                                        <a href="{{ route('proses-frayer.index') }}" class="btn btn-secondary ml-2">
                                            <i class="fas fa-arrow-left"></i> Kembali
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
@endsection