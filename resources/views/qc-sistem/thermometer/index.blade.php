@extends('layouts.app')

@section('title', 'Data Thermometer')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('container')
<!-- Content Header (Page header) -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Data Thermometer</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Home</a></li>
                            <!-- <li class="breadcrumb-item"><a href="#">QC Sistem</a></li> -->
                            <li class="breadcrumb-item active">Data Thermometer</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-thermometer-half mr-1"></i>
                                    Data Thermometer
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#importExcelModal">
                                        <i class="fas fa-file-excel"></i> Import Excel
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#pdfExportModal">
                                        <i class="fas fa-file-pdf"></i> Cetak PDF
                                    </button>
                                    @if(auth()->user()->hasPermissionTo('create-thermometer'))
                                    <a href="{{ route('thermometer.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Tambah Data
                                    </a>
                                @endif
</div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <h5><i class="icon fas fa-check"></i> Sukses!</h5>
                                        {{ session('success') }}
                                    </div>
                                @endif

                                @if(session('error'))
                                    <div class="alert alert-danger alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <h5><i class="icon fas fa-ban"></i> Error!</h5>
                                        {{ session('error') }}
                                    </div>
                                @endif

                                @if(session('warning'))
                                    <div class="alert alert-warning alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <h5><i class="icon fas fa-exclamation-triangle"></i> Peringatan!</h5>
                                        {{ session('warning') }}
                                    </div>
                                @endif

                                @if(session('info'))
                                    <div class="alert alert-info alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <h5><i class="icon fas fa-info"></i> Informasi</h5>
                                        {{ session('info') }}
                                    </div>
                                @endif

                                @if(session('import_errors'))
                                    <div class="alert alert-warning alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <h5><i class="icon fas fa-exclamation-triangle"></i> Detail baris gagal import (maks 20):</h5>
                                        <ul class="mb-0">
                                            @foreach(session('import_errors') as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="table-responsive">
                                    <!-- Form Pencarian Server-Side -->
                                    <div class="row mb-3 mt-3">
                                        <div class="col-md-4 offset-md-8">
                                            <form action="{{ route('thermometer.index') }}" method="GET">
                                                <div class="input-group">
                                                    <input type="text" name="search" class="form-control" placeholder="Cari Jenis atau Kode Thermometer" value="{{ request('search') }}">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary" type="submit">
                                                            <i class="fas fa-search"></i> Cari
                                                        </button>
                                                        @if(request('search'))
                                                            <a href="{{ route('thermometer.index') }}" class="btn btn-outline-danger" title="Clear Search">
                                                                <i class="fas fa-times"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    <table class="table table-bordered table-striped text-center" style="white-space:nowrap;">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Tanggal</th>
                                                <th>Jam</th>
                                                <th>Shift</th>
                                                <th>Jenis Thermometer</th>
                                                <th>Kode Thermometer</th>
                                                <th>Hasil Verifikasi 0°C</th>
                                                <!-- <th>Hasil Verifikasi 100°C</th> -->
                                                <th>Hasil Pengecekan</th>
                                                <!-- <th>User</th> -->
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($data as $index => $item)
                                                <tr>
                                                    <td>{{ $data->firstItem() + $loop->index }}</td>
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
                                                    <td>
                                                        <span class="badge badge-primary">
                                                            Shift {{ $item->shift->shift ?? '-' }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $item->jenis }}</td>
                                                    <td>
                                                        {{ $item->kode_thermometer }}
                                                    </td>
                                                    <td>{{ $item->hasil_verifikasi_0 ?? '-' }}</td>
                                                    <!-- <td>{{ $item->hasil_verifikasi_100 ?? '-' }}</td> -->
                                                    <td>
                                                        <span class="badge {{ $item->hasil_pengecekan === 'ok' ? 'badge-success' : 'badge-danger' }}">
                                                            {!! $item->hasil_pengecekan_label !!}
                                                        </span>
                                                    </td>
                                                    <!-- <td>
                                                        <span class="badge badge-secondary">
                                                            <i class="fas fa-user"></i>
                                                            {{ $item->user->name ?? '-' }}
                                                        </span>
                                                    </td> -->
                                                    <td>
                                                        <div class="btn-vertical">
                                                            @php
                                                                $userRole = auth()->user()->id_role ?? null;
                                                                $hasApprovalColumns = true; // Assume approval columns exist for thermometer
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

                                                            <!--
@if(auth()->user()->hasPermissionTo('edit-thermometer')) <a href="{{ route('thermometer.edit', $item->uuid) }}" 
                                                            class="btn btn-warning btn-sm" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            @endif
@if(auth()->user()->hasPermissionTo('delete-thermometer'))
<form action="{{ route('thermometer.destroy', $item->uuid) }}" 
                                                                method="POST" 
                                                                style="display: inline;"
                                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
@endif
@if(auth()->user()->hasPermissionTo('view-thermometer'))
                                                            <a href="{{ route('thermometer.logs', $item->uuid) }}" 
                                                            class="btn btn-info btn-sm" title="Lihat Log">
                                                                <i class="fas fa-history"></i>
                                                            </a> @endif
-->
                                                            <x-action-buttons :item="$item" route-prefix="thermometer" :show-view="false"/>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="10" class="text-center">
                                                        <i class="fas fa-inbox"></i> Tidak ada data
                                                    </td>
                                                </tr>
                                            @endforelse
                                    </table>

                                    <!-- Menampilkan Navigasi Pagination -->
                                    <div class="d-flex justify-content-center mt-4">
                                        {{ $data->appends(request()->query())->links('pagination::bootstrap-4') }}
                                    </div>
                                </div>
                                <!-- /.table-responsive -->
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
<!-- /.content -->
    </div>
</div>

<!-- PDF Export Modal -->
<div class="modal fade" id="pdfExportModal" tabindex="-1" role="dialog" aria-labelledby="pdfExportModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfExportModalLabel">
                    <i class="fas fa-file-pdf"></i> Export PDF Thermometer
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('thermometer.bulk-export-pdf') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                        @error('tanggal')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="shift_id">Shift <span class="text-danger">*</span></label>
                        <select class="form-control" id="shift_id" name="shift_id" required>
                            <option value="">Pilih Shift</option>
                            @foreach($cachedShifts as $shift)
                                <option value="{{ $shift->id }}">Shift {{ $shift->shift }}</option>
                            @endforeach
                        </select>
                        @error('shift_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="kode_form">Kode Form <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="kode_form" name="kode_form" maxlength="50" value="QF 32/00" required placeholder="Masukkan kode form">
                        @error('kode_form')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Informasi:</strong> PDF akan berisi data thermometer sesuai dengan filter tanggal dan shift yang dipilih. Kode form akan disimpan ke database dan hanya bisa diubah melalui modal ini.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Handle approval button clicks
    $('.approve-btn').click(function(e) {
        e.preventDefault();
        
        var id = $(this).data('id');
        var type = $(this).data('type');
        var button = $(this);
        var originalHtml = button.html();
        
        // Confirmation dialog
        var confirmMessage = 'Apakah Anda yakin ingin menyetujui data ini sebagai ' + type.toUpperCase() + '?';
        if (!confirm(confirmMessage)) {
            return false;
        }
        
        // Disable button and show loading
        button.prop('disabled', true);
        button.html('<i class="fas fa-spinner fa-spin"></i> Processing...');
        
        // Send AJAX request
        $.ajax({
            url: '{{ url("qc-sistem/thermometer") }}/' + id + '/approve',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                type: type
            },
            timeout: 10000, // 10 second timeout
            success: function(response) {
                console.log('Success response:', response);
                
                if (response.success) {
                    // Show success message
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message || 'Data berhasil disetujui');
                    } else {
                        alert(response.message || 'Data berhasil disetujui');
                    }
                    
                    // Update button to success state
                    if (type === 'qc') {
                        button.removeClass('btn-outline-success').addClass('btn-success');
                    } else if (type === 'produksi') {
                        button.removeClass('btn-outline-primary').addClass('btn-primary');
                    } else if (type === 'spv') {
                        button.removeClass('btn-outline-dark').addClass('btn-dark');
                    }
                    button.html('<i class="fas fa-check-circle"></i> ' + type.toUpperCase());
                    
                    // Reload page after short delay
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    // Show error message
                    if (typeof toastr !== 'undefined') {
                        toastr.error(response.message || 'Gagal menyetujui data');
                    } else {
                        alert(response.message || 'Gagal menyetujui data');
                    }
                    
                    // Re-enable button
                    button.prop('disabled', false);
                    button.html(originalHtml);
                }
            },
            error: function(xhr, status, error) {
                console.log('Error response:', xhr.responseText);
                console.log('Status:', status);
                console.log('Error:', error);
                
                var errorMessage = 'Terjadi kesalahan saat memproses approval.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMessage = response.message;
                        }
                    } catch (e) {
                        // If not JSON, use default message
                    }
                } else if (status === 'timeout') {
                    errorMessage = 'Request timeout. Silakan coba lagi.';
                } else if (status === 'error') {
                    errorMessage = 'Network error. Silakan periksa koneksi internet.';
                }
                
                // Show error message
                if (typeof toastr !== 'undefined') {
                    toastr.error(errorMessage);
                } else {
                    alert(errorMessage);
                }
                
                // Re-enable button
                button.prop('disabled', false);
                button.html(originalHtml);
            }
        });
        
        return false;
    });
});
</script>
@endpush

<!-- Modal Import Excel -->
<div class="modal fade" id="importExcelModal" tabindex="-1" role="dialog" aria-labelledby="importExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importExcelModalLabel">Import Data Thermometer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('thermometer.import-excel') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group text-center mb-3">
                        <a href="{{ route('thermometer.download-template') }}" class="btn btn-outline-success">
                            <i class="fas fa-download"></i> Download Template Excel
                        </a>
                    </div>
                    <div class="form-group">
                        <label>Pilih File Excel</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx, .xls" required>
                        <small class="form-text text-muted">
                            Format kolom: Shift | Tanggal | Jam | Nama Thermometer | Hasil Pengecekan | Hasil Verifikasi 0°C | Hasil Verifikasi 100°C
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