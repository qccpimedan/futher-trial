@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Detail Produk Forming</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('produk-forming.index') }}">Produk Forming</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Detail Data Produk Forming</h3>
                            <div class="card-tools">
                                
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                <!-- Basic Information -->
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">Informasi Dasar</h5>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td width="40%"><strong>Tanggal:</strong></td>
                                                    <td>{{ \Carbon\Carbon::parse($produkForming->tanggal)->format('d/m/Y H:i') }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Produk:</strong></td>
                                                    <td>{{ $produkForming->produk->nama_produk ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Plan:</strong></td>
                                                    <td>{{ $produkForming->plan->nama_plan ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Shift:</strong></td>
                                                    <td>{{ $produkForming->shift->shift ?? '-' }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>                
                                <!-- Card Informasi Keterangan -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="card card-info card-outline">
                                            <div class="card-header">
                                                <h3 class="card-title">
                                                    <i class="fas fa-info-circle"></i> Keterangan Pengecekan & Kriteria Penilaian
                                                </h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h5 class="text-primary"><i class="fas fa-clipboard-check"></i> Keterangan Pengecekan:</h5>
                                                        <ul class="list-unstyled">
                                                            <li class="mb-2">
                                                                <i class="fas fa-dot-circle text-info"></i>
                                                                <strong>Pengecekan Kondisi Bahan baku, bahan penunjang, dan kemasan (1 dan 2):</strong> nomer 1-6
                                                            </li>
                                                            <li class="mb-2">
                                                                <i class="fas fa-dot-circle text-info"></i>
                                                                <strong>Pengecekan Kemasan (3 dan 4):</strong> nomer 1-2
                                                            </li>
                                                            <li class="mb-2">
                                                                <i class="fas fa-dot-circle text-info"></i>
                                                                <strong>Pengecekan Kondisi Mesin dan Peralatan:</strong> nomer 3-8
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h5 class="text-success"><i class="fas fa-star"></i> Kriteria Penilaian:</h5>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <small class="text-muted">
                                                                    <ol class="mb-0" style="font-size: 1.0em;">
                                                                        <li>Sesuai spesifikasi</li>
                                                                        <li>Tidak sesuai spesifikasi</li>
                                                                        <li>Bebas dari kontaminan dan bahan sebelumnya</li>
                                                                        <li>Ada kontaminan atau sisa bahan sebelumnya</li>
                                                                        <li>Bebas dari potensi kontaminasi allergen</li>
                                                                        <li>Ada potensi kontaminasi allergen</li>
                                                                        <li>Bersih, tidak ada kontaminan atau kotoran, tidak tercium bau menyimpang</li>
                                                                        <li>Tidak bersih, ada kontaminan atau kotoran, tercium bau menyimpang</li>
                                                                    </ol>
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="alert alert-warning mt-3">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                    <strong>Petunjuk:</strong> Gunakan nomor kriteria penilaian di atas untuk mengisi form penilaian pada setiap aspek pemeriksaan.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>                
                                <!-- Kemasan Information -->
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">Status Kemasan</h5>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td width="40%"><strong>Kemasan Plastik:</strong></td>
                                                    <td>{{ $produkForming->kemasan_plastik ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Kemasan Karton:</strong></td>
                                                    <td>{{ $produkForming->kemasan_karton ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Labelisasi Plastik:</strong></td>
                                                    <td>{{ $produkForming->labelisasi_plastik ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Labelisasi Karton:</strong></td>
                                                    <td>{{ $produkForming->labelisasi_karton ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Verifikasi:</strong></td>
                                                    <td>
                                                        @if($produkForming->verifikasi == '✓')
                                                            <span class="badge badge-success">Ok (✓)</span>
                                                        @elseif($produkForming->verifikasi == '✗')
                                                            <span class="badge badge-danger">Tidak Ok (✗)</span>
                                                        @else
                                                            <span class="badge badge-warning">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Tindakan Koreksi:</strong></td>
                                                    <td>{{ $produkForming->tindakan_koreksi ?? '-' }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bahan Baku -->
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">Bahan Baku</h5>
                                        </div>
                                        <div class="card-body">
                                            @if($produkForming->bahan_baku && count($produkForming->bahan_baku) > 0)
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>No</th>
                                                                <th>Nama Bahan</th>
                                                                <th>Penilaian</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($produkForming->bahan_baku as $index => $bahan)
                                                                <tr>
                                                                    <td>{{ $index + 1 }}</td>
                                                                    <td>{{ $bahan['nama'] ?? '-' }}</td>
                                                                    <td>{{ $bahan['penilaian'] ?? 0 }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <p class="text-muted">Tidak ada data bahan baku</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Bahan Penunjang -->
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">Bahan Penunjang</h5>
                                        </div>
                                        <div class="card-body">
                                            @if($produkForming->bahan_penunjang && count($produkForming->bahan_penunjang) > 0)
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>No</th>
                                                                <th>Nama Bahan</th>
                                                                <th>Penilaian</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($produkForming->bahan_penunjang as $index => $bahan)
                                                                <tr>
                                                                    <td>{{ $index + 1 }}</td>
                                                                    <td>{{ $bahan['nama'] ?? '-' }}</td>
                                                                    <td>{{ $bahan['penilaian'] ?? 0 }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <p class="text-muted">Tidak ada data bahan penunjang</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Mesin Dan Peralatan -->
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="fas fa-cogs mr-2"></i>Mesin Dan Peralatan
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            @php
                                                $mesinFields = [
                                                    'autogrind' => 'Autogrind',
                                                    'bowlcutter' => 'Bowlcutter',
                                                    'ayakan_seasoning' => 'Ayakan Seasoning',
                                                    'unimix' => 'Unimix',
                                                    'revoformer' => 'Revoformer',
                                                    'better_mixer' => 'Better Mixer',
                                                    'wet_coater' => 'Wet Coater',
                                                    'breader' => 'Breader',
                                                    'frayer_1' => 'Frayer 1',
                                                    'frayer_2' => 'Frayer 2',
                                                    'iqf_jbt' => 'IQF JBT',
                                                    'keranjang' => 'Keranjang',
                                                    'timbangan' => 'Timbangan',
                                                    'mhw' => 'MHW',
                                                    'foot_sealer' => 'Foot Sealer',
                                                    'metal_detector' => 'Metal Detector',
                                                    'rotary_table' => 'Rotary Table',
                                                    'carton_sealer' => 'Carton Sealer',
                                                    'meatcar' => 'Meatcar',
                                                    'check_weigher_bag' => 'Check Weigher Bag',
                                                    'check_weigher_box' => 'Check Weigher Box'
                                                ];

                                                $filledMesinFields = array_filter($mesinFields, function ($label, $field) use ($produkForming) {
                                                    $value = $produkForming->$field ?? null;
                                                    return $value !== null && $value !== '';
                                                }, ARRAY_FILTER_USE_BOTH);
                                            @endphp

                                            @if(count($filledMesinFields) > 0)
                                                <div class="row">
                                                    @foreach($filledMesinFields as $field => $label)
                                                        <div class="col-md-4 mb-3">
                                                            <div class="card card-outline card-info">
                                                                <div class="card-body p-2">
                                                                    <strong>{{ $label }}:</strong>
                                                                    <span class="badge badge-{{ $produkForming->$field == '✓' ? 'success' : ($produkForming->$field == '✗' ? 'danger' : 'warning') }} float-right">
                                                                        {{ $produkForming->$field }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-muted">Tidak ada data mesin dan peralatan</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            @if(Auth::user()->role == 'superadmin' || Auth::user()->role == 'admin' || $produkForming->user_id == Auth::id())
@if(auth()->user()->hasPermissionTo('edit-produk-forming'))
                                <a href="{{ route('produk-forming.edit', $produkForming->uuid) }}" class="btn btn-md btn-warning">
                                    <i class="fas fa-edit"></i> Edit Data
                                </a>
                           @endif
 @endif
                            <a href="{{ route('produk-forming.index') }}" class="btn btn-secondary btn-md">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
@endsection