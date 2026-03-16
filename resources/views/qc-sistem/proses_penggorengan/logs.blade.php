@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-history text-warning"></i> Log Perubahan Proses Penggorengan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href=""><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('penggorengan.index') }}">Proses Penggorengan</a></li>
                        <li class="breadcrumb-item active">Log Perubahan</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Info Card -->
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info-circle"></i> Informasi Data</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Produk:</strong><br>
                            <span class="badge badge-info">{{ $penggorengan->produk->nama_produk ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Kode Produksi:</strong><br>
                            <span class="badge badge-secondary">{{ $penggorengan->kode_produksi }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Shift:</strong><br>
                            <span class="badge badge-primary">Shift {{ $penggorengan->shift->shift ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Tanggal:</strong><br>
                            <span class="badge badge-success">{{ $penggorengan->tanggal ? $penggorengan->tanggal->format('d-m-Y H:i:s') : 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Logs Card -->
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-history"></i> Riwayat Perubahan Data</h3>
                    <div class="card-tools">
                        <a href="{{ route('penggorengan.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($logs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 5%;">No</th>
                                        <th style="width: 15%;">Tanggal & Waktu Update</th>
                                        <th style="width: 10%;">Role</th>
                                        <th style="width: 10%;">Aksi</th>
                                        <th style="width: 20%;">Field yang Diubah</th>
                                        <th style="width: 25%;">Detail Perubahan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($logs as $index => $log)
                                    <tr>
                                        <td>{{ $logs->firstItem() + $index }}</td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $log->created_at->format('d/m/Y') }}<br>
                                                {{ $log->created_at->format('H:i:s') }}
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $log->user_name ?? 'System' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-warning">
                                                <i class="fas fa-edit"></i> Update
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-primary">{{ $log->nama_field }}</span>
                                        </td>
                                        <td>
                                            <div class="change-details">
                                                @if($log->nilai_lama && $log->nilai_baru)
                                                    @foreach($log->field_yang_diubah as $index => $field)
                                                        @php
                                                            $nilaiLama = $log->nilai_lama[$index] ?? 'Kosong';
                                                            $nilaiBaru = $log->nilai_baru[$index] ?? 'Kosong';
                                                            $namaField = $log->getNamaFieldSingle($field);
                                                        @endphp
                                                        <div class="mb-1">
                                                            <strong>{{ $namaField }}:</strong><br>
                                                            <span class="text-danger">{{ $nilaiLama }}</span> 
                                                            <i class="fas fa-arrow-right text-muted"></i> 
                                                            <span class="text-success">{{ $nilaiBaru }}</span>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $logs->links('pagination.simple') }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-history fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada riwayat perubahan</h5>
                            <p class="text-muted">Data ini belum pernah diubah sejak dibuat.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
