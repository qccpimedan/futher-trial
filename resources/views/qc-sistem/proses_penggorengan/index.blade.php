{{-- filepath: resources/views/qc-sistem/penggorengan/index.blade.php --}}
@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-fire text-danger"></i>
                        Penggorengan
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">List Proses Penggorengan</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
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
                        Data Proses Penggorengan
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="maximize">
                            <i class="fas fa-expand"></i>
                        </button>
                        @if(auth()->user()->hasPermissionTo('create-proses-penggorengan'))
                            <a href="{{ route('penggorengan.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i>
                                Tambah Data
                            </a>
                        @endif
                    </div>
                </div>
                    <div class="table-responsive">
                        <!-- Form Pencarian Server-Side -->
                        <div class="row mb-3 mt-3">
                            <div class="col-md-4 offset-md-8">
                                <form action="{{ route('penggorengan.index') }}" method="GET">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control" placeholder="Cari Kode atau Nama Produk" value="{{ request('search') }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="submit">
                                                <i class="fas fa-search"></i> Cari
                                            </button>
                                            @if(request('search'))
                                                <a href="{{ route('penggorengan.index') }}" class="btn btn-outline-danger" title="Clear Search">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <table class="table table-bordered table-striped table-hover" style="white-space: nowrap;">
                            <thead class="thead-light">
                                <tr class="text-white text-center">
                                    <th class="align-middle" style="width: 50px;">No</th>
                                    <th class="align-middle">Shift</th>
                                    <th class="align-middle">Tanggal</th>
                                    <th class="align-middle">Jam</th>
                                    <th class="align-middle">Nama Produk</th>
                                    <th class="align-middle">Kode Produksi</th>
                                    <!-- <th class="align-middle">Berat Produk</th> -->
                                    <th class="align-middle">Waktu Pemasakan</th>
                                    <!-- <th class="align-middle">Waktu Selesai Pemasakan</th> -->
                                    <th class="align-middle">No Of Strokes</th>
                                    <th class="align-middle">Hasil Pencetakan</th>
                                    <th class="align-middle" style="width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $i => $item)
                                <tr>
                                    <td class="text-center">
                                        <span>{{ $data->firstItem() + $loop->index }}</span>
                                    </td>
                                    <td>
                                        @if($item->shift_id == 1)
                                        <span class="badge bg-primary">Shift {{ $item->shift->shift ?? 'Shift 1' }}</span>
                                        @elseif($item->shift_id == 2)
                                        <span class="badge bg-success">Shift {{ $item->shift->shift ?? 'Shift 2' }}</span>
                                        @else
                                        <span class="badge bg-secondary">Shift {{ $item->shift->shift ?? 'Shift ' . $item->shift_id }}</span>
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
                                    <td class="text-center">
                                        <span>
                                            {{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span>{{ $item->produk->nama_produk ?? '-' }} ({{ $item->berat_produk }}gram)</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span>{{ $item->kode_produksi }}</span>
                                    </td>
                                    <!-- <td class="text-center">
                                        <span>{{ $item->berat_produk }}</span>
                                    </td> -->
                                    <td class="text-center">
                                        <span>{{ $item->waktu_pemasakan ?? '-' }} </span>
                                    </td>
                                    <!-- <td class="text-center">
                                        <span>{{ $item->waktu_selesai_pemasakan ?? '-' }}</span>
                                    </td> -->
                                    <td class="text-center">
                                        <span>{{ $item->no_of_strokes }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span>{{ $item->hasil_pencetakan }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-vertical" role="group">
                                            <x-action-buttons :item="$item" route-prefix="penggorengan" permission-prefix="proses-penggorengan" :show-view="false" />
                                            
                                            {{-- Tombol Lanjut ke Pembuatan Predust --}}
                                            @php
                                                $predustCount = $item->pembuatan_predust_count ?? 0;
                                            @endphp
                                            @if($predustCount == 0)
                                                <a href="{{ route('pembuatan-predust.create', ['penggorengan_uuid' => $item->uuid]) }}" 
                                                   class="btn btn-success btn-sm" 
                                                   title="Lanjut ke Pembuatan Predust">
                                                    <i class="fas fa-arrow-right"></i> Predust
                                                </a>
                                            @else
                                                <span class="btn btn-success btn-sm disabled" 
                                                      title="Berhasil Input">
                                                    <i class="fas fa-thumbs-up"></i> Predust
                                                </span>
                                            @endif
                                            
                                            {{-- Tombol Lanjut ke Proses Battering --}}
                                            @php
                                                $batteringCount = $item->proses_battering_count ?? 0;
                                            @endphp
                                            @if($batteringCount == 0)
                                                <a href="{{ route('proses-battering.create', ['penggorengan_uuid' => $item->uuid]) }}" 
                                                   class="btn btn-success btn-sm" 
                                                   title="Lanjut ke Proses Battering">
                                                    <i class="fas fa-arrow-right"></i> Battering
                                                </a>
                                            @else
                                                <span class="btn btn-success btn-sm disabled" 
                                                      title="Berhasil Input">
                                                    <i class="fas fa-thumbs-up"></i> Battering
                                                </span>
                                            @endif
                                            
                                            {{-- Tombol Lihat Log --}}
                                            <!--
@if(auth()->user()->hasPermissionTo('edit-proses-penggorengan')) <a href="{{ route('penggorengan.edit', $item->uuid) }}" 
                                               class="btn btn-warning btn-sm" 
                                               data-toggle="tooltip" 
                                               title="Edit Data">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endif
@if(auth()->user()->hasPermissionTo('view-proses-penggorengan'))
<a href="{{ route('penggorengan.logs', $item->uuid) }}" 
                                               class="btn btn-info btn-sm" 
                                               title="Lihat Log Perubahan">
                                                <i class="fas fa-history"></i>
                                            </a>
                                            
                                            @endif
@if(auth()->user()->hasPermissionTo('delete-proses-penggorengan'))
<form action="{{ route('penggorengan.destroy', $item->uuid) }}" 
                                                  method="POST" 
                                                  class="d-inline" 
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
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
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="fas fa-database fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">Belum ada data</h5>
                                            <p class="text-muted">Klik tombol "Tambah Data" untuk menambah data baru</p>
                                    @if(auth()->user()->hasPermissionTo('create-proses-penggorengan'))
                                            <a href="{{ route('penggorengan.create') }}" class="btn btn-success">
                                                <i class="fas fa-plus mr-2"></i>Tambah Data
                                            </a>
                                        @endif
</div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <!-- Menampilkan Navigasi Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $data->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
