@extends('layouts.app')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h1 class="m-0">Data Hasil Penggorengan</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                            <li class="breadcrumb-item active">Hasil Penggorengan</li>
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
                                    <i class="fas fa-fire"></i> Data Hasil Penggorengan
                                </h3>
                                <!-- <div class="card-tools">
                                    @if(auth()->user()->hasPermissionTo('create-hasil-penggorengan'))
                                    <a href="{{ route('hasil-penggorengan.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Tambah Data
                                    </a>
                                @endif
</div> -->
                            </div>
                            <div class="card-body">
                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <i class="icon fas fa-check"></i> {{ session('success') }}
                                    </div>
                                @endif

                                @if(session('error'))
                                    <div class="alert alert-danger alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <i class="icon fas fa-ban"></i> {{ session('error') }}
                                    </div>
                                @endif

                                @if(count($hasilPenggorengan) || request('search'))
                                <div class="table-responsive">
                                    <!-- Form Pencarian Server-Side -->
                                    <div class="row mb-3 mt-3">
                                        <div class="col-md-4 offset-md-8">
                                            <form action="{{ route('hasil-penggorengan.index') }}" method="GET">
                                                <div class="input-group">
                                                    <input type="text" name="search" class="form-control" placeholder="Cari Nama Produk" value="{{ request('search') }}">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary" type="submit">
                                                            <i class="fas fa-search"></i> Cari
                                                        </button>
                                                        @if(request('search'))
                                                            <a href="{{ route('hasil-penggorengan.index') }}" class="btn btn-outline-danger" title="Clear Search">
                                                                <i class="fas fa-times"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    <table class="table text-center table-bordered table-striped table-hover" style="white-space: nowrap;">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>No</th>
                                                <th>Shift</th>
                                                <th>Tanggal</th>
                                                <th>Jam</th>
                                                <!-- <th>Plan</th> -->
                                                <th>Produk</th>
                                                <th>Frayer</th>
                                                <th>Std Suhu Pusat</th>
                                                <th>Aktual Suhu Pusat</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($hasilPenggorengan as $index => $item)
                                            <tr>
                                                <td>{{ $hasilPenggorengan->firstItem() + $index }}</td>
                                                <td>
                                                    @php
                                                        $shift = null;
                                                        if ($item->penggorengan && $item->penggorengan->shift) {
                                                            $shift = $item->penggorengan->shift;
                                                        } elseif ($item->shift) {
                                                            $shift = $item->shift;
                                                        }
                                                    @endphp

                                                    @if($shift && $shift->shift == 1)
                                                        <span class="badge bg-primary">Shift 1</span>
                                                    @elseif($shift && $shift->shift == 2)
                                                        <span class="badge bg-success">Shift 2</span>
                                                    @elseif($shift && $shift->shift == 3)
                                                        <span class="badge bg-secondary">Shift 3</span>
                                                    @else
                                                        <span class="badge bg-info">{{ $shift->shift ?? '-' }}</span>
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
                                                    {{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}
                                                </td>
                                                <!-- <td class="font-weight-medium">
                                                    @if($item->plan)
                                                        {{ $item->plan->nama_plan }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td> -->
                                                <td>
                                                    @if($item->produk)
                                                        {{ $item->produk->nama_produk }}
                                                        @if($item->penggorengan && $item->penggorengan->berat_produk)
                                                            ({{ $item->penggorengan->berat_produk }}gram)
                                                        @endif
                                                    @else
                                                        <span class="badge bg-secondary">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $frayerInfo = [];
                                                        if($item->frayer_uuid) {
                                                            // Cek apakah ini dari ProsesFrayer (Frayer 1)
                                                            $frayer1 = \App\Models\ProsesFrayer::where('uuid', $item->frayer_uuid)->first();
                                                            if($frayer1) {
                                                                $frayerInfo[] = '<span class="badge bg-primary">Fryer 1</span>';
                                                            }
                                                            
                                                            // Cek apakah ini dari Frayer3, 4, atau 5
                                                            $frayer3 = \App\Models\Frayer3::where('uuid', $item->frayer_uuid)->first();
                                                            $frayer4 = \App\Models\Frayer4::where('uuid', $item->frayer_uuid)->first();
                                                            $frayer5 = \App\Models\Frayer5::where('uuid', $item->frayer_uuid)->first();
                                                            
                                                            if($frayer3) $frayerInfo[] = '<span class="badge bg-info">Fryer 3</span>';
                                                            if($frayer4) $frayerInfo[] = '<span class="badge bg-warning">Fryer 4</span>';
                                                            if($frayer5) $frayerInfo[] = '<span class="badge bg-success">Fryer 5</span>';
                                                        }
                                                        
                                                        if($item->frayer2_uuid) {
                                                            $frayer2 = \App\Models\Frayer2::where('uuid', $item->frayer2_uuid)->first();
                                                            if($frayer2) {
                                                                $frayerInfo[] = '<span class="badge bg-secondary">Frayer 2</span>';
                                                            }
                                                        }
                                                    @endphp
                                                    @if(count($frayerInfo) > 0)
                                                        {!! implode(' ', $frayerInfo) !!}
                                                    @else
                                                        <span class="badge bg-light text-dark">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($item->stdSuhuPusat)
                                                        @php
                                                            $suhuArray = is_array($item->stdSuhuPusat->std_suhu_pusat) 
                                                                ? $item->stdSuhuPusat->std_suhu_pusat 
                                                                : json_decode($item->stdSuhuPusat->std_suhu_pusat, true) ?? [];
                                                            
                                                                // Detect fryer yang dipakai
                                                                $fryerNumber = null;

                                                                // Prioritas 1: Cek Frayer 2 dulu
                                                                if($item->frayer2_uuid) {
                                                                    $fryerNumber = 2;
                                                                }
                                                                // Prioritas 2: Cek Frayer 1/3/4/5
                                                                elseif($item->frayer_uuid) {
                                                                    $fryerNumber = 1; // Default
                                                                    
                                                                    // Cek apakah Frayer 3, 4, atau 5
                                                                    $frayer3 = \App\Models\Frayer3::where('uuid', $item->frayer_uuid)->first();
                                                                    $frayer4 = \App\Models\Frayer4::where('uuid', $item->frayer_uuid)->first();
                                                                    $frayer5 = \App\Models\Frayer5::where('uuid', $item->frayer_uuid)->first();
                                                                    
                                                                    if($frayer5) $fryerNumber = 5;
                                                                    elseif($frayer4) $fryerNumber = 4;
                                                                    elseif($frayer3) $fryerNumber = 3;
                                                                }

                                                                // Ambil suhu sesuai fryer yang dipakai
                                                                $displaySuhu = null;
                                                                if($fryerNumber && isset($suhuArray[$fryerNumber - 1])) {
                                                                    $displaySuhu = $suhuArray[$fryerNumber - 1];
                                                                }
                                                            
                                                            // Ambil suhu sesuai fryer yang dipakai
                                                            $displaySuhu = null;
                                                            if($fryerNumber && isset($suhuArray[$fryerNumber - 1])) {
                                                                $displaySuhu = $suhuArray[$fryerNumber - 1];
                                                            }
                                                        @endphp
                                                        
                                                        @if($displaySuhu)
                                                            <span class="badge badge-info">F{{ $fryerNumber }}: {{ $displaySuhu }}°C</span>
                                                        @else
                                                            <span class="badge bg-warning">Data tidak tersedia</span>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-secondary">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span>{{ $item->aktual_suhu_pusat }}°C</span>
                                                </td>
                                                <td>
                                                    <!-- <div class="btn-group" role="group"> -->
                                                        <!--
@if(auth()->user()->hasPermissionTo('edit-hasil-penggorengan')) <a href="{{ route('hasil-penggorengan.edit', ['uuid' => $item->uuid]) }}" 
                                                           class="btn btn-warning btn-sm" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        @endif
@if(auth()->user()->hasPermissionTo('view-hasil-penggorengan'))
<a href="{{ route('hasil-penggorengan.logs', ['uuid' => $item->uuid]) }}" 
                                                           class="btn btn-info btn-sm" title="Lihat Log Perubahan">
                                                            <i class="fas fa-history"></i>
                                                        </a>
                                                        @endif
@if(auth()->user()->hasPermissionTo('delete-hasil-penggorengan'))
<form action="{{ route('hasil-penggorengan.destroy', ['uuid' => $item->uuid]) }}" 
                                                              method="POST" style="display: inline;" 
                                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form> @endif
-->
                                                        <x-action-buttons :item="$item" route-prefix="hasil-penggorengan" :show-view="false" />
                                                        @php
                                                            $queryParams = [
                                                                'hasil_penggorengan_uuid' => $item->uuid,
                                                                'frayer_uuid' => $item->frayer_uuid,
                                                                'frayer2_uuid' => $item->frayer2_uuid,
                                                                'breader_uuid' => $item->breader_uuid,
                                                                'battering_uuid' => $item->battering_uuid,
                                                                'predust_uuid' => $item->predust_uuid,
                                                                'penggorengan_uuid' => $item->penggorengan_uuid
                                                            ];
                                                            $pembekuanExists = \App\Models\PembekuanIqfPenggorengan::where('hasil_penggorengan_uuid', $item->uuid)->exists();
                                                        @endphp
                                                        @if($pembekuanExists)
                                                            <button class="btn btn-sm btn-secondary" disabled title="Pembekuan IQF sudah ada">
                                                                <i class="fas fa-thumbs-up"></i>
                                                            </button>
                                                        @else
                                                            <a href="{{ route('pembekuan-iqf-penggorengan.create', $queryParams) }}" class="btn btn-sm btn-info" title="Tambah Pembekuan IQF">
                                                                <i class="fas fa-arrow-right"></i> Pembekuan IQF
                                                            </a>
                                                        @endif
                                                    <!-- </div> -->
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Menampilkan Navigasi Pagination -->
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $hasilPenggorengan->appends(request()->query())->links('pagination::bootstrap-4') }}
                                </div>
                                @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Tidak ada data hasil penggorengan yang tersedia.
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