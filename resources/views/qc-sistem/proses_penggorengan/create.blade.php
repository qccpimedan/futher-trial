@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
              <li class="breadcrumb-item active">Penggorengan</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- /.col -->
          <div class="col-md-12">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#penggorengan" data-toggle="tab">Penggorengan </a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                    <div class="active tab-pane" id="penggorengan">
                        <form method="POST" action="{{ route('penggorengan.store') }}">
                            @csrf
                            <div class="form-group">
                              <label>Shift</label>
                                <select name="shift_id" class="form-control" required>
                                  <option value="">Pilih Shift</option>
                                  @foreach($shifts as $shift)
                                  <option value="{{ $shift->id }}">{{ $shift->shift }}</option>
                                  @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Tanggal</label>
                                @php
                                    $userRole = auth()->user()->id_role ?? null;
                                    $showTime = in_array($userRole, [1, 2, 5]); // superadmin, admin, spv
                                    $displayFormat = $showTime ? 'd-m-Y H:i:s' : 'd-m-Y';
                                    $submitFormat = 'd-m-Y H:i:s'; // Always submit with H:i:s
                                    $now = \Carbon\Carbon::now('Asia/Jakarta');
                                    $displayValue = $now->format($displayFormat);
                                    $submitValue = $now->format($submitFormat);
                                @endphp
                                <input type="hidden" name="tanggal" id="tanggal_hidden" 
                                        value="{{ old('tanggal', $submitValue) }}">
                                <input type="text" class="form-control" id="tanggal_display" 
                                        value="{{ old('tanggal', $displayValue) }}" readonly required>
                            </div>
                            <div class="form-group">
                                <label>Jam <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('jam') is-invalid @enderror" name="jam" value="{{ old('jam', \Carbon\Carbon::now('Asia/Jakarta')->format('H:i')) }}" required>
                                @error('jam')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                              <label>Nama Produk</label>
                                <select name="id_produk" id="id_produk_proses_penggorengan" class="form-control" required>
                                    <option value="">Pilih Produk</option>
                                    @foreach($produks as $produk)
                                        <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-2">
                                <label class="form-label">Berat Produk (gr) <span class="text-danger">*</span></label>
                                <select id="nilai_select_berat" class="form-control" name="berat_produk"></select>
                            </div>
                            <div class="form-group">
                                <label>Kode Produksi</label>
                                <input type="text" name="kode_produksi" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>No Of Strokes</label>
                                <input type="text" name="no_of_strokes" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Waktu Pemasakan</label>
                                <input type="text" name="waktu_pemasakan" class="form-control" required>
                            </div>
                            <!-- <div class="form-group">
                                <label>Waktu Selesai Pemasakan</label>
                                <input type="text" name="waktu_selesai_pemasakan" class="form-control">
                            </div> -->
                            <div class="form-group">
                                <label>Hasil Pencetakan</label>
                                <select name="hasil_pencetakan" class="form-control" required>
                                    <option value="✔">✔ OK</option>
                                    <option value="✘">✘ Tidak OK</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Data</button>
                            <a href="{{ route('penggorengan.index') }}" class="btn btn-secondary ml-2"><i class="fas fa-arrow-left"></i> Kembali</a>
                        </form>
                    </div>
                </div>
              </div><!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
@endsection