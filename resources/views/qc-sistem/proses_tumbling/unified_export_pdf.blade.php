<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Unified Production Export - Tumbling Process</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 8px;
            margin: 0;
            padding: 0;
            line-height: 1.1;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
        }
        .company-logo {
            float: left;
            width: 40px;
            height: 40px;
            text-align: center;
            line-height: 40px;
            font-weight: bold;
            font-size: 10px;
        }
        .company-info {
            margin-left: 50px;
            text-align: left;
        }
        .company-name {
            font-weight: bold;
            font-size: 10px;
        }
        .title {
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            margin: 10px 0 8px 0;
        }
        .filter-info {
            margin-bottom: 8px;
            padding: 5px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            font-size: 7px;
        }
        .filter-info h4 {
            margin: 0 0 3px 0;
            font-size: 8px;
            font-weight: bold;
        }
        .filter-info p {
            margin: 1px 0;
            font-size: 7px;
        }
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            margin-bottom: 10px;
        }
        .main-table th,
        .main-table td {
            border: 1px solid #000;
            padding: 2px 1px;
            text-align: center;
            font-size: 6px;
            vertical-align: middle;
            word-wrap: break-word;
        }
        .main-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 6px;
        }
        .section-header {
            background-color: #e0e0e0;
            font-weight: bold;
            font-size: 9px;
            text-align: center;
            padding: 3px;
            margin-top: 10px;
            margin-bottom: 5px;
            border: 2px solid #000;
        }
        .signatures {
            margin-top: 15px;
            display: table;
            width: 100%;
        }
        .signature-box {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 5px;
        }
        .signature-line {
            border-bottom: 1px solid #000;
            height: 30px;
            margin-bottom: 2px;
        }
        .no-data {
            text-align: center;
            padding: 10px;
            font-style: italic;
            color: #666;
            font-size: 7px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-logo">
            <img src="{{ public_path('dist/img/cpi-logo.png') }}" alt="CPI Logo" style="width: 40px; height: 40px; object-fit: contain;">
        </div>
        <div class="company-info">
            <div class="company-name">PT. CHAROEN POKPHAND INDONESIA</div>
            <div>FOOD DIVISION {{ strtoupper(auth()->user()->plan->nama_plan ?? 'UNKNOWN') }}</div>
            <div>{{ strtoupper(auth()->user()->plan->nama_plan ?? 'UNKNOWN') }} - INDONESIA</div>
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="title">VERIFIKASI PROSES PRODUKSI - TUMBLING PROSES</div>

    <div class="filter-info">
        <h4>Information</h4>
        <p><strong>Tanggal:</strong> 
            @if(isset($filterInfo['tanggal']) && $filterInfo['tanggal'] !== '-')
                {{ \Carbon\Carbon::parse($filterInfo['tanggal'])->format('l, d-m-Y') }}
            @else
                {{ $filterInfo['tanggal'] ?? '-' }}
            @endif
            | <strong>Shift:</strong> {{ $filterInfo['shift'] ?? '-' }} | <strong>Produk:</strong> {{ $filterInfo['produk'] ?? '-' }} | <strong>Kode Form:</strong> {{ $filterInfo['kode_form'] ?? '-' }}</p>
    </div>

    <!-- BAHAN BAKU TUMBLING SECTION -->
    <div class="section-header">1. BAHAN BAKU TUMBLING</div>
    
    @if($bahanBakuData->count() > 0)
        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 8%;">Waktu</th>
                    <th style="width: 12%;">Nama Bahan Baku</th>
                    <th style="width: 8%;">Suhu (°C)</th>
                    <th style="width: 10%;">Kode Produksi</th>
                    <th style="width: 10%;">Kode Produksi BB</th>
                    <th style="width: 8%;">Jumlah</th>
                    <th style="width: 6%;">Kondisi Daging</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bahanBakuData as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->tanggal ? $item->tanggal->format('H:i') : '-' }}</td>
                        <td>{{ $item->nama_bahan_baku ?? '-' }}</td>
                        <td>{{ $item->suhu ?? '-' }}</td>
                        <td>{{ $item->kode_produksi ?? '-' }}</td>
                        <td>{{ $item->kode_produksi_bahan_baku ?? '-' }}</td>
                        <td>{{ $item->jumlah ?? '-' }}</td>
                        <td>
                            @if($item->kondisi_daging == '✔' || $item->kondisi_daging == '&#10004;')
                                OK
                            @elseif($item->kondisi_daging == '✘' || $item->kondisi_daging == '&#10008;')
                                Tidak OK
                            @else
                                {{ $item->kondisi_daging ?? '-' }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
       
    @else
        <div class="no-data">
            <p>Tidak ada data Bahan Baku Tumbling untuk filter yang dipilih.</p>
        </div>
    @endif

    <!-- PROSES MARINADE SECTION -->
    <div class="section-header">2. PROSES MARINADE</div>
    
    @if($prosesMarinadeData->count() > 0)
        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 8%;">Waktu</th>
                    <th style="width: 25%;">Jenis Marinade</th>
                    <th style="width: 15%;">Kode Produksi</th>
                    <th style="width: 8%;">Jumlah</th>
                    <!-- <th style="width: 6%;">Shift</th> -->
                    <th style="width: 34%;">Hasil Pencampuran</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prosesMarinadeData as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->tanggal ? $item->tanggal->format('H:i') : '-' }}</td>
                        <td>{{ $item->jenisMarinade->jenis_marinade ?? '-' }}</td>
                        <td>{{ $item->kode_produksi ?? '-' }}</td>
                        <td>{{ $item->jumlah ? number_format($item->jumlah, 2) : '-' }}</td>
                        <!-- <td>{{ $item->shift->shift ?? '-' }}</td> -->
                        <td>
                            @if($item->hasil_pencampuran == '✔' || $item->hasil_pencampuran == '&#10004;')
                                OK
                            @elseif($item->hasil_pencampuran == '✘' || $item->hasil_pencampuran == '&#10008;')
                                Tidak OK
                            @else
                                {{ $item->hasil_pencampuran ?? '-' }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            <p>Tidak ada data Proses Marinade untuk filter yang dipilih.</p>
        </div>
    @endif

    <!-- PROSES TUMBLING SECTION -->
    <div class="section-header">3. PARAMETER TUMBLING</div>
    
    @if($prosesTumblingData->count() > 0)
        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 3%;">No</th>
                    <th style="width: 5%;">Waktu</th>
                    <th style="width: 4%;">Suhu</th>
                    <th style="width: 8%;">Kode Produksi</th>
                    <th style="width: 5%;">Drum On</th>
                    <th style="width: 5%;">Drum Off</th>
                    <th style="width: 5%;">Speed</th>
                    <th style="width: 6%;">Total Waktu</th>
                    <th style="width: 5%;">Vakum</th>
                    <th style="width: 7%;">Mulai Tumbling</th>
                    <th style="width: 7%;">Selesai Tumbling</th>
                    <th style="width: 7%;">Mulai Aging</th>
                    <th style="width: 7%;">Selesai Aging</th>
                    <th style="width: 6%;">Kondisi</th>
                    <!-- <th style="width: 4%;">Shift</th> -->
                </tr>
            </thead>
            <tbody>
                @foreach($prosesTumblingData as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->tanggal ? $item->tanggal->format('H:i') : '-' }}</td>
                        <td>{{ $item->suhu ?? '-' }}</td>
                        <td>{{ $item->kode_produksi ?? '-' }}</td>
                        <td>{{ $item->aktual_drum_on ?? '-' }}</td>
                        <td>{{ $item->aktual_drum_off ?? '-' }}</td>
                        <td>{{ $item->aktual_speed ? number_format($item->aktual_speed, 0) : '-' }}</td>
                        <td>{{ $item->aktual_total_waktu ?? '-' }}</td>
                        <td>{{ $item->aktual_vakum ? number_format($item->aktual_vakum, 0) : '-' }}</td>
                        <td>{{ $item->waktu_mulai_tumbling ?? '-' }}</td>
                        <td>{{ $item->waktu_selesai_tumbling ?? '-' }}</td>
                        <td>{{ $item->waktu_mulai_aging ?? '-' }}</td>
                        <td>{{ $item->waktu_selesai_aging ?? '-' }}</td>
                        <td>
                            @if($item->kondisi == '✔' || $item->kondisi == '&#10004;')
                                OK
                            @elseif($item->kondisi == '✘' || $item->kondisi == '&#10008;')
                                Tidak OK
                            @else
                                {{ $item->kondisi ?? '-' }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            <p>Tidak ada data Proses Tumbling untuk filter yang dipilih.</p>
        </div>
    @endif

    @if($bahanBakuData->count() == 0 && $prosesMarinadeData->count() == 0 && $prosesTumblingData->count() == 0)
        <div style="text-align: center; padding: 20px; font-size: 10px;">
            <p><strong>Tidak ada data yang sesuai dengan filter yang dipilih.</strong></p>
            <p>Filter yang digunakan:</p>
            <p>Tanggal: {{ $filterInfo['tanggal'] ?? '-' }}</p>
            <p>Shift: {{ $filterInfo['shift'] ?? '-' }}</p>
            <p>Produk: {{ $filterInfo['produk'] ?? '-' }}</p>
            <p>Kode Form: {{ $filterInfo['kode_form'] ?? '-' }}</p>
        </div>
    @else
        <div class="signatures">
            <div class="signature-box">
                <div style="font-size: 7px;">Dibuat Oleh:</div>
                <div class="signature-line"></div>
                <div style="font-size: 7px;">Operator</div>
            </div>
            <div class="signature-box">
                <div style="font-size: 7px;">Diperiksa Oleh:</div>
                <div class="signature-line"></div>
                <div style="font-size: 7px;">Supervisor</div>
            </div>
            <div class="signature-box">
                <div style="font-size: 7px;">Diketahui Oleh:</div>
                <div class="signature-line"></div>
                <div style="font-size: 7px;">Manager</div>
            </div>
        </div>
    @endif
</body>
</html>
