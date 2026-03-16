@extends('layouts.app')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h1 class="m-0">Parameter Tumbling</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                            <li class="breadcrumb-item active">Parameter Tumbling</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-cogs"></i> Data Parameter Tumbling
                                </h3>
                                <!-- <div class="card-tools">
                                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#exportPdfModal">
                                        <i class="fas fa-file-pdf"></i> Export Unified PDF
                                    </button>
                                </div> -->
                            </div>
                            <div class="card-body">
                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <i class="icon fas fa-check"></i> {{ session('success') }}
                                    </div>
                                @endif

                                @if(session('error'))
                                    <div class="alert alert-danger alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <i class="icon fas fa-ban"></i> {{ session('error') }}
                                    </div>
                                @endif

                                @if(count($prosesTumbling))
                                <div class="table-responsive text-center">
                                    <!-- Form Pencarian Server-Side -->
                                    <div class="row mb-3 mt-3">
                                        <div class="col-md-4 offset-md-8">
                                            <form action="{{ route('proses-tumbling.index') }}" method="GET">
                                                <div class="input-group">
                                                    <input type="text" name="search" class="form-control" placeholder="Cari Produk atau Kode Produksi" value="{{ request('search') }}">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary" type="submit">
                                                            <i class="fas fa-search"></i> Cari
                                                        </button>
                                                        @if(request('search'))
                                                            <a href="{{ route('proses-tumbling.index') }}" class="btn btn-outline-danger" title="Clear Search">
                                                                <i class="fas fa-times"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    <table style="white-space: nowrap;" class="table table-bordered table-striped table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Shift</th>
                                            <th>Tanggal</th>
                                            <th>Jam</th>
                                            <!-- <th>Plan</th> -->
                                            <th>Nama Produk</th>
                                            <th>Kode Produksi</th>
                                            <th>Status Proses</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($prosesTumbling as $item)
                                        <tr>
                                            <td>{{ $prosesTumbling->firstItem() + $loop->index }}</td>
                                            <td>
                                                @if($item->shift->shift == 1 || $item->shift_id == 1)
                                                <span class="badge bg-primary">Shift 1</span>
                                                @elseif($item->shift->shift == 2 || $item->shift_id == 2)
                                                <span class="badge bg-success">Shift 2</span>
                                                @elseif($item->shift->shift == 3 || $item->shift_id == 3)
                                                <span class="badge bg-secondary">Shift 3</span>
                                                @else
                                                <span class="badge bg-info">{{ $item->shift->shift ?? '-' }}</span>
                                                @endif
                                            </td>
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
                                            <td>{{ $item->produk->nama_produk ?? '-' }}</td>
                                            <td>{{ $item->kode_produksi }}</td>
                                            <td>
                                                @if($item->prosesAging->count() > 0)
                                                    <span class="badge badge-success"><i class="fas fa-check-circle"></i> Selesai Aging</span>
                                                @else
                                                    <span class="badge badge-warning"><i class="fas fa-clock"></i> Menunggu Aging</span>
                                                @endif
                                            </td>
                                           
                                           
                                            <!-- Aksi -->
                                            <td class="text-center">
                                                <x-action-buttons :item="$item" route-prefix="proses-tumbling" :show-view="true"/>
                                                @if($item->prosesAging->count() == 0)
                                                    <a href="{{ route('proses-aging.create', ['proses_tumbling_id' => $item->id, 'proses_tumbling_uuid' => $item->uuid]) }}"
                                                        class="btn btn-sm btn-info" title="Lanjut ke Proses Aging">
                                                        <i class="fas fa-arrow-right"></i> Aging
                                                    </a>
                                                @else
                                                    <span class="btn btn-sm btn-info disabled" 
                                                            title="Berhasil Input">
                                                        <i class="fas fa-thumbs-up"></i>
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    </table>
                                </div>

                                <!-- Menampilkan Navigasi Pagination -->
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $prosesTumbling->appends(request()->query())->links('pagination::bootstrap-4') }}
                                </div>
                                @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Tidak ada data proses tumbling yang tersedia.
                                </div>
                                @endif
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
                <h5 class="modal-title" id="exportPdfModalLabel">Cetak PDF Berdasarkan Filter</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="bulkExportForm" action="{{ route('proses-tumbling.bulk-export-pdf') }}" method="POST" data-bulk-form="true">
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
                        <select class="form-control" id="id_produk_select_tumbling" name="id_produk">
                            <option value="">Semua Produk</option>
                            @foreach($cachedProduk as $produk)
                                    <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="bulkKodeForm">Kode Form</label>
                        <input type="text" class="form-control" id="bulkKodeForm" name="kode_form" placeholder="Masukkan kode form" required>
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