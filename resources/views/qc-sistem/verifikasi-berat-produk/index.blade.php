@extends('layouts.app')

@section('title', 'Data Verifikasi Berat Produk')

@section('container')
<!-- Content Header (Page header) -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Data Verifikasi Berat Produk</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Home</a></li>
                            <li class="breadcrumb-item active">Data Verifikasi Berat Produk</li>
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
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Data Verifikasi Berat Produk</h3>
                                <div class="card-tools">
                                    @if(auth()->user()->hasPermissionTo('create-verifikasi-berat-produk'))
                                    <a href="{{ route('verifikasi-berat-produk.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Tambah Data
                                    </a>
                                 @endif
<button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#pdfExportModal">
                                        <i class="fas fa-file-pdf"></i> Cetak PDF
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
                                            <form action="{{ route('verifikasi-berat-produk.index') }}" method="GET">
                                                <div class="input-group">
                                                    <input type="text" name="search" class="form-control" placeholder="Cari Kode atau Nama Produk" value="{{ request('search') }}">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary" type="submit">
                                                            <i class="fas fa-search"></i> Cari
                                                        </button>
                                                        @if(request('search'))
                                                            <a href="{{ route('verifikasi-berat-produk.index') }}" class="btn btn-outline-danger" title="Clear Search">
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
                                                <th>Produk</th>
                                                <th>Kode Produksi</th>
                                                <th>Jenis Produk</th>
                                                <th>Plan</th>
                                                
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($verifikasiBeratProduk as $index => $item)
                                                <tr>
                                                <td>{{ $verifikasiBeratProduk->firstItem() + $loop->index }}</td>
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
                                                        @if($item->shift->shift == 1)
                                                            <span class="badge bg-primary">Shift {{ $item->shift->shift }}</span>
                                                        @elseif($item->shift->shift == 2)
                                                            <span class="badge bg-success">Shift {{ $item->shift->shift }}</span>
                                                        @else
                                                            <span class="badge bg-secondary">Shift {{ $item->shift->shift }}</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $item->produk->nama_produk ?? '-' }}</td>
                                                    <td>{{ $item->kode_produksi }}</td>
                                                    <td>
                                                        @if($item->jenis_produk_kfc === 'KFC')
                                                            <span class="badge badge-primary">KFC</span>
                                                        @else
                                                            <span class="badge badge-secondary">Non-KFC</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $item->plan->nama_plan ?? '-' }}</td>
                                            
                                                       <td>
                                                        <div class="btn-vertical">
                                                            @php
                                                                $userRole = auth()->user()->id_role ?? null;
                                                                $hasApprovalColumns = true;
                                                            @endphp

                                                            <!-- Role-Based Approval Button Display -->
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

                                                            <!-- Action Buttons -->
                                                            <div class="mt-2">
                                                                <a href="{{ route('verifikasi-berat-produk.show', $item->uuid) }}" 
                                                                class="btn btn-info btn-sm">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
@if(auth()->user()->hasPermissionTo('edit-verifikasi-berat-produk'))
                                                                <a href="{{ route('verifikasi-berat-produk.edit', $item->uuid) }}" 
                                                                class="btn btn-warning btn-sm">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                @endif
@if(auth()->user()->hasPermissionTo('delete-verifikasi-berat-produk'))
<form action="{{ route('verifikasi-berat-produk.destroy', $item->uuid) }}" 
                                                                    method="POST" style="display: inline;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger btn-sm" 
                                                                            onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
@endif
@if(auth()->user()->hasPermissionTo('view-verifikasi-berat-produk'))
                                                                <a href="{{ route('verifikasi-berat-produk.logs', $item->uuid) }}" 
                                                                class="btn btn-secondary btn-sm">
                                                                    <i class="fas fa-history"></i>
                                                                </a>
                                                            @endif
</div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="9" class="text-center">Tidak ada data</td>
                                                </tr>
                                            @endforelse
                                    </table>

                                    <!-- Menampilkan Navigasi Pagination -->
                                    <div class="d-flex justify-content-center mt-4">
                                        {{ $verifikasiBeratProduk->appends(request()->query())->links('pagination::bootstrap-4') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
</div>
<!-- Modal Export PDF -->
<div class="modal fade" id="pdfExportModal" tabindex="-1" role="dialog" aria-labelledby="pdfExportModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfExportModalLabel">Export PDF Verifikasi Berat Produk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('verifikasi-berat-produk.bulk-export-pdf') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="modalTanggal">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" id="modalTanggal" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="modalShift">Shift <span class="text-danger">*</span></label>
                        <select name="shift_id" id="modalShift" class="form-control" required>
                            <option value="">Pilih Shift</option>
                            @foreach($cachedShifts as $shift)
                                    <option value="{{ $shift->id }}">Shift {{ $shift->shift }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="modalProduk">Produk</label>
                        <select name="id_produk" id="id_produk" class="form-control">
                            <option value="">Semua Produk</option>
                            @foreach($cachedProduk as $produk)
                                    <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="modalKodeForm">Kode Form <span class="text-danger">*</span></label>
                        <input type="text" name="kode_form" id="modalKodeForm" class="form-control" value="QF 11/00" placeholder="Masukkan kode form" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-file-pdf"></i> Export PDF
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
        const uuid = $(this).data('id');
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
                url: '{{ route("verifikasi-berat-produk.approve") }}',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    uuid: uuid,
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
                    // Restore original button text
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

<!-- /.main-panel -->
@endsection
