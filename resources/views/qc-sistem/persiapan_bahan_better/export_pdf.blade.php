<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Proses Produksi - Persiapan Bahan Better</title>
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

    <div class="title">VERIFIKASI PEMBUATAN BATTER</div>

    @if($data->count() > 0)
        @php
            $firstItem = $data->first();
        @endphp

        <div class="form-info">
            <table>
                <tr>
                    <td>Hari / Tanggal</td>
                    <td colspan="3">: {{ $firstItem->tanggal ? $firstItem->tanggal->format('l, d-m-Y') : '-' }}</td>
                </tr>
                <tr>
                    <td>NAMA PRODUK</td>
                    <td colspan="3">: {{ $firstItem->produk->nama_produk ?? '-' }}</td>
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
                    <th rowspan="2" style="width: 4%;">No</th>
                    <th rowspan="2" style="width: 8%;">Waktu</th>
                    <th rowspan="2" style="width: 10%;">Jenis Better</th>
                    <th rowspan="2" style="width: 8%;">Kode Produksi Produk</th>
                    <th colspan="3" style="width: 25%;">Data Produksi</th>
                    <th rowspan="2" style="width: 6%;">Suhu Air (°C)</th>
                    <th rowspan="2" style="width: 5%;">Sensori</th>
                    <th colspan="3" style="width: 17%;">Standar</th>
                    <th colspan="3" style="width: 17%;">Aktual</th>
                </tr>
                <tr>
                    <th style="width: 12%;">Nama Bahan</th>
                    <th style="width: 6%;">Berat (kg)</th>
                    <th style="width: 7%;">Kode Produksi</th>
                    <th style="width: 5%;">Viskositas (Detik)</th>
                    <th style="width: 6%;">Salinitas (%)</th>
                    <th style="width: 6%;">Suhu Air (°C)</th>
                    <th style="width: 5%;">Viskositas (Detik)</th>
                    <th style="width: 6%;">Salinitas (%)</th>
                    <th style="width: 6%;">Suhu Air (°C)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                    @php
                        $rowspan = $item->aktuals->count() > 0 ? $item->aktuals->count() : 1;
                        $betterRows = $item->better_rows ?? [];
                        if (!is_array($betterRows)) $betterRows = [];
                        if (count($betterRows) === 0) {
                            $betterRows = [[
                                'master_nama_formula_better' => $item->better->nama_formula_better ?? null,
                                'master_berat' => $item->berat_better ?? null,
                                'kode_produksi_better' => $item->kode_produksi_better ?? null,
                                'suhu_air' => $item->suhu_air ?? null,
                                'sensori' => $item->sensori ?? null,
                            ]];
                        }

                        $linesFormula = [];
                        $linesBerat = [];
                        $linesKode = [];
                        foreach ($betterRows as $r) {
                            $linesFormula[] = e($r['master_nama_formula_better'] ?? '-');
                            $linesBerat[] = e($r['master_berat'] ?? '-');
                            $linesKode[] = e($r['kode_produksi_better'] ?? '-');
                        }

                        $cellFormula = implode('<br>', $linesFormula);
                        $cellBerat = implode('<br>', $linesBerat);
                        $cellKode = implode('<br>', $linesKode);
                        
                        $suhuValue = e($item->suhu_air ?? $item->better_rows[0]['suhu_air'] ?? '-');
                        $sensValue = $item->sensori ?? $item->better_rows[0]['sensori'] ?? '-';
                        if ($sensValue == '✔' || $sensValue == '&#10004;') {
                            $cellSensori = 'OK';
                        } elseif ($sensValue == '✘' || $sensValue == '&#10008;') {
                            $cellSensori = 'Tidak OK';
                        } else {
                            $cellSensori = e($sensValue);
                        }
                    @endphp
                    @if($item->aktuals->count() > 0)
                        @foreach($item->aktuals as $aktualIndex => $aktual)
                            <tr>
                                @if($aktualIndex == 0)
                                    <td rowspan="{{ $rowspan }}">{{ $index + 1 }}</td>
                                    <td rowspan="{{ $rowspan }}">{{ $item->jam }}</td>
                                    <td rowspan="{{ $rowspan }}">{{ $item->better->nama_better ?? '-' }}</td>
                                    <td rowspan="{{ $rowspan }}">{{ $item->kode_produksi_produk }}</td>
                                    <td rowspan="{{ $rowspan }}" style="text-align:left;">{!! $cellFormula !!}</td>
                                    <td rowspan="{{ $rowspan }}">{!! $cellBerat !!}</td>
                                    <td rowspan="{{ $rowspan }}">{!! $cellKode !!}</td>
                                    <td rowspan="{{ $rowspan }}">{{ $suhuValue }}</td>
                                    <td rowspan="{{ $rowspan }}">{{ $cellSensori }}</td>
                                @endif
                                <td>{{ $aktual->std->std_viskositas ?? '-' }}</td>
                                <td>{{ $aktual->std->std_salinitas ?? '-' }}</td>
                                <td>{{ $aktual->std->std_suhu_akhir ?? '-' }}</td>
                                <td>{{ $aktual->aktual_vis ?? '-' }}</td>
                                <td>{{ $aktual->aktual_sal ?? '-' }}</td>
                                <td>{{ $aktual->aktual_suhu_air ?? '-' }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</td>
                            <td>{{ $item->better->nama_better ?? '-' }}</td>
                            <td>{{ $item->kode_produksi_produk }}</td>
                            <td style="text-align:left;">{!! $cellFormula !!}</td>
                            <td>{!! $cellBerat !!}</td>
                            <td>{!! $cellKode !!}</td>
                            <td>{{ $suhuValue }}</td>
                            <td>{{ $cellSensori }}</td>
                            <td colspan="6">Tidak ada data aktual</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
         <div style="width: 100%; text-align: right; margin-top: 10px;">
            <span>{{ $kode_form ?? $firstItem->kode_form ?? '-' }}</span>
        </div>
        {{-- ===== TANDA TANGAN ===== --}}
        @php
            $qcApprover = null;
            $qcApproverName = 'QC';
            foreach($data as $item) {
                if($item->approved_by_qc && $item->qc_approved_by) {
                    $qcApprover = \App\Models\User::find($item->qc_approved_by);
                    if($qcApprover) { $qcApproverName = $qcApprover->name; break; }
                }
            }
            $spvApprover = null;
            $spvApproverName = 'SPV';
            foreach($data as $item) {
                if($item->approved_by_spv && $item->spv_approved_by) {
                    $spvApprover = \App\Models\User::find($item->spv_approved_by);
                    if($spvApprover) { $spvApproverName = $spvApprover->name; break; }
                }
            }
            $produksiApprover = null;
            $produksiApproverName = 'FM/FL PRODUKSI';
            foreach($data as $item) {
                if($item->approved_by_produksi && $item->produksi_approved_by) {
                    $produksiApprover = \App\Models\User::find($item->produksi_approved_by);
                    if($produksiApprover) { $produksiApproverName = $produksiApprover->name; break; }
                }
            }
            $base64QcSvg = null;
            if($qcApprover) {
                $qcApprovalDate = '';
                foreach($data as $item) {
                    if($item->approved_by_qc && $item->qc_approved_at) { $qcApprovalDate = $item->qc_approved_at->format('Y-m-d'); break; }
                }
                $qcQrData = "Dokumen ini telah diverifikasi secara sistem oleh {$qcApprover->name} (Tim QC) pada {$qcApprovalDate}";
                $base64QcSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate($qcQrData));
            }
            $base64SpvSvg = null;
            if($spvApprover) {
                $spvApprovalDate = '';
                foreach($data as $item) {
                    if($item->approved_by_spv && $item->spv_approved_at) { $spvApprovalDate = $item->spv_approved_at->format('Y-m-d'); break; }
                }
                $spvQrData = "Dokumen ini telah diverifikasi secara sistem oleh {$spvApprover->name} (SPV) pada {$spvApprovalDate}";
                $base64SpvSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate($spvQrData));
            }
            $base64ProduksiSvg = null;
            if($produksiApprover) {
                $produksiApprovalDate = '';
                foreach($data as $item) {
                    if($item->approved_by_produksi && $item->produksi_approved_at) { $produksiApprovalDate = $item->produksi_approved_at->format('Y-m-d'); break; }
                }
                $produksiQrData = "Dokumen ini telah diverifikasi secara sistem oleh {$produksiApprover->name} (FM/FL PRODUKSI) pada {$produksiApprovalDate}";
                $base64ProduksiSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate($produksiQrData));
            }
        @endphp
        <table class="signature-table">
            <tr>
                <td class="signature-label">Dibuat Oleh:</td>
                <td class="signature-label">Diperiksa Oleh:</td>
                <td class="signature-label">Diketahui Oleh:</td>
            </tr>
            <tr>
                <td class="signature-qr-area">
                    @if($base64QcSvg)<img src="{{ $base64QcSvg }}" class="qr-code-img" alt="QR QC">@endif
                </td>
                <td class="signature-qr-area">
                    @if($base64SpvSvg)<img src="{{ $base64SpvSvg }}" class="qr-code-img" alt="QR SPV">@endif
                </td>
                <td class="signature-qr-area">
                    @if($base64ProduksiSvg)<img src="{{ $base64ProduksiSvg }}" class="qr-code-img" alt="QR Produksi">@endif
                </td>
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
