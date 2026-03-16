@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Proses Roasting Fan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href=""><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item active">Proses Roasting Fan</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Daftar Proses Roasting Fan</h3>
                    <div class="card-tools">
                        @if(auth()->user()->hasPermissionTo('create-proses-roasting-fan')) <a href="{{ route('proses-roasting-fan.create') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Tambah Data
                        </a> @endif
                    </div>
                </div>
                <div class="card-body text-center table-responsive">
                    <div class="row mb-3 mt-3">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('proses-roasting-fan.index') }}">
                                <div class="input-group input-group-sm" style="width: 300px;">
                                    <input type="text" class="form-control" name="search" placeholder="Cari nama produk" value="{{ $search ?? '' }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        @if(!empty($search))
                                            <a class="btn btn-outline-danger" href="{{ route('proses-roasting-fan.index') }}">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <input type="hidden" name="per_page" value="{{ $perPage ?? 10 }}">
                            </form>
                        </div>
                        <div class="col-md-6">
                            <div class="float-right">
                                <form method="GET" action="{{ route('proses-roasting-fan.index') }}">
                                    <select class="form-control form-control-sm" name="per_page" style="width: 80px;" onchange="this.form.submit()">
                                        <option value="5" {{ ($perPage ?? 10) == 5 ? 'selected' : '' }}>5</option>
                                        <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10</option>
                                        <option value="25" {{ ($perPage ?? 10) == 25 ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50</option>
                                        <option value="100" {{ ($perPage ?? 10) == 100 ? 'selected' : '' }}>100</option>
                                    </select>
                                    <input type="hidden" name="search" value="{{ $search ?? '' }}">
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
                                   
                                @endif
                            </small>
                        </div>
                    </div>
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="blok1-tab" data-toggle="tab" href="#blok1" role="tab" aria-controls="blok1" aria-selected="true">Blok 1</a>
                        </li>
                        <!-- <li class="nav-item btn-success" role="presentation">
                            <a class="nav-link" id="blok2-tab" data-toggle="tab" href="#blok2" role="tab" aria-controls="blok2" aria-selected="false">Blok 2</a>
                        </li> -->
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="blok1" role="tabpanel" aria-labelledby="blok1-tab">
                            <table style="white-space: nowrap;"  class="table table-bordered table-striped mt-3">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Shift</th>
                                        <th>Tanggal</th>
                                        <th>Jam</th>
                                        <th>Nama Produk</th>
                                        <th>Alur Proses</th>
                                        <th>Blok Terisi</th>
                                        <th>Aktual Lama Proses</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data as $item)
                                    <tr>
                                        <td>{{ $data->firstItem() + $loop->index }}</td>
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
                                                // Cek apakah data berasal dari records (grouped data)
                                                $hasInputRoasting = false;
                                                $hasPenggorengan = false;
                                                
                                                if(isset($item->records) && $item->records->count() > 0) {
                                                    // Untuk grouped data, cek dari records pertama
                                                    $firstRecord = $item->records->first();
                                                    $hasInputRoasting = !empty($firstRecord->input_roasting_uuid);
                                                    $hasPenggorengan = !empty($firstRecord->penggorengan_uuid);
                                                } else {
                                                    // Untuk single record
                                                    $hasInputRoasting = !empty($item->input_roasting_uuid ?? null);
                                                    $hasPenggorengan = !empty($item->penggorengan_uuid ?? null);
                                                }
                                            @endphp
                                            
                                            @if($hasInputRoasting)
                                                <!-- Alur Input Roasting -->
                                                <div class="d-flex align-items-center flex-wrap">
                                                    <span class="badge bg-primary px-2 py-1 me-1 mb-1" style="font-size: 0.7em;">
                                                        <i class="fas fa-fire me-1"></i>Input Roasting
                                                    </span>
                                                    <i class="fas fa-arrow-right text-muted me-1" style="font-size: 0.6em;"></i>
                                                    <span class="badge bg-info px-2 py-1 mb-1" style="font-size: 0.7em;">
                                                        <i class="fas fa-fan me-1"></i>Roasting Fan
                                                    </span>
                                                </div>
                                            @elseif($hasPenggorengan)
                                                <!-- Kondisi 1: Alur Penggorengan -->
                                                <div class="d-flex align-items-center flex-wrap">
                                                    <span class="badge bg-success px-2 py-1 me-1 mb-1" style="font-size: 0.7em;">
                                                        <i class="fas fa-fire me-1"></i>Penggorengan
                                                    </span>
                                                    <i class="fas fa-arrow-right text-muted me-1" style="font-size: 0.6em;"></i>
                                                    <span class="badge bg-info px-2 py-1 mb-1" style="font-size: 0.7em;">
                                                        <i class="fas fa-fan me-1"></i>Roasting Fan
                                                    </span>
                                                </div>
                                                <small class="text-muted d-block mt-1">Kondisi 1</small>
                                            @else
                                                <span class="badge bg-secondary px-2 py-1" style="font-size: 0.7em;">
                                                    <i class="fas fa-question me-1"></i>Tidak Diketahui
                                                </span>
                                            @endif
                                        </td>               
                                        <td>
                                            @foreach($item->block_numbers as $blockNum)
                                                <span class="badge bg-info mr-1">Blok {{ $blockNum }}</span>
                                            @endforeach
                                        </td>
                                        <td>{{ $item->aktual_lama_proses ?? '-' }} menit</td>
                                        <td>
                                            <!-- <div class="btn-vertical">
                                                <a href="{{ route('proses-roasting-fan.show', $item->uuid) }}" title="Lihat Detail" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
@if(auth()->user()->hasPermissionTo('edit-proses-roasting-fan'))
                                                <a href="{{ route('proses-roasting-fan.edit', $item->uuid) }}" title="Edit" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                                @endif
@if(auth()->user()->hasPermissionTo('view-proses-roasting-fan'))
<a href="{{ route('proses-roasting-fan.logs', $item->uuid) }}" class="btn btn-info btn-sm" title="Lihat Riwayat Perubahan">
                                                    <i class="fas fa-history"></i>
                                                </a>
                                                @endif
@if(auth()->user()->hasPermissionTo('delete-proses-roasting-fan'))
<form action="{{ route('proses-roasting-fan.destroy', $item->uuid) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" title="Hapus" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"><i class="fas fa-trash"></i></button>
                                                </form>
                                                
                                            @endif
</div> -->
                                            <!-- Hasil Proses Roasting -->
                                            @php
                                                $hasilProsesRoastingExists = \App\Models\HasilProsesRoasting::where('proses_roasting_fan_uuid', $item->uuid)->exists();
                                                
                                                // Get the actual record from database to access UUID fields
                                                $prosesRoastingFanRecord = \App\Models\ProsesRoastingFan::where('uuid', $item->uuid)->first();
                                                
                                                $queryParams = [
                                                    'proses_roasting_fan_uuid' => $item->uuid,
                                                    'input_roasting_uuid' => $prosesRoastingFanRecord->input_roasting_uuid ?? null,
                                                    'bahan_baku_roasting_uuid' => $prosesRoastingFanRecord->bahan_baku_roasting_uuid ?? null,
                                                    'frayer_uuid' => $prosesRoastingFanRecord->frayer_uuid ?? null,
                                                    'breader_uuid' => $prosesRoastingFanRecord->breader_uuid ?? null,
                                                    'battering_uuid' => $prosesRoastingFanRecord->battering_uuid ?? null,
                                                    'predust_uuid' => $prosesRoastingFanRecord->predust_uuid ?? null,
                                                    'penggorengan_uuid' => $prosesRoastingFanRecord->penggorengan_uuid ?? null
                                                ];
                                            @endphp
                                            @if($hasilProsesRoastingExists)
                                                <button class="btn btn-sm btn-secondary" disabled title="Hasil Proses Roasting sudah ada">
                                                    <i class="fas fa-thumbs-up"></i>
                                                </button>
                                            @else
                                                <a href="{{ route('hasil-proses-roasting.create', $queryParams) }}" class="btn btn-sm btn-success" title="Tambah Hasil Proses Roasting">
                                                    <i class="fas fa-arrow-right"></i> Hasil Proses Roasting
                                                </a>
                                            @endif
                                            <x-action-buttons :item="$item" route-prefix="proses-roasting-fan"/>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-center mt-3">
                                {{ $data->appends(['search' => $search ?? '', 'per_page' => $perPage ?? ''])->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection