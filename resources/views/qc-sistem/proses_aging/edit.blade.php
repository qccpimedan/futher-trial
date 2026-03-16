@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Proses Aging</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('proses-aging.index') }}">Proses Aging</a></li>
                        <li class="breadcrumb-item active">Edit</li>
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
                                <i class="fas fa-edit mr-2"></i>Form Edit Proses Aging
                            </h3>
                        </div>
                        
                        <form action="{{ route('proses-aging.update', $prosesAging->uuid) }}" method="POST" autocomplete="off">
                            @csrf
                            @method('PUT')

                            <div class="card-body">
                                {{-- Stepper Indikator Proses --}}
                                @include('components.stepper-tumbling', [
                                    'step' => 3,
                                    'bahanBakuUuid' => $prosesAging->prosesTumbling->bahan_baku_tumbling_uuid ?? null,
                                    'prosesTumblingUuid' => $prosesAging->proses_tumbling_uuid,
                                    'prosesAgingUuid' => $prosesAging->uuid
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
                                                @if($prosesAging->proses_tumbling_id)
                                                    <input type="hidden" name="tanggal" value="{{ old('tanggal', $prosesAging->tanggal) }}">
                                                @else
                                                    <div class="form-group">
                                                        <label for="tanggal"><i class="fas fa-calendar"></i> Tanggal <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                                            </div>
                                                            <input type="date" name="tanggal" id="tanggal"
                                                                class="form-control @error('tanggal') is-invalid @enderror"
                                                                value="{{ old('tanggal', $prosesAging->tanggal) }}" required>
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
                                                            value="{{ old('jam', $prosesAging->jam) }}" required>
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
                                                            <option value="{{ $produk->id }}" 
                                                                {{ old('id_produk', $prosesAging->id_produk) == $produk->id ? 'selected' : '' }}>
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
                                                                value="{{ old('waktu_mulai_aging', $prosesAging->waktu_mulai_aging) }}" 
                                                                placeholder="Masukkan waktu mulai" required>
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
                                                                value="{{ old('waktu_selesai_aging', $prosesAging->waktu_selesai_aging) }}" 
                                                                placeholder="Masukkan waktu selesai" required>
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
                                                                value="{{ old('suhu_produk', $prosesAging->suhu_produk) }}" 
                                                                placeholder="Masukkan suhu produk" required>
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
                                                                <option value="✓" {{ old('kondisi_produk', $prosesAging->kondisi_produk) == '✓' ? 'selected' : '' }}>✓ Ok</option>
                                                                <option value="✘" {{ old('kondisi_produk', $prosesAging->kondisi_produk) == '✘' ? 'selected' : '' }}>✘ Tidak Ok</option>
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
                                    <i class="fas fa-save"></i> Update
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