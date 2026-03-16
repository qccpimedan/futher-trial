<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Proses Produksi - Persiapan Bahan Non Forming</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .company-logo {
            float: left;
            width: 60px;
            height: 60px;
            text-align: center;
            line-height: 60px;
            font-weight: bold;
            font-size: 14px;
        }
        .company-info {
            margin-left: 80px;
            text-align: left;
        }
        .company-name {
            font-weight: bold;
            font-size: 12px;
        }
        .title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin: 20px 0;
        }
        .form-info {
            margin-bottom: 15px;
        }
        .form-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .form-info td {
            padding: 3px 5px;
            border: 1px solid #000;
            font-size: 9px;
        }
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .main-table th,
        .main-table td {
            border: 1px solid #000;
            padding: 4px 2px;
            text-align: center;
            font-size: 8px;
            vertical-align: middle;
        }
        .main-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .section-header {
            background-color: #e0e0e0;
            font-weight: bold;
        }
        .signatures {
            margin-top: 30px;
            display: table;
            width: 100%;
        }
        .signature-box {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 10px;
        }
        .signature-line {
            border-bottom: 1px solid #000;
            height: 60px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-logo">
            <img src="{{ public_path('dist/img/cpi-logo.png') }}" alt="CPI Logo" style="width: 60px; height: 60px; object-fit: contain;">
        </div>
        <div class="company-info">
            <div class="company-name">PT. CHAROEN POKPHAND INDONESIA</div>
            <div>FOOD DIVISION {{ strtoupper(auth()->user()->plan->nama_plan) }}</div>
            <div>{{ strtoupper(auth()->user()->plan->nama_plan) }} - INDONESIA</div>
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="title">VERIFIKASI PERSIAPAN BAHAN NON FORMING</div>

    @php
        $details = $data->details ?? collect();
        $rowspan = $details->count() > 0 ? $details->count() : 1;
    @endphp

    <div class="form-info">
        <table>
            <tr>
                <td>Hari / Tanggal</td>
                <td colspan="3">: {{ $data->tanggal ? \Carbon\Carbon::parse($data->tanggal)->format('l, d-m-Y') : '-' }}</td>
            </tr>
            <tr>
                <td>NAMA PRODUK</td>
                <td colspan="3">: {{ $data->formulaNonForming->produk->nama_produk ?? '-' }}</td>
            </tr>
            <tr>
                <td>Shift</td>
                <td colspan="3">: {{ $data->shift->shift ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <table class="main-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 4%;">No</th>
                <th rowspan="2" style="width: 6%;">Waktu</th>
                <th rowspan="2" style="width: 8%;">Kode Produksi</th>
                <th rowspan="2" style="width: 8%;">Kode Produksi Emulsi OIL</th>
                <th rowspan="2" style="width: 8%;">Nomor Formula</th>
                <th colspan="3" class="section-header">LARUTAN BUMBU</th>
                <th rowspan="2" style="width: 8%;">Suhu Standard Adonan</th>
                <th rowspan="2" style="width: 6%;">REWORK (kg)</th>
                <th colspan="6" class="section-header">SUHU AKTUAL (°C)</th>
                <th rowspan="2" style="width: 8%;">Waktu Mixing</th>
                <th rowspan="2" style="width: 6%;">Kondisi</th>
                <th rowspan="2" style="width: 10%;">KETERANGAN</th>
            </tr>
            <tr>
                <th style="width: 12%;">Bahan RM</th>
                <th style="width: 12%;">Kode Produksi Bahan</th>
                <th style="width: 6%;">Suhu (°C)</th>
                <th style="width: 5%;">1</th>
                <th style="width: 5%;">2</th>
                <th style="width: 5%;">3</th>
                <th style="width: 5%;">4</th>
                <th style="width: 5%;">5</th>
                <th style="width: 5%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @if($details->count() > 0)
                @foreach($details as $index => $d)
                    <tr>
                        @if($index == 0)
                            <td rowspan="{{ $rowspan }}">1</td>
                            <td rowspan="{{ $rowspan }}">{{ $data->jam ? \Carbon\Carbon::parse($data->jam)->format('H:i') : '-' }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $data->kode_produksi ?? '-' }}</td>
                            <td rowspan="{{ $rowspan }}">{{ is_array($data->kode_produksi_emulsi_oil) ? implode(', ', $data->kode_produksi_emulsi_oil) : ($data->kode_produksi_emulsi_oil ?? '-') }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $data->formulaNonForming->nomor_formula ?? '-' }}</td>
                        @endif
                        <td>{{ $d->bahanNonForming->nama_rm ?? '-' }}</td>
                        <td>{{ $d->kode_produksi_bahan ?? '-' }}</td>
                        <td>{{ $d->suhu ?? '-' }}</td>
                        @if($index == 0)
                            <td rowspan="{{ $rowspan }}">{{ $data->suhuAdonan->std_suhu ?? '-' }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $data->rework ?? '-' }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $data->aktualSuhuAdonan ? $data->aktualSuhuAdonan->aktual_suhu_1 : '-' }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $data->aktualSuhuAdonan ? $data->aktualSuhuAdonan->aktual_suhu_2 : '-' }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $data->aktualSuhuAdonan ? $data->aktualSuhuAdonan->aktual_suhu_3 : '-' }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $data->aktualSuhuAdonan ? $data->aktualSuhuAdonan->aktual_suhu_4 : '-' }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $data->aktualSuhuAdonan ? $data->aktualSuhuAdonan->aktual_suhu_5 : '-' }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $data->aktualSuhuAdonan ? number_format($data->aktualSuhuAdonan->total_aktual_suhu, 1) : '-' }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $data->waktu_mulai_mixing ?? '-' }} - {{ $data->waktu_selesai_mixing ?? '-' }}</td>
                            <td rowspan="{{ $rowspan }}">
                                @if($data->kondisi == '✔' || $data->kondisi == '&#10004;')
                                    OK
                                @elseif($data->kondisi == '✘' || $data->kondisi == '&#10008;')
                                    Tidak OK
                                @else
                                    {{ $data->kondisi ?? '-' }}
                                @endif
                            </td>
                            <td rowspan="{{ $rowspan }}">{{ $data->catatan ?? '-' }}</td>
                        @endif
                    </tr>
                @endforeach
            @else
                <tr>
                    <td>1</td>
                    <td>{{ $data->jam ? \Carbon\Carbon::parse($data->jam)->format('H:i') : '-' }}</td>
                    <td>{{ $data->kode_produksi ?? '-' }}</td>
                    <td>{{ is_array($data->kode_produksi_emulsi_oil) ? implode(', ', $data->kode_produksi_emulsi_oil) : ($data->kode_produksi_emulsi_oil ?? '-') }}</td>
                    <td>{{ $data->formulaNonForming->nomor_formula ?? '-' }}</td>
                    <td>-</td>
                    <td>-</td>
                    <td>{{ $data->suhuAdonan->std_suhu ?? '-' }}</td>
                    <td>{{ $data->rework ?? '-' }}</td>
                    <td>{{ $data->aktualSuhuAdonan ? $data->aktualSuhuAdonan->aktual_suhu_1 : '-' }}</td>
                    <td>{{ $data->aktualSuhuAdonan ? $data->aktualSuhuAdonan->aktual_suhu_2 : '-' }}</td>
                    <td>{{ $data->aktualSuhuAdonan ? $data->aktualSuhuAdonan->aktual_suhu_3 : '-' }}</td>
                    <td>{{ $data->aktualSuhuAdonan ? $data->aktualSuhuAdonan->aktual_suhu_4 : '-' }}</td>
                    <td>{{ $data->aktualSuhuAdonan ? $data->aktualSuhuAdonan->aktual_suhu_5 : '-' }}</td>
                    <td>{{ $data->aktualSuhuAdonan ? number_format($data->aktualSuhuAdonan->total_aktual_suhu, 1) : '-' }}</td>
                    <td>{{ $data->waktu_mulai_mixing ?? '-' }} - {{ $data->waktu_selesai_mixing ?? '-' }}</td>
                    <td>
                        @if($data->kondisi == '✔' || $data->kondisi == '&#10004;')
                            OK
                        @elseif($data->kondisi == '✘' || $data->kondisi == '&#10008;')
                            Tidak OK
                        @else
                            {{ $data->kondisi ?? '-' }}
                        @endif
                    </td>
                    <td>{{ $data->catatan ?? '-' }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div style="width: 100%; text-align: right; margin-top: 10px; font-style: italic;">
        <span>QF 04/00</span>
    </div>

    <div class="signatures">
        <div class="signature-box">
            <div>Dibuat Oleh:</div>
            <div class="signature-line"></div>
            {{ $data->user->name ?? 'QC' }}
        </div>
        <div class="signature-box">
            <div>Diperiksa Oleh:</div>
            <div class="signature-line"></div>
            SPV
        </div>
        <div class="signature-box">
            <div>Diketahui Oleh:</div>
            <div class="signature-line"></div>
            FM/FL PRODUKSI
        </div>
    </div>
</body>
</html>
