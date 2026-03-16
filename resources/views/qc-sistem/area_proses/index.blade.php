@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Data Area Proses</h1>
                    @if(request('group_uuid'))
                        <small class="text-muted">Riwayat per 2 jam</small>
                    @endif
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Data Area Proses</li>
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
                                    <i class="fas fa-clipboard-check text-primary mr-2"></i>
                                    Data Area Proses
                                    @if(request('group_uuid'))
                                        <span class="badge badge-info ml-2">Riwayat per 2 Jam</span>
                                    @endif
                                </h3>
                                <div>
                                    @if(request('group_uuid'))
                                        <a href="{{ route('area-proses.index') }}" class="btn btn-sm btn-secondary mr-2">
                                            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Utama
                                        </a>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-danger mr-2" data-toggle="modal" data-target="#pdfExportModal">
                                        <i class="fas fa-file-pdf"></i> Cetak PDF
                                    </button>
                                    @if(auth()->user()->hasPermissionTo('create-area-proses'))
                                    <a href="{{ route('area-proses.create') }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus"></i> Tambah Data
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Form Pencarian Server-Side -->
                            <div class="row mb-3 mt-1">
                                <div class="col-md-4 offset-md-8">
                                    <form action="{{ route('area-proses.index') }}" method="GET">
                                        @if(request('group_uuid'))
                                            <input type="hidden" name="group_uuid" value="{{ request('group_uuid') }}">
                                        @endif
                                        <div class="input-group">
                                            <input type="text" name="search" class="form-control" placeholder="Cari Area atau Tanggal" value="{{ $search ?? '' }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="submit">
                                                    <i class="fas fa-search"></i> Cari
                                                </button>
                                                @if(!empty($search))
                                                    <a href="{{ route('area-proses.index', request('group_uuid') ? ['group_uuid' => request('group_uuid')] : []) }}" class="btn btn-outline-danger" title="Clear Search">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

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

                            @if(request('group_uuid'))
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Info:</strong> Anda sedang melihat riwayat data per 2 jam untuk grup UUID: <code>{{ request('group_uuid') }}</code>
                                </div>
                            @endif

                            @include('qc-sistem.area_proses._table')
                            
                            <!-- Menampilkan Navigasi Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $areaProses->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- PDF Export Modal -->
<div class="modal fade" id="pdfExportModal" tabindex="-1" role="dialog" aria-labelledby="pdfExportModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfExportModalLabel">
                    <i class="fas fa-file-pdf text-danger"></i> Export PDF Area Proses
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="pdfExportForm" action="{{ route('area-proses.bulk-export-pdf') }}" method="POST" target="_blank">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="export_tanggal">Tanggal</label>
                                <input type="date" class="form-control" id="export_tanggal" name="tanggal">
                                <small class="form-text text-muted">Kosongkan untuk semua tanggal</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="export_shift">Shift</label>
                                <select class="form-control" id="export_shift" name="shift">
                                    <option value="">Semua Shift</option>
                                    <option value="1">Shift 1</option>
                                    <option value="2">Shift 2</option>
                                    <option value="3">Shift 3</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="export_area">Area</label>
                                <select class="form-control" id="export_area" name="area">
                                    <option value="">Semua Area</option>
                                    @if(isset($areaProses))
                                        @foreach($areaProses->pluck('area.area')->unique()->filter() as $area)
                                            <option value="{{ $area }}">{{ $area }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="export_kode_form">Kode Form <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="export_kode_form" name="kode_form" value="QF 25/00" readonly 
                                       placeholder="Masukkan kode form untuk identifikasi PDF">
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> Kode form akan disimpan ke database dan ditampilkan di PDF
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-danger">
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
                url: '{{ route("area-proses.approve", ":id") }}'.replace(':id', id),
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