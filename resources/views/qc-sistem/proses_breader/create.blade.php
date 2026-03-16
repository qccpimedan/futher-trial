@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h3>Tambah Data Breadering</h3>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
              <li class="breadcrumb-item active">Breadering</li>
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
                  <li class="nav-item"><a class="nav-link active" href="#breadering" data-toggle="tab">Breadering</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="breadering">
                    
                    {{-- Display context information --}}
                    @if($batteringData || $predustData || $penggorenganData)
                        <div class="row mb-3">
                            @if($penggorenganData)
                                <div class="col-md-4">
                                    <div class="card card-info card-outline">
                                        <div class="card-header">
                                            <h3 class="card-title"><i class="fas fa-fire"></i> Data Penggorengan</h3>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Produk:</strong> {{ $penggorenganData->produk->nama_produk ?? '-' }}</p>
                                            <p><strong>Tanggal:</strong> {{ $penggorenganData->tanggal ? $penggorenganData->tanggal->format('d-m-Y H:i:s') : '-' }}</p>
                                            <p><strong>Kode Produksi:</strong> {{ $penggorenganData->kode_produksi ?? '-' }}</p>
                                            <p><strong>Waktu Mulai:</strong> {{ $penggorenganData->waktu_mulai_pemasakan ?? '-' }}</p>
                                            <p><strong>Waktu Selesai:</strong> {{ $penggorenganData->waktu_selesai_pemasakan ?? '-' }}</p>
                                            <p><strong>Suhu Minyak:</strong> {{ $penggorenganData->suhu_minyak ?? '-' }}°C</p>
                                            <p><strong>Hasil Pencetakan:</strong> 
                                                @if($penggorenganData->hasil_pencetakan == 'Baik')
                                                    <span class="badge badge-success">{{ $penggorenganData->hasil_pencetakan }}</span>
                                                @elseif($penggorenganData->hasil_pencetakan == 'Kurang Baik')
                                                    <span class="badge badge-warning">{{ $penggorenganData->hasil_pencetakan }}</span>
                                                @elseif($penggorenganData->hasil_pencetakan == 'Tidak Baik')
                                                    <span class="badge badge-danger">{{ $penggorenganData->hasil_pencetakan }}</span>
                                                @else
                                                    {{ $penggorenganData->hasil_pencetakan ?? '-' }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if($predustData)
                                <div class="col-md-4">
                                    <div class="card card-warning card-outline">
                                        <div class="card-header">
                                            <h3 class="card-title"><i class="fas fa-layer-group"></i> Data Predust</h3>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Produk:</strong> {{ $predustData->produk->nama_produk ?? '-' }}</p>
                                            <p><strong>Jenis Predust:</strong> {{ $predustData->jenisPredust->jenis_predust ?? '-' }}</p>
                                            <!-- <p><strong>Tanggal:</strong> {{ $predustData->tanggal ? $predustData->tanggal->format('d-m-Y H:i:s') : '-' }}</p> -->
                                            <p><strong>Kode Produksi:</strong> {{ $predustData->kode_produksi ?? '-' }}</p>
                                            <p><strong>Kondisi Predust:</strong> {{ $predustData->kondisi_predust ?? '-' }}</p>
                                            <p><strong>Hasil Pencetakan:</strong> 
                                                @if($predustData->hasil_pencetakan == 'oke')
                                                    <span class="badge badge-success">✔ Oke</span>
                                                @elseif($predustData->hasil_pencetakan == 'tidak ok')
                                                    <span class="badge badge-danger">✘ Tidak Ok</span>
                                                @else
                                                    {{ $predustData->hasil_pencetakan ?? '-' }}
                                                @endif
                                            </p>
                                            <!-- <p><strong>User:</strong> {{ $predustData->user->name ?? '-' }}</p> -->
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if($batteringData)
                                <div class="col-md-4">
                                    <div class="card card-success card-outline">
                                        <div class="card-header">
                                            <h3 class="card-title"><i class="fas fa-tint"></i> Data Battering</h3>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Produk:</strong> {{ $batteringData->produk->nama_produk ?? '-' }}</p>
                                            <p><strong>Jenis Better:</strong> {{ $batteringData->jenis_better->nama_better ?? '-' }}</p>
                                            <!-- <p><strong>Shift:</strong> Shift {{ $batteringData->shift->shift ?? '-' }}</p> -->
                                            <!-- <p><strong>Tanggal:</strong> {{ $batteringData->tanggal ? $batteringData->tanggal->format('d-m-Y H:i:s') : '-' }}</p> -->
                                            <p><strong>Hasil Better:</strong> 
                                                @if($batteringData->hasil_better == '✔')
                                                    <span class="badge badge-success">✔ Baik</span>
                                                @elseif($batteringData->hasil_better == '✘')
                                                    <span class="badge badge-danger">✘ Tidak Baik</span>
                                                @else
                                                    {{ $batteringData->hasil_better ?? '-' }}
                                                @endif
                                            </p>
                                            <p><strong>Kode Produksi Better:</strong> {{ $batteringData->kode_produksi_better ?? '-' }}</p>
                                            <!-- <p><strong>User:</strong> {{ $batteringData->user->name ?? '-' }}</p> -->
                                            <!-- @if($batteringData->predust_uuid)
                                                <p><strong>Dari Predust:</strong> 
                                                    <span class="badge badge-info">
                                                        <i class="fas fa-link"></i> Terhubung
                                                    </span>
                                                </p>
                                            @endif
                                            @if($batteringData->penggorengan_uuid)
                                                <p><strong>Dari Penggorengan:</strong> 
                                                    <span class="badge badge-info">
                                                        <i class="fas fa-link"></i> Terhubung
                                                    </span>
                                                </p>
                                            @endif -->
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                    <form action="{{ route('proses-breader.store') }}" method="POST">
                      @csrf
                      @if($batteringData)
                          <input type="hidden" name="battering_uuid" value="{{ $batteringData->uuid }}">
                      @endif
                      @if($predustData)
                          <input type="hidden" name="predust_uuid" value="{{ $predustData->uuid }}">
                      @endif
                      @if($penggorenganData)
                          <input type="hidden" name="penggorengan_uuid" value="{{ $penggorenganData->uuid }}">
                      @endif
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
                                        value="{{ old('tanggal', $displayValue) }}" readonly required>                          </div>
                          <div class="form-group">
                              <label>Jam <span class="text-danger">*</span></label>
                              <input type="time" class="form-control @error('jam') is-invalid @enderror" name="jam" value="{{ old('jam', \Carbon\Carbon::now('Asia/Jakarta')->format('H:i')) }}" required>
                              @error('jam')
                                  <div class="invalid-feedback">{{ $message }}</div>
                              @enderror
                          </div>
                          <div class="form-group">
                                <label>Nama Produk</label>
                                <select name="id_produk" id="id_produk_breader" class="form-control" required>
                                    <option value="">Pilih Produk</option>
                                    @foreach($produks as $produk)
                                        @php
                                            $produkSelected = old('id_produk') == $produk->id;
                                            // Auto-select product if coming from related data
                                            if (!old('id_produk')) {
                                                if ($batteringData && $batteringData->id_produk == $produk->id) {
                                                    $produkSelected = true;
                                                } elseif ($predustData && $predustData->id_produk == $produk->id) {
                                                    $produkSelected = true;
                                                } elseif ($penggorenganData && $penggorenganData->id_produk == $produk->id) {
                                                    $produkSelected = true;
                                                }
                                            }
                                        @endphp
                                        <option value="{{ $produk->id }}" {{ $produkSelected ? 'selected' : '' }}>{{ $produk->nama_produk }}</option>
                                    @endforeach
                                </select>
                            </div>
                             <div class="form-group">
                             <label>Jenis Breader</label>
                                 <select name="id_jenis_breader[]" id="id_jenis_breader_breader" multiple="multiple" class="form-control" required>
                                     @foreach($jenis_breader as $breader)
                               <option value="{{ $breader->id }}">{{ $breader->jenis_breader }}</option>
                                     @endforeach
                                 </select>
                             </div>
                            <div class="form-group">
                                <label>Kode Produksi</label>
                                <input type="text" name="kode_produksi" class="form-control" required>
                            </div>
                            <div class="form-group">
                            <label>Hasil Breader</label>
                                <select class="form-control" name="hasil_breader">
                                    <option value="✔">✔</option>
                                    <option value="✘">✘</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                            <a href="{{ route('proses-breader.index') }}" class="btn btn-secondary ml-2"><i class="fas fa-arrow-left"></i> Kembali</a>
                        </form>
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