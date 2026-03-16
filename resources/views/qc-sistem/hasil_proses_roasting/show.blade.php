@extends('layouts.app')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h1 class="m-0">Detail Hasil Proses Roasting</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('hasil-proses-roasting.index') }}">Hasil Proses Roasting</a></li>
                            <li class="breadcrumb-item active">Detail</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-temperature-high"></i> Detail Data Hasil Proses Roasting
                                </h3>
                            </div>
                            <div class="card-body">
                                <!-- Informasi Data Terkait -->
                                @if($hasilProsesRoasting->inputRoasting || $hasilProsesRoasting->prosesRoastingFan)
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            <h5><i class="fas fa-info-circle"></i> Informasi Data Terkait</h5>
                                            
                                            @if($hasilProsesRoasting->inputRoasting)
                                            <div class="mb-2">
                                                <strong>Input Roasting:</strong>
                                                <span class="badge badge-primary">{{ $hasilProsesRoasting->inputRoasting->produk->nama_produk ?? 'N/A' }}</span>
                                                <span class="badge badge-info">{{ $hasilProsesRoasting->inputRoasting->shift->shift ?? 'N/A' }}</span>
                                                <small class="text-muted">({{ $hasilProsesRoasting->inputRoasting->tanggal ? \Carbon\Carbon::parse($hasilProsesRoasting->inputRoasting->tanggal)->format('d-m-Y H:i') : 'N/A' }})</small>
                                            </div>
                                            @endif
                                            
                                            @if($hasilProsesRoasting->prosesRoastingFan)
                                            <div class="mb-2">
                                                <strong>Proses Roasting Fan:</strong>
                                                <span class="badge badge-primary">{{ $hasilProsesRoasting->prosesRoastingFan->produk->nama_produk ?? 'N/A' }}</span>
                                                <span class="badge badge-info">{{ $hasilProsesRoasting->prosesRoastingFan->shift->shift ?? 'N/A' }}</span>
                                                <small class="text-muted">({{ $hasilProsesRoasting->prosesRoastingFan->tanggal ? \Carbon\Carbon::parse($hasilProsesRoasting->prosesRoastingFan->tanggal)->format('d-m-Y H:i') : 'N/A' }})</small>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <div class="row">
                                    <!-- Informasi Dasar -->
                                    <div class="col-12">
                                        <div class="card card-outline card-secondary">
                                            <div class="card-header">
                                                <h3 class="card-title">
                                                    <i class="fas fa-info-circle"></i> Informasi Dasar
                                                </h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label><i class="fas fa-calendar-alt"></i> Tanggal</label>
                                                            <p>{{ $hasilProsesRoasting->tanggal ? \Carbon\Carbon::parse($hasilProsesRoasting->tanggal)->format('d-m-Y H:i:s') : 'N/A' }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label><i class="fas fa-clock"></i> Jam</label>
                                                            <p>{{ $hasilProsesRoasting->jam ?? 'N/A' }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label><i class="fas fa-box"></i> Produk</label>
                                                            <p>{{ $hasilProsesRoasting->produk->nama_produk ?? 'N/A' }}</p>
                                                        </div>
                                                    </div>
                                                    <!-- <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label><i class="fas fa-user"></i> User</label>
                                                            <p>{{ $hasilProsesRoasting->user->name ?? 'N/A' }}</p>
                                                        </div>
                                                    </div> -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <!-- Parameter Proses -->
                                    <div class="col-12">
                                        <div class="card card-outline card-warning">
                                            <div class="card-header">
                                                <h3 class="card-title">
                                                    <i class="fas fa-thermometer-half"></i> Parameter Proses
                                                </h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label><i class="fas fa-temperature-high"></i> Std Suhu Pusat</label>
                                                            <p>{{ $hasilProsesRoasting->stdSuhuPusat->std_suhu_pusat_roasting ?? 'N/A' }}°C</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label><i class="fas fa-thermometer-three-quarters"></i> Aktual Suhu Pusat</label>
                                                            <p>
                                                                @php
                                                                    $suhuArray = is_string($hasilProsesRoasting->aktual_suhu_pusat) 
                                                                        ? json_decode($hasilProsesRoasting->aktual_suhu_pusat, true) 
                                                                        : [$hasilProsesRoasting->aktual_suhu_pusat];
                                                                    $suhuArray = is_array($suhuArray) ? $suhuArray : [];
                                                                @endphp
                                                                @if(count($suhuArray) > 0)
                                                                    @foreach($suhuArray as $suhu)
                                                                        <span class="badge badge-info">{{ $suhu }}</span>
                                                                    @endforeach
                                                                @else
                                                                    N/A
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <!-- Evaluasi Sensori -->
                                    <div class="col-12">
                                        <div class="card card-outline card-info">
                                            <div class="card-header">
                                                <h3 class="card-title">
                                                    <i class="fas fa-eye"></i> Evaluasi Sensori
                                                </h3>
                                            </div>
                                            <div class="card-body">
                                                @php
                                                    $sensoriItems = [
                                                        'kematangan' => 'Kematangan',
                                                        'kenampakan' => 'Kenampakan',
                                                        'warna' => 'Warna',
                                                        'rasa' => 'Rasa',
                                                        'bau' => 'Bau',
                                                        'tekstur' => 'Tekstur'
                                                    ];
                                                    $sensoriData = is_array($hasilProsesRoasting->sensori) ? $hasilProsesRoasting->sensori : [];
                                                    
                                                    // Parse sensori data - handle multiple groups
                                                    // Format 1: sensori_[parameter] (old format, group 1)
                                                    // Format 2: sensori_group_[timestamp]_[parameter] (new format, group 2+)
                                                    $allSensoriGroups = [];
                                                    $groupedSensoriByGroup = [];
                                                    $directSensori = [];
                                                    $groupIds = [];
                                                    
                                                    foreach($sensoriData as $key => $value) {
                                                        if(strpos($key, 'sensori_group_') === 0) {
                                                            // Format: sensori_group_[timestamp]_[parameter]
                                                            preg_match('/sensori_group_(\d+)_(.+)/', $key, $matches);
                                                            if(count($matches) >= 3) {
                                                                $groupId = $matches[1];
                                                                $paramName = $matches[2];
                                                                
                                                                if(!in_array($groupId, $groupIds)) {
                                                                    $groupIds[] = $groupId;
                                                                }
                                                                
                                                                if(!isset($groupedSensoriByGroup[$groupId])) {
                                                                    $groupedSensoriByGroup[$groupId] = [];
                                                                }
                                                                $groupedSensoriByGroup[$groupId][$paramName] = $value;
                                                            }
                                                        } elseif(strpos($key, 'sensori_direct_') === 0) {
                                                            // Format: sensori_direct_[parameter] (direct group format)
                                                            $paramName = str_replace('sensori_direct_', '', $key);
                                                            $directSensori[$paramName] = $value;
                                                        } elseif(strpos($key, 'sensori_') === 0 && !strpos($key, 'sensori_group_')) {
                                                            // Format: sensori_[parameter] (old format or numbered group)
                                                            // Check if it's a numbered group like sensori_1765867515217_[param]
                                                            preg_match('/sensori_(\d+)_(.+)/', $key, $matches);
                                                            if(count($matches) >= 3) {
                                                                // It's a numbered group
                                                                $groupId = $matches[1];
                                                                $paramName = $matches[2];
                                                                
                                                                if(!in_array($groupId, $groupIds)) {
                                                                    $groupIds[] = $groupId;
                                                                }
                                                                
                                                                if(!isset($groupedSensoriByGroup[$groupId])) {
                                                                    $groupedSensoriByGroup[$groupId] = [];
                                                                }
                                                                $groupedSensoriByGroup[$groupId][$paramName] = $value;
                                                            } else {
                                                                // It's a simple sensori_[parameter]
                                                                $paramName = str_replace('sensori_', '', $key);
                                                                $directSensori[$paramName] = $value;
                                                            }
                                                        }
                                                    }
                                                    
                                                    // Build final groups array - include both direct and grouped
                                                    // Add direct sensori as first group if exists
                                                    if(!empty($directSensori)) {
                                                        $allSensoriGroups['direct'] = $directSensori;
                                                    }
                                                    
                                                    // Add grouped sensori
                                                    sort($groupIds);
                                                    foreach($groupIds as $groupId) {
                                                        $allSensoriGroups[$groupId] = $groupedSensoriByGroup[$groupId];
                                                    }
                                                @endphp
                                                @foreach($allSensoriGroups as $groupIndex => $sensoriGroup)
                                                    @if(!empty($sensoriGroup))
                                                        <div class="mb-4">
                                                            @if(count($allSensoriGroups) > 1)
                                                                <h5 class="mb-3">
                                                                    <i class="fas fa-layer-group"></i> Grup Sensori {{ $loop->iteration }}
                                                                </h5>
                                                            @endif
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered table-sm">
                                                                    <thead class="bg-light">
                                                                        <tr>
                                                                            <th style="width: 40%;">Parameter</th>
                                                                            <th style="width: 30%;" class="text-center">OK</th>
                                                                            <th style="width: 30%;" class="text-center">Tidak OK</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($sensoriItems as $key => $label)
                                                                            <tr>
                                                                                <td>
                                                                                    <strong>{{ $label }}</strong>
                                                                                </td>
                                                                                <td class="text-center">
                                                                                    @if(isset($sensoriGroup[$key]) && $sensoriGroup[$key] == 'OK')
                                                                                        <i class="fas fa-check text-success"></i>
                                                                                    @endif
                                                                                </td>
                                                                                <td class="text-center">
                                                                                    @if(isset($sensoriGroup[$key]) && $sensoriGroup[$key] == 'Tidak OK')
                                                                                        <i class="fas fa-times text-danger"></i>
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
@if(auth()->user()->hasPermissionTo('edit-hasil-proses-roasting'))
                                <a href="{{ route('hasil-proses-roasting.edit', ['uuid' => $hasilProsesRoasting->uuid]) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                @endif
<a href="{{ route('hasil-proses-roasting.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
