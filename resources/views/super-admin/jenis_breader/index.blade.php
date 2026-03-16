{{-- filepath: resources/views/super-admin/jenis_breader/index.blade.php --}}
@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Data Jenis Breader</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item active">Data Jenis Breader</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Data Jenis Breader</h3>
                            <div class="card-tools">
                                <a href="{{ route('jenis-breader.create') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Tambah Data</a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            <table id="myTable" class="table text-center table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Produk</th>
                                        <th>Jenis Breader</th>
                                        <th>Plan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->produk->nama_produk ?? '-' }}</td>
                                        <td><span style="text-transform:uppercase;">{{ $item->jenis_breader }}</span></td>
                                        <td>{{ $item->plan->nama_plan ?? '-' }}</td>
                                        <td>
                                            <a href="{{ route('jenis-breader.edit', $item->uuid) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                            <form action="{{ route('jenis-breader.destroy', $item->uuid) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data ini?')"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection