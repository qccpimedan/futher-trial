@extends('layouts.app')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h1 class="m-0">Tambah Parameter Tumbling</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('proses-tumbling.index') }}">Parameter Tumbling</a></li>
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
                                    <i class="fas fa-cogs"></i> Form Tambah Parameter Tumbling
                                </h3>
                            </div>
                            <form action="{{ route('proses-tumbling.store') }}{{ request('bahan_baku_uuid') || request('marinade_uuid') ? '?' : '' }}{{ request('bahan_baku_uuid') ? 'bahan_baku_uuid=' . request('bahan_baku_uuid') : '' }}{{ request('bahan_baku_uuid') && request('marinade_uuid') ? '&' : '' }}{{ request('marinade_uuid') ? 'marinade_uuid=' . request('marinade_uuid') : '' }}" method="POST" id="prosesTumblingForm">
                                @csrf
                                @if(request('bahan_baku_uuid'))
                                    <input type="hidden" name="bahan_baku_uuid" value="{{ request('bahan_baku_uuid') }}">
                                @endif
                                
                                @if(isset($bahanBakuTumbling))
                                    <div class="card card-info card-outline mb-3">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="fas fa-link"></i> Informasi Relasi
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            @if(isset($bahanBakuTumbling))
                                                <div class="alert alert-info">
                                                    <h6><i class="fas fa-seedling"></i> Data Bahan Baku Tumbling:</h6>
                                                    <ul class="mb-0">
                                                    <li><strong>Nama Produk:</strong> {{ $bahanBakuTumbling->produk->nama_produk ?? '-' }}</li>
                                                    <li><strong>Kode Produksi:</strong> {{ $bahanBakuTumbling->kode_produksi }}</li>
                                                    <!-- <li><strong>Berat:</strong> {{ $bahanBakuTumbling->berat }} kg</li> -->
                                                    <li><strong>Tanggal:</strong> {{ $bahanBakuTumbling->tanggal->format('d-m-Y H:i:s') }}</li>
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <div class="card-body">
                                    {{-- Stepper Indikator Proses --}}
                                    @include('components.stepper-tumbling', [
                                        'step' => 2,
                                        'bahanBakuUuid' => isset($bahanBakuTumbling) ? $bahanBakuTumbling->uuid : request('bahan_baku_uuid')
                                    ])

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
                                                    @if(isset($bahanBakuTumbling))
                                                        <input type="hidden" name="tanggal" value="{{ $bahanBakuTumbling->tanggal->format('d-m-Y H:i:s') }}">
                                                    @else
                                                        <div class="form-group">
                                                            <label for="tanggal"><i class="fas fa-calendar"></i> Tanggal</label>
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                                                </div>
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
                                                                   value="{{ old('jam', now()->format('H:i')) }}" required>
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
                                                    @if(isset($bahanBakuTumbling))
                                                        <input type="hidden" name="shift_id" value="{{ $bahanBakuTumbling->shift_id }}">
                                                    @else
                                                        <div class="form-group">
                                                            <label for="shift_id">
                                                                <i class="fas fa-clock"></i> Shift
                                                            </label>
                                                            <select name="shift_id" class="form-control" required>
                                                                <option value="">Pilih Shift</option>
                                                                @foreach($shifts as $shift)
                                                                    <option value="{{ $shift->id }}">{{ $shift->shift }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('shift_id')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    @endif
                                                    <div class="form-group">
                                                        <label for="id_produk">
                                                            <i class="fas fa-box"></i> Produk
                                                        </label>
                                                        <select class="form-control @error('id_produk') is-invalid @enderror" 
                                                                id="id_produk" name="id_produk" required>
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

                                                    <div class="form-group">
                                                        <label for="kode_produksi">
                                                            <i class="fas fa-barcode"></i> Kode Produksi
                                                        </label>
                                                        <input type="text" class="form-control @error('kode_produksi') is-invalid @enderror" 
                                                               id="kode_produksi" name="kode_produksi" 
                                                               value="{{ old('kode_produksi') }}" required>
                                                        @error('kode_produksi')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                    </div>

                                    <!-- Dynamic Tumbling Data Table -->
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div class="card card-outline card-success" id="tumblingDataCard" style="display: none;">
                                                <div class="card-header">
                                                    <h3 class="card-title">
                                                        <i class="fas fa-table"></i> Data Tumbling Berdasarkan Produk
                                                    </h3>
                                                </div>
                                                <div class="card-body">
                                                <div id="tumblingInputs"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="fas fa-save"></i> Simpan
                                    </button>
                                    <a href="{{ route('proses-tumbling.index') }}" class="btn btn-secondary ml-2">
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
