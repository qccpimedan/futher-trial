@extends('layouts.app')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h1 class="m-0">Edit Data Hasil Penggorengan</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('hasil-penggorengan.index') }}">Hasil Penggorengan</a></li>
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
                                    <i class="fas fa-edit"></i> Form Edit Hasil Penggorengan
                                </h3>
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form class="form-horizontal mb-4" method="POST" action="{{ route('hasil-penggorengan.update', ['uuid' => $hasilPenggorengan->uuid]) }}">
                                @csrf
                                @method('PUT')
                                
                                @if($hasilPenggorengan->frayer_uuid || $hasilPenggorengan->breader_uuid || $hasilPenggorengan->battering_uuid || $hasilPenggorengan->predust_uuid || $hasilPenggorengan->penggorengan_uuid)
                                    @if($hasilPenggorengan->frayer_uuid)
                                        <input type="hidden" name="frayer_uuid" value="{{ $hasilPenggorengan->frayer_uuid }}">
                                    @endif
                                    @if($hasilPenggorengan->breader_uuid)
                                        <input type="hidden" name="breader_uuid" value="{{ $hasilPenggorengan->breader_uuid }}">
                                    @endif
                                    @if($hasilPenggorengan->battering_uuid)
                                        <input type="hidden" name="battering_uuid" value="{{ $hasilPenggorengan->battering_uuid }}">
                                    @endif
                                    @if($hasilPenggorengan->predust_uuid)
                                        <input type="hidden" name="predust_uuid" value="{{ $hasilPenggorengan->predust_uuid }}">
                                    @endif
                                    @if($hasilPenggorengan->penggorengan_uuid)
                                        <input type="hidden" name="penggorengan_uuid" value="{{ $hasilPenggorengan->penggorengan_uuid }}">
                                    @endif
                                @endif
                                
                                @if($hasilPenggorengan->frayer_uuid || $hasilPenggorengan->breader_uuid || $hasilPenggorengan->battering_uuid || $hasilPenggorengan->predust_uuid || $hasilPenggorengan->penggorengan_uuid)
                                <div class="card-body">
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h5><i class="fas fa-project-diagram"></i> Data Proses Terkait</h5>
                                            <div class="row">
                                                @if($hasilPenggorengan->penggorengan)
                                                <div class="col-md-3 col-sm-6">
                                                    <div class="info-box bg-info">
                                                        <span class="info-box-icon"><i class="fas fa-fire"></i></span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">Penggorengan</span>
                                                            <span class="info-box-number">{{ $hasilPenggorengan->penggorengan->kode_proses ?? '-' }}</span>
                                                            <span class="progress-description">
                                                                {{ $hasilPenggorengan->penggorengan->produk->nama_produk ?? '' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                @if($hasilPenggorengan->predust)
                                                <div class="col-md-3 col-sm-6">
                                                    <div class="info-box bg-warning">
                                                        <span class="info-box-icon"><i class="fas fa-powder"></i></span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">Predust</span>
                                                            <span class="info-box-number">{{ $hasilPenggorengan->predust->kode_proses ?? '-' }}</span>
                                                            <span class="progress-description">
                                                                {{ $hasilPenggorengan->predust->produk->nama_produk ?? '' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                @if($hasilPenggorengan->battering)
                                                <div class="col-md-3 col-sm-6">
                                                    <div class="info-box bg-success">
                                                        <span class="info-box-icon"><i class="fas fa-tint"></i></span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">Battering</span>
                                                            <span class="info-box-number">{{ $hasilPenggorengan->battering->kode_proses ?? '-' }}</span>
                                                            <span class="progress-description">
                                                                {{ $hasilPenggorengan->battering->produk->nama_produk ?? '' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                @if($hasilPenggorengan->breader)
                                                <div class="col-md-3 col-sm-6">
                                                    <div class="info-box bg-primary">
                                                        <span class="info-box-icon"><i class="fas fa-bread-slice"></i></span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">Breader</span>
                                                            <span class="info-box-number">{{ $hasilPenggorengan->breader->kode_proses ?? '-' }}</span>
                                                            <span class="progress-description">
                                                                {{ $hasilPenggorengan->breader->produk->nama_produk ?? '' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                @if($hasilPenggorengan->frayer)
                                                <div class="col-md-3 col-sm-6">
                                                    <div class="info-box bg-danger">
                                                        <span class="info-box-icon"><i class="fas fa-fire"></i></span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">Frayer</span>
                                                            <span class="info-box-number">
                                                                {{ $hasilPenggorengan->frayer->kode_proses ?? '-' }}
                                                            </span>
                                                            <span class="progress-description">
                                                                {{ $hasilPenggorengan->frayer->produk->nama_produk ?? '' }}
                                                                    }
                                                                @endphp
                                                            </span>
                                                            <span class="progress-description">
                                                                @if($frayer && $frayer->produk)
                                                                    {{ $frayer->produk->nama_produk }}
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <div class="card-body">
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
                                                            <input type="text" class="form-control" id="tanggal" name="tanggal" value="{{ \Carbon\Carbon::parse($hasilPenggorengan->tanggal)->format('d-m-Y H:i:s') }}" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="id_produk" class="col-sm-4 col-form-label">
                                                            <i class="fas fa-box"></i> Produk
                                                        </label>
                                                        <div class="col-sm-8">
                                                            <select class="form-control" id="id_produk" name="id_produk" required>
                                                                <option value="">Pilih Produk</option>
                                                                @foreach ($products as $product)
                                                                    <option value="{{ $product->id }}" {{ $hasilPenggorengan->id_produk == $product->id ? 'selected' : '' }}>
                                                                        {{ $product->nama_produk }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
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
                                                        <label for="id_std_suhu_pusat" class="col-sm-4 col-form-label">
                                                            <i class="fas fa-thermometer-half"></i> Std Suhu Pusat
                                                        </label>
                                                        <div class="col-sm-8">
                                                            <select class="form-control" id="id_std_suhu_pusat" name="id_std_suhu_pusat" required>
                                                                <option value="">Pilih Std Suhu Pusat</option>
                                                                @foreach ($stdSuhuPusats as $suhu)
                                                                    <option value="{{ $suhu->id }}" {{ $hasilPenggorengan->id_std_suhu_pusat == $suhu->id ? 'selected' : '' }}>
                                                                        {{ $suhu->std_suhu_pusat }}°C
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="aktual_suhu_pusat" class="col-sm-4 col-form-label">
                                                            <i class="fas fa-chart-line"></i> Aktual Suhu Pusat
                                                        </label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control" id="aktual_suhu_pusat" name="aktual_suhu_pusat" value="{{ $hasilPenggorengan->aktual_suhu_pusat }}" required>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="sensori" class="col-sm-4 col-form-label">
                                                            <i class="fas fa-eye"></i> Sensori
                                                        </label>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="sensori_kematangan" class="col-sm-4 col-form-label">Kematangan</label>
                                                        <div class="col-sm-8">
                                                            <select class="form-control" id="sensori_kematangan" name="sensori_kematangan">
                                                                <option value="">Pilih</option>
                                                                <option value="✔" {{ $hasilPenggorengan->sensori_kematangan == '✔' ? 'selected' : '' }}>✔</option>
                                                                <option value="✘" {{ $hasilPenggorengan->sensori_kematangan == '✘' ? 'selected' : '' }}>✘</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="sensori_kenampakan" class="col-sm-4 col-form-label">Kenampakan</label>
                                                        <div class="col-sm-8">
                                                            <select class="form-control" id="sensori_kenampakan" name="sensori_kenampakan">
                                                                <option value="">Pilih</option>
                                                                <option value="✔" {{ $hasilPenggorengan->sensori_kenampakan == '✔' ? 'selected' : '' }}>✔</option>
                                                                <option value="✘" {{ $hasilPenggorengan->sensori_kenampakan == '✘' ? 'selected' : '' }}>✘</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="sensori_warna" class="col-sm-4 col-form-label">Warna</label>
                                                        <div class="col-sm-8">
                                                            <select class="form-control" id="sensori_warna" name="sensori_warna">
                                                                <option value="">Pilih</option>
                                                                <option value="✔" {{ $hasilPenggorengan->sensori_warna == '✔' ? 'selected' : '' }}>✔</option>
                                                                <option value="✘" {{ $hasilPenggorengan->sensori_warna == '✘' ? 'selected' : '' }}>✘</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="sensori_rasa" class="col-sm-4 col-form-label">Rasa</label>
                                                        <div class="col-sm-8">
                                                            <select class="form-control" id="sensori_rasa" name="sensori_rasa">
                                                                <option value="">Pilih</option>
                                                                <option value="✔" {{ $hasilPenggorengan->sensori_rasa == '✔' ? 'selected' : '' }}>✔</option>
                                                                <option value="✘" {{ $hasilPenggorengan->sensori_rasa == '✘' ? 'selected' : '' }}>✘</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="sensori_bau" class="col-sm-4 col-form-label">Bau</label>
                                                        <div class="col-sm-8">
                                                            <select class="form-control" id="sensori_bau" name="sensori_bau">
                                                                <option value="">Pilih</option>
                                                                <option value="✔" {{ $hasilPenggorengan->sensori_bau == '✔' ? 'selected' : '' }}>✔</option>
                                                                <option value="✘" {{ $hasilPenggorengan->sensori_bau == '✘' ? 'selected' : '' }}>✘</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="sensori_tekstur" class="col-sm-4 col-form-label">Tekstur</label>
                                                        <div class="col-sm-8">
                                                            <select class="form-control" id="sensori_tekstur" name="sensori_tekstur">
                                                                <option value="">Pilih</option>
                                                                <option value="✔" {{ $hasilPenggorengan->sensori_tekstur == '✔' ? 'selected' : '' }}>✔</option>
                                                                <option value="✘" {{ $hasilPenggorengan->sensori_tekstur == '✘' ? 'selected' : '' }}>✘</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-12">
                                            <a href="{{ route('hasil-penggorengan.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                                            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Simpan Perubahan</button>
                                        </div>
                                    </div>
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