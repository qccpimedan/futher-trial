@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Data Pemeriksaan Kedatangan  Bahan Kemas</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Data Pemeriksaan Kedatangan Bahan Kemas</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="card-title">
                                    <i class="fas fa-box text-primary mr-2"></i>
                                    Data Pemeriksaan Kedatangan Bahan Kemas
                                </h3>
                                <div>
                                    <button type="button" class="btn btn-danger btn-sm mr-2" data-toggle="modal" data-target="#exportPdfModal">
                                        <i class="fas fa-file-pdf"></i> Cetak PDF Filter
                                    </button>
                                    <button type="button" class="btn btn-success btn-sm mr-2" data-toggle="modal" data-target="#importExcelModal">
                                        <i class="fas fa-file-excel"></i> Import Excel
                                    </button>
                                    @if(auth()->user()->hasPermissionTo('create-pemeriksaan-bahan-kemas'))
                                    <a href="{{ route('pemeriksaan-bahan-kemas.create') }}" class="btn btn-sm btn-primary">
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

                            <!-- Form Pencarian Server-Side -->
                            <div class="row mb-3 mt-3">
                                <div class="col-md-6">
                                    <form method="GET" action="{{ route('pemeriksaan-bahan-kemas.index') }}">
                                        <div class="input-group input-group-sm" style="width: 300px;">
                                            <input type="text" class="form-control" name="search" placeholder="Cari nama kemasan atau kode produksi" value="{{ $search ?? '' }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="submit">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                                @if(!empty($search))
                                                    <a class="btn btn-outline-danger" href="{{ route('pemeriksaan-bahan-kemas.index') }}">
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
                                        <form method="GET" action="{{ route('pemeriksaan-bahan-kemas.index') }}">
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
                                            Data Pemeriksaan Bahan Kemas
                                        @endif
                                    </small>
                                </div>
                            </div>

                            @if(isset($items) && count($items))
                                <div class="table-responsive">
                                    <table  class="table table-bordered table-striped table-hover" style="white-space: nowrap;">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>No</th>
                                                <th class="text-center">Shift</th>
                                                <th class="text-center">Tanggal</th>
                                                <th class="text-center">Jam</th>
                                                <th class="text-center">Nama Kemasan</th>
                                                <th class="text-center">Kode Produksi</th>
                                                <th class="text-center">Kondisi</th>
                                                <th class="text-center">Keterangan</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($items as $index => $item)
                                                <tr>
                                                    <td class="text-center">{{ ($items->currentPage() - 1) * $items->perPage() + $index + 1 }}</td>
                                                    <td class="text-center">
                                                        <span class="badge bg-secondary">{{ $item->shift->shift ?? '-' }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge badge-secondary">
                                                            @php
                                                                $userRole = auth()->user()->id_role ?? null;
                                                                $showTime = in_array($userRole, [1, 2, 5]);
                                                                $format = $showTime ? 'd-m-Y H:i:s' : 'd-m-Y';
                                                            @endphp
                                                            {{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format($format) : '-' }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}
                                                    </td>
                                                    <td class="text-center">{{ $item->nama_kemasan ?? '-' }}</td>
                                                    <td class="text-center">{{ $item->kode_produksi ?? '-' }}</td>
                                                    <td class="text-center">
                                                        <span class="badge {{ $item->kondisi_bahan_kemasan === 'OK' ? 'badge-success' : 'badge-danger' }}">
                                                            {{ $item->kondisi_bahan_kemasan ?? '-' }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">{{ $item->keterangan ?? '-' }}</td>
                                                    <td class="text-center">
                                                        <div class="btn-vertical">
                                                            <div class="mb-1">
                                                                <x-action-buttons :item="$item" route-prefix="pemeriksaan-bahan-kemas" :show-view="true" :show-history="false"/>
@if(auth()->user()->hasPermissionTo('view-pemeriksaan-bahan-kemas'))
                                                                <a href="{{ route('pemeriksaan-bahan-kemas.logs', $item->uuid) }}" 
                                                                   class="btn btn-sm btn-info" 
                                                                   title="Lihat Log">
                                                                    <i class="fas fa-history"></i>
                                                                </a>
                                                            @endif
</div>

                                                            @php
                                                                $userRole = auth()->user()->id_role ?? null;
                                                            @endphp

                                                            <div class="btn-group-vertical mb-1" role="group">
                                                                @if(in_array($userRole, [1, 5]))
                                                                    <button type="button"
                                                                            class="btn btn-sm {{ $item->approved_by_qc ? 'btn-success' : 'btn-outline-success' }} approve-btn"
                                                                            data-id="{{ $item->uuid }}"
                                                                            data-type="qc"
                                                                            title="Disetujui oleh QC"
                                                                            {{ $item->approved_by_qc ? 'disabled' : '' }}>
                                                                        <i class="fas {{ $item->approved_by_qc ? 'fa-check-circle' : 'fa-check' }}"></i> QC
                                                                    </button>

                                                                    <button type="button"
                                                                            class="btn btn-sm {{ $item->approved_by_produksi ? 'btn-primary' : 'btn-secondary' }}"
                                                                            title="{{ $item->approved_by_produksi ? 'Sudah disetujui Produksi' : 'Menunggu persetujuan Produksi' }}"
                                                                            disabled>
                                                                        <i class="fas {{ $item->approved_by_produksi ? 'fa-check-circle' : 'fa-clock' }}"></i> FM/FL PRODUKSI
                                                                    </button>

                                                                    <button type="button"
                                                                            class="btn btn-sm {{ $item->approved_by_spv ? 'btn-dark' : 'btn-secondary' }}"
                                                                            title="{{ $item->approved_by_spv ? 'Sudah disetujui SPV' : 'Menunggu persetujuan SPV' }}"
                                                                            disabled>
                                                                        <i class="fas {{ $item->approved_by_spv ? 'fa-check-circle' : 'fa-clock' }}"></i> SPV
                                                                    </button>
                                                                @elseif($userRole == 2)
                                                                    <button type="button"
                                                                            class="btn btn-sm {{ $item->approved_by_produksi ? 'btn-primary' : ($item->approved_by_qc ? 'btn-outline-primary' : 'btn-secondary') }} {{ $item->approved_by_qc && !$item->approved_by_produksi ? 'approve-btn' : '' }}"
                                                                            data-id="{{ $item->uuid }}"
                                                                            data-type="produksi"
                                                                            title="{{ !$item->approved_by_qc ? 'Menunggu persetujuan QC terlebih dahulu' : ($item->approved_by_produksi ? 'Sudah disetujui Produksi' : 'Disetujui oleh Produksi') }}"
                                                                            {{ !$item->approved_by_qc || $item->approved_by_produksi ? 'disabled' : '' }}>
                                                                        <i class="fas {{ $item->approved_by_produksi ? 'fa-check-circle' : (!$item->approved_by_qc ? 'fa-clock' : 'fa-check') }}"></i> Disetujui oleh Produksi
                                                                    </button>
                                                                @elseif($userRole == 3)
                                                                    <button type="button"
                                                                            class="btn btn-sm {{ $item->approved_by_qc ? 'btn-success' : 'btn-outline-success' }} approve-btn"
                                                                            data-id="{{ $item->uuid }}"
                                                                            data-type="qc"
                                                                            title="Disetujui oleh QC"
                                                                            {{ $item->approved_by_qc ? 'disabled' : '' }}>
                                                                        <i class="fas {{ $item->approved_by_qc ? 'fa-check-circle' : 'fa-check' }}"></i> Disetujui oleh QC
                                                                    </button>
                                                                @elseif($userRole == 4)
                                                                    <button type="button"
                                                                            class="btn btn-sm {{ $item->approved_by_spv ? 'btn-dark' : ($item->approved_by_produksi ? 'btn-outline-dark' : 'btn-secondary') }} {{ $item->approved_by_produksi && !$item->approved_by_spv ? 'approve-btn' : '' }}"
                                                                            data-id="{{ $item->uuid }}"
                                                                            data-type="spv"
                                                                            title="{{ !$item->approved_by_produksi ? 'Menunggu persetujuan Produksi terlebih dahulu' : ($item->approved_by_spv ? 'Sudah disetujui SPV' : 'Disetujui oleh SPV') }}"
                                                                            {{ !$item->approved_by_produksi || $item->approved_by_spv ? 'disabled' : '' }}>
                                                                        <i class="fas {{ $item->approved_by_spv ? 'fa-check-circle' : (!$item->approved_by_produksi ? 'fa-clock' : 'fa-check') }}"></i> Disetujui oleh SPV
                                                                    </button>
                                                                @else
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

                                <div class="d-flex justify-content-center mt-3">
                                    {{ $items->appends(['search' => $search ?? '', 'per_page' => $perPage ?? ''])->links('pagination::bootstrap-4') }}
                                </div>

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

                                @push('scripts')
                                <script>
                                $(document).ready(function() {
                                    $('.approve-btn').click(function() {
                                        const id = $(this).data('id');
                                        const type = $(this).data('type');
                                        const button = $(this);

                                        const typeNames = {
                                            'qc': 'QC',
                                            'produksi': 'Produksi',
                                            'spv': 'SPV'
                                        };

                                        if (confirm(`Apakah Anda yakin ingin menyetujui data ini sebagai ${typeNames[type]}?`)) {
                                            button.prop('disabled', true);
                                            button.html('<i class="fas fa-spinner fa-spin"></i> Processing...');

                                            $.ajax({
                                                url: '{{ route("pemeriksaan-bahan-kemas.approve", ":id") }}'.replace(':id', id),
                                                method: 'POST',
                                                data: {
                                                    _token: $('meta[name="csrf-token"]').attr('content'),
                                                    type: type
                                                },
                                                success: function(response) {
                                                    if (response.success) {
                                                        setTimeout(function() {
                                                            location.reload();
                                                        }, 1500);
                                                    } else {
                                                        alert('Gagal menyetujui data: ' + response.message);
                                                        button.prop('disabled', false);
                                                        const originalText = {
                                                            'qc': '<i class="fas fa-check"></i> QC',
                                                            'produksi': '<i class="fas fa-check"></i> Disetujui oleh Produksi',
                                                            'spv': '<i class="fas fa-check"></i> Disetujui oleh SPV'
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
                                                    const originalText = {
                                                        'qc': '<i class="fas fa-check"></i> QC',
                                                        'produksi': '<i class="fas fa-check"></i> Disetujui oleh Produksi',
                                                        'spv': '<i class="fas fa-check"></i> Disetujui oleh SPV'
                                                    };
                                                    button.html(originalText[type]);
                                                }
                                            });
                                        }
                                    });
                                });
                                </script>
                                @endpush
                            @else
                                <div class="alert alert-info text-center">
                                    <i class="fas fa-info-circle fa-2x mb-2"></i>
                                    <h5>Belum ada data Pemeriksaan Bahan Kemas</h5>
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
<div class="modal fade" id="exportPdfModal" tabindex="-1" role="dialog" aria-labelledby="exportPdfModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportPdfModalLabel">Export PDF Berdasarkan Filter</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="bulkExportForm" action="{{ route('pemeriksaan-bahan-kemas.bulk-export-pdf') }}" method="POST" data-bulk-form="true">
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
                        <input type="text" class="form-control" id="bulkKodeForm" name="kode_form" value="{{ old('kode_form', 'QF 35/00') }}" placeholder="Masukkan kode form" readonly>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Batal</button>
                <button type="button" class="btn btn-primary" data-bulk-export="true"><i class="fas fa-download"></i> Export PDF</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Import Excel -->
<div class="modal fade" id="importExcelModal" tabindex="-1" role="dialog" aria-labelledby="importExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importExcelModalLabel">Import Data dari Excel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('pemeriksaan-bahan-kemas.import-excel') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group text-center mb-3">
                        <a href="{{ route('pemeriksaan-bahan-kemas.download-template') }}" class="btn btn-outline-success">
                            <i class="fas fa-download"></i> Download Template Excel
                        </a>
                    </div>
                    <div class="form-group">
                        <label>Pilih File Excel</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx, .xls" required>
                        <small class="form-text text-muted">
                            Format kolom: Shift | Tanggal | Jam | Nama Kemasan | Kode Produksi | Kondisi (OK/Tidak OK) | Keterangan
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
@endsection
