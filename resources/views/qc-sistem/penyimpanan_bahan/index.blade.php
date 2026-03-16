@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1> Penyimpanan Bahan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item active">List Penyimpanan Bahan</li>
                    </ol>
                </div>
            </div>
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
                    <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            @if(request('group_uuid'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-history"></i> Sedang menampilkan riwayat data per 2 jam untuk Group UUID: <code>{{ request('group_uuid') }}</code>.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title"></i> Data Penyimpanan Bahan</h3>
                            <div class="card-tools">
                                @if(request('group_uuid'))
                                    <a href="{{ route('penyimpanan-bahan.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-arrow-left"></i> Kembali ke Semua Data
                                    </a>
                                @else
                                    @if(auth()->user()->hasPermissionTo('create-penyimpanan-bahan'))
                                    <a href="{{ route('penyimpanan-bahan.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Tambah Data
                                    </a>
                               @endif
 @endif
                            </div>
                        </div>
                        <div class="card-body">
                            @include('qc-sistem.penyimpanan_bahan._table')
                        </div>
                    </div>
                </div>
            </div>        
        </div>
    </section>
</div>
@endsection