@extends('layouts.app')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h1 class="m-0">Data Hasil Proses Roasting</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                            <li class="breadcrumb-item active">Hasil Proses Roasting</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-temperature-high"></i> Data Hasil Proses Roasting
                                </h3>
                                <div class="card-tools d-flex">
                                    <form action="{{ route('hasil-proses-roasting.index') }}" method="GET" class="mr-2">
                                        <div class="input-group input-group-sm" style="width: 250px;">
                                            <input type="text" name="search" class="form-control" placeholder="Cari Nama Produk atau Tanggal" value="{{ $search ?? '' }}">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-default">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                                @if($search)
                                                    <a href="{{ route('hasil-proses-roasting.index') }}" class="btn btn-default">
                                                        <i class="fas fa-times text-danger"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </form>
                                    <!--
                                    @if(auth()->user()->hasPermissionTo('create-hasil-proses-roasting')) <a href="{{ route('hasil-proses-roasting.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Tambah Data
                                    </a> @endif
-->
                                </div>
                            </div>
                            <div class="card-body">
                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <i class="icon fas fa-check"></i> {{ session('success') }}
                                    </div>
                                @endif

                                @if(count($hasilProsesRoasting))
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover text-center" style="white-space: nowrap;">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>No</th>
                                                <th>Shift</th>
                                                <th>Tanggal</th>
                                                <th>Jam</th>
                                                <th>Produk</th>
                                                <th>Alur Proses</th>
                                                <!-- <th>Std Suhu Pusat</th>
                                                <th>Aktual Suhu Pusat</th> -->
                                                <!-- <th>Sensori</th> -->
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($hasilProsesRoasting as $item)
                                            <tr>
                                                <td>{{ $hasilProsesRoasting->firstItem() + $loop->index }}</td>
                                                <td>
                                                    @if($item->shift_data && $item->shift_data->shift == 1)
                                                        <span class="badge bg-primary">Shift 1</span>
                                                    @elseif($item->shift_data && $item->shift_data->shift == 2)
                                                        <span class="badge bg-success">Shift 2</span>
                                                    @elseif($item->shift_data && $item->shift_data->shift == 3)
                                                        <span class="badge bg-secondary">Shift 3</span>
                                                    @else
                                                        <span class="badge bg-info">{{ $item->shift_data->shift ?? '-' }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge badge-secondary">
                                                        @php
                                                            $userRole = auth()->user()->id_role ?? null;
                                                            $showTime = in_array($userRole, [1, 2, 5]); // superadmin, admin, spv
                                                            $format = $showTime ? 'd-m-Y H:i:s' : 'd-m-Y';
                                                        @endphp
                                                        {{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format($format) : '-' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    {{ isset($item->jam) && $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}
                                                </td>
                                                <td>
                                                    @php
                                                        $produk = $item->produk;
                                                        $berat_produk = null;
                                                        
                                                        // KONDISI 1 & 2: Cek berdasarkan shift yang sama
                                                        if($item->shift_data) {
                                                            // KONDISI 1: Cari dari Penggorengan berdasarkan shift yang sama
                                                            $penggorengan = \App\Models\Penggorengan::where('shift_id', $item->shift_data->id)->first();
                                                            if($penggorengan && $penggorengan->berat_produk) {
                                                                $berat_produk = $penggorengan->berat_produk;
                                                            }
                                                            
                                                            // KONDISI 2: Jika tidak ada, cari dari Input Roasting
                                                            if(!$berat_produk) {
                                                                $inputRoasting = \App\Models\InputRoasting::where('shift_id', $item->shift_data->id)->first();
                                                                if($inputRoasting && $inputRoasting->berat_produk) {
                                                                    $berat_produk = $inputRoasting->berat_produk;
                                                                }
                                                            }
                                                        }
                                                    @endphp

                                                    {{ $produk ? $produk->nama_produk : 'N/A' }}
                                                    @if($berat_produk)
                                                        ({{ $berat_produk }}gram)
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        // Cek alur proses berdasarkan UUID yang tersedia
                                                        $hasInputRoasting = !empty($item->input_roasting_uuid);
                                                        $hasPenggorengan = !empty($item->penggorengan_uuid);
                                                        $hasFrayer = !empty($item->frayer_uuid);
                                                        $hasBreader = !empty($item->breader_uuid);
                                                        $hasBattering = !empty($item->battering_uuid);
                                                        $hasPredust = !empty($item->predust_uuid);
                                                    @endphp
                                                    
                                                    @if($hasInputRoasting)
                                                        <!-- Alur Input Roasting -->
                                                        <div class="d-flex align-items-center flex-wrap">
                                                            <span class="badge bg-primary px-2 py-1 me-1 mb-1" style="font-size: 0.7em;">
                                                                <i class="fas fa-fire me-1"></i>Input Roasting
                                                            </span>
                                                            <i class="fas fa-arrow-right text-muted me-1" style="font-size: 0.6em;"></i>
                                                            <span class="badge bg-info px-2 py-1 me-1 mb-1" style="font-size: 0.7em;">
                                                                <i class="fas fa-fan me-1"></i>Roasting Fan
                                                            </span>
                                                            <i class="fas fa-arrow-right text-muted me-1" style="font-size: 0.6em;"></i>
                                                            <span class="badge bg-success px-2 py-1 mb-1" style="font-size: 0.7em;">
                                                                <i class="fas fa-clipboard-check me-1"></i>Hasil Proses
                                                            </span>
                                                        </div>
                                                    @elseif($hasPenggorengan || $hasFrayer || $hasBreader || $hasBattering || $hasPredust)
                                                        <!-- Kondisi 1: Alur Penggorengan -->
                                                        <div class="d-flex align-items-center flex-wrap">
                                                            @if($hasPredust)
                                                                <span class="badge bg-secondary px-2 py-1 me-1 mb-1" style="font-size: 0.7em;">
                                                                    <i class="fas fa-layer-group me-1"></i>Predust
                                                                </span>
                                                                <i class="fas fa-arrow-right text-muted me-1" style="font-size: 0.6em;"></i>
                                                            @endif
                                                            @if($hasBattering)
                                                                <span class="badge bg-warning px-2 py-1 me-1 mb-1" style="font-size: 0.7em;">
                                                                    <i class="fas fa-tint me-1"></i>Battering
                                                                </span>
                                                                <i class="fas fa-arrow-right text-muted me-1" style="font-size: 0.6em;"></i>
                                                            @endif
                                                            @if($hasBreader)
                                                                <span class="badge bg-info px-2 py-1 me-1 mb-1" style="font-size: 0.7em;">
                                                                    <i class="fas fa-bread-slice me-1"></i>Breader
                                                                </span>
                                                                <i class="fas fa-arrow-right text-muted me-1" style="font-size: 0.6em;"></i>
                                                            @endif
                                                            @if($hasFrayer)
                                                                <span class="badge bg-primary px-2 py-1 me-1 mb-1" style="font-size: 0.7em;">
                                                                    <i class="fas fa-fire me-1"></i>Frayer
                                                                </span>
                                                                <i class="fas fa-arrow-right text-muted me-1" style="font-size: 0.6em;"></i>
                                                            @endif
                                                            @if($hasPenggorengan)
                                                                <span class="badge bg-danger px-2 py-1 me-1 mb-1" style="font-size: 0.7em;">
                                                                    <i class="fas fa-fire me-1"></i>Penggorengan
                                                                </span>
                                                                <i class="fas fa-arrow-right text-muted me-1" style="font-size: 0.6em;"></i>
                                                            @endif
                                                            <span class="badge bg-info px-2 py-1 me-1 mb-1" style="font-size: 0.7em;">
                                                                <i class="fas fa-fan me-1"></i>Roasting Fan
                                                            </span>
                                                            <i class="fas fa-arrow-right text-muted me-1" style="font-size: 0.6em;"></i>
                                                            <span class="badge bg-success px-2 py-1 mb-1" style="font-size: 0.7em;">
                                                                <i class="fas fa-clipboard-check me-1"></i>Hasil Proses
                                                            </span>
                                                        </div>
                                                        <small class="text-muted d-block mt-1">Kondisi 1 - Alur Penggorengan</small>
                                                    @else
                                                        <span class="badge bg-secondary px-2 py-1" style="font-size: 0.7em;">
                                                            <i class="fas fa-question me-1"></i>Tidak Diketahui
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <!-- <div class="btn-group" role="group">
@if(auth()->user()->hasPermissionTo('edit-hasil-proses-roasting'))
                                                        <a href="{{ route('hasil-proses-roasting.edit', ['uuid' => $item->uuid]) }}" 
                                                           class="btn btn-warning btn-sm" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        @endif
@if(auth()->user()->hasPermissionTo('view-hasil-proses-roasting'))
<a href="{{ route('hasil-proses-roasting.logs', ['uuid' => $item->uuid]) }}" 
                                                           class="btn btn-info btn-sm" title="Lihat Riwayat Perubahan">
                                                            <i class="fas fa-history"></i>
                                                        </a>
                                                        @endif
@if(auth()->user()->hasPermissionTo('delete-hasil-proses-roasting'))
<form action="{{ route('hasil-proses-roasting.destroy', ['uuid' => $item->uuid]) }}" 
                                                            method="POST" style="display: inline;" 
                                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
</div> -->
                                                        @if($item->pembekuanIqfRoastingCount == 0)
                                                            <a href="{{ route('pembekuan-iqf-roasting.create', [
                                                                'hasil_proses_roasting_uuid' => $item->uuid,
                                                                'proses_roasting_fan_uuid' => $item->proses_roasting_fan_uuid,
                                                                'input_roasting_uuid' => $item->input_roasting_uuid,
                                                                'bahan_baku_roasting_uuid' => $item->bahan_baku_roasting_uuid,
                                                                'frayer_uuid' => $item->frayer_uuid,
                                                                'breader_uuid' => $item->breader_uuid,
                                                                'battering_uuid' => $item->battering_uuid,
                                                                'predust_uuid' => $item->predust_uuid,
                                                                'penggorengan_uuid' => $item->penggorengan_uuid
                                                            ]) }}" class="btn btn-success btn-sm">
                                                                <i class="fas fa-arrow-right"></i> Lanjut Proses IQF
                                                            </a>
                                                        @else
                                                            <span class="btn btn-success btn-sm disabled" 
                                                                title="Berhasil Input">
                                                                <i class="fas fa-thumbs-up"></i>
                                                            </span>
                                                        @endif
                                                        <x-action-buttons :item="$item" route-prefix="hasil-proses-roasting"/>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $hasilProsesRoasting->appends(['search' => $search ?? ''])->links('pagination::bootstrap-4') }}
                                </div>
                                @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Tidak ada data hasil proses roasting yang tersedia.
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection