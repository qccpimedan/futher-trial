@extends('layouts.app')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h1 class="m-0">Tambah Data Proses Battering</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('proses-battering.index') }}">Proses Battering</a></li>
                            <li class="breadcrumb-item active">Tambah Data</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Form Input Proses Battering</h3>
                            </div>
                            @if ($errors->any())
                            <div class="alert alert-danger m-3">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                            
                            @if($penggorenganData)
                                <div class="alert alert-info">
                                    <h5><i class="fas fa-info-circle"></i> Data Penggorengan Sebelumnya</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Produk:</strong> {{ $penggorenganData->produk->nama_produk ?? '-' }}<br>
                                            <strong>Kode Produksi:</strong> {{ $penggorenganData->kode_produksi }}<br>
                                            <strong>Tanggal:</strong> {{ $penggorenganData->tanggal ? \Carbon\Carbon::parse($penggorenganData->tanggal)->format('d/m/Y H:i') : '-' }}
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Waktu Pemasakan:</strong> {{ $penggorenganData->waktu_pemasakan ?? '-' }}<br>
                                            <strong>No Of Strokes:</strong> {{ $penggorenganData->no_of_strokes }}<br>
                                            <strong>Hasil Pencetakan:</strong> {{ $penggorenganData->hasil_pencetakan }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($predustData)
                                <div class="alert alert-warning">
                                    <h5><i class="fas fa-info-circle"></i> Data Pembuatan Predust Sebelumnya</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Produk:</strong> {{ $predustData->produk->nama_produk ?? '-' }}<br>
                                            <strong>Jenis Predust:</strong> {{ $predustData->jenisPredust->jenis_predust ?? '-' }}<br>
                                            <strong>Kode Produksi:</strong> {{ $predustData->kode_produksi ?? '-' }}<br>
                                            <strong>Tanggal:</strong> {{ $predustData->tanggal ? \Carbon\Carbon::parse($predustData->tanggal)->format('d/m/Y H:i') : '-' }}
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Kondisi Predust:</strong> {{ $predustData->kondisi_predust ?? '-' }}<br>
                                            <strong>Hasil Pencetakan:</strong> {{ $predustData->hasil_pencetakan ?? '-' }}<br>
                                            @if($predustData->penggorengan)
                                                <strong>Dari Penggorengan:</strong> {{ $predustData->penggorengan->kode_produksi ?? '-' }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <form action="{{ route('proses-battering.store') }}" method="POST">
                                @csrf
                                @if($penggorenganData)
                                    <input type="hidden" name="penggorengan_uuid" value="{{ $penggorenganData->uuid }}">
                                @endif
                                @if($predustData)
                                    <input type="hidden" name="predust_uuid" value="{{ $predustData->uuid }}">
                                @endif
                                
                                <div class="card-body">   
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Tanggal <span class="text-danger">*</span></label>
                                        @php
                                            $userRole = auth()->user()->id_role ?? null;
                                            $showTime = in_array($userRole, [1, 2, 5]); // superadmin, admin, spv
                                            $displayFormat = $showTime ? 'd-m-Y H:i:s' : 'd-m-Y';
                                            $submitFormat = 'd-m-Y H:i:s'; // Always submit with H:i:s
                                            $now = \Carbon\Carbon::now('Asia/Jakarta');
                                            $displayValue = $now->format($displayFormat);
                                            $submitValue = $now->format($submitFormat);
                                        @endphp
                                        <div class="col-sm-9">
                                            <input type="hidden" name="tanggal" id="tanggal_hidden" 
                                                    value="{{ old('tanggal', $submitValue) }}">
                                            <input type="text" class="form-control" id="tanggal_display" 
                                                    value="{{ old('tanggal', $displayValue) }}" readonly required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Jam <span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="time" class="form-control @error('jam') is-invalid @enderror" name="jam" value="{{ old('jam', \Carbon\Carbon::now('Asia/Jakarta')->format('H:i')) }}" required>
                                            @error('jam')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Nama Produk <span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <select id="id_produk_select_better" name="id_produk" class="form-control" required>
                                                <option value="">Pilih Produk</option>
                                                @foreach($produks as $produk)
                                                    @php
                                                        $selected = old('id_produk') == $produk->id;
                                                        // Auto-select product if coming from Penggorengan or Predust
                                                        if (!old('id_produk')) {
                                                            if ($penggorenganData && $penggorenganData->id_produk == $produk->id) {
                                                                $selected = true;
                                                            } elseif ($predustData && $predustData->id_produk == $produk->id) {
                                                                $selected = true;
                                                            }
                                                        }
                                                    @endphp
                                                    <option value="{{ $produk->id }}" {{ $selected ? 'selected' : '' }}>{{ $produk->nama_produk }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Kode Produksi Better <span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" name="kode_produksi_better" class="form-control" 
                                                placeholder="Masukkan kode produksi better" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Jenis Better <span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <select id="id_better_select_better" name="id_jenis_better" class="form-control" required>
                                                <option value="">Pilih Jenis Better</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Hasil Better <span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <select class="form-control" name="hasil_better">
                                                <option value="✔">✔</option>
                                                <option value="✘">✘</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-1"></i> Simpan
                                    </button>
                                    <a href="{{ route('proses-battering.index') }}" class="ml-2 btn btn-secondary ml-2">
                                        <i class="fas fa-arrow-left mr-1"></i> Kembali
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