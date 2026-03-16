{{-- filepath: resources/views/qc-sistem/rebox/index.blade.php --}}
@extends('layouts.app')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Penyimpanan Bahan Rebox</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                            <li class="breadcrumb-item active">Rebox</li>
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
                                <h3 class="card-title">Data Penyimpanan Bahan Rebox</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-danger btn-sm mr-2" data-toggle="modal" data-target="#exportPdfModal">
                                        <i class="fas fa-file-pdf"></i> Cetak PDF Filter
                                    </button>
                                    <button type="button" class="btn btn-success btn-sm mr-2" data-toggle="modal" data-target="#importExcelModal">
                                        <i class="fas fa-file-excel"></i> Import Excel
                                    </button>
                                    @if(auth()->user()->hasPermissionTo('create-rebox'))
                                    <a href="{{ route('rebox.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Tambah Data
                                    </a>
                                @endif
</div>
                            </div>
                            <div class="card-body">
                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        {{ session('success') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif

                                @if(session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif

                                @if(session('warning'))
                                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('warning') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif

                                @if(session('info'))
                                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                                        <i class="fas fa-info-circle mr-2"></i>{{ session('info') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif

                                @if(session('import_errors'))
                                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                        <strong>Detail baris gagal import (maks 20):</strong>
                                        <ul class="mb-0">
                                            @foreach(session('import_errors') as $err)
                                                <li>{{ $err }}</li>
                                            @endforeach
                                        </ul>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif
                                <!-- Form Pencarian Server-Side -->
                                <div class="row mb-3 mt-3">
                                    <div class="col-md-4 offset-md-8">
                                        <form action="{{ route('rebox.index') }}" method="GET">
                                            <div class="input-group">
                                                <input type="text" name="search" class="form-control" placeholder="Cari Produk atau Kode Produksi" value="{{ request('search') }}">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="submit">
                                                        <i class="fas fa-search"></i> Cari
                                                    </button>
                                                    @if(request('search'))
                                                        <a href="{{ route('rebox.index') }}" class="btn btn-outline-danger" title="Clear Search">
                                                            <i class="fas fa-times"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                @include('qc-sistem.rebox._table')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
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
                <form id="bulkExportForm" action="{{ route('rebox.bulk-export-pdf') }}" method="POST" data-bulk-form="true">
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
                        <input type="text" class="form-control" id="bulkKodeForm" name="kode_form" placeholder="Masukkan kode form" value="QF 34/00" readonly>
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

</div>

<!-- Modal Import Excel -->
<div class="modal fade" id="importExcelModal" tabindex="-1" role="dialog" aria-labelledby="importExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importExcelModalLabel">Import Excel Rebox</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('rebox.import-excel') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>File Excel (.xlsx / .xls)</label>
                        <a href="{{ route('rebox.download-template') }}" class="btn btn-sm btn-outline-primary mb-2">
                            <i class="fas fa-download"></i> Download Template
                        </a>
                        <input type="file" name="file" class="form-control" required>
                        <small class="form-text text-muted">
                            Format kolom: Shift | Tanggal | Jam | Nama Produk | Kode Produksi | Best Before | Isi/Jumlah (✔/✘) | Labelisasi (✔/✘)
                        </small>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success"><i class="fas fa-upload"></i> Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection