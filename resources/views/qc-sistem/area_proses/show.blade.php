@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Detail Data Area Proses</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('area-proses.index') }}">Data Area Proses</a></li>
                        <li class="breadcrumb-item active">Detail Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-eye text-info mr-2"></i>
                                Detail Data Area Proses
                            </h3>
                            <div class="card-tools">
                                <span class="badge badge-secondary">
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ $areaProses->created_at ? $areaProses->created_at->format('d-m-Y H:i:s') : '-' }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Basic Information -->
                                <div class="col-md-6">
                                    <div class="card card-outline card-primary">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="fas fa-info-circle mr-2"></i>
                                                Informasi Umum
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td width="40%"><strong>UUID:</strong></td>
                                                    <td>
                                                        <code>{{ $areaProses->uuid }}</code>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Area:</strong></td>
                                                    <td>
                                                        <span class="badge badge-info">
                                                            {{ $areaProses->area->area ?? '-' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Plan:</strong></td>
                                                    <td>
                                                        <span class="badge badge-secondary">
                                                            {{ $areaProses->plan->nama_plan ?? '-' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Tanggal:</strong></td>
                                                    <td>
                                                        <i class="fas fa-calendar-alt text-primary mr-1"></i>
                                                        {{ $areaProses->tanggal ? $areaProses->tanggal->format('d-m-Y H:i:s') : '-' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Jam:</strong></td>
                                                    <td>
                                                        <i class="fas fa-clock text-success mr-1"></i>
                                                        {{ $areaProses->jam }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Dibuat oleh:</strong></td>
                                                    <td>
                                                        <i class="fas fa-user text-warning mr-1"></i>
                                                        {{ $areaProses->user->name ?? '-' }}
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Information -->
                                <div class="col-md-6">
                                    <div class="card card-outline card-success">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="fas fa-check-circle mr-2"></i>
                                                Status Pemeriksaan
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-12 mb-3">
                                                    <label class="font-weight-bold">Kebersihan Ruangan:</label>
                                                    <div class="mt-1">
                                                        <span class="badge badge-{{ $areaProses->getStatusBadgeClass('kebersihan_ruangan') }} badge-lg">
                                                            <i class="fas fa-{{ $areaProses->getStatusIcon('kebersihan_ruangan') }} mr-1"></i>
                                                            {{ $areaProses->kebersihan_ruangan }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-12 mb-3">
                                                    <label class="font-weight-bold">Kebersihan Karyawan:</label>
                                                    <div class="mt-1">
                                                        <span class="badge badge-{{ $areaProses->getStatusBadgeClass('kebersihan_karyawan') }} badge-lg">
                                                            <i class="fas fa-{{ $areaProses->getStatusIcon('kebersihan_karyawan') }} mr-1"></i>
                                                            {{ $areaProses->kebersihan_karyawan }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-12 mb-3">
                                                    <label class="font-weight-bold">Pemeriksaan Suhu Ruang:</label>
                                                    <div class="mt-1">
                                                        <span class="badge badge-{{ $areaProses->getStatusBadgeClass('pemeriksaan_suhu_ruang') }} badge-lg">
                                                            <i class="fas fa-{{ $areaProses->getStatusIcon('pemeriksaan_suhu_ruang') }} mr-1"></i>
                                                            {{ $areaProses->pemeriksaan_suhu_ruang }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-12 mb-3">
                                                    <label class="font-weight-bold">Ketidaksesuaian:</label>
                                                    <div class="mt-1">
                                                        <span class="badge badge-{{ $areaProses->getStatusBadgeClass('ketidaksesuaian') }} badge-lg">
                                                            <i class="fas fa-{{ $areaProses->getStatusIcon('ketidaksesuaian') }} mr-1"></i>
                                                            {{ $areaProses->ketidaksesuaian }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-12 mb-3">
                                                    <label class="font-weight-bold">Tindakan Koreksi:</label>
                                                    <div class="mt-1">
                                                        <span class="badge badge-{{ $areaProses->getStatusBadgeClass('tindakan_koreksi') }} badge-lg">
                                                            <i class="fas fa-{{ $areaProses->getStatusIcon('tindakan_koreksi') }} mr-1"></i>
                                                            {{ $areaProses->tindakan_koreksi }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Summary Card -->
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="card card-outline card-info">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="fas fa-chart-pie mr-2"></i>
                                                Ringkasan Status
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row text-center">
                                                @php
                                                    $statusFields = ['kebersihan_ruangan', 'kebersihan_karyawan', 'pemeriksaan_suhu_ruang', 'ketidaksesuaian', 'tindakan_koreksi'];
                                                    $okCount = 0;
                                                    $tidakOkCount = 0;
                                                    foreach($statusFields as $field) {
                                                        if($areaProses->$field == 'OK') $okCount++;
                                                        else $tidakOkCount++;
                                                    }
                                                    $totalFields = count($statusFields);
                                                    $okPercentage = ($okCount / $totalFields) * 100;
                                                    $tidakOkPercentage = ($tidakOkCount / $totalFields) * 100;
                                                @endphp
                                                <div class="col-md-4">
                                                    <div class="info-box bg-success">
                                                        <span class="info-box-icon">
                                                            <i class="fas fa-check"></i>
                                                        </span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">Status OK</span>
                                                            <span class="info-box-number">{{ $okCount }} dari {{ $totalFields }}</span>
                                                            <div class="progress">
                                                                <div class="progress-bar" style="width: {{ $okPercentage }}%"></div>
                                                            </div>
                                                            <span class="progress-description">{{ number_format($okPercentage, 1) }}%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="info-box bg-danger">
                                                        <span class="info-box-icon">
                                                            <i class="fas fa-times"></i>
                                                        </span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">Status Tidak OK</span>
                                                            <span class="info-box-number">{{ $tidakOkCount }} dari {{ $totalFields }}</span>
                                                            <div class="progress">
                                                                <div class="progress-bar" style="width: {{ $tidakOkPercentage }}%"></div>
                                                            </div>
                                                            <span class="progress-description">{{ number_format($tidakOkPercentage, 1) }}%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="info-box bg-info">
                                                        <span class="info-box-icon">
                                                            <i class="fas fa-clipboard-check"></i>
                                                        </span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">Total Pemeriksaan</span>
                                                            <span class="info-box-number">{{ $totalFields }}</span>
                                                            <div class="progress">
                                                                <div class="progress-bar bg-info" style="width: 100%"></div>
                                                            </div>
                                                            <span class="progress-description">Selesai</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Metadata -->
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="alert alert-light border">
                                        <h6 class="alert-heading">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            Informasi Metadata
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <small class="text-muted">
                                                    <strong>Tanggal Dibuat:</strong> 
                                                    {{ $areaProses->created_at ? $areaProses->created_at->format('d-m-Y H:i:s') : '-' }}
                                                </small>
                                            </div>
                                            <div class="col-md-6">
                                                <small class="text-muted">
                                                    <strong>Terakhir Diupdate:</strong> 
                                                    {{ $areaProses->updated_at ? $areaProses->updated_at->format('d-m-Y H:i:s') : '-' }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('area-proses.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar
                                </a>
                                <div>
                                    @can('update', $areaProses)
@if(auth()->user()->hasPermissionTo('edit-area-proses'))
                                        <a href="{{ route('area-proses.edit', $areaProses->uuid) }}" class="btn btn-warning mr-2">
                                            <i class="fas fa-edit mr-2"></i>Edit Data
                                        </a>
                                    @endif
@endcan
                                    @can('delete', $areaProses)
@if(auth()->user()->hasPermissionTo('delete-area-proses'))
                                        <form action="{{ route('area-proses.destroy', $areaProses->uuid) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-trash mr-2"></i>Hapus Data
                                            </button>
                                        </form>
                                    @endif
@endcan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
