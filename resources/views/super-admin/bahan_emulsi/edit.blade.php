{{-- filepath: [edit.blade.php](http://_vscodecontentref_/3) --}}
@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Bahan Emulsi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('bahan-emulsi.index') }}">Bahan Emulsi</a></li>
                        <li class="breadcrumb-item active">Edit Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">Edit Data Bahan Emulsi</h3>
                        </div>
                        <form action="{{ route('bahan-emulsi.update', $item->uuid) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Nama RM</label>
                                    <input type="text" name="nama_rm" class="form-control" value="{{ $item->nama_rm }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Berat RM (kg)</label>
                                    <input type="text" name="berat_rm" class="form-control" value="{{ $item->berat_rm }}" placeholder="000" required>
                                </div>
                                <div class="form-group">
                                    <label>Nomor Emulsi</label>
                                    <select name="nomor_emulsi_id" class="form-control" required>
                                        @foreach($nomorEmulsis as $ne)
                                            <option value="{{ $ne->id }}" {{ $item->nomor_emulsi_id == $ne->id ? 'selected' : '' }}>{{ $ne->nomor_emulsi }} - {{ $ne->produk->nama_produk ?? 'Produk Tidak Ditemukan' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Total Pemakaian</label>
                                    <select name="total_pemakaian_id" class="form-control" required>
                                        @foreach($totalPemakaians as $tp)
                                            <option value="{{ $tp->id }}" {{ $item->total_pemakaian_id == $tp->id ? 'selected' : '' }}>{{ $tp->total_pemakaian }} - {{ $tp->produk->nama_produk ?? 'Produk Tidak Ditemukan' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Produk</label>
                                    <select name="id_produk" class="form-control" required>
                                        @foreach($produks as $produk)
                                            <option value="{{ $produk->id }}" {{ $item->id_produk == $produk->id ? 'selected' : '' }}>{{ $produk->nama_produk }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Nama Emulsi</label>
                                    <select name="nama_emulsi_id" class="form-control" required>
                                        @foreach($emulsis as $emulsi)
                                            <option value="{{ $emulsi->id }}" {{ $item->nama_emulsi_id == $emulsi->id ? 'selected' : '' }}>{{ $emulsi->nama_emulsi }}  - {{ $emulsi->produk->nama_produk ?? 'Produk Tidak Ditemukan' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Plan</label>
                                    <select name="id_plan" class="form-control" required>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}" {{ $item->id_plan == $plan->id ? 'selected' : '' }}>{{ $plan->nama_plan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Update</button>
                                <a href="{{ route('bahan-emulsi.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection