@extends('layouts.app')

@section('title', 'Detail Data Timbangan')

@section('container')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Detail Data Timbangan</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <!-- <li class="breadcrumb-item"><a href="#">QC Sistem</a></li> -->
                    <li class="breadcrumb-item"><a href="{{ route('timbangan.index') }}">Data Timbangan</a></li>
                    <li class="breadcrumb-item active">Detail Data</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-balance-scale mr-1"></i>
                            Detail Data Timbangan
                        </h3>
                        <div class="card-tools">
@if(auth()->user()->hasPermissionTo('edit-timbangan'))
                            <a href="{{ route('timbangan.edit', $data->uuid) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit Data
                            </a>
                            @endif
<a href="{{ route('timbangan.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    
                    <div class="card-body">
                        <div class="row">
                            <!-- Kolom Kiri -->
                            <div class="col-md-6">
                                <div class="card card-outline card-info">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-info-circle"></i>
                                            Informasi Dasar
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td style="width: 40%;"><strong><i class="fas fa-clock"></i> Shift:</strong></td>
                                                <td>
                                                    <span class="badge badge-primary">
                                                        {{ $data->shift->shift ?? '-' }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong><i class="fas fa-calendar-alt"></i> Tanggal & Waktu:</strong></td>
                                                <td>
                                                    <span class="badge badge-info">
                                                        {{ \Carbon\Carbon::parse($data->tanggal)->format('d/m/Y H:i:s') }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Hasil Verifikasi 500 Gr:</strong></td>
                                                <td>
                                                    <span class="badge badge-secondary">
                                                        {{ $data->hasil_verifikasi_500 ?? '-' }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Hasil Verifikasi 1000 Gr:</strong></td>
                                                <td>
                                                    <span class="badge badge-secondary">
                                                        {{ $data->hasil_verifikasi_1000 ?? '-' }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong><i class="fas fa-barcode"></i> Kode Timbangan:</strong></td>
                                                <td>
                                                    <span class="badge badge-secondary">
                                                        {{ $data->kode_timbangan }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Kolom Kanan -->
                            <div class="col-md-6">
                                <div class="card card-outline card-success">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-check-circle"></i>
                                            Hasil Pengecekan
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center mb-3">
                                            <div class="display-4">
                                                <span class="badge {{ $data->hasil_pengecekan === 'ok' ? 'badge-success' : 'badge-danger' }}" style="font-size: 1.5rem;">
                                                    {!! $data->hasil_pengecekan_label !!}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="alert {{ $data->hasil_pengecekan === 'ok' ? 'alert-success' : 'alert-danger' }}">
                                            <i class="fas {{ $data->hasil_pengecekan === 'ok' ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                            <strong>Status:</strong>
                                            {{ $data->hasil_pengecekan === 'ok' ? 'Hasil pengecekan sesuai standar' : 'Hasil pengecekan tidak sesuai standar' }}
                                        </div>

                                        <div class="progress mb-3">
                                            <div class="progress-bar {{ $data->hasil_pengecekan === 'ok' ? 'bg-success' : 'bg-danger' }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $data->hasil_pengecekan === 'ok' ? '100' : '0' }}%" 
                                                 aria-valuenow="{{ $data->hasil_pengecekan === 'ok' ? '100' : '0' }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                {{ $data->hasil_pengecekan === 'ok' ? '100%' : '0%' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Metadata -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card card-outline card-secondary">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-info"></i>
                                            Informasi Sistem
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <table class="table table-borderless">
                                                    <tr>
                                                        <td style="width: 40%;"><strong><i class="fas fa-user"></i> Dibuat oleh:</strong></td>
                                                        <td>
                                                            <span class="badge badge-secondary">
                                                                {{ $data->user->name ?? '-' }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong><i class="fas fa-building"></i> Plan:</strong></td>
                                                        <td>
                                                            <span class="badge badge-primary">
                                                                {{ $data->plan->nama_plan ?? '-' }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <table class="table table-borderless">
                                                    <tr>
                                                        <td style="width: 40%;"><strong><i class="fas fa-calendar-plus"></i> Dibuat pada:</strong></td>
                                                        <td>
                                                            <span class="badge badge-info">
                                                                {{ $data->created_at ? $data->created_at->format('d/m/Y H:i:s') : '-' }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong><i class="fas fa-calendar-check"></i> Terakhir diupdate:</strong></td>
                                                        <td>
                                                            <span class="badge badge-warning">
                                                                {{ $data->updated_at ? $data->updated_at->format('d/m/Y H:i:s') : '-' }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- UUID Information -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card card-outline card-dark">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-fingerprint"></i>
                                            Identifikasi Unik
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>UUID:</strong> 
                                            <code>{{ $data->uuid }}</code>
                                        </p>
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle"></i>
                                            UUID digunakan untuk identifikasi unik data ini dalam sistem
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6">
@if(auth()->user()->hasPermissionTo('edit-timbangan'))
                                <a href="{{ route('timbangan.edit', $data->uuid) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Edit Data
                                </a>
                                @endif
<a href="{{ route('timbangan.index') }}" class="btn btn-secondary ml-2">
                                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                                </a>
                            </div>
                            <div class="col-md-6 text-right">
@if(auth()->user()->hasPermissionTo('delete-timbangan'))
                                <form action="{{ route('timbangan.destroy', $data->uuid) }}" 
                                      method="POST" 
                                      style="display: inline;"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i> Hapus Data
                                    </button>
                                </form>
                            @endif
</div>
                        </div>
                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>
<!-- /.content -->
@endsection
