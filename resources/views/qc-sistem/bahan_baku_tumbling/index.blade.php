@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Bahan Baku Tumbling</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Bahan Baku Tumbling</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Data Bahan Baku Tumbling</h3>
                            <div class="card-tools">
                                @if(auth()->user()->hasPermissionTo('create-bahan-baku-tumbling'))
                                <a href="{{ route('bahan-baku-tumbling.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Tambah Data
                                </a>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    {{ session('error') }}
                                </div>
                            @endif
                            <div class="table-responsive text-center">
                                <!-- Form Pencarian Server-Side -->
                                <div class="row mb-3 mt-3">
                                    <div class="col-md-4 offset-md-8">
                                        <form action="{{ route('bahan-baku-tumbling.index') }}" method="GET">
                                            <div class="input-group">
                                                <input type="text" name="search" class="form-control" placeholder="Cari Produk atau Kode Produksi" value="{{ request('search') }}">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="submit">
                                                        <i class="fas fa-search"></i> Cari
                                                    </button>
                                                    @if(request('search'))
                                                        <a href="{{ route('bahan-baku-tumbling.index') }}" class="btn btn-outline-danger" title="Clear Search">
                                                            <i class="fas fa-times"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <table style="white-space: nowrap;" class="table table-bordered table-striped">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Shift</th>
                                            <th>Tanggal</th>
                                            <th>Jam</th>
                                            <!-- <th>Plan</th> -->
                                            <th>Nama Produk</th>
                                            <th>Kode Produksi</th>
                                            <th>Status Proses</th>
                                            <!-- <th>Nama Bahan Baku</th>
                                            <th>Kode Produksi Bahan Baku</th>
                                            <th>Jumlah</th>
                                            <th>Suhu °C</th>
                                            <th>Kondisi Daging</th> -->
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($bahanBakuTumbling as $index => $item)
                                            <tr>
                                                <td>{{ $bahanBakuTumbling->firstItem() + $loop->index }}</td>
                                                <!-- <td>{{ $item->plan->nama_plan ?? '-' }}</td> -->
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
                                                        @php
                                                            $userRole = auth()->user()->id_role ?? null;
                                                            $showTime = in_array($userRole, [1, 2, 5]); // superadmin, admin, spv
                                                            $format = $showTime ? 'd-m-Y H:i:s' : 'd-m-Y';
                                                        @endphp
                                                        {{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format($format) : '-' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    {{ isset($item->jam) && $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}
                                                </td>
                                                <td>{{ $item->produk->nama_produk ?? '-' }}</td>
                                                <td>{{ $item->kode_produksi }}</td>
                                                <td>
                                                    @if($item->prosesTumblings->count() > 0)
                                                        <span class="badge badge-success"><i class="fas fa-check-circle"></i> Selesai Tumbling</span>
                                                    @else
                                                        <span class="badge badge-warning"><i class="fas fa-clock"></i> Menunggu Tumbling</span>
                                                    @endif
                                                </td>
                                                <!-- <td>{{ $item->nama_bahan_baku }}</td>
                                                <td>{{ $item->kode_produksi_bahan_baku }}</td>
                                                <td>{{ $item->jumlah }}</td>
                                                <td>{{ $item->suhu }}°C</td>
                                                <td>{{ $item->kondisi_daging }}</td> -->
            
                                                <td>
                                                    <!-- <div class="btn-vertical">
@if(auth()->user()->hasPermissionTo('edit-bahan-baku-tumbling'))
                                                        <a href="{{ route('bahan-baku-tumbling.edit', $item->uuid) }}" class="btn btn-warning btn-sm">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        
                                                        @endif
@if(auth()->user()->hasPermissionTo('view-bahan-baku-tumbling'))
<a href="{{ route('bahan-baku-tumbling.logs', $item->uuid) }}" 
                                                           class="btn btn-info btn-sm" title="Riwayat Perubahan">
                                                            <i class="fas fa-history"></i>
                                                        </a>
                                                        
                                                       
                                                        @endif
@if(auth()->user()->hasPermissionTo('delete-bahan-baku-tumbling'))
<form action="{{ route('bahan-baku-tumbling.destroy', $item->uuid) }}" method="POST" style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
</div> -->
                                                    <x-action-buttons :item="$item" route-prefix="bahan-baku-tumbling"/>
                                                    @if($item->prosesTumblings->count() == 0)
                                                        <a href="{{ route('proses-tumbling.create', ['bahan_baku_uuid' => $item->uuid]) }}" 
                                                            class="btn btn-sm btn-success" title="Lanjut ke Proses Tumbling">
                                                            <i class="fas fa-arrow-right"></i> Tumbling
                                                        </a>
                                                    @else
                                                        <span class="btn btn-sm btn-success disabled" 
                                                                title="Berhasil Input">
                                                            <i class="fas fa-thumbs-up"></i>
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">Tidak ada data</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Menampilkan Navigasi Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $bahanBakuTumbling->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
