<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Proses Produksi - Pembekuan IQF Penggorengan</title>
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
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        .signature-table td {
            width: 33.33%;
            text-align: center;
            vertical-align: bottom;
            padding: 5px 10px;
            border: none;
        }
        .signature-label {
            text-align: center;
            font-size: 10px;
            margin-bottom: 5px;
        }
        .signature-qr-area {
            height: 65px;
            text-align: center;
            vertical-align: bottom;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 5px;
            margin-bottom: 5px;
        }
        .signature-name {
            font-size: 10px;
            text-align: center;
        }
        .qr-code-img {
            max-height: 55px;
            max-width: 55px;
        }
        .filter-info {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
        .filter-info h4 {
            margin: 0 0 10px 0;
            font-size: 11px;
            font-weight: bold;
        }
        .filter-info p {
            margin: 2px 0;
            font-size: 9px;
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

    <div class="title">VERIFIKASI PROSES PENGGORENGAN/PEMASAKAN</div>

    @if($data->count() > 0)
        @php
            $firstItem = $data->first();
        @endphp

        <div class="form-info">
            <table>
                <tr>
                    <td>Hari / Tanggal</td>
                    <td colspan="3">: {{ $firstItem->tanggal ? \Carbon\Carbon::parse($firstItem->tanggal)->format('l, d-m-Y') : '-' }}</td>
                </tr>
                <tr>
                    <td>Shift</td>
                    <td colspan="3">: {{ $firstItem->penggorenganData && $firstItem->penggorenganData->shift ? $firstItem->penggorenganData->shift->shift : '-' }}</td>
                </tr>
               
                <tr>
                    <td>Produk</td>
                    <td colspan="3">: {{ $firstItem->penggorenganData && $firstItem->penggorenganData->produk ? $firstItem->penggorenganData->produk->nama_produk : '-' }} {{ $firstItem->penggorenganData && $firstItem->penggorenganData->berat_produk ? $firstItem->penggorenganData->berat_produk . 'gram' : '' }}</td>
                </tr>
            </table>
        </div>

        <!-- A. PENGGORENGAN -->
        <h3 style="margin-top: 20px; margin-bottom: 10px; color: #333;">A. PENGGORENGAN</h3>
        <table class="main-table" style="margin-bottom: 20px;">
            <thead>
                <tr>
                    <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">No</th>
                    <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">Waktu</th>
                    <!-- <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">Nama Produk</th> -->
                    <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">Kode Produksi</th>
                    <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">No Of Strokes</th>
                    <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">Waktu Pemasakan</th>
                    <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">Hasil Pencetakan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                    @if($item->penggorenganData)
                        <tr>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $index + 1 }}</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->penggorenganData->jam ? \Carbon\Carbon::parse($item->penggorenganData->jam)->format('H:i') : '-' }}</td>
                            <!-- <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->penggorenganData->produk->nama_produk ?? '-' }}</td> -->
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->penggorenganData->kode_produksi ?? '-' }}</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->penggorenganData->no_of_strokes ?? '-' }}</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->penggorenganData->waktu_pemasakan ?? '-' }} Detik</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">
                                @if($item->penggorenganData->hasil_pencetakan == '✔' || $item->penggorenganData->hasil_pencetakan == '&#10004;')
                                    OK
                                @elseif($item->penggorenganData->hasil_pencetakan == '✘' || $item->penggorenganData->hasil_pencetakan == '&#10008;')
                                    Tidak OK
                                @else
                                    {{ $item->penggorenganData->hasil_pencetakan ?? '-' }}
                                @endif
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        <!-- B. BATTERING -->
        <h3 style="margin-top: 20px; margin-bottom: 10px; color: #333;">B. BATTERING</h3>
        <table class="main-table" style="margin-bottom: 20px;">
            <thead>
                <tr>
                    <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">No</th>
                    <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">Waktu</th>
                    <!-- <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">Produk</th> -->
                    <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">Kode Produksi</th>
                    <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">Jenis Better</th>
                    <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">Hasil Better</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                    @if($item->batteringData)
                        <tr>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $index + 1 }}</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->batteringData->jam ? \Carbon\Carbon::parse($item->batteringData->jam)->format('H:i') : '-' }}</td>
                            <!-- <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->batteringData->produk->nama_produk ?? '-' }}</td> -->
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->batteringData->kode_produksi_better ?? '-' }}</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->batteringData->jenis_better->nama_better ?? '-' }}</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">
                                @if($item->batteringData->hasil_better == '✔' || $item->batteringData->hasil_better == '&#10004;')
                                    OK
                                @elseif($item->batteringData->hasil_better == '✘' || $item->batteringData->hasil_better == '&#10008;')
                                    Tidak OK
                                @else
                                    {{ $item->batteringData->hasil_better ?? '-' }}
                                @endif
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        <!-- C. BREADER -->
        <h3 style="margin-top: 20px; margin-bottom: 10px; color: #333;">C. BREADER</h3>
        <table class="main-table" style="margin-bottom: 20px;">
            <thead>
                <tr>
                    <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">No</th>
                    <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">Waktu</th>
                    <!-- <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">Produk</th> -->

                    <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">Kode Produksi</th>
                    <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">Jenis Breader</th>
                    <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">Hasil Breader</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                    @if($item->breaderData)
                        <tr>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $index + 1 }}</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->breaderData->jam ? \Carbon\Carbon::parse($item->breaderData->jam)->format('H:i') : '-' }}</td>
                            <!-- <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->breaderData->produk->nama_produk ?? '-' }}</td> -->
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->breaderData->kode_produksi ?? '-' }}</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->breaderData->jenis_breader_names ?? '-' }}</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">
                                @if($item->breaderData->hasil_breader == '✔' || $item->breaderData->hasil_breader == '&#10004;')
                                    OK
                                @elseif($item->breaderData->hasil_breader == '✘' || $item->breaderData->hasil_breader == '&#10008;')
                                    Tidak OK
                                @else
                                    {{ $item->breaderData->hasil_breader ?? '-' }}
                                @endif
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        <!-- D. FRAYER 1 - 5 -->
        <h3 style="margin-top: 20px; margin-bottom: 10px; color: #333;">D. FRAYER 1 - 5</h3>
        <table class="main-table" style="margin-bottom: 20px;">
            <thead>
                <tr>
                    <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">No</th>
                    <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">Waktu</th>
                    <!-- <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">Produk</th> -->
                    <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">Fryer</th>
                    <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">Standart Suhu Fryer</th>
                    <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">Aktual Suhu Fryer</th>
                    <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">Standart Waktu Penggorengan</th>
                    <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">Aktual Waktu Penggorengan</th>
                    <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">TPM Minyak</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                    @if($item->frayerData)
                        <tr>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $index + 1 }}.1</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->frayerData->jam ? \Carbon\Carbon::parse($item->frayerData->jam)->format('H:i') : '-' }}</td>
                            <!-- <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->frayerData->produk->nama_produk ?? '-' }}</td> -->
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">Fryer 1</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->frayerData->suhuFrayer->suhu_frayer ?? '-' }}°C</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->frayerData->aktual_suhu_penggorengan ?? '-' }}°C</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->frayerData->waktuPenggorengan->waktu_penggorengan ?? $item->frayerData->suhuFrayer->waktu_penggorengan_1 ?? '-' }} detik</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->frayerData->aktual_penggorengan ?? '-' }}detik</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->frayerData->tpm_minyak ?? '-' }}</td>
                        </tr>
                    @endif
                    @if($item->frayer2Data)
                        <tr>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $index + 1 }}.2</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->frayer2Data->jam ? \Carbon\Carbon::parse($item->frayer2Data->jam)->format('H:i') : '-' }}</td>
                             <!-- <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->frayer2Data->produk->nama_produk ?? '-' }}</td> -->
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">Fryer 2</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->frayer2Data->suhuFrayer2->suhu_frayer_2 ?? '-' }}°C</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->frayer2Data->aktual_suhu_penggorengan ?? '-' }}°C</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->frayer2Data->waktuPenggorengan2->waktu_penggorengan_2 ?? $item->frayer2Data->suhuFrayer2->waktu_penggorengan_2 ?? '-' }} s</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->frayer2Data->aktual_penggorengan ?? '-' }}s</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->frayer2Data->tpm_minyak ?? '-' }}</td>
                        </tr>
                    @endif
                    @if($item->frayer3Data)
                        <tr>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $index + 1 }}.3</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->frayer3Data->jam ? \Carbon\Carbon::parse($item->frayer3Data->jam)->format('H:i') : '-' }}</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">Fryer 3</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->frayer3Data->suhuFrayer->suhu_frayer_3 ?? '-' }}°C</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->frayer3Data->aktual_suhu_penggorengan ?? '-' }}°C</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->frayer3Data->waktuPenggorengan->waktu_penggorengan ?? $item->frayer3Data->suhuFrayer->waktu_penggorengan_3 ?? '-' }} s</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->frayer3Data->aktual_penggorengan ?? '-' }}s</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->frayer3Data->tpm_minyak ?? '-' }}</td>
                        </tr>
                    @endif
                    @if($item->frayer4Data)
                        <tr>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $index + 1 }}.4</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->frayer4Data->jam ? \Carbon\Carbon::parse($item->frayer4Data->jam)->format('H:i') : '-' }}</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">Fryer 4</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->frayer4Data->suhuFrayer->suhu_frayer_4 ?? '-' }}°C</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->frayer4Data->aktual_suhu_penggorengan ?? '-' }}°C</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->frayer4Data->waktuPenggorengan->waktu_penggorengan ?? $item->frayer4Data->suhuFrayer->waktu_penggorengan_4 ?? '-' }} s</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->frayer4Data->aktual_penggorengan ?? '-' }}s</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->frayer4Data->tpm_minyak ?? '-' }}</td>
                        </tr>
                    @endif
                    @if($item->frayer5Data)
                        <tr>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $index + 1 }}.5</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->frayer5Data->jam ? \Carbon\Carbon::parse($item->frayer5Data->jam)->format('H:i') : '-' }}</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">Fryer 5</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->frayer5Data->suhuFrayer->suhu_frayer_5 ?? '-' }}°C</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->frayer5Data->aktual_suhu_penggorengan ?? '-' }}°C</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->frayer5Data->waktuPenggorengan->waktu_penggorengan ?? $item->frayer5Data->suhuFrayer->waktu_penggorengan_5 ?? '-' }} s</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->frayer5Data->aktual_penggorengan ?? '-' }}s</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->frayer5Data->tpm_minyak ?? '-' }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        <!-- E. HASIL PENGGORENGAN -->
        <h3 style="margin-top: 20px; margin-bottom: 10px; color: #333;">E. HASIL PENGGORENGAN</h3>
        <table class="main-table" style="margin-bottom: 20px;">
            <thead>
                <tr>
                    <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">No</th>
                    <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">Waktu</th>
                    <!-- <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">Produk</th> -->
                    <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">Std Suhu Pusat</th>
                    <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">Aktual Suhu Pusat</th>
                    <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">Kematangan</th>
                    <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">Kenampakan</th>
                    <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">Warna</th>
                    <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">Rasa</th>
                    <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">Bau</th>
                    <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">Tekstur</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                    @if($item->hasilPenggorenganData)
                        <tr>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $index + 1 }}</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->hasilPenggorenganData->jam ? \Carbon\Carbon::parse($item->hasilPenggorenganData->jam)->format('H:i') : '-' }}</td>
                            <!-- <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->hasilPenggorenganData->produk->nama_produk ?? '-' }}</td> -->
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">
                                @if($item->hasilPenggorenganData->stdSuhuPusat)
                                    @php
                                        $suhuArray = is_array($item->hasilPenggorenganData->stdSuhuPusat->std_suhu_pusat) 
                                            ? $item->hasilPenggorenganData->stdSuhuPusat->std_suhu_pusat 
                                            : json_decode($item->hasilPenggorenganData->stdSuhuPusat->std_suhu_pusat, true) ?? [];
                                        
                                        // Detect fryer yang dipakai
                                        $fryerNumber = null;
                                        if($item->hasilPenggorenganData->frayer2_uuid) {
                                            $fryerNumber = 2;
                                        } elseif($item->hasilPenggorenganData->frayer_uuid) {
                                            $fryerNumber = 1;
                                            
                                            // Cek Frayer 3/4/5
                                            $frayer3 = \App\Models\Frayer3::where('uuid', $item->hasilPenggorenganData->frayer_uuid)->first();
                                            $frayer4 = \App\Models\Frayer4::where('uuid', $item->hasilPenggorenganData->frayer_uuid)->first();
                                            $frayer5 = \App\Models\Frayer5::where('uuid', $item->hasilPenggorenganData->frayer_uuid)->first();
                                            
                                            if($frayer5) $fryerNumber = 5;
                                            elseif($frayer4) $fryerNumber = 4;
                                            elseif($frayer3) $fryerNumber = 3;
                                        }
                                        
                                        // Ambil suhu sesuai fryer
                                        $displaySuhu = ($fryerNumber && isset($suhuArray[$fryerNumber - 1])) 
                                            ? $suhuArray[$fryerNumber - 1] 
                                            : '-';
                                    @endphp
                                    {{ $displaySuhu }}°C
                                @else
                                    -
                                @endif
                            </td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->hasilPenggorenganData->aktual_suhu_pusat ?? '-' }}°C</td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">
                                @if(($item->hasilPenggorenganData->sensori_kematangan ?? null) == '✔' || ($item->hasilPenggorenganData->sensori_kematangan ?? null) == '&#10004;')
                                    OK
                                @elseif(($item->hasilPenggorenganData->sensori_kematangan ?? null) == '✘' || ($item->hasilPenggorenganData->sensori_kematangan ?? null) == '&#10008;')
                                    Tidak OK
                                @else
                                    {{ $item->hasilPenggorenganData->sensori_kematangan ?? '-' }}
                                @endif
                            </td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">
                                @if(($item->hasilPenggorenganData->sensori_kenampakan ?? null) == '✔' || ($item->hasilPenggorenganData->sensori_kenampakan ?? null) == '&#10004;')
                                    OK
                                @elseif(($item->hasilPenggorenganData->sensori_kenampakan ?? null) == '✘' || ($item->hasilPenggorenganData->sensori_kenampakan ?? null) == '&#10008;')
                                    Tidak OK
                                @else
                                    {{ $item->hasilPenggorenganData->sensori_kenampakan ?? '-' }}
                                @endif
                            </td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">
                                @if(($item->hasilPenggorenganData->sensori_warna ?? null) == '✔' || ($item->hasilPenggorenganData->sensori_warna ?? null) == '&#10004;')
                                    OK
                                @elseif(($item->hasilPenggorenganData->sensori_warna ?? null) == '✘' || ($item->hasilPenggorenganData->sensori_warna ?? null) == '&#10008;')
                                    Tidak OK
                                @else
                                    {{ $item->hasilPenggorenganData->sensori_warna ?? '-' }}
                                @endif
                            </td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">
                                @if(($item->hasilPenggorenganData->sensori_rasa ?? null) == '✔' || ($item->hasilPenggorenganData->sensori_rasa ?? null) == '&#10004;')
                                    OK
                                @elseif(($item->hasilPenggorenganData->sensori_rasa ?? null) == '✘' || ($item->hasilPenggorenganData->sensori_rasa ?? null) == '&#10008;')
                                    Tidak OK
                                @else
                                    {{ $item->hasilPenggorenganData->sensori_rasa ?? '-' }}
                                @endif
                            </td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">
                                @if(($item->hasilPenggorenganData->sensori_bau ?? null) == '✔' || ($item->hasilPenggorenganData->sensori_bau ?? null) == '&#10004;')
                                    OK
                                @elseif(($item->hasilPenggorenganData->sensori_bau ?? null) == '✘' || ($item->hasilPenggorenganData->sensori_bau ?? null) == '&#10008;')
                                    Tidak OK
                                @else
                                    {{ $item->hasilPenggorenganData->sensori_bau ?? '-' }}
                                @endif
                            </td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">
                                @if(($item->hasilPenggorenganData->sensori_tekstur ?? null) == '✔' || ($item->hasilPenggorenganData->sensori_tekstur ?? null) == '&#10004;')
                                    OK
                                @elseif(($item->hasilPenggorenganData->sensori_tekstur ?? null) == '✘' || ($item->hasilPenggorenganData->sensori_tekstur ?? null) == '&#10008;')
                                    Tidak OK
                                @else
                                    {{ $item->hasilPenggorenganData->sensori_tekstur ?? '-' }}
                                @endif
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        <!-- F. PEMBEKUAN IQF -->
        <h3 style="margin-top: 20px; margin-bottom: 10px; color: #333;">F. PEMBEKUAN IQF</h3>
        <table class="main-table" style="margin-bottom: 20px;">
            <thead>
                <tr>
                    <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">No</th>
                    <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">Waktu</th>
                    <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">Suhu Ruang IQF</th>
                    <th style="border: 1px solid black; padding: 8px; background: #f0f0f0;">Holding Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                    <tr>
                        <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $index + 1 }}</td>
                        <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</td>
                        <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->suhu_ruang_iqf ?? '-' }}°C</td>
                        <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->holding_time ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div style="width: 100%; text-align: right; margin-top: 10px;">
            <span> {{ $request->kode_form ?? $firstItem->kode_form ?? '-' }}</span>
        </div>
        {{-- ===== TANDA TANGAN ===== --}}
        @php
            $qcApprover = null; $qcApproverName = 'QC';
            foreach($data as $item) {
                if($item->approved_by_qc && $item->qc_approved_by) {
                    $qcApprover = \App\Models\User::find($item->qc_approved_by);
                    if($qcApprover) { $qcApproverName = $qcApprover->name; break; }
                }
            }
            $spvApprover = null; $spvApproverName = 'SPV';
            foreach($data as $item) {
                if($item->approved_by_spv && $item->spv_approved_by) {
                    $spvApprover = \App\Models\User::find($item->spv_approved_by);
                    if($spvApprover) { $spvApproverName = $spvApprover->name; break; }
                }
            }
            $produksiApprover = null; $produksiApproverName = 'FM/FL PRODUKSI';
            foreach($data as $item) {
                if($item->approved_by_produksi && $item->produksi_approved_by) {
                    $produksiApprover = \App\Models\User::find($item->produksi_approved_by);
                    if($produksiApprover) { $produksiApproverName = $produksiApprover->name; break; }
                }
            }
            $base64QcSvg = null;
            if($qcApprover) {
                $qcApprovalDate = '';
                foreach($data as $item) { if($item->approved_by_qc && $item->qc_approved_at) { $qcApprovalDate = $item->qc_approved_at->format('Y-m-d'); break; } }
                $base64QcSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$qcApprover->name} (Tim QC) pada {$qcApprovalDate}"));
            }
            $base64SpvSvg = null;
            if($spvApprover) {
                $spvApprovalDate = '';
                foreach($data as $item) { if($item->approved_by_spv && $item->spv_approved_at) { $spvApprovalDate = $item->spv_approved_at->format('Y-m-d'); break; } }
                $base64SpvSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$spvApprover->name} (SPV) pada {$spvApprovalDate}"));
            }
            $base64ProduksiSvg = null;
            if($produksiApprover) {
                $produksiApprovalDate = '';
                foreach($data as $item) { if($item->approved_by_produksi && $item->produksi_approved_at) { $produksiApprovalDate = $item->produksi_approved_at->format('Y-m-d'); break; } }
                $base64ProduksiSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$produksiApprover->name} (FM/FL PRODUKSI) pada {$produksiApprovalDate}"));
            }
        @endphp
        <table class="signature-table">
            <tr>
                <td class="signature-label">Dibuat Oleh:</td>
                <td class="signature-label">Diperiksa Oleh:</td>
                <td class="signature-label">Diketahui Oleh:</td>
            </tr>
            <tr>
                <td class="signature-qr-area">@if($base64QcSvg)<img src="{{ $base64QcSvg }}" class="qr-code-img" alt="QR QC">@endif</td>
                <td class="signature-qr-area">@if($base64SpvSvg)<img src="{{ $base64SpvSvg }}" class="qr-code-img" alt="QR SPV">@endif</td>
                <td class="signature-qr-area">@if($base64ProduksiSvg)<img src="{{ $base64ProduksiSvg }}" class="qr-code-img" alt="QR Produksi">@endif</td>
            </tr>
            <tr>
                <td><div class="signature-line"></div></td>
                <td><div class="signature-line"></div></td>
                <td><div class="signature-line"></div></td>
            </tr>
            <tr>
                <td class="signature-name">{{ $qcApproverName }}</td>
                <td class="signature-name">{{ $spvApproverName }}</td>
                <td class="signature-name">{{ $produksiApproverName }}</td>
            </tr>
        </table>

    @else
        <div style="text-align: center; padding: 20px;">
            <p>Tidak ada data yang sesuai dengan filter yang dipilih.</p>
        </div>
    @endif
</body>
</html>
