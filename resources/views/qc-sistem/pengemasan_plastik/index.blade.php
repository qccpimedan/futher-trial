{{-- filepath: resources/views/qc-sistem/persiapan_cold_mixing/index.blade.php --}}
@extends('layouts.app')
@php
    $isLocalhost = in_array(request()->getHost(), ['localhost', '127.0.0.1', '10.68.1.37']);
    $assetPath = $isLocalhost ? 'public/' : '';
@endphp
@section('container')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-shopping-basket text-info"></i>
                        Pengemasan Plastik
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                        <li class="breadcrumb-item active">Pengemasan Plastik</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            
            <!-- Alert Success -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            <!-- Data Table Card -->
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-table"></i>
                        Data Pengemasan Plastik
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="maximize">
                            <i class="fas fa-expand"></i>
                        </button>
                        <!--
                                    @if(auth()->user()->hasPermissionTo('create-pengemasan-plastik')) <a href="{{ route('pengemasan-plastik.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i>
                            Tambah Data
                        </a> @endif
-->
                    </div>
                </div>
                <div class="card-body">
                    <!-- Form Pencarian Server-Side -->
                    <div class="row mb-3 mt-3">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('pengemasan-plastik.index') }}">
                                <div class="input-group input-group-sm" style="width: 300px;">
                                    <input type="text" class="form-control" name="search" placeholder="Cari nama produk" value="{{ $search ?? '' }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        @if(!empty($search))
                                            <a class="btn btn-outline-danger" href="{{ route('pengemasan-plastik.index') }}">
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
                                <form method="GET" action="{{ route('pengemasan-plastik.index') }}">
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
                                    Data Pengemasan Plastik
                                @endif
                            </small>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table  style="white-space: nowrap;" class="table table-bordered table-striped table-hover" id="coldMixingTable">
                            <thead class="thead-light">
                                <tr class="text-white text-center">
                                    <th rowspan="2" class="align-middle" style="width: 50px;">No</th>
                                    <th rowspan="2" class="align-middle">
                                        Shift
                                    </th>
                                    <!-- <th rowspan="2" class="align-middle">
                                        Plan
                                    </th> -->
                                    <th rowspan="2" class="align-middle">
                                        Tanggal
                                    </th>
                                       <th rowspan="2" class="align-middle">
                                        Jam
                                    </th>
                                    <th rowspan="2" class="align-middle">
                                        Produk
                                    </th>
                                    <th rowspan="2" class="align-middle">
                                       Proses Penimbangan
                                    </th>
                                    <th rowspan="2" class="align-middle">
                                      Proses Sealing
                                    </th>
                                    <th rowspan="2" class="align-middle">
                                       Identitas Produk pada Plastik (tinta)
                                    </th>
                                    <th rowspan="2" class="align-middle">
                                       Nomor MD
                                    </th>
                                    <th rowspan="2" class="align-middle">
                                       Kode Kemasan Plastik
                                    </th>
                                    <th rowspan="2" class="align-middle">
                                       Kekuatan Seal
                                    </th>
                                   
                                  
                                    <th rowspan="2" class="align-middle" style="width: 120px;">
                                        Aksi
                                    </th>
                                </tr>
                             
                            </thead>
                            <tbody>
                                @forelse($data as $i => $item)
                                <tr>
                                    <td class="text-center">
                                        <span>{{ $data->firstItem() + $i }}</span>
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
                                    <!-- <td class="text-center">
                                        <span>
                                            {{ $item->plan->nama_plan ?? '-' }}
                                        </span>
                                    </td> -->
                                    <td>
                                        @if(auth()->user()->id_role == 2 || auth()->user()->id_role == 3)
                                            <span class="badge badge-secondary">{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</span>
                                        @else
                                            <span class="badge badge-secondary">{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y H:i:s') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-info">{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                          
                                            <span>{{ $item->pengemasanProduk->produk->nama_produk ?? '-' }} {{ $item->berat ?? '-' }} gram</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $penimbangan = $item->proses_penimbangan;
                                            if ($penimbangan === 'mhw') {
                                                $penimbangan = 'MHW';
                                            } elseif ($penimbangan === 'manual') {
                                                $penimbangan = 'Manual';
                                            }
                                        @endphp
                                        {{ $penimbangan ?? '-' }}
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $sealing = $item->proses_sealing;
                                            if ($sealing === 'bag-sealer') {
                                                $sealing = 'Bag Sealer';
                                            } elseif ($sealing === 'manual') {
                                                $sealing = 'Manual';
                                            }
                                        @endphp
                                        {{ $sealing ?? '-' }}
                                    </td>
                                   
                                    <td class="text-center">
                                        {{ ($item->identitas_produk ?? null) !== null && ($item->identitas_produk ?? '') !== '' ? trim(str_replace([
                                            "\u{00E2}\u{0153}\u{201D}",
                                            "\u{00E2}\u{0153}\u{201C}",
                                            "\u{00E2}\u{0153}\u{201E}",
                                            "\u{00E2}\u{0153}\u{00BB}",
                                            "\u{00E2}\u{0153}\u{00BC}",
                                            "\u{00C2}",
                                            "\u{00C3}",
                                        ], [
                                            "✓",
                                            "✓",
                                            "✓",
                                            "✓",
                                            "✓",
                                            "",
                                            "",
                                        ], str_replace([
                                            "\u{00E2}\u{0153}\u{2014}",
                                            "\u{00E2}\u{0153}\u{02DC}",
                                        ], [
                                            "✘",
                                            "✘",
                                        ], $item->identitas_produk))) : '-' }}
                                    </td>
                                    
                                    <td class="text-center">
                                         @if($item->nomor_md)
                                                <a href="#" data-toggle="modal" data-target="#modalQrCode{{ $item->id }}">
                                                    <img src="{{ asset($assetPath . 'storage/' . $item->nomor_md) }}" alt="Nomor_md" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                                                    <br>
                                                   
                                                </a>
                                                <!-- Modal -->
                                                <div class="modal fade" id="modalQrCode{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="modalQrCodeLabel{{ $item->id }}" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="modalQrCodeLabel{{ $item->id }}">Nomor MD</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body text-center">
                                                                <img src="{{ asset($assetPath . 'storage/' . $item->nomor_md) }}" alt="QR Code" style="max-width:100%;max-height:70vh;">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span>-</span>
                                            @endif
                                    </td>
                                       
                                    <td class="text-center">
                                        {{ $item->kode_kemasan_plastik ?? '-' }}
                                    </td>
                                    <td class="text-center">
                                        {{ ($item->kekuatan_seal ?? null) !== null && ($item->kekuatan_seal ?? '') !== '' ? trim(str_replace([
                                            "\u{00E2}\u{0153}\u{201D}",
                                            "\u{00E2}\u{0153}\u{201C}",
                                            "\u{00E2}\u{0153}\u{201E}",
                                            "\u{00E2}\u{0153}\u{00BB}",
                                            "\u{00E2}\u{0153}\u{00BC}",
                                            "\u{00C2}",
                                            "\u{00C3}",
                                        ], [
                                            "✓",
                                            "✓",
                                            "✓",
                                            "✓",
                                            "✓",
                                            "",
                                            "",
                                        ], str_replace([
                                            "\u{00E2}\u{0153}\u{2014}",
                                            "\u{00E2}\u{0153}\u{02DC}",
                                        ], [
                                            "✘",
                                            "✘",
                                        ], $item->kekuatan_seal))) : '-' }}
                                    </td>
                                    
                                    <td class="text-center">
                                        <x-action-buttons :item="$item" route-prefix="pengemasan-plastik" :show-view="false" />
                                        @if(($item->berat_produk_bag_count ?? 0) > 0)
                                            <button type="button" class="btn btn-secondary btn-sm" title="Berat Produk Pack sudah diinput" disabled>
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @else
                                            <a href="{{ route('berat-produk.create', ['tab' => 'bag', 'id_pengemasan_plastik' => $item->id, 'id_shift' => $item->id_shift]) }}" class="btn btn-success btn-sm" title="Lanjut ke Berat Produk Pack (C1)">
                                                <i class="fas fa-arrow-right"></i>
                                            </a>
                                        @endif
                                        
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="14" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="fas fa-database fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">Belum ada data</h5>
                                            <p class="text-muted">Klik tombol "Tambah Data Pengemasan Plastik" untuk menambah data baru</p>
                                    @if(auth()->user()->hasPermissionTo('create-pengemasan-plastik'))
                                            <a href="{{ route('pengemasan-plastik.create') }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-plus mr-2"></i>Tambah Data Pertama
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

@endsection