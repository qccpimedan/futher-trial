{{-- filepath: resources/views/super-admin/nomor-formula/index.blade.php --}}
@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Data Nomor Formula</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item active">Data Nomor Formula</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <a href="{{ route('nomor-formula.create') }}" class="btn btn-primary btn-sm float-right">
                                <i class="fas fa-plus"></i> Tambah Nomor Formula
                            </a>
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
                            <table id="myTable" class="table text-center table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nomor Formula</th>
                                        <th>Nama Plan</th>
                                        <th>Nama Produk</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($nomor_formula as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->nomor_formula }}</td>
                                        <td>{{ $item->plan->nama_plan ?? '-' }}</td>
                                        <td>{{ $item->produk->nama_produk ?? '-' }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('nomor-formula.edit', $item->uuid) }}" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($item->bahanForming()->exists())
                                                <button class="btn btn-danger btn-sm" disabled title="Ada {{ $item->bahanForming()->count() }} data Bahan Forming terkait">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @else
                                                <form action="{{ route('nomor-formula.destroy', $item->uuid) }}" method="POST" style="display:inline;">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-danger btn-sm" onclick="return confirm('Hapus data?')">
                                                        <i class="fas fa-trash"></i> 
                                                    </button>
                                                </form>
                                            @endif
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