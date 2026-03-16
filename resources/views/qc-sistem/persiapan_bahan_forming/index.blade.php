@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-industry text-primary"></i> Persiapan Bahan Forming & Non Forming</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item active">Persiapan Bahan Forming & Non Forming</li>
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
                            <h3 class="card-title"><i class="fas fa-list"></i> Data Persiapan Bahan Forming</h3>
                            <div class="card-tools">
                                <div class="btn-group">
                                    <a href="#" 
                                       class="btn btn-danger btn-sm" 
                                       title="Cetak PDF Berdasarkan Filter"
                                       data-toggle="modal" 
                                       data-target="#bulkPrintModal">
                                        <i class="fas fa-file-pdf"></i> Cetak PDF Filter
                                    </a>
                                    <!-- <a href="{{ route('persiapan-bahan-forming.export-pdf') }}" class="btn btn-danger btn-sm" title="Export PDF">
                                        <i class="fas fa-file-pdf"></i> Cetak PDF
                                    </a> -->
                                    <!-- <a href="{{ route('persiapan-bahan-forming.export-excel') }}" class="btn btn-success btn-sm" title="Export Excel">
                                        <i class="fas fa-file-excel"></i> Excel
                                    </a> -->
                                </div>
                                    @if(auth()->user()->hasPermissionTo('create-persiapan-bahan-forming'))
                                <a href="{{ route('persiapan-bahan-forming.create') }}" class="btn btn-primary btn-sm">
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
                            
                            <!-- Search and Filter Controls -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="input-group input-group-sm" style="width: 300px;">
                                        <input type="text" id="searchInput" class="form-control" 
                                               placeholder="Cari data..." value="{{ $search }}"
                                               title="Cari berdasarkan nama produk, plan, formula, bahan forming, kode produksi, kondisi, atau catatan">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                                                <i class="fas fa-search"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" type="button" id="clearBtn" title="Hapus pencarian" style="display: none;">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="float-right">
                                        <select id="perPageSelect" class="form-control form-control-sm" style="width: 80px;">
                                            <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Data Info -->
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <small class="text-muted" id="dataInfo">
                                        @if($search)
                                            Hasil pencarian: "<strong>{{ $search }}</strong>"
                                        @else
                                            Data Persiapan Bahan Forming & Non Forming
                                        @endif
                                    </small>
                                </div>
                            </div>
                            
                            <div class="table-responsive">
                                <table style="white-space: nowrap;" class="text-center table table-bordered table-hover table-striped">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Jenis</th>
                                            <th>Shift</th>
                                            <th>Tanggal</th>
                                            <th>Jam</th>
                                            <th>Nama Produk</th>
                                            <th>Kode Produksi</th>
                                            <th>Nomor Formula</th>
                                            <th>Kondisi</th>
                                            <th>Catatan</th>
                                            <th>Dibuat Oleh</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBody">
                                        @forelse($records as $i => $item)
                                            <tr>
                                                <td class="text-center align-middle">
                                                    <span>{{ ($records->currentPage() - 1) * $records->perPage() + $i + 1 }}</span>
                                                </td>
                                                <td class="align-middle">
                                                    <span class="badge {{ $item->jenis === 'Forming' ? 'bg-primary' : 'bg-secondary' }}">{{ $item->jenis }}</span>
                                                </td>
                                                <td class="align-middle">
                                                    @if(($item->shift_id ?? null) == 1)
                                                        <span class="badge bg-primary">Shift {{ $item->shift ?? 'Shift 1' }}</span>
                                                    @elseif(($item->shift_id ?? null) == 2)
                                                        <span class="badge bg-success">Shift {{ $item->shift ?? 'Shift 2' }}</span>
                                                    @else
                                                        <span class="badge bg-secondary">Shift {{ $item->shift ?? ($item->shift_id ?? '-') }}</span>
                                                    @endif
                                                </td>
                                                <td class="align-middle">
                                                    <span class="badge badge-secondary">
                                                        @php
                                                            $userRole = auth()->user()->id_role ?? null;
                                                            $showTime = in_array($userRole, [1, 2, 5]);
                                                            $format = $showTime ? 'd-m-Y H:i:s' : 'd-m-Y';
                                                        @endphp
                                                        {{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format($format) : '-' }}
                                                    </span>
                                                </td>
                                                <td class="align-middle">
                                                    <span>{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</span>
                                                </td>
                                                <td class="align-middle">
                                                    <span>{{ $item->nama_produk ?? '-' }}</span>
                                                </td>
                                                <td class="align-middle">
                                                    <span>{{ $item->kode_produksi ?? '-' }}</span>
                                                </td>
                                                <td class="align-middle">
                                                    <span>{{ $item->nomor_formula ?? '-' }}</span>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <span>{{ $item->kondisi ?? '-' }}</span>
                                                </td>
                                                <td class="align-middle">
                                                    <small class="text-muted">{{ Str::limit($item->catatan, 50) ?? '-' }}</small>
                                                </td>
                                                <td class="align-middle">
                                                    <span>{{ $item->dibuat_oleh ?? '-' }}</span>
                                                </td>
                                                <td class="text-center align-middle">
                                                    @if($item->jenis === 'Forming')
                                                        <a href="{{ route('persiapan-bahan-forming.show', $item->uuid) }}" class="btn btn-primary btn-sm" title="Lihat Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
@if(auth()->user()->hasPermissionTo('edit-persiapan-bahan-forming'))
                                                        <a href="{{ route('persiapan-bahan-forming.edit', $item->uuid) }}" class="btn btn-warning btn-sm" title="Edit Data">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        @endif
<a href="{{ route('persiapan-bahan-forming.export-pdf', $item->uuid) }}" class="btn btn-danger btn-sm" title="Cetak PDF">
                                                            <i class="fas fa-file-pdf"></i>
                                                        </a>
@if(auth()->user()->hasPermissionTo('view-persiapan-bahan-forming'))
                                                        <a href="{{ route('persiapan-bahan-forming.logs', $item->uuid) }}" class="btn btn-info btn-sm" title="Lihat Riwayat Perubahan">
                                                            <i class="fas fa-history"></i>
                                                        </a>
                                                        @endif
@if(auth()->user()->hasPermissionTo('delete-persiapan-bahan-forming'))
<form action="{{ route('persiapan-bahan-forming.destroy', $item->uuid) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')" title="Hapus Data">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
@else
                                                        <a href="{{ route('persiapan-bahan-non-forming.show', $item->uuid) }}" class="btn btn-primary btn-sm" title="Lihat Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
@if(auth()->user()->hasPermissionTo('edit-persiapan-bahan-non-forming'))
                                                        <a href="{{ route('persiapan-bahan-non-forming.edit', $item->uuid) }}" class="btn btn-warning btn-sm" title="Edit Data">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        @endif
<a href="{{ route('persiapan-bahan-non-forming.export-pdf', $item->uuid) }}" class="btn btn-danger btn-sm" title="Cetak PDF">
                                                            <i class="fas fa-file-pdf"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="12" class="text-center">
                                                    <div class="py-4">
                                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                        @if($search)
                                                            <h5 class="text-muted">Tidak ada data yang ditemukan</h5>
                                                            <p class="text-muted">Tidak ada data yang cocok dengan pencarian "<strong>{{ $search }}</strong>".</p>
                                                            <a href="{{ route('persiapan-bahan-forming.index') }}" class="btn btn-secondary mr-2">
                                                                <i class="fas fa-times"></i> Hapus Filter
                                                            </a>
                                                        @else
                                                            <h5 class="text-muted">Belum ada data</h5>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="card-footer d-flex justify-content-center">
                        <div id="paginationContainer">
                            {{ $records->links('pagination.simple') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal tunggal forming -->
<!-- <div class="modal fade" id="kodeFormModal" tabindex="-1" role="dialog" aria-labelledby="kodeFormModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="kodeFormModalLabel">Input Kode Form</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="kodeForm">Kode Form</label>
                        <input type="text" class="form-control" id="kodeForm" placeholder="Masukkan kode form">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Batal</button>
                <button type="button" class="btn btn-primary" id="simpanKodeBtn"><i class="fas fa-download"></i> Simpan & Download PDF</button>
            </div>
        </div>
    </div>
</div> -->

<!-- Modal Bulk Print Forming dan Non Forming-->
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
                <form id="bulkExportForm" action="{{ route('persiapan-bahan-forming.bulk-export-pdf') }}" method="POST" data-bulk-form="true">
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
                        <select class="form-control" id="id_produk_select_forming" name="produk_id">
                            <option value="">Semua Produk</option>
                            @foreach($cachedProduk as $produk)
                                    <option value="{{ $produk->id }}" {{ request('produk_id') == $produk->id ? 'selected' : '' }}>{{ $produk->nama_produk }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="bulkKodeForm">Kode Form</label>
                        <input type="text" class="form-control" id="bulkKodeForm" name="kode_form"  value="QF 04/00" placeholder="Masukkan kode form" required>
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
                url: '{{ route("persiapan-bahan-forming.approve", ":id") }}'.replace(':id', id),
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
