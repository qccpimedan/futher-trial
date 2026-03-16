{{-- filepath: resources/views/super-admin/jenis_better/index.blade.php --}}
@extends('layouts.app')

@section('container')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Data Jenis Better</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Home</a></li>
                            <li class="breadcrumb-item active">Jenis Better</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Data Jenis Better</h3>
                                <a href="{{ route('jenis-better.create') }}" class="btn btn-primary btn-sm float-right"><i class="fas fa-plus"></i> Tambah Data</a>
                            </div>
                            <div class="card-body">
                                @if(session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif
                                <table id="myTable" class="table text-center table-bordered table-striped table-responsive" style="white-space: nowrap;">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Plan</th>
                                            <th>Produk</th>
                                            <th>Nama Better</th>
                                            <th>Nama Bahan</th>
                                            <th>Berat (kg)</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data as $i => $item)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ $item->plan->nama_plan ?? '-' }}</td>
                                            <td>{{ $item->produk->nama_produk ?? '-' }}</td>
                                            <td>
                                                <span style="text-transform:uppercase;">{{ $item->nama_better ?? '-' }}</span>
                                            </td>
                                            <td>
                                                @php
                                                    $formulaList = [];
                                                    if (is_array($item->better_items) && count($item->better_items) > 0) {
                                                        foreach ($item->better_items as $bi) {
                                                            if (!empty($bi['nama_formula_better'])) {
                                                                $formulaList[] = $bi['nama_formula_better'];
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                @if(count($formulaList))
                                                    <ul class="mb-0 pl-3">
                                                        @foreach($formulaList as $f)
                                                            <li>{{ $f }}</li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    {{ $item->nama_formula_better ?? '-' }}
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $beratList = [];
                                                    if (is_array($item->better_items) && count($item->better_items) > 0) {
                                                        foreach ($item->better_items as $bi) {
                                                            if (!empty($bi['berat'])) {
                                                                $beratList[] = $bi['berat'];
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                @if(count($beratList))
                                                    <ul class="mb-0 pl-3">
                                                        @foreach($beratList as $b)
                                                            <li>{{ $b }}</li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    {{ $item->berat ?? '-' }}
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('jenis-better.edit', $item->uuid) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                                <form action="{{ route('jenis-better.destroy', $item->uuid) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah anda yakin ingin menghapus data ini?')"><i class="fas fa-trash"></i></button>
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