@extends('layouts.app')

@section('title', 'Data Pemeriksaan Produk Cooking Mixer FLA')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Data Pemeriksaan Produk Cooking Mixer FLA</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Data Pemeriksaan Produk Cooking Mixer FLA</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Daftar Pemeriksaan Produk Cooking Mixer FLA</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-danger btn-sm mr-2" data-toggle="modal" data-target="#exportPdfModal">
                                    <i class="fas fa-file-pdf"></i> Cetak PDF Filter
                                </button>
                                    @if(auth()->user()->hasPermissionTo('create-pemeriksaan-produk-cooking-mixer-fla'))
                                <a href="{{ route('pemeriksaan-produk-cooking-mixer-fla.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Tambah Data
                                </a>
                            @endif
</div>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <!-- Form Pencarian Server-Side -->
                            <div class="row mb-3 mt-3">
                                <div class="col-md-6">
                                    <form method="GET" action="{{ route('pemeriksaan-produk-cooking-mixer-fla.index') }}">
                                        <div class="input-group input-group-sm" style="width: 300px;">
                                            <input type="text" class="form-control" name="search" placeholder="Cari nama produk" value="{{ $search ?? '' }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="submit">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                                @if(!empty($search))
                                                    <a class="btn btn-outline-danger" href="{{ route('pemeriksaan-produk-cooking-mixer-fla.index') }}">
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
                                        <form method="GET" action="{{ route('pemeriksaan-produk-cooking-mixer-fla.index') }}">
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
                                            Data Pemeriksaan Produk Cooking Mixer FLA
                                        @endif
                                    </small>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered text-center table-striped"  style="white-space: nowrap;">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Plan</th>
                                            <th>Shift</th>
                                            <th>Tanggal</th>
                                            <th>Jam</th>
                                            <th>Nama Produk</th>
                                            <th>Kode Produksi</th>
                                            <th>Waktu</th>
                                            <th>Status Gas</th>
                                            <th>Speed</th>
                                            <th>Organoleptic</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($items as $index => $item)
                                            <tr>
                                                <td>{{ $items->firstItem() + $index }}</td>
                                                <td>{{ $item->plan->nama_plan ?? '-' }}</td>
                                                <td>
                                                    <span class="badge badge-info">
                                                      Shift  {{ $item->shift->shift ?? '-' }}
                                                    </span>
                                                </td>
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
                                                <td>{{ $item->namaFormulaFla->produk->nama_produk ?? '-' }} {{ $item->berat }} gram </td>
                                                <td>
                                                        {{ $item->kode_produksi }}
                                                </td>
                                                <td>
                                                    <small>
                                                        <strong>Start:</strong> {{ $item->waktu_start }}<br>
                                                        <strong>Stop:</strong> {{ $item->waktu_stop }}
                                                    </small>
                                                </td>
                                                <td class="text-center">
                                                    @if($item->status_gas)
                                                        <i class="fas fa-check text-success" title="Aktif"></i>
                                                    @else
                                                        <i class="fas fa-times text-danger" title="Tidak Aktif"></i>
                                                    @endif
                                                </td>
                                                <td>{{ $item->speed }} <small class="text-muted">RPM</small></td>
                                                <td>
                                                    <small>
                                                        <strong>Warna:</strong> {!! $item->getOrganoIcon($item->organo_warna) !!}<br>
                                                        <strong>Aroma:</strong> {!! $item->getOrganoIcon($item->organo_aroma) !!}<br>
                                                        <strong>Tekstur:</strong> {!! $item->getOrganoIcon($item->organo_tekstur) !!}<br>
                                                        <strong>Rasa:</strong> {!! $item->getOrganoIcon($item->organo_rasa) !!}
                                                    </small>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-vertical">
                                                            <!-- CRUD Buttons -->
                                                        <!-- <div class="mb-1">
                                                            <a href="{{ route('pemeriksaan-produk-cooking-mixer-fla.show', $item->uuid) }}" 
                                                            class="btn btn-info btn-sm" title="Lihat">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            @if(Auth::user()->role == 'Superadmin' || Auth::user()->role == 'Admin' || $item->user_id == Auth::id()|| Auth::user()->role == 'Spv')
@if(auth()->user()->hasPermissionTo('edit-pemeriksaan-produk-cooking-mixer-fla'))
                                                            <a href="{{ route('pemeriksaan-produk-cooking-mixer-fla.edit', $item->uuid) }}" 
                                                            class="btn btn-warning btn-sm" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                           @endif
 @endif
@if(auth()->user()->hasPermissionTo('view-pemeriksaan-produk-cooking-mixer-fla'))
                                                            <a href="{{ route('pemeriksaan-produk-cooking-mixer-fla.logs', $item->uuid) }}" 
                                                            class="btn btn-secondary btn-sm" title="Lihat Riwayat">
                                                                <i class="fas fa-history"></i>
                                                            </a>
                                                            @endif
@if(Auth::user()->role == 'Superadmin' || Auth::user()->role == 'Admin'|| Auth::user()->role == 'Spv')
@if(auth()->user()->hasPermissionTo('delete-pemeriksaan-produk-cooking-mixer-fla'))
                                                            <form action="{{ route('pemeriksaan-produk-cooking-mixer-fla.destroy', $item->uuid) }}" 
                                                                method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                           @endif
 @endif
                                                        </div> -->
                                                        <x-action-buttons :item="$item" route-prefix="pemeriksaan-produk-cooking-mixer-fla"/>
                                                        <!-- Approval Buttons -->
                                                        @php
                                                            $userRole = auth()->user()->id_role ?? null;
                                                            $hasApprovalColumns = Schema::hasColumn('pemeriksaan_produk_cooking_mixer_fla', 'approved_by_qc');
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
                                                <td colspan="11" class="text-center">Tidak ada data</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center mt-3">
                                {{ $items->appends(['search' => $search ?? '', 'per_page' => $perPage ?? ''])->links('pagination::bootstrap-4') }}
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
                <form id="bulkExportForm" action="{{ route('pemeriksaan-produk-cooking-mixer-fla.bulk-export-pdf') }}" method="POST" data-bulk-form="true">
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
                        <input type="text" class="form-control" id="bulkKodeForm" name="kode_form" value="QF 12/00" placeholder="Masukkan kode form" required>
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
                url: '{{ route("pemeriksaan-produk-cooking-mixer-fla.approve", ":id") }}'.replace(':id', id),
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
