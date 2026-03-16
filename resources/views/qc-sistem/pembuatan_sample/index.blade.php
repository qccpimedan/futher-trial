@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Data Pembuatan Sample</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                        <li class="breadcrumb-item active">Pembuatan Sample</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <!-- Alert Messages - Tambahkan setelah content-header dan sebelum <div class="row"> -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('warning') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle mr-2"></i>{{ session('info') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('import_errors'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong>Detail baris gagal import (maks 20):</strong>
                    <ul class="mb-0">
                        @foreach(session('import_errors') as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-vial"></i> Tabel Data Sample</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-danger btn-sm mr-2" data-toggle="modal" data-target="#exportPdfModal">
                                    <i class="fas fa-file-pdf"></i> Cetak PDF Filter
                                </button>
                                <button type="button" class="btn btn-success btn-sm mr-2" data-toggle="modal" data-target="#importExcelModal">
                                    <i class="fas fa-file-excel"></i> Import Excel
                                </button>
                                    @if(auth()->user()->hasPermissionTo('create-pembuatan-sample'))
                                <a href="{{ route('pembuatan-sample.create') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Tambah Data</a>
                            @endif
</div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                 <!-- Form Pencarian Server-Side -->
                                <div class="row mb-3 mt-3">
                                    <div class="col-md-6">
                                        <form method="GET" action="{{ route('pembuatan-sample.index') }}">
                                            <div class="input-group input-group-sm" style="width: 300px;">
                                                <input type="text" class="form-control" name="search" placeholder="Cari Produk atau kode produksi atau jenis sample" value="{{ $search ?? '' }}">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="submit">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                    @if(!empty($search))
                                                        <a class="btn btn-outline-danger" href="{{ route('pembuatan-sample.index') }}">
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
                                            <form method="GET" action="{{ route('pembuatan-sample.index') }}">
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
                                                Data Pembuatan Sample
                                            @endif
                                        </small>
                                    </div>
                                </div>
                                <table  style="white-space: nowrap;" class="table text-center table-bordered table-striped table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Shift</th>
                                           <th>Tanggal</th>
                                            <th>Jam</th>
                                            <!-- <th>Plan</th> -->
                                            <th>Nama Produk</th>
                                            <th>Jenis Sample</th>
                                            <th>Kode Produksi</th>
                                            <th>Dibuat Oleh</th>
                                            <th>Jumlah</th>
                                            <th>Berat</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $item)
                                            <tr>
                                                <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
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
                                              <td>
                                                    @if(auth()->user()->id_role == 2 || auth()->user()->id_role == 3)
                                                        <span class="badge badge-secondary">{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') : '-' }}</span>
                                                    @else
                                                        <span class="badge badge-secondary">{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y H:i:s') : '-' }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge badge-info">{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</span>
                                                </td>
                                                <!-- <td>{{ $item->plan->nama_plan }}</td> -->
                                                <td>{{ $item->produk->nama_produk }}</td>
                                                <td><span class="badge badge-info">{{ $item->jenis_sample }}</span></td>
                                                <td>{{ $item->kode_produksi }}</td>
                                                <td>{{ $item->createdBy->name ?? '-' }}</td>
                                                <td>{{ $item->jumlah }}</td>
                                                <td>{{ rtrim(rtrim(number_format($item->berat, 2), '0'), '.') }} gram</td>
                                                <td>
                                                    <div class="btn-vertical">
                                                        <!-- CRUD Buttons -->
                                                        <!-- <div class="mb-1">
@if(auth()->user()->hasPermissionTo('edit-pembuatan-sample'))
                                                            <a href="{{ route('pembuatan-sample.edit', $item->uuid) }}" 
                                                               class="btn btn-sm btn-warning" 
                                                               title="Edit Data">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            @endif
@if(auth()->user()->hasPermissionTo('delete-pembuatan-sample'))
<form action="{{ route('pembuatan-sample.destroy', $item->uuid) }}" 
                                                                  method="POST" 
                                                                  class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" 
                                                                        class="btn btn-sm btn-danger" 
                                                                        title="Hapus Data"
                                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
@endif
@if(auth()->user()->hasPermissionTo('view-pembuatan-sample'))
                                                            <a href="{{ route('pembuatan-sample.logs', $item->uuid) }}" 
                                                               class="btn btn-sm btn-info" 
                                                               title="Lihat Riwayat Perubahan">
                                                                <i class="fas fa-history"></i>
                                                            </a>
                                                        @endif
</div> -->
                                                        <x-action-buttons :item="$item" route-prefix="pembuatan-sample" :show-view="false" />
                                                        <!-- Approval Buttons -->
                                                        @php
                                                            $userRole = auth()->user()->id_role;
                                                        @endphp
                                                        
                                                        <!-- Role-Based Button Display following Chillroom pattern -->
                                                        <div class="btn-group-vertical" role="group">
                                                            @if(in_array($userRole, [1, 5]))
                                                                <!-- Role 1 dan 5: Tampilkan semua tombol dengan QC yang bisa diklik -->
                                                                <button type="button" 
                                                                        class="btn btn-sm {{ $item->approved_by_qc ? 'btn-success' : 'btn-outline-success' }} approve-btn" 
                                                                        data-id="{{ $item->uuid }}" 
                                                                        data-type="qc"
                                                                        title="Disetujui oleh QC"
                                                                        {{ $item->approved_by_qc ? 'disabled' : '' }}>
                                                                    <i class="fas {{ $item->approved_by_qc ? 'fa-check-circle' : 'fa-check' }}"></i> Verfikasi QC
                                                                </button>
                                                                <!-- Produksi button (read-only untuk role 1,5) -->
                                                                <button type="button" 
                                                                        class="btn btn-sm {{ $item->approved_by_produksi ? 'btn-primary' : 'btn-secondary' }}" 
                                                                        title="{{ $item->approved_by_produksi ? 'Sudah disetujui Produksi' : 'Menunggu persetujuan Produksi' }}"
                                                                        disabled>
                                                                    <i class="fas {{ $item->approved_by_produksi ? 'fa-check-circle' : 'fa-clock' }}"></i> Verifikasi FM/FL PRODUKSI
                                                                </button>
                                                                <!-- SPV button (read-only untuk role 1,5) -->
                                                                <button type="button" 
                                                                        class="btn btn-sm {{ $item->approved_by_spv ? 'btn-dark' : 'btn-secondary' }}" 
                                                                        title="{{ $item->approved_by_spv ? 'Sudah disetujui SPV' : 'Menunggu persetujuan SPV' }}"
                                                                        disabled>
                                                                    <i class="fas {{ $item->approved_by_spv ? 'fa-check-circle' : 'fa-clock' }}"></i> Verifikasi SPV
                                                                </button>

                                                            @elseif($userRole == 2)
                                                                <!-- Role 2: Hanya tampilkan tombol Produksi -->
                                                                <button type="button" 
                                                                        class="btn btn-sm {{ $item->approved_by_produksi ? 'btn-primary' : ($item->approved_by_qc ? 'btn-outline-primary' : 'btn-secondary') }} {{ $item->approved_by_qc && !$item->approved_by_produksi ? 'approve-btn' : '' }}" 
                                                                        data-id="{{ $item->uuid }}" 
                                                                        data-type="produksi"
                                                                        title="{{ !$item->approved_by_qc ? 'Menunggu persetujuan QC terlebih dahulu' : ($item->approved_by_produksi ? 'Sudah disetujui Produksi' : 'Disetujui oleh Produksi') }}"
                                                                        {{ !$item->approved_by_qc || $item->approved_by_produksi ? 'disabled' : '' }}>
                                                                    <i class="fas {{ $item->approved_by_produksi ? 'fa-check-circle' : (!$item->approved_by_qc ? 'fa-clock' : 'fa-check') }}"></i> FM/FL PRODUKSI
                                                                </button>

                                                            @elseif($userRole == 3)
                                                                <!-- Role 3: Hanya tampilkan tombol QC -->
                                                                <button type="button" 
                                                                        class="btn btn-sm {{ $item->approved_by_qc ? 'btn-success' : 'btn-outline-success' }} approve-btn" 
                                                                        data-id="{{ $item->uuid }}" 
                                                                        data-type="qc"
                                                                        title="Disetujui oleh QC"
                                                                        {{ $item->approved_by_qc ? 'disabled' : '' }}>
                                                                    <i class="fas {{ $item->approved_by_qc ? 'fa-check-circle' : 'fa-check' }}"></i> Verifikasi QC
                                                                </button>

                                                            @elseif($userRole == 4)
                                                                <!-- Role 4: Hanya tampilkan tombol SPV -->
                                                                <button type="button" 
                                                                        class="btn btn-sm {{ $item->approved_by_spv ? 'btn-dark' : ($item->approved_by_produksi ? 'btn-outline-dark' : 'btn-secondary') }} {{ $item->approved_by_produksi && !$item->approved_by_spv ? 'approve-btn' : '' }}" 
                                                                        data-id="{{ $item->uuid }}" 
                                                                        data-type="spv"
                                                                        title="{{ !$item->approved_by_produksi ? 'Menunggu persetujuan Produksi terlebih dahulu' : ($item->approved_by_spv ? 'Sudah disetujui SPV' : 'Disetujui oleh SPV') }}"
                                                                        {{ !$item->approved_by_produksi || $item->approved_by_spv ? 'disabled' : '' }}>
                                                                    <i class="fas {{ $item->approved_by_spv ? 'fa-check-circle' : (!$item->approved_by_produksi ? 'fa-clock' : 'fa-check') }}"></i> SPV
                                                                </button>

                                                            @else
                                                                <!-- Role lain: Tampilkan semua tombol sebagai read-only -->
                                                                <button type="button" 
                                                                        class="btn btn-sm {{ $item->approved_by_qc ? 'btn-success' : 'btn-secondary' }}" 
                                                                        title="{{ $item->approved_by_qc ? 'Sudah disetujui QC' : 'Menunggu persetujuan QC' }}"
                                                                        disabled>
                                                                    <i class="fas {{ $item->approved_by_qc ? 'fa-check-circle' : 'fa-clock' }}"></i> Verifasi QC
                                                                </button>
                                                                <button type="button" 
                                                                        class="btn btn-sm {{ $item->approved_by_produksi ? 'btn-primary' : 'btn-secondary' }}" 
                                                                        title="{{ $item->approved_by_produksi ? 'Sudah disetujui Produksi' : 'Menunggu persetujuan Produksi' }}"
                                                                        disabled>
                                                                    <i class="fas {{ $item->approved_by_produksi ? 'fa-check-circle' : 'fa-clock' }}"></i> Verifikasi FM/FL PRODUKSI
                                                                </button>
                                                                <button type="button" 
                                                                        class="btn btn-sm {{ $item->approved_by_spv ? 'btn-dark' : 'btn-secondary' }}" 
                                                                        title="{{ $item->approved_by_spv ? 'Sudah disetujui SPV' : 'Menunggu persetujuan SPV' }}"
                                                                        disabled>
                                                                    <i class="fas {{ $item->approved_by_spv ? 'fa-check-circle' : 'fa-clock' }}"></i> Verifikasi SPV
                                                                </button>
                                                            @endif
                                                        </div>

                                                        <!-- Status Persetujuan -->
                                                        <div class="mt-1">
                                                            @if($item->approved_by_qc)
                                                                <small class="badge badge-success d-block mb-1">✓ QC</small>
                                                            @endif
                                                            @if($item->approved_by_produksi)
                                                                <small class="badge badge-primary d-block mb-1">✓ FM/FL PRODUKSI</small>
                                                            @endif
                                                            @if($item->approved_by_spv)
                                                                <small class="badge badge-dark d-block mb-1">✓ SPV</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-center">
                            {{ $data->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Export PDF -->
<div class="modal fade" id="exportPdfModal" tabindex="-1" role="dialog" aria-labelledby="exportPdfModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportPdfModalLabel">Cetak PDF Pembuatan Sample Berdasarkan Filter</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="bulkExportForm" action="{{ route('pembuatan-sample.bulk-export-pdf') }}" method="POST" data-bulk-form="true">
                    @csrf
                    <div class="form-group">
                        <label for="filterTanggal">Tanggal</label>
                        <input type="date" class="form-control" id="filterTanggal" name="tanggal">
                    </div>
                    <div class="form-group">
                        <label for="filterShift">Shift</label>
                        <select class="form-control" id="filterShift" name="id_shift">
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
                        <input type="text" class="form-control" id="bulkKodeForm" name="kode_form" value="QF 17/00" placeholder="Masukkan kode form" required>
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

<!-- Modal Import Excel -->
<div class="modal fade" id="importExcelModal" tabindex="-1" role="dialog" aria-labelledby="importExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importExcelModalLabel">Import Excel Pembuatan Sample</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('pembuatan-sample.import-excel') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>File Excel (.xlsx / .xls)</label>
                        <a href="{{ route('pembuatan-sample.download-template') }}" class="btn btn-sm btn-outline-primary mb-2">
                            <i class="fas fa-download"></i> Download Template
                        </a>
                        <input type="file" name="file" class="form-control" required>
                        <small class="form-text text-muted">
                            Format kolom: Shift | Nama Produk | Berat Produk (gram) | Gramase (gram) | Kode Produksi | Tanggal Produksi | Jam | Tanggal Expired | Jumlah | Jenis Sample
                        </small>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success"><i class="fas fa-upload"></i> Import</button>
                    </div>
                </form>
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
                url: '{{ route("pembuatan-sample.approve", ":id") }}'.replace(':id', id),
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
