@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Total Pemakaian Emulsi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item active">Total Pemakaian Emulsi</li>
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
                            <h3 class="card-title">Data Total Pemakaian Emulsi</h3>
                            <a href="{{ route('total-pemakaian-emulsi.create') }}" class="btn btn-sm btn-primary float-right"><i class="fas fa-plus"></i> Tambah Data</a>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            <table id="myTable" class="table text-center table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Total Pemakaian</th>
                                        <th>Nama Emulsi</th>
                                        <th>Plan</th>
                                        <th>Nama Produk</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->total_pemakaian }}</td>
                                        <td>{{ $item->emulsi->nama_emulsi ?? '-' }}</td>
                                        <td>{{ $item->plan->nama_plan ?? '-' }}</td>
                                        <td>{{ $item->produk->nama_produk ?? '-' }}</td>
                                        <td>
                                            <a href="{{ route('total-pemakaian-emulsi.edit', $item->uuid) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                            @if($item->nomorEmulsi()->exists())
                                                <button class="btn btn-sm btn-danger" disabled title="Ada {{ $item->nomorEmulsi()->count() }} data Nomor Emulsi terkait">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @else
                                                <form action="{{ route('total-pemakaian-emulsi.destroy', $item->uuid) }}" method="POST" style="display:inline;">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus data?')"><i class="fas fa-trash"></i></button>
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