@extends('layouts.app')

@php
use Illuminate\Support\Facades\Schema;
@endphp

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Data Ketidaksesuaian Benda Asing</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Data Ketidaksesuaian Benda Asing</li>
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
                                    <i class="fas fa-exclamation-triangle text-warning mr-2"></i>
                                    Data Ketidaksesuaian Benda Asing
                                </h3>
                                <div>
                                    <button type="button" class="btn btn-danger btn-sm mr-2" data-toggle="modal" data-target="#exportPdfModal">
                                        <i class="fas fa-file-pdf"></i> Cetak PDF Filter
                                    </button>
                                    @if(auth()->user()->hasPermissionTo('create-ketidaksesuaian-benda-asing'))
                                    <a href="{{ route('ketidaksesuaian-benda-asing.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Tambah Data
                                    </a>
                                @endif
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

                            @if($ketidaksesuaianBendaAsing->isEmpty())
                                <div class="alert alert-info text-center">
                                    <i class="fas fa-info-circle"></i>
                                    Belum ada data ketidaksesuaian benda asing yang tersedia.
                                </div>
                                @else
                                    <div class="table-responsive">
                                        <!-- Form Pencarian Server-Side -->
                                        <div class="row mb-3 mt-3">
                                            <div class="col-md-4 offset-md-8">
                                                <form action="{{ route('ketidaksesuaian-benda-asing.index') }}" method="GET">
                                                    <div class="input-group">
                                                        <input type="text" name="search" class="form-control" placeholder="Cari Kode Produksi" value="{{ request('search') }}">
                                                        <div class="input-group-append">
                                                            <button class="btn btn-outline-secondary" type="submit">
                                                                <i class="fas fa-search"></i> Cari
                                                            </button>
                                                            @if(request('search'))
                                                                <a href="{{ route('ketidaksesuaian-benda-asing.index') }}" class="btn btn-outline-danger" title="Clear Search">
                                                                    <i class="fas fa-times"></i>
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                        <table class="table table-bordered table-striped" style="white-space:nowrap;">
                                        <thead class="thead-light">
                                            <tr>
                                                <th class="text-center">No</th>
                                                <!-- <th class="text-center">Plan</th> -->
                                                <th class="text-center">Shift</th>
                                                <th class="text-center">Tanggal</th>
                                                <th class="text-center">Jam</th>
                                                <th class="text-center">Produk</th>
                                                <th class="text-center">Kode Produksi</th>
                                                <th class="text-center">Jenis Kontaminan</th>
                                                <th class="text-center">Jumlah Terdampak</th>
                                                <th class="text-center">Tahapan</th>
                                                <th class="text-center">Dokumentasi</th>
                                                <th class="text-center">Aksi</th>
                                        </thead>
                                        <tbody>
                                            @foreach($ketidaksesuaianBendaAsing as $index => $item)
                                                <tr>
                                                    <td class="text-center">{{ $ketidaksesuaianBendaAsing->firstItem() + $loop->index }}</td>
                                                    <td class="text-center"><span class="badge badge-secondary">Shift {{ $item->shift->shift ?? '-' }}</span></td>
                                                    <!-- <td class="text-center">{{ $item->plan->nama_plan ?? '-' }}</td> -->
                                                   <td class="text-center">
                                                        @if(auth()->user()->id_role == 2 || auth()->user()->id_role == 3)
                                                            <span class="badge badge-info">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</span>
                                                        @else
                                                            <span class="badge badge-info">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y H:i:s') }}</span>
                                                        @endif
                                                    </td >
                                                    <td class="text-center">
                                                        <span class="badge badge-secondary">{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="text-truncate" style="max-width: 150px; display: inline-block;" title="{{ $item->produk->nama_produk ?? '-' }}">
                                                            {{ Str::limit($item->produk->nama_produk ?? '-', 30) }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">{{ $item->kode_produksi }}</td>
                                                    <td class="text-center">
                                                        <span class="text-truncate" style="max-width: 150px; display: inline-block;" title="{{ $item->jenis_kontaminan }}">
                                                            {{ Str::limit($item->jenis_kontaminan, 30) }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">{{ number_format($item->jumlah_produk_terdampak) }}</td>
                                                    <td class="text-center">
                                                        <span class="text-truncate" style="max-width: 150px; display: inline-block;" title="{{ $item->tahapan }}">
                                                            {{ Str::limit($item->tahapan, 30) }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        @if($item->dokumentasi)
                                                            <span class="badge badge-success">
                                                                <i class="fas fa-check"></i> Ada
                                                            </span>
                                                        @else
                                                            <span class="badge badge-warning">
                                                                <i class="fas fa-times"></i> Tidak Ada
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="btn-vertical">
                                                            <!-- Tombol Edit, Log, dan Delete -->
                                                            <!-- <div class="mb-1">
@if(auth()->user()->hasPermissionTo('edit-ketidaksesuaian-benda-asing'))
                                                                <a href="{{ route('ketidaksesuaian-benda-asing.edit', $item->uuid) }}" 
                                                                   class="btn btn-sm btn-warning" 
                                                                   title="Edit Data">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                @endif
<a href="{{ route('ketidaksesuaian-benda-asing.show', $item->uuid) }}" 
                                                                   class="btn btn-sm btn-info" 
                                                                   title="Lihat Detail">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
@if(auth()->user()->hasPermissionTo('delete-ketidaksesuaian-benda-asing'))
                                                                <form action="{{ route('ketidaksesuaian-benda-asing.destroy', $item->uuid) }}" 
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
@if(auth()->user()->hasPermissionTo('view-ketidaksesuaian-benda-asing'))
                                                                <a href="{{ route('ketidaksesuaian-benda-asing.logs', $item->uuid) }}" 
                                                                class="btn btn-sm btn-secondary" 
                                                                title="Lihat Riwayat Perubahan">
                                                                    <i class="fas fa-history"></i>
                                                                </a>
                                                            @endif
</div> -->
                                                            <x-action-buttons :item="$item" route-prefix="ketidaksesuaian-benda-asing" />

                                                            <!-- Tombol Persetujuan berdasarkan Role -->
                                                            @php
                                                                $userRole = auth()->user()->id_role ?? null;
                                                                $hasApprovalColumns = Schema::hasColumn('ketidaksesuaian_benda_asing', 'approved_by_qc');
                                                            @endphp

                                                            <!-- Role-Based Button Display -->
                                                            @if($hasApprovalColumns)
                                                            <div class="btn-group-vertical" role="group">
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
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Menampilkan Navigasi Pagination -->
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $ketidaksesuaianBendaAsing->appends(request()->query())->links('pagination::bootstrap-4') }}
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
<div class="modal fade" id="exportPdfModal" tabindex="-1" role="dialog" aria-labelledby="exportPdfModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportPdfModalLabel">Cetak PDF Berdasarkan Filter</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="bulkExportForm" action="{{ route('ketidaksesuaian-benda-asing.bulk-export-pdf') }}" method="POST" data-bulk-form="true">
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
                        <input type="text" class="form-control" id="bulkKodeForm" name="kode_form" value="QF 23/00" placeholder="Masukkan kode form" required>
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
                url: '{{ route("ketidaksesuaian-benda-asing.approve", ":id") }}'.replace(':id', id),
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