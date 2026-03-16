@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Data Nomor Emulsi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Nomor Emulsi</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Data Nomor Emulsi</h3>
                            <a href="{{ route('nomor-emulsi.create') }}" class="btn btn-sm btn-primary float-right"><i class="fas fa-plus"></i> Tambah Data</a>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            <table id="myTable" class="table text-center table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Jumlah Proses Emulsi</th>
                                        <th>Total Pemakaian</th>
                                        <th>Nama Produk</th>
                                        <th>Nama Emulsi</th>
                                        <!-- <th>Nama Plan</th> -->
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data as $formula)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $formula->nomor_emulsi }} kali</td>
                                        <td>{{ $formula->total_pemakaian->total_pemakaian }}</td>
                                        <td>{{ $formula->produk->nama_produk }}</td>
                                        <td>{{ $formula->emulsi->nama_emulsi }}</td>
                                        <!-- <td>{{ $formula->plan->nama_plan }}</td> -->
                                        <td>
                                            <a href="{{ route('nomor-emulsi.edit', $formula->uuid) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                            <form action="{{ route('nomor-emulsi.destroy', $formula->uuid) }}" method="POST" style="display:inline;">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Yakin hapus data?')"><i class="fas fa-trash"></i></button>
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