@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Detail Barang Mudah Pecah</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('barang-mudah-pecah.index') }}">Barang Mudah Pecah</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="card-title">
                                    <i class="fas fa-eye text-primary mr-2"></i>
                                    Detail Barang Mudah Pecah
                                </h3>
                                <a href="{{ route('barang-mudah-pecah.index') }}" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @php
                                $userRole = auth()->user()->id_role ?? null;
                                $showTime = in_array($userRole, [1, 2, 5]); // superadmin, admin, spv
                                $format = $showTime ? 'd-m-Y H:i:s' : 'd-m-Y';
                            @endphp

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Tanggal</label>
                                        <div class="form-control bg-light">
                                            {{ $barangMudahPecah->tanggal ? \Carbon\Carbon::parse($barangMudahPecah->tanggal)->format($format) : '-' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Shift</label>
                                        <div class="form-control bg-light">
                                            {{ $barangMudahPecah->shift->shift ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Waktu</label>
                                        <div class="form-control bg-light">
                                            {{ $barangMudahPecah->jam ? \Carbon\Carbon::parse($barangMudahPecah->jam)->format('H:i') : '-' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Area</label>
                                        <div class="form-control bg-light">
                                            {{ $barangMudahPecah->area->area ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" style="white-space: nowrap;">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 60px;">No</th>
                                            <th>Nama Barang</th>
                                            <th class="text-center" style="width: 120px;">Jumlah</th>
                                            <th class="text-center" style="width: 140px;">Kondisi</th>
                                            <th class="text-center">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($groupItems as $idx => $item)
                                            <tr>
                                                <td class="text-center">{{ $idx + 1 }}</td>
                                                <td>
                                                    @if($item->is_manual && !empty($item->nama_barang_manual))
                                                        {{ $item->nama_barang_manual }}
                                                    @else
                                                        {{ $item->namaBarang->nama_barang ?? 'Data tidak ditemukan' }}
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $item->jumlah ?? '-' }}</td>
                                                <td class="text-center">
                                                    <span class="badge {{ $item->getStatusBadgeClass() }}">
                                                        <i class="{{ $item->getStatusIcon() }}"></i>
                                                        {{ $item->kondisi }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    @if($item->temuan_ketidaksesuaian)
                                                        {{ $item->temuan_ketidaksesuaian }}
                                                    @else
                                                        <span class="text-muted">-</span>
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
        </div>
    </section>
</div>
@endsection
