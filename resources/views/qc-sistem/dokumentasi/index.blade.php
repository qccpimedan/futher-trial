{{-- filepath: resources/views/qc-sistem/persiapan_cold_mixing/index.blade.php --}}
@extends('layouts.app')

@php
use Illuminate\Support\Facades\Schema;
    $isLocalhost = in_array(request()->getHost(), ['localhost', '127.0.0.1', '10.68.1.37']);
    $assetPath = $isLocalhost ? 'public/' : '';

@endphp

@section('container')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-camera text-info"></i>
                        Dokumentasi
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                        <li class="breadcrumb-item active">Dokumentasi</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            
            <!-- Alert Success -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            <!-- Data Table Card -->
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-table"></i>
                        Data Dokumentasi
                    </h3>
                    <div class="card-tools d-flex">
                        <form action="{{ route('dokumentasi.index') }}" method="GET" class="mr-2">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <input type="text" name="search" class="form-control" placeholder="Cari Nama Produk atau Tanggal" value="{{ $search ?? '' }}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    @if($search)
                                        <a href="{{ route('dokumentasi.index') }}" class="btn btn-default">
                                            <i class="fas fa-times text-danger"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="maximize">
                            <i class="fas fa-expand"></i>
                        </button>
                        <!--
                                    @if(auth()->user()->hasPermissionTo('create-dokumentasi')) <a href="{{ route('dokumentasi.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i>
                            Tambah Data
                        </a> @endif
-->
                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#exportPdfModal">
                            <i class="fas fa-file-pdf"></i> Cetak Form Pengemasan
                        </button>
                          
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table style="white-space: nowrap;" class="table table-bordered table-striped table-hover">
                            <thead class="thead-light">
                                <tr class="text-white text-center">
                                    <th class="align-middle" style="width: 50px;">No</th>
                                    <th class="align-middle">
                                        Shift
                                    </th>
                                    <!-- <th rowspan="2" class="align-middle">
                                        Plan
                                    </th> -->
                                    <th class="align-middle">
                                        Tanggal
                                    </th>
                                    <th class="align-middle">
                                        Jam
                                    </th>
                                    <th class="align-middle">
                                        Produk
                                    </th>
                                    <th class="align-middle">
                                       Kode Produksi
                                    </th>
                                    <th class="align-middle">
                                        Dibuat Oleh
                                    </th>
                                    <th class="align-middle">
                                        Foto Kode Produksi dan Best Before
                                    </th>
                                    <th class="align-middle">
                                       QR Code
                                    </th>
                                    <th class="align-middle">
                                        Foto Label Pollyroll
                                    </th>
                                   
                                  
                                    <th class="align-middle" style="width: 120px;">
                                        Aksi
                                    </th>
                                </tr>
                             
                            </thead>
                            <tbody>
                                @forelse($data as $i => $item)
                                <tr>
                                    <td class="text-center">
                                        <span>{{ $data->firstItem() + $i }}</span>
                                    </td>
                                    <td>
                                       @if($item->shift->shift == 1 || $item->shift_id == 1)
                                                        <span class="badge bg-primary">Shift 1</span>
                                                    @elseif($item->shift->shift == 2 || $item->shift_id == 2)
                                                        <span class="badge bg-success">Shift 2</span>
                                                    @elseif($item->shift->shift == 3 || $item->shift_id == 3)
                                                        <span class="badge bg-secondary">Shift 3</span>
                                                    @else
                                                        <span class="badge bg-info">{{ $item->shift->shift ?? '-' }}</span>
                                                    @endif
                                    </td>
                                    <!-- <td class="text-center">
                                        <span>
                                            {{ $item->plan->nama_plan ?? '-' }}
                                        </span>
                                    </td> -->
                               <td class="text-center">
                                    @if(auth()->user()->id_role == 2 || auth()->user()->id_role == 3)
                                        <span class="badge badge-secondary">{{ \Carbon\Carbon::parse($item->tanggal)->format('d-M-Y') }}</span>
                                    @else
                                        <span class="badge badge-secondary">{{ \Carbon\Carbon::parse($item->tanggal)->format('d-M-Y H:i:s') }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-info">{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</span>
                                </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <p>{{ $item->pengemasanproduk->produk->nama_produk ?? '-' }}  {{$item->pengemasanproduk->berat}} gram</p>
                                            <span>{{ $item->produk->nama_produk ?? '-' }}</span>
                                        </div>
                                    </td>
                                  
                                      
                                         <td>{{ $item->pengemasanproduk->kode_produksi ?? '-' }}</td>
                                         <td>{{ $item->user->name ?? '-' }}</td>
                                         
                                    
                                     <td class="text-center">
                                            @if($item->foto_kode_produksi)
                                                <a href="#" data-toggle="modal" data-target="#modalFotoKode{{ $item->id }}">
                                                    <img src="{{ asset($assetPath . 'storage/'.$item->foto_kode_produksi) }}" alt="Foto Kode Produksi" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                                                    <br>
                                                  
                                                </a>
                                                <!-- Modal -->
                                                <div class="modal fade" id="modalFotoKode{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="modalFotoKodeLabel{{ $item->id }}" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="modalFotoKodeLabel{{ $item->id }}">Foto Kode Produksi dan Best Before</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body text-center">
                                                                <img src="{{ asset($assetPath . 'storage/'.$item->foto_kode_produksi) }}" alt="Foto Kode Produksi" style="max-width:100%;max-height:70vh;">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span>-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($item->qr_code)
                                                <a href="#" data-toggle="modal" data-target="#modalQrCode{{ $item->id }}">
                                                    <img src="{{ asset($assetPath . 'storage/' . $item->qr_code) }}" alt="QR Code" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                                                    <br>
                                                   
                                                </a>
                                                <!-- Modal -->
                                                <div class="modal fade" id="modalQrCode{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="modalQrCodeLabel{{ $item->id }}" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="modalQrCodeLabel{{ $item->id }}">QR Code</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body text-center">
                                                                <img src="{{ asset($assetPath . 'storage/' . $item->qr_code) }}" alt="QR Code" style="max-width:100%;max-height:70vh;">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span>-</span>
                                            @endif
                                        </td>
                                   
                                        <td class="text-center">
                                            @if($item->label_polyroll)
                                                <a href="#" data-toggle="modal" data-target="#modalPolyroll{{ $item->id }}">
                                                    <img src="{{ asset($assetPath . 'storage/' . $item->label_polyroll) }}" alt="Label Polyroll" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                                                    <br>
                                                    
                                                </a>
                                                <!-- Modal -->
                                                <div class="modal fade" id="modalPolyroll{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="modalPolyrollLabel{{ $item->id }}" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="modalPolyrollLabel{{ $item->id }}">Foto Label Polyroll</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body text-center">
                                                                <img src="{{ asset($assetPath . 'storage/' . $item->label_polyroll) }}" alt="Label Polyroll" style="max-width:100%;max-height:70vh;">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span>-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-vertical">
                                                <!-- Tombol Edit dan Delete -->
                                                <!-- <div class="mb-1">
@if(auth()->user()->hasPermissionTo('edit-dokumentasi'))
                                                    <a href="{{ route('dokumentasi.edit', $item->uuid) }}" 
                                                    class="btn btn-sm btn-warning" 
                                                    title="Edit Data">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @endif
@if(auth()->user()->hasPermissionTo('delete-dokumentasi'))
<form action="{{ route('dokumentasi.destroy', $item->uuid) }}" 
                                                        method="POST" 
                                                        class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-danger" 
                                                                title="Hapus Data"
                                                                onclick="return confirmDelete()">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
@endif
@if(auth()->user()->hasPermissionTo('view-dokumentasi'))
                                                    <a href="{{ route('dokumentasi.logs', $item->uuid) }}" 
                                                        class="btn btn-sm btn-info" 
                                                        title="Riwayat Perubahan">
                                                            <i class="fas fa-history"></i>
                                                    </a>
                                                @endif
</div> -->
                                                <x-action-buttons :item="$item" route-prefix="dokumentasi" :show-view="false" />

                                                <!-- Tombol Persetujuan berdasarkan Role -->
                                                @php
                                                    $userRole = auth()->user()->id_role ?? null;
                                                    $hasApprovalColumns = Schema::hasColumn('dokumentasi', 'approved_by_qc');
                                                @endphp

                                                <!-- Role-Based Button Display -->
                                                @if($hasApprovalColumns)
                                                <div class="btn-group-vertical" role="group">
                                                    @if(in_array($userRole, [1, 5]))
                                                        <!-- Role 1 dan 5: Tampilkan semua tombol dengan QC yang bisa diklik -->
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
                                                                class="btn btn-sm {{ isset($item->approved_by_produksi) && $item->approved_by_produksi ? 'btn-primary' : 'btn-secondary' }}" 
                                                                title="{{ isset($item->approved_by_produksi) && $item->approved_by_produksi ? 'Sudah disetujui Produksi' : 'Menunggu persetujuan Produksi' }}"
                                                                disabled>
                                                            <i class="fas {{ isset($item->approved_by_produksi) && $item->approved_by_produksi ? 'fa-check-circle' : 'fa-clock' }}"></i> FM/FL PRODUKSI
                                                        </button>
                                                        <!-- SPV button (read-only untuk role 1,5) -->
                                                        <button type="button" 
                                                                class="btn btn-sm {{ isset($item->approved_by_spv) && $item->approved_by_spv ? 'btn-dark' : 'btn-secondary' }}" 
                                                                title="{{ isset($item->approved_by_spv) && $item->approved_by_spv ? 'Sudah disetujui SPV' : 'Menunggu persetujuan SPV' }}"
                                                                disabled>
                                                            <i class="fas {{ isset($item->approved_by_spv) && $item->approved_by_spv ? 'fa-check-circle' : 'fa-clock' }}"></i> SPV
                                                        </button>

                                                    @elseif($userRole == 2)
                                                        <!-- Role 2: Hanya tampilkan tombol Produksi -->
                                                        <button type="button" 
                                                                class="btn btn-sm {{ isset($item->approved_by_produksi) && $item->approved_by_produksi ? 'btn-primary' : (isset($item->approved_by_qc) && $item->approved_by_qc ? 'btn-outline-primary' : 'btn-secondary') }} {{ isset($item->approved_by_qc) && $item->approved_by_qc && (!isset($item->approved_by_produksi) || !$item->approved_by_produksi) ? 'approve-btn' : '' }}" 
                                                                data-id="{{ $item->uuid }}" 
                                                                data-type="produksi"
                                                                title="{{ !isset($item->approved_by_qc) || !$item->approved_by_qc ? 'Menunggu persetujuan QC terlebih dahulu' : (isset($item->approved_by_produksi) && $item->approved_by_produksi ? 'Sudah disetujui Produksi' : 'Disetujui oleh Produksi') }}"
                                                                {{ !isset($item->approved_by_qc) || !$item->approved_by_qc || (isset($item->approved_by_produksi) && $item->approved_by_produksi) ? 'disabled' : '' }}>
                                                            <i class="fas {{ isset($item->approved_by_produksi) && $item->approved_by_produksi ? 'fa-check-circle' : (!isset($item->approved_by_qc) || !$item->approved_by_qc ? 'fa-clock' : 'fa-check') }}"></i> Disetujui oleh Produksi
                                                        </button>

                                                    @elseif($userRole == 3)
                                                        <!-- Role 3: Hanya tampilkan tombol QC -->
                                                        <button type="button" 
                                                                class="btn btn-sm {{ isset($item->approved_by_qc) && $item->approved_by_qc ? 'btn-success' : 'btn-outline-success' }} approve-btn" 
                                                                data-id="{{ $item->uuid }}" 
                                                                data-type="qc"
                                                                title="Disetujui oleh QC"
                                                                {{ isset($item->approved_by_qc) && $item->approved_by_qc ? 'disabled' : '' }}>
                                                            <i class="fas {{ isset($item->approved_by_qc) && $item->approved_by_qc ? 'fa-check-circle' : 'fa-check' }}"></i> Disetujui oleh QC
                                                        </button>

                                                    @elseif($userRole == 4)
                                                        <!-- Role 4: Hanya tampilkan tombol SPV -->
                                                        <button type="button" 
                                                                class="btn btn-sm {{ isset($item->approved_by_spv) && $item->approved_by_spv ? 'btn-dark' : (isset($item->approved_by_produksi) && $item->approved_by_produksi ? 'btn-outline-dark' : 'btn-secondary') }} {{ isset($item->approved_by_produksi) && $item->approved_by_produksi && (!isset($item->approved_by_spv) || !$item->approved_by_spv) ? 'approve-btn' : '' }}" 
                                                                data-id="{{ $item->uuid }}" 
                                                                data-type="spv"
                                                                title="{{ !isset($item->approved_by_produksi) || !$item->approved_by_produksi ? 'Menunggu persetujuan Produksi terlebih dahulu' : (isset($item->approved_by_spv) && $item->approved_by_spv ? 'Sudah disetujui SPV' : 'Disetujui oleh SPV') }}"
                                                                {{ !isset($item->approved_by_produksi) || !$item->approved_by_produksi || (isset($item->approved_by_spv) && $item->approved_by_spv) ? 'disabled' : '' }}>
                                                            <i class="fas {{ isset($item->approved_by_spv) && $item->approved_by_spv ? 'fa-check-circle' : (!isset($item->approved_by_produksi) || !$item->approved_by_produksi ? 'fa-clock' : 'fa-check') }}"></i> Disetujui oleh SPV
                                                        </button>

                                                    @else
                                                        <!-- Role lain: Tampilkan semua tombol sebagai read-only -->
                                                        <button type="button" 
                                                                class="btn btn-sm {{ isset($item->approved_by_qc) && $item->approved_by_qc ? 'btn-success' : 'btn-secondary' }}" 
                                                                title="{{ isset($item->approved_by_qc) && $item->approved_by_qc ? 'Sudah disetujui QC' : 'Menunggu persetujuan QC' }}"
                                                                disabled>
                                                            <i class="fas {{ isset($item->approved_by_qc) && $item->approved_by_qc ? 'fa-check-circle' : 'fa-clock' }}"></i> QC
                                                        </button>
                                                        <button type="button" 
                                                                class="btn btn-sm {{ isset($item->approved_by_produksi) && $item->approved_by_produksi ? 'btn-primary' : 'btn-secondary' }}" 
                                                                title="{{ isset($item->approved_by_produksi) && $item->approved_by_produksi ? 'Sudah disetujui Produksi' : 'Menunggu persetujuan Produksi' }}"
                                                                disabled>
                                                            <i class="fas {{ isset($item->approved_by_produksi) && $item->approved_by_produksi ? 'fa-check-circle' : 'fa-clock' }}"></i> Produksi
                                                        </button>
                                                        <button type="button" 
                                                                class="btn btn-sm {{ isset($item->approved_by_spv) && $item->approved_by_spv ? 'btn-dark' : 'btn-secondary' }}" 
                                                                title="{{ isset($item->approved_by_spv) && $item->approved_by_spv ? 'Sudah disetujui SPV' : 'Menunggu persetujuan SPV' }}"
                                                                disabled>
                                                            <i class="fas {{ isset($item->approved_by_spv) && $item->approved_by_spv ? 'fa-check-circle' : 'fa-clock' }}"></i> SPV
                                                        </button>
                                                    @endif
                                                </div>

                                                <!-- Status Persetujuan -->
                                                <div class="mt-1">
                                                    @if(isset($item->approved_by_qc) && $item->approved_by_qc)
                                                        <small class="badge badge-success d-block mb-1">✓ QC</small>
                                                    @endif
                                                    @if(isset($item->approved_by_produksi) && $item->approved_by_produksi)
                                                        <small class="badge badge-primary d-block mb-1">✓ FM/FL PRODUKSI</small>
                                                    @endif
                                                    @if(isset($item->approved_by_spv) && $item->approved_by_spv)
                                                        <small class="badge badge-dark d-block mb-1">✓ SPV</small>
                                                    @endif
                                                </div>
                                                @endif
                                            </div>
                                        </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="fas fa-database fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">Belum ada data</h5>
                                            <p class="text-muted">Klik tombol "Tambah Data Dokumentasi" untuk menambah data baru</p>
                                    @if(auth()->user()->hasPermissionTo('create-dokumentasi'))
                                            <a href="{{ route('dokumentasi.create') }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-plus mr-2"></i>Tambah Data Pertama
                                            </a>
                                        @endif
</div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $data->appends(['search' => $search ?? ''])->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


<div class="modal fade" id="exportPdfModal" tabindex="-1" role="dialog" aria-labelledby="exportPdfModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportPdfModalLabel">Cetak PDF Form Pengemasan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="bulkExportForm" action="{{ route('dokumentasi.bulk-export-pdf') }}" method="POST" data-bulk-form="true">
                    @csrf
                    <div class="form-group">
                        <label for="filterTanggal">Tanggal</label>
                        <input type="date" class="form-control" id="filterTanggal" name="tanggal">
                    </div>
                    <div class="form-group">
                        <label for="filterShift">Shift</label>
                        <select class="form-control" id="filterShift" name="shift_id">
                            <option value="">Semua Shift</option>
                            @foreach($cachedShifts as $shift)
                                    <option value="{{ $shift->id }}">{{ $shift->shift }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="filterProduk">Nama Produk</label>
                        <select class="form-control" id="id_produk" name="id_produk">
                            <option value="">Semua Produk</option>
                            @if(auth()->user()->role === 'superadmin')
                                @foreach(\App\Models\JenisProduk::all() as $produk)
                                    <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                                @endforeach
                            @else
                                @foreach(\App\Models\JenisProduk::where('id_plan', auth()->user()->id_plan)->get() as $produk)
                                    <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="bulkKodeForm">Kode Form</label>
                        <input type="text" class="form-control" id="bulkKodeForm" name="kode_form" value="QF 15/00" placeholder="Masukkan kode form" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Batal</button>
                <button type="button" class="btn btn-primary" data-bulk-export="true"><i class="fas fa-download"></i> Cetak PDF</button>
            </div>
        </div>
    </div>
</div>
<!-- CSS untuk Sequential Approval -->
<style>
.btn-group-vertical .btn {
    margin-bottom: 2px;
}
.btn-outline-success:hover {
    background-color: #28a745;
    border-color: #28a745;
}
.btn-outline-primary:hover {
    background-color: #007bff;
    border-color: #007bff;
}
.btn-outline-dark:hover {
    background-color: #343a40;
    border-color: #343a40;
}
.approval-pending {
    opacity: 0.6;
    cursor: not-allowed;
}
.approval-ready {
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.7); }
    70% { box-shadow: 0 0 0 10px rgba(0, 123, 255, 0); }
    100% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0); }
}
</style>

<!-- JavaScript untuk Handle Approval -->
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
                url: '{{ route("dokumentasi.approve", ":id") }}'.replace(':id', id),
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
                            'produksi': '<i class="fas fa-check"></i> FM/FL PRODUKSI',
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
                        'produksi': '<i class="fas fa-check"></i> FM/FL PRODUKSI',
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