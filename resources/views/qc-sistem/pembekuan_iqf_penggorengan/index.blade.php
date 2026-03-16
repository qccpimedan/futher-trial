@extends('layouts.app')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h1 class="m-0">Data Pembekuan IQF Penggorengan</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                            <li class="breadcrumb-item active">Pembekuan IQF Penggorengan</li>
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
                                    <i class="fas fa-snowflake"></i> Data Pembekuan IQF Penggorengan
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-danger btn-sm mr-2" data-toggle="modal" data-target="#exportPdfModal">
                                        <i class="fas fa-file-pdf"></i> Cetak PDF
                                    </button>
                                    <!--
                                    @if(auth()->user()->hasPermissionTo('create-pembekuan-iqf-penggorengan')) <a href="{{ route('pembekuan-iqf-penggorengan.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Tambah Data
                                    </a> @endif
-->
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
                                        <i class="icon fas fa-ban"></i> {{ session('error') }}
                                    </div>
                                @endif

                                <div class="row mb-3 mt-3">
                                    <div class="col-md-6">
                                        <form method="GET" action="{{ route('pembekuan-iqf-penggorengan.index') }}">
                                            <div class="input-group input-group-sm" style="width: 300px;">
                                                <input type="text" class="form-control" name="search" placeholder="Cari nama produk" value="{{ $search ?? '' }}">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="submit">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                    @if(!empty($search))
                                                        <a class="btn btn-outline-danger" href="{{ route('pembekuan-iqf-penggorengan.index') }}">
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
                                            <form method="GET" action="{{ route('pembekuan-iqf-penggorengan.index') }}">
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
                                                Data Pembekuan IQF Penggorengan
                                            @endif
                                        </small>
                                    </div>
                                </div>

                                @if(count($data))
                                <div class="table-responsive">
                                    <table  class="table text-center table-bordered table-striped table-hover" style="white-space: nowrap;">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>No</th>
                                                <th>Shift</th>
                                                <th>Tanggal</th>
                                                <th>Jam</th>
                                                <th>Produk</th>
                                                <th>Suhu Ruang IQF</th>
                                                <th>Holding Time</th>
                                                <th>Dibuat Oleh</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($data as $item)
                                            <tr>
                                                <td>{{ $data->firstItem() + $loop->index }}</td>
                                                <td>
                                                    @php
                                                        $shift = null;
                                                        if($item->penggorenganData && $item->penggorenganData->shift) {
                                                            $shift = $item->penggorenganData->shift;
                                                        } elseif($item->hasilPenggorenganData && $item->hasilPenggorenganData->penggorengan && $item->hasilPenggorenganData->penggorengan->shift) {
                                                            $shift = $item->hasilPenggorenganData->penggorengan->shift;
                                                        } elseif($item->frayerData && $item->frayerData->penggorengan && $item->frayerData->penggorengan->shift) {
                                                            $shift = $item->frayerData->penggorengan->shift;
                                                        } elseif($item->frayer2Data && $item->frayer2Data->penggorengan && $item->frayer2Data->penggorengan->shift) {
                                                            $shift = $item->frayer2Data->penggorengan->shift;
                                                        } elseif($item->breaderData && $item->breaderData->penggorengan && $item->breaderData->penggorengan->shift) {
                                                            $shift = $item->breaderData->penggorengan->shift;
                                                        } elseif($item->batteringData && $item->batteringData->penggorengan && $item->batteringData->penggorengan->shift) {
                                                            $shift = $item->batteringData->penggorengan->shift;
                                                        } elseif($item->predustData && $item->predustData->penggorengan && $item->predustData->penggorengan->shift) {
                                                            $shift = $item->predustData->penggorengan->shift;
                                                        }
                                                    @endphp
                                                    
                                                    @if($shift)
                                                        @if($shift->shift == 1)
                                                            <span class="badge bg-primary">Shift 1</span>
                                                        @elseif($shift->shift == 2)
                                                            <span class="badge bg-success">Shift 2</span>
                                                        @elseif($shift->shift == 3)
                                                            <span class="badge bg-secondary">Shift 3</span>
                                                        @else
                                                            <span class="badge bg-info">{{ $shift->shift }}</span>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-warning">-</span>
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
                                                    {{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}
                                                </td>
                                                <td>
                                                    @php
                                                        $penggorengan = null;
                                                        if($item->penggorenganData) {
                                                            $penggorengan = $item->penggorenganData;
                                                        } elseif($item->hasilPenggorenganData && $item->hasilPenggorenganData->penggorengan) {
                                                            $penggorengan = $item->hasilPenggorenganData->penggorengan;
                                                        } elseif($item->frayerData && $item->frayerData->penggorengan) {
                                                            $penggorengan = $item->frayerData->penggorengan;
                                                        } elseif($item->frayer2Data && $item->frayer2Data->penggorengan) {
                                                            $penggorengan = $item->frayer2Data->penggorengan;
                                                        } elseif($item->breaderData && $item->breaderData->penggorengan) {
                                                            $penggorengan = $item->breaderData->penggorengan;
                                                        } elseif($item->batteringData && $item->batteringData->penggorengan) {
                                                            $penggorengan = $item->batteringData->penggorengan;
                                                        } elseif($item->predustData && $item->predustData->penggorengan) {
                                                            $penggorengan = $item->predustData->penggorengan;
                                                        }
                                                    @endphp            
                                                    @if($penggorengan)
                                                        @if($penggorengan->produk)
                                                            {{ $penggorengan->produk->nama_produk }}
                                                        @else
                                                            -
                                                        @endif
                                                        @if($penggorengan->berat_produk)
                                                            ({{ $penggorengan->berat_produk }}gram)
                                                        @else
                                                            -
                                                        @endif
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ $item->suhu_ruang_iqf }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-warning">{{ $item->holding_time }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">{{ $item->user->name ?? '-' }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <!-- <div class="btn-vertical">
                                                        <div class="mb-1">
@if(auth()->user()->hasPermissionTo('edit-pembekuan-iqf-penggorengan'))
                                                            <a href="{{ route('pembekuan-iqf-penggorengan.edit', ['uuid' => $item->uuid]) }}" 
                                                               class="btn btn-warning btn-sm" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            @endif
@if(auth()->user()->hasPermissionTo('view-pembekuan-iqf-penggorengan'))
<a href="{{ route('pembekuan-iqf-penggorengan.logs', ['uuid' => $item->uuid]) }}" 
                                                               class="btn btn-info btn-sm" title="Lihat Log Perubahan">
                                                                <i class="fas fa-history"></i>
                                                            </a>
                                                            @endif
@if(auth()->user()->hasPermissionTo('delete-pembekuan-iqf-penggorengan'))
<form action="{{ route('pembekuan-iqf-penggorengan.destroy', ['uuid' => $item->uuid]) }}" 
                                                                  method="POST" style="display: inline;" 
                                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        @endif
</div> -->
                                                        <x-action-buttons :item="$item" route-prefix="pembekuan-iqf-penggorengan" :show-view="false" />

                                                        <!-- Approval Buttons -->
                                                        @php
                                                            $userRole = auth()->user()->id_role ?? null;
                                                            $hasApprovalColumns = \Illuminate\Support\Facades\Schema::hasColumn('pembekuan_iqf_penggorengan', 'approved_by_qc');
                                                        @endphp

                                                        <!-- Role-Based Button Display -->
                                                        @if($hasApprovalColumns)
                                                        <div class="btn-group-vertical'" role="group">
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
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $data->appends(['search' => $search ?? '', 'per_page' => $perPage ?? ''])->links('pagination::bootstrap-4') }}
                                </div>
                                @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Tidak ada data pembekuan IQF penggorengan yang tersedia.
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
                <h5 class="modal-title" id="exportPdfModalLabel">Cetak PDF Alur Proses Pembekuan IQF</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="bulkExportForm" action="{{ route('pembekuan-iqf-penggorengan.bulk-export-pdf') }}" method="POST" data-bulk-form="true">
                    @csrf
                    <div class="form-group">
                        <label for="filterTanggal">Tanggal</label>
                        <input type="date" class="form-control" id="filterTanggal" name="tanggal">
                    </div>
                    <div class="form-group">
                        <label for="filterShift">Shift</label>
                        <select class="form-control" id="filterShift" name="shift">
                            <option value="">Semua Shift</option>
                            @if(auth()->user()->role === 'superadmin')
                                @foreach(\App\Models\DataShift::orderBy('shift')->get() as $shift)
                                    <option value="{{ $shift->shift }}">Shift {{ $shift->shift }}</option>
                                @endforeach
                            @else
                                @foreach(\App\Models\DataShift::where('id_plan', auth()->user()->id_plan)->orderBy('shift')->get() as $shift)
                                    <option value="{{ $shift->shift }}">Shift {{ $shift->shift }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="filterProduk">Produk</label>
                        <select class="form-control" id="id_produk_select_pembeukan_iqf_penggorengan" name="id_produk">
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
                        <input type="text" class="form-control" id="bulkKodeForm" name="kode_form" placeholder="Masukkan kode form" value="QF 09/00" required>
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
                url: '{{ route("pembekuan-iqf-penggorengan.approve", ":id") }}'.replace(':id', id),
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
    $('[data-bulk-export]').on('click', function() {
        const button = $(this);
        const form = $('#bulkExportForm');
        const kodeForm = $('#bulkKodeForm').val().trim();
        
        // Check if button is already processing
        if (button.prop('disabled')) {
            return;
        }
        
        if (!kodeForm) {
            alert('Kode form harus diisi!');
            return;
        }
        
        // Disable button to prevent multiple clicks
        button.prop('disabled', true);
        button.html('<i class="fas fa-spinner fa-spin"></i> Memproses...');
        
        // Submit form
        form.submit();
        
        // Re-enable button after a delay (in case of error)
        setTimeout(function() {
            button.prop('disabled', false);
            button.html('<i class="fas fa-download"></i> Cetak PDF');
        }, 5000);
    });
});
</script>
@endpush
@endsection
