@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Data Kontrol Sanitasi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Data Kontrol Sanitasi</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="card-title">
                                    <i class="fas fa-tint text-primary mr-2"></i>
                                    Data Kontrol Sanitasi
                                </h3>
                                <div class="d-flex align-items-center">
                                    <form action="{{ route('kontrol-sanitasi.index') }}" method="GET" class="mr-2">
                                        <div class="input-group input-group-sm" style="width: 200px;">
                                            <input type="date" name="search" class="form-control" value="{{ $search ?? '' }}">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-default">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                                @if($search)
                                                    <a href="{{ route('kontrol-sanitasi.index') }}" class="btn btn-default">
                                                        <i class="fas fa-times text-danger"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </form>
                                    @if(auth()->user()->hasPermissionTo('create-kontrol-sanitasi'))
                                    <a href="{{ route('kontrol-sanitasi.create') }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus"></i> Tambah Data
                                    </a>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-success ml-2" data-toggle="modal" data-target="#importExcelModal">
                                        <i class="fas fa-file-excel"></i> Import Excel
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger ml-2" data-toggle="modal" data-target="#bulkExportModal">
                                        <i class="fas fa-file-pdf"></i> Export PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
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

                            @if(session('warning'))
                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if(session('info'))
                                <div class="alert alert-info alert-dismissible fade show" role="alert">
                                    <i class="fas fa-info-circle"></i> {{ session('info') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if(session('import_errors'))
                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    <h5><i class="icon fas fa-exclamation-triangle"></i> Detail baris gagal import (maks 20):</h5>
                                    <ul class="mb-0">
                                        @foreach(session('import_errors') as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if(isset($kontrolSanitasi) && count($kontrolSanitasi))
                                <div class="table-responsive">
                                    <table class="text-center table table-bordered table-striped table-hover" style="white-space: nowrap;">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>No</th>
                                                <!-- <th class="text-center">Plan</th> -->
                                                <th class="text-center">Shift</th>
                                                <th class="text-center">Tanggal</th>
                                                <th class="text-center">Jam</th>
                                                <th class="text-center">Suhu Air</th>
                                                <th class="text-center">Kadar Klorin Foot Basin</th>
                                                <th class="text-center">Kadar Klorin Hand Basin</th>
                                                <th class="text-center">Hasil Verifikasi</th>
                                                <!-- <th class="text-center">User Input</th> -->
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($kontrolSanitasi as $index => $item)
                                            <tr>
                                                <td class="text-center">{{ $kontrolSanitasi->firstItem() + $index }}</td>
                                                <!-- <td class="text-center">
                                                    <span class="badge badge-primary">
                                                        {{ $item->plan->nama_plan ?? '-' }}
                                                    </span>
                                                </td> -->
                                                <td class="text-center">
                                                    @if($item->shift_id == 1)
                                                    <span class="badge bg-primary">Shift {{ $item->shift->shift ?? 'Shift 1' }}</span>
                                                    @elseif($item->shift_id == 2)
                                                    <span class="badge bg-success">Shift {{ $item->shift->shift ?? 'Shift 2' }}</span>
                                                    @else
                                                    <span class="badge bg-secondary">Shift {{ $item->shift->shift ?? 'Shift ' . $item->shift_id }}</span>
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
                                                <td class="text-center">
                                                    {{ $item->suhu_air }}°C
                                                </td>
                                                <td class="text-center">
                                                    {{ $item->kadar_klorin_food_basin }}
                                                </td>
                                                <td class="text-center">
                                                    {{ $item->kadar_klorin_hand_basin }}
                                                </td>
                                                <td class="text-center">
                                                    {{ Str::limit($item->hasil_verifikasi, 30) }}
                                                </td>
                                                <!-- <td class="text-center">
                                                    <span class="badge badge-secondary">
                                                        {{ $item->user->name ?? '-' }}
                                                    </span>
                                                </td> -->
                                                <td class="text-center">
                                                    <div class="btn-vertical">
                                                        <!-- CRUD Buttons -->
                                                        <!-- <div class="mb-1">
@if(auth()->user()->hasPermissionTo('edit-kontrol-sanitasi'))
                                                            <a href="{{ route('kontrol-sanitasi.edit', $item->uuid) }}" class="btn btn-warning btn-sm" title="Edit Data">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            @endif
@if(auth()->user()->hasPermissionTo('delete-kontrol-sanitasi'))
<form action="{{ route('kontrol-sanitasi.destroy', $item->uuid) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                                @csrf @method('DELETE')
                                                                <button class="btn btn-danger btn-sm" title="Hapus Data">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
@endif
@if(auth()->user()->hasPermissionTo('view-kontrol-sanitasi'))
                                                            <a href="{{ route('kontrol-sanitasi.logs', $item->uuid) }}" class="btn btn-sm btn-primary" title="Lihat Riwayat Perubahan">
                                                                <i class="fas fa-history"></i>
                                                            </a>
                                                        @endif
</div> -->
                                                        <x-action-buttons :item="$item" route-prefix="kontrol-sanitasi" :show-view="false" />
                                                        <!-- Approval Buttons -->
                                                        @php
                                                            $userRole = auth()->user()->id_role ?? null;
                                                            $hasApprovalColumns = Schema::hasColumn('kontrol_sanitasi', 'approved_by_qc');
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
                                                                    <i class="fas {{ $item->approved_by_produksi ? 'fa-check-circle' : 'fa-clock' }}"></i> FM/FL PRODUKSI
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

                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $kontrolSanitasi->appends(['search' => $search ?? ''])->links('pagination::bootstrap-4') }}
                                </div>
                            @else
                                <div class="alert alert-info text-center">
                                    <i class="fas fa-info-circle fa-2x mb-2"></i>
                                    <h5>Belum ada data Kontrol Sanitasi</h5>
                                    <p class="mb-0">Silakan tambah data baru dengan mengklik tombol "Tambah Data" di atas.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- Modal Export PDF -->
<div class="modal fade" id="bulkExportModal" tabindex="-1" role="dialog" aria-labelledby="bulkExportModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkExportModalLabel">Cetak PDF Kontrol Sanitasi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="bulkExportForm" action="{{ route('kontrol-sanitasi.bulk-export-pdf') }}" method="POST" data-bulk-form="true">
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
                        <label for="bulkKodeForm">Kode Form</label>
                        <input type="text" class="form-control" id="bulkKodeForm" name="kode_form" value="QF 29/00" placeholder="Masukkan kode form" required>
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
                url: '{{ route("kontrol-sanitasi.approve", ":id") }}'.replace(':id', id),
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

<!-- Modal Import Excel -->
<div class="modal fade" id="importExcelModal" tabindex="-1" role="dialog" aria-labelledby="importExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importExcelModalLabel">Import Data Kontrol Sanitasi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('kontrol-sanitasi.import-excel') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group text-center mb-3">
                        <a href="{{ route('kontrol-sanitasi.download-template') }}" class="btn btn-outline-success">
                            <i class="fas fa-download"></i> Download Template Excel
                        </a>
                    </div>
                    <div class="form-group">
                        <label>Pilih File Excel</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx, .xls" required>
                        <small class="form-text text-muted">
                            Format kolom: Shift | Tanggal | Jam | Suhu Air | Kadar Klorin Food Basin | Kadar Klorin Hand Basin | Hasil Verifikasi
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-file-import"></i> Import Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>