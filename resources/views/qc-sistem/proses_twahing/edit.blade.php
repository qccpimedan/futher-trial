@extends('layouts.app')

@section('title', 'Edit Pemeriksaan Proses Thawing')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Edit Pemeriksaan Proses Thawing</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('proses-twahing.index') }}">Proses Thawing</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Form Pemeriksaan Proses Thawing</h3>
                                <div class="card-tools">
                                    <a href="{{ route('proses-twahing.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                </div>
                            </div>

                            <form action="{{ route('proses-twahing.update', $item->uuid) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="card-body">
                                    @if($errors->any())
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                @foreach($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <div class="row">
                                        @if(auth()->user()->role === 'superadmin')
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Plan</label>
                                                    <input type="text" class="form-control" value="{{ $item->plan->nama_plan ?? '-' }}" readonly>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="tanggal">Hari/Tanggal <span class="text-danger">*</span></label>
                                                <input type="date" name="tanggal" id="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') : '') }}" required>
                                                @error('tanggal')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="jam">Jam</label>
                                                <input type="time" name="jam" id="jam" class="form-control @error('jam') is-invalid @enderror" value="{{ old('jam', $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '') }}">
                                                @error('jam')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="id_shift">Shift <span class="text-danger">*</span></label>
                                                <select name="id_shift" id="id_shift" class="form-control @error('id_shift') is-invalid @enderror" required>
                                                    <option value="">-- pilih shift --</option>
                                                    @foreach($shifts as $shift)
                                                        <option value="{{ $shift->id }}" {{ (string) old('id_shift', $item->id_shift) === (string) $shift->id ? 'selected' : '' }}>
                                                            Shift {{ $shift->shift }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('id_shift')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="waktu_thawing_awal">Waktu Thawing (Awal)</label>
                                                <input type="time" name="waktu_thawing_awal" id="waktu_thawing_awal" class="form-control" value="{{ old('waktu_thawing_awal', $item->waktu_thawing_awal ? \Carbon\Carbon::parse($item->waktu_thawing_awal)->format('H:i') : '') }}">
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="waktu_thawing_akhir">Waktu Thawing (Akhir)</label>
                                                <input type="time" name="waktu_thawing_akhir" id="waktu_thawing_akhir" class="form-control" value="{{ old('waktu_thawing_akhir', $item->waktu_thawing_akhir ? \Carbon\Carbon::parse($item->waktu_thawing_akhir)->format('H:i') : '') }}">
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="total_waktu_thawing_jam">Total Waktu Thawing (Jam)</label>
                                                <input type="number" step="0.01" name="total_waktu_thawing_jam" id="total_waktu_thawing_jam" class="form-control" value="{{ old('total_waktu_thawing_jam', $item->total_waktu_thawing_jam) }}">
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="kondisi_kemasan_rm">Kondisi Kemasan RM</label>
                                                <select name="kondisi_kemasan_rm" id="kondisi_kemasan_rm" class="form-control">
                                                    <option value="">-- pilih --</option>
                                                    <option value="utuh" {{ old('kondisi_kemasan_rm', $item->kondisi_kemasan_rm) === 'utuh' ? 'selected' : '' }}>Utuh</option>
                                                    <option value="sobek" {{ old('kondisi_kemasan_rm', $item->kondisi_kemasan_rm) === 'sobek' ? 'selected' : '' }}>Sobek</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="catatan">Catatan</label>
                                                <textarea name="catatan" id="catatan" rows="2" class="form-control">{{ old('catatan', $item->catatan) }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>
                                    <h5 class="mb-2">Detail Pemeriksaan</h5>

                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="detailTable" style="white-space:nowrap;">
                                            <thead>
                                                <tr class="text-center">
                                                    <th style="width:60px;">No</th>
                                                    <th>Nama RM</th>
                                                    <th>Kode Produksi</th>
                                                    <th>Kondisi Ruang</th>
                                                    <th>Waktu Pemeriksaan</th>
                                                    <th>Suhu Ruang (°C)</th>
                                                    <th>Suhu Air Thawing (°C)</th>
                                                    <th>Suhu Produk (°C)</th>
                                                    <th>Kondisi Produk</th>
                                                    <th style="width:80px;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $details = old('details');
                                                    if ($details === null) {
                                                        $details = ($item->details ?? collect())->map(function ($d) {
                                                            return [
                                                                'id_rm' => $d->id_rm,
                                                                'kode_produksi' => $d->kode_produksi,
                                                                'kondisi_ruang' => $d->kondisi_ruang,
                                                                'waktu_pemeriksaan' => $d->waktu_pemeriksaan,
                                                                'suhu_ruang' => $d->suhu_ruang,
                                                                'suhu_air_thawing' => $d->suhu_air_thawing,
                                                                'suhu_produk' => $d->suhu_produk,
                                                                'kondisi_produk' => $d->kondisi_produk,
                                                            ];
                                                        })->values()->toArray();
                                                    }

                                                    if (!is_array($details) || count($details) === 0) {
                                                        $details = [[
                                                            'id_rm' => null,
                                                            'kode_produksi' => null,
                                                            'kondisi_ruang' => null,
                                                            'waktu_pemeriksaan' => null,
                                                            'suhu_ruang' => null,
                                                            'suhu_air_thawing' => null,
                                                            'suhu_produk' => null,
                                                            'kondisi_produk' => null,
                                                        ]];
                                                    }
                                                @endphp

                                                @foreach($details as $i => $d)
                                                    <tr>
                                                        <td class="text-center align-middle row-no">{{ $i + 1 }}</td>
                                                        <td>
                                                            <select name="details[{{ $i }}][id_rm]" class="form-control select2-rm">
                                                                <option value="">-- pilih RM --</option>
                                                                @foreach($rms as $rm)
                                                                    <option value="{{ $rm->id }}" {{ (string) ($d['id_rm'] ?? '') === (string) $rm->id ? 'selected' : '' }}>
                                                                        {{ $rm->nama_rm }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td><input type="text" name="details[{{ $i }}][kode_produksi]" class="form-control" value="{{ $d['kode_produksi'] ?? '' }}"></td>
                                                        <td><input type="text" name="details[{{ $i }}][kondisi_ruang]" class="form-control" value="{{ $d['kondisi_ruang'] ?? '' }}"></td>
                                                        <td><input type="time" name="details[{{ $i }}][waktu_pemeriksaan]" class="form-control" value="{{ $d['waktu_pemeriksaan'] ? \Carbon\Carbon::parse($d['waktu_pemeriksaan'])->format('H:i') : '' }}"></td>
                                                        <td><input type="number" step="0.01" name="details[{{ $i }}][suhu_ruang]" class="form-control" value="{{ $d['suhu_ruang'] ?? '' }}"></td>
                                                        <td><input type="number" step="0.01" name="details[{{ $i }}][suhu_air_thawing]" class="form-control" value="{{ $d['suhu_air_thawing'] ?? '' }}"></td>
                                                        <td><input type="number" step="0.01" name="details[{{ $i }}][suhu_produk]" class="form-control" value="{{ $d['suhu_produk'] ?? '' }}"></td>
                                                        <td><input type="text" name="details[{{ $i }}][kondisi_produk]" class="form-control" value="{{ $d['kondisi_produk'] ?? '' }}"></td>
                                                        <td class="text-center align-middle">
                                                            <button type="button" class="btn btn-danger btn-sm btn-remove-row" title="Hapus">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <button type="button" class="btn btn-success btn-sm" id="btnAddRow">
                                        <i class="fas fa-plus"></i> Tambah Baris
                                    </button>
                                </div>

                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    function initSelect2Rm($ctx) {
        const $el = ($ctx && $ctx.length) ? $ctx.find('.select2-rm') : $('.select2-rm');
        $el.each(function () {
            const $s = $(this);
            if ($s.hasClass('select2-hidden-accessible')) {
                return;
            }
            $s.select2({
                placeholder: '-- pilih RM --',
                allowClear: true,
                width: '100%'
            });
        });
    }

    function renumber() {
        $('#detailTable tbody tr').each(function (idx) {
            $(this).find('.row-no').text(idx + 1);
            $(this).find('select, input').each(function () {
                const name = $(this).attr('name');
                if (!name) return;
                $(this).attr('name', name.replace(/details\[\d+\]/, 'details[' + idx + ']'));
            });
        });
    }

    $(document).ready(function () {
        initSelect2Rm();
    });

    $('#btnAddRow').on('click', function () {
        const $tbody = $('#detailTable tbody');
        const idx = $tbody.find('tr').length;

        const rowHtml = `
<tr>
    <td class="text-center align-middle row-no">${idx + 1}</td>
    <td>
        <select name="details[${idx}][id_rm]" class="form-control select2-rm">
            <option value="">-- pilih RM --</option>
            @foreach($rms as $rm)
                <option value="{{ $rm->id }}">{{ $rm->nama_rm }}</option>
            @endforeach
        </select>
    </td>
    <td><input type="text" name="details[${idx}][kode_produksi]" class="form-control"></td>
    <td><input type="text" name="details[${idx}][kondisi_ruang]" class="form-control"></td>
    <td><input type="time" name="details[${idx}][waktu_pemeriksaan]" class="form-control"></td>
    <td><input type="number" step="0.01" name="details[${idx}][suhu_ruang]" class="form-control"></td>
    <td><input type="number" step="0.01" name="details[${idx}][suhu_air_thawing]" class="form-control"></td>
    <td><input type="number" step="0.01" name="details[${idx}][suhu_produk]" class="form-control"></td>
    <td><input type="text" name="details[${idx}][kondisi_produk]" class="form-control"></td>
    <td class="text-center align-middle">
        <button type="button" class="btn btn-danger btn-sm btn-remove-row" title="Hapus">
            <i class="fas fa-trash"></i>
        </button>
    </td>
</tr>`;

        $tbody.append(rowHtml);
        renumber();
        initSelect2Rm($tbody);
    });

    $(document).on('click', '.btn-remove-row', function () {
        const $tbody = $('#detailTable tbody');
        if ($tbody.find('tr').length <= 1) {
            $tbody.find('tr').find('input').val('');
            $tbody.find('tr').find('select').val('').trigger('change');
            return;
        }

        $(this).closest('tr').remove();
        renumber();
    });
})();
</script>
@endpush
