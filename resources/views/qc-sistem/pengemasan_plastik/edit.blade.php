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
          <div class="col-sm-6"></div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
              <li class="breadcrumb-item active">Edit Pengemasan Plastik</li>
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
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#dokumentasi" data-toggle="tab">Edit Pengemasan Plastik</a></li>
                </ul>
              </div>
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="dokumentasi">
                    <form action="{{ route('pengemasan-plastik.update', $item->uuid) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="form-group mb-2">
                            <label>Kode Produksi dan Produk</label>
                          
                                                     <input type="text"
          
           class="form-control"
           
           value="{{$kode_produksi ?? '-' }}-{{$produk->nama_produk ?? '-' }} {{$berat}} gram "
           readonly>
                        </div>
                       
                             <input type="hidden" class="form-control" name="berat_produk" id="berat_pengemasan_produk_for_edit" readonly>
                
                            
                        <div class="form-group mb-2">
                            <label>Shift</label>
                            <select name="shift_id" class="form-control" required>
                                <option value="">Pilih Shift</option>
                                @foreach($shifts as $shift)
                                    <option value="{{ $shift->id }}" {{ $item->id_shift == $shift->id ? 'selected' : '' }}>{{ $shift->shift }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="tanggal" class="font-weight-bold">Tanggal</label>
                            <input type="text" name="tanggal" id="tanggal" class="form-control"
                                value="{{ old('tanggal', \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y H:i:s')) }}" readonly>
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Proses Penimbangan</label>
                            <select name="proses_penimbangan" class="form-control" required>
                                <option value="" disabled hidden>Pilih Proses Penimbangan</option>
                                <option value="mhw" {{ $item->proses_penimbangan == 'mhw' ? 'selected' : '' }}>MHW</option>
                                <option value="manual" {{ $item->proses_penimbangan == 'manual' ? 'selected' : '' }}>Manual</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Proses Sealing</label>
                            <select name="proses_sealing" class="form-control" required>
                                <option value="" disabled hidden>Pilih Proses Sealing</option>
                                <option value="bag-sealer" {{ $item->proses_sealing == 'bag-sealer' ? 'selected' : '' }}>Bag Sealer</option>
                                <option value="manual" {{ $item->proses_sealing == 'manual' ? 'selected' : '' }}>Manual</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Identitas Produk pada Plastik (tinta)</label>
                            <select name="identitas_produk" class="form-control" required>
                                <option value="" disabled hidden>Pilih Identitas Produk pada Plastik (tinta)</option>
                                <option value="✔" {{ $item->identitas_produk == '✔' ? 'selected' : '' }}>✔ OK</option>
                                <option value="✘" {{ $item->identitas_produk == '✘' ? 'selected' : '' }}>✘ Tidak Sesuai Spesifikasi</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Nomor MD</label>
                            @if($item->nomor_md)
                                <div class="mb-2">
                                    <img src="{{ asset($assetPath . 'storage/' . $item->nomor_md) }}" alt="Nomor MD" style="width:80px; height:80px; object-fit:cover; border-radius:4px;">
                                </div>
                            @endif
                            <input type="file" name="nomor_md" id="nomor_md" class="form-control-file" accept="image/*" capture="camera">
                            <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah foto.</small>
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Kode Kemasan Plastik</label>
                            <input type="text" name="kode_kemasan_plastik" id="kode_kemasan_plastik" class="form-control" value="{{ $item->kode_kemasan_plastik }}">
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Kekuatan Seal</label>
                            <select name="kekuatan_seal" class="form-control" required>
                                <option value="" disabled hidden>Kekuatan Seal</option>
                                <option value="✔" {{ $item->kekuatan_seal == '✔' ? 'selected' : '' }}>✔ OK</option>
                                <option value="✘" {{ $item->kekuatan_seal == '✘' ? 'selected' : '' }}>✘ Tidak Sesuai Spesifikasi</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Data</button>
                        <a href="{{ route('pengemasan-plastik.index') }}" class="btn btn-secondary ml-2"><i class="fas fa-arrow-left"></i> Kembali</a>
                    </form>
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
  