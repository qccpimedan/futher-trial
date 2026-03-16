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
              <li class="breadcrumb-item active">Persiapan Bahan Forming</li>
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
                  <li class="nav-item"><a class="nav-link active" href="#forming" data-toggle="tab">Persiapan Bahan Forming</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="forming">
                      <form class="form-horizontal mb-4" id="main-chillroom-form" method="POST" action="{{ route('persiapan-bahan-forming.store') }}">
                        @csrf
                          <div id="form-container-chillroom">
                            <div class="chillroom-form">
                                <div class="form-group mb-2">
                                    <label>Shift</label>
                                    <select name="shift_id" class="form-control" required>
                                        <option value="">Pilih Shift</option>
                                        @foreach($shifts as $shift)
                                            <option value="{{ $shift->id }}">{{ $shift->shift }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-2">
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
                                            value="{{ old('tanggal', $displayValue) }}" readonly required>                              </div>
                                <div class="form-group mb-2">
                                    <label>Jam <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control @error('jam') is-invalid @enderror" name="jam" value="{{ old('jam', \Carbon\Carbon::now('Asia/Jakarta')->format('H:i')) }}" required>
                                    @error('jam')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group mb-2">
                                    <label>Nama Produk</label>
                                    <select name="id_produk" id="id_produk_select" class="form-control" required>
                                        <option value="">Pilih Produk</option>
                                        @foreach($jenis_produk as $produk)
                                            <option value="{{ $produk->id }}" data-status-bahan="{{ strtolower($produk->status_bahan ?? '') }}" {{ (old('id_produk', $selectedProdukId ?? null) == $produk->id) ? 'selected' : '' }}>{{ $produk->nama_produk }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-2">
                                    <label>Kode Produksi</label>
                                    <input type="text" name="kode_produksi_emulsi" class="form-control">
                                </div>
                                <div class="form-group mb-2">
                                    <label>Kode Produksi Emulsi Oil</label>
                                    <div id="kode-emulsi-oil-container">
                                        <div class="input-group mb-2 kode-emulsi-oil-item">
                                            <input type="text" name="kode_produksi_emulsi_oil[]" class="form-control" placeholder="Masukkan kode produksi emulsi oil">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-success btn-sm add-kode-emulsi-oil">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm remove-kode-emulsi-oil" style="display: none;">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-2">
                                    <label>Pilih Nomor Formula</label>
                                    <select name="id_formula" id="id_formula_select" class="form-control" required>
                                        <option value="">Pilih Nomor Formula</option>
                                        {{-- Akan diisi AJAX --}}
                                    </select>
                                </div>
                                <div class="form-group mb-2">
                                    <label>Suhu Adonan (STD) <small class="text-muted">(filter by produk)</small></label>
                                    <select name="id_suhu_adonan" id="id_suhu_adonan_select" class="form-control" required>
                                        <option value="">Pilih Suhu Adonan</option>
                                    </select>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Waktu Mulai Mixing</label>
                                        <input type="text" name="waktu_mulai_mixing" class="form-control">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Waktu Selesai Mixing</label>
                                        <input type="text" name="waktu_selesai_mixing" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group mb-2">
                                    <label>Data Bahan Forming Berdasarkan Nomor Formula</label>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="tabel-bahan-forming">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nomor Formula</th>
                                                    <th>Nama RM</th>
                                                    <th>Berat RM</th>
                                                    <th>Kode Produksi Bahan</th>
                                                    <th>Suhu</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($bahan as $b)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $b->formula->nomor_formula ?? '-' }}</td>
                                                        <td>{{ $b->nama_rm }}</td>
                                                        <td>{{ $b->berat_rm }}</td>
                                                        <td>
                                                            <input type="text" name="kode_produksi_bahan[]" value="" class="form-control" placeholder="Kode Produksi Bahan">
                                                        </td>
                                                        <td>
                                                            <input type="hidden" name="id_bahan_forming[]" value="{{ $b->id }}">
                                                            <input type="text" name="suhu[]" value="" class="form-control" placeholder="Suhu">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5 class="mt-3">Suhu Aktual</h5>
                                    </div>
                                    @for($i=1;$i<=5;$i++)
                                        <div class="form-group col-md-2">
                                            <label>Aktual {{ $i }}</label>
                                            <input type="number" step="0.1" name="aktual_suhu_{{ $i }}" class="form-control" placeholder="0.0" id="aktual_suhu_{{ $i }}">
                                        </div>
                                    @endfor
                                    <div class="form-group col-md-2">
                                        <label>Hasil Suhu Aktual</label>
                                        <input type="number" step="0.01" name="total_aktual_suhu" class="form-control" id="total_aktual_suhu" placeholder="Otomatis terisi" readonly>
                                    </div>
                                </div>
                                <div class="form-group mb-2">
                                    <label>Kondisi</label>
                                    <select name="kondisi" class="form-control">
                                        <option value="✔">✔ OK</option>
                                        <option value="✘">✘ Tidak OK</option>
                                    </select>
                                </div>
                                <div class="form-group mb-2">
                                    <label>Rework (kg)</label>
                                    <input type="text" name="rework" class="form-control">
                                </div>
                                <div class="form-group mb-2">
                                    <label>Catatan</label>
                                    <textarea name="catatan" class="form-control"></textarea>
                                </div>
                                <hr>
                            </div>
                          </div>
                        <button type="submit" class="btn btn-primary mb-2"><i class="fas fa-save mr-1"></i>Simpan Data</button>
                        <a href="{{ route('persiapan-bahan-forming.index') }}" class="btn btn-secondary btn-md ml-2">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali
                        </a>
                    </form>
                  </div>
                </div><!-- /.tab-content -->
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
document.addEventListener('DOMContentLoaded', function () {
    let isRedirecting = false;

    function hitungTotalSuhuAktual() {
        let total = 0;
        let count = 0;
        for (let i = 1; i <= 5; i++) {
            let input = document.getElementById('aktual_suhu_' + i);
            if (input && input.value !== '') {
                let val = parseFloat(input.value);
                if (!isNaN(val)) {  
                    total += val;
                    count++;
                }
            }
        }
        let avg = count > 0 ? (total / count) : 0;
        document.getElementById('total_aktual_suhu').value = avg.toFixed(2);
    }
    
    for (let i = 1; i <= 5; i++) {
        let input = document.getElementById('aktual_suhu_' + i);
        if (input) {
            input.addEventListener('input', hitungTotalSuhuAktual);
        }
    }
});
document.addEventListener('DOMContentLoaded', function() {
    let kodeEmulsiOilContainer = document.getElementById('kode-emulsi-oil-container');
    
    // Event delegation untuk tombol add dan remove
    kodeEmulsiOilContainer.addEventListener('click', function(e) {
        if (e.target.closest('.add-kode-emulsi-oil')) {
            // Clone item pertama
            let firstItem = kodeEmulsiOilContainer.querySelector('.kode-emulsi-oil-item');
            let newItem = firstItem.cloneNode(true);
            
            // Reset value input
            newItem.querySelector('input').value = '';
            
            // Pastikan baris baru tidak punya tombol tambah (hanya tombol hapus)
            let addBtn = newItem.querySelector('.add-kode-emulsi-oil');
            if (addBtn) {
                addBtn.style.display = 'none';
            }
            
            // Tambahkan ke container
            kodeEmulsiOilContainer.appendChild(newItem);
            
            // Update visibility tombol remove
            updateButtonsVisibility();
        }
        
        if (e.target.closest('.remove-kode-emulsi-oil')) {
            let items = kodeEmulsiOilContainer.querySelectorAll('.kode-emulsi-oil-item');
            if (items.length > 1) {
                e.target.closest('.kode-emulsi-oil-item').remove();
                updateButtonsVisibility();
            }
        }
    });
    
    function updateButtonsVisibility() {
        let items = kodeEmulsiOilContainer.querySelectorAll('.kode-emulsi-oil-item');
        
        items.forEach(function(item, index) {
            let addBtn = item.querySelector('.add-kode-emulsi-oil');
            let removeBtn = item.querySelector('.remove-kode-emulsi-oil');

            if (addBtn) {
                addBtn.style.display = (index === 0) ? 'inline-block' : 'none';
            }

            if (removeBtn) {
                removeBtn.style.display = (items.length <= 1) ? 'none' : 'inline-block';
            }
        });
    }
    
    // Initial setup
    updateButtonsVisibility();
});

document.addEventListener('DOMContentLoaded', function() {
    const produkSelect = document.getElementById('id_produk_select');
    if (!produkSelect) return;

    const formulaSelect = document.getElementById('id_formula_select');
    const suhuSelect = document.getElementById('id_suhu_adonan_select');

    // Jika masuk dari redirect dengan query ?id_produk=..., paksa select agar tampil di UI (termasuk Select2)
    try {
        const url = new URL(window.location.href);
        const preselectProduk = url.searchParams.get('id_produk');
        if (preselectProduk && produkSelect.value !== String(preselectProduk)) {
            const hasOption = Array.from(produkSelect.options).some(o => String(o.value) === String(preselectProduk));
            if (hasOption) {
                produkSelect.value = String(preselectProduk);
                if (window.jQuery) {
                    window.jQuery(produkSelect).trigger('change');
                } else {
                    produkSelect.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }
        }
    } catch (e) {}

    async function loadFormulaFormingByProduk(idProduk) {
        if (!formulaSelect) return;
        formulaSelect.innerHTML = '<option value="">Pilih Nomor Formula</option>';
        if (!idProduk) return;

        const res = await fetch("{{ url('/qc-sistem/ajax/nomor-formula-by-produk') }}/" + encodeURIComponent(idProduk), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();

        (data || []).forEach(function(item) {
            const opt = document.createElement('option');
            opt.value = item.id;
            opt.textContent = item.nomor_formula;
            formulaSelect.appendChild(opt);
        });
    }

    async function loadSuhuAdonanByProduk(idProduk) {
        if (!suhuSelect) return;
        suhuSelect.innerHTML = '<option value="">Pilih Suhu Adonan</option>';
        if (!idProduk) return;

        const res = await fetch("{{ url('/qc-sistem/get-suhu-adonan-by-produk') }}/" + encodeURIComponent(idProduk), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();

        (data || []).forEach(function(item) {
            const opt = document.createElement('option');
            opt.value = item.id;
            opt.textContent = item.std_suhu ?? item.suhu ?? item.id;
            suhuSelect.appendChild(opt);
        });
    }

    async function produkHasNonFormingFormula(idProduk) {
        if (!idProduk) return false;
        const res = await fetch("{{ url('/qc-sistem/ajax/nomor-formula-non-forming-by-produk') }}/" + encodeURIComponent(idProduk), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        if (!res.ok) return false;
        const data = await res.json();
        return Array.isArray(data) && data.length > 0;
    }

    function redirectIfNonForming() {
        if (isRedirecting) return;
        const selectedOption = produkSelect.selectedOptions ? produkSelect.selectedOptions[0] : produkSelect.options[produkSelect.selectedIndex];
        const rawStatusBahan = (selectedOption?.getAttribute('data-status-bahan') || '').toString().trim().toLowerCase();
        const normalizedStatus = rawStatusBahan.replace(/[\s_]+/g, '-');
        const idProduk = produkSelect.value;

        const isNonForming = normalizedStatus === 'non-forming' || normalizedStatus.startsWith('non-') || normalizedStatus === 'nonforming' || rawStatusBahan.startsWith('non');

        if (idProduk && isNonForming) {
            const url = "{{ route('persiapan-bahan-non-forming.create') }}" + '?id_produk=' + encodeURIComponent(idProduk);
            const path = window.location.pathname || '';
            if (path.indexOf('/qc-sistem/persiapan-bahan-non-forming') !== -1) {
                return;
            }
            isRedirecting = true;
            window.location.href = url;
            return;
        }

        // Fallback: hanya jika status_bahan kosong/unknown, tapi master non-forming-nya ada
        if (idProduk && !rawStatusBahan) {
            produkHasNonFormingFormula(idProduk)
                .then(function(hasNonForming) {
                    if (hasNonForming) {
                        const url = "{{ route('persiapan-bahan-non-forming.create') }}" + '?id_produk=' + encodeURIComponent(idProduk);
                        const path = window.location.pathname || '';
                        if (path.indexOf('/qc-sistem/persiapan-bahan-non-forming') !== -1) {
                            return;
                        }
                        isRedirecting = true;
                        window.location.href = url;
                    }
                })
                .catch(function() {});
        }
    }

    async function handleProdukChange() {
        const idProduk = produkSelect.value;

        // Sinkronkan query string id_produk agar URL selalu sesuai pilihan produk
        try {
            const url = new URL(window.location.href);
            if (idProduk) {
                url.searchParams.set('id_produk', idProduk);
            } else {
                url.searchParams.delete('id_produk');
            }
            window.history.replaceState({}, '', url.toString());
        } catch (e) {}

        // Cek redirect terlebih dulu (non-forming). Jangan tergantung hasil AJAX forming.
        redirectIfNonForming();

        // Load data forming dengan aman (jika endpoint error / bukan JSON, tidak mematikan script)
        try {
            await Promise.all([
                loadFormulaFormingByProduk(idProduk),
                loadSuhuAdonanByProduk(idProduk),
            ]);
        } catch (e) {
            // ignore
        }
    }

    produkSelect.addEventListener('change', handleProdukChange);
    handleProdukChange();
});
</script>
@endsection