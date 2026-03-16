@extends('layouts.app')

@section('title', 'Data Pemeriksaan Rice Bites')

@php
use Illuminate\Support\Facades\Schema;
@endphp

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Data Pemeriksaan Rice Bites</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            <li class="breadcrumb-item active">Pemeriksaan Rice Bites</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

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

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list mr-2"></i>Daftar Pemeriksaan Rice Bites
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-sm btn-danger mr-2" data-toggle="modal" data-target="#pdfExportModal">
                                <i class="fas fa-file-pdf"></i> Cetak PDF
                            </button>
                                    @if(auth()->user()->hasPermissionTo('create-pemeriksaan-rice-bites'))
                            <a href="{{ route('pemeriksaan-rice-bites.create') }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus"></i> Tambah Data
                            </a>
                        @endif
</div>
                    </div>
                    <div class="card-body">
                        <!-- Form Pencarian Server-Side -->
                        <div class="row mb-3 mt-3">
                            <div class="col-md-6">
                                <form method="GET" action="{{ route('pemeriksaan-rice-bites.index') }}">
                                    <div class="input-group input-group-sm" style="width: 300px;">
                                        <input type="text" class="form-control" name="search" placeholder="Cari nama produk" value="{{ $search ?? '' }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="submit">
                                                <i class="fas fa-search"></i>
                                            </button>
                                            @if(!empty($search))
                                                <a class="btn btn-outline-danger" href="{{ route('pemeriksaan-rice-bites.index') }}">
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
                                    <form method="GET" action="{{ route('pemeriksaan-rice-bites.index') }}">
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
                                        Data Pemeriksaan Rice Bites
                                    @endif
                                </small>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table  class="table table-bordered text-center table-striped" style="white-space: nowrap;">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Tanggal</th>
                                        <th>Jam</th>
                                        <th>Shift</th>
                                        <th>Produk</th>
                                        <th>Batch</th>
                                        <th>No Cooking Cycle</th>
                                        <th>Rata-rata Suhu</th>
                                        <th>Hasil Pencampuran</th>
                                        <th>Dibuat Oleh</th>
                                    
                                        <th width="20%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($data as $index => $item)
                                        <tr>
                                            <td>{{ $data->firstItem() + $index }}</td>
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
                                            <td>{{ $item->produk->nama_produk ?? '-' }}</td>
                                            <td>
                                                {{ $item->batch }}
                                            </td>
                                            <td>{{ $item->no_cooking_cycle }}</td>
                                            <td class="text-center">
                                                {{ number_format($item->rata_rata_suhu, 2) }}°C
                                            </td>
                                            <td class="text-center">
                                                {!! $item->hasil_pencampuran_icon !!} {{ $item->hasil_pencampuran ?? '-' }}
                                            </td>
                                            <td>{{ $item->user->name ?? '-' }}</td>
                                           
                                            <td>
                                                <!-- <div class="btn-vertical">
                                                    <div class="btn-vertical mb-1">
                                                        <a href="{{ route('pemeriksaan-rice-bites.show', $item->uuid) }}" 
                                                           class="btn btn-info btn-sm" title="Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
@if(auth()->user()->hasPermissionTo('edit-pemeriksaan-rice-bites'))
                                                        <a href="{{ route('pemeriksaan-rice-bites.edit', $item->uuid) }}" 
                                                           class="btn btn-warning btn-sm" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        @endif
@if(auth()->user()->hasPermissionTo('delete-pemeriksaan-rice-bites'))
<form action="{{ route('pemeriksaan-rice-bites.destroy', $item->uuid) }}" 
                                                            method="POST" class="d-inline" 
                                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
@endif
@if(auth()->user()->hasPermissionTo('view-pemeriksaan-rice-bites'))
                                                        <a href="{{ route('pemeriksaan-rice-bites.logs', $item->uuid) }}" 
                                                           class="btn btn-primary btn-sm" title="Riwayat Perubahan">
                                                            <i class="fas fa-history"></i>
                                                        </a>
                                                    @endif
</div> -->
                                                    <x-action-buttons :item="$item" route-prefix="pemeriksaan-rice-bites"/>
                                                    
                                                    <!-- Approval Buttons -->
                                                    @php
                                                        $userRole = auth()->user()->id_role ?? null;
                                                        $hasApprovalColumns = Schema::hasColumn('pemeriksaan_rice_bites', 'approved_by_qc');
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
                                            <td colspan="11" class="text-center">
                                                <div class="py-4">
                                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted">Belum ada data pemeriksaan rice bites</p>
                                    @if(auth()->user()->hasPermissionTo('create-pemeriksaan-rice-bites'))
                                                    <a href="{{ route('pemeriksaan-rice-bites.create') }}" class="btn btn-primary">
                                                        <i class="fas fa-plus"></i> Tambah Data Pertama
                                                    </a>
                                                @endif
</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            {{ $data->appends(['search' => $search ?? '', 'per_page' => $perPage ?? ''])->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                    
                </div>
            </div>
        </section>
    </div>
</div>

<!-- PDF Export Modal -->
<div class="modal fade" id="pdfExportModal" tabindex="-1" role="dialog" aria-labelledby="pdfExportModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfExportModalLabel">
                    <i class="fas fa-file-pdf text-danger"></i> Export PDF Pemeriksaan Rice Bites
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="pdfExportForm" action="{{ route('pemeriksaan-rice-bites.bulk-export-pdf') }}" method="POST" target="_blank">
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
                                <label for="export_produk">Produk</label>
                                <select class="form-control" id="id_produk" name="produk">
                                    <option value="">Semua Produk</option>
                                    @foreach($data->pluck('produk.nama_produk')->unique()->filter() as $produk)
                                        <option value="{{ $produk }}">{{ $produk }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="export_kode_form">Kode Form <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="export_kode_form" name="kode_form" value="QF 14/00" required 
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
                url: '{{ route("pemeriksaan-rice-bites.approve", ":id") }}'.replace(':id', id),
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
