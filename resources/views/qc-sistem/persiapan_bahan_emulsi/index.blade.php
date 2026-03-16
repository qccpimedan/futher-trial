{{-- filepath: resources/views/qc-sistem/persiapan_bahan_emulsi/index.blade.php --}}
@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-flask text-primary"></i> Persiapan Bahan Emulsi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item active">Persiapan Bahan Emulsi</li>
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
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-list"></i> Data Persiapan Bahan Emulsi</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#bulkPrintModal">
                                    <i class="fas fa-file-pdf"></i> Cetak PDF Filter
                                </button>
                                    @if(auth()->user()->hasPermissionTo('create-persiapan-bahan-emulsi'))
                                <a href="{{ route('persiapan-bahan-emulsi.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Tambah Data
                                </a>
                            @endif
</div>
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
                                    <i class="icon fas fa-times"></i> {{ session('error') }}
                                </div>
                            @endif


                            <!-- Search and Filter -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="input-group input-group-sm" style="width: 300px;">
                                        <input type="text" id="searchInputEmulsi" class="form-control" 
                                               placeholder="Cari data..." 
                                               title="Cari berdasarkan nama produk, plan, emulsi, bahan, kode produksi, atau hasil emulsi">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="searchBtnEmulsi">
                                                <i class="fas fa-search"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" type="button" id="clearBtnEmulsi" title="Hapus pencarian" style="display: none;">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="float-right">
                                        <select id="perPageSelectEmulsi" class="form-control form-control-sm" style="width: 80px;">
                                            <option value="5">5</option>
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Data Info -->
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <small class="text-muted" id="dataInfoEmulsi">
                                        Data Persiapan Bahan Emulsi
                                    </small>
                                </div>
                            </div>
                            
                            <div class="table-responsive text-center">
                                <table style="white-space: nowrap;" class="table table-bordered table-striped table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-center">No</th>
                                            <!-- <th>Plan</th> -->
                                            <th class="text-center">Shift</th>
                                            <th class="text-center">Tanggal</th>
                                            <th class="text-center">Jam</th>
                                            <th>Kode Produksi</th>
                                            <th>Nama Produk</th>
                                            <th>Nama Emulsi</th>
                                            <th>Jumlah Proses Emulsi</th>
                                            <th>Dibuat Oleh</th>
                                            <!-- <th>Nama Bahan</th>
                                            <th class="text-center">Berat Bahan (gram)</th>
                                            <th class="text-center">Suhu (°C)</th> -->
                                            <!-- <th>Hasil Emulsi</th> -->
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBody">
                                        @php $itemNumber = ($data->currentPage() - 1) * $data->perPage() + 1; @endphp
                                        @forelse($data as $i => $item)
                                            @php 
                                                $suhus = $item->suhuEmulsi->count() > 0 ? $item->suhuEmulsi : [null];
                                                $rowspan = count($suhus); 
                                            @endphp
                                            @foreach($suhus as $j => $suhu)
                                                <tr>
                                                    @if($j == 0)
                                                        <td rowspan="{{ $rowspan }}" class="text-center align-middle">
                                                            <span>{{ $itemNumber }}</span>
                                                        </td>
                                                        <td rowspan="{{ $rowspan }}" class="align-middle">
                                                            @if($item->shift_id == 1)
                                                                <span class="badge bg-primary">Shift {{ $item->shift->shift ?? 'Shift 1' }}</span>
                                                            @elseif($item->shift_id == 2)
                                                                <span class="badge bg-success">Shift {{ $item->shift->shift ?? 'Shift 2' }}</span>
                                                            @else
                                                                <span class="badge bg-secondary">Shift {{ $item->shift->shift ?? 'Shift ' . $item->shift_id }}</span>
                                                            @endif
                                                        </td>
                                                        <td rowspan="{{ $rowspan }}" class="text-center align-middle">
                                                            <span class="badge badge-secondary">
                                                                @php
                                                                    $userRole = auth()->user()->id_role ?? null;
                                                                    $showTime = in_array($userRole, [1, 2, 5]); // superadmin, admin, spv
                                                                    $format = $showTime ? 'd-m-Y H:i:s' : 'd-m-Y';
                                                                @endphp
                                                                {{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format($format) : '-' }}
                                                            </span>
                                                        </td>
                                                        <td rowspan="{{ $rowspan }}" class="text-center align-middle">
                                                            <span>
                                                                {{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}
                                                            </span>
                                                        </td>
                                                        <td rowspan="{{ $rowspan }}" class="align-middle">
                                                            <span>{{ $item->kode_produksi_emulsi }}</span>
                                                        </td>
                                                        <td rowspan="{{ $rowspan }}" class="align-middle">
                                                            <span>{{ $item->produk->nama_produk ?? '-' }}</span>
                                                        </td>
                                                        <td rowspan="{{ $rowspan }}" class="align-middle">
                                                            <span>{{ $item->nama_emulsi->nama_emulsi ?? '-' }}</span>
                                                        </td>
                                                        <td rowspan="{{ $rowspan }}" class="align-middle">
                                                            <span>{{ $item->nomor_emulsi->nomor_emulsi ?? '-' }}</span>
                                                        </td>
                                                        <td rowspan="{{ $rowspan }}" class="align-middle">
                                                            <span>{{ $item->user->name ?? '-' }}</span>
                                                        </td>
                                                    @endif
                                                    
                                                    <!-- <td>{{ optional(optional($suhu)->bahanEmulsi)->nama_rm ?? '-' }}</td>
                                                    <td class="text-center">
                                                        <span>{{ optional(optional($suhu)->bahanEmulsi)->berat_rm ?? '-' }} gram</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span>{{ optional($suhu)->suhu ?? '-' }}°C</span>
                                                    </td> -->
                                                    
                                                    @if($j == 0)
                                                        <!-- <td rowspan="{{ $rowspan }}" class="align-middle">
                                                            <span>{{ $item->hasil_emulsi }}</span>
                                                        </td> -->
                                                        <td rowspan="{{ $rowspan }}" class="text-center align-middle">
                                                            <div class="btn-vertical">
                                                                <!-- Tombol CRUD -->
                                                                <div class="mb-1">
                                                                    <a href="{{ route('persiapan-bahan-emulsi.show', $item->uuid) }}" 
                                                                        class="btn btn-primary btn-sm" title="Lihat Detail">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
@if(auth()->user()->hasPermissionTo('edit-persiapan-bahan-emulsi'))
                                                                    <a href="{{ route('persiapan-bahan-emulsi.edit', $item->uuid) }}" 
                                                                        class="btn btn-warning btn-sm" title="Edit Data">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                    @endif
<!-- <a href="{{ route('persiapan-bahan-emulsi.show', $item->uuid) }}" 
                                                                        class="btn btn-secondary btn-sm" title="Lihat Detail">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a> -->
@if(auth()->user()->hasPermissionTo('view-persiapan-bahan-emulsi'))

                                                                    <a href="{{ route('persiapan-bahan-emulsi.logs', $item->uuid) }}" 
                                                                        class="btn btn-info btn-sm" 
                                                                        title="History">
                                                                        <i class="fas fa-history"></i>
                                                                    </a>
                                                                    @endif
@if(auth()->user()->hasPermissionTo('delete-persiapan-bahan-emulsi'))
<form action="{{ route('persiapan-bahan-emulsi.destroy', $item->uuid) }}" 
                                                                        method="POST" style="display:inline;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-danger btn-sm" 
                                                                                onclick="return confirm('Yakin ingin menghapus data ini?')" 
                                                                                title="Hapus Data">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                    </form>
                                                                @endif
</div>

                                                                <!-- Tombol Persetujuan berdasarkan Role -->
                                                                @php
                                                                    $userRole = auth()->user()->id_role ?? null;
                                                                @endphp

                                                                <!-- Role-Based Button Display -->
                                                                <div class="btn-group-vertical mb-1" role="group">
                                                                    @if(in_array($userRole, [1, 5]))
                                                                        <!-- Role 1 dan 5: Tampilkan semua tombol dengan QC yang bisa diklik -->
                                                                        <button type="button" 
                                                                                class="btn btn-sm {{ $item->approved_by_qc ? 'btn-success' : 'btn-outline-success' }} approve-btn" 
                                                                                data-id="{{ $item->uuid }}" 
                                                                                data-type="qc"
                                                                                title="Disetujui oleh QC"
                                                                                {{ $item->approved_by_qc ? 'disabled' : '' }}>
                                                                            <i class="fas {{ $item->approved_by_qc ? 'fa-check-circle' : 'fa-check' }}"></i> QC
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

                                                                    @else
                                                                        <!-- Role lain: Tampilkan semua tombol sebagai read-only -->
                                                                        <button type="button" 
                                                                                class="btn btn-sm {{ $item->approved_by_qc ? 'btn-success' : 'btn-secondary' }}" 
                                                                                title="{{ $item->approved_by_qc ? 'Sudah disetujui QC' : 'Menunggu persetujuan QC' }}"
                                                                                disabled>
                                                                            <i class="fas {{ $item->approved_by_qc ? 'fa-check-circle' : 'fa-clock' }}"></i> QC
                                                                        </button>
                                                                        <button type="button" 
                                                                                class="btn btn-sm {{ $item->approved_by_produksi ? 'btn-primary' : 'btn-secondary' }}" 
                                                                                title="{{ $item->approved_by_produksi ? 'Sudah disetujui Produksi' : 'Menunggu persetujuan Produksi' }}"
                                                                                disabled>
                                                                            <i class="fas {{ $item->approved_by_produksi ? 'fa-check-circle' : 'fa-clock' }}"></i> Produksi
                                                                        </button>
                                                                        <button type="button" 
                                                                                class="btn btn-sm {{ $item->approved_by_spv ? 'btn-dark' : 'btn-secondary' }}" 
                                                                                title="{{ $item->approved_by_spv ? 'Sudah disetujui SPV' : 'Menunggu persetujuan SPV' }}"
                                                                                disabled>
                                                                            <i class="fas {{ $item->approved_by_spv ? 'fa-check-circle' : 'fa-clock' }}"></i> SPV
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
                                                    @endif
                                                </tr>
                                            @endforeach
                                            @php $itemNumber++; @endphp
                                        @empty
                                            <tr>
                                                <td colspan="13" class="text-center">
                                                    <div class="py-4">
                                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                        <h5 class="text-muted">Belum ada data</h5>
                                                        <p class="text-muted">Silakan tambah data persiapan bahan emulsi terlebih dahulu.</p>
                                    @if(auth()->user()->hasPermissionTo('create-persiapan-bahan-emulsi'))
                                                        <a href="{{ route('persiapan-bahan-emulsi.create') }}" class="btn btn-primary">
                                                            <i class="fas fa-plus"></i> Tambah Data
                                                        </a>
                                                    @endif
</div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- Pagination -->
                        <div class="card-footer d-flex justify-content-center">
                            {{ $data->links('pagination.simple') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- Modal Bulk Print -->
<div class="modal fade" id="bulkPrintModal" tabindex="-1" role="dialog" aria-labelledby="bulkPrintModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkPrintModalLabel">Cetak PDF Berdasarkan Filter</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="bulkExportForm" action="{{ route('persiapan-bahan-emulsi.bulk-export-pdf') }}" method="POST" data-bulk-form="true">
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
                                    <option value="{{ $shift->id }}" {{ request('shift_id') == $shift->id ? 'selected' : '' }}>{{ $shift->shift }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="filterProduk">Produk</label>
                        <select class="form-control" id="id_produk_select_emulsi" name="id_produk">
                            <option value="">Semua Produk</option>
                            @foreach($cachedProduk as $produk)
                                    <option value="{{ $produk->id }}" {{ request('id_produk') == $produk->id ? 'selected' : '' }}>{{ $produk->nama_produk }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="bulkKodeForm">Kode Form</label>
                        <input type="text" class="form-control" id="bulkKodeForm" name="kode_form" value="QF 05/00" placeholder="Masukkan kode form" required>
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
                url: '{{ route("persiapan-bahan-emulsi.approve", ":id") }}'.replace(':id', id),
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
                            'produksi': '<i class="fas fa-check"></i> Fm/Fl PRODUKSI',
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
                        'produksi': '<i class="fas fa-check"></i>FM/FL PRODUKSI',
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