@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detail Data Pembekuan IQF Roasting</h3>
                    <div class="card-tools">
@if(auth()->user()->hasPermissionTo('edit-pembekuan-iqf-roasting'))
                        <a href="{{ route('pembekuan-iqf-roasting.edit', $pembekuanIqfRoasting->uuid) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        @endif
<a href="{{ route('pembekuan-iqf-roasting.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Tanggal</th>
                                    <td>{{ \Carbon\Carbon::parse($pembekuanIqfRoasting->tanggal)->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>Shift</th>
                                    <td>{{ $pembekuanIqfRoasting->shift->shift ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Plan</th>
                                    <td>{{ $pembekuanIqfRoasting->plan->nama_plan ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Suhu Ruang IQF (°C)</th>
                                    <td>{{ $pembekuanIqfRoasting->suhu_ruang_iqf }}</td>
                                </tr>
                                <tr>
                                    <th>Holding Time (menit)</th>
                                    <td>{{ $pembekuanIqfRoasting->holding_time }}</td>
                                </tr>
                                <tr>
                                    <th>Dibuat Oleh</th>
                                    <td>{{ $pembekuanIqfRoasting->user->name ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Related Process Information -->
                    @if($frayerData || $breaderData || $batteringData || $predustData || $penggorenganData || $roastingFanData || $hasilRoastingData)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card card-info">
                                <div class="card-header">
                                    <h3 class="card-title">Informasi Proses Terkait</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @if($frayerData)
                                        <div class="col-md-4">
                                            <div class="card card-outline card-primary">
                                                <div class="card-header">
                                                    <h3 class="card-title">Frayer</h3>
                                                </div>
                                                <div class="card-body">
                                                    <strong>Produk:</strong> {{ $frayerData->produk->nama_produk ?? 'N/A' }}<br>
                                                    <strong>Shift:</strong> {{ $frayerData->shift->shift ?? 'N/A' }}<br>
                                                    <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($frayerData->tanggal)->format('d/m/Y') }}
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        @if($breaderData)
                                        <div class="col-md-4">
                                            <div class="card card-outline card-success">
                                                <div class="card-header">
                                                    <h3 class="card-title">Breader</h3>
                                                </div>
                                                <div class="card-body">
                                                    <strong>Produk:</strong> {{ $breaderData->produk->nama_produk ?? 'N/A' }}<br>
                                                    <strong>Shift:</strong> {{ $breaderData->shift->shift ?? 'N/A' }}<br>
                                                    <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($breaderData->tanggal)->format('d/m/Y') }}
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        @if($batteringData)
                                        <div class="col-md-4">
                                            <div class="card card-outline card-warning">
                                                <div class="card-header">
                                                    <h3 class="card-title">Battering</h3>
                                                </div>
                                                <div class="card-body">
                                                    <strong>Produk:</strong> {{ $batteringData->produk->nama_produk ?? 'N/A' }}<br>
                                                    <strong>Shift:</strong> {{ $batteringData->shift->shift ?? 'N/A' }}<br>
                                                    <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($batteringData->tanggal)->format('d/m/Y') }}
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    
                                    <div class="row mt-2">
                                        @if($predustData)
                                        <div class="col-md-4">
                                            <div class="card card-outline card-info">
                                                <div class="card-header">
                                                    <h3 class="card-title">Predust</h3>
                                                </div>
                                                <div class="card-body">
                                                    <strong>Produk:</strong> {{ $predustData->produk->nama_produk ?? 'N/A' }}<br>
                                                    <strong>Jenis:</strong> {{ $predustData->jenisPredust->jenis_predust ?? 'N/A' }}<br>
                                                    <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($predustData->tanggal)->format('d/m/Y') }}
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        @if($penggorenganData)
                                        <div class="col-md-4">
                                            <div class="card card-outline card-danger">
                                                <div class="card-header">
                                                    <h3 class="card-title">Penggorengan</h3>
                                                </div>
                                                <div class="card-body">
                                                    <strong>Produk:</strong> {{ $penggorenganData->produk->nama_produk ?? 'N/A' }}<br>
                                                    <strong>Shift:</strong> {{ $penggorenganData->shift->shift ?? 'N/A' }}<br>
                                                    <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($penggorenganData->tanggal)->format('d/m/Y') }}
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        @if($roastingFanData)
                                        <div class="col-md-4">
                                            <div class="card card-outline card-secondary">
                                                <div class="card-header">
                                                    <h3 class="card-title">Roasting Fan</h3>
                                                </div>
                                                <div class="card-body">
                                                    <strong>Produk:</strong> {{ $roastingFanData->produk->nama_produk ?? 'N/A' }}<br>
                                                    <strong>Shift:</strong> {{ $roastingFanData->shift->shift ?? 'N/A' }}<br>
                                                    <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($roastingFanData->tanggal)->format('d/m/Y') }}
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    
                                    @if($hasilRoastingData)
                                    <div class="row mt-2">
                                        <div class="col-md-4">
                                            <div class="card card-outline card-dark">
                                                <div class="card-header">
                                                    <h3 class="card-title">Hasil Roasting</h3>
                                                </div>
                                                <div class="card-body">
                                                    <strong>Produk:</strong> {{ $hasilRoastingData->produk->nama_produk ?? 'N/A' }}<br>
                                                    <strong>Suhu Pusat:</strong> {{ $hasilRoastingData->aktual_suhu_pusat ?? 'N/A' }}°C<br>
                                                    <strong>Sensori:</strong> {{ $hasilRoastingData->sensori ?? 'N/A' }}<br>
                                                    <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($hasilRoastingData->tanggal)->format('d/m/Y') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>
@endsection
