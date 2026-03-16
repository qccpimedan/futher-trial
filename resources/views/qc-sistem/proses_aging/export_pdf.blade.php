<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Proses Aging - Bahan Baku → Tumbling → Aging</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 7px;
            margin: 0;
            padding: 6px;
            line-height: 1.1;
        }
        .header {
            text-align: center;
            margin-bottom: 8px;
            border-bottom: 2px solid #000;
            padding-bottom: 4px;
        }
        .company-logo {
            float: left;
            width: 35px;
            height: 35px;
            text-align: center;
            line-height: 35px;
            font-weight: bold;
            font-size: 9px;
        }
        .company-info {
            margin-left: 45px;
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
            margin: 6px 0;
        }
        .filter-info {
            margin-bottom: 6px;
            padding: 4px;
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            font-size: 6px;
        }
        .filter-info h4 {
            margin: 0 0 2px 0;
            font-size: 7px;
            font-weight: bold;
        }
        .filter-info p {
            margin: 1px 0;
        }
        .section-title {
            background-color: #e8e8e8;
            font-weight: bold;
            padding: 3px;
            margin-top: 6px;
            margin-bottom: 3px;
            border-left: 3px solid #333;
            font-size: 7px;
        }
        .subsection-title {
            background-color: #f5f5f5;
            font-weight: bold;
            padding: 2px;
            margin-top: 4px;
            margin-bottom: 2px;
            border-left: 2px solid #666;
            font-size: 6px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            border: 1px solid #000;
        }
        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 2px;
            text-align: left;
            font-size: 6px;
        }
        .data-table th {
            background-color: #d0d0d0;
            font-weight: bold;
            text-align: center;
        }
        .data-table td {
            vertical-align: top;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .no-data {
            text-align: center;
            padding: 8px;
            color: #999;
            font-style: italic;
            font-size: 6px;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
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
            font-size: 7px;
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
            font-size: 7px;
            text-align: center;
        }
        .qr-code-img {
            max-height: 45px;
            max-width: 45px;
        }
        .page-break {
            page-break-after: always;
        }
        .info-box {
            background-color: #fafafa;
            border: 1px solid #ddd;
            padding: 2px;
            margin-bottom: 4px;
            font-size: 6px;
        }
        .info-box strong {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-logo">
            <img src="{{ public_path('dist/img/cpi-logo.png') }}" alt="CPI Logo" style="width: 50px; height: 50px; object-fit: contain;">
        </div>
        <div class="company-info">
            <div class="company-name">PT. CHAROEN POKPHAND INDONESIA</div>
            <div>FOOD DIVISION {{ strtoupper(auth()->user()->plan->nama_plan ?? 'UNKNOWN') }}</div>
            <div>{{ strtoupper(auth()->user()->plan->nama_plan ?? 'UNKNOWN') }} - INDONESIA</div>
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="title">VERIFIKASI PROSES AGING<br></div>

    <div class="filter-info">
        <h4>Informasi Filter</h4>
        <p><strong>Tanggal:</strong> {{ $filterInfo['tanggal'] }} | <strong>Shift:</strong> {{ $filterInfo['shift'] }} | <strong>Produk:</strong> {{ $filterInfo['produk'] }}</p>
    </div>

    @if(count($data) > 0)
        @php
            $rowNumber = 1;
        @endphp
        @foreach($data as $index => $aging)
            @php
                $tumbling = $aging->prosesTumbling;
                $bahanBaku = $tumbling ? $tumbling->bahanBakuTumbling : null;
            @endphp

            <!-- BAHAN BAKU SECTION -->
            <div class="section-title" style="background-color: #fff3cd; border-left-color: #ff9800;">
                TAHAP 1: BAHAN BAKU TUMBLING
            </div>
            @if($bahanBaku)
                <table class="data-table text-center">
                    <thead>
                        <tr>
                            <th>No</th>
                            <!-- <th>Kode Form</th> -->
                            <th>Kode Produksi</th>
                            <th>Jam</th>
                            <th>Nama Bahan Baku</th>
                            <th>Jumlah (kg)</th>
                            <th>Kode Produksi BBK</th>
                            <th>Suhu (°C)</th>
                            <th>Kondisi Daging</th>
                            <th>Salinity</th>
                            <th>Hasil Pencampuran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($bahanBaku->manual_bahan_data && count($bahanBaku->manual_bahan_data) > 0)
                            @foreach($bahanBaku->manual_bahan_data as $bahanIndex => $bahan)
                                <tr>
                                    @if($bahanIndex == 0)
                                        <td class="text-center" rowspan="{{ count($bahanBaku->manual_bahan_data) }}">1</td>
                                        <!-- <td class="text-center" rowspan="{{ count($bahanBaku->manual_bahan_data) }}">{{ $bahanBaku->kode_form ?? '-' }}</td> -->
                                        <td class="text-center" rowspan="{{ count($bahanBaku->manual_bahan_data) }}">{{ $bahanBaku->kode_produksi ?? '-' }}</td>
                                        <td class="text-center" rowspan="{{ count($bahanBaku->manual_bahan_data) }}">{{ $bahanBaku->jam ? \Carbon\Carbon::parse($bahanBaku->jam)->format('H:i') : '-' }}</td>
                                    @endif
                                    <td>{{ $bahan['nama_bahan_baku'] ?? '-' }}</td>
                                    <td class="text-right">{{ $bahan['jumlah'] ?? '-' }}</td>
                                    <td>{{ $bahan['kode_produksi_bahan_baku'] ?? '-' }}</td>
                                    <td class="text-center">{{ $bahan['suhu'] ?? '-' }}</td>
                                    <td class="text-center">
                                        @if($bahan['kondisi_daging'] == '✓')
                                            <strong style="color: green;">OK</strong>
                                        @elseif($bahan['kondisi_daging'] == '✘')
                                            <strong style="color: red;">NOT OK</strong>
                                        @else
                                            {{ $bahan['kondisi_daging'] ?? '-' }}
                                        @endif
                                    </td>
                                    @if($bahanIndex == 0)
                                        <td class="text-center" rowspan="{{ count($bahanBaku->manual_bahan_data) }}">{{ $bahanBaku->salinity ?? '-' }}</td>
                                        <td class="text-center" rowspan="{{ count($bahanBaku->manual_bahan_data) }}">
                                            @if($bahanBaku->hasil_pencampuran == '✓')
                                                <strong style="color: green;">OK</strong>
                                            @elseif($bahanBaku->hasil_pencampuran == '✘')
                                                <strong style="color: red;">NOT OK</strong>
                                            @else
                                                {{ $bahanBaku->hasil_pencampuran ?? '-' }}
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td class="text-center">{{ $bahanBaku->kode_form ?? '-' }}</td>
                                <td class="text-center">{{ $bahanBaku->kode_produksi ?? '-' }}</td>
                                <td class="text-center">{{ $bahanBaku->jam ? \Carbon\Carbon::parse($bahanBaku->jam)->format('H:i') : '-' }}</td>
                                <td>{{ $bahanBaku->nama_bahan_baku ?? '-' }}</td>
                                <td class="text-right">{{ $bahanBaku->jumlah ?? '-' }}</td>
                                <td>{{ $bahanBaku->kode_produksi_bahan_baku ?? '-' }}</td>
                                <td class="text-center">{{ $bahanBaku->suhu ?? '-' }}</td>
                                <td class="text-center">{{ $bahanBaku->kondisi_daging ?? '-' }}</td>
                                <td class="text-center">{{ $bahanBaku->salinity ?? '-' }}</td>
                                <td class="text-center">{{ $bahanBaku->hasil_pencampuran ?? '-' }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            @else
                <div class="no-data">Tidak ada data Bahan Baku Tumbling</div>
            @endif

            <!-- PROSES TUMBLING SECTION -->
            <div class="section-title" style="background-color: #e3f2fd; border-left-color: #2196f3;">
                TAHAP 2: PROSES TUMBLING
            </div>
            @if($tumbling)
                <table class="data-table text-center">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Produksi</th>
                            <th>Jam</th>
                            <th>Parameter</th>
                            <th>Standar</th>
                            <th>Aktual</th>
                            <th>Waktu Mulai Tumbling</th>
                            <th>Waktu Selesai Tumbling</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" rowspan="5">1</td>
                            <td class="text-center" rowspan="5">{{ $tumbling->kode_produksi ?? '-' }}</td>
                            <td class="text-center" rowspan="5">{{ $tumbling->jam ? \Carbon\Carbon::parse($tumbling->jam)->format('H:i') : '-' }}</td>
                            <td>Drum ON</td>
                            <td class="text-center">{{ $tumbling->dataTumbling->drum_on ?? '-' }}</td>
                            <td class="text-center">{{ $tumbling->aktual_drum_on ?? '-' }}</td>
                            <td class="text-center" rowspan="5">{{ $tumbling->waktu_mulai_tumbling ?? '-' }}</td>
                            <td class="text-center" rowspan="5">{{ $tumbling->waktu_selesai_tumbling ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Drum OFF</td>
                            <td class="text-center">{{ $tumbling->dataTumbling->drum_off ?? '-' }}</td>
                            <td class="text-center">{{ $tumbling->aktual_drum_off ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Drum Speed (RPM)</td>
                            <td class="text-center">{{ $tumbling->dataTumbling->drum_speed ?? '-' }}</td>
                            <td class="text-center">{{ $tumbling->aktual_speed ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Total Waktu (menit)</td>
                            <td class="text-center">{{ $tumbling->dataTumbling->total_waktu ?? '-' }}</td>
                            <td class="text-center">{{ $tumbling->aktual_total_waktu ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Tekanan Vakum</td>
                            <td class="text-center">{{ $tumbling->dataTumbling->tekanan_vakum ?? '-' }}</td>
                            <td class="text-center">{{ $tumbling->aktual_vakum ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>

                @php
                    $hasNonVakum = (
                        !empty($tumbling->dataTumbling->drum_on_non_vakum ?? null)
                        || !empty($tumbling->dataTumbling->drum_off_non_vakum ?? null)
                        || !empty($tumbling->dataTumbling->drum_speed_non_vakum ?? null)
                        || !empty($tumbling->dataTumbling->total_waktu_non_vakum ?? null)
                        || !empty($tumbling->dataTumbling->tekanan_non_vakum ?? null)
                        || !empty($tumbling->aktual_drum_on_non_vakum ?? null)
                        || !empty($tumbling->aktual_drum_off_non_vakum ?? null)
                        || !empty($tumbling->aktual_speed_non_vakum ?? null)
                        || !empty($tumbling->aktual_total_waktu_non_vakum ?? null)
                        || !empty($tumbling->aktual_tekanan_non_vakum ?? null)
                        || !empty($tumbling->waktu_mulai_tumbling_non_vakum ?? null)
                        || !empty($tumbling->waktu_selesai_tumbling_non_vakum ?? null)
                    );
                @endphp

                @if($hasNonVakum)
                    <table class="data-table text-center" style="margin-top: 8px;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Produksi</th>
                                <th>Jam</th>
                                <th>Parameter (Non Vakum)</th>
                                <th>Standar</th>
                                <th>Aktual</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center" rowspan="7">1</td>
                                <td class="text-center" rowspan="7">{{ $tumbling->kode_produksi ?? '-' }}</td>
                                <td class="text-center" rowspan="7">{{ $tumbling->jam ? \Carbon\Carbon::parse($tumbling->jam)->format('H:i') : '-' }}</td>
                                <td>Drum ON</td>
                                <td class="text-center">{{ $tumbling->dataTumbling->drum_on_non_vakum ?? '-' }}</td>
                                <td class="text-center">{{ $tumbling->aktual_drum_on_non_vakum ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>Drum OFF</td>
                                <td class="text-center">{{ $tumbling->dataTumbling->drum_off_non_vakum ?? '-' }}</td>
                                <td class="text-center">{{ $tumbling->aktual_drum_off_non_vakum ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>Drum Speed (RPM)</td>
                                <td class="text-center">{{ $tumbling->dataTumbling->drum_speed_non_vakum ?? '-' }}</td>
                                <td class="text-center">{{ $tumbling->aktual_speed_non_vakum ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>Total Waktu (menit)</td>
                                <td class="text-center">{{ $tumbling->dataTumbling->total_waktu_non_vakum ?? '-' }}</td>
                                <td class="text-center">{{ $tumbling->aktual_total_waktu_non_vakum ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>Tekanan</td>
                                <td class="text-center">{{ $tumbling->dataTumbling->tekanan_non_vakum ?? '-' }}</td>
                                <td class="text-center">{{ $tumbling->aktual_tekanan_non_vakum ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>Waktu Mulai Tumbling</td>
                                <td class="text-center">-</td>
                                <td class="text-center">{{ $tumbling->waktu_mulai_tumbling_non_vakum ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>Waktu Selesai Tumbling</td>
                                <td class="text-center">-</td>
                                <td class="text-center">{{ $tumbling->waktu_selesai_tumbling_non_vakum ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                @endif
            @else
                <div class="no-data">Tidak ada data Proses Tumbling</div>
            @endif

            <!-- PROSES AGING SECTION -->
            <div class="section-title" style="background-color: #f3e5f5; border-left-color: #9c27b0; margin-top: 5px;">
                TAHAP 3: PROSES AGING
            </div>
            <table class="data-table text-center">
                <thead>
                    <tr>
                        <th>No</th>
                        <!-- <th>Tanggal</th> -->
                        <th>Jam</th>
                        <!-- <th>Shift</th> -->
                        <th>Produk</th>
                        <th>Waktu Mulai Aging</th>
                        <th>Waktu Selesai Aging</th>
                        <th>Suhu Produk</th>
                        <th>Kondisi Produk</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <!-- <td class="text-center">{{ \Carbon\Carbon::parse($aging->tanggal)->format('d-m-Y') }}</td> -->
                        <td class="text-center">{{ $aging->jam ? \Carbon\Carbon::parse($aging->jam)->format('H:i') : '-' }}</td>
                        <!-- <td class="text-center">{{ $aging->prosesTumbling && $aging->prosesTumbling->shift ? $aging->prosesTumbling->shift->shift : '-' }}</td> -->
                        <td>{{ $aging->produk->nama_produk ?? '-' }}</td>
                        <td class="text-center">{{ $aging->waktu_mulai_aging ?? '-' }}</td>
                        <td class="text-center">{{ $aging->waktu_selesai_aging ?? '-' }}</td>
                        <td class="text-center">{{ $aging->suhu_produk ?? '-' }}°C</td>
                        <td class="text-center">
                            @if($aging->kondisi_produk == '✓')
                                <strong style="color: green;">OK</strong>
                            @elseif($aging->kondisi_produk == '✘')
                                <strong style="color: red;">NOT OK</strong>
                            @else
                                {{ $aging->kondisi_produk ?? '-' }}
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
            <div style="text-align: right; padding-right: 10px; font-style: italic; font-size: 9px; color: #666; margin-top: 5px;">
                {{ $filterInfo['kode_form'] ?? '-' }}
            </div>
            @if($index < count($data) - 1)
                <div class="page-break"></div>
            @endif
        @endforeach

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
        <table class="signature-table" style="margin-top: 20px;">
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
        <div class="no-data" style="margin-top: 20px; font-size: 10px;">
            <p>Tidak ada data Proses Aging yang sesuai dengan filter yang dipilih.</p>
        </div>
    @endif

</body>
</html>