
@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-eye text-primary"></i> Detail Persiapan Bahan Non Forming</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('persiapan-bahan-forming.index') }}">Persiapan Bahan</a></li>
                        <li class="breadcrumb-item active">Detail Non Forming</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <i class="icon fas fa-check"></i> {{ session('success') }}
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tanggal</label>
                                        <input type="text" class="form-control" value="{{ $data->tanggal ? \Carbon\Carbon::parse($data->tanggal)->format('Y-m-d H:i:s') : '-' }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Shift</label>
                                        <input type="text" class="form-control" value="{{ $data->shift->shift ?? '-' }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Jam</label>
                                        <input type="text" class="form-control" value="{{ $data->jam ? \Carbon\Carbon::parse($data->jam)->format('H:i') : '-' }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nama Produk</label>
                                        <input type="text" class="form-control" value="{{ $data->formulaNonForming->produk->nama_produk ?? '-' }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nomor Formula</label>
                                        <input type="text" class="form-control" value="{{ $data->formulaNonForming->nomor_formula ?? '-' }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Suhu Adonan (STD)</label>
                                        <input type="text" class="form-control" value="{{ $data->suhuAdonan->std_suhu ?? '-' }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Kode Produksi</label>
                                        <input type="text" class="form-control" value="{{ $data->kode_produksi ?? '-' }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Waktu Mulai Mixing</label>
                                        <input type="text" class="form-control" value="{{ $data->waktu_mulai_mixing ?? '-' }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Waktu Selesai Mixing</label>
                                        <input type="text" class="form-control" value="{{ $data->waktu_selesai_mixing ?? '-' }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Kondisi</label>
                                        <input type="text" class="form-control" value="{{ $data->kondisi ?? '-' }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Rework</label>
                                        <input type="text" class="form-control" value="{{ $data->rework ?? '-' }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Dibuat Oleh</label>
                                        <input type="text" class="form-control" value="{{ $data->user->name ?? '-' }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Catatan</label>
                                <textarea class="form-control" rows="3" readonly>{{ $data->catatan ?? '-' }}</textarea>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th>Nama RM</th>
                                            <th class="text-center">Berat RM</th>
                                            <th class="text-center">Kode Produksi Bahan</th>
                                            <th class="text-center">Suhu RM</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($data->details as $i => $d)
                                            <tr>
                                                <td class="text-center">{{ $i + 1 }}</td>
                                                <td>{{ $d->bahanNonForming->nama_rm ?? '-' }}</td>
                                                <td class="text-center">{{ $d->bahanNonForming->berat_rm ?? '-' }}</td>
                                                <td class="text-center">{{ $d->kode_produksi_bahan ?? '-' }}</td>
                                                <td class="text-center">{{ $d->suhu ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">Tidak ada detail bahan</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer">
                            <a href="{{ route('persiapan-bahan-forming.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
@if(auth()->user()->hasPermissionTo('edit-persiapan-bahan-non-forming'))
                            <a href="{{ route('persiapan-bahan-non-forming.edit', $data->uuid) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit Data
                            </a>
                        @endif
</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
