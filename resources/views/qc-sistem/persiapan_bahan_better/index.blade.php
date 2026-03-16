{{-- filepath: resources/views/qc-sistem/persiapan_bahan_better/index.blade.php --}}
@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-industry text-primary"></i> Persiapan Bahan Better</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item active">Persiapan Bahan Better</li>
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
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-list"></i> Data Persiapan Bahan Better</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#exportPdfModal">
                                    <i class="fas fa-file-pdf"></i> Cetak PDF Filter
                                </button>
                                    @if(auth()->user()->hasPermissionTo('create-persiapan-bahan-better'))
                                <a href="{{ route('persiapan-bahan-better.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Tambah Data
                                </a>
                            @endif
</div>
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
                                    <i class="icon fas fa-times"></i> {{ session('error') }}
                                </div>
                            @endif
                            
                            <!-- Form Pencarian Server-Side -->
                            <div class="row mb-3 mt-3">
                                <div class="col-md-6">
                                    <form method="GET" action="{{ route('persiapan-bahan-better.index') }}">
                                        <div class="input-group input-group-sm" style="width: 300px;">
                                            <input type="text" class="form-control" name="search" placeholder="Cari nama produk atau kode produksi" value="{{ $search ?? '' }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="submit">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                                @if(!empty($search))
                                                    <a class="btn btn-outline-danger" href="{{ route('persiapan-bahan-better.index') }}">
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
                                        <form method="GET" action="{{ route('persiapan-bahan-better.index') }}">
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
                                            Data Persiapan Bahan Better
                                        @endif
                                    </small>
                                </div>
                            </div>
                            
                            <div class="table-responsive">
                                <table  style="white-space: nowrap;" class="text-center table table-bordered table-striped table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="">No</th>
                                            <th class="">Shift</th>
                                            <th class="">Tanggal</th>
                                            <th class="">Jam</th>
                                            <th class="">Nama Produk</th>
                                            <th class="">Better</th>
                                            <th class="">Kode Produksi Produk</th>
                                            <!-- <th class="">Kode Produksi Better </th> -->
                                            <th class="">Dibuat Oleh</th>
                                            <!-- <th class="">Berat Better (gram)</th>
                                            <th class="">Berat Air (gram)</th>
                                            <th class="">Suhu Air (°C)</th>
                                            <th colspan="6" class="">Detail STD & Aktual</th>
                                            <th class="">Sensori</th> -->
                                            <th class="">Aksi</th>
                                        </tr>
                                        <!-- <tr>
                                            <th class="">Std Viskositas</th>
                                            <th class="">Std Salinitas</th>
                                            <th class="">Std Suhu Akhir</th>
                                            <th class="">Aktual Viskositas</th>
                                            <th class="">Aktual Salinitas</th>
                                            <th class="">Aktual Suhu Akhir</th>
                                        </tr> -->
                                    </thead>
                                    <tbody>
                                        @foreach($data as $i => $item)
                                        @php
                                                $rowspan = max(1, $item->aktuals->count());
                                                // Hitung nomor baris yang konsisten (mendukung pagination jika ada)
                                                $no = (method_exists($data, 'firstItem') && $data->firstItem())
                                                    ? ($data->firstItem() + $loop->iteration - 1)
                                                    : $loop->iteration;
                                            @endphp
                                             @for($a = 0; $a < $rowspan; $a++)
                                             <tr>
                                                 @if($a == 0)
                                                     <td rowspan="{{ $rowspan }}" class=" align-middle">
                                                        <span>{{ $no }}</span>
                                                    </td>
                                                    <td class=" align-middle">
                                                        @if($item->shift_id == 1)
                                                        <span class="badge bg-primary">Shift {{ $item->shift->shift ?? 'Shift 1' }}</span>
                                                        @elseif($item->shift_id == 2)
                                                        <span class="badge bg-success">Shift {{ $item->shift->shift ?? 'Shift 2' }}</span>
                                                        @else
                                                        <span class="badge bg-secondary">Shift {{ $item->shift->shift ?? 'Shift ' . $item->shift_id }}</span>
                                                        @endif
                                                    </td>
                                                    <td rowspan="{{ $rowspan }}" class="align-middle">
                                                            <span class="badge badge-secondary">
                                                                @php
                                                                    $userRole = auth()->user()->id_role ?? null;
                                                                    $showTime = in_array($userRole, [1, 2, 5]); // superadmin, admin, spv
                                                                    $format = $showTime ? 'd-m-Y H:i:s' : 'd-m-Y';
                                                                @endphp
                                                                {{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format($format) : '-' }}
                                                            </span>
                                                        </td>
                                                    <td rowspan="{{ $rowspan }}" class="align-middle">
                                                        <span>{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</span>
                                                    </td>
                                                    <td rowspan="{{ $rowspan }}" class="align-middle">
                                                        <span>{{ $item->produk->nama_produk ?? '-' }}</span>
                                                    </td>
                                                     <td rowspan="{{ $rowspan }}" class="align-middle">
                                                         <span>{{ $item->better->nama_better ?? '-' }}</span>
                                                     </td>
                                                     <td rowspan="{{ $rowspan }}" class="align-middle">
                                                         <span>{{ $item->kode_produksi_produk }}</span>
                                                     </td>
                                                     <!-- <td rowspan="{{ $rowspan }}" class="align-middle">
                                                         <span>{{ $item->kode_produksi_better }}</span>
                                                     </td> -->
                                                     <td rowspan="{{ $rowspan }}" class="align-middle">
                                                         <span>{{ $item->user->name ?? '-' }}</span>
                                                     </td>
                                                     <!-- <td rowspan="{{ $rowspan }}" class=" align-middle">
                                                         <span>{{ intval($item->berat_better) }} gram</span>
                                                     </td>
                                                     <td rowspan="{{ $rowspan }}" class=" align-middle">
                                                         <span>{{ intval($item->suhu_air) }}°C</span>
                                                     </td>
                                                 @endif
 　　　　　　　　　　　　　　　　
                                                 @if($item->aktuals->count())
                                                     <td class="">{{ $item->aktuals[$a]->std->std_viskositas ?? '-' }}</td>
                                                     <td class="">{{ $item->aktuals[$a]->std->std_salinitas ?? '-' }}</td>
                                                     <td class="">{{ $item->aktuals[$a]->std->std_suhu_akhir ?? '-' }}</td>
                                                     <td class="">{{ $item->aktuals[$a]->aktual_vis ?? '-' }}</td>
                                                     <td class="">{{ $item->aktuals[$a]->aktual_sal ?? '-' }}</td>
                                                     <td class="">{{ $item->aktuals[$a]->aktual_suhu_air ?? '-' }}</td>
                                                     <td rowspan="{{ $rowspan }}" class=" align-middle">
                                                         <span>{{ $item->sensori }}</span>
                                                     </td> -->
                                                     <td rowspan="{{ $rowspan }}" class="text-center align-middle">
                                                         <div class="btn-vertical">
                                                             <!-- Tombol CRUD -->
                                                             <div class="mb-1">
                                                                 <a href="{{ route('persiapan-bahan-better.show', $item->uuid) }}" 
                                                                    class="btn btn-primary btn-sm" title="Lihat Detail">
                                                                     <i class="fas fa-eye"></i>
                                                                 </a>
@if(auth()->user()->hasPermissionTo('edit-persiapan-bahan-better'))
                                                                 <a href="{{ route('persiapan-bahan-better.edit', $item->uuid) }}" 
                                                                    class="btn btn-warning btn-sm" title="Edit Data">
                                                                     <i class="fas fa-edit"></i>
                                                                 </a>
                                                                 @endif
@if(auth()->user()->hasPermissionTo('view-persiapan-bahan-better'))
<a href="{{ route('persiapan-bahan-better.logs', $item->uuid) }}" 
                                                                    class="btn btn-info btn-sm" title="Lihat Riwayat Perubahan">
                                                                     <i class="fas fa-history"></i>
                                                                 </a>
                                                                 @endif
@if(auth()->user()->hasPermissionTo('delete-persiapan-bahan-better'))
<form action="{{ route('persiapan-bahan-better.destroy', $item->uuid) }}" 
                                                                       method="POST" style="display:inline;">
                                                                     @csrf @method('DELETE')
                                                                     <button type="submit" class="btn btn-danger btn-sm" 
                                                                             onclick="return confirm('Yakin hapus?')" title="Hapus Data">
                                                                         <i class="fas fa-trash"></i>
                                                                     </button>
                                                                 </form>
                                                             @endif
</div>

                                                             <!-- Tombol Persetujuan berdasarkan Role -->
                                                             @php
                                                                 $userRole = auth()->user()->id_role ?? null;
                                                             @endphp

                                                             <!-- Role-Based Button Display -->
                                                             <div class="btn-group-vertical mb-1" role="group">
                                                                 @if(in_array($userRole, [1, 5]))
                                                                     <!-- Role 1 dan 5: Tampilkan semua tombol dengan QC yang bisa diklik -->
                                                                     <button type="button" 
                                                                             class="btn btn-sm {{ $item->approved_by_qc ? 'btn-success' : 'btn-outline-success' }} approve-btn" 
                                                                             data-id="{{ $item->uuid }}" 
                                                                             data-type="qc"
                                                                             title="Disetujui oleh QC"
                                                                             {{ $item->approved_by_qc ? 'disabled' : '' }}>
                                                                         <i class="fas {{ $item->approved_by_qc ? 'fa-check-circle' : 'fa-check' }}"></i> QC
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

                                                                 @else
                                                                     <!-- Role lain: Tampilkan semua tombol sebagai read-only -->
                                                                     <button type="button" 
                                                                             class="btn btn-sm {{ $item->approved_by_qc ? 'btn-success' : 'btn-secondary' }}" 
                                                                             title="{{ $item->approved_by_qc ? 'Sudah disetujui QC' : 'Menunggu persetujuan QC' }}"
                                                                             disabled>
                                                                         <i class="fas {{ $item->approved_by_qc ? 'fa-check-circle' : 'fa-clock' }}"></i> QC
                                                                     </button>
                                                                     <button type="button" 
                                                                             class="btn btn-sm {{ $item->approved_by_produksi ? 'btn-primary' : 'btn-secondary' }}" 
                                                                             title="{{ $item->approved_by_produksi ? 'Sudah disetujui Produksi' : 'Menunggu persetujuan Produksi' }}"
                                                                             disabled>
                                                                         <i class="fas {{ $item->approved_by_produksi ? 'fa-check-circle' : 'fa-clock' }}"></i> Produksi
                                                                     </button>
                                                                     <button type="button" 
                                                                             class="btn btn-sm {{ $item->approved_by_spv ? 'btn-dark' : 'btn-secondary' }}" 
                                                                             title="{{ $item->approved_by_spv ? 'Sudah disetujui SPV' : 'Menunggu persetujuan SPV' }}"
                                                                             disabled>
                                                                         <i class="fas {{ $item->approved_by_spv ? 'fa-check-circle' : 'fa-clock' }}"></i> SPV
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
                                                         </div>
                                                     </td>
                                                 @else
                                                     <td colspan="6" class="">-</td>
                                                 @endif
                                             </tr>
                                             @endfor
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-3">
                                {{ $data->appends(['search' => $search ?? '', 'per_page' => $perPage ?? ''])->links('pagination::bootstrap-4') }}
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
                <h5 class="modal-title" id="exportPdfModalLabel">Cetak PDF Berdasarkan Filter</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="bulkExportForm" action="{{ route('persiapan-bahan-better.export-pdf') }}" method="POST" data-bulk-form="true">
                    @csrf
                    <div class="form-group">
                        <label for="filterTanggal">Tanggal</label>
                        <input type="date" class="form-control" id="filterTanggal" name="tanggal_dari">
                    </div>
                    <div class="form-group">
                        <label for="filterShift">Shift</label>
                        <select class="form-control" id="filterShift" name="shift_filter">
                            <option value="">Semua Shift</option>
                            @foreach($cachedShifts as $shift)
                                    <option value="{{ $shift->id }}">{{ $shift->shift }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="filterProduk">Produk</label>
                        <select class="form-control" id="id_produk_select_better" name="produk_filter">
                            <option value="">Semua Produk</option>
                            @if(auth()->user()->role === 'superadmin')
                                @foreach(\App\Models\JenisProduk::all() as $produk)
                                    <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                                @endforeach
                            @else
                                @foreach(\App\Models\JenisProduk::where('id_plan', auth()->user()->id_plan)->get() as $produk)
                                    <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="bulkKodeForm">Kode Form</label>
                        <input type="text" class="form-control" id="bulkKodeForm" name="kode_form" value="QF 06/00" placeholder="Masukkan kode form" required>
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

<!-- CSS untuk Sequential Approval -->
<style>
.btn-group-vertical .btn {
    margin-bottom: 2px;
}
.btn-outline-success:hover {
    background-color: #28a745;
    border-color: #28a745;
}
.btn-outline-primary:hover {
    background-color: #007bff;
    border-color: #007bff;
}
.btn-outline-dark:hover {
    background-color: #343a40;
    border-color: #343a40;
}
.approval-pending {
    opacity: 0.6;
    cursor: not-allowed;
}
.approval-ready {
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.7); }
    70% { box-shadow: 0 0 0 10px rgba(0, 123, 255, 0); }
    100% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0); }
}
</style>

<!-- JavaScript untuk Handle Approval -->
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
                url: '{{ route("persiapan-bahan-better.approve", ":id") }}'.replace(':id', id),
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
                            'produksi': '<i class="fas fa-check"></i> Fm/Fl PRODUKSI',
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
                        'produksi': '<i class="fas fa-check"></i>FM/FL PRODUKSI',
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