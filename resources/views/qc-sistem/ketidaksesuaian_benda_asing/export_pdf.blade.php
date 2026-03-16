<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Proses Produksi - Ketidaksesuaian Benda Asing</title>
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
        .doc-image {
            max-width: 80px;
            max-height: 60px;
            margin: 2px;
            border: 1px solid #ddd;
        }
        .image-container {
            text-align: center;
            padding: 2px;
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

    <div class="title">PEMERIKSAAN KONTAMINASI BENDA ASING</div>

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
                    <td colspan="3">: {{ $firstItem->shift->shift ?? '-' }}</td>
                </tr>
                
            </table>
        </div>

        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 8%;">Waktu</th>
                    <th style="width: 6%;">Shift</th>
                    <th style="width: 12%;">Produk</th>
                    <th style="width: 10%;">Kode Produksi</th>
                    <th style="width: 12%;">Jenis Kontaminan</th>
                    <th style="width: 8%;">Jumlah Terdampak</th>
                    <th style="width: 10%;">Tahapan</th>
                    <th style="width: 30%;">Dokumentasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</td>
                        <td>{{ $item->shift->shift ?? '-' }}</td>
                        <td style="text-align: left; padding: 4px;">{{ $item->produk->nama_produk ?? '-' }}</td>
                        <td>{{ $item->kode_produksi ?? '-' }}</td>
                        <td style="text-align: left; padding: 4px;">{{ $item->jenis_kontaminan ?? '-' }}</td>
                        <td>{{ $item->jumlah_produk_terdampak ?? '-' }} pcs</td>
                        <td style="text-align: left; padding: 4px;">{{ $item->tahapan ?? '-' }}</td>
                        <td class="image-container">
                            @if($item->dokumentasi)
                                <img src="{{ public_path('storage/' . $item->dokumentasi) }}" 
                                     alt="Dokumentasi" class="doc-image">
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
         <div style="width: 100%; text-align: right; margin-top: 10px;">
            <span>{{ $kode_form ?? $firstItem->kode_form ?? '-' }}</span>
        </div>

        <div class="signatures">
            <div class="signature-box">
                <div>Dibuat Oleh:</div>
                @php
                    $qcApprover = null;
                    $qcApproverName = 'QC';
                    $allQcApproved = true;
                    
                    // Cek apakah SEMUA data sudah di-approve oleh QC
                    foreach($data as $item) {
                        if(!$item->approved_by_qc) {
                            $allQcApproved = false;
                            break;
                        }
                    }
                    
                    // Jika semua sudah di-approve QC, cari approver pertama
                    if($allQcApproved && count($data) > 0) {
                        foreach($data as $item) {
                            if($item->approved_by_qc && $item->qc_approved_by) {
                                $qcApprover = \App\Models\User::find($item->qc_approved_by);
                                if($qcApprover) {
                                    $qcApproverName = $qcApprover->name;
                                    break;
                                }
                            }
                        }
                    }
                @endphp
                @if($qcApprover && $allQcApproved)
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
                    $allSpvApproved = true;
                    
                    // Cek apakah SEMUA data sudah di-approve oleh SPV
                    foreach($data as $item) {
                        if(!$item->approved_by_spv) {
                            $allSpvApproved = false;
                            break;
                        }
                    }
                    
                    // Jika semua sudah di-approve SPV, cari approver pertama
                    if($allSpvApproved && count($data) > 0) {
                        foreach($data as $item) {
                            if($item->approved_by_spv && $item->spv_approved_by) {
                                $spvApproverForDiperiksa = \App\Models\User::find($item->spv_approved_by);
                                if($spvApproverForDiperiksa) {
                                    $spvApproverNameForDiperiksa = $spvApproverForDiperiksa->name;
                                    break;
                                }
                            }
                        }
                    }
                @endphp
                @if($spvApproverForDiperiksa && $allSpvApproved)
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
                    $produksiApproverName = 'FM/FL PRODUKSI';
                    $allProduksiApproved = true;
                    
                    // Cek apakah SEMUA data sudah di-approve oleh Produksi
                    foreach($data as $item) {
                        if(!$item->approved_by_produksi) {
                            $allProduksiApproved = false;
                            break;
                        }
                    }
                    
                    // Jika semua sudah di-approve Produksi, cari approver pertama
                    if($allProduksiApproved && count($data) > 0) {
                        foreach($data as $item) {
                            if($item->approved_by_produksi && $item->produksi_approved_by) {
                                $produksiApprover = \App\Models\User::find($item->produksi_approved_by);
                                if($produksiApprover) {
                                    $produksiApproverName = $produksiApprover->name;
                                    break;
                                }
                            }
                        }
                    }
                @endphp
                @if($produksiApprover && $allProduksiApproved)
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

    @else
        <div style="text-align: center; padding: 20px;">
            <p>Tidak ada data yang sesuai dengan filter yang dipilih.</p>
        </div>
    @endif
</body>
</html>
