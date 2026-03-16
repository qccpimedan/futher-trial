@extends('layouts.app')

@section('title', 'Data Produk YUM')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Data KPI Produk YUM</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            <li class="breadcrumb-item active">KPI Produk YUM</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list mr-2"></i>Daftar KPI Produk YUM
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-danger btn-sm mr-2" data-toggle="modal" data-target="#exportPdfModal">
                                <i class="fas fa-file-pdf"></i> Cetak PDF Filter
                            </button>
                                    @if(auth()->user()->hasPermissionTo('create-produk-yum'))
                            <a href="{{ route('produk-yum.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Tambah Data
                            </a>
                        @endif
</div>
                    </div>
                    <div class="card-body">
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

                        <!-- Form Pencarian Server-Side -->
                        <div class="row mb-3 mt-3">
                            <div class="col-md-6">
                                <form method="GET" action="{{ route('produk-yum.index') }}">
                                    <div class="input-group input-group-sm" style="width: 300px;">
                                        <input type="text" class="form-control" name="search" placeholder="Cari nama produk atau kode produksi" value="{{ $search ?? '' }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="submit">
                                                <i class="fas fa-search"></i>
                                            </button>
                                            @if(!empty($search))
                                                <a class="btn btn-outline-danger" href="{{ route('produk-yum.index') }}">
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
                                    <form method="GET" action="{{ route('produk-yum.index') }}">
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
                                        Data Produk YUM
                                    @endif
                                </small>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped text-center"  style="white-space: nowrap">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Jam</th>
                                        <th>Shift</th>
                                        <th>Produk</th>
                                        <th>Kode Produksi</th>
                                        <th>Data Pack</th>
                                        <th>Dibuat Oleh</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($produkYums as $index => $item)
                                        <tr>
                                            <td>{{ $produkYums->firstItem() + $index }}</td>
                                            <td>
                                                @if(auth()->user()->id_role == 2 || auth()->user()->id_role == 3)
                                                    <span class="badge badge-secondary">{{ $item->tanggal ? $item->tanggal->format('d/m/Y') : '-' }}</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ $item->tanggal ? $item->tanggal->format('d/m/Y H:i:s') : '-' }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</span>
                                            </td>
                                            <td><span class="badge badge-primary">Shift {{ $item->shift->shift ?? '-' }}</span></td>
                                            <td>{{ $item->produk->nama_produk ?? '-' }}</td>
                                            <td>{{ $item->kode_produksi }}</td>
                                            <td>{{ $item->dataBag->std_bag ?? '-' }}</td>
                                            <td>{{ $item->user->name ?? '-' }}</td>
                                            <td class="text-center">
                                                <div class="btn-vertical">
                                                    <!-- <a href="{{ route('produk-yum.show', $item->uuid) }}" 
                                                       class="btn btn-info btn-sm" title="Lihat">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
@if(auth()->user()->hasPermissionTo('edit-produk-yum'))
                                                    <a href="{{ route('produk-yum.edit', $item->uuid) }}" 
                                                       class="btn btn-warning btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @endif
@if(auth()->user()->hasPermissionTo('view-produk-yum'))
<a href="{{ route('produk-yum.logs', $item->uuid) }}" 
                                                       class="btn btn-info btn-sm" title="Lihat Riwayat Perubahan">
                                                        <i class="fas fa-history"></i>
                                                    </a>
                                                    @endif
@if(auth()->user()->hasPermissionTo('delete-produk-yum'))
<form action="{{ route('produk-yum.destroy', $item->uuid) }}" 
                                                          method="POST" style="display: inline-block;"
                                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form> @endif
-->
                                                    <x-action-buttons :item="$item" route-prefix="produk-yum" :show-logs="false" />
                                                    
                                                    <!-- Approval Buttons -->
                                                    @php
                                                        $userRole = auth()->user()->id_role ?? null;
                                                        $hasApprovalColumns = true; // Assume approval columns exist for produk_yum
                                                    @endphp

                                                    <!-- Role-Based Button Display -->
                                                    @if($hasApprovalColumns)
                                                    <div class="btn-vertical mb-1 mt-1">
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
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">Tidak ada data</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $produkYums->appends(['search' => $search ?? '', 'per_page' => $perPage ?? ''])->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

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
                <form id="bulkExportForm" action="{{ route('produk-yum.bulk-export-pdf') }}" method="POST" data-bulk-form="true">
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
                        <label for="filterProduk">Produk</label>
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
                        <input type="text" class="form-control" id="bulkKodeForm" name="kode_form" value="QF 16/00" placeholder="Masukkan kode form" required>
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

@push('styles')
<style>
.btn-vertical {
    display: flex;
    flex-direction: column;
    gap: 1px;
    min-width: 140px;
    align-items: stretch;
}

.btn-vertical .btn {
    margin-bottom: 1px;
    font-size: 10px;
    padding: 6px 8px;
    border-radius: 4px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.btn-vertical .btn-block {
    width: 100%;
    display: block;
}

/* Approval button colors exactly matching chillroom */
.btn-vertical .btn-success {
    background-color: #28a745 !important;
    border-color: #28a745 !important;
    color: white !important;
}

.btn-vertical .btn-primary {
    background-color: #007bff !important;
    border-color: #007bff !important;
    color: white !important;
}

.btn-vertical .btn-dark {
    background-color: #343a40 !important;
    border-color: #343a40 !important;
    color: white !important;
}

.btn-vertical .btn-secondary {
    background-color: #6c757d !important;
    border-color: #6c757d !important;
    color: white !important;
}

.btn-vertical .btn:disabled {
    opacity: 1 !important;
    cursor: not-allowed;
}

.btn-vertical .btn:not(:disabled):hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Action buttons styling */
.btn-vertical .btn-warning {
    background-color: #ffc107 !important;
    border-color: #ffc107 !important;
    color: #212529 !important;
}

.btn-vertical .btn-info {
    background-color: #17a2b8 !important;
    border-color: #17a2b8 !important;
    color: white !important;
}

.btn-vertical .btn-danger {
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
    color: white !important;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Handle approval button clicks
    $('.approve-btn').click(function() {
        const id = $(this).data('id');
        const type = $(this).data('type');
        const button = $(this);
        
        // Confirmation dialog
        if (confirm(`Apakah Anda yakin ingin menyetujui data ini sebagai ${type.toUpperCase()}?`)) {
            // Disable button and show loading state
            button.prop('disabled', true);
            const originalText = button.html();
            button.html('<i class="fas fa-spinner fa-spin"></i> Processing...');
            
            // AJAX request
            $.ajax({
                url: `/qc-sistem/produk-yum/${id}/approve`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    type: type
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        alert(response.message);
                        
                        // Reload page to update status
                        location.reload();
                    } else {
                        alert(response.message);
                        button.prop('disabled', false);
                        button.html(originalText);
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    alert(response.message || 'Terjadi kesalahan saat melakukan approval');
                    button.prop('disabled', false);
                    button.html(originalText);
                }
            });
        }
    });
});
</script>
@endpush

