@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Input Roasting</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Input Roasting</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Data Input Roasting</h3>
                                    <div class="card-tools d-flex">
                                        <form action="{{ route('input-roasting.index') }}" method="GET" class="mr-2">
                                            <div class="input-group input-group-sm" style="width: 250px;">
                                                <input type="text" name="search" class="form-control" placeholder="Cari Nama Produk atau Tanggal" value="{{ $search ?? '' }}">
                                                <div class="input-group-append">
                                                    <button type="submit" class="btn btn-default">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                    @if($search)
                                                        <a href="{{ route('input-roasting.index') }}" class="btn btn-default">
                                                            <i class="fas fa-times text-danger"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </form>
                                            @if(auth()->user()->hasPermissionTo('create-input-roasting'))
                                        <a href="{{ route('input-roasting.create') }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus"></i> Tambah Data
                                        </a>
                                    @endif
                                    </div>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    {{ session('success') }}
                                </div>
                            @endif
                            <div class="table-responsive text-center">
                                <table style="white-space: nowrap;" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Shift</th>
                                            <th>Tanggal</th>
                                            <th>Jam</th>
                                            <!-- <th>Plan</th> -->
                                            <th>Produk</th>
                                            <th>Kode Produksi</th>
                                            <th>Std Suhu Sebelum Masak</th>
                                            <th>Aktual Suhu Sebelum Masak</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($inputRoasting as $index => $item)
                                            <tr>
                                                <td>{{ $inputRoasting->firstItem() + $index }}</td>
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
                                                    {{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}
                                                </td>
                                                <!-- <td>{{ $item->plan->nama_plan ?? '-' }}</td> -->
                                                <td>{{ $item->produk->nama_produk ?? '-' }} {{ $item->berat_produk ?? '-' }}gram</td>
                                                <td>{{ $item->kode_produksi }}</td>
                                                <td>{{ $item->std_suhu_sebelum ?? '-' }}</td>
                                                <td>{{ $item->aktual_suhu_sesudah ?? '-' }}</td>
                                                <td>
                                                    <!-- <div class="btn-group"> -->
                                                        <!--
@if(auth()->user()->hasPermissionTo('edit-input-roasting')) <a href="{{ route('input-roasting.edit', $item->uuid) }}" class="btn btn-warning btn-sm">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        @endif
@if(auth()->user()->hasPermissionTo('view-input-roasting'))
<a href="{{ route('input-roasting.logs', $item->uuid) }}" class="btn btn-info btn-sm" title="Lihat Log Perubahan">
                                                            <i class="fas fa-history"></i>
                                                        </a>
                                                        @endif
@if(auth()->user()->hasPermissionTo('delete-input-roasting'))
<form action="{{ route('input-roasting.destroy', $item->uuid) }}" method="POST" style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form> @endif
-->
                                                        @if($item->prosesRoastingFanCount == 0)
                                                            <a href="{{ route('proses-roasting-fan.create', ['input_roasting_uuid' => $item->uuid]) }}" 
                                                               class="btn btn-success btn-sm" title="Lanjut ke Proses Roasting Fan">
                                                                <i class="fas fa-arrow-right"></i> Lanjut Proses Roasting Fan
                                                            </a>
                                                        @else
                                                            <span class="btn btn-success btn-sm disabled" 
                                                                  title="Berhasil Input">
                                                                <i class="fas fa-thumbs-up"></i>
                                                            </span>
                                                        @endif
                                                        <x-action-buttons :item="$item" route-prefix="input-roasting" :show-view="false" />
                                                    <!-- </div> -->
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center">Tidak ada data</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                {{ $inputRoasting->appends(['search' => $search ?? ''])->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection