@extends('layouts.app')

@php
    $isLocalhost = in_array(request()->getHost(), ['localhost', '127.0.0.1', '10.68.1.37']);
    $assetPath = $isLocalhost ? 'public/' : '';
@endphp

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Detail Data Shoestring</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('shoestring.index') }}">Shoestring</a></li>
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
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Data Utama</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 35%">Shift</th>
                                            <td>{{ $shoestring->shift->shift ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal</th>
                                            <td>
                                                @php
                                                    $userRole = auth()->user()->id_role ?? null;
                                                    $showTime = in_array($userRole, [1, 2, 5]);
                                                    $format = $showTime ? 'd-m-Y H:i:s' : 'd-m-Y';
                                                @endphp
                                                {{ $shoestring->tanggal ? \Carbon\Carbon::parse($shoestring->tanggal)->format($format) : '-' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Jam</th>
                                            <td>{{ $shoestring->jam ? \Carbon\Carbon::parse($shoestring->jam)->format('H:i') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Nama Produsen</th>
                                            <td>{{ $shoestring->nama_produsen ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 35%">Kode Produksi</th>
                                            <td>{{ $shoestring->kode_produksi ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal Expired</th>
                                            <td>{{ $shoestring->best_before ? \Carbon\Carbon::parse($shoestring->best_before)->format('d-m-Y') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Pemeriksaan Defect</th>
                                            <td>{{ $shoestring->sampling_defect ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Total Defect</th>
                                            <td>{{ $shoestring->total_defect ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Catatan</th>
                                            <td>{{ $shoestring->catatan ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-12">
                                    <h5>Jumlah per Defect</h5>
                                    @if(!empty($qtyMap))
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 60%">Defect</th>
                                                        <th style="width: 40%">Jumlah</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($qtyMap as $defectId => $qty)
                                                        @php
                                                            $defect = $defectsById->get((int) $defectId);
                                                            $label = $defect ? $defect->jenis_defect . ($defect->spec_defect ? ' - ' . $defect->spec_defect : '') : 'Defect #' . $defectId;
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $label }}</td>
                                                            <td>{{ $qty }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-muted">Tidak ada jumlah defect.</div>
                                    @endif

                                    @if($shoestring->dokumentasi && is_array($shoestring->dokumentasi) && count($shoestring->dokumentasi) > 0)
                                        <h5 class="mt-4">Dokumentasi (Foto)</h5>
                                        <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px;">
                                            @foreach($shoestring->dokumentasi as $doc)
                                                <a href="{{ asset($assetPath . 'storage/' . $doc) }}" target="_blank">
                                                    <img src="{{ asset($assetPath . 'storage/' . $doc) }}" class="img-thumbnail" style="height: 150px; width: 150px; object-fit: cover;">
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                    <!--
@if(auth()->user()->hasPermissionTo('edit-shoestring')) <a href="{{ route('shoestring.edit', $shoestring->uuid) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a> @endif
-->
                                    <a href="{{ route('shoestring.index') }}" class="btn btn-secondary btn-md ml-2">
                                        <i class="fas fa-arrow-left"></i> Kembali
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
