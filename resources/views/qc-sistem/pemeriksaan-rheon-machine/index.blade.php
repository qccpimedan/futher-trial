@extends('layouts.app')

@section('title', 'Data Pemeriksaan Rheon Machine')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Data Pemeriksaan Rheon Machine</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Data Pemeriksaan Rheon Machine</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-cogs mr-2"></i>
                                Daftar Pemeriksaan Rheon Machine
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-danger btn-sm mr-2" data-toggle="modal" data-target="#exportPdfModal">
                                    <i class="fas fa-file-pdf"></i> Cetak PDF Filter
                                </button>
                                    @if(auth()->user()->hasPermissionTo('create-pemeriksaan-rheon-machine'))
                                <a href="{{ route('pemeriksaan-rheon-machine.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus mr-1"></i> Tambah Data
                                </a>
                            @endif
</div>
                        </div>
                        
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    {{ session('success') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <!-- Form Pencarian Server-Side -->
                            <div class="row mb-3 mt-3">
                                <div class="col-md-6">
                                    <form method="GET" action="{{ route('pemeriksaan-rheon-machine.index') }}">
                                        <div class="input-group input-group-sm" style="width: 300px;">
                                            <input type="text" class="form-control" name="search" placeholder="Cari nama produk" value="{{ $search ?? '' }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="submit">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                                @if(!empty($search))
                                                    <a class="btn btn-outline-danger" href="{{ route('pemeriksaan-rheon-machine.index') }}">
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
                                        <form method="GET" action="{{ route('pemeriksaan-rheon-machine.index') }}">
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
                                            Data Pemeriksaan Rheon Machine
                                        @endif
                                    </small>
                                </div>
                            </div>

                            @if($pemeriksaan->count() > 0)
                                <div class="table-responsive">
                                    <table  class="text-center table table-bordered table-striped" style="white-space: nowrap;">
                                        <thead class="thead-light">
                                            <tr>
                                                <th width="5%">No</th>
                                                <th width="12%">Shift</th>
                                                <th width="12%">Tanggal</th>
                                                <th width="10%">Batch</th>
                                                <th width="8%">Waktu</th>
                                                <th width="15%">Produk</th>
                                                <th width="8%">Dough</th>
                                                <th width="8%">Filler</th>
                                                <th width="8%">Forming</th>
                                                <th width="8%">Frying</th>
                                                <th width="12%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pemeriksaan as $index => $item)
                                                <tr>
                                                    <td class="text-center">{{ $pemeriksaan->firstItem() + $index }}</td>
                                                    <td>
                                                        <span class="badge badge-secondary">Shift {{ $item->shift->shift ?? '-' }}</span>
                                                    </td>
                                                 <td>
                                                    @if(auth()->user()->id_role == 2 || auth()->user()->id_role == 3)
                                                        <span class="badge badge-secondary">{{ $item->tanggal ? $item->tanggal->format('d/m/Y') : '-' }}</span>
                                                    @else
                                                        <span class="badge badge-secondary">{{ $item->tanggal ? $item->tanggal->format('d/m/Y H:i:s') : '-' }}</span>
                                                    @endif
                                                </td>
                                                    <td>
                                                        <span class="badge badge-info">{{ $item->batch }}</span>
                                                    </td>
                                                    <td>
                                                       {{ $item->pukul ? \Carbon\Carbon::parse($item->pukul)->format('H:i') : '-' }}
                                                    </td>
                                                    <td>
                                                        <span>{{ $item->produk->nama_produk ?? '-' }}</span>
                                                    </td>
                                                   
                                                    <td class="text-center">
                                                        <span class="">
                                                            {{ number_format($item->rata_rata_dough, 2) }}gram
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="">
                                                            {{ number_format($item->rata_rata_filler, 2) }}gram
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="">
                                                            {{ number_format($item->rata_rata_after_forming, 2) }}gram
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="">
                                                            {{ number_format($item->rata_rata_after_frying, 2) }}gram
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="btn-vertical">
                                                            <!-- CRUD Buttons -->
                                                            <!-- <div class="mb-1">
                                                                <a href="{{ route('pemeriksaan-rheon-machine.show', $item->uuid) }}" class="btn btn-info btn-sm" title="Detail">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                @if(Auth::user()->role == 'Superadmin' || Auth::user()->role == 'Admin' || $item->user_id == Auth::id() || Auth::user()->role == 'Spv')
@if(auth()->user()->hasPermissionTo('edit-pemeriksaan-rheon-machine'))
                                                                    <a href="{{ route('pemeriksaan-rheon-machine.edit', $item->uuid) }}" class="btn btn-warning btn-sm" title="Edit">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                               @endif
 @endif
@if(auth()->user()->hasPermissionTo('view-pemeriksaan-rheon-machine'))
                                                                <a href="{{ route('pemeriksaan-rheon-machine.logs', $item->uuid) }}" class="btn btn-secondary btn-sm" title="Lihat Riwayat">
                                                                    <i class="fas fa-history"></i>
                                                                </a>
                                                                @endif
@if(Auth::user()->role == 'Superadmin' || Auth::user()->role == 'Admin' || Auth::user()->role == 'Spv')
@if(auth()->user()->hasPermissionTo('delete-pemeriksaan-rheon-machine'))
                                                                    <form action="{{ route('pemeriksaan-rheon-machine.destroy', $item->uuid) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                    </form>
                                                               @endif
 @endif
                                                            </div> -->
                                                            <x-action-buttons :item="$item" route-prefix="pemeriksaan-rheon-machine"/>

                                                            <!-- Approval Buttons -->
                                                            @php
                                                                $userRole = auth()->user()->id_role ?? null;
                                                                $hasApprovalColumns = Schema::hasColumn('pemeriksaan_rheon_machine', 'approved_by_qc');
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
                                    {{ $pemeriksaan->appends(['search' => $search ?? '', 'per_page' => $perPage ?? ''])->links('pagination::bootstrap-4') }}
                                </div>
                            @else
                                <tr>
                                    <td colspan="10" class="text-center py-5">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">Belum ada data pemeriksaan</h5>
                                        <p class="text-muted">Silakan tambah data pemeriksaan rheon machine baru.</p>
                                    @if(auth()->user()->hasPermissionTo('create-pemeriksaan-rheon-machine'))
                                        <a href="{{ route('pemeriksaan-rheon-machine.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus mr-1"></i> Tambah Data Pertama
                                        </a>
                                    @endif
</td>
                                </tr>
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
                <h5 class="modal-title" id="exportPdfModalLabel">Cetak PDF Pemeriksaan Rheon Machine Berdasarkan Filter</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="bulkExportForm" action="{{ route('pemeriksaan-rheon-machine.bulk-export-pdf') }}" method="POST" data-bulk-form="true">
                    @csrf
                    <div class="form-group">
                        <label for="filterTanggal">Tanggal</label>
                        <input type="date" class="form-control" id="filterTanggal" name="tanggal">
                    </div>
                    <div class="form-group">
                        <label for="filterShift">Shift</label>
                        <select class="form-control" id="filterShift" name="shift">
                            <option value="">Semua Shift</option>
                            @foreach($cachedShifts as $shift)
                                    <option value="{{ $shift->shift }}">{{ $shift->shift }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="filterProduk">Nama Produk</label>
                        <select class="form-control" id="id_produk" name="produk">
                            <option value="">Semua Produk</option>
                            @foreach($cachedProduk as $produk)
                                    <option value="{{ $produk->nama_produk }}">{{ $produk->nama_produk }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="bulkKodeForm">Kode Form</label>
                        <input type="text" class="form-control" id="bulkKodeForm" name="kode_form" value="QF 19/00" placeholder="Masukkan kode form" required>
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
                url: '{{ route("pemeriksaan-rheon-machine.approve", ":id") }}'.replace(':id', id),
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

    // Handle bulk PDF export with new tab
    $('#newRheonMachine').click(function(e) {
        e.preventDefault();
        
        const form = $('#bulkExportForm');
        const kodeForm = $('#bulkKodeForm').val().trim();
        
        if (!kodeForm) {
            alert('Silakan masukkan kode form terlebih dahulu!');
            return false;
        }
        
        // Tampilkan loading
        $(this).prop('disabled', true).text('Memproses...');
        
        // Create a temporary form for submission in new tab
        const tempForm = $('<form>', {
            'method': 'POST',
            'action': form.attr('action'),
            'target': '_blank'
        });
        
        // Add CSRF token
        tempForm.append($('<input>', {
            'type': 'hidden',
            'name': '_token',
            'value': '{{ csrf_token() }}'
        }));
        
        // Add all form data
        form.find('input, select').each(function() {
            const input = $(this);
            const name = input.attr('name');
            const value = input.val();
            
            if (name && value) {
                tempForm.append($('<input>', {
                    'type': 'hidden',
                    'name': name,
                    'value': value
                }));
            }
        });
        
        // Submit form in new tab
        $('body').append(tempForm);
        tempForm.submit();
        tempForm.remove();
        
        // Reset tombol dan tutup modal
        const button = $(this);
        const modal = button.closest('.modal');
        
        setTimeout(() => {
            button.prop('disabled', false).html('<i class="fas fa-download"></i> Cetak PDF');
            modal.modal('hide');
            form[0].reset();
        }, 2000);
    });

});
</script>
@endpush

@endsection
