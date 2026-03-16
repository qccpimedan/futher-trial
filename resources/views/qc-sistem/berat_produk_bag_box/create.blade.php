@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Tambah Data Berat Produk</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('berat-produk.index') }}">Berat Produk</a></li>
                        <li class="breadcrumb-item active">Tambah Data</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary card-tabs">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                        @php
                            $activeTab = request('tab');
                            if (!$activeTab && request()->filled('id_berat_produk_bag')) {
                                $activeTab = 'box';
                            }
                            $isBoxTab = $activeTab === 'box';
                        @endphp
                        <li class="nav-item">
                            <a class="nav-link {{ $isBoxTab ? '' : 'active' }}" id="custom-tabs-one-bag-tab" data-toggle="pill" href="#custom-tabs-one-bag" role="tab" aria-controls="custom-tabs-one-bag" aria-selected="{{ $isBoxTab ? 'false' : 'true' }}">Data Berat Produk (Pack)</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $isBoxTab ? 'active' : '' }}" id="custom-tabs-one-box-tab" data-toggle="pill" href="#custom-tabs-one-box" role="tab" aria-controls="custom-tabs-one-box" aria-selected="{{ $isBoxTab ? 'true' : 'false' }}">Data Berat Produk (Box)</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box bg-success">
                                <span class="info-box-icon">
                                    <i class="fas fa-check" style="font-size: 2rem;"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Tanda Centang (✓)</span>
                                    <span class="info-box-number">Sesuai Spesifikasi (printing jelas, terbaca; seal ok, tidak bocor dan tidak sobek)</span>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" style="width: 100%"></div>
                                    </div>
                                    <!-- <span class="progress-description">
                                        Kondisi baik dan memenuhi standar
                                    </span> -->
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box bg-danger">
                                <span class="info-box-icon">
                                    <i class="fas fa-times" style="font-size: 2rem;"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Tanda Silang (✗)</span>
                                    <span class="info-box-number">Tidak Sesuai Spesifikasi</span>
                                    <div class="progress">
                                        <div class="progress-bar bg-danger" style="width: 100%"></div>
                                    </div>
                                    <!-- <span class="progress-description">
                                        Kondisi tidak sesuai standar
                                    </span> -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info mt-2">
                        <i class="fas fa-lightbulb"></i>
                        <strong>Petunjuk:</strong> Pilih tanda centang (✓) jika kondisi sesuai standar, dan tanda silang (✗) jika ditemukan ketidaksesuaian yang perlu diperbaiki.
                    </div>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-one-tabContent">
                        <div class="tab-pane fade {{ $isBoxTab ? '' : 'show active' }}" id="custom-tabs-one-bag" role="tabpanel" aria-labelledby="custom-tabs-one-bag-tab">
                            <div class="card card-info card-outline mb-3" id="c1_pengemasan_produk_card" style="display: none;">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-link"></i> Informasi Pengemasan Produk
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info mb-0">
                                        <ul class="mb-0">
                                            <li><strong>Kode Produksi:</strong> <span id="c1_info_kode_produksi">-</span></li>
                                            <li><strong>Nama Produk:</strong> <span id="c1_info_nama_produk">-</span></li>
                                            <li><strong>Berat:</strong> <span id="c1_info_berat_produk">-</span></li>
                                            <li><strong>Shift:</strong> <span id="c1_info_shift">-</span></li>
                                            <li><strong>Tanggal:</strong> <span id="c1_info_tanggal">-</span></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="card card-secondary card-outline mb-3" id="c1_pengemasan_plastik_card" style="display: none;">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-box"></i> Informasi Pengemasan Plastik
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-secondary mb-0">
                                        <ul class="mb-0">
                                            <li><strong>Kode Kemasan Plastik:</strong> <span id="c1_info_kode_kemasan_plastik">-</span></li>
                                            <li><strong>Proses Penimbangan:</strong> <span id="c1_info_proses_penimbangan">-</span></li>
                                            <li><strong>Proses Sealing:</strong> <span id="c1_info_proses_sealing">-</span></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <h4>Form C1: Berat Produk Pack</h4>
                            <button type="button" class="btn btn-success mb-3" id="addFormBag">
                                <i class="fas fa-plus"></i> Tambah Form
                            </button>
                            
                            <form action="{{ route('berat-produk.store_bag') }}" method="POST">
                                @csrf
                                <input type="hidden" id="c1_validation_errors" value='@json($errors->all())'>
                                <input type="hidden" id="c1_session_error" value='@json(session('error'))'>
                            
                            <!-- Common Fields -->
                            <div class="card-body">
                              <div class="form-group">
                                    <!-- <label for="tanggal">
                                        <i class="fas fa-calendar-alt"></i> Tanggal
                                    </label> -->

                                    @php
                                        $user = auth()->user();
                                        $roleId = $user->id_role ?? $user->role ?? 0; // Mencoba beberapa kemungkinan nama field
                                    @endphp

                                    @if($roleId == 2 || $roleId == 3)
                                        <input type="hidden" class="form-control" 
                                            id="tanggal" name="tanggal" 
                                            value="{{ old('tanggal', now()->format('d-m-Y')) }}" readonly>
                                    @else
                                        <input type="hidden" class="form-control" 
                                            id="tanggal" name="tanggal" 
                                            value="{{ old('tanggal', now()->format('d-m-Y H:i:s')) }}" readonly>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="jam">
                                        <i class="fas fa-clock"></i> Jam
                                    </label>
                                    <input type="time" class="form-control" id="jam" name="jam" 
                                        value="{{ old('jam', date('H:i')) }}" required>
                                </div>
                            </div>

                            <!-- Dynamic Forms Container -->
                            <div id="dynamicFormsContainer">
                                <div class="card mb-3 dynamic-form-item" data-index="0">
                                    <div class="card-header bg-primary">
                                        <h5 class="mb-0">Data Berat Produk Pack #1</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <!-- <label for="id_shift_bag_0">Shift</label> -->
                                            @php
                                                $selectedShiftIdBag = old('id_shift.0', request('id_shift'));
                                                if (!$selectedShiftIdBag && isset($shifts) && $shifts->count() > 0) {
                                                    $selectedShiftIdBag = $shifts->first()->id;
                                                }
                                                $selectedShiftBag = $selectedShiftIdBag ? $shifts->firstWhere('id', $selectedShiftIdBag) : null;
                                                $selectedShiftTextBag = $selectedShiftBag ? ('Shift ' . $selectedShiftBag->shift) : '';
                                            @endphp
                                            <input type="hidden" class="form-control" id="shift_text_bag_0" value="{{ $selectedShiftTextBag }}" readonly>
                                            <input type="hidden" name="id_shift[]" id="id_shift_bag_0" value="{{ $selectedShiftIdBag }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="line_bag_0">Line</label>
                                            <select name="line[]" id="line_bag_0" class="form-control" required>
                                                <option>Pilih Line</option>
                                                @for ($i = 1; $i <= 8; $i++)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="id_produk_bag_0">Produk</label>
                                            <select name="id_pengemasan_plastik[]" id="id_produk_bag" class="form-control produk-select" required>
                                                <option>Pilih Produk</option>
                                                @foreach ($pengemasanPlastiks as $newProduk)
                                                   <option value="{{ $newProduk['id'] }}"
                                                       data-id="{{ $newProduk['pengemasanProduk']['id'] ?? '' }}"
                                                       data-produk-id="{{ $newProduk['pengemasanProduk']['produk']['id'] ?? '' }}"
                                                       data-kode-produksi="{{ $newProduk['pengemasanProduk']['kode_produksi'] ?? '' }}"
                                                       data-nama-produk="{{ $newProduk['pengemasanProduk']['produk']['nama_produk'] ?? '' }}"
                                                       data-berat-produk="{{ $newProduk['pengemasanProduk']['berat'] ?? '' }}"
                                                       data-shift="{{ $newProduk['shift']['shift'] ?? '' }}"
                                                       data-tanggal="{{ isset($newProduk['tanggal']) ? $newProduk['tanggal'] : '' }}"
                                                       data-kode-kemasan-plastik="{{ $newProduk['kode_kemasan_plastik'] ?? '' }}"
                                                       data-proses-penimbangan="{{ $newProduk['proses_penimbangan'] ?? '' }}"
                                                       data-proses-sealing="{{ $newProduk['proses_sealing'] ?? '' }}"
                                                       {{ (string) old('id_pengemasan_plastik.0', request('id_pengemasan_plastik')) === (string) $newProduk['id'] ? 'selected' : '' }}>
                                                {{ $newProduk['pengemasanProduk']['kode_produksi'] ?? 'data kosong' }} - {{ $newProduk['pengemasanProduk']['produk']['nama_produk'] ?? 'nama produk kosong' }} {{ $newProduk['berat'] ?? 'nama produk kosong' }} gram
                                                </option>
                                                @endforeach
                                            </select>
                                            @if(count($pengemasanPlastiks) == 0)
                                            <small class="text-danger font-weight-bold">Isi Data Pengemasan Plastik terlebih dahulu</small>
                                            @endif
                                        </div>  
                                        <div class="form-group">
                                            <input type="hidden" name="id_pengemasan_produk[]" id="id_produk_value_bag" class="form-control" value="{{ old('id_pengemasan_produk.0', request('id_pengemasan_produk')) }}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="id_data_bag_0">Nilai Standar Pack</label>
                                            <select name="id_data_bag[]" id="id_data_bag" class="form-control nilai-standar-select" required>
                                                <option>Pilih Nilai Standar Pack</option>
                                                <!-- Options will be loaded by AJAX -->
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="berat_aktual_1_0">Berat Aktual 1</label>
                                            <input type="number" step="0.01" name="berat_aktual_1[]" class="form-control berat-aktual-1" id="berat_aktual_1_0" placeholder="Masukkan Berat" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="berat_aktual_2_0">Berat Aktual 2</label>
                                            <input type="number" step="0.01" name="berat_aktual_2[]" class="form-control berat-aktual-2" id="berat_aktual_2_0" placeholder="Masukkan Berat" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="berat_aktual_3_0">Berat Aktual 3</label>
                                            <input type="number" step="0.01" name="berat_aktual_3[]" class="form-control berat-aktual-3" id="berat_aktual_3_0" placeholder="Masukkan Berat" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="rata_rata_berat_0">Rata-rata Berat Aktual</label>
                                            <input type="number" step="0.01" name="rata_rata_berat[]" class="form-control rata-rata-berat" id="rata_rata_berat_0" placeholder="Otomatis terisi" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- /.card-body -->
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Data</button>
                                <a href="{{ route('berat-produk.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                            </div>
                            </form>
                        </div>
                        <div class="tab-pane fade {{ $isBoxTab ? 'show active' : '' }}" id="custom-tabs-one-box" role="tabpanel" aria-labelledby="custom-tabs-one-box-tab">
                            <div class="card card-info card-outline mb-3" id="c2_pengemasan_produk_card" style="display: none;">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-link"></i> Informasi Pengemasan Produk
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info mb-0">
                                        <ul class="mb-0">
                                            <li><strong>Kode Produksi:</strong> <span id="c2_info_kode_produksi">-</span></li>
                                            <li><strong>Nama Produk:</strong> <span id="c2_info_nama_produk">-</span></li>
                                            <li><strong>Berat:</strong> <span id="c2_info_berat_produk">-</span></li>
                                            <li><strong>Shift:</strong> <span id="c2_info_shift">-</span></li>
                                            <li><strong>Tanggal:</strong> <span id="c2_info_tanggal">-</span></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="card card-secondary card-outline mb-3" id="c2_pengemasan_plastik_card" style="display: none;">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-box"></i> Informasi Pengemasan Plastik
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-secondary mb-0">
                                        <ul class="mb-0">
                                            <li><strong>Kode Kemasan Plastik:</strong> <span id="c2_info_kode_kemasan_plastik">-</span></li>
                                            <li><strong>Proses Penimbangan:</strong> <span id="c2_info_proses_penimbangan">-</span></li>
                                            <li><strong>Proses Sealing:</strong> <span id="c2_info_proses_sealing">-</span></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="card card-warning card-outline mb-3" id="c2_berat_produk_pack_card" style="display: none;">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-weight"></i> Informasi Berat Produk Pack (C1)
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-warning mb-0">
                                        <ul class="mb-0">
                                            <li><strong>Line:</strong> <span id="c2_info_line">-</span></li>
                                            <li><strong>Berat Aktual 1:</strong> <span id="c2_info_b1">-</span></li>
                                            <li><strong>Berat Aktual 2:</strong> <span id="c2_info_b2">-</span></li>
                                            <li><strong>Berat Aktual 3:</strong> <span id="c2_info_b3">-</span></li>
                                            <li><strong>Rata-rata:</strong> <span id="c2_info_avg">-</span></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            Form C2: Berat Produk Box
                            <form action="{{ route('berat-produk.store_box') }}" method="POST">
                                @csrf
                                <div class="card-body">
                                   <div class="form-group">
                                        <!-- <label for="tanggal_box">
                                            <i class="fas fa-calendar-alt"></i> Tanggal
                                        </label> -->

                                        @php
                                            $user = auth()->user();
                                            $roleId = $user->id_role ?? $user->role ?? 0; // Mencoba beberapa kemungkinan nama field
                                        @endphp

                                        @if($roleId == 2 || $roleId == 3)
                                            <input type="hidden" class="form-control" 
                                                id="tanggal_box" name="tanggal" 
                                                value="{{ old('tanggal', now()->format('d-m-Y')) }}" readonly>
                                        @else
                                            <input type="hidden" class="form-control" 
                                                id="tanggal_box" name="tanggal" 
                                                value="{{ old('tanggal', now()->format('d-m-Y H:i:s')) }}" readonly>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="jam_box">
                                            <i class="fas fa-clock"></i> Jam
                                        </label>
                                        <input type="time" class="form-control" id="jam_box" name="jam" 
                                            value="{{ old('jam', date('H:i')) }}" required>
                                    </div>
                                    <div class="form-group">
                                        <!-- <label for="id_shift_box">Shift</label> -->
                                        @php
                                            $selectedShiftIdBox = old('id_shift', request('id_shift'));
                                            if (!$selectedShiftIdBox && isset($shifts) && $shifts->count() > 0) {
                                                $selectedShiftIdBox = $shifts->first()->id;
                                            }
                                            $selectedShiftBox = $selectedShiftIdBox ? $shifts->firstWhere('id', $selectedShiftIdBox) : null;
                                            $selectedShiftTextBox = $selectedShiftBox ? ('Shift ' . $selectedShiftBox->shift) : '';
                                        @endphp
                                        <input type="hidden" class="form-control" id="shift_text_box" value="{{ $selectedShiftTextBox }}" readonly>
                                        <input type="hidden" name="id_shift" id="id_shift_box" value="{{ $selectedShiftIdBox }}" required>
                                    </div>
                                    <div class="form-group">    
                                        <label for="id_produk_box">Produk</label>
                                        <select name="id_berat_produk_bag" id="id_produk_box" class="form-control" required>
                                            <option>Pilih Produk</option>
                                            @foreach ($beratProdukPacks as $produk)
                                                <option value="{{ $produk->id }}"
                                                    data-produk-id="{{ $produk->pengemasanProduk->produk->id ?? '' }}"
                                                    data-pengemasan-produk-id="{{ $produk->pengemasanProduk->id ?? '' }}"
                                                    data-pengemasan-plastik-id="{{ $produk->pengemasanPlastik->id ?? '' }}"
                                                    data-kode-produksi="{{ $produk->pengemasanProduk->kode_produksi ?? '' }}"
                                                    data-nama-produk="{{ $produk->pengemasanProduk->produk->nama_produk ?? '' }}"
                                                    data-berat-produk="{{ $produk->pengemasanProduk->berat ?? '' }}"
                                                    data-shift="{{ $produk->shift->shift ?? '' }}"
                                                    data-tanggal="{{ optional($produk->tanggal)->format('d-m-Y H:i:s') ?? '' }}"
                                                    data-kode-kemasan-plastik="{{ $produk->pengemasanPlastik->kode_kemasan_plastik ?? '' }}"
                                                    data-proses-penimbangan="{{ $produk->pengemasanPlastik->proses_penimbangan ?? '' }}"
                                                    data-proses-sealing="{{ $produk->pengemasanPlastik->proses_sealing ?? '' }}"
                                                    data-line="{{ $produk->line ?? '' }}"
                                                    data-b1="{{ $produk->berat_aktual_1 ?? '' }}"
                                                    data-b2="{{ $produk->berat_aktual_2 ?? '' }}"
                                                    data-b3="{{ $produk->berat_aktual_3 ?? '' }}"
                                                    data-avg="{{ $produk->rata_rata_berat ?? '' }}"
                                                    {{ (string) old('id_berat_produk_bag', request('id_berat_produk_bag')) === (string) $produk->id ? 'selected' : '' }}>
                                                    {{ $produk->pengemasanProduk->kode_produksi ?? 'data kosong' }} - {{ $produk->pengemasanProduk->produk->nama_produk ?? 'data kosong' }}   {{ $produk->pengemasanProduk->berat ?? 'data kosong' }} gram
                                                </option>
                                            @endforeach
                                        </select>
                                        @if(count($beratProdukPacks) == 0)
                                        <small class="text-danger font-weight-bold">Isi Data Berat Produk Pack terlebih dahulu</small>
                                        @endif
                                    </div>
                                    <input type="hidden" class="form-control" name="id_pengemasan_plastik" id="id_pengemasan_plastik" readonly>
                                    <input type="hidden" class="form-control" name="id_pengemasan_produk" id="id_pengemasan_produk" readonly>
                                    <div class="form-group">
                                        <label for="id_data_box">Data Box</label>
                                        <select name="id_data_box" id="id_data_box" class="form-control" required>
                                            <option>Pilih Data Box</option>
                                            <!-- Options will be loaded by AJAX -->
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="berat_aktual_1_box">Berat Aktual 1</label>
                                        <input type="number" step="0.01" name="berat_aktual_1" class="form-control" id="berat_aktual_1_box" placeholder="Masukkan Berat" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="berat_aktual_2_box">Berat Aktual 2</label>
                                        <input type="number" step="0.01" name="berat_aktual_2" class="form-control" id="berat_aktual_2_box" placeholder="Masukkan Berat" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="berat_aktual_3_box">Berat Aktual 3</label>
                                        <input type="number" step="0.01" name="berat_aktual_3" class="form-control" id="berat_aktual_3_box" placeholder="Masukkan Berat" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="rata_rata_berat_box">Rata-rata Berat Aktual</label>
                                        <input type="number" step="0.01" name="rata_rata_berat" class="form-control" id="rata_rata_berat_box" placeholder="Otomatis terisi" readonly>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Data</button>
                                    <a href="{{ route('berat-produk.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>
    </section>
</div>
<script>
    (function () {
        try {
            var errEl = document.getElementById('c1_validation_errors');
            var sessionErrEl = document.getElementById('c1_session_error');
            var validationErrors = [];

            if (errEl && errEl.value) {
                validationErrors = JSON.parse(errEl.value) || [];
            }

            var sessionError = '';
            if (sessionErrEl && sessionErrEl.value) {
                sessionError = JSON.parse(sessionErrEl.value) || '';
            }

            var messages = [];
            if (sessionError) messages.push(sessionError);
            if (Array.isArray(validationErrors) && validationErrors.length) {
                messages = messages.concat(validationErrors);
            }

            var msg = messages.filter(Boolean).join("\n").trim();
            if (!msg) return;

            setTimeout(function () {
                alert(msg);
            }, 50);
        } catch (e) {
            // ignore
        }
    })();

    (function () {
        const produkCard = document.getElementById('c1_pengemasan_produk_card');
        const plastikCard = document.getElementById('c1_pengemasan_plastik_card');

        function setText(id, value) {
            const el = document.getElementById(id);
            if (el) el.textContent = value || '-';
        }

        function updateInfoFromSelect(selectEl) {
            if (!selectEl) return;
            const opt = selectEl.options[selectEl.selectedIndex];
            if (!opt || !selectEl.value) {
                if (produkCard) produkCard.style.display = 'none';
                if (plastikCard) plastikCard.style.display = 'none';
                return;
            }

            setText('c1_info_kode_produksi', opt.getAttribute('data-kode-produksi'));
            setText('c1_info_nama_produk', opt.getAttribute('data-nama-produk'));
            setText('c1_info_berat_produk', opt.getAttribute('data-berat-produk'));
            setText('c1_info_shift', opt.getAttribute('data-shift'));
            setText('c1_info_tanggal', opt.getAttribute('data-tanggal'));

            setText('c1_info_kode_kemasan_plastik', opt.getAttribute('data-kode-kemasan-plastik'));
            setText('c1_info_proses_penimbangan', opt.getAttribute('data-proses-penimbangan'));
            setText('c1_info_proses_sealing', opt.getAttribute('data-proses-sealing'));

            if (produkCard) produkCard.style.display = 'block';
            if (plastikCard) plastikCard.style.display = 'block';
        }

        document.addEventListener('change', function (e) {
            if (e.target && e.target.classList && e.target.classList.contains('produk-select')) {
                updateInfoFromSelect(e.target);
            }
        });

        const firstSelect = document.querySelector('#custom-tabs-one-bag .produk-select');
        if (firstSelect) {
            updateInfoFromSelect(firstSelect);
        }
    })();

    (function () {
        const produkCard = document.getElementById('c2_pengemasan_produk_card');
        const plastikCard = document.getElementById('c2_pengemasan_plastik_card');
        const packCard = document.getElementById('c2_berat_produk_pack_card');

        function setText(id, value) {
            const el = document.getElementById(id);
            if (el) el.textContent = value || '-';
        }

        function updateFromC2Select(selectEl) {
            if (!selectEl) return;
            const opt = selectEl.options[selectEl.selectedIndex];

            if (!opt || !selectEl.value) {
                if (produkCard) produkCard.style.display = 'none';
                if (plastikCard) plastikCard.style.display = 'none';
                if (packCard) packCard.style.display = 'none';
                return;
            }

            setText('c2_info_kode_produksi', opt.getAttribute('data-kode-produksi'));
            setText('c2_info_nama_produk', opt.getAttribute('data-nama-produk'));
            setText('c2_info_berat_produk', opt.getAttribute('data-berat-produk'));
            setText('c2_info_shift', opt.getAttribute('data-shift'));
            setText('c2_info_tanggal', opt.getAttribute('data-tanggal'));

            setText('c2_info_kode_kemasan_plastik', opt.getAttribute('data-kode-kemasan-plastik'));
            setText('c2_info_proses_penimbangan', opt.getAttribute('data-proses-penimbangan'));
            setText('c2_info_proses_sealing', opt.getAttribute('data-proses-sealing'));

            setText('c2_info_line', opt.getAttribute('data-line'));
            setText('c2_info_b1', opt.getAttribute('data-b1'));
            setText('c2_info_b2', opt.getAttribute('data-b2'));
            setText('c2_info_b3', opt.getAttribute('data-b3'));
            setText('c2_info_avg', opt.getAttribute('data-avg'));

            if (produkCard) produkCard.style.display = 'block';
            if (plastikCard) plastikCard.style.display = 'block';
            if (packCard) packCard.style.display = 'block';
        }

        const c2Select = document.getElementById('id_produk_box');
        if (c2Select) {
            c2Select.addEventListener('change', function () {
                updateFromC2Select(c2Select);
            });
            updateFromC2Select(c2Select);
        }
    })();

    let formIndex = 1;

    function getFixedShiftBag() {
        const idEl = document.getElementById('id_shift_bag_0');
        const textEl = document.getElementById('shift_text_bag_0');
        return {
            id: idEl ? idEl.value : '',
            text: textEl ? textEl.value : ''
        };
    }

    // Add new dynamic form
    document.getElementById('addFormBag').addEventListener('click', function() {
        const container = document.getElementById('dynamicFormsContainer');
        
        // Get options from first form selects to copy to new form
        const firstDataBagSelect = document.getElementById('id_data_bag');
        let dataBagOptions = '';
       
        for (let i = 0; i < firstDataBagSelect.options.length; i++) {
            dataBagOptions += `<option value="${firstDataBagSelect.options[i].value}">${firstDataBagSelect.options[i].text}</option>`;
        }
       
        // Get line options
        const firstLineSelect = document.getElementById('line_bag_0');
        let lineOptions = '';
        for (let i = 0; i < firstLineSelect.options.length; i++) {
            lineOptions += `<option value="${firstLineSelect.options[i].value}">${firstLineSelect.options[i].text}</option>`;
        }

        // Get produk options
        const firstProdukSelect = document.getElementById('id_produk_bag');
        let produkOptions = '';
        for (let i = 0; i < firstProdukSelect.options.length; i++) {
            const opt = firstProdukSelect.options[i];
            produkOptions += `<option value="${opt.value}"
                data-id="${opt.getAttribute('data-id') || ''}"
                data-produk-id="${opt.getAttribute('data-produk-id') || ''}"
                data-kode-produksi="${opt.getAttribute('data-kode-produksi') || ''}"
                data-nama-produk="${opt.getAttribute('data-nama-produk') || ''}"
                data-berat-produk="${opt.getAttribute('data-berat-produk') || ''}"
                data-shift="${opt.getAttribute('data-shift') || ''}"
                data-tanggal="${opt.getAttribute('data-tanggal') || ''}"
                data-kode-kemasan-plastik="${opt.getAttribute('data-kode-kemasan-plastik') || ''}"
                data-proses-penimbangan="${opt.getAttribute('data-proses-penimbangan') || ''}"
                data-proses-sealing="${opt.getAttribute('data-proses-sealing') || ''}"
            >${opt.text}</option>`;
        }
        
        const fixedShift = getFixedShiftBag();

        const newForm = document.createElement('div');
        newForm.className = 'card mb-3 dynamic-form-item';
        newForm.setAttribute('data-index', formIndex);
        newForm.innerHTML = `
            <div class="card-header bg-primary d-flex align-items-center">
                <h5 class="mb-0">Data Berat Produk Pack #${formIndex + 1}</h5>
                <button type="button" class="btn btn-danger btn-sm remove-form-btn ml-auto">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="id_shift_bag_${formIndex}" style="display: none;">Shift</label>
                    <input type="hidden" class="form-control" id="shift_text_bag_${formIndex}" value="${fixedShift.text}" readonly>
                    <input type="hidden" name="id_shift[]" id="id_shift_bag_${formIndex}" value="${fixedShift.id}" required>
                </div>
                <div class="form-group">
                    <label for="line_bag_${formIndex}">Line</label>
                    <select name="line[]" id="line_bag_${formIndex}" class="form-control" required>
                        ${lineOptions}
                    </select>
                </div>
                <div class="form-group">
                    <label for="id_produk_bag_${formIndex}">Produk</label>
                    <select name="id_pengemasan_plastik[]" id="id_produk_bag" class="form-control produk-select" required>
                        ${produkOptions}
                    </select>
                </div>
                <div class="form-group">
                    <input type="hidden" name="id_pengemasan_produk[]" id="id_produk_value_bag" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label for="id_data_bag_${formIndex}">Nilai Standar Pack</label>
                    <select name="id_data_bag[]" id="id_data_bag" class="form-control nilai-standar-select" required>
                        ${dataBagOptions}
                    </select>
                </div>
                <div class="form-group">
                    <label for="berat_aktual_1_${formIndex}">Berat Aktual 1</label>
                    <input type="number" step="0.01" name="berat_aktual_1[]" class="form-control berat-aktual-1" id="berat_aktual_1_${formIndex}" placeholder="Masukkan Berat" required>
                </div>
                <div class="form-group">
                    <label for="berat_aktual_2_${formIndex}">Berat Aktual 2</label>
                    <input type="number" step="0.01" name="berat_aktual_2[]" class="form-control berat-aktual-2" id="berat_aktual_2_${formIndex}" placeholder="Masukkan Berat" required>
                </div>
                <div class="form-group">
                    <label for="berat_aktual_3_${formIndex}">Berat Aktual 3</label>
                    <input type="number" step="0.01" name="berat_aktual_3[]" class="form-control berat-aktual-3" id="berat_aktual_3_${formIndex}" placeholder="Masukkan Berat" required>
                </div>
                <div class="form-group">
                    <label for="rata_rata_berat_${formIndex}">Rata-rata Berat Aktual</label>
                    <input type="number" step="0.01" name="rata_rata_berat[]" class="form-control rata-rata-berat" id="rata_rata_berat_${formIndex}" placeholder="Otomatis terisi" readonly>
                </div>
            </div>
        `;
        container.appendChild(newForm);
        formIndex++;
        
        // Re-attach event listeners
        attachCalculationListeners();
    });

    // Remove form
    document.addEventListener('click', function(e) {
        if (e.target && e.target.closest('.remove-form-btn')) {
            const forms = document.querySelectorAll('.dynamic-form-item');
            if (forms.length > 1) {
                e.target.closest('.dynamic-form-item').remove();
                updateFormNumbers();
            } else {
                alert('Minimal harus ada 1 form!');
            }
        }
    });

    // Update form numbers after deletion
    function updateFormNumbers() {
        const forms = document.querySelectorAll('.dynamic-form-item');
        forms.forEach((form, index) => {
            const header = form.querySelector('.card-header h5');
            if (header) {
                header.textContent = `Data Berat Produk Pack #${index + 1}`;
            }
        });
    }

    // Attach calculation listeners to all forms
    function attachCalculationListeners() {
        document.querySelectorAll('.dynamic-form-item').forEach(function(formItem) {
            const b1 = formItem.querySelector('.berat-aktual-1');
            const b2 = formItem.querySelector('.berat-aktual-2');
            const b3 = formItem.querySelector('.berat-aktual-3');
            const avg = formItem.querySelector('.rata-rata-berat');

            function calculateAverage() {
                const val1 = parseFloat(b1.value) || 0;
                const val2 = parseFloat(b2.value) || 0;
                const val3 = parseFloat(b3.value) || 0;
                const average = (val1 + val2 + val3) / 3;
                avg.value = average ? average.toFixed(2) : '';
            }

            b1.removeEventListener('input', calculateAverage);
            b2.removeEventListener('input', calculateAverage);
            b3.removeEventListener('input', calculateAverage);

            b1.addEventListener('input', calculateAverage);
            b2.addEventListener('input', calculateAverage);
            b3.addEventListener('input', calculateAverage);
        });
    }

    // Initialize calculation listeners on page load
    attachCalculationListeners();

    // Box form calculations (unchanged)
    function hitungRataRataBox() {
        let b1 = parseFloat(document.getElementById('berat_aktual_1_box').value) || 0;
        let b2 = parseFloat(document.getElementById('berat_aktual_2_box').value) || 0;
        let b3 = parseFloat(document.getElementById('berat_aktual_3_box').value) || 0;
        let avg = (b1 + b2 + b3) / 3;
        document.getElementById('rata_rata_berat_box').value = avg ? avg.toFixed(2) : '';
    }
    document.getElementById('berat_aktual_1_box').addEventListener('input', hitungRataRataBox);
    document.getElementById('berat_aktual_2_box').addEventListener('input', hitungRataRataBox);
    document.getElementById('berat_aktual_3_box').addEventListener('input', hitungRataRataBox);

    $(document).ready(function() {
        const selected = $('#id_produk_box');
        if (selected.length && selected.val()) {
            selected.trigger('change');
        }
    });
</script>
@endsection