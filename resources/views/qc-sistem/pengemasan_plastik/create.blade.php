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
              <li class="breadcrumb-item active">Pengemasan Plastik</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- /.col -->
          <div class="col-md-12">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#dokumentasi" data-toggle="tab">Pengemasan Plastik</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="dokumentasi">
                    <div class="card card-info card-outline mb-3" id="pengemasanProdukInfoCard" style="display: none;">
                      <div class="card-header">
                        <h3 class="card-title">
                          <i class="fas fa-link"></i> Informasi Pengemasan Produk
                        </h3>
                      </div>
                      <div class="card-body">
                        <div class="alert alert-info mb-0">
                          <ul class="mb-0">
                            <li><strong>Kode Produksi:</strong> <span id="info_kode_produksi">-</span></li>
                            <li><strong>Nama Produk:</strong> <span id="info_nama_produk">-</span></li>
                            <li><strong>Berat:</strong> <span id="info_berat">-</span></li>
                            <li><strong>Shift:</strong> <span id="info_shift">-</span></li>
                            <li><strong>Tanggal:</strong> <span id="info_tanggal">-</span></li>
                          </ul>
                        </div>
                      </div>
                    </div>
                    <form action="{{ route('pengemasan-plastik.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                          @if ($errors->any())
                          <div class="alert alert-danger">
                              <ul class="mb-0">
                                  @foreach ($errors->all() as $error)
                                      <li>{{ $error }}</li>
                                  @endforeach
                              </ul>
                          </div>
                          @endif
                          <input type="hidden" name="shift_id" id="shift_id" value="{{ old('shift_id', request('shift_id')) }}">
                          <div class="form-group">
                                 

                                  @php
                                      $user = auth()->user();
                                      $roleId = $user->id_role ?? $user->role ?? 0;
                                      $isSpecialRole = ($roleId == 2 || $roleId == 3);
                                  @endphp

                                  <input type="hidden" class="form-control" 
                                        id="tanggal" name="tanggal" 
                                        value="" readonly>
                              </div>

                              <div class="form-group">
                                  <label for="jam">
                                      <i class="fas fa-clock"></i> Jam
                                  </label>
                                  <input type="time" class="form-control" id="jam" name="jam" 
                                        value="{{ old('jam', date('H:i')) }}" required>
                              </div>
                          <div class="form-group mb-2">
                                <label>Kode Produksi dan Produk</label>
                                <select name="id_pengemasan_produk" id="id_pengemasan_produk" class="form-control" required>
                                    <option value="">Pilih Produk dan Kode Produksi</option>
                                  
                                    @foreach($pengemasanProduks as $produk)
                                        @php
                                            $tanggalValue = $produk->tanggal ? $produk->tanggal->format('d-m-Y H:i:s') : '';
                                            $tanggalDisplay = $produk->tanggal ? ($isSpecialRole ? $produk->tanggal->format('d-m-Y') : $produk->tanggal->format('d-m-Y H:i:s')) : '';
                                        @endphp
                                        <option value="{{ $produk->id }}"
                                            data-tanggal-value="{{ $tanggalValue }}"
                                            data-tanggal-display="{{ $tanggalDisplay }}"
                                            data-kode-produksi="{{ $produk->kode_produksi }}"
                                            data-nama-produk="{{ $produk->produk->nama_produk ?? '-' }}"
                                            data-berat="{{ $produk->berat ?? '-' }}"
                                            data-shift-id="{{ $produk->id_shift ?? '' }}"
                                            data-shift="{{ $produk->shift->shift ?? '-' }}"
                                            {{ (string) old('id_pengemasan_produk', request('id_pengemasan_produk')) === (string) $produk->id ? 'selected' : '' }}>
                                            {{ $produk->kode_produksi }}-{{$produk->produk->nama_produk}}
                                        </option>
                                    @endforeach
                                </select>
                                  @if(count($pengemasanProduks) == 0)
                                <small class="text-danger font-weight-bold">Isi Data Pengemasan Produk terlebih dahulu</small>
                            @endif
                          </div> 

                          <input type="hidden" class="form-control" name="berat_pengemasan_produk" id="berat_pengemasan_produk" readonly>
                          {{-- <div class="form-group mb-2">
                              <label class="form-label">Berat Produk (gr) <span class="text-danger">*</span></label>
                              <select id="nilai_select_berat" class="form-control" name="berat_produk"></select>
                            
                          </div> --}}
                      
                          <div class="form-group mb-3">
                              <label for="foto_kode_produksi" class="font-weight-bold">Proses Penimbangan</label>
                              <select name="proses_penimbangan" class="form-control" required>
                                  <option value="" disabled selected hidden>Pilih Proses Penimbangan</option>
                                    <option value="mhw">MHW</option>
                                    <option value="manual">Manual</option>    
                              </select>
                          </div>
                          <div class="form-group mb-3">
                              <label for="foto_kode_produksi" class="font-weight-bold">Proses Sealing</label>
                              <select name="proses_sealing" class="form-control" required>
                                  <option value="" disabled selected hidden>Pilih Proses Sealing</option>
                                  <option value="bag-sealer">Bag Sealer</option>
                                  <option value="manual">Manual</option>
                              </select>
                          </div>
                          <div class="form-group mb-3">
                              <label for="foto_kode_produksi" class="font-weight-bold">Identitas Produk pada Plastik (tinta)</label>
                              <select name="identitas_produk" class="form-control" required>
                                  <option value="" disabled selected hidden>Pilih Identitas Produk pada Plastik (tinta)</option>
                                  <option value="✔" >✔ OK</option>
                                  <option value="✘" >✘ Tidak Sesuai Spesifikasi</option>
                              </select>
                          </div>
                          <div class="form-group mb-3">
                              <label for="qr_code" class="font-weight-bold">Nomor MD</label>
                              <input type="file" name="nomor_md" id="nomor_md" class="form-control-file" accept="image/*" capture="camera">
                          </div>
                          <div class="form-group mb-3">
                            <label for="qr_code" class="font-weight-bold">Kode Kemasan Plastik</label>
                            <input type="text" name="kode_kemasan_plastik" id="kode_kemasan_plastik" class="form-control">
                          </div>
                          <div class="form-group mb-3">
                            <label for="foto_kode_produksi" class="font-weight-bold">Kekuatan Seal</label>
                            <select name="kekuatan_seal" class="form-control" required>
                                <option value="" disabled selected hidden>Kekuatan Seal</option>
                                <option value="✔" >✔ OK</option>
                                <option value="✘" >✘ Tidak Sesuai Spesifikasi</option>
                            </select>
                          </div>  
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Data</button>
                        <a href="{{ route('pengemasan-plastik.index') }}" class="ml-2 btn btn-secondary btn-md"><i class="fas fa-arrow-left"></i> Kembali</a>
                    </form>
                  </div>
                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
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

  <script>
    (function () {
      const select = document.getElementById('id_pengemasan_produk');
      const tanggalInput = document.getElementById('tanggal');
      const card = document.getElementById('pengemasanProdukInfoCard');

      function setText(id, value) {
        const el = document.getElementById(id);
        if (el) el.textContent = value || '-';
      }

      function updateFromSelected() {
        if (!select) return;
        const opt = select.options[select.selectedIndex];
        const selectedValue = select.value;

        if (!selectedValue) {
          if (card) card.style.display = 'none';
          return;
        }

        const tanggalDisplay = opt.getAttribute('data-tanggal-display') || '';
        const shiftId = opt.getAttribute('data-shift-id') || '';

        if (tanggalInput && tanggalDisplay) {
          tanggalInput.value = tanggalDisplay;
        }

        const shiftIdInput = document.getElementById('shift_id');
        if (shiftIdInput && shiftId) {
          shiftIdInput.value = shiftId;
        }

        setText('info_kode_produksi', opt.getAttribute('data-kode-produksi'));
        setText('info_nama_produk', opt.getAttribute('data-nama-produk'));
        setText('info_berat', opt.getAttribute('data-berat'));
        setText('info_shift', opt.getAttribute('data-shift'));

        setText('info_tanggal', tanggalDisplay);

        if (card) card.style.display = 'block';
      }

      if (select) {
        select.addEventListener('change', updateFromSelected);
        updateFromSelected();
      }
    })();
  </script>
@endsection