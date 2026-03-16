@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1> Metal Detector</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item active">List Metal Detector</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title"></i> Data Metal Detector</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-danger btn-sm mr-2" data-toggle="modal" data-target="#exportPdfModal">
                                    <i class="fas fa-file-pdf"></i> Cetak PDF Filter
                                </button>
                                    @if(auth()->user()->hasPermissionTo('create-input-metal-detector'))
                                <a href="{{ route('input-metal-detector.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Tambah Data
                                </a>
                            @endif
</div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <form method="GET" action="{{ route('input-metal-detector.index') }}">
                                        <div class="input-group input-group-sm" style="width: 300px;">
                                            <input type="text" class="form-control" name="search" placeholder="Cari data..." value="{{ $search ?? '' }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="submit">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                                @if(!empty($search))
                                                    <a class="btn btn-outline-danger" href="{{ route('input-metal-detector.index') }}">
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
                                        <form method="GET" action="{{ route('input-metal-detector.index') }}">
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
                                            Data Metal Detector
                                        @endif
                                    </small>
                                </div>
                            </div>

                            @include('qc-sistem.input_metal_detector._table')
                        </div>

                        <div class="card-footer d-flex justify-content-center">
                            {{ $metalDetector->links('pagination::bootstrap-4') }}
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
                <h5 class="modal-title" id="exportPdfModalLabel">Cetak PDF Metal Detector Berdasarkan Filter</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="bulkExportForm" action="{{ route('input-metal-detector.bulk-export-pdf') }}" method="POST" data-bulk-form="true">
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
                        <label for="filterProduk">Nama Produk</label>
                        <select class="form-control" id="id_produk" name="id_produk">
                            <option value="">Semua Produk</option>
                            @foreach($cachedProduk as $produk)
                                    <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="bulkKodeForm">Kode Form</label>
                        <input type="text" class="form-control" id="bulkKodeForm" name="kode_form" value="QF 18/00" placeholder="Masukkan kode form" required>
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

@endsection