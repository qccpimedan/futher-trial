
@extends('layouts.app')

@php
use Illuminate\Support\Facades\Schema;
@endphp

@section('title', 'Data Verif CIP')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Data Verif CIP</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Verif CIP</li>
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
                                <h3 class="card-title">Data Verif CIP</h3>
                                <div class="card-tools">
                                    @if(auth()->user()->hasPermissionTo('create-verif-cip'))
                                    <a href="{{ route('verif-cip.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Tambah Data
                                    </a>
                                    @endif
<button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#exportPdfModal">
                                        <i class="fas fa-file-pdf"></i> Export PDF
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

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="input-group input-group-sm" style="width: 300px;">
                                            <input type="text" id="searchInput" class="form-control" placeholder="Cari data..." value="{{ $search ?? '' }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                                <button class="btn btn-outline-danger" type="button" id="clearBtn" title="Hapus pencarian" style="display: none;">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="float-right">
                                            <select id="perPageSelect" class="form-control form-control-sm" style="width: 80px;">
                                                <option value="5" {{ (int)($perPage ?? 15) === 5 ? 'selected' : '' }}>5</option>
                                                <option value="10" {{ (int)($perPage ?? 15) === 10 ? 'selected' : '' }}>10</option>
                                                <option value="25" {{ (int)($perPage ?? 15) === 25 ? 'selected' : '' }}>25</option>
                                                <option value="50" {{ (int)($perPage ?? 15) === 50 ? 'selected' : '' }}>50</option>
                                                <option value="100" {{ (int)($perPage ?? 15) === 100 ? 'selected' : '' }}>100</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            @if(!empty($search))
                                                Hasil pencarian: "<strong>{{ $search }}</strong>"
                                            @else
                                                Data Verif CIP
                                            @endif
                                        </small>
                                    </div>
                                </div>

                                <div class="table-responsive text-center">
                                    <table class="table table-bordered table-striped" style="white-space:nowrap;">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>No</th>
                                                <th>Tanggal</th>
                                                <th>Jenis Mouldrum</th>
                                                <th>Dibuat Oleh</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $no = ($rowsPaginator->currentPage() - 1) * $rowsPaginator->perPage();
                                                $isSpecialRole = ((int) (auth()->user()->id_role ?? 0) === 2 || (int) (auth()->user()->id_role ?? 0) === 3);
                                            @endphp
                                            @forelse($rowsPaginator as $row)
                                                @php
                                                    $item = $row['item'];
                                                    $formIndex = $row['formIndex'];
                                                    $detailIndex = $row['detailIndex'] ?? 0;
                                                    $formTanggal = $row['formTanggal'];
                                                    $detail = $row['detail'] ?? null;
                                                    $jenisMouldrum = is_array($detail) ? (data_get($detail, 'jenis_mouldrum') ?: '-') : '-';
                                                    $userRole = auth()->user()->id_role ?? null;
                                                    $hasApprovalColumns = Schema::hasColumn('verif_cip', 'approved_by_qc');
                                                @endphp

                                                @php
                                                    $no++;
                                                @endphp
                                                <tr>
                                                    <td class="text-center">{{ $no }}</td>
                                                    <td class="text-center">
                                                        <span class="badge badge-secondary">
                                                            {{ $formTanggal ? \Carbon\Carbon::parse($formTanggal)->format('d-M-Y') : '-' }}
                                                        </span>
                                                    </td>
                                                    <td class="text-left">{{ $jenisMouldrum }}</td>
                                                    <td>{{ $item->user->name ?? '-' }}</td>
                                                    <td class="text-center">
                                                        <div class="btn-group" role="group">
                                                            <a href="{{ route('verif-cip.show', $item->uuid) }}" class="btn btn-info btn-sm" title="Show">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <x-action-buttons :item="$item" route-prefix="verif-cip" :show-view="false"/>
                                                        </div>

                                                        @if($hasApprovalColumns)
                                                            <div class="mt-2">
                                                                <div class="btn-group-vertical" role="group">
                                                                    @if(in_array($userRole, [1, 5]))
                                                                        <button type="button"
                                                                                class="btn btn-sm {{ isset($item->approved_by_qc) && $item->approved_by_qc ? 'btn-success' : 'btn-outline-success' }} approve-btn"
                                                                                data-id="{{ $item->uuid }}"
                                                                                data-type="qc"
                                                                                title="Disetujui oleh QC"
                                                                                {{ isset($item->approved_by_qc) && $item->approved_by_qc ? 'disabled' : '' }}>
                                                                            <i class="fas {{ isset($item->approved_by_qc) && $item->approved_by_qc ? 'fa-check-circle' : 'fa-check' }}"></i> QC
                                                                        </button>
                                                                        <button type="button"
                                                                                class="btn btn-sm {{ $item->approved_by_produksi ? 'btn-primary' : 'btn-secondary' }}"
                                                                                title="{{ $item->approved_by_produksi ? 'Sudah disetujui Produksi' : 'Menunggu persetujuan Produksi' }}"
                                                                                disabled>
                                                                            <i class="fas {{ $item->approved_by_produksi ? 'fa-check-circle' : 'fa-clock' }}"></i> FM/FL PRODUKSI
                                                                        </button>
                                                                        <button type="button"
                                                                                class="btn btn-sm {{ $item->approved_by_spv ? 'btn-dark' : 'btn-secondary' }}"
                                                                                title="{{ $item->approved_by_spv ? 'Sudah disetujui SPV' : 'Menunggu persetujuan SPV' }}"
                                                                                disabled>
                                                                            <i class="fas {{ $item->approved_by_spv ? 'fa-check-circle' : 'fa-clock' }}"></i> SPV
                                                                        </button>
                                                                    @elseif($userRole == 2)
                                                                        <button type="button"
                                                                                class="btn btn-sm {{ $item->approved_by_produksi ? 'btn-primary' : ($item->approved_by_qc ? 'btn-outline-primary' : 'btn-secondary') }} {{ $item->approved_by_qc && !$item->approved_by_produksi ? 'approve-btn' : '' }}"
                                                                                data-id="{{ $item->uuid }}"
                                                                                data-type="produksi"
                                                                                title="{{ !$item->approved_by_qc ? 'Menunggu persetujuan QC terlebih dahulu' : ($item->approved_by_produksi ? 'Sudah disetujui Produksi' : 'Disetujui oleh Produksi') }}"
                                                                                {{ !$item->approved_by_qc || $item->approved_by_produksi ? 'disabled' : '' }}>
                                                                            <i class="fas {{ $item->approved_by_produksi ? 'fa-check-circle' : (!$item->approved_by_qc ? 'fa-clock' : 'fa-check') }}"></i> Disetujui oleh Produksi
                                                                        </button>
                                                                    @elseif($userRole == 3)
                                                                        <button type="button"
                                                                                class="btn btn-sm {{ $item->approved_by_qc ? 'btn-success' : 'btn-outline-success' }} approve-btn"
                                                                                data-id="{{ $item->uuid }}"
                                                                                data-type="qc"
                                                                                title="Disetujui oleh QC"
                                                                                {{ $item->approved_by_qc ? 'disabled' : '' }}>
                                                                            <i class="fas {{ $item->approved_by_qc ? 'fa-check-circle' : 'fa-check' }}"></i> Disetujui oleh QC
                                                                        </button>
                                                                    @elseif($userRole == 4)
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
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5">Tidak ada data.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="card-footer d-flex justify-content-center">
                                {{ $rowsPaginator->links('pagination.simple') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
  (function() {
    const searchInput = document.getElementById('searchInput');
    const searchBtn = document.getElementById('searchBtn');
    const clearBtn = document.getElementById('clearBtn');
    const perPageSelect = document.getElementById('perPageSelect');

    if (!searchInput || !searchBtn || !clearBtn || !perPageSelect) return;

    function setClearVisibility() {
      clearBtn.style.display = searchInput.value.trim() ? 'inline-block' : 'none';
    }

    function buildUrl(params) {
      const url = new URL(window.location.href);
      Object.keys(params).forEach((k) => {
        const v = params[k];
        if (v === null || v === undefined || v === '') {
          url.searchParams.delete(k);
        } else {
          url.searchParams.set(k, v);
        }
      });
      url.searchParams.delete('page');
      return url.toString();
    }

    function applySearch() {
      const search = searchInput.value.trim();
      const perPage = perPageSelect.value;
      window.location.href = buildUrl({ search, perPage });
    }

    searchBtn.addEventListener('click', applySearch);
    searchInput.addEventListener('keydown', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        applySearch();
      }
    });
    perPageSelect.addEventListener('change', function() {
      applySearch();
    });
    clearBtn.addEventListener('click', function() {
      searchInput.value = '';
      applySearch();
    });

    setClearVisibility();
    searchInput.addEventListener('input', setClearVisibility);
  })();
</script>

<script>
$(document).ready(function() {
    $('.approve-btn').click(function() {
        const id = $(this).data('id');
        const type = $(this).data('type');
        const button = $(this);

        const typeNames = {
            'qc': 'QC',
            'produksi': 'Produksi',
            'spv': 'SPV'
        };

        if (confirm(`Apakah Anda yakin ingin menyetujui data ini sebagai ${typeNames[type]}?`)) {
            button.prop('disabled', true);
            button.html('<i class="fas fa-spinner fa-spin"></i> Processing...');

            $.ajax({
                url: '{{ route("verif-cip.approve", ":id") }}'.replace(':id', id),
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    type: type
                },
                success: function(response) {
                    if (response.success) {
                        setTimeout(function() {
                            location.reload();
                        }, 500);
                    } else {
                        alert(response.message || 'Gagal menyetujui data');
                        location.reload();
                    }
                },
                error: function(xhr) {
                    let message = 'Terjadi kesalahan saat menyetujui data';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    alert(message);
                    location.reload();
                }
            });
        }
    });

    $('[data-bulk-export]').click(function() {
        const button = $(this);
        const form = $('#bulkExportForm');
        const kodeForm = $('#bulkKodeForm').val();

        if (!kodeForm.trim()) {
            alert('Kode Form harus diisi!');
            return;
        }

        button.prop('disabled', true);
        form.trigger('submit');

        setTimeout(function() {
            button.prop('disabled', false);
        }, 1500);
    });
});
</script>
@endpush

<!-- Modal Export PDF -->
<div class="modal fade" id="exportPdfModal" tabindex="-1" role="dialog" aria-labelledby="exportPdfModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportPdfModalLabel">Cetak PDF Verif CIP Berdasarkan Filter</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="bulkExportForm" action="{{ route('verif-cip.bulk-export-pdf') }}" method="POST" data-bulk-form="true" target="_blank">
                    @csrf
                    <div class="form-group">
                        <label for="filterTanggal">Tanggal</label>
                        <input type="date" class="form-control" id="filterTanggal" name="tanggal">
                    </div>
                    <div class="form-group">
                        <label for="bulkKodeForm">Kode Form</label>
                        <input type="text" class="form-control" id="bulkKodeForm" name="kode_form" value="QF 27/00" placeholder="Kode form" readonly>
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

