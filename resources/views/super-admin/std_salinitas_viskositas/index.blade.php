{{-- filepath: resources/views/super-admin/std_salinitas_viskositas/index.blade.php --}}
@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Data Std Salinitas & Viskositas</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item active">Std Salinitas & Viskositas</li>
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
                            <h3 class="card-title">Data Std Salinitas & Viskositas</h3>
                            <div class="card-tools">
                                <a href="{{ route('std-salinitas-viskositas.create') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Tambah Data</a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            <table id="myTable" class="table table-responsive text-center table-bordered table-striped" style="white-space: nowrap;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Plan</th>
                                        <th>Produk</th>
                                        <th>Better</th>
                                        <th>Std Viskositas (Detik)</th>
                                        <th>Std Salinitas (%)</th>
                                        <th>Std Suhu Air (°C)</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data as $i => $item)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $item->plan->nama_plan ?? '-' }}</td>
                                        <td>{{ $item->produk->nama_produk ?? '-' }}</td>
                                        <td>{{ $item->better->nama_better ?? '-' }}</td>
                                        <td>{{ $item->std_viskositas }}</td>
                                        <td>{{ $item->std_salinitas }}</td>
                                        <td>{{ $item->std_suhu_akhir }}</td>
                                        <td>
                                            <a href="{{ route('std-salinitas-viskositas.edit', $item->uuid) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                            <form action="{{ route('std-salinitas-viskositas.destroy', $item->uuid) }}" method="POST" style="display:inline;">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')"><i class="fas fa-trash"></i></button>
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