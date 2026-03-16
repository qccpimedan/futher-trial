@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Data Barang</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Data Barang</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="card-title">
                                    <i class="fas fa-box text-primary mr-2"></i>
                                    Data Barang
                                </h3>
                                <a href="{{ route('data-barang.create') }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus"></i> Tambah Data
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if($dataBarang->isEmpty())
                                <div class="alert alert-info text-center">
                                    <i class="fas fa-info-circle"></i>
                                    Belum ada data barang yang tersedia.
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table id="myTable" class="table table-bordered table-striped" style="white-space:nowrap;">
                                        <thead>
                                            <tr>
                                                <th class="text-center">No</th>
                                                <th class="text-center">Plan</th>
                                                <th class="text-center">Area</th>
                                                <th class="text-center">Nama Barang</th>
                                                <th class="text-center">Jumlah</th>
                                                <!-- <th class="text-center">User Input</th> -->
                                                <!-- <th class="text-center">Tanggal Dibuat</th> -->
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($dataBarang as $index => $item)
                                                <tr>
                                                    <td class="text-center">{{ $index + 1 }}</td>
                                                    <td class="text-center">
                                                        {{ $item->plan->nama_plan ?? '-' }}
                                                    </td>
                                                    <td class="text-center" style="text-transform:uppercase">
                                                        {{ $item->area->area ?? '-' }}
                                                    </td>
                                                    <td class="text-center" style="text-transform:uppercase">
                                                        {{ Str::limit($item->nama_barang, 50) }}
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge badge-primary">
                                                            {{ number_format($item->jumlah ?? 0) }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="btn-vertical" role="group">
                                                            <a href="{{ route('data-barang.edit', $item->uuid) }}" 
                                                               class="btn btn-warning btn-sm" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            @if(auth()->user()->role === 'superadmin' || $item->id_plan === auth()->user()->id_plan)
                                                                <form action="{{ route('data-barang.destroy', $item->uuid) }}" 
                                                                      method="POST" style="display: inline-block;" 
                                                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection