@extends('layouts.app')

@section('title', 'Data Pemasakan Nasi')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Data Pemasakan Nasi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Data Pemasakan Nasi</li>
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
                            <h3 class="card-title">Daftar Pemasakan Nasi</h3>
                                <div class="card-tools d-flex">
                                    <form action="{{ route('pemasakan-nasi.index') }}" method="GET" class="mr-2">
                                        <div class="input-group input-group-sm" style="width: 250px;">
                                            <input type="text" name="search" class="form-control" placeholder="Cari Produk, Kode, atau Tanggal" value="{{ $search ?? '' }}">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-default">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                                @if($search)
                                                    <a href="{{ route('pemasakan-nasi.index') }}" class="btn btn-default">
                                                        <i class="fas fa-times text-danger"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </form>
                                    <button type="button" class="btn btn-danger btn-sm mr-2" data-toggle="modal" data-target="#exportPdfModal">
                                        <i class="fas fa-file-pdf"></i> Cetak PDF Filter
                                    </button>
                                    @if(auth()->user()->hasPermissionTo('create-pemasakan-nasi'))
                                        <a href="{{ route('pemasakan-nasi.create') }}" class="btn btn-primary btn-sm">
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

                            <div class="table-responsive">
                                <table class="table table-bordered text-center table-striped" style="white-space: nowrap;">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Shift</th>
                                            <th>Tanggal</th>
                                            <th>Jam</th>
                                            <th>Nama Produk</th>
                                            <th>Kode Produksi</th>
                                            <th>Proses</th>
                                            <th>Status Cooking</th>
                                            <th>Sensori Kondisi</th>
                                            <th>Organo Warna</th>
                                            <th>Organo Aroma</th>
                                            <th>Organo Rasa</th>
                                            <th>Organo Tekstur</th>
                                            <th>Dibuat Oleh</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($items as $index => $item)
                                            <tr>
                                                <td>{{ $items->firstItem() + $index }}</td>
                                                <td>   @if($item->shift->shift == 1)
                                                <span class="badge bg-primary">Shift {{ $item->shift->shift }}</span>
                                                @elseif($item->shift->shift == 2)
                                                    <span class="badge bg-success">Shift {{ $item->shift->shift }}</span>
                                                @else
                                                    <span class="badge bg-secondary">Shift {{ $item->shift->shift }}</span>
                                                @endif
                                                </td>
                                            <td>
                                                @if(auth()->user()->id_role == 2 || auth()->user()->id_role == 3)
                                                    <span class="badge badge-info">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</span>
                                                @else
                                                    <span class="badge badge-info">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y H:i:s') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-secondary">{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</span>
                                            </td>
                                                <td>{{ $item->produk->nama_produk ?? '-' }}</td>
                                                <td>{{ $item->kode_produksi }}</td>
                                                <td>{{ $item->proses }}</td>
                                                <td class="text-center">
                                                    @if($item->status_cooking)
                                                        <i class="fas fa-check text-success"></i>
                                                    @else
                                                        <i class="fas fa-times text-danger"></i>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    {!! App\Models\PemasakanNasi::getOrganoIcon($item->sensori_kondisi) !!}
                                                </td>
                                                <td class="text-center">
                                                    {!! App\Models\PemasakanNasi::getOrganoIcon($item->organo_warna) !!}
                                                </td>
                                                <td class="text-center">
                                                    {!! App\Models\PemasakanNasi::getOrganoIcon($item->organo_aroma) !!}
                                                </td>
                                                <td class="text-center">
                                                    {!! App\Models\PemasakanNasi::getOrganoIcon($item->organo_rasa) !!}
                                                </td>
                                                <td class="text-center">
                                                    {!! App\Models\PemasakanNasi::getOrganoIcon($item->organo_tekstur) !!}
                                                </td>
                                                <td>{{ $item->user->name ?? '-' }}</td>
                                                <td>
                                                    <div class="btn-vertical" role="">
                                                        <!-- <a href="{{ route('pemasakan-nasi.show', $item->uuid) }}" 
                                                           class="btn btn-info btn-sm" title="Lihat">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
@if(auth()->user()->hasPermissionTo('edit-pemasakan-nasi'))
                                                        <a href="{{ route('pemasakan-nasi.edit', $item->uuid) }}" 
                                                           class="btn btn-warning btn-sm" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        @endif
@if(auth()->user()->hasPermissionTo('delete-pemasakan-nasi'))
<form action="{{ route('pemasakan-nasi.destroy', $item->uuid) }}" 
                                                              method="POST" class="d-inline" 
                                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
@endif
@if(auth()->user()->hasPermissionTo('view-pemasakan-nasi'))
                                                        <a href="{{ route('pemasakan-nasi.logs', $item->uuid) }}" 
                                                           class="btn btn-primary btn-sm" title="Lihat Riwayat">
                                                            <i class="fas fa-history"></i>
                                                        </a> @endif
-->
                                                        <x-action-buttons :item="$item" route-prefix="pemasakan-nasi" :show-log="false"/>

                                                        <!-- Approval Buttons -->
                                                        @php
                                                            $userRole = auth()->user()->id_role ?? null;
                                                            $hasApprovalColumns = Schema::hasColumn('pemasakan_nasi', 'approved_by_qc');
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
                                        @empty
                                            <tr>
                                                <td colspan="14" class="text-center">Tidak ada data</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-3">
                                {{ $items->appends(['search' => $search ?? ''])->links('pagination::bootstrap-4') }}
                            </div>
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
                <h5 class="modal-title" id="exportPdfModalLabel">Cetak PDF Pemasakan Nasi Berdasarkan Filter</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="bulkExportForm" action="{{ route('pemasakan-nasi.bulk-export-pdf') }}" method="POST" data-bulk-form="true">
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
                        <input type="text" class="form-control" id="bulkKodeForm" name="kode_form" value="QF 13/00" placeholder="Masukkan kode form" required>
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
                url: '{{ route("pemasakan-nasi.approve", ":id") }}'.replace(':id', id),
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

    // Handle bulk export PDF
    $('[data-bulk-export]').click(function() {
        const form = $('#bulkExportForm');
        const kodeForm = $('#bulkKodeForm').val();
        
        if (!kodeForm) {
            alert('Kode Form harus diisi!');
            return;
        }
        
        // Submit form
        form.submit();
        
        // Close modal after short delay
        setTimeout(function() {
            $('#exportPdfModal').modal('hide');
        }, 1000);
    });
});
</script>
@endpush

@endsection
