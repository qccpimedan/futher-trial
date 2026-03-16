@extends('layouts.app')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h1 class="m-0">Data Proses Fryer</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                            <li class="breadcrumb-item active">Proses Fryer</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-12">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Daftar Proses Fryer</h3>
                                <div class="card-tools">
                                    @if(auth()->user()->hasPermissionTo('create-proses-frayer')) <a href="{{ route('proses-frayer.create') }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus"></i> Tambah Data
                                    </a> @endif
                                </div>
                            </div>
                            <div class="card-body table-responsive">
                                <div class="row mb-3 mt-3">
                                    <div class="col-md-6">
                                        <form method="GET" action="{{ route('proses-frayer.index') }}">
                                            <div class="input-group input-group-sm" style="width: 300px;">
                                                <input type="text" class="form-control" name="search" placeholder="Cari nama produk" value="{{ $search ?? '' }}">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="submit">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                    @if(!empty($search))
                                                        <a class="btn btn-outline-danger" href="{{ route('proses-frayer.index', ['tab' => $activeTab ?? 'frayer1', 'per_page' => $perPage ?? 10]) }}">
                                                            <i class="fas fa-times"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                            <input type="hidden" name="per_page" value="{{ $perPage ?? 10 }}">
                                            <input type="hidden" name="tab" value="{{ $activeTab ?? 'frayer1' }}">
                                        </form>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="float-right">
                                            <form method="GET" action="{{ route('proses-frayer.index') }}">
                                                <select class="form-control form-control-sm" name="per_page" style="width: 80px;" onchange="this.form.submit()">
                                                    <option value="5" {{ ($perPage ?? 10) == 5 ? 'selected' : '' }}>5</option>
                                                    <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10</option>
                                                    <option value="25" {{ ($perPage ?? 10) == 25 ? 'selected' : '' }}>25</option>
                                                    <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50</option>
                                                    <option value="100" {{ ($perPage ?? 10) == 100 ? 'selected' : '' }}>100</option>
                                                </select>
                                                <input type="hidden" name="search" value="{{ $search ?? '' }}">
                                                <input type="hidden" name="tab" value="{{ $activeTab ?? 'frayer1' }}">
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            @if(!empty($search))
                                                Hasil pencarian: "<strong>{{ $search }}</strong>"
                                            @else
                                                Data Proses Fryer
                                            @endif
                                        </small>
                                    </div>
                                </div>
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link active" id="frayer1-tab" data-toggle="tab" href="#frayer1" role="tab" aria-controls="frayer1" aria-selected="true">Fryer 1</a>
                                    </li>
                                    <li class="nav-item btn-success" role="presentation">
                                        <a class="nav-link" id="frayer2-tab" data-toggle="tab" href="#frayer2" role="tab" aria-controls="frayer2" aria-selected="false">Fryer 2</a>
                                    </li>
                                    <li class="nav-item btn-success" role="presentation">
                                        <a class="nav-link" id="frayer3-tab" data-toggle="tab" href="#frayer3" role="tab" aria-controls="frayer3" aria-selected="false">Fryer 3</a>
                                    </li>
                                    <li class="nav-item btn-success" role="presentation">
                                        <a class="nav-link" id="frayer4-tab" data-toggle="tab" href="#frayer4" role="tab" aria-controls="frayer4" aria-selected="false">Fryer 4</a>
                                    </li>
                                    <li class="nav-item btn-success" role="presentation">
                                        <a class="nav-link" id="frayer5-tab" data-toggle="tab" href="#frayer5" role="tab" aria-controls="frayer5" aria-selected="false">Fryer 5</a>
                                    </li>
                                </ul>
                                <div class="tab-content" id="myTabContent">
                                    {{-- TAB FRAYER 1 --}}
                                    <div class="tab-pane fade show active" id="frayer1" role="tabpanel" aria-labelledby="frayer1-tab">
                                        <table  class="table table-bordered table-striped mt-3 text-center" style="white-space: nowrap;">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Shift</th>
                                                    <th>Tanggal</th>
                                                    <th>Jam</th>
                                                    <!-- <th>Plan</th> -->
                                                    <th>Produk</th>
                                                    <th>Standart Suhu Fryer</th>
                                                    <th>Aktual Suhu Fryer</th>
                                                    <th>Standart Waktu Penggorengan</th>
                                                    <th>Aktual Waktu Penggorengan</th>
                                                    <th>TPM Minyak</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($prosesFrayer as $item)
                                                <tr>
                                                    <td>{{ $prosesFrayer->firstItem() + $loop->index }}</td>
                                                    <td>
                                                        @if($item->penggorengan && $item->penggorengan->shift && $item->penggorengan->shift->shift == 1)
                                                            <span class="badge bg-primary">Shift 1</span>
                                                        @elseif($item->penggorengan && $item->penggorengan->shift && $item->penggorengan->shift->shift == 2)
                                                            <span class="badge bg-success">Shift 2</span>
                                                        @elseif($item->penggorengan && $item->penggorengan->shift && $item->penggorengan->shift->shift == 3)
                                                            <span class="badge bg-secondary">Shift 3</span>
                                                        @else
                                                            <span class="badge bg-info">{{ $item->penggorengan->shift->shift ?? '-' }}</span>
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
                                                    <td>{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</td>
                                                    <!-- <td>{{ $item->plan->nama_plan ?? '-' }}</td> -->
                                                    <td>{{ $item->produk->nama_produk ?? '-' }}
                                                        @if($item->penggorengan && $item->penggorengan->berat_produk)
                                                            ({{ $item->penggorengan->berat_produk }}gram)
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-primary">{{ $item->suhuFrayer->suhu_frayer ?? '-' }}&deg;C</span>
                                                    </td>
                                                    <td>{{ $item->aktual_suhu_penggorengan }} &deg;C</td>
                                                    <td>{{ $item->waktuPenggorengan->waktu_penggorengan ?? '-' }} detik</td>
                                                    <td>{{ $item->aktual_penggorengan }} detik</td>
                                                    <td>{{ $item->tpm_minyak }}</td>
                                                    <td class="text-center">
                                                        <!--
@if(auth()->user()->hasPermissionTo('edit-proses-frayer')) <a href="{{ route('proses-frayer.edit', $item->uuid) }}" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                                        @endif
@if(auth()->user()->hasPermissionTo('delete-proses-frayer'))
<form action="{{ route('proses-frayer.destroy', $item->uuid) }}" method="POST" style="display:inline-block;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')" title="Hapus"><i class="fas fa-trash"></i></button>
                                                        </form> @endif
-->
                                                        <x-action-buttons :item="$item" route-prefix="proses-frayer" :show-view="false" />

                                                        @php
                                                            $queryParams = [
                                                                'frayer_uuid' => $item->uuid,
                                                                'breader_uuid' => $item->breader_uuid ?? null,
                                                                'battering_uuid' => $item->battering_uuid ?? null,
                                                                'predust_uuid' => $item->predust_uuid ?? null,
                                                                'penggorengan_uuid' => $item->penggorengan_uuid ?? null
                                                            ];
                                                            $hasilPenggorenganExists = \App\Models\HasilPenggorengan::where('frayer_uuid', $item->uuid)->exists();
                                                        @endphp

                                                        @if($hasilPenggorenganExists)
                                                            <button class="btn btn-sm btn-secondary" disabled title="Hasil Penggorengan sudah ada">
                                                                <i class="fas fa-thumbs-up"></i>
                                                            </button>
                                                        @else
                                                            <a href="{{ route('hasil-penggorengan.create', $queryParams) }}" class="btn btn-sm btn-success" title="Tambah Hasil Penggorengan">
                                                                <i class="fas fa-arrow-right"></i> Hasil Penggorengan
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <div class="d-flex justify-content-center mt-3">
                                            {{ $prosesFrayer->appends(['search' => $search ?? '', 'per_page' => $perPage ?? '', 'tab' => 'frayer1', 'frayer2_page' => request('frayer2_page'), 'frayer3_page' => request('frayer3_page'), 'frayer4_page' => request('frayer4_page'), 'frayer5_page' => request('frayer5_page')])->links('pagination::bootstrap-4') }}
                                        </div>
                                    </div>

                                    {{-- TAB FRAYER 2 --}}
                                    <div class="tab-pane fade" id="frayer2" role="tabpanel" aria-labelledby="frayer2-tab">
                                        <table  class="table table-bordered table-striped mt-3 text-center" style="white-space: nowrap;">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Shift</th>
                                                    <th>Tanggal</th>
                                                    <th>Jam</th>
                                                    <!-- <th>Plan</th> -->
                                                    <th>Produk</th>
                                                    <th>Standart Suhu Fryer</th>
                                                    <th>Aktual Suhu Fryer</th>
                                                    <th>Standart Waktu Penggorengan</th>
                                                    <th>Aktual Waktu Penggorengan</th>
                                                    <th>TPM Minyak</th>
                                                    <th class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($frayer2 as $item)
                                                <tr>
                                                    <td>{{ $frayer2->firstItem() + $loop->index }}</td>
                                                    <td>
                                                        @php
                                                            $shift = null;
                                                            if ($item->penggorenganData && $item->penggorenganData->shift) {
                                                                $shift = $item->penggorenganData->shift;
                                                            } elseif ($item->penggorengan && $item->penggorengan->shift) {
                                                                $shift = $item->penggorengan->shift;
                                                            } elseif ($item->frayerData && $item->frayerData->penggorengan && $item->frayerData->penggorengan->shift) {
                                                                $shift = $item->frayerData->penggorengan->shift;
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
                                                    <td>{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</td>
                                                    <!-- <td>{{ $item->plan->nama_plan ?? '-' }}</td> -->
                                                    <td>{{ $item->produk->nama_produk ?? '-' }}
                                                        @if($item->penggorengan && $item->penggorengan->berat_produk)
                                                            ({{ $item->penggorengan->berat_produk }}gram)
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($item->suhuFrayer2)
                                                            {{ $item->suhuFrayer2->getRawOriginal('suhu_frayer_2') ?? $item->suhuFrayer2->suhu_frayer_2 ?? '-' }} &deg;C
                                                        @else
                                                            - &deg;C
                                                        @endif
                                                    </td>
                                                    <td>{{ $item->aktual_suhu_penggorengan }} &deg;C</td>
                                                    <td>
                                                        @if($item->waktuPenggorengan2)
                                                            {{ $item->waktuPenggorengan2->waktu_penggorengan_2 ?? '-' }} detik
                                                        @else
                                                            - detik
                                                        @endif
                                                      </td>
                                                    <td>{{ $item->aktual_penggorengan ?? '-' }} detik</td>
                                                    <td>{{ $item->tpm_minyak }}</td>
                                                    <td class="text-center">
                                                        <!--
@if(auth()->user()->hasPermissionTo('edit-frayer-2')) <a href="{{ route('frayer-2.edit', $item->uuid) }}" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                                        @endif
@if(auth()->user()->hasPermissionTo('delete-frayer-2'))
<form action="{{ route('frayer-2.destroy', $item->uuid) }}" method="POST" style="display:inline-block;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')" title="Hapus"><i class="fas fa-trash"></i></button>
                                                        </form> @endif
-->
                                                        <x-action-buttons :item="$item" route-prefix="frayer-2" :show-view="false" />
                                                        @php
                                                            $queryParams = [
                                                                'frayer_uuid' => $item->frayer_uuid ?? null,
                                                                'frayer2_uuid' => $item->uuid,
                                                                'breader_uuid' => $item->breader_uuid ?? null,
                                                                'battering_uuid' => $item->battering_uuid ?? null,
                                                                'predust_uuid' => $item->predust_uuid ?? null,
                                                                'penggorengan_uuid' => $item->penggorengan_uuid ?? null
                                                            ];
                                                            $hasilPenggorenganExists = \App\Models\HasilPenggorengan::where('frayer2_uuid', $item->uuid)->exists();
                                                        @endphp
                                                        @if($hasilPenggorenganExists)
                                                            <button class="btn btn-sm btn-secondary" disabled title="Hasil Penggorengan sudah ada">
                                                                <i class="fas fa-thumbs-up"></i>
                                                            </button>
                                                        @else
                                                            <a href="{{ route('hasil-penggorengan.create', $queryParams) }}" class="btn btn-sm btn-success" title="Tambah Hasil Penggorengan">
                                                                <i class="fas fa-arrow-right"></i> Hasil Penggorengan
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <div class="d-flex justify-content-center mt-3">
                                            {{ $frayer2->appends(['search' => $search ?? '', 'per_page' => $perPage ?? '', 'tab' => 'frayer2', 'frayer1_page' => request('frayer1_page'), 'frayer3_page' => request('frayer3_page'), 'frayer4_page' => request('frayer4_page'), 'frayer5_page' => request('frayer5_page')])->links('pagination::bootstrap-4') }}
                                        </div>
                                    </div>

                                    {{-- TAB FRAYER 3 --}}
                                    <div class="tab-pane fade" id="frayer3" role="tabpanel" aria-labelledby="frayer3-tab">
                                        <table  class="table table-bordered table-striped mt-3 text-center" style="white-space: nowrap;">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Shift</th>
                                                    <th>Tanggal</th>
                                                    <th>Jam</th>
                                                    <!-- <th>Plan</th> -->
                                                    <th>Produk</th>
                                                    <th>Standart Suhu Fryer</th>
                                                    <th>Aktual Suhu Fryer</th>
                                                    <th>Standart Waktu Penggorengan</th>
                                                    <th>Aktual Waktu Penggorengan</th>
                                                    <th>TPM Minyak</th>
                                                    <th class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($frayer3 as $item)
                                                <tr>
                                                    <td>{{ $frayer3->firstItem() + $loop->index }}</td>
                                                    <td>
                                                        @if($item->penggorengan && $item->penggorengan->shift && $item->penggorengan->shift->shift == 1)
                                                            <span class="badge bg-primary">Shift 1</span>
                                                        @elseif($item->penggorengan && $item->penggorengan->shift && $item->penggorengan->shift->shift == 2)
                                                            <span class="badge bg-success">Shift 2</span>
                                                        @elseif($item->penggorengan && $item->penggorengan->shift && $item->penggorengan->shift->shift == 3)
                                                            <span class="badge bg-secondary">Shift 3</span>
                                                        @else
                                                            <span class="badge bg-info">{{ $item->penggorengan->shift->shift ?? '-' }}</span>
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
                                                    <td>{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</td>
                                                    <!-- <td>{{ $item->plan->nama_plan ?? '-' }}</td> -->
                                                    <td>{{ $item->produk->nama_produk ?? '-' }}
                                                        @if($item->penggorengan && $item->penggorengan->berat_produk)
                                                            ({{ $item->penggorengan->berat_produk }}gram)
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-secondary">{{ $item->suhuFrayer->suhu_frayer_3 ?? '-' }}&deg;C</span>
                                                    </td>
                                                    <td>{{ $item->aktual_suhu_penggorengan }} &deg;C</td>
                                                    <td>{{ $item->waktuPenggorengan->waktu_penggorengan ?? '-' }} detik</td>
                                                    <td>{{ $item->aktual_penggorengan }} detik</td>
                                                    <td>{{ $item->tpm_minyak }}</td>
                                                    <td class="text-center">
                                                        <!--
@if(auth()->user()->hasPermissionTo('edit-frayer-3')) <a href="{{ route('frayer-3.edit', $item->uuid) }}" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                                        @endif
@if(auth()->user()->hasPermissionTo('delete-frayer-3'))
<form action="{{ route('frayer-3.destroy', $item->uuid) }}" method="POST" style="display:inline-block;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')" title="Hapus"><i class="fas fa-trash"></i></button>
                                                        </form> @endif
-->
                                                        <x-action-buttons :item="$item" route-prefix="frayer-3" :show-view="false" />
                                                        @php
                                                            $queryParams = [
                                                                'frayer_uuid' => $item->uuid,
                                                                'breader_uuid' => $item->breader_uuid ?? null,
                                                                'battering_uuid' => $item->battering_uuid ?? null,
                                                                'predust_uuid' => $item->predust_uuid ?? null,
                                                                'penggorengan_uuid' => $item->penggorengan_uuid ?? null
                                                            ];
                                                            $hasilPenggorenganExists3 = \App\Models\HasilPenggorengan::where('frayer_uuid', $item->uuid)->exists();
                                                            $prosesRoastingFanExists = \App\Models\ProsesRoastingFan::where('frayer_uuid', $item->uuid)->exists();
                                                        @endphp
                                                        @if($hasilPenggorenganExists3)
                                                            <button class="btn btn-sm btn-secondary" disabled title="Hasil Penggorengan sudah ada">
                                                                <i class="fas fa-thumbs-up"></i> Hasil Penggorengan
                                                            </button>
                                                        @else
                                                            <a href="{{ route('hasil-penggorengan.create', $queryParams) }}" class="btn btn-sm btn-success" title="Tambah Hasil Penggorengan">
                                                                <i class="fas fa-arrow-right"></i> Hasil Penggorengan
                                                            </a>
                                                        @endif
                                                        @php
                                                            $prosesRoastingFanExists = \App\Models\ProsesRoastingFan::where('frayer_uuid', $item->uuid)->exists();
                                                            $queryParams = [
                                                                'frayer_uuid' => $item->uuid,
                                                                'breader_uuid' => $item->breader_uuid ?? null,
                                                                'battering_uuid' => $item->battering_uuid ?? null,
                                                                'predust_uuid' => $item->predust_uuid ?? null,
                                                                'penggorengan_uuid' => $item->penggorengan_uuid ?? null
                                                            ];
                                                        @endphp
                                                        @if($prosesRoastingFanExists)
                                                            <button class="btn btn-sm btn-secondary" disabled title="Proses Roasting Fan sudah ada">
                                                                <i class="fas fa-thumbs-up"></i> Proses Roasting Fan
                                                            </button>
                                                        @else
                                                            <a href="{{ route('proses-roasting-fan.create', $queryParams) }}" class="btn btn-sm btn-info" title="Tambah Proses Roasting Fan">
                                                                <i class="fas fa-arrow-right"></i> Proses Roasting Fan
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <div class="d-flex justify-content-center mt-3">
                                            {{ $frayer3->appends(['search' => $search ?? '', 'per_page' => $perPage ?? '', 'tab' => 'frayer3', 'frayer1_page' => request('frayer1_page'), 'frayer2_page' => request('frayer2_page'), 'frayer4_page' => request('frayer4_page'), 'frayer5_page' => request('frayer5_page')])->links('pagination::bootstrap-4') }}
                                        </div>
                                    </div>
                                    {{-- TAB FRAYER 4 --}}
                                    <div class="tab-pane fade" id="frayer4" role="tabpanel" aria-labelledby="frayer4-tab">
                                        <table  class="table table-bordered table-striped mt-3 text-center" style="white-space: nowrap;">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Shift</th>
                                                    <th>Tanggal</th>
                                                    <th>Jam</th>
                                                    <th>Produk</th>
                                                    <th>Standart Suhu Fryer</th>
                                                    <th>Standart Aktual Suhu Fryer</th>
                                                    <th>Waktu Penggorengan</th>
                                                    <th>Aktual Waktu Penggorengan</th>
                                                    <th>TPM Minyak</th>
                                                    <th class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($frayer4 as $item)
                                                <tr>
                                                    <td>{{ $frayer4->firstItem() + $loop->index }}</td>
                                                    <td>
                                                        @if($item->penggorengan && $item->penggorengan->shift && $item->penggorengan->shift->shift == 1)
                                                            <span class="badge bg-primary">Shift 1</span>
                                                        @elseif($item->penggorengan && $item->penggorengan->shift && $item->penggorengan->shift->shift == 2)
                                                            <span class="badge bg-success">Shift 2</span>
                                                        @elseif($item->penggorengan && $item->penggorengan->shift && $item->penggorengan->shift->shift == 3)
                                                            <span class="badge bg-secondary">Shift 3</span>
                                                        @else
                                                            <span class="badge bg-info">{{ $item->penggorengan->shift->shift ?? '-' }}</span>
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
                                                    <td>{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</td>
                                                    <td>{{ $item->produk->nama_produk ?? '-' }}
                                                        @if($item->penggorengan && $item->penggorengan->berat_produk)
                                                            ({{ $item->penggorengan->berat_produk }}gram)
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info">{{ $item->suhuFrayer->suhu_frayer_4 ?? '-' }}&deg;C</span>
                                                    </td>
                                                    <td>{{ $item->aktual_suhu_penggorengan }} &deg;C</td>
                                                    <td>{{ $item->waktuPenggorengan->waktu_penggorengan ?? '-' }} detik</td>
                                                    <td>{{ $item->aktual_penggorengan }} detik</td>
                                                    <td>{{ $item->tpm_minyak }}</td>
                                                    <td class="text-center">
                                                        <!--
@if(auth()->user()->hasPermissionTo('edit-frayer-4')) <a href="{{ route('frayer-4.edit', $item->uuid) }}" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                                        @endif
@if(auth()->user()->hasPermissionTo('delete-frayer-4'))
<form action="{{ route('frayer-4.destroy', $item->uuid) }}" method="POST" style="display:inline-block;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')" title="Hapus"><i class="fas fa-trash"></i></button>
                                                        </form> @endif
-->
                                                        <x-action-buttons :item="$item" route-prefix="frayer-4" :show-view="false" />
                                                        @php
                                                            $queryParams = [
                                                                'frayer_uuid' => $item->uuid,
                                                                'breader_uuid' => $item->breader_uuid ?? null,
                                                                'battering_uuid' => $item->battering_uuid ?? null,
                                                                'predust_uuid' => $item->predust_uuid ?? null,
                                                                'penggorengan_uuid' => $item->penggorengan_uuid ?? null
                                                            ];
                                                            $hasilPenggorenganExists4 = \App\Models\HasilPenggorengan::where('frayer_uuid', $item->uuid)->exists();
                                                        @endphp
                                                        @if($hasilPenggorenganExists4)
                                                            <button class="btn btn-sm btn-secondary" disabled title="Hasil Penggorengan sudah ada">
                                                                <i class="fas fa-thumbs-up"></i> Hasil Penggorengan
                                                            </button>
                                                        @else
                                                            <a href="{{ route('hasil-penggorengan.create', $queryParams) }}" class="btn btn-sm btn-success" title="Tambah Hasil Penggorengan">
                                                                <i class="fas fa-arrow-right"></i> Hasil Penggorengan
                                                            </a>
                                                        @endif
                                                        <!-- Proses Roasting Fan -->
                                                        @php
                                                            $prosesRoastingFanExists = \App\Models\ProsesRoastingFan::where('frayer_uuid', $item->uuid)->exists();
                                                            $queryParams = [
                                                                'frayer_uuid' => $item->uuid,
                                                                'breader_uuid' => $item->breader_uuid ?? null,
                                                                'battering_uuid' => $item->battering_uuid ?? null,
                                                                'predust_uuid' => $item->predust_uuid ?? null,
                                                                'penggorengan_uuid' => $item->penggorengan_uuid ?? null
                                                            ];
                                                        @endphp
                                                        @if($prosesRoastingFanExists)
                                                            <button class="btn btn-sm btn-secondary" disabled title="Proses Roasting Fan sudah ada">
                                                                <i class="fas fa-thumbs-up"></i> Proses Roasting Fan
                                                            </button>
                                                        @else
                                                            <a href="{{ route('proses-roasting-fan.create', $queryParams) }}" class="btn btn-sm btn-info" title="Tambah Proses Roasting Fan">
                                                                <i class="fas fa-arrow-right"></i> Proses Roasting Fan
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <div class="d-flex justify-content-center mt-3">
                                            {{ $frayer4->appends(['search' => $search ?? '', 'per_page' => $perPage ?? '', 'tab' => 'frayer4', 'frayer1_page' => request('frayer1_page'), 'frayer2_page' => request('frayer2_page'), 'frayer3_page' => request('frayer3_page'), 'frayer5_page' => request('frayer5_page')])->links('pagination::bootstrap-4') }}
                                        </div>
                                    </div>
                                    {{-- TAB FRAYER 5 --}}
                                    <div class="tab-pane fade" id="frayer5" role="tabpanel" aria-labelledby="frayer5-tab">
                                        <table  class="table table-bordered table-striped mt-3 text-center" style="white-space: nowrap;">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Shift</th>
                                                    <th>Tanggal</th>
                                                    <th>Jam</th>
                                                    <th>Produk</th>
                                                    <th>Standart Suhu Fryer</th>
                                                    <th>Aktual Suhu Fryer</th>
                                                    <th>Waktu Penggorengan</th>
                                                    <th>Aktual Waktu Penggorengan</th>
                                                    <th>TPM Minyak</th>
                                                    <th class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($frayer5 as $item)
                                                <tr>
                                                    <td>{{ $frayer5->firstItem() + $loop->index }}</td>
                                                    <td>
                                                        @if($item->penggorengan && $item->penggorengan->shift && $item->penggorengan->shift->shift == 1)
                                                            <span class="badge bg-primary">Shift 1</span>
                                                        @elseif($item->penggorengan && $item->penggorengan->shift && $item->penggorengan->shift->shift == 2)
                                                            <span class="badge bg-success">Shift 2</span>
                                                        @elseif($item->penggorengan && $item->penggorengan->shift && $item->penggorengan->shift->shift == 3)
                                                            <span class="badge bg-secondary">Shift 3</span>
                                                        @else
                                                            <span class="badge bg-info">{{ $item->penggorengan->shift->shift ?? '-' }}</span>
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
                                                    <td>{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</td>
                                                    <td>{{ $item->produk->nama_produk ?? '-' }}
                                                        @if($item->penggorengan && $item->penggorengan->berat_produk)
                                                            ({{ $item->penggorengan->berat_produk }}gram)
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-warning">{{ $item->suhuFrayer->suhu_frayer_5 ?? '-' }}&deg;C</span>
                                                    </td>
                                                    <td>{{ $item->aktual_suhu_penggorengan }} &deg;C</td>
                                                    <td>{{ $item->waktuPenggorengan->waktu_penggorengan ?? '-' }} detik</td>
                                                    <td>{{ $item->aktual_penggorengan }} detik</td>
                                                    <td>{{ $item->tpm_minyak }}</td>
                                                    <td class="text-center">
                                                        <!--
@if(auth()->user()->hasPermissionTo('edit-frayer-5')) <a href="{{ route('frayer-5.edit', $item->uuid) }}" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                                        @endif
@if(auth()->user()->hasPermissionTo('delete-frayer-5'))
<form action="{{ route('frayer-5.destroy', $item->uuid) }}" method="POST" style="display:inline-block;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')" title="Hapus"><i class="fas fa-trash"></i></button>
                                                        </form> @endif
-->
                                                        <x-action-buttons :item="$item" route-prefix="frayer-5" :show-view="false" />
                                                        @php
                                                            $queryParams = [
                                                                'frayer_uuid' => $item->uuid,
                                                                'breader_uuid' => $item->breader_uuid,
                                                                'battering_uuid' => $item->battering_uuid,
                                                                'predust_uuid' => $item->predust_uuid,
                                                                'penggorengan_uuid' => $item->penggorengan_uuid
                                                            ];
                                                            $hasilPenggorenganExists5 = \App\Models\HasilPenggorengan::where('frayer_uuid', $item->uuid)->exists();
                                                        @endphp
                                                        @if($hasilPenggorenganExists5)
                                                            <button class="btn btn-sm btn-secondary" disabled title="Hasil Penggorengan sudah ada">
                                                                <i class="fas fa-thumbs-up"></i> Hasil Penggorengan
                                                            </button>
                                                        @else
                                                            <a href="{{ route('hasil-penggorengan.create', $queryParams) }}" class="btn btn-sm btn-success" title="Tambah Hasil Penggorengan">
                                                                <i class="fas fa-arrow-right"></i> Hasil Penggorengan
                                                            </a>
                                                        @endif
                                                        <!-- Proses Roasting Fan -->
                                                        @php
                                                            $prosesRoastingFanExists = \App\Models\ProsesRoastingFan::where('frayer_uuid', $item->uuid)->exists();
                                                            $queryParams = [
                                                                'frayer_uuid' => $item->uuid,
                                                                'breader_uuid' => $item->breader_uuid ?? null,
                                                                'battering_uuid' => $item->battering_uuid ?? null,
                                                                'predust_uuid' => $item->predust_uuid ?? null,
                                                                'penggorengan_uuid' => $item->penggorengan_uuid ?? null
                                                            ];
                                                        @endphp
                                                        @if($prosesRoastingFanExists)
                                                            <button class="btn btn-sm btn-secondary" disabled title="Proses Roasting Fan sudah ada">
                                                                <i class="fas fa-thumbs-up"></i> Proses Roasting Fan
                                                            </button>
                                                        @else
                                                            <a href="{{ route('proses-roasting-fan.create', $queryParams) }}" class="btn btn-sm btn-info" title="Tambah Proses Roasting Fan">
                                                                <i class="fas fa-arrow-right"></i> Proses Roasting Fan
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <div class="d-flex justify-content-center mt-3">
                                            {{ $frayer5->appends(['search' => $search ?? '', 'per_page' => $perPage ?? '', 'tab' => 'frayer5', 'frayer1_page' => request('frayer1_page'), 'frayer2_page' => request('frayer2_page'), 'frayer3_page' => request('frayer3_page'), 'frayer4_page' => request('frayer4_page')])->links('pagination::bootstrap-4') }}
                                        </div>
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

@push('scripts')
<script>
$(document).ready(function () {
    const activeTab = @json($activeTab ?? 'frayer1');
    const tabSelector = `#${activeTab}-tab`;
    if ($(tabSelector).length) {
        $(tabSelector).tab('show');
    }
});
</script>
@endpush