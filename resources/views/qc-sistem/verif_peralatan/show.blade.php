@extends('layouts.app')

@section('title', 'Detail Verifikasi Peralatan')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Detail Verifikasi Peralatan</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('verif-peralatan.index') }}">Verifikasi Peralatan</a></li>
                            <li class="breadcrumb-item active">Detail</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Informasi</h3>
                                <div class="card-tools">
                                    <a href="{{ route('verif-peralatan.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
@if(auth()->user()->hasPermissionTo('edit-verif-peralatan'))
                                    <a href="{{ route('verif-peralatan.edit', $item->uuid) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                @endif
</div>
                            </div>
                            <div class="card-body">
                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        {{ session('success') }}
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="col-md-3"><strong>Tanggal</strong><br>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</div>
                                    <div class="col-md-3"><strong>Jam</strong><br>{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</div>
                                    <div class="col-md-3"><strong>Shift</strong><br>{{ $item->shift->shift ?? '-' }}</div>
                                    <div class="col-md-3"><strong>Plan</strong><br>{{ $item->plan->nama_plan ?? '-' }}</div>
                                    <div class="col-md-3"><strong>Dibuat Oleh</strong><br>{{ $item->user->name ?? '-' }}</div>
                                </div>
                            </div>
                        </div>

                        @foreach($detailsByArea as $areaId => $details)
                            @php
                                $first = $details->first();
                                $areaNama = $first?->mesin?->area?->area;
                            @endphp

                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Area: {{ $areaNama ?? '-' }}</h3>
                                </div>
                                <div class="card-body table-responsive">
                                    <table class="table table-bordered table-striped" style="white-space:nowrap;">
                                        <thead>
                                            <tr>
                                                <th style="width:140px;" class="text-center">Verifikasi</th>
                                                <th>Mesin/Peralatan</th>
                                                <th>Keterangan</th>
                                                <th>Tindakan Koreksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($details as $d)
                                                <tr>
                                                    <td class="text-center">
                                                        @if($d->verifikasi)
                                                            <span class="badge badge-success">OK</span>
                                                        @else
                                                            <span class="badge badge-danger">Tidak OK</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $d->mesin->nama_mesin ?? '-' }}</td>
                                                    <td>
                                                        {{ $d->verifikasi ? '-' : ($d->keterangan ?? '-') }}
                                                    </td>
                                                    <td>
                                                        {{ $d->verifikasi ? '-' : ($d->tindakan_koreksi ?? '-') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
