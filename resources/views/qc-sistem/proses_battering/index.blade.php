@extends('layouts.app')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h1 class="m-0">Proses Battering</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Home</a></li>
                            <li class="breadcrumb-item active">Proses Battering</li>
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
                                <h3 class="card-title">Data Proses Battering</h3>
                                <div class="card-tools">
                                    <!--
                                    @if(auth()->user()->hasPermissionTo('create-proses-battering')) <a href="{{ route('proses-battering.create') }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus mr-1"></i> Tambah Data
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
                                
                                @if($data->count() > 0 || request('search'))
                                <div class="table-responsive">
                                    <!-- Form Pencarian Server-Side -->
                                    <div class="row mb-3 mt-3">
                                        <div class="col-md-4 offset-md-8">
                                            <form action="{{ route('proses-battering.index') }}" method="GET">
                                                <div class="input-group">
                                                    <input type="text" name="search" class="form-control" placeholder="Cari Nama Produk" value="{{ request('search') }}">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary" type="submit">
                                                            <i class="fas fa-search"></i> Cari
                                                        </button>
                                                        @if(request('search'))
                                                            <a href="{{ route('proses-battering.index') }}" class="btn btn-outline-danger" title="Clear Search">
                                                                <i class="fas fa-times"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    <table style="white-space: nowrap;" class="table table-bordered text-center table-hover table-striped">
                                        <thead class="">
                                            <tr>
                                                <th>#</th>
                                                <th>Shift</th>
                                                <th>Tanggal</th>
                                                <th>Jam</th>
                                                <th>Produk</th>
                                                <th>Kode Produksi</th>
                                                <th>Jenis Better</th>
                                                <th>Hasil Better</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($data as $index => $row)
                                            <tr>
                                                <td>{{ $data->firstItem() + $index }}</td>
                                                <td>
                                                    @if($row->penggorengan && $row->penggorengan->shift && ($row->penggorengan->shift->shift == 1))
                                                        <span class="badge bg-primary">Shift 1</span>
                                                    @elseif($row->penggorengan && $row->penggorengan->shift && ($row->penggorengan->shift->shift == 2))
                                                        <span class="badge bg-success">Shift 2</span>
                                                    @elseif($row->penggorengan && $row->penggorengan->shift && ($row->penggorengan->shift->shift == 3))
                                                        <span class="badge bg-secondary">Shift 3</span>
                                                    @elseif($row->penggorengan && $row->penggorengan->shift)
                                                        <span class="badge bg-info">{{ $row->penggorengan->shift->shift }}</span>
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
                                                        {{ $row->tanggal ? \Carbon\Carbon::parse($row->tanggal)->format($format) : '-' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span>
                                                        {{ $row->jam ? \Carbon\Carbon::parse($row->jam)->format('H:i') : '-' }}
                                                    </span>
                                                </td>
                                                <td>{{ $row->produk->nama_produk ?? '-' }}
                                                    @if($row->penggorengan && $row->penggorengan->berat_produk)
                                                        ({{ $row->penggorengan->berat_produk }}gram)
                                                    @endif
                                                </td>
                                                <td>{{ $row->kode_produksi_better ?? '-' }}</td>
                                                <td>{{ $row->jenis_better->nama_better ?? '-' }}</td>
                                                <td>{{ $row->hasil_better ?? '-' }}</td>
                                                <td>
                                                    <x-action-buttons :item="$row" route-prefix="proses-battering" :show-view="false" />
                                                    {{-- Tombol Lanjut ke Proses Breader --}}
                                                    @php
                                                        $breaderCount = \App\Models\ProsesBreader::where('battering_uuid', $row->uuid)->count();
                                                    @endphp
                                                    @if($breaderCount == 0)
                                                        <a href="{{ route('proses-breader.create', ['battering_uuid' => $row->uuid]) }}" 
                                                           class="btn btn-success btn-sm" 
                                                           title="Lanjut ke Proses Breader">
                                                            <i class="fas fa-arrow-right"></i> Breader
                                                        </a>
                                                    @else
                                                        <span class="btn btn-success btn-sm disabled" 
                                                              title="Berhasil Input">
                                                            <i class="fas fa-thumbs-up"></i>
                                                        </span>
                                                        @endif
                                                    <!--
@if(auth()->user()->hasPermissionTo('edit-proses-battering')) <a href="{{ route('proses-battering.edit', $row->uuid) }}" 
                                                        class="btn btn-sm btn-warning" 
                                                        title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @endif
@if(auth()->user()->hasPermissionTo('view-proses-battering'))
<a href="{{ route('proses-battering.logs', $row->uuid) }}" 
                                                        class="btn btn-sm btn-info" 
                                                        title="Riwayat Perubahan">
                                                        <i class="fas fa-history"></i>
                                                    </a>
                                                    @endif
@if(auth()->user()->hasPermissionTo('delete-proses-battering'))
<form action="{{ route('proses-battering.destroy', $row->uuid) }}" 
                                                            method="POST" 
                                                            class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-danger" 
                                                                title="Hapus"
                                                                onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form> @endif
-->
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <!-- Menampilkan Navigasi Pagination -->
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $data->appends(request()->query())->links('pagination::bootstrap-4') }}
                                </div>
                                @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Tidak ada data yang tersedia.
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
@endsection