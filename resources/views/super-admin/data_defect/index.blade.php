@extends('layouts.app')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h1 class="m-0">Data Defect</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                            <li class="breadcrumb-item active">Data Defect</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Daftar Data Defect</h3>
                                <div class="card-tools">
                                    <a href="{{ route('data-defect.create') }}" class="btn btn-primary btn-sm">
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

                                <div class="table-responsive text-center">
                                    <table id="myTable" class="table table-bordered table-striped table-hover" style="white-space: nowrap;">
                                        <thead class="">
                                            <tr>
                                                <th>No</th>
                                                <th>Plan</th>
                                                <th>Jenis Defect</th>
                                                <th>Spec Defect</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($data as $index => $item)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>
                                                        {{ $item->plan->nama_plan ?? '-' }}
                                                    </td>
                                                    <td>{{ $item->jenis_defect }}</td>
                                                    <td>{{ $item->spec_defect ?? '-' }}</td>
                                                    <td>
                                                        <div class="btn-vertical">
                                                            <a href="{{ route('data-defect.edit', $item->uuid) }}" 
                                                               class="btn btn-warning btn-sm" 
                                                               title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <form action="{{ route('data-defect.destroy', $item->uuid) }}" 
                                                                  method="POST" 
                                                                  class="d-inline"
                                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" 
                                                                        class="btn btn-danger btn-sm" 
                                                                        title="Hapus">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center">
                                                        <div class="py-4">
                                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                            <p class="text-muted">Belum ada data defect</p>
                                                            <a href="{{ route('data-defect.create') }}" class="btn btn-primary btn-sm">
                                                                <i class="fas fa-plus"></i> Tambah Data Pertama
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

@endsection