@extends('layouts.app')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h1 class="m-0">Detail Proses Tumbling</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('proses-tumbling.index') }}">Proses Tumbling</a></li>
                            <li class="breadcrumb-item active">Detail Data</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-cogs"></i> Informasi Detail Proses Tumbling
                                </h3>
                                <!-- <div class="card-tools">
                                    <a href="{{ route('proses-tumbling.index') }}" class="btn btn-sm btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                </div> -->
                            </div>
                            <div class="card-body">
                                <!-- Informasi Dasar -->
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <h5 class="card-title border-bottom pb-2">
                                            <i class="fas fa-info-circle"></i> Informasi Dasar
                                        </h5>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Nama Produk</label>
                                            <p>{{ $prosesTumbling->produk->nama_produk ?? '-' }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Shift</label>
                                            <p>
                                                @if($prosesTumbling->shift_id == 1)
                                                    <span class="badge bg-primary">Shift 1</span>
                                                @elseif($prosesTumbling->shift_id == 2)
                                                    <span class="badge bg-success">Shift 2</span>
                                                @elseif($prosesTumbling->shift_id == 3)
                                                    <span class="badge bg-secondary">Shift 3</span>
                                                @else
                                                    <span class="badge bg-info">{{ $prosesTumbling->shift->shift ?? '-' }}</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Kode Produksi</label>
                                            <p>{{ $prosesTumbling->kode_produksi }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Tanggal</label>
                                            <p>{{ $prosesTumbling->tanggal ? \Carbon\Carbon::parse($prosesTumbling->tanggal)->format('d-m-Y H:i:s') : '-' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Jam</label>
                                            <p>{{ isset($prosesTumbling->jam) && $prosesTumbling->jam ? \Carbon\Carbon::parse($prosesTumbling->jam)->format('H:i') : '-' }}</p>
                                        </div>
                                    </div>
                                    <!-- <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Suhu</label>
                                            <p>{{ $prosesTumbling->suhu }}°C</p>
                                        </div>
                                    </div> -->
                                    <!-- <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Kondisi</label>
                                            <p>{{ $prosesTumbling->kondisi ?? '-' }}</p>
                                        </div>
                                    </div> -->
                                </div>

                                <!-- Parameter Tumbling -->
                                <div class="row mb-4 mt-4">
                                    <div class="col-md-12">
                                        <h5 class="card-title border-bottom pb-2">
                                            <i class="fas fa-sliders-h"></i> Parameter Tumbling
                                        </h5>
                                    </div>
                                </div>

                                @php
                                    $hasNonVakum = (
                                        !empty($prosesTumbling->dataTumbling->drum_on_non_vakum ?? null)
                                        || !empty($prosesTumbling->dataTumbling->drum_off_non_vakum ?? null)
                                        || !empty($prosesTumbling->dataTumbling->drum_speed_non_vakum ?? null)
                                        || !empty($prosesTumbling->dataTumbling->total_waktu_non_vakum ?? null)
                                        || !empty($prosesTumbling->dataTumbling->tekanan_non_vakum ?? null)
                                        || !empty($prosesTumbling->aktual_drum_on_non_vakum ?? null)
                                        || !empty($prosesTumbling->aktual_drum_off_non_vakum ?? null)
                                        || !empty($prosesTumbling->aktual_speed_non_vakum ?? null)
                                        || !empty($prosesTumbling->aktual_total_waktu_non_vakum ?? null)
                                        || !empty($prosesTumbling->aktual_tekanan_non_vakum ?? null)
                                        || !empty($prosesTumbling->waktu_mulai_tumbling_non_vakum ?? null)
                                        || !empty($prosesTumbling->waktu_selesai_tumbling_non_vakum ?? null)
                                    );
                                @endphp

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card card-outline card-warning">
                                            <div class="card-header">
                                                <h3 class="card-title">
                                                    <i class="fas fa-wind"></i> Tumbling Vakum
                                                </h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">Drum On (Std)</label>
                                                            <p>{{ $prosesTumbling->dataTumbling->drum_on ?? '-' }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">Aktual Drum On</label>
                                                            <p>{{ $prosesTumbling->aktual_drum_on ?? '-' }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">Drum Off (Std)</label>
                                                            <p>{{ $prosesTumbling->dataTumbling->drum_off ?? '-' }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">Aktual Drum Off</label>
                                                            <p>{{ $prosesTumbling->aktual_drum_off ?? '-' }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">Drum Speed (Std)</label>
                                                            <p>{{ $prosesTumbling->dataTumbling->drum_speed ?? '-' }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">Aktual Speed</label>
                                                            <p>{{ $prosesTumbling->aktual_speed ?? '-' }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">Total Waktu (Std)</label>
                                                            <p>{{ $prosesTumbling->dataTumbling->total_waktu ?? '-' }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">Aktual Total Waktu</label>
                                                            <p>{{ $prosesTumbling->aktual_total_waktu ?? '-' }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">Tekanan Vakum (Std)</label>
                                                            <p>{{ ($prosesTumbling->dataTumbling && $prosesTumbling->dataTumbling->tekanan_vakum) ? number_format((float)$prosesTumbling->dataTumbling->tekanan_vakum, 0) : '-' }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">Aktual Vakum</label>
                                                            <p>{{ $prosesTumbling->aktual_vakum ? number_format((float)$prosesTumbling->aktual_vakum, 0) : '-' }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">Waktu Mulai Tumbling</label>
                                                            <p>{{ $prosesTumbling->waktu_mulai_tumbling ? $prosesTumbling->waktu_mulai_tumbling . ' menit' : '-' }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">Waktu Selesai Tumbling</label>
                                                            <p>{{ $prosesTumbling->waktu_selesai_tumbling ? $prosesTumbling->waktu_selesai_tumbling . ' menit' : '-' }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if($hasNonVakum)
                                        <div class="col-md-6">
                                            <div class="card card-outline card-info">
                                                <div class="card-header">
                                                    <h3 class="card-title">
                                                        <i class="fas fa-wind"></i> Tumbling Non Vakum
                                                    </h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="font-weight-bold">Drum On (Std)</label>
                                                                <p>{{ $prosesTumbling->dataTumbling->drum_on_non_vakum ?? '-' }}</p>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="font-weight-bold">Aktual Drum On</label>
                                                                <p>{{ $prosesTumbling->aktual_drum_on_non_vakum ?? '-' }}</p>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="font-weight-bold">Drum Off (Std)</label>
                                                                <p>{{ $prosesTumbling->dataTumbling->drum_off_non_vakum ?? '-' }}</p>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="font-weight-bold">Aktual Drum Off</label>
                                                                <p>{{ $prosesTumbling->aktual_drum_off_non_vakum ?? '-' }}</p>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="font-weight-bold">Drum Speed (Std)</label>
                                                                <p>{{ $prosesTumbling->dataTumbling->drum_speed_non_vakum ?? '-' }}</p>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="font-weight-bold">Aktual Speed</label>
                                                                <p>{{ $prosesTumbling->aktual_speed_non_vakum ?? '-' }}</p>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="font-weight-bold">Total Waktu (Std)</label>
                                                                <p>{{ $prosesTumbling->dataTumbling->total_waktu_non_vakum ?? '-' }}</p>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="font-weight-bold">Aktual Total Waktu</label>
                                                                <p>{{ $prosesTumbling->aktual_total_waktu_non_vakum ?? '-' }}</p>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="font-weight-bold">Tekanan (Std)</label>
                                                                <p>{{ $prosesTumbling->dataTumbling->tekanan_non_vakum ?? '-' }}</p>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="font-weight-bold">Aktual Tekanan</label>
                                                                <p>{{ $prosesTumbling->aktual_tekanan_non_vakum ?? '-' }}</p>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="font-weight-bold">Waktu Mulai Tumbling</label>
                                                                <p>{{ $prosesTumbling->waktu_mulai_tumbling_non_vakum ? $prosesTumbling->waktu_mulai_tumbling_non_vakum . ' menit' : '-' }}</p>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="font-weight-bold">Waktu Selesai Tumbling</label>
                                                                <p>{{ $prosesTumbling->waktu_selesai_tumbling_non_vakum ? $prosesTumbling->waktu_selesai_tumbling_non_vakum . ' menit' : '-' }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Tombol Aksi -->
                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <!--
@if(auth()->user()->hasPermissionTo('edit-proses-tumbling')) <a href="{{ route('proses-tumbling.edit', ['uuid' => $prosesTumbling->uuid]) }}" class="btn btn-warning">
                                            <i class="fas fa-edit"></i> Edit Data
                                        </a> @endif
-->
                                        <a href="{{ route('proses-tumbling.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left"></i> Kembali
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection