@extends('layouts.app')

@section('container')
<div class="content-wrapper">

    {{-- CONTENT HEADER --}}
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">

                <div class="col-sm-6"></div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="#">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">
                            Dokumentasi
                        </li>
                    </ol>
                </div>

            </div>
        </div>
    </section>
    {{-- CONTENT --}}
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        {{-- CARD HEADER --}}
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#dokumentasi" data-toggle="tab">
                                        Dokumentasi
                                    </a>
                                </li>
                            </ul>
                        </div>
                        {{-- CARD BODY --}}
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="active tab-pane" id="dokumentasi">
                                    {{-- FORM --}}
                                    <form action="{{ route('dokumentasi.store') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        {{-- ERROR VALIDATION --}}
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
                                                <div class="card card-info card-outline mb-3" id="dokumentasi_pengemasan_produk_card" style="display: none;">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Informasi Pengemasan Produk</h3>
                                                    </div>
                                                    <div class="card-body">
                                                        <p><strong>Kode Produksi:</strong> <span id="dokumentasi_info_kode_produksi">-</span></p>
                                                        <p><strong>Nama Produk:</strong> <span id="dokumentasi_info_nama_produk">-</span></p>
                                                        <p><strong>Berat Produk:</strong> <span id="dokumentasi_info_berat_produk">-</span></p>
                                                        <p><strong>Tanggal:</strong> <span id="dokumentasi_info_tanggal">-</span></p>
                                                        <p><strong>Shift:</strong> <span id="dokumentasi_info_shift">-</span></p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="card card-info card-outline mb-3" id="dokumentasi_pengemasan_plastik_card" style="display: none;">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Informasi Pengemasan Plastik</h3>
                                                    </div>
                                                    <div class="card-body">
                                                        <p><strong>Kode Kemasan Plastik:</strong> <span id="dokumentasi_info_kode_kemasan_plastik">-</span></p>
                                                        <p><strong>Proses Penimbangan:</strong> <span id="dokumentasi_info_proses_penimbangan">-</span></p>
                                                        <p><strong>Proses Sealing:</strong> <span id="dokumentasi_info_proses_sealing">-</span></p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="card card-info card-outline mb-3" id="dokumentasi_berat_produk_pack_card" style="display: none;">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Informasi Berat Produk Pack</h3>
                                                    </div>
                                                    <div class="card-body">
                                                        <p><strong>Berat Aktual 1:</strong> <span id="dokumentasi_info_pack_b1">-</span></p>
                                                        <p><strong>Berat Aktual 2:</strong> <span id="dokumentasi_info_pack_b2">-</span></p>
                                                        <p><strong>Berat Aktual 3:</strong> <span id="dokumentasi_info_pack_b3">-</span></p>
                                                        <p><strong>Rata-rata:</strong> <span id="dokumentasi_info_pack_avg">-</span></p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="card card-info card-outline mb-3" id="dokumentasi_berat_produk_box_card" style="display: none;">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Informasi Berat Produk Box</h3>
                                                    </div>
                                                    <div class="card-body">
                                                        <p><strong>Berat Aktual 1:</strong> <span id="dokumentasi_info_box_b1">-</span></p>
                                                        <p><strong>Berat Aktual 2:</strong> <span id="dokumentasi_info_box_b2">-</span></p>
                                                        <p><strong>Berat Aktual 3:</strong> <span id="dokumentasi_info_box_b3">-</span></p>
                                                        <p><strong>Rata-rata:</strong> <span id="dokumentasi_info_box_avg">-</span></p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="card card-info card-outline mb-3" id="dokumentasi_pengemasan_karton_card" style="display: none;">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Informasi Pengemasan Karton</h3>
                                                    </div>
                                                    <div class="card-body">
                                                        <p><strong>Identitas Produk Pada Karton:</strong> <span id="dokumentasi_info_identitas_karton">-</span></p>
                                                        <p><strong>Standar Jumlah Bag/Karton:</strong> <span id="dokumentasi_info_standar_karton">-</span></p>
                                                        <p><strong>Aktual Jumlah Bag/Karton:</strong> <span id="dokumentasi_info_aktual_karton">-</span></p>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- NAMA PRODUK --}}
                                        <div class="form-group mb-3">
                                            <label>Nama Produk</label>

                                            <select name="id_pengemasan_karton" 
                                                    id="id_produk_pengemasan_karton"
                                                    class="form-control"
                                                    required>

                                                <option value="">Pilih Produk</option>
                                                @foreach ($newDokumentasi as $produk)
                                                    <option value="{{ $produk->id }}"
                                                        data-berat-produk-box-id="{{ optional($produk->beratProdukBox)->id ?? '' }}"
                                                        data-berat-produk-pack-id="{{ optional($produk->beratProdukBag)->id ?? '' }}"
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
                                                        data-pack-b1="{{ optional($produk->beratProdukBag)->berat_aktual_1 ?? '' }}"
                                                        data-pack-b2="{{ optional($produk->beratProdukBag)->berat_aktual_2 ?? '' }}"
                                                        data-pack-b3="{{ optional($produk->beratProdukBag)->berat_aktual_3 ?? '' }}"
                                                        data-pack-avg="{{ optional($produk->beratProdukBag)->rata_rata_berat ?? '' }}"
                                                        data-box-b1="{{ optional($produk->beratProdukBox)->berat_aktual_1 ?? '' }}"
                                                        data-box-b2="{{ optional($produk->beratProdukBox)->berat_aktual_2 ?? '' }}"
                                                        data-box-b3="{{ optional($produk->beratProdukBox)->berat_aktual_3 ?? '' }}"
                                                        data-box-avg="{{ optional($produk->beratProdukBox)->rata_rata_berat ?? '' }}"
                                                        data-identitas-karton="{{ $produk->identitas_produk_pada_karton ?? '' }}"
                                                        data-standar-karton="{{ $produk->standar_jumlah_karton ?? '' }}"
                                                        data-aktual-karton="{{ $produk->aktual_jumlah_karton ?? '' }}"
                                                        data-tanggal-karton="{{ optional($produk->tanggal)->format('d-m-Y H:i:s') ?? '' }}"
                                                        data-jam-karton="{{ $produk->jam ?? '' }}"
                                                        {{ (string) old('id_pengemasan_karton', $prefill['id_pengemasan_karton'] ?? request('id_pengemasan_karton')) === (string) $produk->id ? 'selected' : '' }}>
                                                        {{ optional($produk->pengemasanProduk)->kode_produksi ?? 'data kosong' }}
                                                        -
                                                        {{ optional(optional($produk->pengemasanProduk)->produk)->nama_produk ?? 'data kosong' }}

                                                        {{ optional($produk->pengemasanProduk)->berat ?? 'data kosong' }} gram
                                                    </option>
                                                @endforeach
                                            </select>
                                            {{-- INFO JIKA DATA KOSONG --}}
                                            @if(count($newDokumentasi) == 0)
                                                <small class="text-danger font-weight-bold">
                                                    Isi Data Pengemasan Karton terlebih dahulu
                                                </small>
                                            @endif
                                            {{-- HIDDEN FIELD --}}
                                            <input type="hidden" name="id_berat_produk_box"
                                                   id="id_berat_produk_box"
                                                   value="{{ old('id_berat_produk_box', $prefill['id_berat_produk_box'] ?? '') }}">
                                            <input type="hidden" name="id_berat_produk_bag"
                                                   id="id_berat_produk_bag"
                                                   value="{{ old('id_berat_produk_bag', $prefill['id_berat_produk_bag'] ?? '') }}">
                                            <input type="hidden" name="id_pengemasan_plastik"
                                                   id="id_pengemasan_plastik"
                                                   value="{{ old('id_pengemasan_plastik', $prefill['id_pengemasan_plastik'] ?? '') }}">
                                            <input type="hidden" name="id_pengemasan_produk"
                                                   id="id_pengemasan_produk"
                                                   value="{{ old('id_pengemasan_produk', $prefill['id_pengemasan_produk'] ?? '') }}">
                                        </div>
                                        {{-- SHIFT --}}
                                        <div class="form-group mb-3">
                                            <!-- <label>Shift</label> -->
                                            <input type="hidden" name="shift_id" id="shift_id" class="form-control" value="{{ old('shift_id', $prefill['shift_id'] ?? request('shift_id')) }}">
                                        </div>

                                        {{-- TANGGAL --}}
                                        <div class="form-group mb-3">
                                            {{-- <label for="tanggal" class="font-weight-bold">
                                                <i class="fas fa-calendar-alt"></i> Tanggal
                                            </label> --}}
                                            @php
                                                $user = auth()->user();
                                                $roleId = $user->id_role ?? $user->role ?? 0;
                                            @endphp
                                            @if($roleId == 2 || $roleId == 3)
                                                <input type="hidden"
                                                       name="tanggal"
                                                       id="tanggal"
                                                       class="form-control"
                                                       value="{{ old('tanggal', now()->format('d-m-Y')) }}"
                                                       readonly>
                                            @else
                                                <input type="hidden"
                                                       name="tanggal"
                                                       id="tanggal"
                                                       class="form-control"
                                                       value="{{ old('tanggal', now()->format('d-m-Y H:i:s')) }}"
                                                       readonly>
                                            @endif
                                        </div>
                                        {{-- JAM --}}
                                        <div class="form-group mb-3">
                                            <label for="jam" class="font-weight-bold">
                                                <i class="fas fa-clock"></i> Jam
                                            </label>
                                            <input type="time"
                                                   name="jam"
                                                   id="jam"
                                                   class="form-control"
                                                   value="{{ old('jam', date('H:i')) }}"
                                                   required>
                                        </div>
                                        {{-- FOTO --}}
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">
                                                Foto Kode Produksi dan Best Before
                                            </label>
                                            <input type="file"
                                                   name="foto_kode_produksi"
                                                   class="form-control-file"
                                                   accept="image/*"
                                                   capture="camera">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">
                                                QR Code
                                            </label>
                                            <input type="file"
                                                   name="qr_code"
                                                   class="form-control-file"
                                                   accept="image/*"
                                                   capture="camera">
                                        </div>
                                        <div class="form-group mb-4">
                                            <label class="font-weight-bold">
                                                Foto Label Polyroll yang digunakan
                                            </label>
                                            <input type="file"
                                                   name="label_polyroll"
                                                   class="form-control-file"
                                                   accept="image/*"
                                                   capture="camera">
                                        </div>
                                        {{-- BUTTON --}}
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i>
                                            Simpan
                                        </button>
                                        <a href="{{ route('dokumentasi.index') }}"
                                           class="btn btn-secondary ml-2">
                                            <i class="fas fa-arrow-left"></i>
                                            Kembali
                                        </a>
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

<script>
    (function () {
        const produkCard = document.getElementById('dokumentasi_pengemasan_produk_card');
        const plastikCard = document.getElementById('dokumentasi_pengemasan_plastik_card');
        const packCard = document.getElementById('dokumentasi_berat_produk_pack_card');
        const boxCard = document.getElementById('dokumentasi_berat_produk_box_card');
        const kartonCard = document.getElementById('dokumentasi_pengemasan_karton_card');

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
                if (kartonCard) kartonCard.style.display = 'none';
                return;
            }

            setText('dokumentasi_info_kode_produksi', opt.getAttribute('data-kode-produksi'));
            setText('dokumentasi_info_nama_produk', opt.getAttribute('data-nama-produk'));
            setText('dokumentasi_info_berat_produk', opt.getAttribute('data-berat-produk'));
            setText('dokumentasi_info_tanggal', opt.getAttribute('data-tanggal'));
            setText('dokumentasi_info_shift', opt.getAttribute('data-shift'));

            setText('dokumentasi_info_kode_kemasan_plastik', opt.getAttribute('data-kode-kemasan-plastik'));
            setText('dokumentasi_info_proses_penimbangan', opt.getAttribute('data-proses-penimbangan'));
            setText('dokumentasi_info_proses_sealing', opt.getAttribute('data-proses-sealing'));

            setText('dokumentasi_info_pack_b1', opt.getAttribute('data-pack-b1'));
            setText('dokumentasi_info_pack_b2', opt.getAttribute('data-pack-b2'));
            setText('dokumentasi_info_pack_b3', opt.getAttribute('data-pack-b3'));
            setText('dokumentasi_info_pack_avg', opt.getAttribute('data-pack-avg'));

            setText('dokumentasi_info_box_b1', opt.getAttribute('data-box-b1'));
            setText('dokumentasi_info_box_b2', opt.getAttribute('data-box-b2'));
            setText('dokumentasi_info_box_b3', opt.getAttribute('data-box-b3'));
            setText('dokumentasi_info_box_avg', opt.getAttribute('data-box-avg'));

            setText('dokumentasi_info_identitas_karton', opt.getAttribute('data-identitas-karton'));
            setText('dokumentasi_info_standar_karton', opt.getAttribute('data-standar-karton'));
            setText('dokumentasi_info_aktual_karton', opt.getAttribute('data-aktual-karton'));
            setText('dokumentasi_info_tanggal_karton', opt.getAttribute('data-tanggal-karton'));
            setText('dokumentasi_info_jam_karton', opt.getAttribute('data-jam-karton'));

            const shiftId = opt.getAttribute('data-shift-id') || '';
            const shiftEl = document.getElementById('shift_id');
            if (shiftEl && shiftId) shiftEl.value = shiftId;

            const boxIdEl = document.getElementById('id_berat_produk_box');
            const packIdEl = document.getElementById('id_berat_produk_bag');
            const plastikIdEl = document.getElementById('id_pengemasan_plastik');
            const produkIdEl = document.getElementById('id_pengemasan_produk');
            if (boxIdEl) boxIdEl.value = opt.getAttribute('data-berat-produk-box-id') || '';
            if (packIdEl) packIdEl.value = opt.getAttribute('data-berat-produk-pack-id') || '';
            if (plastikIdEl) plastikIdEl.value = opt.getAttribute('data-pengemasan-plastik-id') || '';
            if (produkIdEl) produkIdEl.value = opt.getAttribute('data-pengemasan-produk-id') || '';

            if (produkCard) produkCard.style.display = 'block';
            if (plastikCard) plastikCard.style.display = 'block';
            if (packCard) packCard.style.display = 'block';
            if (boxCard) boxCard.style.display = 'block';
            if (kartonCard) kartonCard.style.display = 'block';
        }

        function init() {
            const selectEl = document.getElementById('id_produk_pengemasan_karton');
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
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    })();
</script>
@endsection