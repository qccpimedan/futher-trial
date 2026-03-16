<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Timbangan - {{ $filters['kode_form'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 7px;
            margin: 0;
            padding: 8px;
            line-height: 1.1;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 1px solid #000;
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
            font-size: 8px;
        }
        .title {
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            margin: 8px 0;
        }
        .filter-info {
            margin-bottom: 5px;
            padding: 4px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
        .filter-info h4 {
            margin: 0 0 3px 0;
            font-size: 7px;
            font-weight: bold;
        }
        .filter-info p {
            margin: 1px 0;
            font-size: 6px;
        }
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            page-break-inside: auto;
            border: 1px solid #000;
        }
        .main-table th,
        .main-table td {
            border: 1px solid #000;
            padding: 2px;
            text-align: center;
            font-size: 6px;
            vertical-align: middle;
            page-break-inside: avoid;
        }
        .main-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .signature-table td {
            width: 33.33%;
            text-align: center;
            vertical-align: bottom;
            padding: 3px 5px;
            border: none;
        }
        .signature-label {
            text-align: center;
            font-size: 8px;
            margin-bottom: 3px;
        }
        .signature-qr-area {
            height: 55px;
            text-align: center;
            vertical-align: bottom;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 3px;
            margin-bottom: 3px;
        }
        .signature-name {
            font-size: 8px;
            text-align: center;
        }
        .qr-code-img {
            max-height: 45px;
            max-width: 45px;
        }
        @media print {
            .main-table {
                border-collapse: collapse;
                border-spacing: 0;
            }
            .main-table td,
            .main-table th {
                border: 1px solid #000;
                position: relative;
            }
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
            <div>FOOD DIVISION {{ strtoupper(auth()->user()->plan->nama_plan ?? 'UNKNOWN') }}</div>
            <div>{{ strtoupper(auth()->user()->plan->nama_plan ?? 'UNKNOWN') }} - INDONESIA</div>
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="title">PEMERIKSAAN TIMBANGAN</div>

    <div class="filter-info">
        <h4>Informasi Filter</h4>
        <p><strong>Tanggal:</strong> {{ $filters['tanggal'] }} | <strong>Shift:</strong> {{ $filters['shift'] }} |</p>
    </div>
    
    @if($data->count() > 0)
        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                      <th style="width: 5%;">Jam</th>
                    <th style="width: 30%;">Jenis</th>
                    <th style="width: 30%;">Kode Timbangan</th>
                    <th style="width: 15%;">Hasil Pengecekan</th>
                    <th style="width: 15%;">Hasil Verifikasi 500 Gr</th>
                    <th style="width: 15%;">Hasil Verifikasi 1000 Gr</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</td>
                        <td>{{ $item->jenis }}</td>
                        <td>{{ $item->kode_timbangan }}</td>
                        <td>
                            @php
                                $hasilText = $item->hasil_pengecekan === 'ok' ? 'OK' : 'TIDAK OK';
                            @endphp
                            {{ $hasilText }}
                        </td>
                        <td>{{ $item->hasil_verifikasi_500 ?? '-' }}</td>
                        <td>{{ $item->hasil_verifikasi_1000 ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
         <div style="width: 100%; text-align: right; margin-top: 10px;">
            <span>{{ $filters['kode_form'] ?? $firstItem->kode_form ?? '-' }}</span>
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
                $base64QcSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(45)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$qcApprover->name} (Tim QC) pada {$qcApprovalDate}"));
            }
            $base64SpvSvg = null;
            if($spvApprover) {
                $spvApprovalDate = '';
                foreach($data as $item) { if($item->approved_by_spv && $item->spv_approved_at) { $spvApprovalDate = $item->spv_approved_at->format('Y-m-d'); break; } }
                $base64SpvSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(45)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$spvApprover->name} (SPV) pada {$spvApprovalDate}"));
            }
            $base64ProduksiSvg = null;
            if($produksiApprover) {
                $produksiApprovalDate = '';
                foreach($data as $item) { if($item->approved_by_produksi && $item->produksi_approved_at) { $produksiApprovalDate = $item->produksi_approved_at->format('Y-m-d'); break; } }
                $base64ProduksiSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(45)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$produksiApprover->name} (FM/FL PRODUKSI) pada {$produksiApprovalDate}"));
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
        <div style="text-align: center; padding: 40px; border: 2px dashed #ccc; margin: 20px 0; background-color: #f9f9f9;">
            <h3 style="color: #666; margin-bottom: 10px;">
                <i style="font-size: 24px;">⚠️</i>
            </h3>
            <h4 style="color: #333; margin-bottom: 15px;">Tidak Ada Data Ditemukan</h4>
            <p style="color: #666; margin-bottom: 10px; font-size: 12px;">
                Tidak ada data timbangan yang sesuai dengan filter yang dipilih:
            </p>
            <div style="background-color: #fff; padding: 15px; border-radius: 5px; margin: 15px 0; border: 1px solid #ddd;">
                <p style="margin: 5px 0; font-size: 11px;"><strong>Tanggal:</strong> {{ $filters['tanggal'] }}</p>
                <p style="margin: 5px 0; font-size: 11px;"><strong>Shift:</strong> {{ $filters['shift'] }}</p>
                <p style="margin: 5px 0; font-size: 11px;"><strong>Kode Form:</strong> {{ $filters['kode_form'] }}</p>
            </div>
            <p style="color: #888; font-size: 10px; margin-top: 15px;">
                Silakan periksa kembali filter yang digunakan atau pastikan data sudah tersedia dalam sistem.
            </p>
        </div>
    @endif
</body>
</html>
