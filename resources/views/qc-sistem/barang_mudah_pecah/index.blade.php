@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Data Barang Mudah Pecah</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Data Barang Mudah Pecah</li>
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
                                    <i class="fas fa-glass-whiskey text-primary mr-2"></i>
                                    Data Barang Mudah Pecah
                                </h3>
                                <div class="card-tools d-flex">
                                    <form action="{{ route('barang-mudah-pecah.index') }}" method="GET" class="mr-2">
                                        <div class="input-group input-group-sm" style="width: 250px;">
                                            <input type="text" name="search" class="form-control" placeholder="Cari Barang, Area atau Tanggal" value="{{ $search ?? '' }}">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-default">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                                @if($search)
                                                    <a href="{{ route('barang-mudah-pecah.index') }}" class="btn btn-default">
                                                        <i class="fas fa-times text-danger"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </form>
                                    @if(auth()->user()->hasPermissionTo('create-barang-mudah-pecah'))
                                    <a href="{{ route('barang-mudah-pecah.create') }}" class="btn btn-sm btn-primary mr-2">
                                        <i class="fas fa-plus"></i> Tambah Data
                                    </a>
                                    @endif
                                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#bulkExportModal">
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

                            @if(isset($groupedBarangMudahPecah) && count($groupedBarangMudahPecah))
                                <div class="table-responsive">
                                    <table class="table text-center table-bordered table-striped table-hover" style="white-space: nowrap;">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>No</th>
                                                <th class="text-center">Tanggal</th>
                                                <th class="text-center">Shift</th>
                                                <th class="text-center">Waktu</th>
                                                <th class="text-center">Area</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($groupedBarangMudahPecah as $groupKey => $group)
                                                @php
                                                    $first = $group->first();
                                                    $detailId = 'detail_' . md5($groupKey);
                                                    $userRole = auth()->user()->id_role ?? null;
                                                    $showTime = in_array($userRole, [1, 2, 5]); // superadmin, admin, spv
                                                    $format = $showTime ? 'd-m-Y H:i:s' : 'd-m-Y';
                                                @endphp
                                                <tr>
                                                    <td>{{ $groupedBarangMudahPecah->firstItem() + $loop->index }}</td>
                                                    <td>
                                                        <span class="badge badge-secondary">
                                                            {{ $first && $first->tanggal ? \Carbon\Carbon::parse($first->tanggal)->format($format) : '-' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-secondary">{{ $first->shift->shift ?? '-' }}</span>
                                                    </td>
                                                    <td>
                                                        {{ $first && $first->jam ? \Carbon\Carbon::parse($first->jam)->format('H:i') : '-' }}
                                                    </td>
                                                    <td>{{ $first->area->area ?? '-' }}</td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            @if($first)
                                                                <!-- <button type="button" class="btn btn-secondary btn-sm btn-toggle-detail" data-detail-id="{{ $detailId }}" title="Detail">
                                                                    <i class="fas fa-list"></i>
                                                                </button> -->
                                                                @if(auth()->user()->hasPermissionTo('view-barang-mudah-pecah'))
                                                                <a href="{{ route('barang-mudah-pecah.show', $first->uuid) }}" class="btn btn-info btn-sm" title="Show">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                @endif
                                                                <x-action-buttons :item="$first" route-prefix="barang-mudah-pecah" :show-view="false"/>
                                                            @endif
                                                        </div>

                                                        @php
                                                            $hasApprovalColumns = Schema::hasColumn('barang_mudah_pecah', 'approved_by_qc');
                                                        @endphp

                                                        @if($first && $hasApprovalColumns)
                                                            <div class="btn-group-vertical mt-1" role="group">
                                                                @if(in_array($userRole, [1, 5]))
                                                                    <button type="button" 
                                                                            class="btn btn-sm {{ isset($first->approved_by_qc) && $first->approved_by_qc ? 'btn-success' : 'btn-outline-success' }} approve-btn" 
                                                                            data-id="{{ $first->uuid }}" 
                                                                            data-type="qc"
                                                                            title="Disetujui oleh QC"
                                                                            {{ isset($first->approved_by_qc) && $first->approved_by_qc ? 'disabled' : '' }}>
                                                                        <i class="fas {{ isset($first->approved_by_qc) && $first->approved_by_qc ? 'fa-check-circle' : 'fa-check' }}"></i> QC
                                                                    </button>
                                                                    <button type="button" 
                                                                            class="btn btn-sm {{ $first->approved_by_produksi ? 'btn-primary' : 'btn-secondary' }}" 
                                                                            title="{{ $first->approved_by_produksi ? 'Sudah disetujui Produksi' : 'Menunggu persetujuan Produksi' }}"
                                                                            disabled>
                                                                        <i class="fas {{ $first->approved_by_produksi ? 'fa-check-circle' : 'fa-clock' }}"></i> FM/FL PRODUKSI
                                                                    </button>
                                                                    <button type="button" 
                                                                            class="btn btn-sm {{ $first->approved_by_spv ? 'btn-dark' : 'btn-secondary' }}" 
                                                                            title="{{ $first->approved_by_spv ? 'Sudah disetujui SPV' : 'Menunggu persetujuan SPV' }}"
                                                                            disabled>
                                                                        <i class="fas {{ $first->approved_by_spv ? 'fa-check-circle' : 'fa-clock' }}"></i> SPV
                                                                    </button>

                                                                @elseif($userRole == 2)
                                                                    <button type="button" 
                                                                            class="btn btn-sm {{ $first->approved_by_produksi ? 'btn-primary' : ($first->approved_by_qc ? 'btn-outline-primary' : 'btn-secondary') }} {{ $first->approved_by_qc && !$first->approved_by_produksi ? 'approve-btn' : '' }}" 
                                                                            data-id="{{ $first->uuid }}" 
                                                                            data-type="produksi"
                                                                            title="{{ !$first->approved_by_qc ? 'Menunggu persetujuan QC terlebih dahulu' : ($first->approved_by_produksi ? 'Sudah disetujui Produksi' : 'Disetujui oleh Produksi') }}"
                                                                            {{ !$first->approved_by_qc || $first->approved_by_produksi ? 'disabled' : '' }}>
                                                                        <i class="fas {{ $first->approved_by_produksi ? 'fa-check-circle' : (!$first->approved_by_qc ? 'fa-clock' : 'fa-check') }}"></i> Disetujui oleh Produksi
                                                                    </button>

                                                                @elseif($userRole == 3)
                                                                    <button type="button" 
                                                                            class="btn btn-sm {{ $first->approved_by_qc ? 'btn-success' : 'btn-outline-success' }} approve-btn" 
                                                                            data-id="{{ $first->uuid }}" 
                                                                            data-type="qc"
                                                                            title="Disetujui oleh QC"
                                                                            {{ $first->approved_by_qc ? 'disabled' : '' }}>
                                                                        <i class="fas {{ $first->approved_by_qc ? 'fa-check-circle' : 'fa-check' }}"></i> Disetujui oleh QC
                                                                    </button>

                                                                @elseif($userRole == 4)
                                                                    <button type="button" 
                                                                            class="btn btn-sm {{ $first->approved_by_spv ? 'btn-dark' : ($first->approved_by_produksi ? 'btn-outline-dark' : 'btn-secondary') }} {{ $first->approved_by_produksi && !$first->approved_by_spv ? 'approve-btn' : '' }}" 
                                                                            data-id="{{ $first->uuid }}" 
                                                                            data-type="spv"
                                                                            title="{{ !$first->approved_by_produksi ? 'Menunggu persetujuan Produksi terlebih dahulu' : ($first->approved_by_spv ? 'Sudah disetujui SPV' : 'Disetujui oleh SPV') }}"
                                                                            {{ !$first->approved_by_produksi || $first->approved_by_spv ? 'disabled' : '' }}>
                                                                        <i class="fas {{ $first->approved_by_spv ? 'fa-check-circle' : (!$first->approved_by_produksi ? 'fa-clock' : 'fa-check') }}"></i> Disetujui oleh SPV
                                                                    </button>
                                                                @endif
                                                            </div>

                                                            <div class="mt-1">
                                                                @if($first->approved_by_qc)
                                                                    <small class="badge badge-success d-block mb-1">✓ QC</small>
                                                                @endif
                                                                @if($first->approved_by_produksi)
                                                                    <small class="badge badge-primary d-block mb-1">✓ FM/FL PRODUKSI</small>
                                                                @endif
                                                                @if($first->approved_by_spv)
                                                                    <small class="badge badge-dark d-block mb-1">✓ SPV</small>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $groupedBarangMudahPecah->appends(['search' => $search ?? ''])->links('pagination::bootstrap-4') }}
                                </div>

                                @stack('detail_templates')
                            @else
                                <div class="alert alert-info text-center">
                                    <i class="fas fa-info-circle fa-2x mb-2"></i>
                                    <h5>Belum ada data Barang Mudah Pecah</h5>
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
                <h5 class="modal-title" id="bulkExportModalLabel">Cetak PDF Barang Mudah Pecah</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="bulkExportForm" action="{{ route('barang-mudah-pecah.bulk-export-pdf') }}" method="POST" data-bulk-form="true">
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
                        <label for="filterArea">Area</label>
                        <select class="form-control" id="filterArea" name="id_area">
                            <option value="">Semua Area</option>
                            @foreach($inputAreas as $area)
                                <option value="{{ $area->id }}">{{ $area->area }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="bulkKodeForm">Kode Form</label>
                        <input type="text" class="form-control" id="bulkKodeForm" name="kode_form" value="QF 30/00" placeholder="Masukkan kode form" readonly>
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
<!-- End Export PDF -->
@push('detail_templates')
    @foreach($groupedBarangMudahPecah as $groupKey => $group)
        @php
            $first = $group->first();
            $detailId = 'detail_' . md5($groupKey);
        @endphp
        <div id="{{ $detailId }}" class="d-none">
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0" style="white-space:nowrap;">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Barang</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-center">Kondisi</th>
                            <th class="text-center">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($group as $idx => $item)
                            <tr>
                                <td class="text-center">{{ $idx + 1 }}</td>
                                <td>
                                    @if($item->is_manual && !empty($item->nama_barang_manual))
                                        {{ $item->nama_barang_manual }}
                                    @else
                                        {{ $item->namaBarang->nama_barang ?? 'Data tidak ditemukan' }}
                                    @endif
                                </td>
                                <td class="text-center">{{ $item->jumlah ?? '-' }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $item->getStatusBadgeClass() }}">
                                        <i class="{{ $item->getStatusIcon() }}"></i>
                                        {{ $item->kondisi }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($item->temuan_ketidaksesuaian)
                                        {{ Str::limit($item->temuan_ketidaksesuaian, 50) }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    const table = $('#myTable').length ? $('#myTable').DataTable() : null;

    $(document).on('click', '.btn-toggle-detail', function () {
        if (!table) return;

        const $btn = $(this);
        const detailId = $btn.data('detail-id');
        const $tr = $btn.closest('tr');
        const row = table.row($tr);

        if (row.child.isShown()) {
            row.child.hide();
            $tr.removeClass('shown');
            return;
        }

        const $tpl = $('#' + detailId);
        row.child($tpl.length ? $tpl.html() : '<div class="text-muted">Detail tidak ditemukan.</div>').show();
        $tr.addClass('shown');
    });

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
                url: '{{ route("barang-mudah-pecah.approve", ":id") }}'.replace(':id', id),
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