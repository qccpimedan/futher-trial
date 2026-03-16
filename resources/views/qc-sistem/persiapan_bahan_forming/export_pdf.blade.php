<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Proses Produksi - Persiapan Bahan Forming</title>
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
        .barcode-container {
            margin-bottom: 10px;
        }
        .barcode {
            font-family: 'Courier New', monospace;
            font-size: 8px;
            background-color: #f0f0f0;
            padding: 2px 4px;
            border: 1px solid #ccc;
            display: inline-block;
            margin-bottom: 5px;
        }
        .page-break {
            page-break-before: always;
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

    <div class="title">VERIFIKASI PERSIAPAN BAHAN FORMING</div>

    @foreach($data->groupBy(function($item) { return $item->tanggal->format('Y-m-d') . '_' . $item->formula->produk->nama_produk; }) as $groupKey => $groupItems)
    @php
        $firstItem = $groupItems->first();
        [$date, $productName] = explode('_', $groupKey, 2);
    @endphp
    
    @if(!$loop->first)
        <div class="page-break"></div>
    @endif

    <div class="form-info">
        <table>
            <tr>
                <td>Hari / Tanggal</td>
                <td colspan="3">: {{ \Carbon\Carbon::parse($date)->format('l, d-m-Y') }}</td>
                
            </tr>
            <tr>
                <td>NAMA PRODUK</td>
                <td colspan="3">: {{ $productName }}</td>
            </tr>
            <tr>
                <td>Shift</td>
                <td colspan="3">: {{ $firstItem->shift->shift ?? '-' }}</td>
                <!-- <td></td> -->
                <!-- <td></td> -->
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
            @php $rowNumber = 1; @endphp
            @foreach($groupItems as $item)
                @foreach($item->suhuForming as $index => $suhu)
                <tr>
                    @if($index == 0)
                        <td rowspan="{{ $item->suhuForming->count() }}">{{ $rowNumber }}</td>
                        <td rowspan="{{ $item->suhuForming->count() }}">{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</td>
                        <td rowspan="{{ $item->suhuForming->count() }}">{{ $item->kode_produksi_emulsi ?? '-' }}</td>
                        <td rowspan="{{ $item->suhuForming->count() }}">{{ is_array($item->kode_produksi_emulsi_oil) ? implode(', ', $item->kode_produksi_emulsi_oil) : ($item->kode_produksi_emulsi_oil ?? '-') }}</td>                        
                        <td rowspan="{{ $item->suhuForming->count() }}">{{ $item->formula->nomor_formula ?? '-' }}</td>
                    @endif
                    <td>{{ $suhu->bahanForming->nama_rm ?? '-' }}</td>
                    <td>{{ $suhu->kode_produksi_bahan ?? '-' }}</td>
                    <td>{{ $suhu->suhu }}°C</td>
                    @if($index == 0)
                    <td rowspan="{{ $item->suhuForming->count() }}">{{ $item->suhuAdonan->std_suhu ?? '-' }}</td>
                    <td rowspan="{{ $item->suhuForming->count() }}">{{ $item->rework ?? '-' }}</td>
                        <td rowspan="{{ $item->suhuForming->count() }}">
                            {{ $item->aktualSuhuAdonan ? $item->aktualSuhuAdonan->aktual_suhu_1 : '-' }}
                        </td>
                        <td rowspan="{{ $item->suhuForming->count() }}">
                            {{ $item->aktualSuhuAdonan ? $item->aktualSuhuAdonan->aktual_suhu_2 : '-' }}
                        </td>
                        <td rowspan="{{ $item->suhuForming->count() }}">
                            {{ $item->aktualSuhuAdonan ? $item->aktualSuhuAdonan->aktual_suhu_3 : '-' }}
                        </td>
                        <td rowspan="{{ $item->suhuForming->count() }}">
                            {{ $item->aktualSuhuAdonan ? $item->aktualSuhuAdonan->aktual_suhu_4 : '-' }}
                        </td>
                        <td rowspan="{{ $item->suhuForming->count() }}">
                            {{ $item->aktualSuhuAdonan ? $item->aktualSuhuAdonan->aktual_suhu_5 : '-' }}
                        </td>
                        <td rowspan="{{ $item->suhuForming->count() }}">
                            {{ $item->aktualSuhuAdonan ? number_format($item->aktualSuhuAdonan->total_aktual_suhu, 1) : '-' }}
                        </td>
                        <td rowspan="{{ $item->suhuForming->count() }}">
                            {{ $item->waktu_mulai_mixing ?? '-' }} - {{ $item->waktu_selesai_mixing ?? '-' }}
                        </td>
                        <td rowspan="{{ $item->suhuForming->count() }}">
                            @if($item->kondisi == '✔' || $item->kondisi == '&#10004;')
                                OK
                            @elseif($item->kondisi == '✘' || $item->kondisi == '&#10008;')
                                Tidak OK
                            @else
                                {{ $item->kondisi ?? '-' }}
                            @endif
                        </td>
                        <td rowspan="{{ $item->suhuForming->count() }}">{{ $item->catatan ?? '-' }}</td>
                    @endif
                </tr>
                @endforeach
                @php $rowNumber++; @endphp
            @endforeach
        </tbody>
    </table>
         <div style="width: 100%; text-align: right; margin-top: 10px; font-style: italic;">
            <span>QF 04/00</span>
        </div>
    <div class="signatures">
        <div class="signature-box">
            <div>Dibuat Oleh:</div>
            @php
                $qcApprover = null;
                $qcApproverName = 'QC';
                
                // Cari QC approver dari data yang ada
                foreach($groupItems as $item) {
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
                        foreach($groupItems as $item) {
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
                foreach($groupItems as $item) {
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
                        foreach($groupItems as $item) {
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
                $produksiApproverName = 'FM/FL PRODUKSI';
                
                // Cari Produksi approver dari data yang ada
                foreach($groupItems as $item) {
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
                        foreach($groupItems as $item) {
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
    @endforeach
</body>
</html>
