@extends('layouts.app')
@php
    $isLocalhost = in_array(request()->getHost(), ['localhost', '127.0.0.1', '10.68.1.37']);
    $assetPath = $isLocalhost ? 'public/' : '';
@endphp
@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Detail Ketidaksesuaian Benda Asing</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('ketidaksesuaian-benda-asing.index') }}">Ketidaksesuaian Benda Asing</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Ketidaksesuaian Benda Asing</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Tanggal</th>
                                            <td>
                                                <span class="badge badge-info">
                                                    {{ $ketidaksesuaianBendaAsing->tanggal ? $ketidaksesuaianBendaAsing->tanggal->format('d-m-Y') : '-' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Jam</th>
                                            <td>
                                                <span class="badge badge-secondary">
                                                    {{ $ketidaksesuaianBendaAsing->jam ?? '-' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Shift</th>
                                            <td>
                                                <span class="badge badge-secondary">
                                                    {{ $ketidaksesuaianBendaAsing->shift->shift ?? '-' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Produk</th>
                                            <td><strong>{{ $ketidaksesuaianBendaAsing->produk->nama_produk ?? '-' }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Kode Produksi</th>
                                            <td><strong>{{ $ketidaksesuaianBendaAsing->kode_produksi }}</strong></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Jenis Kontaminan:</label>
                                        <div class="border p-3 bg-light">
                                            {{ $ketidaksesuaianBendaAsing->jenis_kontaminan }}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="font-weight-bold">Jumlah Produk Terdampak:</label>
                                        <div class="border p-3 bg-light">
                                            {{ number_format($ketidaksesuaianBendaAsing->jumlah_produk_terdampak) }}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="font-weight-bold">Tahapan:</label>
                                        <div class="border p-3 bg-light">
                                            {{ $ketidaksesuaianBendaAsing->tahapan }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-12">
                                    <strong>Dokumentasi:</strong><br>
                                    @if($ketidaksesuaianBendaAsing->dokumentasi)
                                        <a href="#" data-toggle="modal" data-target="#modalDokumentasi">
                                            <img src="{{ asset($assetPath . 'storage/' . $ketidaksesuaianBendaAsing->dokumentasi) }}" alt="Dokumentasi" style="width: 100px; height: 100px; object-fit: cover; border-radius: 4px;">
                                        </a>
                                        <!-- Modal -->
                                        <div class="modal fade" id="modalDokumentasi" tabindex="-1" role="dialog" aria-labelledby="modalDokumentasiLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="modalDokumentasiLabel">Dokumentasi</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <img src="{{ asset($assetPath . 'storage/' . $ketidaksesuaianBendaAsing->dokumentasi) }}" alt="Dokumentasi" style="max-width:100%;max-height:70vh;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span>-</span>
                                    @endif
                                </div>
                            </div>

                            <hr>
                            <div class="row">
                                <div class="col-12">
@if(auth()->user()->hasPermissionTo('edit-ketidaksesuaian-benda-asing'))
                                    <a href="{{ route('ketidaksesuaian-benda-asing.edit', $ketidaksesuaianBendaAsing->uuid) }}" class="btn btn-warning btn-md">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    @endif
<a href="{{ route('ketidaksesuaian-benda-asing.index') }}" class="btn btn-secondary btn-md ml-2">
                                        <i class="fas fa-arrow-left"></i>
                                        Kembali
                                    </a>
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
