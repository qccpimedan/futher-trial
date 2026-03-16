<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Unified Packaging Export - Documentation Process</title>
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
            <img src="{{ public_path('dist/img/cpi-logo.png') }}" alt="CPI Logo" style="width: 40px; height: 40px; object-fit: contain;">
        </div>
        <div class="company-info">
            <div class="company-name">PT. CHAROEN POKPHAND INDONESIA</div>
            <div>FOOD DIVISION {{ strtoupper(auth()->user()->plan->nama_plan ?? 'UNKNOWN') }}</div>
            <div>{{ strtoupper(auth()->user()->plan->nama_plan ?? 'UNKNOWN') }} - INDONESIA</div>
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="title">VERIFIKASI PROSES PENGEMASAN</div>

    <div class="filter-info">
        <h4>Information</h4>
        <p><strong>Tanggal:</strong> 
            @if(isset($filterInfo['tanggal']) && $filterInfo['tanggal'] !== '-')
                {{ \Carbon\Carbon::parse($filterInfo['tanggal'])->format('l, d-m-Y') }}
            @else
                {{ $filterInfo['tanggal'] ?? '-' }}
            @endif
            | <strong>Shift:</strong> {{ $filterInfo['shift'] ?? '-' }} | <strong>Produk:</strong> 
            @if($pengemasanProdukData->count() > 0)
                @php $firstProduct = $pengemasanProdukData->first(); @endphp
                {{ $firstProduct->produk->nama_produk ?? '-' }} {{ $firstProduct->berat ?? '-' }}g
            @else
                {{ $filterInfo['produk'] ?? '-' }}
            @endif
            | 
    </div>

    <!-- PENGEMASAN PRODUK SECTION -->
    <div class="section-header">1. PENGEMASAN PRODUK</div>
    
    @if($pengemasanProdukData->count() > 0)
        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 8%;">No</th>
                    <th style="width: 12%;">Waktu</th>
                    <th style="width: 15%;">Tgl Expired</th>
                    <th style="width: 18%;">Kode Produksi</th>
                    <th style="width: 12%;">Std Suhu IQF</th>
                    <th style="width: 12%;">Aktual Suhu</th>
                    <th style="width: 11%;">Waktu Packing</th>
                    <!-- <th style="width: 12%;">Waktu Selesai</th> -->
                </tr>
            </thead>
            <tbody>
                @foreach($pengemasanProdukData as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</td>
                        <td>{{ $item->tanggal_expired ? \Carbon\Carbon::parse($item->tanggal_expired)->format('d-m-Y') : '-' }}</td>
                        <td>{{ $item->kode_produksi ?? '-' }}</td>
                        <td>{{ ($item->std_suhu_produk_iqf !== null && $item->std_suhu_produk_iqf !== '') ? (trim(str_replace(['Â', '°C'], '', $item->std_suhu_produk_iqf)) . '°C') : '-' }}</td>
                        <td>
                            @if(is_array($item->aktual_suhu_produk))
                                @foreach($item->aktual_suhu_produk as $suhu)
                                    {{ trim(str_replace(['Â', '°C'], '', $suhu)) }}°C{{ !$loop->last ? ', ' : '' }}
                                @endforeach
                            @else
                                {{ ($item->aktual_suhu_produk !== null && $item->aktual_suhu_produk !== '') ? (trim(str_replace(['Â', '°C'], '', $item->aktual_suhu_produk)) . '°C') : '-' }}
                            @endif
                        </td>
                        <td>{{ $item->waktu_awal_packing ?? '-' }}</td>
                        <!-- <td>{{ $item->waktu_selesai_packing ?? '-' }}</td> -->
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            <p>Tidak ada data Pengemasan Produk untuk filter yang dipilih.</p>
        </div>
    @endif

    <!-- PENGEMASAN PLASTIK SECTION -->
    <div class="section-header">2. PENGEMASAN PLASTIK</div>
    
    @if($pengemasanPlastikData->count() > 0)
        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 8%;">No</th>
                    <th style="width: 12%;">Waktu</th>
                    <th style="width: 16%;">Proses Penimbangan</th>
                    <th style="width: 16%;">Proses Sealing</th>
                    <th style="width: 20%;">Identitas Produk Pada Tinta</th>
                    <th style="width: 14%;">Kode Kemasan</th>
                    <th style="width: 14%;">Kekuatan Seal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pengemasanPlastikData as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                    <td>{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</td>
                        <td>
                            @php
                                $penimbangan = $item->proses_penimbangan;
                                if ($penimbangan === 'mhw') {
                                    $penimbangan = 'MHW';
                                } elseif ($penimbangan === 'manual') {
                                    $penimbangan = 'Manual';
                                }
                            @endphp
                            {{ $penimbangan ?? '-' }}
                        </td>
                        <td>
                            @php
                                $sealing = $item->proses_sealing;
                                if ($sealing === 'bag-sealer') {
                                    $sealing = 'Bag Sealer';
                                } elseif ($sealing === 'manual') {
                                    $sealing = 'Manual';
                                }
                            @endphp
                            {{ $sealing ?? '-' }}
                        </td>
                        <td>  @if($item->identitas_produk == '✔' || $item->identitas_produk == '&#10004;')
                                OK(sesuai spesifikasi (printing jelas, terbaca; seal ok, tidak bocor dan tidak sobek)
                                )
                            @elseif($item->identitas_produk == '✘' || $item->identitas_produk == '&#10008;')
                                Tidak OK (Tidak Sesuai Spesifikasi)
                            @else
                                {{ $item->identitas_produk ?? '-' }}
                            @endif</td>
                        <td>{{ $item->kode_kemasan_plastik ?? '-' }}</td>
                        <td>  @if($item->kekuatan_seal == '✔' || $item->kekuatan_seal == '&#10004;')
                                OK(sesuai spesifikasi (printing jelas, terbaca; seal ok, tidak bocor dan tidak sobek)
                                )
                            @elseif($item->kekuatan_seal == '✘' || $item->kekuatan_seal == '&#10008;')
                                Tidak OK (Tidak Sesuai Spesifikasi)
                            @else
                                {{ $item->kekuatan_seal ?? '-' }}
                            @endif</td>
                      
                    </tr>
                  
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            <p>Tidak ada data Pengemasan Plastik untuk filter yang dipilih.</p>
        </div>
    @endif

    <!-- BERAT PRODUK BAG SECTION -->
    <div class="section-header">3. BERAT PRODUK (PACK)</div>
    
    @if($beratProdukPackData->count() > 0)
        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 8%;">No</th>
                    <th style="width: 12%;">Waktu</th>
                    <th style="width: 22%;">Kode Produksi</th>
                    <th style="width: 15%;">Line</th>
                    <th style="width: 15%;">Std Pack</th>
                    <th style="width: 9%;">Aktual 1</th>
                    <th style="width: 9%;">Aktual 2</th>
                    <th style="width: 10%;">Aktual 3</th>
                </tr>
            </thead>
            <tbody>
                @foreach($beratProdukPackData as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                     <td>{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</td>
                        <td>{{ $item->pengemasanProduk->kode_produksi ?? '-' }}</td>
                        <td>{{ $item->line ?? '-' }}</td>
                        <td>{{ $item->data_bag->std_bag ?? '-' }}</td>
                        <td>{{ $item->berat_aktual_1 ?? '-' }}</td>
                        <td>{{ $item->berat_aktual_2 ?? '-' }}</td>
                        <td>{{ $item->berat_aktual_3 ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            <p>Tidak ada data Berat Produk (Pack) untuk filter yang dipilih.</p>
        </div>
    @endif

    <!-- BERAT PRODUK BOX SECTION -->
    <div class="section-header">4. BERAT PRODUK (BOX)</div>
    
    @if($beratProdukBoxData->count() > 0)
        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 10%;">No</th>
                    <th style="width: 15%;">Waktu</th>
                    <th style="width: 25%;">Kode Produksi</th>
                    <th style="width: 18%;">Data Box</th>
                    <th style="width: 11%;">Aktual 1</th>
                    <th style="width: 11%;">Aktual 2</th>
                    <th style="width: 10%;">Aktual 3</th>
                </tr>
            </thead>
            <tbody>
                @foreach($beratProdukBoxData as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</td>
                        <td>{{ $item->pengemasanProduk->kode_produksi ?? '-' }}</td>
                        <td>{{ $item->data_box->std_box ?? '-' }}</td>
                        <td>{{ $item->berat_aktual_1 ?? '-' }}</td>
                        <td>{{ $item->berat_aktual_2 ?? '-' }}</td>
                        <td>{{ $item->berat_aktual_3 ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            <p>Tidak ada data Berat Produk (Box) untuk filter yang dipilih.</p>
        </div>
    @endif

    <!-- PENGEMASAN KARTON SECTION -->
    <div class="section-header">5. PENGEMASAN KARTON</div>
    
    @if($pengemasanKartonData->count() > 0)
        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 8%;">No</th>
                    <th style="width: 12%;">Waktu</th>
                    <th style="width: 22%;">Kode Produksi</th>
                    <th style="width: 28%;">Identitas Produk</th>
                    <th style="width: 15%;">Std Karton</th>
                    <th style="width: 15%;">Aktual Karton</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pengemasanKartonData as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</td>
                        <td>{{ $item->pengemasanproduk->kode_produksi ?? '-' }}</td>
                        <td>   @if($item->identitas_produk_pada_karton == '✔' || $item->identitas_produk_pada_karton == '&#10004;')
                                OK(sesuai spesifikasi (printing jelas, terbaca; seal ok, tidak bocor dan tidak sobek)
                                )
                            @elseif($item->identitas_produk_pada_karton == '✘' || $item->identitas_produk_pada_karton == '&#10008;')
                                Tidak OK (Tidak Sesuai Spesifikasi)
                            @else
                                {{ $item->identitas_produk_pada_karton ?? '-' }}
                            @endif</td>
                        <td>{{ $item->standar_jumlah_karton ?? '-' }} pcs</td>
                        <td>{{ $item->aktual_jumlah_karton ?? '-' }} pcs</td>

                     
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            <p>Tidak ada data Pengemasan Karton untuk filter yang dipilih.</p>
        </div>
    @endif

    <!-- DOKUMENTASI SECTION -->
    <div class="section-header">6. DOKUMENTASI</div>
    
    @if($DokumentasiData->count() > 0)
        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 10%;">No</th>
                    <th style="width: 15%;">Waktu</th>
                    <th style="width: 25%;">Foto Kode Produksi</th>
                    <th style="width: 25%;">QR Code</th>
                    <th style="width: 25%;">Label Polyroll</th>
                </tr>
            </thead>
            <tbody>
                @foreach($DokumentasiData as $index => $item)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td style="text-align: center;">{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</td>
                        <td style="text-align: center;">
                            @if($item->foto_kode_produksi)
                                <img src="{{ public_path('storage/' . $item->foto_kode_produksi) }}" alt="Foto Kode Produksi" style="width: 50px; height: 50px; object-fit: cover; border-radius: 2px;">
                            @else
                                <span style="font-size: 6px;">Tidak Ada</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            @if($item->qr_code)
                                <img src="{{ public_path('storage/' . $item->qr_code) }}" alt="QR Code" style="width: 50px; height: 50px; object-fit: cover; border-radius: 2px;">
                            @else
                                <span style="font-size: 6px;">Tidak Ada</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            @if($item->label_polyroll)
                                <img src="{{ public_path('storage/' . $item->label_polyroll) }}" alt="Label Polyroll" style="width: 50px; height: 50px; object-fit: cover; border-radius: 2px;">
                            @else
                                <span style="font-size: 6px;">Tidak Ada</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else

        <div class="no-data">
            <p>Tidak ada data Dokumentasi untuk filter yang dipilih.</p>
        </div>
    @endif

    @if($pengemasanProdukData->count() == 0 && $pengemasanPlastikData->count() == 0 && $beratProdukPackData->count() == 0 && $beratProdukBoxData->count() == 0 && $pengemasanKartonData->count() == 0 && $DokumentasiData->count() == 0)
        <div style="text-align: center; padding: 20px; font-size: 10px;">
            <p><strong>Tidak ada data yang sesuai dengan filter yang dipilih.</strong></p>
            <p>Filter yang digunakan:</p>
            <p>Tanggal: {{ $filterInfo['tanggal'] ?? '-' }}</p>
            <p>Shift: {{ $filterInfo['shift'] ?? '-' }}</p>
            <p>Produk: {{ $filterInfo['produk'] ?? '-' }}</p>
            <p>Kode Form: {{ $filterInfo['kode_form'] ?? '-' }}</p>
        </div>
    @else
     <div style="width: 100%; text-align: right; margin-top: 10px;">
            <span>{{ $filterInfo['kode_form'] }}</span>
        </div>
        {{-- ===== TANDA TANGAN ===== --}}
        @php
            $qcApprover = null; $qcApproverName = 'QC';
            foreach($DokumentasiData as $item) {
                if($item->approved_by_qc && $item->qc_approved_by) {
                    $qcApprover = \App\Models\User::find($item->qc_approved_by);
                    if($qcApprover) { $qcApproverName = $qcApprover->name; break; }
                }
            }
            $spvApprover = null; $spvApproverName = 'SPV';
            foreach($DokumentasiData as $item) {
                if($item->approved_by_spv && $item->spv_approved_by) {
                    $spvApprover = \App\Models\User::find($item->spv_approved_by);
                    if($spvApprover) { $spvApproverName = $spvApprover->name; break; }
                }
            }
            $produksiApprover = null; $produksiApproverName = 'FM/FL PRODUKSI';
            foreach($DokumentasiData as $item) {
                if($item->approved_by_produksi && $item->produksi_approved_by) {
                    $produksiApprover = \App\Models\User::find($item->produksi_approved_by);
                    if($produksiApprover) { $produksiApproverName = $produksiApprover->name; break; }
                }
            }
            $base64QcSvg = null;
            if($qcApprover) {
                $qcApprovalDate = '';
                foreach($DokumentasiData as $item) { if($item->approved_by_qc && $item->qc_approved_at) { $qcApprovalDate = $item->qc_approved_at->format('Y-m-d'); break; } }
                $base64QcSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(45)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$qcApprover->name} (Tim QC) pada {$qcApprovalDate}"));
            }
            $base64SpvSvg = null;
            if($spvApprover) {
                $spvApprovalDate = '';
                foreach($DokumentasiData as $item) { if($item->approved_by_spv && $item->spv_approved_at) { $spvApprovalDate = $item->spv_approved_at->format('Y-m-d'); break; } }
                $base64SpvSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(45)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$spvApprover->name} (SPV) pada {$spvApprovalDate}"));
            }
            $base64ProduksiSvg = null;
            if($produksiApprover) {
                $produksiApprovalDate = '';
                foreach($DokumentasiData as $item) { if($item->approved_by_produksi && $item->produksi_approved_at) { $produksiApprovalDate = $item->produksi_approved_at->format('Y-m-d'); break; } }
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
    @endif
</body>
</html>