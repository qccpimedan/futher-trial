@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">GMP Karyawan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item active">GMP Karyawan</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
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

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Data GMP Karyawan</h3>
                            <div class="card-tools">
                                    @if(auth()->user()->hasPermissionTo('create-gmp-karyawan'))
                                <a href="{{ route('gmp-karyawan.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Tambah Data
                                </a>
                                @endif
<button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#importExcelModal">
                                    <i class="fas fa-file-excel"></i> Import Excel
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#bulkExportModal">
                                    <i class="fas fa-file-pdf"></i> Export PDF
                                </button>
                            </div>
                        </div>
                        <div class="card-body">

                            @if($data->count() > 0)
                                <div class="table-responsive">
                                    <!-- Form Pencarian Server-Side -->
                                    <div class="row mb-3 mt-3">
                                        <div class="col-md-4 offset-md-8">
                                            <form action="{{ route('gmp-karyawan.index') }}" method="GET">
                                                <div class="input-group">
                                                    <input type="text" name="search" class="form-control" placeholder="Cari Nama Karyawan" value="{{ request('search') }}">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary" type="submit">
                                                            <i class="fas fa-search"></i> Cari
                                                        </button>
                                                        @if(request('search'))
                                                            <a href="{{ route('gmp-karyawan.index') }}" class="btn btn-outline-danger" title="Clear Search">
                                                                <i class="fas fa-times"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    <table class="table table-bordered table-striped text-center" style="white-space:nowrap;">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>No</th>
                                                <th>Shift</th>
                                                <th>Tanggal</th>
                                                <th>Jam</th>
                                                <th>Area</th>
                                                <!-- <th>Plan</th> -->
                                                <th>Nama Karyawan</th>
                                                <th>Temuan Ketidaksesuaian</th>
                                                <th>Keterangan</th>
                                                <th>Tindakan Koreksi</th>
                                                <th>Verifikasi</th>
                                                <!-- <th>Petugas</th> -->
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($data as $index => $item)
                                                <tr>
                                                    <td class="text-center">{{ $data->firstItem() + $loop->index }}</td>
                                                    <td><span class="badge badge-info">Shift {{ $item->shift->shift ?? '-' }}</span></td>
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
                                                    <!-- <td>{{ $item->plan->nama_plan ?? '-' }}</td> -->
                                                    <td>{{ $item->area->area ?? '-' }}</td>
                                                    <td>{{ $item->nama_karyawan }}</td>
                                                    <td>
                                                        <span class="badge badge-{{ $item->temuan_ketidaksesuaian == 'sesuai' ? 'success' : 'warning' }}">
                                                            {{ $item->temuan_ketidaksesuaian_label ?: 'Data Kosong' }}
                                                        </span>
                                                    </td>
                                                    <td>{{ Str::limit($item->keterangan, 50) ?? '-' }}</td>
                                                    <td>{{ Str::limit($item->tindakan_koreksi, 50) ?? '-' }}</td>
                                                    <td>
                                                        @php
                                                            $verifikasi = $item->verifikasi ?? null;
                                                            $badgeClass = $verifikasi === 'ok' ? 'success' : ($verifikasi === 'tidak_ok' ? 'danger' : 'secondary');
                                                            $verifikasiText = $verifikasi === 'ok' ? 'OK' : ($verifikasi === 'tidak_ok' ? 'Tidak OK' : '-');
                                                        @endphp
                                                        <span class="badge badge-{{ $badgeClass }}">{{ $verifikasiText }}</span>
                                                    </td>
                                                    <!-- <td>{{ $item->user->name ?? '-' }}</td> -->
                                                    <td class="text-center">
                                                        <div class="btn-vertical">
                                                            <!-- CRUD Buttons -->
                                                            <!-- <div class="mb-1"> -->
                                                            <x-action-buttons :item="$item" route-prefix="gmp-karyawan" :show-view="false" />                                                                <!-- <a href="{{ route('gmp-karyawan.show', $item->uuid) }}" 
                                                                class="btn btn-info btn-sm" title="Lihat Detail">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
@if(auth()->user()->hasPermissionTo('edit-gmp-karyawan'))
                                                                <a href="{{ route('gmp-karyawan.edit', $item->uuid) }}" 
                                                                    class="btn btn-warning btn-sm" title="Edit">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                @endif
@if(auth()->user()->hasPermissionTo('view-gmp-karyawan'))
<a href="{{ route('gmp-karyawan.logs', $item->uuid) }}" 
                                                                class="btn btn-secondary btn-sm" title="Lihat Riwayat">
                                                                    <i class="fas fa-history"></i>
                                                                </a>
                                                                @endif
@if(auth()->user()->hasPermissionTo('delete-gmp-karyawan'))
<form action="{{ route('gmp-karyawan.destroy', $item->uuid) }}" 
                                                                        method="POST" style="display: inline;" 
                                                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form> @endif
-->
                                                            <!-- </div> -->

                                                            <!-- Approval Buttons -->
                                                            @php
                                                                $userRole = auth()->user()->id_role ?? null;
                                                                $hasApprovalColumns = Schema::hasColumn('gmp_karyawan', 'approved_by_qc');
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

                                <!-- Menampilkan Navigasi Pagination -->
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $data->appends(request()->query())->links('pagination::bootstrap-4') }}
                                </div>
                                @else
                                <div class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-gray-400 mb-3"></i>
                                    <p class="text-gray-500">Belum ada data GMP Karyawan.</p>
                                    @if(auth()->user()->hasPermissionTo('create-gmp-karyawan'))
                                    <a href="{{ route('gmp-karyawan.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Tambah Data Pertama
                                    </a>
                                @endif
</div>
                            @endif
                        </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- Modal Export PDF -->
<div class="modal fade" id="bulkExportModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkExportModalLabel">
                    <i class="fas fa-file-pdf text-danger"></i> Export PDF GMP Karyawan
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="bulkExportForm" data-bulk-form="true" action="{{ route('gmp-karyawan.bulk-export-pdf') }}" method="POST">
            @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="tanggal">Tanggal:</label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal">
                        <small class="form-text text-muted">Kosongkan untuk semua tanggal</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="shift_id">Shift:</label>
                        <select class="form-control" id="shift_id" name="shift_id">
                            <option value="">Semua Shift</option>
                            @foreach($cachedShifts as $shift)
                                    <option value="{{ $shift->id }}">{{ $shift->shift }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="kode_form">Kode Form:</label>
                        <input type="text" class="form-control" id="kode_form" name="kode_form" value="QF 28/00" placeholder="Masukkan kode form" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" data-bulk-export="true">
                        <i class="fas fa-download"></i> Download PDF
                    </button>
                </div>
            </form>
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
                url: '{{ route("gmp-karyawan.approve", ":id") }}'.replace(':id', id),
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
                <h5 class="modal-title" id="importExcelModalLabel">Import Data dari Excel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('gmp-karyawan.import-excel') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group text-center mb-3">
                        <a href="{{ route('gmp-karyawan.download-template') }}" class="btn btn-outline-success">
                            <i class="fas fa-download"></i> Download Template Excel
                        </a>
                    </div>
                    <div class="form-group">
                        <label>Pilih File Excel</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx, .xls" required>
                        <small class="form-text text-muted">
                            Format kolom: Shift | Area | Tanggal | Jam | Nama Karyawan | Temuan | Keterangan | Tindakan | Verifikasi | Koreksi Lanjutan
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