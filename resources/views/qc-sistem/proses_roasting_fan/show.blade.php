@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Detail Proses Roasting Fan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href=""><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('proses-roasting-fan.index') }}">Proses Roasting Fan</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Detail Proses Roasting Fan</h3>
                    <div class="card-tools">
                        
                    </div>
                </div>
                <div class="card-body">
                    <!-- Informasi Dasar -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card card-info">
                                <div class="card-header">
                                    <h3 class="card-title">Informasi Dasar</h3>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Tanggal</strong></td>
                                            <td>{{ $firstRecord->tanggal->format('d-m-Y H:i:s') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Shift</strong></td>
                                            <td>
                                                @php
                                                    $shiftData = $firstRecord->shift_data;
                                                    $shiftSource = 'Unknown';
                                                    
                                                    // Tentukan sumber shift berdasarkan dual flow
                                                    if($firstRecord->penggorengan_uuid) {
                                                        $shiftSource = 'Penggorengan (Alur 1)';
                                                    } elseif($firstRecord->input_roasting_uuid) {
                                                        $shiftSource = 'Input Roasting (Alur 2)';
                                                    } else {
                                                        $shiftSource = 'Shift Lokal';
                                                    }
                                                @endphp
                                                
                                                <div>
                                                    @if($shiftData && $shiftData->shift == 1)
                                                        <span class="badge bg-primary">Shift 1</span>
                                                    @elseif($shiftData && $shiftData->shift == 2)
                                                        <span class="badge bg-success">Shift 2</span>
                                                    @elseif($shiftData && $shiftData->shift == 3)
                                                        <span class="badge bg-secondary">Shift 3</span>
                                                    @else
                                                        <span class="badge bg-info">{{ $shiftData->shift ?? '-' }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Alur Proses</strong></td>
                                            <td>
                                                @php
                                                    $alurProses = [];
                                                    
                                                    // Cek alur berdasarkan UUID yang tersedia
                                                    if($firstRecord->penggorengan_uuid) {
                                                        $alurProses[] = '<span class="badge bg-danger">Penggorengan</span>';
                                                        if($firstRecord->predust_uuid) {
                                                            $alurProses[] = '<span class="badge bg-warning">Predust</span>';
                                                        }
                                                        if($firstRecord->battering_uuid) {
                                                            $alurProses[] = '<span class="badge bg-info">Battering</span>';
                                                        }
                                                        if($firstRecord->breader_uuid) {
                                                            $alurProses[] = '<span class="badge bg-success">Breader</span>';
                                                        }
                                                        if($firstRecord->frayer_uuid) {
                                                            $alurProses[] = '<span class="badge bg-secondary">Frayer</span>';
                                                        }
                                                        $alurProses[] = '<span class="badge bg-primary">Roasting Fan</span>';
                                                    } elseif($firstRecord->input_roasting_uuid) {
                                                        $alurProses[] = '<span class="badge bg-purple">Input Roasting</span>';
                                                        if($firstRecord->bahan_baku_roasting_uuid) {
                                                            $alurProses[] = '<span class="badge bg-orange">Bahan Baku Roasting</span>';
                                                        }
                                                        $alurProses[] = '<span class="badge bg-primary">Roasting Fan</span>';
                                                    } else {
                                                        $alurProses[] = '<span class="badge bg-dark">Proses Mandiri</span>';
                                                    }
                                                @endphp
                                                
                                                <div>
                                                    @if(count($alurProses) > 0)
                                                        {!! implode(' → ', $alurProses) !!}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </div>
                                                
                                                <small class="text-muted mt-2 d-block">
                                                    @if($firstRecord->penggorengan_uuid)
                                                        <i class="fas fa-route text-danger"></i> <strong>Kondisi 1:</strong> Alur Penggorengan
                                                    @elseif($firstRecord->input_roasting_uuid)
                                                        <i class="fas fa-route text-purple"></i> <strong>Kondisi 2:</strong> Alur Input Roasting
                                                    @else
                                                        <i class="fas fa-route text-dark"></i> <strong>Mandiri:</strong> Tanpa alur sebelumnya
                                                    @endif
                                                </small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Produk</strong></td>
                                            <td>{{ $firstRecord->produk->nama_produk ?? 'N/A' }}</td>
                                        </tr>
                                        <!-- <tr>
                                            <td><strong>Aktual Lama Proses</strong></td>
                                            <td>{{ $firstRecord->aktual_lama_proses ?? '-' }} menit</td>
                                        </tr> -->
                                        <!-- <tr>
                                            <td><strong>Operator</strong></td>
                                            <td>{{ $firstRecord->user->name ?? 'N/A' }}</td>
                                        </tr> -->
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-success">
                                <div class="card-header">
                                    <h3 class="card-title">Blok Terisi</h3>
                                </div>
                                <div class="card-body">
                                    @foreach($sessionRecords as $record)
                                        <span class="badge bg-info mr-2 mb-2">Blok {{ $record->block_number }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Proses Roasting -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Detail Proses Roasting/Steaming</h3>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th class="text-left align-middle font-weight-bold" style="width: 250px; background-color: #343a40; color: white;">
                                            <strong>II. PROSES ROASTING/ STEAMING</strong>
                                        </th>
                                        @foreach($sessionRecords as $record)
                                            <th class="text-center align-middle font-weight-bold" style="width: 120px; background-color: #495057; color: white;">
                                                <strong>Blok {{ $record->block_number }}</strong>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Standar Suhu Roasting/Steaming -->
                                    <tr>
                                        <td class="align-middle font-weight-bold" style="background-color: #f8f9fa;">
                                            Standar Suhu Pemasakan (°C)
                                        </td>
                                        @foreach($sessionRecords as $record)
                                            <td class="text-center">
                                                <span class="badge bg-secondary">{{ $record->suhuBlok->suhu_blok ?? '-' }}°C</span>
                                            </td>
                                        @endforeach
                                    </tr>
                                    
                                    <!-- Suhu Roasting (Aktual) -->
                                    <tr>
                                        <td class="align-middle font-weight-bold" style="background-color: #f8f9fa;">
                                            Suhu Pemasakan (°C)
                                        </td>
                                        @foreach($sessionRecords as $record)
                                            <td class="text-center">
                                                <span class="badge bg-primary">{{ $record->suhu_roasting ?? '-' }}°C</span>
                                            </td>
                                        @endforeach
                                    </tr>
                                    
                                    <!-- Standar Fan 1 -->
                                    <tr>
                                        <td class="align-middle font-weight-bold" style="background-color: #f8f9fa;">
                                            Standar Fan 1 (%)
                                        </td>
                                        @foreach($sessionRecords as $record)
                                            <td class="text-center">
                                                <span class="badge bg-secondary">{{ $record->stdFan->std_fan ?? '-' }}%</span>
                                            </td>
                                        @endforeach
                                    </tr>
                                    
                                    <!-- Fan 1 (Aktual) -->
                                    <tr>
                                        <td class="align-middle font-weight-bold" style="background-color: #f8f9fa;">
                                            Fan 1 (%)
                                        </td>
                                        @foreach($sessionRecords as $record)
                                            <td class="text-center">
                                                <span class="badge bg-success">{{ $record->fan_1 ?? '-' }}%</span>
                                            </td>
                                        @endforeach
                                    </tr>
                                    
                                    <!-- Standar Fan 2 -->
                                    <tr>
                                        <td class="align-middle font-weight-bold" style="background-color: #f8f9fa;">
                                            Standart Fan 2 (%)
                                        </td>
                                        @foreach($sessionRecords as $record)
                                            <td class="text-center">
                                                <span class="badge bg-secondary">{{ $record->stdFan->std_fan_2 ?? '-' }}%</span>
                                            </td>
                                        @endforeach
                                    </tr>
                                    
                                    <!-- Fan 2 (Aktual) -->
                                    <tr>
                                        <td class="align-middle font-weight-bold" style="background-color: #f8f9fa;">
                                            Fan 2 (%)
                                        </td>
                                        @foreach($sessionRecords as $record)
                                            <td class="text-center">
                                                <span class="badge bg-success">{{ $record->fan_2 ?? '-' }}%</span>
                                            </td>
                                        @endforeach
                                    </tr>
                                    
                                    <!-- Standar Fan 3 -->
                                    <tr>
                                        <td class="align-middle font-weight-bold" style="background-color: #f8f9fa;">
                                            Standart Fan 3 (%)
                                        </td>
                                        @foreach($sessionRecords as $record)
                                            <td class="text-center">
                                                <span class="badge bg-secondary">{{ $record->stdFan->fan_3 ?? '-' }} %</span>
                                            </td>
                                        @endforeach
                                    </tr>
                                    
                                    <!-- Fan 3 (Aktual) -->
                                    <tr>
                                        <td class="align-middle font-weight-bold" style="background-color: #f8f9fa;">
                                            Fan 3 (%)
                                        </td>
                                        @foreach($sessionRecords as $record)
                                            <td class="text-center">
                                                <span class="badge bg-success">{{ $record->fan_3 ?? '-' }} %</span>
                                            </td>
                                        @endforeach
                                    </tr>
                                    
                                    <!-- Standar Fan 4 -->
                                    <tr>
                                        <td class="align-middle font-weight-bold" style="background-color: #f8f9fa;">
                                            Standart Fan 4 (%)
                                        </td>
                                        @foreach($sessionRecords as $record)
                                            <td class="text-center">
                                                <span class="badge bg-secondary">{{ $record->stdFan->fan_4 ?? '-' }} %</span>
                                            </td>
                                        @endforeach
                                    </tr>
                                    
                                    <!-- Fan 4 (Aktual) -->
                                    <tr>
                                        <td class="align-middle font-weight-bold" style="background-color: #f8f9fa;">
                                            Fan 4 (%)
                                        </td>
                                        @foreach($sessionRecords as $record)
                                            <td class="text-center">
                                                <span class="badge bg-success">{{ $record->fan_4 ?? '-' }} %</span>
                                            </td>
                                        @endforeach
                                    </tr>
                                    
                                    <!-- Standar Humidity -->
                                    <tr>
                                        <td class="align-middle font-weight-bold" style="background-color: #f8f9fa;">
                                            Humidity/Steam Valve (Standard)%	
                                        </td>
                                        @foreach($sessionRecords as $record)
                                            <td class="text-center">
                                                <span class="badge bg-secondary">{{ $record->stdFan->std_humadity ?? '-' }}%</span>
                                            </td>
                                        @endforeach
                                    </tr>
                                    
                                    <!-- Humidity (Aktual) -->
                                    <tr>
                                        <td class="align-middle font-weight-bold" style="background-color: #f8f9fa;">
                                            Humidity/Steam Valve (Aktual)%
                                        </td>
                                        @foreach($sessionRecords as $record)
                                            <td class="text-center">
                                                <span class="badge bg-success">{{ $record->aktual_humadity ?? '-' }}%</span>
                                            </td>
                                        @endforeach
                                    </tr>
                                    
                                    <!-- Infra Red -->
                                    <tr>
                                        <td class="align-middle font-weight-bold" style="background-color: #f8f9fa;">
                                            Infra Red
                                        </td>
                                        @foreach($sessionRecords as $record)
                                            <td class="text-center">
                                                <span class="badge bg-info">{{ $record->infra_red ?? '-' }}</span>
                                            </td>
                                        @endforeach
                                    </tr>
                                    
                                    <!-- Standar Lama Proses -->
                                    <tr>
                                        <td class="align-middle font-weight-bold" style="background-color: #f8f9fa;">
                                            Standart Lama Proses (Menit)
                                        </td>
                                        <td class="text-center" colspan="{{ count($sessionRecords) }}">
                                            <span class="badge bg-secondary">{{ $sessionRecords->first()->stdFan->std_lama_proses ?? '-' }} menit</span>
                                        </td>
                                    </tr>
                                    
                                    <!-- Lama Proses (Aktual) -->
                                    <tr>
                                        <td class="align-middle font-weight-bold" style="background-color: #f8f9fa;">
                                            Lama Proses (Display Aktual; Menit)
                                        </td>
                                        <td class="text-center" colspan="{{ count($sessionRecords) }}">
                                            <span class="badge bg-primary">{{ $firstRecord->aktual_lama_proses ?? '-' }} menit</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">
                            <div class="">
                                <a href="{{ route('proses-roasting-fan.index') }}" class="btn btn-md btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                <!--
@if(auth()->user()->hasPermissionTo('edit-proses-roasting-fan')) <a href="{{ route('proses-roasting-fan.edit', $firstRecord->uuid) }}" class="btn btn-md btn-warning">
                                    <i class="fas fa-edit"></i> Update Data
                                </a> @endif
-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
