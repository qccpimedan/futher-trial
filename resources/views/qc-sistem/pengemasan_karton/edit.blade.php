{{-- filepath: d:\laragon\www\paperless_futher\resources\views\qc-sistem\bahan_baku_tumbling\edit.blade.php --}}
@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h1 class="m-0">Edit Pengemasan Karton</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('pengemasan-karton.index') }}">Pengemasan Karton</a></li>
                            <li class="breadcrumb-item active">Edit Pengemasan Karton</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

  <section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-12 col-md-10">
                <div class="card card-warning card-outline">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-edit mr-2"></i>Edit Data Pengemasan Karton</h5>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger rounded">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form action="{{ route('pengemasan-karton.update', $pengemasanKarton->uuid) }}" method="POST" autocomplete="off">
                            @csrf
                            @method('PUT')
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="id_plan" class="font-weight-bold">Plan <span class="text-danger">*</span></label>
                                    <select name="id_plan" id="id_plan" class="form-control @error('id_plan') is-invalid @enderror" required>
                                        <option value="">Pilih Plan</option>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}" {{ old('id_plan', $pengemasanKarton->id_plan) == $plan->id ? 'selected' : '' }}>
                                                {{ $plan->nama_plan }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_plan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="shift_id" class="font-weight-bold">Shift <span class="text-danger">*</span></label>
                                    <select name="shift_id" id="shift_id" class="form-control @error('shift_id') is-invalid @enderror" required>
                                        <option value="">Pilih Shift</option>
                                        @foreach($shifts as $shift)
                                            <option value="{{ $shift->id }}" {{ old('shift_id', $pengemasanKarton->shift_id) == $shift->id ? 'selected' : '' }}>
                                                {{ $shift->shift }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('shift_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row">
                               <div class="form-group col-md-6">
    <label for="id_produk_berat_produk_box" class="font-weight-bold">Nama Produk <span class="text-danger">*</span></label>
   <input type="text" 
        class="form-control" 
        value="{{ $pengemasanKarton->pengemasanProduk->kode_produksi ?? 'data kosong' }} -{{ $pengemasanKarton->pengemasanProduk->produk->nama_produk ?? 'data kosong' }} {{ $pengemasanKarton->pengemasanProduk->berat ?? 'data kosong' }} gram" 
        readonly>
   

    <input type="hidden" class="form-control" id="id_berat_produk_bag" readonly>
    <input type="hidden" class="form-control" id="id_pengemasan_plastik" readonly>
    <input type="hidden" class="form-control" id="id_pengemasan_produk" readonly>
</div>
                                <div class="form-group col-md-6">
                                    <label for="kode_produksi_rm" class="font-weight-bold">Identitas Produk Pada Karton (Tinta) <span class="text-danger">*</span></label>
                                    <select class="form-control" name="identitas_produk_pada_karton" id="identitas_produk_pada_karton">
                                        <option value="✔" {{ old('identitas_produk_pada_karton', $pengemasanKarton->identitas_produk_pada_karton) == '✔' ? 'selected' : '' }}>✔</option>
                                        <option value="✘" {{ old('identitas_produk_pada_karton', $pengemasanKarton->identitas_produk_pada_karton) == '✘' ? 'selected' : '' }}>✘</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="standar_jumlah_karton" class="font-weight-bold">Standar Jumlah Karton (pcs) <span class="text-danger">*</span></label>
                                    <input type="number" name="standar_jumlah_karton" id="standar_jumlah_karton"
                                            class="form-control @error('standar_jumlah_karton') is-invalid @enderror"
                                            value="{{ old('standar_jumlah_karton', $pengemasanKarton->standar_jumlah_karton) }}" placeholder="Masukkan Nilai Standar Jumlah Karton" required>
                                    @error('standar_jumlah_karton')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="aktual_jumlah_karton" class="font-weight-bold">Aktual Jumlah Karton (pcs) <span class="text-danger">*</span></label>
                                    <input type="number" name="aktual_jumlah_karton" id="aktual_jumlah_karton"
                                            class="form-control @error('aktual_jumlah_karton') is-invalid @enderror"
                                            value="{{ old('aktual_jumlah_karton', $pengemasanKarton->aktual_jumlah_karton) }}" placeholder="Masukkan Nilai Aktual Jumlah Karton" required>
                                    @error('aktual_jumlah_karton')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="tanggal" class="font-weight-bold">Tanggal <span class="text-danger">*</span></label>
                                    <input type="text" name="tanggal" id="tanggal"
                                            class="form-control @error('tanggal') is-invalid @enderror"
                                            value="{{ old('tanggal', $pengemasanKarton->tanggal ? \Carbon\Carbon::parse($pengemasanKarton->tanggal)->format('d-m-Y H:i:s') : '') }}" readonly>
                                    @error('tanggal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group mt-4 mb-0">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Update Data
                                </button>
                                <a href="{{ route('pengemasan-karton.index') }}" class="btn btn-secondary ml-2">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</div>
@endsection