<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Proses Produksi - Roasting</title>
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
        .roasting-fan-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .roasting-fan-table th,
        .roasting-fan-table td {
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: center;
            font-size: 7px;
            vertical-align: middle;
        }
        .roasting-fan-table th {
            background-color: #343a40;
            color: white;
            font-weight: bold;
        }
        .roasting-fan-table .label-cell {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: left;
            padding-left: 8px;
            width: 250px;
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

    <div class="title">VERIFIKASI PROSES ROASTING/ STEAMING</div>

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
                    <td>Produk</td>
                    @php
                        $produkName = '-';
                        $beratProduk = '';
                        
                        // KONDISI 2: Prioritas untuk Alur Input Roasting
                        
                        // 1. Cari dari Input Roasting terlebih dahulu (sumber utama)
                        foreach($data as $item) {
                            if($item->inputRoastingData && $item->inputRoastingData->produk) {
                                $produkName = $item->inputRoastingData->produk->nama_produk;
                                if($item->inputRoastingData->berat_produk) {
                                    $beratProduk = ' (' . $item->inputRoastingData->berat_produk . ' gram)';
                                }
                                break;
                            }
                        }
                        
                        // 2. Fallback ke Proses Roasting Fan jika Input Roasting tidak ada
                        if($produkName == '-') {
                            foreach($data as $item) {
                                if($item->prosesRoastingFanData && $item->prosesRoastingFanData->produk) {
                                    $produkName = $item->prosesRoastingFanData->produk->nama_produk;
                                    break;
                                }
                            }
                        }
                        
                        // 3. Fallback terakhir ke data utama
                        if($produkName == '-' && $firstItem->produk) {
                            $produkName = $firstItem->produk->nama_produk;
                        }
                    @endphp
                    <td colspan="3">: {{ $produkName }}{{ $beratProduk }}</td>                </tr>
            </table>
        </div>

        <!-- A. INPUT ROASTING -->
        <h3 class="section-title">A. PROSES ROASTING</h3>
        <table class="main-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Waktu</th>
                    <!-- <th>Produk</th> -->
                    <th>Kode Produksi</th>
                    <th>Berat Produk (gr)</th>
                    <th>Waktu Pemasakan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                    @if($item->inputRoastingData)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->inputRoastingData->tanggal ? \Carbon\Carbon::parse($item->inputRoastingData->tanggal)->format('H:i') : '-' }}</td>
                            <!-- <td>{{ $item->inputRoastingData->produk->nama_produk ?? '-' }}</td> -->
                            <td>{{ $item->inputRoastingData->kode_produksi ?? '-' }}</td>
                            <td>{{ $item->inputRoastingData->berat_produk ?? '-' }}</td>
                            <td>{{ $item->inputRoastingData->waktu_pemasakan ?? '-' }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        <!-- B. PROSES ROASTING/STEAMING FAN -->
        <h3 class="section-title">B. PROSES ROASTING/STEAMING FAN</h3>
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
            
            // Urutkan berdasarkan block_number (INFEED dulu, lalu OUTFEED)
            $sessionRecords = $sessionRecords->sortBy('block_number');
            
            // Ambil data std_fan untuk produk ini
            $stdFanData = null;
            if($data->isNotEmpty()) {
                $firstRecord = $data->first();
                
                // Cari produk dari berbagai sumber
                $produkId = null;
                if($firstRecord->inputRoastingData && $firstRecord->inputRoastingData->produk) {
                    $produkId = $firstRecord->inputRoastingData->produk->id;
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
                        <th colspan="2">Lama Proses (Menit)</th>
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
                        @php $groupNumber = 1; @endphp
                        @foreach($sessionRecords as $index => $record)
                        @php
                            $isFirstInGroup = ($index == 0 || ($sessionRecords[$index-1]->id != $record->id));
                            if($isFirstInGroup && $index > 0) $groupNumber++;
                            
                            // Hitung berapa baris dalam grup ini (biasanya 2: INFEED dan OUTFEED)
                            $rowsInGroup = 0;
                            $currentId = $record->id;
                            for($i = $index; $i < $sessionRecords->count(); $i++) {
                                if($sessionRecords[$i]->id == $currentId) {
                                    $rowsInGroup++;
                                } else {
                                    break;
                                }
                            }
                        @endphp
                        <tr>
                            @if($isFirstInGroup)
                                <td rowspan="{{ $rowsInGroup }}">{{ $groupNumber }}</td>
                            @endif
                            @if($isFirstInGroup)
                                <td rowspan="{{ $rowsInGroup }}">
                                    {{ isset($record->tanggal) ? \Carbon\Carbon::parse($record->tanggal)->format('H:i') : '-' }}
                                </td>
                            @endif
                            
                            <td>{{ $record->block_number ?? 'Blok ' . ($index + 1) }}</td>

                            <!-- Suhu Pemasakan: Std dan Aktual -->
                            @if($isFirstInGroup)
                                <td rowspan="{{ $rowsInGroup }}">{{ $stdFanData && $stdFanData->suhuBlok ? $stdFanData->suhuBlok->suhu_blok : '-' }}</td>
                            @endif
                            <td>{{ $record->suhu_roasting ?? '-' }}</td>

                            <!-- Fan 1: Std dan Aktual -->
                            @if($isFirstInGroup)
                                <td rowspan="{{ $rowsInGroup }}">{{ $stdFanData->std_fan ?? '-' }}</td>
                            @endif
                            <td>{{ $record->fan_1 ?? '-' }}</td>

                            <!-- Fan 2: Std dan Aktual -->
                            @if($isFirstInGroup)
                                <td rowspan="{{ $rowsInGroup }}">{{ $stdFanData->std_fan_2 ?? '-' }}</td>
                            @endif
                            <td>{{ $record->fan_2 ?? '-' }}</td>

                            <!-- Fan 3: Std dan Aktual -->
                            @if($isFirstInGroup)
                                <td rowspan="{{ $rowsInGroup }}">{{ $stdFanData->fan_3 ?? '-' }}</td>
                            @endif
                            <td>{{ $record->fan_3 ?? '-' }}</td>

                            <!-- Fan 4: Std dan Aktual -->
                            @if($isFirstInGroup)
                                <td rowspan="{{ $rowsInGroup }}">{{ $stdFanData->fan_4 ?? '-' }}</td>
                            @endif
                            <td>{{ $record->fan_4 ?? '-' }}</td>

                            <!-- Humidity/Steam Valve: Std dan Aktual -->
                            @if($isFirstInGroup)
                                <td rowspan="{{ $rowsInGroup }}">{{ $stdFanData->std_humadity ?? '-' }}</td>
                            @endif
                            <td>{{ $record->aktual_humadity ?? '-' }}</td>

                            <!-- Infra Red (tetap sama) -->
                            <td>{{ $record->infra_red ?? '-' }}</td>

                            <!-- Lama Proses: Std dan Aktual -->
                            @if($isFirstInGroup)
                                <td rowspan="{{ $rowsInGroup }}">{{ $stdFanData->std_lama_proses ?? '-' }}</td>
                                <td rowspan="{{ $rowsInGroup }}">{{ $record->aktual_lama_proses ?? '-' }}</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="text-align: center; padding: 10px;">Tidak ada data Proses Roasting Fan</p>
        @endif
        <!-- D. HASIL PROSES ROASTING -->
        <h3 class="section-title">D. HASIL PROSES ROASTING</h3>
        <table class="main-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <!-- <th>Produk</th> -->
                    <th>Std Suhu Pusat (°C)</th>
                    <th>Aktual Suhu Pusat (°C)</th>
                    <th>Sensori</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                    @if($item->hasilProsesRoastingData)
                        @php
                            // Parse aktual_suhu_pusat JSON
                            $suhuArray = is_string($item->hasilProsesRoastingData->aktual_suhu_pusat)
                                ? json_decode($item->hasilProsesRoastingData->aktual_suhu_pusat, true)
                                : [$item->hasilProsesRoastingData->aktual_suhu_pusat];
                            $suhuArray = is_array($suhuArray) ? $suhuArray : [];
                            $suhuDisplay = count($suhuArray) > 0 ? implode(', ', $suhuArray) : '-';
                            
                            // Parse sensori JSON
                            $sensoriData = is_array($item->hasilProsesRoastingData->sensori) 
                                ? $item->hasilProsesRoastingData->sensori 
                                : [];
                            
                            // Tentukan status sensori (OK jika semua parameter OK, Tidak OK jika ada yang Tidak OK)
                            // Handle format baru: sensori_direct_*, sensori_[timestamp]_*
                            $sensoriStatus = '-';
                            if(count($sensoriData) > 0) {
                                $sensoriValues = [];
                                foreach($sensoriData as $key => $value) {
                                    // Extract nilai dari format apapun
                                    if(is_string($value)) {
                                        $sensoriValues[] = $value;
                                    }
                                }
                                
                                if(count($sensoriValues) > 0) {
                                    $hasNotOk = false;
                                    $allOk = true;
                                    
                                    foreach($sensoriValues as $val) {
                                        if($val == 'Tidak OK') {
                                            $hasNotOk = true;
                                            $allOk = false;
                                        }
                                    }
                                    
                                    if($hasNotOk) {
                                        $sensoriStatus = 'Tidak OK';
                                    } elseif($allOk) {
                                        $sensoriStatus = 'OK';
                                    }
                                }
                            }
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->hasilProsesRoastingData->tanggal ? \Carbon\Carbon::parse($item->hasilProsesRoastingData->tanggal)->format('d-m-Y') : '-' }}</td>
                            <td>{{ $item->hasilProsesRoastingData->jam ?? '-' }}</td>
                            <!-- <td>{{ $item->hasilProsesRoastingData->produk->nama_produk ?? '-' }}</td> -->
                            <td>{{ $item->hasilProsesRoastingData->stdSuhuPusat->std_suhu_pusat_roasting ?? '-' }}°C</td>
                            <td>{{ $suhuDisplay }}°C</td>
                            <td>{{ $sensoriStatus }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        <!-- E. PEMBEKUAN IQF ROASTING -->
        <h3 class="section-title">E. PEMBEKUAN IQF ROASTING</h3>
        <table class="main-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Waktu</th>
                    <th>Suhu Ruang IQF (°C)</th>
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
                        <!-- <td>{{ $item->user ? $item->user->name : '-' }}</td> -->
                    </tr>
                @endforeach
            </tbody>
        </table>
     <div style="width: 100%; text-align: right; margin-top: 10px;">
            <span>{{ $request->kode_form ?? $firstItem->kode_form ?? '-' }}</span>
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