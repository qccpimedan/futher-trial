<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Proses Produksi - Persiapan Bahan (Forming & Non Forming)</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 0; padding: 10px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .company-logo { float: left; width: 60px; height: 60px; }
        .company-info { margin-left: 80px; text-align: left; }
        .company-name { font-weight: bold; font-size: 12px; }
        .title { text-align: center; font-size: 14px; font-weight: bold; margin: 20px 0; }
        .section-title { font-size: 12px; font-weight: bold; margin: 12px 0 6px 0; }
        .muted { color: #666; }
        table { width: 100%; border-collapse: collapse; }
        td, th { border: 1px solid #000; padding: 4px 3px; font-size: 8px; vertical-align: top; }
        th { background: #f0f0f0; }
        .page-break { page-break-before: always; }
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

    <div class="title">CETAK PDF BERDASARKAN FILTER</div>
    <div class="muted" style="text-align:right; margin-bottom: 6px;">{{ $kode_form ?? '-' }}</div>

    <div class="section-title">A. Persiapan Bahan Forming</div>
    <table>
        <thead>
            <tr>
                <th style="width:4%;">No</th>
                <th style="width:8%;">Tanggal</th>
                <th style="width:6%;">Jam</th>
                <th style="width:10%;">Shift</th>
                <th>Produk</th>
                <th style="width:10%;">Kode Produksi</th>
                <th style="width:10%;">Nomor Formula</th>
                <th style="width:8%;">Kondisi</th>
                <th style="width:12%;">Dibuat Oleh</th>
                <th style="width:16%;">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($dataForming as $item)
                <tr>
                    <td style="text-align:center;">{{ $no++ }}</td>
                    <td style="text-align:center;">{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') : '-' }}</td>
                    <td style="text-align:center;">{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</td>
                    <td style="text-align:center;">{{ $item->shift->shift ?? '-' }}</td>
                    <td>{{ $item->formula->produk->nama_produk ?? '-' }}</td>
                    <td style="text-align:center;">{{ $item->kode_produksi_emulsi ?? '-' }}</td>
                    <td style="text-align:center;">{{ $item->formula->nomor_formula ?? '-' }}</td>
                    <td style="text-align:center;">{{ $item->kondisi ?? '-' }}</td>
                    <td style="text-align:center;">{{ $item->user->name ?? '-' }}</td>
                    <td>{{ $item->catatan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align:center;">Tidak ada data forming untuk filter ini</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title" style="margin-top: 14px;">B. Persiapan Bahan Non Forming</div>
    <table>
        <thead>
            <tr>
                <th style="width:4%;">No</th>
                <th style="width:8%;">Tanggal</th>
                <th style="width:6%;">Jam</th>
                <th style="width:10%;">Shift</th>
                <th>Produk</th>
                <th style="width:10%;">Kode Produksi</th>
                <th style="width:10%;">Nomor Formula</th>
                <th style="width:8%;">Kondisi</th>
                <th style="width:12%;">Dibuat Oleh</th>
                <th style="width:16%;">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @php $no2 = 1; @endphp
            @forelse($dataNonForming as $item)
                <tr>
                    <td style="text-align:center;">{{ $no2++ }}</td>
                    <td style="text-align:center;">{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') : '-' }}</td>
                    <td style="text-align:center;">{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</td>
                    <td style="text-align:center;">{{ $item->shift->shift ?? '-' }}</td>
                    <td>{{ $item->formulaNonForming->produk->nama_produk ?? '-' }}</td>
                    <td style="text-align:center;">{{ $item->kode_produksi ?? '-' }}</td>
                    <td style="text-align:center;">{{ $item->formulaNonForming->nomor_formula ?? '-' }}</td>
                    <td style="text-align:center;">{{ $item->kondisi ?? '-' }}</td>
                    <td style="text-align:center;">{{ $item->user->name ?? '-' }}</td>
                    <td>{{ $item->catatan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align:center;">Tidak ada data non-forming untuk filter ini</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
