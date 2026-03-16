@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Data Bahan Emulsi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item active">Bahan Emulsi</li>
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
                            <h3 class="card-title">Data Bahan Emulsi</h3>
                            <a href="{{ route('bahan-emulsi.create') }}" class="btn btn-primary btn-sm float-right"><i class="fas fa-plus"></i> Tambah Data</a>
                        </div>
                        <div class="card-body table-responsive">
                            @if(session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            <table id="bahan-emulsi" class="text-center table table-bordered table-striped" style="white-space:nowrap;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Plan</th>
                                        <th>Produk</th>
                                        <th>Nomor Proses Emulsi</th>
                                        <th>Total Pemakaian</th>
                                        <th>Nama Emulsi</th>
                                        <th>Nama RM</th>
                                        <th>Berat RM (kg)</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->plan->nama_plan ?? '-' }}</td>
                                        <td>{{ $item->produk->nama_produk ?? '-' }}</td>
                                        <td>{{ $item->nomor_emulsi->nomor_emulsi ?? '-' }}</td>
                                        <td>{{ $item->total_pemakaian->total_pemakaian ?? '-' }}</td>
                                        <td>{{ $item->emulsi->nama_emulsi ?? '-' }}</td>
                                        <td><span style="text-transform:uppercase;">{{ $item->nama_rm }}</span></td>
                                        <td>{{ $item->berat_rm }} kg</td>
                                        <td class="text-center">
                                            <a href="{{ route('bahan-emulsi.edit', $item->uuid) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                            <form action="{{ route('bahan-emulsi.destroy', $item->uuid) }}" method="POST" style="display:inline;">
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