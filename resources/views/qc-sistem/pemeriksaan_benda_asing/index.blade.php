@extends('layouts.app')
@php
    $isLocalhost = in_array(request()->getHost(), ['localhost', '127.0.0.1', '10.68.1.37']);
    $assetPath = $isLocalhost ? 'public/' : '';
@endphp
@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Pemeriksaan Benda Asing</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item active">Pemeriksaan Benda Asing</li>
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
<!-- 
            <div class="row mb-2">
                <div class="col-md-6">
                    <small class="text-muted">
                        @if(!empty($search))
                            Hasil pencarian: "<strong>{{ $search }}</strong>"
                        @else
                            Data Pemeriksaan Benda Asing
                        @endif
                    </small>
                </div>
                <div class="col-md-6 text-right">
                    <small class="text-muted">
                        Menampilkan {{ $data->firstItem() ?? 0 }} sampai {{ $data->lastItem() ?? 0 }} dari {{ $data->total() }} data
                    </small>
                </div>
            </div> -->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-search text-primary mr-2"></i>
                                Data Pemeriksaan Benda Asing
                            </h3>
                            <div class="card-tools">
                                    @if(auth()->user()->hasPermissionTo('create-pemeriksaan-benda-asing'))
                                <a href="{{ route('pemeriksaan-benda-asing.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Tambah Data
                                </a>
                                @endif
                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#exportPdfModal">
                                    <i class="fas fa-file-pdf"></i> Cetak PDF
                                </button>
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
                                    <form method="GET" action="{{ route('pemeriksaan-benda-asing.index') }}">
                                        <div class="input-group input-group-sm" style="width: 300px;">
                                            <input type="text" class="form-control" name="search" placeholder="Cari jenis kontaminasi atau kode produksi" value="{{ $search ?? '' }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="submit">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                                @if(!empty($search))
                                                    <a class="btn btn-outline-danger" href="{{ route('pemeriksaan-benda-asing.index') }}">
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
                                        <form method="GET" action="{{ route('pemeriksaan-benda-asing.index') }}">
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
                                            Data Pemeriksaan Benda Asing
                                        @endif
                                    </small>
                                </div>
                                <div class="col-md-6 text-right">
                                    <small class="text-muted">
                                        Menampilkan {{ $data->firstItem() ?? 0 }} sampai {{ $data->lastItem() ?? 0 }} dari {{ $data->total() }} data
                                    </small>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table text-center table-bordered table-striped table-hover" style="white-space: nowrap;">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Shift</th>
                                            <th>Produk</th>
                                            <th>Berat</th>
                                            <th>Waktu</th>
                                            <th>Kode Produksi</th>
                                            <th>Jenis Kontaminasi</th>
                                            <th>Ukuran</th>
                                            <th>Ditemukan</th>
                                            <th>Diketahui Oleh</th>
                                            <th>Analisa Masalah</th>
                                            <th>KOreksi</th>
                                            <th>Tindakan Korektif</th>
                                            <th>Bukti</th>
                                            <th>Aksi</th>
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
                                                    <span class="badge badge-info">
                                                        Shift {{ $item->shift->shift ?? '-' }}
                                                    </span>
                                                </td>
                                                <td>{{ $item->produk->nama_produk ?? '-' }}</td>
                                                <td>{{ $item->berat !== null && $item->berat !== '' ? $item->berat . ' gram' : '-' }}</td>
                                               <td>
                                                    <span class="badge badge-secondary">{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</span>
                                                </td>
                                                <td>{{ $item->kode_produksi }}</td>
                                                <td>{{ $item->jenis_kontaminasi }}</td>
                                                <td>{{ $item->ukuran_kontaminasi }}</td>
                                                <td>{{ $item->ditemukan }}</td>
                                                <td>{{ $item->diketahui }}</td>
                                                <td>{{ $item->analisa_masalah }}</td>
                                                <td>{{ $item->koreksi }}</td>
                                                <td>{{ $item->tindak_korektif }}</td>
                                                <td class="text-center">
                                                    @if($item->bukti)
                                                        <a href="#" data-toggle="modal" data-target="#modalBukti{{ $item->id }}">
                                                            <img src="{{ asset($assetPath . 'storage/' . $item->bukti) }}" 
                                                                 alt="Bukti" 
                                                                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                                                        </a>
                                                        <!-- Modal -->
                                                        <div class="modal fade" id="modalBukti{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="modalBuktiLabel{{ $item->id }}" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="modalBuktiLabel{{ $item->id }}">Bukti Kontaminasi - {{ $item->jenis_kontaminasi }}</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body text-center">
                                                                        <img src="{{ asset($assetPath . 'storage/' . $item->bukti) }}" alt="Bukti" style="max-width:100%;max-height:70vh;">
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Tutup</button>
                                                                        <!-- <a href="{{ asset($assetPath . 'storage/' . $item->bukti) }}" target="_blank" class="btn btn-primary">
                                                                            <i class="fas fa-download"></i> Download
                                                                        </a> -->
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-vertical">
                                                        <!-- CRUD Buttons -->
                                                        <!-- <div class="mb-1">
                                                            <a href="{{ route('pemeriksaan-benda-asing.show', $item->uuid) }}" 
                                                            class="btn btn-info btn-sm" title="Detail">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
@if(auth()->user()->hasPermissionTo('edit-pemeriksaan-benda-asing'))
                                                            <a href="{{ route('pemeriksaan-benda-asing.edit', $item->uuid) }}" 
                                                            class="btn btn-warning btn-sm" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            @endif
@if(auth()->user()->hasPermissionTo('delete-pemeriksaan-benda-asing'))
<form action="{{ route('pemeriksaan-benda-asing.destroy', $item->uuid) }}" 
                                                                method="POST" style="display: inline-block;"
                                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
@endif
@if(auth()->user()->hasPermissionTo('view-pemeriksaan-benda-asing'))
                                                            <a href="{{ route('pemeriksaan-benda-asing.logs', $item->uuid) }}" 
                                                            class="btn btn-primary btn-sm" title="Riwayat Perubahan">
                                                                <i class="fas fa-history"></i>
                                                            </a>
                                                        @endif
</div> -->
                                                        <x-action-buttons :item="$item" route-prefix="pemeriksaan-benda-asing" :show-view="false"/>


                                                        <!-- Approval Buttons -->
                                                        @php
                                                            $userRole = auth()->user()->id_role ?? null;
                                                            $hasApprovalColumns = Schema::hasColumn('pemeriksaan_benda_asing', 'approved_by_qc');
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
                                                <td colspan="16" class="text-center">
                                                    <div class="py-4">
                                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                        <h5 class="text-muted">Tidak ada data pemeriksaan benda asing</h5>
                                                        <p class="text-muted">Silakan tambah data pemeriksaan benda asing baru.</p>
                                    @if(auth()->user()->hasPermissionTo('create-pemeriksaan-benda-asing'))
                                                        <a href="{{ route('pemeriksaan-benda-asing.create') }}" class="btn btn-primary">
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
                        </div>
                        
                        <div class="d-flex justify-content-center mt-3">
                            {{ $data->appends(['search' => $search ?? '', 'per_page' => $perPage ?? ''])->links('pagination::bootstrap-4') }}
                        </div>

                        <!-- @if($data->hasPages())
                            <div class="card-footer">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <p class="text-muted mb-0">
                                            Menampilkan {{ $data->firstItem() }} sampai {{ $data->lastItem() }} 
                                            dari {{ $data->total() }} data
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        {{ $data->links() }}
                                    </div>
                                </div>
                            </div>
                        @endif -->
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
                <form id="bulkExportForm" action="{{ route('pemeriksaan-benda-asing.bulk-export-pdf') }}" method="POST" data-bulk-form="true">
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
                        <label for="filterProduk">Produk</label>
                         
                        <select  name="id_produk" id="id_produk" class="form-control  @error('id_produk') is-invalid @enderror" >
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
                        <input type="text" class="form-control" id="bulkKodeForm" name="kode_form" placeholder="Masukkan kode form" value="QF 23/00" required>
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
                url: '{{ route("pemeriksaan-benda-asing.approve", ":id") }}'.replace(':id', id),
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