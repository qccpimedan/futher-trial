@extends('layouts.app')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h1 class="m-0">Data Proses Marinade</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                            <li class="breadcrumb-item active">Proses Marinade</li>
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
                                    <i class="fas fa-flask"></i> Daftar Proses Marinade
                                </h3>
                                <!-- <div class="card-tools">
                                    @if(auth()->user()->hasPermissionTo('create-proses-marinade'))
                                    <a href="{{ route('proses-marinade.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Tambah Data
                                    </a>
                                @endif
</div> -->
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

                                <div class="table-responsive text-center">
                                    <!-- Form Pencarian Server-Side -->
                                    <div class="row mb-3 mt-3">
                                        <div class="col-md-4 offset-md-8">
                                            <form action="{{ route('proses-marinade.index') }}" method="GET">
                                                <div class="input-group">
                                                    <input type="text" name="search" class="form-control" placeholder="Cari Kode Produksi" value="{{ request('search') }}">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary" type="submit">
                                                            <i class="fas fa-search"></i> Cari
                                                        </button>
                                                        @if(request('search'))
                                                            <a href="{{ route('proses-marinade.index') }}" class="btn btn-outline-danger" title="Clear Search">
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
                                                <th>Jenis Marinade</th>
                                                <th>Kode Produksi</th>
                                                <th>Jumlah</th>
                                                <th>Hasil Pencampuran</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($prosesMarinades as $index => $item)
                                                <tr>
                                                    <td>{{ $prosesMarinades->firstItem() + $loop->index }}</td>
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
                                                            {{ $item->tanggal->format('d-m-Y H:i:s') }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        {{ $item->jenisMarinade->jenis_marinade ?? '-' }}
                                                    </td>
                                                    <td>{{ $item->kode_produksi }}</td>
                                                    <td>{{ number_format($item->jumlah, 0) }} gram</td>
                                                    <td>{{ $item->hasil_pencampuran }}</td> 
                                                    <td>
                                                        <!-- <div class="btn-vertical" role="group">
@if(auth()->user()->hasPermissionTo('edit-proses-marinade'))
                                                            <a href="{{ route('proses-marinade.edit', $item->uuid) }}" 
                                                               class="btn btn-warning btn-sm" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            
                                                            @endif
@if(auth()->user()->hasPermissionTo('view-proses-marinade'))
<a href="{{ route('proses-marinade.logs', $item->uuid) }}" 
                                                               class="btn btn-info btn-sm" title="Riwayat Perubahan">
                                                                <i class="fas fa-history"></i>
                                                            </a>
                                                            
                                                            
                                                            @endif
@if(auth()->user()->hasPermissionTo('delete-proses-marinade'))
<form action="{{ route('proses-marinade.destroy', $item->uuid) }}" 
                                                                method="POST" style="display: inline-block;"
                                                                onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        @endif
</div> -->
                                                        <x-action-buttons :item="$item" route-prefix="proses-marinade" :show-view="false" />
                                                        @if($item->bahan_baku_tumbling_uuid)
                                                            @if($item->prosesTumblings->count() == 0)
                                                                <a href="{{ route('proses-tumbling.create', ['bahan_baku_uuid' => $item->bahan_baku_tumbling_uuid, 'marinade_uuid' => $item->uuid]) }}" 
                                                                class="btn btn-success btn-sm" title="Lanjut ke Proses Tumbling">
                                                                    <i class="fas fa-arrow-right"></i> Tumbling
                                                                </a>
                                                            @else
                                                                <span class="btn btn-success btn-sm disabled" 
                                                                    title="Berhasil Input">
                                                                    <i class="fas fa-thumbs-up"></i>
                                                                </span>
                                                            @endif
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center">
                                                        <i class="fas fa-info-circle"></i> Tidak ada data proses marinade
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Menampilkan Navigasi Pagination -->
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $prosesMarinades->appends(request()->query())->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection