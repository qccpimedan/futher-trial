<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Proses Produksi - Penggorengan</title>
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
        .section-title {
            margin-top: 20px;
            margin-bottom: 10px;
            color: #333;
            font-size: 12px;
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
        .barcode-container {
            margin-bottom: 5px;
        }
        .barcode {
            font-family: 'Courier New', monospace;
            font-size: 8px;
            background-color: #f0f0f0;
            padding: 2px;
            border: 1px solid #ccc;
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

<div class="title">VERIFIKASI PROSES PRODUKSI - PENGGORENGAN</div>

    <!-- Flow Information -->
    <!-- <div class="flow-info">
        <strong>🔥 ALUR PENGGORENGAN:</strong><br>
        Penggorengan → Predust → Battering → Breader → Frayer → Roasting Fan → Hasil Roasting → Pembekuan IQF
    </div> -->
    @if($data->count() > 0)
    @php
        $firstItem = $data->first();
    @endphp

    <div class="form-info">
        <table>
            <tr>
                <td style="width: 150px;">Hari / Tanggal</td>
                <td colspan="3">: {{ $firstItem->tanggal ? \Carbon\Carbon::parse($firstItem->tanggal)->format('l, d-m-Y') : '-' }}</td>
            </tr>
            <tr>
                <td>Shift</td>
                <td colspan="3">: {{ $firstItem->shift_data ? $firstItem->shift_data->shift : '-' }}</td>
            </tr>
            <tr>
                <td>Kode Form</td>
                <td colspan="3">: {{ $request->kode_form ?? $firstItem->kode_form ?? '-' }}</td>
            </tr>
            <tr>
                <td>Produk</td>
                @php
                    $produkName = '-';
                    $beratProduk = '';
                    
                    // KONDISI 1: Prioritas untuk Alur Penggorengan
                    
                    // 1. Cari dari Penggorengan terlebih dahulu (sumber utama)
                    foreach($data as $item) {
                        if($item->penggorenganData && $item->penggorenganData->produk) {
                            $produkName = $item->penggorenganData->produk->nama_produk;
                            if($item->penggorenganData->berat_produk) {
                                $beratProduk = ' (' . $item->penggorenganData->berat_produk . ' gram)';
                            }
                            break;
                        }
                    }
                    
                    // 2. Fallback ke Predust jika Penggorengan tidak ada
                    if($produkName == '-') {
                        foreach($data as $item) {
                            if($item->predustData && $item->predustData->produk) {
                                $produkName = $item->predustData->produk->nama_produk;
                                break;
                            }
                        }
                    }
                    
                    // 3. Fallback ke Input Roasting untuk berat jika belum ada
                    if($beratProduk == '') {
                        foreach($data as $item) {
                            if($item->inputRoastingData && $item->inputRoastingData->berat_produk) {
                                $beratProduk = ' (' . $item->inputRoastingData->berat_produk . ' gram)';
                                break;
                            }
                        }
                    }
                    
                    // 4. Fallback terakhir ke data utama
                    if($produkName == '-' && $firstItem->produk) {
                        $produkName = $firstItem->produk->nama_produk;
                    }
                @endphp
                <td colspan="3">: {{ $produkName }}{{ $beratProduk }}</td>
            </tr>
        </table>
    </div>

        <!-- A. PENGGORENGAN -->
        <h3 class="section-title">A. PENGGORENGAN</h3>
        <table class="main-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Waktu</th>
                    <th>Kode Produksi</th>
                    <th>No Of Strokes</th>
                    <th>Waktu Pemasakan</th>
                    <th>Hasil Pencetakan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                    @if($item->penggorenganData)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->penggorenganData->tanggal ? \Carbon\Carbon::parse($item->penggorenganData->tanggal)->format('H:i') : '-' }}</td>
                            <td>{{ $item->penggorenganData->kode_produksi ?? '-' }}</td>
                            <td>{{ $item->penggorenganData->no_of_strokes ?? '-' }}</td>
                            <td>{{ $item->penggorenganData->waktu_pemasakan ?? '-' }} Detik</td>
                            <td>
                                @if($item->penggorenganData->hasil_pencetakan == '✔')
                                    OK
                                @elseif($item->penggorenganData->hasil_pencetakan == '✘')
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

        <!-- B. PREDUST -->
        <h3 class="section-title">B. PREDUST</h3>
        <table class="main-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Waktu</th>
                    <th>Kode Produksi</th>
                    <th>Jenis Predust</th>
                    <th>Kondisi Predust</th>
                    <th>Hasil Pencetakan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                    @if($item->predustData)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->predustData->tanggal ? \Carbon\Carbon::parse($item->predustData->tanggal)->format('H:i') : '-' }}</td>
                            <td>{{ $item->predustData->kode_produksi ?? '-' }}</td>
                            <td>{{ $item->predustData->jenisPredust->jenis_predust ?? '-' }}</td>
                            <td>{{ $item->predustData->kondisi_predust ?? '-' }}</td>
                            <td>{{ $item->predustData->hasil_pencetakan ?? '-' }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        <!-- C. BATTERING -->
        <h3 class="section-title">C. BATTERING</h3>
        <table class="main-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Waktu</th>
                    <th>Kode Produksi Better</th>
                    <th>Jenis Better</th>
                   <th>Hasil Better</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                    @if($item->batteringData)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->batteringData->tanggal ? \Carbon\Carbon::parse($item->batteringData->tanggal)->format('H:i') : '-' }}</td>
                            <td>{{ $item->batteringData->kode_produksi_better ?? '-' }}</td>
                            <td>{{ $item->batteringData->jenis_better->nama_better ?? '-' }}</td>
                            <td>
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

        <!-- D. BREADER -->
        <h3 class="section-title">D. BREADER</h3>
        <table class="main-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Waktu</th>
                    <th>Kode Produksi</th>
                    <th>Jenis Breader</th>
                    <th>Hasil Breader</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                    @if($item->breaderData)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->breaderData->tanggal ? \Carbon\Carbon::parse($item->breaderData->tanggal)->format('H:i') : '-' }}</td>
                            <td>{{ $item->breaderData->kode_produksi ?? '-' }}</td>
                            <td>{{ $item->breaderData->jenisBreader->jenis_breader ?? '-' }}</td>
                            <td>
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
        <!-- E. FRAYER -->
        <h3 class="section-title">E. Fryer</h3>
        <table class="main-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Waktu</th>
                    <th>Fryer</th>
                    <th>Standart Suhu Fryer</th>
                    <th>Aktual Suhu Fryer</th>
                    <th>Standart Waktu Penggorengan</th>
                    <th>Aktual Waktu Penggorengan</th>
                    <th>TPM Minyak</th>
                </tr>
            </thead>
            <tbody>
            @foreach($data as $index => $item)
                @if($item->penggorenganData)
                @php
                    $frayerNumber = 1; // default
                    $frayerName = 'Frayer 1'; // default
                    
                    // Gunakan penggorengan_uuid dari database
                    $penggorenganUuid = $item->penggorenganData->penggorengan_uuid ?? null;
                    
                    if($penggorenganUuid) {
                        // Cek di semua model frayer menggunakan penggorengan_uuid
                        $frayer1 = \App\Models\ProsesFrayer::where('penggorengan_uuid', $penggorenganUuid)->first();
                        $frayer2 = \App\Models\Frayer2::where('penggorengan_uuid', $penggorenganUuid)->first();
                        $frayer3 = \App\Models\Frayer3::where('penggorengan_uuid', $penggorenganUuid)->first();
                        $frayer4 = \App\Models\Frayer4::where('penggorengan_uuid', $penggorenganUuid)->first();
                        $frayer5 = \App\Models\Frayer5::where('penggorengan_uuid', $penggorenganUuid)->first();
                        
                        if($frayer1) {
                            $frayerNumber = 1;
                            $frayerName = 'Frayer 1';
                        } elseif($frayer2) {
                            $frayerNumber = 2;
                            $frayerName = 'Frayer 2';
                        } elseif($frayer3) {
                            $frayerNumber = 3;
                            $frayerName = 'Frayer 3';
                        } elseif($frayer4) {
                            $frayerNumber = 4;
                            $frayerName = 'Frayer 4';
                        } elseif($frayer5) {
                            $frayerNumber = 5;
                            $frayerName = 'Frayer 5';
                        }
                    }
                @endphp
                    <tr>
                        <td>{{ $index + 1 }}.{{ $frayerNumber }}</td>
                        <td>{{ $item->penggorenganData->tanggal ? \Carbon\Carbon::parse($item->penggorenganData->tanggal)->format('H:i') : '-' }}</td>
                        <td>{{ $frayerName }}</td>
                        <td>{{ $item->penggorenganData->suhuFrayer->suhu_frayer ?? '-' }}°C</td>
                        <td>{{ $item->penggorenganData->aktual_suhu_penggorengan ?? '-' }}°C</td>
                        <td>{{ $item->penggorenganData->waktuPenggorengan->waktu_penggorengan ?? '-' }}s</td>
                        <td>{{ $item->penggorenganData->waktuPenggorengan->aktual_penggorengan ?? '-' }}s</td>
                        <td>{{ $item->penggorenganData->tpm_minyak ?? '-' }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
        </table>
        <!-- F. ROASTING FAN -->
        <h3 class="section-title">F. ROASTING FAN</h3>
        @php
            // Kumpulkan semua data roasting fan dari berbagai sumber
            $sessionRecords = collect();
            
            foreach($data as $item) {
                if($item->prosesRoastingFanData) {
                    $record = $item->prosesRoastingFanData;
                    
                    // Jika ada blok_data (JSON), parse dan tambahkan ke collection
                    if($record->blok_data) {
                        $blokData = is_string($record->blok_data) ? json_decode($record->blok_data, true) : $record->blok_data;
                        
                        if(is_array($blokData)) {
                            foreach($blokData as $blokKey => $blokItem) {
                                if(is_array($blokItem)) {
                                    $blockRecord = (object) [
                                        'id' => $record->id,
                                        'tanggal' => $record->tanggal,
                                        'produk' => $record->produk,
                                        'block_number' => $blokItem['block_number'] ?? $blokKey,
                                        'suhu_roasting' => $blokItem['suhu_roasting'] ?? '',
                                        'fan_1' => $blokItem['fan_1'] ?? '',
                                        'fan_2' => $blokItem['fan_2'] ?? '',
                                        'fan_3' => $blokItem['fan_3'] ?? '',
                                        'fan_4' => $blokItem['fan_4'] ?? '',
                                        'aktual_humadity' => $blokItem['aktual_humadity'] ?? '',
                                        'infra_red' => $blokItem['infra_red'] ?? '',
                                        'aktual_lama_proses' => $record->aktual_lama_proses ?? ''
                                    ];
                                    
                                    $sessionRecords->push($blockRecord);
                                }
                            }
                        }
                    }
                }
            }
            
            // PENTING: Urutkan berdasarkan ID dulu, lalu block_number
            $sessionRecords = $sessionRecords->sortBy([
                ['id', 'asc'],
                ['block_number', 'asc']
            ]);
            
            // Ambil data std_fan untuk produk ini
            $stdFanData = null;
            if($data->isNotEmpty()) {
                $firstRecord = $data->first();
                
                // Cari produk dari berbagai sumber
                $produkId = null;
                if($firstRecord->penggorenganData && $firstRecord->penggorenganData->produk) {
                    $produkId = $firstRecord->penggorenganData->produk->id;
                } elseif($firstRecord->prosesRoastingFanData && $firstRecord->prosesRoastingFanData->produk) {
                    $produkId = $firstRecord->prosesRoastingFanData->produk->id;
                }
                
                // Ambil std_fan berdasarkan produk
                if($produkId) {
                    $stdFanData = \App\Models\StdFan::where('id_produk', $produkId)->first();
                }
            }
        @endphp
        @if($sessionRecords->count() > 0)
            <table class="main-table">
                <thead>
                    <tr>
                        <th rowspan="2">No</th>
                        <th rowspan="2">Waktu</th>
                        <th rowspan="2">Blok</th>
                        <th colspan="2">Suhu Pemasakan (°C)</th>
                        <th colspan="2">Fan 1 (%)</th>
                        <th colspan="2">Fan 2 (%)</th>
                        <th colspan="2">Fan 3 (%)</th>
                        <th colspan="2">Fan 4 (%)</th>
                        <th colspan="2">Humidity/Steam Valve (%)</th>
                        <th rowspan="2">Infra Red</th>
                        <th colspan="2">Lama Proses (menit)</th>
                    </tr>
                    
                    <tr>
                        <th>Std</th>
                        <th>Aktual</th>
                        <th>Std</th>
                        <th>Aktual</th>
                        <th>Std</th>
                        <th>Aktual</th>
                        <th>Std</th>
                        <th>Aktual</th>
                        <th>Std</th>
                        <th>Aktual</th>
                        <th>Std</th>
                        <th>Aktual</th>
                        <th>Std</th>
                        <th>Aktual</th>
                    </tr>
                </thead>
                <tbody>
            @php 
                $groupedRecords = $sessionRecords->groupBy('id');
                $groupNumber = 1;
            @endphp
            
            @foreach($groupedRecords as $groupId => $records)
                @foreach($records as $recordIndex => $record)
                    <tr>
                        @if($recordIndex == 0)
                            <td rowspan="{{ $records->count() }}">{{ $groupNumber }}</td>
                            <td rowspan="{{ $records->count() }}">
                                {{ $record->tanggal ? \Carbon\Carbon::parse($record->tanggal)->format('H:i') : '-' }}
                            </td>
                        @endif
                        
                        <td>{{ $record->block_number }}</td>
                        <td>{{ $stdFanData && $stdFanData->suhuBlok ? $stdFanData->suhuBlok->suhu_blok : '-' }}</td>
                        <td>{{ $record->suhu_roasting ?? '-' }}</td>
                        <td>{{ $stdFanData->std_fan ?? '-' }}</td>
                        <td>{{ $record->fan_1 ?? '-' }}</td>
                        <td>{{ $stdFanData->std_fan_2 ?? '-' }}</td>
                        <td>{{ $record->fan_2 ?? '-' }}</td>
                        <td>{{ $stdFanData->fan_3 ?? '-' }}</td>
                        <td>{{ $record->fan_3 ?? '-' }}</td>
                        <td>{{ $stdFanData->fan_4 ?? '-' }}</td>
                        <td>{{ $record->fan_4 ?? '-' }}</td>
                        <td>{{ $stdFanData->std_humadity ?? '-' }}</td>
                        <td>{{ $record->aktual_humadity ?? '-' }}</td>
                        <td>{{ $record->infra_red ?? '-' }}</td>
                        @if($recordIndex == 0)
                        <td rowspan="{{ $records->count() }}">{{ $stdFanData->std_lama_proses ?? '-' }}</td>
                        <td rowspan="{{ $records->count() }}">{{ $record->aktual_lama_proses ?? '-' }}</td>
                        @endif
                    </tr>
                @endforeach
                @php $groupNumber++; @endphp
            @endforeach
        </tbody>
            </table>
        @else
            <p>Tidak ada data Proses Roasting Fan yang tersedia.</p>
        @endif

        <!-- G. HASIL ROASTING -->
        <h3 class="section-title">G. HASIL PROSES ROASTING</h3>
        <table class="main-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Waktu</th>
                    <th>STD Suhu Pusat</th>
                    <th>Aktual Suhu Pusat</th>
                    <th>Sensori</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                    @if($item->hasilProsesRoastingData)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->hasilProsesRoastingData->tanggal ? \Carbon\Carbon::parse($item->hasilProsesRoastingData->tanggal)->format('H:i') : '-' }}</td>
                            <td>{{ $item->hasilProsesRoastingData->stdSuhuPusat->std_suhu_pusat_roasting ?? '-' }}</td>
                            <td>{{ $item->hasilProsesRoastingData->aktual_suhu_pusat ?? '-' }}</td>
                            <td>
                                @if($item->hasilProsesRoastingData->sensori == '✔' || $item->hasilProsesRoastingData->sensori == '&#10004;')
                                    OK
                                @elseif($item->hasilProsesRoastingData->sensori == '✘' || $item->hasilProsesRoastingData->sensori == '&#10008;')
                                    Tidak OK
                                @else
                                    {{ $item->hasilProsesRoastingData->sensori ?? '-' }}
                                @endif
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        <!-- H. PEMBEKUAN IQF -->
        <h3 class="section-title">H. PEMBEKUAN IQF ROASTING</h3>
        <table class="main-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Waktu</th>
                    <th>Suhu Ruang IQF</th>
                    <th>Holding Time</th>
                    <!-- <th>Operator</th> -->
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('H:i') : '-' }}</td>
                        <td>{{ $item->suhu_ruang_iqf ?? '-' }}</td>
                        <td>{{ $item->holding_time ?? '-' }}</td>
                        <!-- <td>{{ $item->user->name ?? '-' }}</td> -->
                    </tr>
                @endforeach
            </tbody>
        </table>

    @else
        <p style="text-align: center; margin-top: 50px; font-size: 14px;">
            Tidak ada data untuk ditampilkan.
        </p>
    @endif

    <!-- Signature Section -->
    <div class="signatures">
        <div class="signature-box">
            <div>Dibuat Oleh:</div>
            @php
                $qcApprover = null;
                $qcApproverName = 'QC';
                
                // Cari QC approver dari data yang ada
                foreach($data as $item) {
                    if($item->approved_by_qc && $item->qc_approved_by) {
                        $qcApprover = \App\Models\User::find($item->qc_approved_by);
                        if($qcApprover) {
                            $qcApproverName = $qcApprover->name;
                            break;
                        }
                    }
                }
            @endphp
            @if($qcApprover)
                <div class="barcode-container">
                    @php
                        $qcApprovalDate = '';
                        foreach($data as $item) {
                            if($item->approved_by_qc && $item->qc_approved_at) {
                                $qcApprovalDate = $item->qc_approved_at->format('Y-m-d');
                                break;
                            }
                        }
                        $barcodeText = "disetujui oleh {$qcApprover->name}";
                        $barcodeData = strtoupper(str_replace(' ', '-', "QC-{$qcApprover->name}-{$qcApprovalDate}"));
                    @endphp
                    <div class="barcode">
                        {{ $barcodeData }}
                    </div>
                </div>
            @endif
            <div class="signature-line"></div>
            <div>{{ $qcApproverName }}</div>
        </div>
        <div class="signature-box">
            <div>Diperiksa Oleh:</div>
            @php
                $spvApproverForDiperiksa = null;
                $spvApproverNameForDiperiksa = 'SPV';
                
                // Cari SPV approver dari data yang ada
                foreach($data as $item) {
                    if($item->approved_by_spv && $item->spv_approved_by) {
                        $spvApproverForDiperiksa = \App\Models\User::find($item->spv_approved_by);
                        if($spvApproverForDiperiksa) {
                            $spvApproverNameForDiperiksa = $spvApproverForDiperiksa->name;
                            break;
                        }
                    }
                }
            @endphp
            @if($spvApproverForDiperiksa)
                <div class="barcode-container">
                    @php
                        $spvApprovalDateForDiperiksa = '';
                        foreach($data as $item) {
                            if($item->approved_by_spv && $item->spv_approved_at) {
                                $spvApprovalDateForDiperiksa = $item->spv_approved_at->format('Y-m-d');
                                break;
                            }
                        }
                        $barcodeText = "disetujui oleh {$spvApproverForDiperiksa->name}";
                        $barcodeData = strtoupper(str_replace(' ', '-', "SPV-{$spvApproverForDiperiksa->name}-{$spvApprovalDateForDiperiksa}"));
                    @endphp
                    <div class="barcode">
                        {{ $barcodeData }}
                    </div>
                </div>
            @endif
            <div class="signature-line"></div>
            <div>{{ $spvApproverNameForDiperiksa }}</div>
        </div>
        <div class="signature-box">
            <div>Diketahui Oleh:</div>
            @php
                $produksiApprover = null;
                $produksiApproverName = 'Produksi';
                
                // Cari Produksi approver dari data yang ada
                foreach($data as $item) {
                    if($item->approved_by_produksi && $item->produksi_approved_by) {
                        $produksiApprover = \App\Models\User::find($item->produksi_approved_by);
                        if($produksiApprover) {
                            $produksiApproverName = $produksiApprover->name;
                            break;
                        }
                    }
                }
            @endphp
            @if($produksiApprover)
                <div class="barcode-container">
                    @php
                        $produksiApprovalDate = '';
                        foreach($data as $item) {
                            if($item->approved_by_produksi && $item->produksi_approved_at) {
                                $produksiApprovalDate = $item->produksi_approved_at->format('Y-m-d');
                                break;
                            }
                        }
                        $barcodeText = "disetujui oleh {$produksiApprover->name}";
                        $barcodeData = strtoupper(str_replace(' ', '-', "PRODUKSI-{$produksiApprover->name}-{$produksiApprovalDate}"));
                    @endphp
                    <div class="barcode">
                        {{ $barcodeData }}
                    </div>
                </div>
            @endif
            <div class="signature-line"></div>
            <div>{{ $produksiApproverName }}</div>
        </div>
    </div>

    <!-- Footer -->
    <div style="margin-top: 30px; text-align: right; font-size: 9px;">
        <p>Dicetak pada: {{ now()->format('d-m-Y H:i:s') }}</p>
        <p>Total Records: {{ $data->count() }}</p>
    </div>
</body>
</html>