@extends('layouts.app')

@section('title', 'Data Verifikasi Peralatan')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Data Verifikasi Peralatan</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Verifikasi Peralatan</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Data Verifikasi Peralatan</h3>
                                <div class="card-tools">
                                    @if(auth()->user()->hasPermissionTo('create-verif-peralatan'))
                                    <a href="{{ route('verif-peralatan.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Tambah Data
                                    </a>
                                    @endif
<button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#exportPdfModal">
                                        <i class="fas fa-file-pdf"></i> Export PDF
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        {{ session('success') }}
                                    </div>
                                @endif

                                <div class="table-responsive text-center">
                                    <!-- Form Pencarian Server-Side -->
                                    <div class="row mb-3 mt-3">
                                        <div class="col-md-4 offset-md-8">
                                            <form action="{{ route('verif-peralatan.index') }}" method="GET">
                                                <div class="input-group">
                                                    <input type="date" name="search" class="form-control" placeholder="Cari Tanggal" value="{{ request('search') }}">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary" type="submit">
                                                            <i class="fas fa-search"></i> Cari
                                                        </button>
                                                        @if(request('search'))
                                                            <a href="{{ route('verif-peralatan.index') }}" class="btn btn-outline-danger" title="Clear Search">
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
                                                <th>No</th>
                                                <th>Tanggal</th>
                                                <th>Jam</th>
                                                <th>Shift</th>
                                                <th>Area</th>
                                                <th>Ketidaksesuaian</th>
                                                <th>Plan</th>
                                                <th>Dibuat Oleh</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($items as $index => $item)
                                                <tr>
                                                    <td>{{ $items->firstItem() + $index }}</td>
                                                    <td><span class="badge badge-info">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</span></td>
                                                    <td>
                                                        <span class="badge badge-secondary">{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</span>
                                                    </td>
                                                    <td>
                                                        @if($item->shift && $item->shift->shift == 1)
                                                            <span class="badge bg-primary">Shift {{ $item->shift->shift }}</span>
                                                        @elseif($item->shift && $item->shift->shift == 2)
                                                            <span class="badge bg-success">Shift {{ $item->shift->shift }}</span>
                                                        @elseif($item->shift)
                                                            <span class="badge bg-secondary">Shift {{ $item->shift->shift }}</span>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @php
                                                            $areaNames = $item->details
                                                                ?->map(function ($d) {
                                                                    return $d->mesin?->area?->area;
                                                                })
                                                                ->filter()
                                                                ->unique()
                                                                ->values();
                                                        @endphp

                                                        {{ $areaNames && $areaNames->count() ? $areaNames->implode(', ') : '-' }}
                                                    </td>
                                                    <td>
                                                        @php
                                                            $ketidaksesuaian = $item->details
                                                                ?->where('verifikasi', false)
                                                                ->count() ?? 0;
                                                        @endphp

                                                        @if($ketidaksesuaian > 0)
                                                            <span class="badge badge-danger">{{ $ketidaksesuaian }}</span>
                                                        @else
                                                            <span class="badge badge-success">0</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $item->plan->nama_plan ?? '-' }}</td>
                                                    <td>{{ $item->user->name ?? '-' }}</td>
                                                    <td>
                                                        <div class="btn-vertical">
                                                            <div class="mb-1">
                                                                <a href="{{ route('verif-peralatan.show', $item->uuid) }}" class="btn btn-info btn-sm">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
@if(auth()->user()->hasPermissionTo('edit-verif-peralatan'))
                                                                <a href="{{ route('verif-peralatan.edit', $item->uuid) }}" class="btn btn-warning btn-sm">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                @endif
@if(auth()->user()->hasPermissionTo('delete-verif-peralatan'))
<form action="{{ route('verif-peralatan.destroy', $item->uuid) }}" method="POST" style="display:inline;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            @endif
</div>

                                                            @php
                                                                $userRole = auth()->user()->id_role ?? null;
                                                                $hasApprovalColumns = Schema::hasColumn('verif_peralatan', 'approved_by_qc');
                                                            @endphp

                                                            @if($hasApprovalColumns)
                                                            <div class="btn-group-vertical" role="group">
                                                                @if(in_array($userRole, [1, 5]))
                                                                    <button type="button" 
                                                                            class="btn btn-sm {{ isset($item->approved_by_qc) && $item->approved_by_qc ? 'btn-success' : 'btn-outline-success' }} approve-btn" 
                                                                            data-id="{{ $item->uuid }}" 
                                                                            data-type="qc"
                                                                            title="Disetujui oleh QC"
                                                                            {{ isset($item->approved_by_qc) && $item->approved_by_qc ? 'disabled' : '' }}>
                                                                        <i class="fas {{ isset($item->approved_by_qc) && $item->approved_by_qc ? 'fa-check-circle' : 'fa-check' }}"></i> QC
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
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="9" class="text-center">Tidak ada data</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-center mt-3">
                                    {{ $items->appends(request()->query())->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

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
                url: '{{ route("verif-peralatan.approve", ":id") }}'.replace(':id', id),
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    type: type
                },
                success: function(response) {
                    if (response.success) {
                        setTimeout(function() {
                            location.reload();
                        }, 800);
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
                    location.reload();
                }
            });
        }
    });

    $('[data-bulk-export]').click(function() {
        const button = $(this);
        const form = $('#bulkExportForm');
        const kodeForm = $('#bulkKodeForm').val();

        if (!kodeForm.trim()) {
            alert('Kode Form harus diisi!');
            return;
        }

        button.prop('disabled', true);
        form.trigger('submit');

        setTimeout(function() {
            button.prop('disabled', false);
        }, 1500);
    });
});
</script>
@endpush

<!-- Modal Export PDF -->
<div class="modal fade" id="exportPdfModal" tabindex="-1" role="dialog" aria-labelledby="exportPdfModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportPdfModalLabel">Cetak PDF Verifikasi Peralatan Berdasarkan Filter</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="bulkExportForm" action="{{ route('verif-peralatan.bulk-export-pdf') }}" method="POST" data-bulk-form="true" target="_blank">
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
                        <label for="bulkKodeForm">Kode Form</label>
                        <input type="text" class="form-control" id="bulkKodeForm" name="kode_form" value="QF 26/00" placeholder="Masukkan kode form" readonly>
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
