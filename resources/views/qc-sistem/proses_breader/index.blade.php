{{-- filepath: resources/views/qc-sistem/proses_breader/index.blade.php --}}
@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-cookie-bite text-warning"></i>
                        Proses Breadering
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                        <li class="breadcrumb-item active">Proses Breadering</li>
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

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                {{ session('error') }}
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
                        Data Proses Breader
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="maximize">
                            <i class="fas fa-expand"></i>
                        </button>
                        <!--
                                    @if(auth()->user()->hasPermissionTo('create-proses-breader')) <a href="{{ route('proses-breader.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i>
                            Tambah Data
                        </a> @endif
-->
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <!-- Form Pencarian Server-Side -->
                        <div class="row mb-3 mt-3">
                            <div class="col-md-4 offset-md-8">
                                <form action="{{ route('proses-breader.index') }}" method="GET">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control" placeholder="Cari Nama Produk" value="{{ request('search') }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="submit">
                                                <i class="fas fa-search"></i> Cari
                                            </button>
                                            @if(request('search'))
                                                <a href="{{ route('proses-breader.index') }}" class="btn btn-outline-danger" title="Clear Search">
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
                                <tr class="text-white text-center">
                                    <th class="align-middle">No</th>
                                    <th class="align-middle">Shift</th>
                                    <th class="align-middle">Tanggal</th>
                                    <th class="align-middle">Jam</th>
                                    <th class="align-middle">Nama Produk</th>
                                    <th class="align-middle">Jenis Breader</th>
                                    <th class="align-middle">Kode Produksi</th>
                                    <th class="align-middle">Hasil Breader</th>
                                    <!-- <th class="align-middle">Plan</th> -->
                                    <th class="align-middle">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $i => $item)
                                <tr>
                                    <td class="text-center">
                                        <span>{{ $data->firstItem() + $i }}</span>
                                    </td>
                                    <td>
                                        @if($item->penggorengan && $item->penggorengan->shift && $item->penggorengan->shift->shift == 1)
                                            <span class="badge bg-primary">Shift 1</span>
                                        @elseif($item->penggorengan && $item->penggorengan->shift && $item->penggorengan->shift->shift == 2)
                                            <span class="badge bg-success">Shift 2</span>
                                        @elseif($item->penggorengan && $item->penggorengan->shift && $item->penggorengan->shift->shift == 3)
                                            <span class="badge bg-secondary">Shift 3</span>
                                        @else
                                            <span class="badge bg-info">{{ $item->penggorengan->shift->shift ?? '-' }}</span>
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
                                        <div class="d-flex align-items-center">
                                            <span>{{ $item->produk->nama_produk ?? '-' }}
                                                @if($item->penggorengan && $item->penggorengan->berat_produk)
                                                    ({{ $item->penggorengan->berat_produk }}gram)
                                                @endif
                                            </span>
                                        </div>
                                    </td>
                                  <td class="text-center">
                                        @if($item->getAllJenisBreader()->count() > 0)
                                            @foreach($item->getAllJenisBreader() as $breader)
                                                <span class="badge badge-info mr-1">{{ $breader->jenis_breader }}</span>
                                            @endforeach
                                        @else
                                            <span>-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span>
                                            {{ $item->kode_produksi ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span>{{ $item->hasil_breader ?? '-' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <x-action-buttons :item="$item" route-prefix="proses-breader" :show-view="false" />
                                        {{-- Tombol Lanjut ke Proses Frayer --}}
                                        @php
                                            $frayerCount = \App\Models\ProsesFrayer::where('breader_uuid', $item->uuid)->count();
                                            
                                            // Untuk Frayer2, cari berdasarkan relasi dengan ProsesFrayer
                                            $frayer2Count = \App\Models\Frayer2::whereHas('frayerData', function($query) use ($item) {
                                                $query->where('breader_uuid', $item->uuid);
                                            })->count();
                                            
                                            $frayerCount += $frayer2Count +
                                                          \App\Models\Frayer3::where('breader_uuid', $item->uuid)->count() +
                                                          \App\Models\Frayer4::where('breader_uuid', $item->uuid)->count() +
                                                          \App\Models\Frayer5::where('breader_uuid', $item->uuid)->count();
                                        @endphp
                                        @if($frayerCount == 0)
                                            <button type="button" 
                                                    class="btn btn-success btn-sm frayer-btn" 
                                                    data-toggle="modal" 
                                                    data-target="#lineSelectionModal"
                                                    data-breader-uuid="{{ $item->uuid }}"
                                                    data-produk-id="{{ $item->id_produk }}"
                                                    data-plan-id="{{ $item->id_plan }}"
                                                    data-user-id="{{ $item->user_id }}"
                                                    title="Lanjut ke Frayer">
                                                <i class="fas fa-arrow-right"></i> Fryer
                                            </button>
                                        @else
                                            <span class="btn btn-success btn-sm disabled" 
                                                  title="Berhasil Input">
                                                <i class="fas fa-thumbs-up"></i>
                                            </span>
                                        @endif
                                        <!--
@if(auth()->user()->hasPermissionTo('edit-proses-breader')) <a href="{{ route('proses-breader.edit', $item->uuid) }}" 
                                           class="btn btn-warning btn-sm" 
                                           data-toggle="tooltip" 
                                           title="Edit Data">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endif
@if(auth()->user()->hasPermissionTo('view-proses-breader'))
<a href="{{ route('proses-breader.logs', $item->uuid) }}" 
                                           class="btn btn-info btn-sm" 
                                           data-toggle="tooltip" 
                                           title="Riwayat Perubahan">
                                            <i class="fas fa-history"></i>
                                        </a>
                                        @endif
@if(auth()->user()->hasPermissionTo('delete-proses-breader'))
<form action="{{ route('proses-breader.destroy', $item->uuid) }}" 
                                              method="POST" 
                                              style="display:inline;" 
                                              onsubmit="return confirmDelete()">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-danger btn-sm" 
                                                    data-toggle="tooltip" 
                                                    title="Hapus Data">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form> @endif
-->
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="fas fa-cookie-bite fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">Belum ada data</h5>
                                            <p class="text-muted">Klik tombol "Tambah Data" untuk menambah data proses breader baru</p>
                                            @if(auth()->user()->hasPermissionTo('create-proses-breader'))
                                                <a href="{{ route('proses-breader.create') }}" class="btn btn-success">
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

                    <!-- Menampilkan Navigasi Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $data->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Pemilihan Line -->
<div class="modal fade" id="lineSelectionModal" tabindex="-1" role="dialog" aria-labelledby="lineSelectionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title text-white" id="lineSelectionModalLabel">
                    <i class="fas fa-route mr-2"></i>Pilih Line Proses Fryer
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">Pilih line produksi untuk melanjutkan proses ke tahap Fryer:</p>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <button type="button" class="btn btn-outline-primary btn-block line-btn" data-line="1" onclick="selectLine(1)">
                            <i class="fas fa-industry mr-2"></i>Line 1
                        </button>
                    </div>
                    <div class="col-md-6 mb-2">
                        <button type="button" class="btn btn-outline-primary btn-block line-btn" data-line="2" onclick="selectLine(2)">
                            <i class="fas fa-industry mr-2"></i>Line 2
                        </button>
                    </div>
                    <div class="col-md-6 mb-2">
                        <button type="button" class="btn btn-outline-primary btn-block line-btn" data-line="3" onclick="selectLine(3)">
                            <i class="fas fa-industry mr-2"></i>Line 3
                        </button>
                    </div>
                    <div class="col-md-6 mb-2">
                        <button type="button" class="btn btn-outline-primary btn-block line-btn" data-line="4" onclick="selectLine(4)">
                            <i class="fas fa-industry mr-2"></i>Line 4
                        </button>
                    </div>
                    <!-- <div class="col-md-12 mb-2">
                        <button type="button" class="btn btn-outline-primary btn-block line-btn" data-line="5" onclick="selectLine(5)">
                            <i class="fas fa-industry mr-2"></i>Line 5
                        </button>
                    </div> -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-2"></i>Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>

</script>
@endsection