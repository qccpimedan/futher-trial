@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-sm-6">
                    <h1 class="m-0">Tambah Proses Aging</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('proses-aging.index') }}">Proses Aging</a></li>
                        <li class="breadcrumb-item active">Tambah</li>
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
                                <i class="fas fa-plus mr-2"></i>Form Tambah Proses Aging
                            </h3>
                        </div>
                        
                        <form action="{{ route('proses-aging.store') }}" method="POST" autocomplete="off">
                            @csrf
                                @if(request('proses_tumbling_id'))
                                    <input type="hidden" name="proses_tumbling_id" value="{{ request('proses_tumbling_id') }}">
                                @endif
                                @if(request('proses_tumbling_uuid'))
                                    <input type="hidden" name="proses_tumbling_uuid" value="{{ request('proses_tumbling_uuid') }}">
                                @endif
                                @if(isset($prosesTumbling))
                                    <div class="card card-info card-outline mb-3">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="fas fa-link"></i> Informasi Relasi
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="alert alert-info">
                                                <h6><i class="fas fa-cogs"></i> Data Proses Tumbling:</h6>
                                                <ul class="mb-0">
                                                    <li><strong>Nama Produk:</strong> {{ $prosesTumbling->produk->nama_produk ?? '-' }}</li>
                                                    <li><strong>Kode Produksi:</strong> {{ $prosesTumbling->kode_produksi }}</li>
                                                    <li><strong>Tanggal:</strong> {{ $prosesTumbling->tanggal ? \Carbon\Carbon::parse($prosesTumbling->tanggal)->format('d-m-Y H:i:s') : '-' }}</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            <div class="card-body">
                                {{-- Stepper Indikator Proses --}}
                                @include('components.stepper-tumbling', [
                                    'step' => 3,
                                    'bahanBakuUuid' => isset($prosesTumbling) ? $prosesTumbling->bahan_baku_tumbling_uuid : null,
                                    'prosesTumblingUuid' => isset($prosesTumbling) ? $prosesTumbling->uuid : request('proses_tumbling_uuid')
                                ])

                                @if ($errors->any())
                                    <div class="alert alert-danger rounded">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="row">
                                    <!-- Kolom Kanan - Detail Produksi -->
                                    <div class="col-md-6">
                                        <div class="card card-outline card-warning">
                                            <div class="card-header">
                                                <h3 class="card-title">
                                                    <i class="fas fa-industry"></i> Detail Produksi
                                                </h3>
                                            </div>
                                            <div class="card-body">
                                                @if(isset($prosesTumbling))
                                                    <input type="hidden" name="tanggal" value="{{ \Carbon\Carbon::parse($prosesTumbling->tanggal)->format('Y-m-d') }}">
                                                @else
                                                    <div class="form-group">
                                                        <label for="tanggal"><i class="fas fa-calendar"></i> Tanggal <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                                            </div>
                                                            <input type="date" name="tanggal" id="tanggal"
                                                                class="form-control @error('tanggal') is-invalid @enderror"
                                                                value="{{ old('tanggal', now()->format('Y-m-d')) }}" required>
                                                        </div>
                                                        @error('tanggal')
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                @endif
                                                <div class="form-group">
                                                    <label for="jam"><i class="fas fa-clock"></i> Jam <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="far fa-clock"></i></span>
                                                        </div>
                                                        <input type="time" name="jam" id="jam"
                                                            class="form-control @error('jam') is-invalid @enderror"
                                                            value="{{ old('jam') }}" required>
                                                    </div>
                                                    @error('jam')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Kolom Kiri - Informasi Umum -->
                                    <div class="col-md-6">
                                        <div class="card card-outline card-secondary">
                                            <div class="card-header">
                                                <h3 class="card-title">
                                                    <i class="fas fa-info-circle"></i> Informasi Umum
                                                </h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label for="id_produk">
                                                        <i class="fas fa-box"></i> Nama Produk <span class="text-danger">*</span>
                                                    </label>
                                                    <select name="id_produk" id="id_produk" class="form-control @error('id_produk') is-invalid @enderror" required>
                                                        <option value="">Pilih Produk</option>
                                                        @foreach($produks as $produk)
                                                            <option value="{{ $produk->id }}" {{ old('id_produk') == $produk->id ? 'selected' : '' }}>
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
                                </div>

                                <!-- Parameter Aging -->
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="card card-outline card-success">
                                            <div class="card-header">
                                                <h3 class="card-title">
                                                    <i class="fas fa-cogs"></i> Parameter Aging
                                                </h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="waktu_mulai_aging">
                                                                <i class="fas fa-play"></i> Waktu Mulai Aging <span class="text-danger">*</span>
                                                            </label>
                                                            <input type="text" name="waktu_mulai_aging" id="waktu_mulai_aging"
                                                                class="form-control @error('waktu_mulai_aging') is-invalid @enderror"
                                                                value="{{ old('waktu_mulai_aging') }}" placeholder="Masukkan waktu mulai" required>
                                                            @error('waktu_mulai_aging')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="waktu_selesai_aging">
                                                                <i class="fas fa-stop"></i> Waktu Selesai Aging <span class="text-danger">*</span>
                                                            </label>
                                                            <input type="text" name="waktu_selesai_aging" id="waktu_selesai_aging"
                                                                class="form-control @error('waktu_selesai_aging') is-invalid @enderror"
                                                                value="{{ old('waktu_selesai_aging') }}" placeholder="Masukkan waktu selesai" required>
                                                            @error('waktu_selesai_aging')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="suhu_produk">
                                                                <i class="fas fa-thermometer-half"></i> Suhu Produk <span class="text-danger">*</span>
                                                            </label>
                                                            <input type="text" name="suhu_produk" id="suhu_produk"
                                                                class="form-control @error('suhu_produk') is-invalid @enderror"
                                                                value="{{ old('suhu_produk') }}" placeholder="Masukkan suhu produk" required>
                                                            @error('suhu_produk')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="kondisi_produk">
                                                                <i class="fas fa-check-circle"></i> Kondisi Produk <span class="text-danger">*</span>
                                                            </label>
                                                            <select name="kondisi_produk" id="kondisi_produk" class="form-control @error('kondisi_produk') is-invalid @enderror" required>
                                                                <option value="">Pilih Kondisi</option>
                                                                <option value="✓" {{ old('kondisi_produk') == '✓' ? 'selected' : '' }}>✓ Ok</option>
                                                                <option value="✘" {{ old('kondisi_produk') == '✘' ? 'selected' : '' }}>✘ Tidak Ok</option>
                                                            </select>
                                                            @error('kondisi_produk')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                                <a href="{{ route('proses-aging.index') }}" class="btn btn-secondary ml-2">
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
@endsection