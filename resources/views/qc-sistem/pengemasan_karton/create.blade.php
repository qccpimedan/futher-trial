@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Tambah Data Pengemasan Karton</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pengemasan-karton.index') }}">Pengemasan Karton</a></li>
                        <li class="breadcrumb-item active">Tambah Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Form Tambah Data</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('pengemasan-karton.store') }}" method="POST" autocomplete="off">
                        
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

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card card-info card-outline mb-3" id="pengemasanProdukInfoCard">
                                <div class="card-header">
                                    <h3 class="card-title">Informasi Pengemasan Produk</h3>
                                </div>
                                <div class="card-body">
                                    <p><strong>Kode Produksi:</strong> <span id="info_kode_produksi_pengemasan_produk">-</span></p>
                                    <p><strong>Nama Produk:</strong> <span id="info_nama_produk_pengemasan_produk">-</span></p>
                                    <p><strong>Berat Produk:</strong> <span id="info_berat_pengemasan_produk">-</span></p>
                                    <p><strong>Tanggal:</strong> <span id="info_tanggal_pengemasan_produk">-</span></p>
                                    <p><strong>Shift:</strong> <span id="info_shift_pengemasan_produk">-</span></p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card card-info card-outline mb-3" id="pengemasanPlastikInfoCard">
                                <div class="card-header">
                                    <h3 class="card-title">Informasi Pengemasan Plastik</h3>
                                </div>
                                <div class="card-body">
                                    <p><strong>Kode Kemasan Plastik:</strong> <span id="info_kode_kemasan_plastik">-</span></p>
                                    <p><strong>Proses Penimbangan:</strong> <span id="info_proses_penimbangan">-</span></p>
                                    <p><strong>Proses Sealing:</strong> <span id="info_proses_sealing">-</span></p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card card-info card-outline mb-3" id="beratProdukPackInfoCard">
                                <div class="card-header">
                                    <h3 class="card-title">Informasi Berat Produk Pack</h3>
                                </div>
                                <div class="card-body">
                                    <p><strong>Berat Aktual 1:</strong> <span id="info_pack_berat_aktual_1">-</span></p>
                                    <p><strong>Berat Aktual 2:</strong> <span id="info_pack_berat_aktual_2">-</span></p>
                                    <p><strong>Berat Aktual 3:</strong> <span id="info_pack_berat_aktual_3">-</span></p>
                                    <p><strong>Rata-rata:</strong> <span id="info_pack_rata_rata">-</span></p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card card-info card-outline mb-3" id="beratProdukBoxInfoCard">
                                <div class="card-header">
                                    <h3 class="card-title">Informasi Berat Produk Box</h3>
                                </div>
                                <div class="card-body">
                                    <p><strong>Berat Aktual 1:</strong> <span id="info_box_berat_aktual_1">-</span></p>
                                    <p><strong>Berat Aktual 2:</strong> <span id="info_box_berat_aktual_2">-</span></p>
                                    <p><strong>Berat Aktual 3:</strong> <span id="info_box_berat_aktual_3">-</span></p>
                                    <p><strong>Rata-rata:</strong> <span id="info_box_rata_rata">-</span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="shift_id" id="shift_id" class="form-control @error('shift_id') is-invalid @enderror" value="{{ old('shift_id', $prefill['shift_id'] ?? request('shift_id')) }}">

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="id_produk" class="font-weight-bold">Nama Produk <span class="text-danger">*</span></label>
                               
                                  <select name="id_berat_produk_box" id="id_produk_berat_produk_box" class="form-control" required>
                                            <option value="">Pilih Produk</option>
                                            @foreach ($pengemasanKartons as $produk)
                                                <option value="{{ $produk->id }}"
                                                    data-berat-produk-pack-id="{{ optional($produk->beratProdukPack)->id ?? '' }}"
                                                    data-pengemasan-plastik-id="{{ optional($produk->pengemasanPlastik)->id ?? '' }}"
                                                    data-pengemasan-produk-id="{{ optional($produk->pengemasanProduk)->id ?? '' }}"
                                                    data-shift-id="{{ optional($produk->pengemasanProduk)->id_shift ?? '' }}"
                                                    data-shift="{{ optional(optional($produk->pengemasanProduk)->shift)->shift ?? '' }}"
                                                    data-kode-produksi="{{ optional($produk->pengemasanProduk)->kode_produksi ?? '' }}"
                                                    data-nama-produk="{{ optional(optional($produk->pengemasanProduk)->produk)->nama_produk ?? '' }}"
                                                    data-berat-produk="{{ optional($produk->pengemasanProduk)->berat ?? '' }}"
                                                    data-tanggal="{{ optional(optional($produk->pengemasanProduk)->tanggal)->format('d-m-Y') ?? '' }}"
                                                    data-kode-kemasan-plastik="{{ optional($produk->pengemasanPlastik)->kode_kemasan_plastik ?? '' }}"
                                                    data-proses-penimbangan="{{ optional($produk->pengemasanPlastik)->proses_penimbangan ?? '' }}"
                                                    data-proses-sealing="{{ optional($produk->pengemasanPlastik)->proses_sealing ?? '' }}"
                                                    data-pack-berat-aktual-1="{{ optional($produk->beratProdukPack)->berat_aktual_1 ?? '' }}"
                                                    data-pack-berat-aktual-2="{{ optional($produk->beratProdukPack)->berat_aktual_2 ?? '' }}"
                                                    data-pack-berat-aktual-3="{{ optional($produk->beratProdukPack)->berat_aktual_3 ?? '' }}"
                                                    data-pack-rata-rata="{{ optional($produk->beratProdukPack)->rata_rata_berat ?? '' }}"
                                                    data-box-berat-aktual-1="{{ $produk->berat_aktual_1 ?? '' }}"
                                                    data-box-berat-aktual-2="{{ $produk->berat_aktual_2 ?? '' }}"
                                                    data-box-berat-aktual-3="{{ $produk->berat_aktual_3 ?? '' }}"
                                                    data-box-rata-rata="{{ $produk->rata_rata_berat ?? '' }}"
                                                    {{ (string) old('id_berat_produk_box', $prefill['id_berat_produk_box'] ?? request('id_berat_produk_box')) === (string) $produk->id ? 'selected' : '' }}>
                                                    {{ $produk->pengemasanProduk->kode_produksi ?? 'data kosong' }} - {{ $produk->pengemasanProduk->produk->nama_produk ?? 'data kosong' }}   {{ $produk->pengemasanProduk->berat ?? 'data kosong' }} gram
                                                </option>
                                            @endforeach
                                        </select>

                                        
                                              @if(count($pengemasanKartons) == 0)
             <small class="text-danger font-weight-bold">Isi Data Berat Produk Box terlebih dahulu</small>
           @endif
                                     <input type="hidden" class="form-control" name="id_berat_produk_bag" id="id_berat_produk_bag" value="{{ old('id_berat_produk_bag', $prefill['id_berat_produk_bag'] ?? '') }}" readonly>
                                     <input type="hidden" class="form-control" name="id_pengemasan_plastik" id="id_pengemasan_plastik" value="{{ old('id_pengemasan_plastik', $prefill['id_pengemasan_plastik'] ?? '') }}" readonly>

                                      <input type="hidden" class="form-control" name="id_pengemasan_produk" id="id_pengemasan_produk" value="{{ old('id_pengemasan_produk', $prefill['id_pengemasan_produk'] ?? '') }}" readonly>    

                               
                            </div>
                        </div>
                    
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="kode_produksi_rm" class="font-weight-bold">Identitas Produk Pada Karton (Tinta) <span class="text-danger">*</span></label>
                             <select class="form-control" name="identitas_produk_pada_karton" id="identitas_produk_pada_karton">
                                    <option value="✔" {{ old('identitas_produk_pada_karton') == '✔' ? 'selected' : '' }}>✔</option>
                                    <option value="✘" {{ old('identitas_produk_pada_karton') == '✘' ? 'selected' : '' }}>✘</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="standar_jumlah_karton" class="font-weight-bold">Standar Jumlah Bag/Karton <span class="text-danger">*</span></label>
                                <input type="number" name="standar_jumlah_karton" id="standar_jumlah_karton"
                                    class="form-control @error('standar_jumlah_karton') is-invalid @enderror"
                                    value="{{ old('standar_jumlah_karton') }}" placeholder="Masukkan Nilai Standar Jumlah Karton" required>
                                @error('standar_jumlah_karton')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="aktual_jumlah_karton" class="font-weight-bold">Aktual Jumlah Bag/Karton <span class="text-danger">*</span></label>
                                <input type="number" name="aktual_jumlah_karton" id="aktual_jumlah_karton"
                                    class="form-control @error('aktual_jumlah_karton') is-invalid @enderror"
                                    value="{{ old('aktual_jumlah_karton') }}" placeholder="Masukkan Nilai Aktual Jumlah Karton" required>
                                @error('aktual_jumlah_karton')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <!-- <label for="tanggal" class="font-weight-bold">
                                    <i class="fas fa-calendar-alt"></i> Tanggal <span class="text-danger">*</span>
                                </label> -->

                                @php
                                    $user = auth()->user();
                                    $roleId = $user->id_role ?? $user->role ?? 0;
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
                            
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="jam" class="font-weight-bold">
                                    <i class="fas fa-clock"></i> Jam <span class="text-danger">*</span>
                                </label>
                                <input type="time" class="form-control" id="jam" name="jam" 
                                    value="{{ old('jam', date('H:i')) }}" required>
                            </div>
                        </div>
                    
                        <div class="form-group mt-4 mb-0">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                            <a href="{{ route('pengemasan-karton.index') }}" class="ml-2 btn btn-secondary px-4">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </section>
</div>

<script>
    (function () {
        const produkCard = document.getElementById('pengemasanProdukInfoCard');
        const plastikCard = document.getElementById('pengemasanPlastikInfoCard');
        const packCard = document.getElementById('beratProdukPackInfoCard');
        const boxCard = document.getElementById('beratProdukBoxInfoCard');

        function setText(id, value) {
            const el = document.getElementById(id);
            if (el) el.textContent = value || '-';
        }

        function updateFromSelect(selectEl) {
            if (!selectEl) return;
            const opt = selectEl.querySelector('option:checked') || selectEl.options[selectEl.selectedIndex];

            if (!opt || !selectEl.value) {
                if (produkCard) produkCard.style.display = 'none';
                if (plastikCard) plastikCard.style.display = 'none';
                if (packCard) packCard.style.display = 'none';
                if (boxCard) boxCard.style.display = 'none';
                return;
            }

            setText('info_kode_produksi_pengemasan_produk', opt.getAttribute('data-kode-produksi'));
            setText('info_nama_produk_pengemasan_produk', opt.getAttribute('data-nama-produk'));
            setText('info_berat_pengemasan_produk', opt.getAttribute('data-berat-produk'));
            setText('info_tanggal_pengemasan_produk', opt.getAttribute('data-tanggal'));
            setText('info_shift_pengemasan_produk', opt.getAttribute('data-shift'));

            setText('info_kode_kemasan_plastik', opt.getAttribute('data-kode-kemasan-plastik'));
            setText('info_proses_penimbangan', opt.getAttribute('data-proses-penimbangan'));
            setText('info_proses_sealing', opt.getAttribute('data-proses-sealing'));

            setText('info_pack_berat_aktual_1', opt.getAttribute('data-pack-berat-aktual-1'));
            setText('info_pack_berat_aktual_2', opt.getAttribute('data-pack-berat-aktual-2'));
            setText('info_pack_berat_aktual_3', opt.getAttribute('data-pack-berat-aktual-3'));
            setText('info_pack_rata_rata', opt.getAttribute('data-pack-rata-rata'));

            setText('info_box_berat_aktual_1', opt.getAttribute('data-box-berat-aktual-1'));
            setText('info_box_berat_aktual_2', opt.getAttribute('data-box-berat-aktual-2'));
            setText('info_box_berat_aktual_3', opt.getAttribute('data-box-berat-aktual-3'));
            setText('info_box_rata_rata', opt.getAttribute('data-box-rata-rata'));

            const shiftId = opt.getAttribute('data-shift-id') || '';
            const shiftEl = document.getElementById('shift_id');
            if (shiftEl && shiftId) shiftEl.value = shiftId;

            const packIdEl = document.getElementById('id_berat_produk_bag');
            const plastikIdEl = document.getElementById('id_pengemasan_plastik');
            const produkIdEl = document.getElementById('id_pengemasan_produk');
            if (packIdEl) packIdEl.value = opt.getAttribute('data-berat-produk-pack-id') || '';
            if (plastikIdEl) plastikIdEl.value = opt.getAttribute('data-pengemasan-plastik-id') || '';
            if (produkIdEl) produkIdEl.value = opt.getAttribute('data-pengemasan-produk-id') || '';

            if (produkCard) produkCard.style.display = 'block';
            if (plastikCard) plastikCard.style.display = 'block';
            if (packCard) packCard.style.display = 'block';
            if (boxCard) boxCard.style.display = 'block';
        }

        function init() {
            const selectEl = document.getElementById('id_produk_berat_produk_box');
            if (!selectEl) return;
            selectEl.addEventListener('change', function () {
                updateFromSelect(selectEl);
            });

            if (window.jQuery && window.jQuery.fn && window.jQuery.fn.select2) {
                window.jQuery(selectEl).on('select2:select', function () {
                    updateFromSelect(selectEl);
                });
                window.jQuery(selectEl).on('select2:clear', function () {
                    updateFromSelect(selectEl);
                });
            }

            updateFromSelect(selectEl);
            setTimeout(function () {
                updateFromSelect(selectEl);
            }, 200);

            let tries = 0;
            const maxTries = 10;
            const poll = setInterval(function () {
                tries++;
                updateFromSelect(selectEl);
                if (selectEl.value || tries >= maxTries) {
                    clearInterval(poll);
                }
            }, 200);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    })();
</script>
@endsection