@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Tambah Data Proses Roasting Fan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href=""><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('proses-roasting-fan.index') }}">Proses Roasting Fan</a></li>
                        <li class="breadcrumb-item active">Tambah Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5><i class="icon fas fa-ban"></i> Terjadi Kesalahan!</h5>
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
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5><i class="icon fas fa-ban"></i> Error!</h5>
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if($inputRoastingData)
                <div class="alert alert-warning">
                    <h5><i class="icon fas fa-info"></i> Data Input Roasting Terkait</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($inputRoastingData->tanggal)->format('d-m-Y H:i:s') }}<br>
                            <strong>Produk:</strong> {{ $inputRoastingData->produk->nama_produk ?? '-' }}<br>
                            <strong>Kode Produksi:</strong> {{ $inputRoastingData->kode_produksi }}<br>
                        </div>
                        <div class="col-md-6">
                            <strong>Shift:</strong> {{ $inputRoastingData->shift->shift ?? '-' }}<br>
                            <strong>Berat Produk:</strong> {{ $inputRoastingData->berat_produk ?? '-' }} gram<br>
                            <!-- <strong>User:</strong> {{ $inputRoastingData->user->name ?? '-' }}<br> -->
                        </div>
                    </div>
                </div>
            @endif
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <h5><i class="icon fas fa-check"></i> Berhasil!</h5>
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            
            <div class="card card-primary card-tabs">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="custom-tabs-one-blok1-tab" data-toggle="pill" href="#custom-tabs-one-blok1" role="tab" aria-controls="custom-tabs-one-blok1" aria-selected="true">BLOK 1</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-one-tabContent">
                        {{-- TAB BLOK 1 --}}
                        <div class="tab-pane fade show active" id="custom-tabs-one-blok1" role="tabpanel" aria-labelledby="custom-tabs-one-blok1-tab">
                        <h4><b>BLOK 1</b></h4>
                            <form action="{{ route('proses-roasting-fan.store') }}" method="POST">
                                @csrf
                                
                                <!-- Hidden fields untuk UUID dari proses sebelumnya -->
                                @if($inputRoastingUuid)
                                    <input type="hidden" name="input_roasting_uuid" value="{{ $inputRoastingUuid }}">
                                @endif
                                <input type="hidden" name="frayer_uuid" value="{{ request('frayer_uuid') }}">
                                <input type="hidden" name="breader_uuid" value="{{ request('breader_uuid') }}">
                                <input type="hidden" name="battering_uuid" value="{{ request('battering_uuid') }}">
                                <input type="hidden" name="predust_uuid" value="{{ request('predust_uuid') }}">
                                <input type="hidden" name="penggorengan_uuid" value="{{ request('penggorengan_uuid') }}">
                                
                                @if($frayerData || $breaderData || $batteringData || $predustData || $penggorenganData)
                                <div class="card card-info card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-info-circle"></i> Informasi Proses Sebelumnya</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @if($penggorenganData)
                                            <div class="col-md-3">
                                                <strong>Penggorengan:</strong><br>
                                                <span class="badge badge-primary">{{ $penggorenganData->produk->nama_produk ?? '-' }}</span><br>
                                                <small>Shift: {{ $penggorenganData->shift->shift ?? '-' }}</small>
                                            </div>
                                            @endif
                                            @if($predustData)
                                            <div class="col-md-3">
                                                <strong>Predust:</strong><br>
                                                <span class="badge badge-secondary">{{ $predustData->produk->nama_produk ?? '-' }}</span><br>
                                                <small>Jenis: {{ $predustData->jenisPredust->jenis_predust ?? '-' }}</small>
                                            </div>
                                            @endif
                                            @if($batteringData)
                                            <div class="col-md-3">
                                                <strong>Battering:</strong><br>
                                                <span class="badge badge-success">{{ $batteringData->produk->nama_produk ?? '-' }}</span><br>
                                                <!-- <small>Shift: {{ $batteringData->shift->shift ?? '-' }}</small> -->
                                            </div>
                                            @endif
                                            @if($breaderData)
                                            <div class="col-md-3">
                                                <strong>Breader:</strong><br>
                                                <span class="badge badge-info">{{ $breaderData->produk->nama_produk ?? '-' }}</span><br>
                                            </div>
                                            @endif
                                            @if($frayerData)
                                            <div class="col-md-3">
                                                <strong>Frayer:</strong><br>
                                                <span class="badge badge-warning">{{ $frayerData->produk->nama_produk ?? '-' }}</span><br>
                                                <small>Aktual: {{ $frayerData->aktual_penggorengan ?? '-' }} detik</small>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endif
                                
                                <div class="card card-primary card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">Informasi Dasar</h3>
                                    </div>
                                    <div class="card-body">
                                        {{-- Existing form fields for Blok 1 --}}
                                        <div class="form-group row">
                                            <label for="tanggal" class="col-sm-2 col-form-label">Tanggal <span class="text-danger">*</span></label>
                                            <div class="col-sm-10">
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
                                        <div class="form-group row">
                                            <label for="jam" class="col-sm-2 col-form-label"><i class="fas fa-clock"></i> Jam <span class="text-danger">*</span></label>
                                            <div class="col-sm-10">
                                                <input type="time" class="form-control @error('jam') is-invalid @enderror" id="jam" name="jam" value="{{ old('jam', \Carbon\Carbon::now('Asia/Jakarta')->format('H:i')) }}" required>
                                                @error('jam')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="waktu_pemasakan" class="col-sm-2 col-form-label">Waktu Pemasakan</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control @error('waktu_pemasakan') is-invalid @enderror" id="waktu_pemasakan" name="waktu_pemasakan" value="{{ old('waktu_pemasakan') }}" placeholder="Contoh: 45 menit">
                                                @error('waktu_pemasakan')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="id_produk_1" class="col-sm-2 col-form-label">Produk <span class="text-danger">*</span></label>
                                            <div class="col-sm-10">
                                                <select class="form-control @error('id_produk') is-invalid @enderror product-selector" id="id_produk_1" name="id_produk" data-target-table="#product-details-table-1" required>
                                                    <option value="" selected disabled>-- Pilih Produk --</option>
                                                    @foreach($produks as $produk)
                                                        <option value="{{ $produk->id }}" {{ (old('id_produk') == $produk->id || ($frayerData && $frayerData->id_produk == $produk->id) || ($breaderData && $breaderData->id_produk == $produk->id) || ($batteringData && $batteringData->id_produk == $produk->id) || ($predustData && $predustData->id_produk == $produk->id) || ($penggorenganData && $penggorenganData->id_produk == $produk->id)) ? 'selected' : '' }}>
                                                            {{ $produk->nama_produk }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('id_produk')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card card-primary card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">Detail Proses Roasting Fan</h3>
                                    </div>
                                    <div class="card-body">
                                        <table id="product-details-table-1" class="table table-bordered">
                                            <thead>
                                                Pilih Produk Terlebih Dahulu Untuk Menampilkan Isi Tablenya
                                            </thead>
                                            <tbody>
                                                {{-- Rows will be populated by AJAX --}}
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Data</button>
                                        <a href="{{ route('proses-roasting-fan.index') }}" class="btn btn-secondary ml-2"><i class="fas fa-arrow-left"></i> Kembali</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection