<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Peralatan</title>
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
            padding: 4px 3px;
            text-align: center;
            font-size: 8px;
            vertical-align: middle;
        }
        .main-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .text-left {
            text-align: left;
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

    <div class="title">Verifikasi Peralatan</div>

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
                    <td>: {{ $firstItem->shift->shift ?? '-' }}</td>
                    <td>Plan</td>
                    <td>: {{ $firstItem->plan->nama_plan ?? '-' }}</td>
                </tr>
            </table>
        </div>

        @foreach($data as $headerIndex => $item)
            <div class="form-info" style="margin-top: 10px;">
                <table>
                    <tr>
                        <td>Jam</td>
                        <td>: {{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</td>
                        <td>Dibuat Oleh</td>
                        <td>: {{ $item->user->name ?? '-' }}</td>
                    </tr>
                </table>
            </div>

            @php
                $detailsByArea = ($item->details ?? collect())
                    ->sortBy(function ($d) {
                        $area = $d->mesin->area->area ?? '';
                        $mesin = $d->mesin->nama_mesin ?? '';
                        return $area . '|' . $mesin;
                    })
                    ->groupBy(function ($d) {
                        return $d->mesin->area->area ?? '-';
                    });
            @endphp

            @foreach($detailsByArea as $areaName => $details)
                <table class="main-table">
                    <thead>
                        <tr>
                            <th colspan="5" class="text-left">Area: {{ $areaName }}</th>
                        </tr>
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 40%;">Mesin/Peralatan</th>
                            <th style="width: 10%;">Verifikasi</th>
                            <th style="width: 22.5%;">Keterangan</th>
                            <th style="width: 22.5%;">Tindakan Koreksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($details as $i => $d)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td class="text-left">{{ $d->mesin->nama_mesin ?? '-' }}</td>
                                <td>{{ $d->verifikasi ? 'OK' : 'Tidak OK' }}</td>
                                <td class="text-left">{{ $d->verifikasi ? '-' : ($d->keterangan ?? '-') }}</td>
                                <td class="text-left">{{ $d->verifikasi ? '-' : ($d->tindakan_koreksi ?? '-') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endforeach
        @endforeach

        <div style="width: 100%; text-align: right; margin-top: 10px;">
            <span>{{ $kode_form ?? $firstItem->kode_form ?? '-' }}</span>
        </div>

        {{-- ===== TANDA TANGAN ===== --}}
        @php
            $qcApprover = null; $qcApproverName = 'QC';
            if (!empty($firstItem->approved_by_qc_user_id)) {
                $qcApprover = \App\Models\User::find($firstItem->approved_by_qc_user_id);
                if($qcApprover) $qcApproverName = $qcApprover->name;
            }
            $produksiApprover = null; $produksiApproverName = 'FM/FL PRODUKSI';
            if (!empty($firstItem->approved_by_produksi_user_id)) {
                $produksiApprover = \App\Models\User::find($firstItem->approved_by_produksi_user_id);
                if($produksiApprover) $produksiApproverName = $produksiApprover->name;
            }
            $spvApprover = null; $spvApproverName = 'SPV';
            if (!empty($firstItem->approved_by_spv_user_id)) {
                $spvApprover = \App\Models\User::find($firstItem->approved_by_spv_user_id);
                if($spvApprover) $spvApproverName = $spvApprover->name;
            }
            $base64QcSvg = null;
            if($qcApprover && $firstItem->approved_by_qc) {
                $qcApprovalDate = $firstItem->approved_by_qc_at ? $firstItem->approved_by_qc_at->format('Y-m-d') : '';
                $base64QcSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(45)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$qcApprover->name} (Tim QC) pada {$qcApprovalDate}"));
            }
            $base64ProduksiSvg = null;
            if($produksiApprover && $firstItem->approved_by_produksi) {
                $produksiApprovalDate = $firstItem->approved_by_produksi_at ? $firstItem->approved_by_produksi_at->format('Y-m-d') : '';
                $base64ProduksiSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(45)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$produksiApprover->name} (FM/FL PRODUKSI) pada {$produksiApprovalDate}"));
            }
            $base64SpvSvg = null;
            if($spvApprover && $firstItem->approved_by_spv) {
                $spvApprovalDate = $firstItem->approved_by_spv_at ? $firstItem->approved_by_spv_at->format('Y-m-d') : '';
                $base64SpvSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(45)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$spvApprover->name} (SPV) pada {$spvApprovalDate}"));
            }
        @endphp
        <table class="signature-table">
            <tr>
                <td class="signature-label">Dibuat Oleh:</td>
                <td class="signature-label">Diperiksa Oleh:</td>
                <td class="signature-label">Disetujui Oleh:</td>
            </tr>
            <tr>
                <td class="signature-qr-area">@if($base64QcSvg)<img src="{{ $base64QcSvg }}" class="qr-code-img" alt="QR QC">@endif</td>
                <td class="signature-qr-area">@if($base64ProduksiSvg)<img src="{{ $base64ProduksiSvg }}" class="qr-code-img" alt="QR Produksi">@endif</td>
                <td class="signature-qr-area">@if($base64SpvSvg)<img src="{{ $base64SpvSvg }}" class="qr-code-img" alt="QR SPV">@endif</td>
            </tr>
            <tr>
                <td><div class="signature-line"></div></td>
                <td><div class="signature-line"></div></td>
                <td><div class="signature-line"></div></td>
            </tr>
            <tr>
                <td class="signature-name">{{ $qcApproverName }}</td>
                <td class="signature-name">{{ $produksiApproverName }}</td>
                <td class="signature-name">{{ $spvApproverName }}</td>
            </tr>
        </table>
    @else
        <div style="text-align: center; margin-top: 50px;">
            <h2>Tidak ada data untuk ditampilkan</h2>
        </div>
    @endif
</body>
</html>
