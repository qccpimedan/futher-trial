@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-sm-6">
                    <h1 class="m-0">Detail Bahan Baku Tumbling</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('bahan-baku-tumbling.index') }}">Bahan Baku Tumbling</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-12 col-md-10">
                    <div class="card card-primary">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-eye mr-2"></i>Informasi Detail Bahan Baku Tumbling</h5>
                        </div>
                        <div class="card-body">
                            {{-- Informasi Dasar --}}
                            <div class="card card-outline card-info mb-3">
                                <div class="card-header py-2">
                                    <h6 class="mb-0">Informasi Dasar</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Tanggal</label>
                                                <p class="form-control-plaintext">{{ $bahanBakuTumbling->tanggal ? $bahanBakuTumbling->tanggal->format('d-m-Y H:i:s') : '-' }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Jam</label>
                                                <p class="form-control-plaintext">{{ $bahanBakuTumbling->jam ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Shift</label>
                                                <p class="form-control-plaintext">
                                                    <span class="badge badge-info">{{ $bahanBakuTumbling->shift->shift ?? '-' }}</span>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Plan</label>
                                                <p class="form-control-plaintext">{{ $bahanBakuTumbling->plan->nama_plan ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Detail Bahan Baku --}}
                            <div class="card card-outline card-info mb-3">
                                <div class="card-header py-2">
                                    <h6 class="mb-0">Detail Bahan Baku</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Nama Produk</label>
                                                <p class="form-control-plaintext">{{ $bahanBakuTumbling->produk->nama_produk ?? '-' }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Kode Produksi</label>
                                                <p class="form-control-plaintext">{{ $bahanBakuTumbling->kode_produksi ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    @if ($bahanBakuTumbling->manual_bahan_data)
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <!-- <th>No</th> -->
                                                        <th>Nama Bahan Baku</th>
                                                        <th>Jumlah (kg)</th>
                                                        <th>Kode Produksi Bahan Baku</th>
                                                        <th>Suhu (°C)</th>
                                                        <th>Kondisi Daging</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($bahanBakuTumbling->manual_bahan_data as $index => $bahan)
                                                        <tr>
                                                            <!-- <td>{{ $index + 1 }}</td> -->
                                                            <td>{{ $bahan['nama_bahan_baku'] ?? '-' }}</td>
                                                            <td>{{ $bahan['jumlah'] ?? '-' }}</td>
                                                            <td>{{ $bahan['kode_produksi_bahan_baku'] ?? '-' }}</td>
                                                            <td>{{ $bahan['suhu'] ?? '-' }}</td>
                                                            <td>
                                                                {{ $bahan['kondisi_daging'] ?? '-' }}
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="6" class="text-center text-muted">Tidak ada data bahan manual</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    @elseif ($bahanBakuTumbling->id_bahan_nonforming)
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">Nama Bahan Baku</label>
                                                    <p class="form-control-plaintext">{{ $bahanBakuTumbling->nama_bahan_baku ?? '-' }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">Jumlah (kg)</label>
                                                    <p class="form-control-plaintext">{{ $bahanBakuTumbling->jumlah ?? '-' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">Kode Produksi Bahan Baku</label>
                                                    <p class="form-control-plaintext">{{ $bahanBakuTumbling->kode_produksi_bahan_baku ?? '-' }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">Suhu (°C)</label>
                                                    <p class="form-control-plaintext">{{ $bahanBakuTumbling->suhu ?? '-' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">Kondisi Daging</label>
                                                    <p class="form-control-plaintext">
                                                        <span class="badge {{ $bahanBakuTumbling->kondisi_daging === '✔' ? 'badge-success' : 'badge-danger' }}">
                                                            {{ $bahanBakuTumbling->kondisi_daging ?? '-' }}
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle mr-2"></i>Tidak ada data bahan baku
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Informasi Tambahan --}}
                            <div class="card card-outline card-info mb-3">
                                <div class="card-header py-2">
                                    <h6 class="mb-0">Informasi Tambahan</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Salinity</label>
                                                <p class="form-control-plaintext">{{ $bahanBakuTumbling->salinity ?? '-' }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Hasil Pencampuran</label>
                                                <p class="form-control-plaintext">
                                                    <span class="badge {{ $bahanBakuTumbling->hasil_pencampuran === '✓' ? 'badge-success' : 'badge-warning' }}">
                                                        {{ $bahanBakuTumbling->hasil_pencampuran ?? '-' }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Informasi User --}}
                            <!-- <div class="card card-outline card-secondary mb-3">
                                <div class="card-header py-2">
                                    <h6 class="mb-0">Informasi User</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">User</label>
                                                <p class="form-control-plaintext">{{ $bahanBakuTumbling->user->name ?? '-' }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Dibuat Pada</label>
                                                <p class="form-control-plaintext">{{ $bahanBakuTumbling->created_at ? $bahanBakuTumbling->created_at->format('d-m-Y H:i:s') : '-' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Diubah Pada</label>
                                                <p class="form-control-plaintext">{{ $bahanBakuTumbling->updated_at ? $bahanBakuTumbling->updated_at->format('d-m-Y H:i:s') : '-' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> -->

                            {{-- Action Buttons --}}
                            <div class="form-group">
@if(auth()->user()->hasPermissionTo('edit-bahan-baku-tumbling'))
                                <a href="{{ route('bahan-baku-tumbling.edit', $bahanBakuTumbling->uuid) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                @endif
<a href="{{ route('bahan-baku-tumbling.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection