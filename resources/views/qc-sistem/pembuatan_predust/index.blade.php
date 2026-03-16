@extends('layouts.app')

@section('title', 'Pembuatan Predust')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Pembuatan Predust</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            <li class="breadcrumb-item active">Pembuatan Predust</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list mr-2"></i>Data Pembuatan Predust
                        </h3>
                        <div class="card-tools">
                            <!--
                                    @if(auth()->user()->hasPermissionTo('create-pembuatan-predust')) <a href="{{ route('pembuatan-predust.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Tambah Data
                            </a> @endif
-->
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

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <div class="row mb-3 mt-3">
                            <div class="col-md-6">
                                <form method="GET" action="{{ route('pembuatan-predust.index') }}">
                                    <div class="input-group input-group-sm" style="width: 300px;">
                                        <input type="text" class="form-control" name="search" placeholder="Cari nama produk" value="{{ $search ?? '' }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="submit">
                                                <i class="fas fa-search"></i>
                                            </button>
                                            @if(!empty($search))
                                                <a class="btn btn-outline-danger" href="{{ route('pembuatan-predust.index') }}">
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
                                    <form method="GET" action="{{ route('pembuatan-predust.index') }}">
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
                                        Data Pembuatan Predust
                                    @endif
                                </small>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table  class="table text-center table-bordered table-striped" style="white-space:nowrap;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Shift</th>
                                        <th>Tanggal</th>
                                        <th>Jam</th>
                                        <th>Produk</th>
                                        <th>Kode Produksi</th>
                                        <th>Jenis Predust</th>
                                        <th>Kondisi Predust</th>
                                        <th>Hasil Pencetakan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pembuatanPredust as $index => $item)
                                        <tr>
                                            <td>{{ $pembuatanPredust->firstItem() + $index }}</td>
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
                                                <span>
                                                    {{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}
                                                </span>
                                            </td>
                                            <td>{{ $item->produk->nama_produk ?? '-' }} 
                                                @if($item->penggorengan && $item->penggorengan->berat_produk)
                                                    ({{ $item->penggorengan->berat_produk }} gram)
                                                @endif
                                            </td>
                                            <td>{{ $item->kode_produksi }}</td>                                            
                                            <td>{{ $item->jenisPredust->jenis_predust ?? '-' }}</td>
                                            <td>{{ $item->kondisi_predust }}</td>
                                            <td>
                                                @if($item->hasil_pencetakan == 'oke')
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check"></i> OK
                                                    </span>
                                                @else
                                                    <span class="badge badge-danger">
                                                        <i class="fas fa-times"></i> Tidak OK
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-vertical" role="group">
                                                    <x-action-buttons :item="$item" route-prefix="pembuatan-predust" :show-view="false" />
                                                    {{-- Tombol Lanjut ke Proses Battering --}}
                                                    @php
                                                        $batteringCount = \App\Models\ProsesBattering::where('predust_uuid', $item->uuid)->count();
                                                    @endphp
                                                    @if($batteringCount == 0)
                                                        <a href="{{ route('proses-battering.create', ['predust_uuid' => $item->uuid]) }}" 
                                                           class="btn btn-success btn-sm" 
                                                           title="Lanjut ke Proses Battering">
                                                            <i class="fas fa-arrow-right"></i> Battering
                                                        </a>
                                                    @else
                                                        <span class="btn btn-success btn-sm disabled" 
                                                              title="Berhasil Input">
                                                            <i class="fas fa-thumbs-up"></i>
                                                        </span>
                                                    @endif
                                                    
                                                    <!-- {{-- Tombol Lihat Log --}}
@if(auth()->user()->hasPermissionTo('edit-pembuatan-predust'))
                                                    <a href="{{ route('pembuatan-predust.edit', $item->uuid) }}" 
                                                       class="btn btn-warning btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @endif
@if(auth()->user()->hasPermissionTo('view-pembuatan-predust'))
<a href="{{ route('pembuatan-predust.logs', $item->uuid) }}" 
                                                       class="btn btn-info btn-sm" 
                                                       title="Lihat Log Perubahan">
                                                        <i class="fas fa-history"></i>
                                                    </a>
                                                    
                                                    @endif
@if(in_array(auth()->user()->role, ['superadmin', 'admin']))
@if(auth()->user()->hasPermissionTo('delete-pembuatan-predust'))
                                                        <form action="{{ route('pembuatan-predust.destroy', $item->uuid) }}" 
                                                              method="POST" style="display: inline-block;"
                                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                   @endif
 @endif -->
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">Tidak ada data pembuatan predust</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            {{ $pembuatanPredust->appends(['search' => $search ?? '', 'per_page' => $perPage ?? ''])->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection