@extends('layouts.app')
@php
    use Illuminate\Support\Facades\Schema;
@endphp

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h1 class="m-0">Data Pembekuan IQF Roasting</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                            <li class="breadcrumb-item active">Pembekuan IQF Roasting</li>
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
                                    <i class="fas fa-snowflake"></i> Data Pembekuan IQF Roasting
                                </h3>
                                <div class="card-tools">
                                    <!--
                                    @if(auth()->user()->hasPermissionTo('create-pembekuan-iqf-roasting')) <a href="{{ route('pembekuan-iqf-roasting.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Tambah Data
                                    </a> @endif
-->
                                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#exportPdfModal">                                        
                                        <i class="fas fa-file-pdf"></i> Cetak PDF
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <i class="icon fas fa-check"></i> {{ session('success') }}
                                    </div>
                                @endif

                                <div class="row mb-3 mt-3">
                                    <div class="col-md-6">
                                        <form method="GET" action="{{ route('pembekuan-iqf-roasting.index') }}">
                                            <div class="input-group input-group-sm" style="width: 300px;">
                                                <input type="text" class="form-control" name="search" placeholder="Cari nama produk" value="{{ $search ?? '' }}">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="submit">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                    @if(!empty($search))
                                                        <a class="btn btn-outline-danger" href="{{ route('pembekuan-iqf-roasting.index') }}">
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
                                            <form method="GET" action="{{ route('pembekuan-iqf-roasting.index') }}">
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
                                                Data Pembekuan IQF Roasting
                                            @endif
                                        </small>
                                    </div>
                                </div>

                                @if(count($pembekuanIqfRoastings))
                                <div class="table-responsive">
                                    <table  class="table text-center table-bordered table-striped table-hover" style="white-space: nowrap;">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>No</th>
                                                <th>Shift</th>
                                                <th>Tanggal</th>
                                                <th>Jam</th>
                                                <th>Nama Produk</th>
                                                <th>Alur Proses</th>
                                                <th>Suhu Ruang IQF</th>
                                                <th>Holding Time</th>
                                                <th>Dibuat Oleh</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pembekuanIqfRoastings as $item)
                                            <tr>
                                                <td>{{ $pembekuanIqfRoastings->firstItem() + $loop->index }}</td>
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
                                                        $produk = null;
                                                        $berat_produk = null;
                                                        
                                                        // KONDISI 1: Cek dari Penggorengan (Alur Penggorengan)
                                                        if($item->penggorengan_uuid && $item->penggorengan) {
                                                            $produk = $item->penggorengan->produk;
                                                            $berat_produk = $item->penggorengan->berat_produk;
                                                        }
                                                        // KONDISI 2: Cek dari Input Roasting (Alur Input Roasting)
                                                        elseif($item->input_roasting_uuid && $item->inputRoasting) {
                                                            $produk = $item->inputRoasting->produk;
                                                            $berat_produk = $item->inputRoasting->berat_produk ?? null;
                                                        }
                                                        // Fallback: Cek dari HasilProsesRoasting
                                                        elseif($item->hasil_proses_roasting_uuid && $item->hasilProsesRoasting) {
                                                            $produk = $item->hasilProsesRoasting->produk;
                                                            $berat_produk = null; // HasilProsesRoasting mungkin tidak punya berat_produk
                                                        }
                                                        // Fallback: Cek dari ProsesRoastingFan
                                                        elseif($item->proses_roasting_fan_uuid && $item->prosesRoastingFan) {
                                                            $produk = $item->prosesRoastingFan->produk;
                                                            $berat_produk = null;
                                                        }
                                                        // Fallback: Cek dari Frayer
                                                        elseif($item->frayer_uuid && $item->frayer) {
                                                            $produk = $item->frayer->produk;
                                                            $berat_produk = null;
                                                        }
                                                        // Fallback: Cek dari Breader
                                                        elseif($item->breader_uuid && $item->breader) {
                                                            $produk = $item->breader->produk;
                                                            $berat_produk = null;
                                                        }
                                                        // Fallback: Cek dari Battering
                                                        elseif($item->battering_uuid && $item->battering) {
                                                            $produk = $item->battering->produk;
                                                            $berat_produk = null;
                                                        }
                                                        // Fallback: Cek dari Predust
                                                        elseif($item->predust_uuid && $item->predust) {
                                                            $produk = $item->predust->produk;
                                                            $berat_produk = null;
                                                        }
                                                    @endphp
                                                    
                                                    @if($produk)
                                                        <div>
                                                            {{ $produk->nama_produk }}
                                                            @if($berat_produk)
                                                            ({{ $berat_produk }}gram)
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span class="text-muted">-</span>
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
                                                        $hasHasilPenggorengan = !empty($item->hasil_penggorengan_uuid);
                                                    @endphp
                                                    
                                                    @if($hasInputRoasting)
                                                        <!-- Alur Input Roasting -->
                                                        <div class="d-flex align-items-center flex-wrap">
                                                            <span class="badge bg-primary px-2 py-1 me-1 mb-1" style="font-size: 0.65em;">
                                                                <i class="fas fa-fire me-1"></i>Input Roasting
                                                            </span>
                                                            <i class="fas fa-arrow-right text-muted me-1" style="font-size: 0.5em;"></i>
                                                            <span class="badge bg-info px-2 py-1 me-1 mb-1" style="font-size: 0.65em;">
                                                                <i class="fas fa-fan me-1"></i>Roasting Fan
                                                            </span>
                                                            <i class="fas fa-arrow-right text-muted me-1" style="font-size: 0.5em;"></i>
                                                            <span class="badge bg-success px-2 py-1 me-1 mb-1" style="font-size: 0.65em;">
                                                                <i class="fas fa-clipboard-check me-1"></i>Hasil Proses
                                                            </span>
                                                            <i class="fas fa-arrow-right text-muted me-1" style="font-size: 0.5em;"></i>
                                                            <span class="badge bg-primary px-2 py-1 mb-1" style="font-size: 0.65em;">
                                                                <i class="fas fa-snowflake me-1"></i>IQF Roasting
                                                            </span>
                                                        </div>
                                                    @elseif($hasPenggorengan || $hasFrayer || $hasBreader || $hasBattering || $hasPredust || $hasHasilPenggorengan)
                                                        <!-- Kondisi 1: Alur Penggorengan -->
                                                        <div class="d-flex align-items-center flex-wrap">
                                                            @if($hasPredust)
                                                                <span class="badge bg-secondary px-2 py-1 me-1 mb-1" style="font-size: 0.65em;">
                                                                    <i class="fas fa-layer-group me-1"></i>Predust
                                                                </span>
                                                                <i class="fas fa-arrow-right text-muted me-1" style="font-size: 0.5em;"></i>
                                                            @endif
                                                            @if($hasBattering)
                                                                <span class="badge bg-warning px-2 py-1 me-1 mb-1" style="font-size: 0.65em;">
                                                                    <i class="fas fa-tint me-1"></i>Battering
                                                                </span>
                                                                <i class="fas fa-arrow-right text-muted me-1" style="font-size: 0.5em;"></i>
                                                            @endif
                                                            @if($hasBreader)
                                                                <span class="badge bg-info px-2 py-1 me-1 mb-1" style="font-size: 0.65em;">
                                                                    <i class="fas fa-bread-slice me-1"></i>Breader
                                                                </span>
                                                                <i class="fas fa-arrow-right text-muted me-1" style="font-size: 0.5em;"></i>
                                                            @endif
                                                            @if($hasFrayer)
                                                                <span class="badge bg-primary px-2 py-1 me-1 mb-1" style="font-size: 0.65em;">
                                                                    <i class="fas fa-fire me-1"></i>Frayer
                                                                </span>
                                                                <i class="fas fa-arrow-right text-muted me-1" style="font-size: 0.5em;"></i>
                                                            @endif
                                                            @if($hasPenggorengan)
                                                                <span class="badge bg-danger px-2 py-1 me-1 mb-1" style="font-size: 0.65em;">
                                                                    <i class="fas fa-fire me-1"></i>Penggorengan
                                                                </span>
                                                                <i class="fas fa-arrow-right text-muted me-1" style="font-size: 0.5em;"></i>
                                                            @endif
                                                            @if($hasHasilPenggorengan)
                                                                <span class="badge bg-success px-2 py-1 me-1 mb-1" style="font-size: 0.65em;">
                                                                    <i class="fas fa-clipboard-check me-1"></i>Hasil Penggorengan
                                                                </span>
                                                                <i class="fas fa-arrow-right text-muted me-1" style="font-size: 0.5em;"></i>
                                                            @endif
                                                            <span class="badge bg-primary px-2 py-1 mb-1" style="font-size: 0.65em;">
                                                                <i class="fas fa-snowflake me-1"></i>IQF Penggorengan
                                                            </span>
                                                        </div>
                                                        <small class="text-muted d-block mt-1">Kondisi 1 - Alur Penggorengan</small>
                                                    @else
                                                        <span class="badge bg-secondary px-2 py-1" style="font-size: 0.65em;">
                                                            <i class="fas fa-question me-1"></i>Tidak Diketahui
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ $item->suhu_ruang_iqf }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-warning">{{ $item->holding_time }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">{{ $item->user->name ?? '-' }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-vertical">
                                                        <!-- CRUD Buttons -->
                                                        <!-- <div class="mb-1">
@if(auth()->user()->hasPermissionTo('edit-pembekuan-iqf-roasting'))
                                                            <a href="{{ route('pembekuan-iqf-roasting.edit', ['uuid' => $item->uuid]) }}" 
                                                               class="btn btn-warning btn-sm" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            @endif
@if(auth()->user()->hasPermissionTo('delete-pembekuan-iqf-roasting'))
<form action="{{ route('pembekuan-iqf-roasting.destroy', ['uuid' => $item->uuid]) }}" 
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
                                                        <x-action-buttons :item="$item" route-prefix="pembekuan-iqf-roasting" :show-view="false" />

                                                        <!-- Approval Buttons -->
                                                        @php
                                                            $userRole = auth()->user()->id_role ?? null;
                                                            $hasApprovalColumns = Schema::hasColumn('pembekuan_iqf_roasting', 'approved_by_qc');
                                                        @endphp

                                                        <!-- Role-Based Button Display -->
                                                        @if($hasApprovalColumns)
                                                        <div class="btn-group-vertical" role="group">
                                                            @if(in_array($userRole, [1, 5]))
                                                                <!-- Role 1 dan 5: Tampilkan QC button yang bisa diklik -->
                                                                <button type="button" 
                                                                        class="btn btn-sm {{ isset($item->approved_by_qc) && $item->approved_by_qc ? 'btn-success' : 'btn-outline-success' }} approve-btn" 
                                                                        data-id="{{ $item->uuid }}" 
                                                                        data-type="qc"
                                                                        title="Disetujui oleh QC"
                                                                        {{ isset($item->approved_by_qc) && $item->approved_by_qc ? 'disabled' : '' }}>
                                                                    <i class="fas {{ isset($item->approved_by_qc) && $item->approved_by_qc ? 'fa-check-circle' : 'fa-check' }}"></i> QC
                                                                </button>
                                                                <!-- Produksi button (read-only untuk role 1,5) -->
                                                                <button type="button" 
                                                                        class="btn btn-sm {{ $item->approved_by_produksi ? 'btn-primary' : 'btn-secondary' }}" 
                                                                        title="{{ $item->approved_by_produksi ? 'Sudah disetujui Produksi' : 'Menunggu persetujuan Produksi' }}"
                                                                        disabled>
                                                                    <i class="fas {{ $item->approved_by_produksi ? 'fa-check-circle' : 'fa-clock' }}"></i> Produksi
                                                                </button>
                                                                <!-- SPV button (read-only untuk role 1,5) -->
                                                                <button type="button" 
                                                                        class="btn btn-sm {{ $item->approved_by_spv ? 'btn-dark' : 'btn-secondary' }}" 
                                                                        title="{{ $item->approved_by_spv ? 'Sudah disetujui SPV' : 'Menunggu persetujuan SPV' }}"
                                                                        disabled>
                                                                    <i class="fas {{ $item->approved_by_spv ? 'fa-check-circle' : 'fa-clock' }}"></i> SPV
                                                                </button>

                                                            @elseif($userRole == 2)
                                                                <!-- Role 2: Hanya tampilkan tombol Produksi -->
                                                                <button type="button" 
                                                                        class="btn btn-sm {{ $item->approved_by_produksi ? 'btn-primary' : ($item->approved_by_qc ? 'btn-outline-primary' : 'btn-secondary') }} {{ $item->approved_by_qc && !$item->approved_by_produksi ? 'approve-btn' : '' }}" 
                                                                        data-id="{{ $item->uuid }}" 
                                                                        data-type="produksi"
                                                                        title="{{ !$item->approved_by_qc ? 'Menunggu persetujuan QC terlebih dahulu' : ($item->approved_by_produksi ? 'Sudah disetujui Produksi' : 'Disetujui oleh Produksi') }}"
                                                                        {{ !$item->approved_by_qc || $item->approved_by_produksi ? 'disabled' : '' }}>
                                                                    <i class="fas {{ $item->approved_by_produksi ? 'fa-check-circle' : (!$item->approved_by_qc ? 'fa-clock' : 'fa-check') }}"></i> Disetujui oleh Produksi
                                                                </button>

                                                            @elseif($userRole == 3)
                                                                <!-- Role 3: Hanya tampilkan tombol QC -->
                                                                <button type="button" 
                                                                        class="btn btn-sm {{ $item->approved_by_qc ? 'btn-success' : 'btn-outline-success' }} approve-btn" 
                                                                        data-id="{{ $item->uuid }}" 
                                                                        data-type="qc"
                                                                        title="Disetujui oleh QC"
                                                                        {{ $item->approved_by_qc ? 'disabled' : '' }}>
                                                                    <i class="fas {{ $item->approved_by_qc ? 'fa-check-circle' : 'fa-check' }}"></i> Disetujui oleh QC
                                                                </button>

                                                            @elseif($userRole == 4)
                                                                <!-- Role 4: Hanya tampilkan tombol SPV -->
                                                                <button type="button" 
                                                                        class="btn btn-sm {{ $item->approved_by_spv ? 'btn-dark' : ($item->approved_by_produksi ? 'btn-outline-dark' : 'btn-secondary') }} {{ $item->approved_by_produksi && !$item->approved_by_spv ? 'approve-btn' : '' }}" 
                                                                        data-id="{{ $item->uuid }}" 
                                                                        data-type="spv"
                                                                        title="{{ !$item->approved_by_produksi ? 'Menunggu persetujuan Produksi terlebih dahulu' : ($item->approved_by_spv ? 'Sudah disetujui SPV' : 'Disetujui oleh SPV') }}"
                                                                        {{ !$item->approved_by_produksi || $item->approved_by_spv ? 'disabled' : '' }}>
                                                                    <i class="fas {{ $item->approved_by_spv ? 'fa-check-circle' : (!$item->approved_by_produksi ? 'fa-clock' : 'fa-check') }}"></i> Disetujui oleh SPV
                                                                </button>
                                                            @endif
                                                        </div>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $pembekuanIqfRoastings->appends(['search' => $search ?? '', 'per_page' => $perPage ?? ''])->links('pagination::bootstrap-4') }}
                                </div>
                                @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Tidak ada data pembekuan IQF roasting yang tersedia.
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
<!-- Modal Export PDF -->
<div class="modal fade" id="exportPdfModal" tabindex="-1" role="dialog" aria-labelledby="exportPdfModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportPdfModalLabel">Cetak PDF Alur Proses Pembekuan IQF Roasting</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="bulkExportForm" action="{{ route('pembekuan-iqf-roasting.bulk-export-pdf') }}" method="POST" data-bulk-form="true">
                    @csrf
                    <div class="form-group">
                        <label for="filterTanggal">Tanggal</label>
                        <input type="date" class="form-control" id="filterTanggal" name="tanggal">
                    </div>
                    <div class="form-group">
                        <label for="filterProduk">Produk</label>
                        <select class="form-control" id="id_produk_select_pembeukan_iqf_roasting" name="id_produk">
                            <option value="">Semua Produk</option>
                            @php
                                $produks = \App\Models\JenisProduk::all();
                            @endphp
                            @foreach($produks as $produk)
                                <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="filterFlowType">Alur Proses</label>
                        <select class="form-control" id="filterFlowType" name="flow_type">
                            <option value="">Pilih Kondisi Proses</option>
                            <option value="penggorengan">Kondisi 1 - Alur Penggorengan Saja</option>
                            <option value="input_roasting">Kondisi 2 - Alur Input Roasting Saja</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="filterShift">Shift</label>
                        <select class="form-control" id="filterShift" name="shift">
                            <option value="">Semua Shift</option>
                            <option value="1">Shift 1</option>
                            <option value="2">Shift 2</option>
                            <option value="3">Shift 3</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="kodeForm">Kode Form <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="kodeForm" name="kode_form" required value="QF 10/00" placeholder="Contoh: IQF-ROAST-001">
                        <small class="form-text text-muted">Kode form wajib diisi</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" data-bulk-export="true">
                    <i class="fas fa-file-pdf"></i> Cetak PDF
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('.approve-btn').click(function() {
        const id = $(this).data('id');
        const type = $(this).data('type');
        const button = $(this);
        
        // Konfirmasi sebelum approve
        const typeNames = {
            'qc': 'QC',
            'produksi': 'Produksi', 
            'spv': 'SPV'
        };
        
        if (confirm(`Apakah Anda yakin ingin menyetujui data ini sebagai ${typeNames[type]}?`)) {
            // Disable button sementara dan show loading
            button.prop('disabled', true);
            button.html('<i class="fas fa-spinner fa-spin"></i> Processing...');
            
            // AJAX request untuk approval
            $.ajax({
                url: '{{ route("pembekuan-iqf-roasting.approve", ":id") }}'.replace(':id', id),
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    type: type
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        button.removeClass('btn-outline-success btn-outline-primary btn-outline-dark')
                              .addClass('btn-success')
                              .html('<i class="fas fa-check-circle"></i> Approved');
                        
                        // Reload halaman setelah delay singkat
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        alert('Gagal menyetujui data: ' + response.message);
                        button.prop('disabled', false);
                        // Restore original button text
                        const originalText = {
                            'qc': '<i class="fas fa-check"></i> QC',
                            'produksi': '<i class="fas fa-check"></i> Produksi',
                            'spv': '<i class="fas fa-check"></i> SPV'
                        };
                        button.html(originalText[type]);
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Terjadi kesalahan saat menyetujui data';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                    button.prop('disabled', false);
                    // Restore original button text
                    const originalText = {
                        'qc': '<i class="fas fa-check"></i> QC',
                        'produksi': '<i class="fas fa-check"></i> Produksi',
                        'spv': '<i class="fas fa-check"></i> SPV'
                    };
                    button.html(originalText[type]);
                }
            });
        }
    });
});
</script>
@endpush

@endsection
