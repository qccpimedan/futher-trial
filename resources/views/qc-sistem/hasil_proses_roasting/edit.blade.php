@extends('layouts.app')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h1 class="m-0">Edit Data Hasil Proses Roasting</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('hasil-proses-roasting.index') }}">Hasil Proses Roasting</a></li>
                            <li class="breadcrumb-item active">Edit Data</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                                <h4 class="alert-heading"><i class="fas fa-exclamation-circle"></i> Error!</h4>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        <div class="card card-warning">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-temperature-high"></i> Form Edit Data Hasil Proses Roasting
                                </h3>
                            </div>
                            <form action="{{ route('hasil-proses-roasting.update', ['uuid' => $hasilProsesRoasting->uuid]) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <!-- Hidden UUID fields untuk shift detection -->
                                @if($hasilProsesRoasting->proses_roasting_fan_uuid)
                                    <input type="hidden" name="proses_roasting_fan_uuid" value="{{ $hasilProsesRoasting->proses_roasting_fan_uuid }}">
                                @endif
                                @if($hasilProsesRoasting->input_roasting_uuid)
                                    <input type="hidden" name="input_roasting_uuid" value="{{ $hasilProsesRoasting->input_roasting_uuid }}">
                                @endif
                                @if($hasilProsesRoasting->penggorengan_uuid)
                                    <input type="hidden" name="penggorengan_uuid" value="{{ $hasilProsesRoasting->penggorengan_uuid }}">
                                @endif
                                @if($hasilProsesRoasting->frayer_uuid)
                                    <input type="hidden" name="frayer_uuid" value="{{ $hasilProsesRoasting->frayer_uuid }}">
                                @endif
                                @if($hasilProsesRoasting->breader_uuid)
                                    <input type="hidden" name="breader_uuid" value="{{ $hasilProsesRoasting->breader_uuid }}">
                                @endif
                                @if($hasilProsesRoasting->battering_uuid)
                                    <input type="hidden" name="battering_uuid" value="{{ $hasilProsesRoasting->battering_uuid }}">
                                @endif
                                @if($hasilProsesRoasting->predust_uuid)
                                    <input type="hidden" name="predust_uuid" value="{{ $hasilProsesRoasting->predust_uuid }}">
                                @endif
                                <div class="card-body">
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
                                                                <label for="tanggal">
                                                                    <i class="fas fa-calendar-alt"></i> Tanggal
                                                                </label>
                                                                <input type="text" class="form-control @error('tanggal') is-invalid @enderror" 
                                                                       id="tanggal" name="tanggal" 
                                                                       value="{{ old('tanggal', $hasilProsesRoasting->tanggal ? $hasilProsesRoasting->tanggal->format('d-m-Y H:i:s') : '') }}" readonly>
                                                                @error('tanggal')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="jam">
                                                                    <i class="fas fa-clock"></i> Jam
                                                                </label>
                                                                <input type="time" class="form-control @error('jam') is-invalid @enderror" 
                                                                       id="jam" name="jam" 
                                                                       value="{{ old('jam', $hasilProsesRoasting->jam ?? '') }}" required>
                                                                @error('jam')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="id_produk">
                                                                    <i class="fas fa-box"></i> Produk
                                                                </label>
                                                                <select class="form-control @error('id_produk') is-invalid @enderror" 
                                                                        id="id_produk" name="id_produk" required>
                                                                    <option value="">Pilih Produk</option>
                                                                    @foreach($produks as $produk)
                                                                        <option value="{{ $produk->id }}" 
                                                                            {{ (old('id_produk', $hasilProsesRoasting->id_produk) == $produk->id) ? 'selected' : '' }}>
                                                                            {{ $produk->nama_produk }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                @error('id_produk')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- Parameter Proses -->
                                        <div class="col-12">
                                            <div class="card card-outline card-warning">
                                                <div class="card-header">
                                                    <h3 class="card-title">
                                                        <i class="fas fa-thermometer-half"></i> Parameter Proses
                                                    </h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="id_std_suhu_pusat">
                                                            <i class="fas fa-temperature-high"></i> Std Suhu Pusat
                                                        </label>
                                                        <select class="form-control @error('id_std_suhu_pusat') is-invalid @enderror" 
                                                                id="id_std_suhu_pusat" name="id_std_suhu_pusat" required>
                                                            <option value="">Pilih Std Suhu Pusat</option>
                                                            @php
                                                                $selectedId = old('id_std_suhu_pusat', $hasilProsesRoasting->id_std_suhu_pusat);
                                                            @endphp
                                                            @foreach($stdSuhuPusats as $stdSuhu)
                                                                <option value="{{ $stdSuhu->id }}" 
                                                                    @if($selectedId == $stdSuhu->id) selected @endif>
                                                                    {{ $stdSuhu->std_suhu_pusat_roasting }}°C
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('id_std_suhu_pusat')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group">
                                                        <label>
                                                            <i class="fas fa-thermometer-three-quarters"></i> <strong>Aktual Suhu Pusat</strong>
                                                        </label>
                                                        <div class="card card-light">
                                                            <div class="card-header bg-light">
                                                                <button type="button" class="btn btn-sm btn-success" id="addSuhuBtn">
                                                                    <i class="fas fa-plus"></i> Tambah Suhu
                                                                </button>
                                                            </div>
                                                            <div class="card-body">
                                                                <div id="suhuContainer">
                                                                    @php
                                                                        $suhuArray = is_string($hasilProsesRoasting->aktual_suhu_pusat) 
                                                                            ? json_decode($hasilProsesRoasting->aktual_suhu_pusat, true) 
                                                                            : [$hasilProsesRoasting->aktual_suhu_pusat];
                                                                        $suhuArray = is_array($suhuArray) ? $suhuArray : [];
                                                                    @endphp
                                                                    @forelse($suhuArray as $index => $suhu)
                                                                        <div class="input-group mb-2 suhu-row">
                                                                            <input type="text" 
                                                                                   class="form-control aktual-suhu-input" 
                                                                                   name="aktual_suhu_pusat[]" 
                                                                                   placeholder="Contoh: 75°C" 
                                                                                   value="{{ old("aktual_suhu_pusat.$index", $suhu) }}" required>
                                                                            <div class="input-group-append">
                                                                                <button type="button" class="btn btn-danger removeSuhuBtn">
                                                                                    <i class="fas fa-trash"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    @empty
                                                                        <div class="input-group mb-2 suhu-row">
                                                                            <input type="text" 
                                                                                   class="form-control aktual-suhu-input" 
                                                                                   name="aktual_suhu_pusat[]" 
                                                                                   placeholder="Contoh: 75°C" required>
                                                                            <div class="input-group-append">
                                                                                <button type="button" class="btn btn-danger removeSuhuBtn">
                                                                                    <i class="fas fa-trash"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    @endforelse
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @error('aktual_suhu_pusat')
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group">
                                                        <label>
                                                            <i class="fas fa-eye"></i> <strong>Evaluasi Sensori</strong>
                                                        </label>
                                                        <div class="card card-light">
                                                            <div class="card-header bg-light">
                                                                <button type="button" class="btn btn-sm btn-success" id="addSensoriBtn">
                                                                    <i class="fas fa-plus"></i> Tambah Parameter Sensori
                                                                </button>
                                                            </div>
                                                            <div class="card-body">
                                                                <div id="sensoriContainer">
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
                                                                        $directSensori = [];
                                                                        $groupedSensoriByGroup = [];
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
                                                                        
                                                                        // Build final groups array
                                                                        $allSensoriGroups = [];
                                                                        if(!empty($directSensori)) {
                                                                            $allSensoriGroups['direct'] = $directSensori;
                                                                        }
                                                                        
                                                                        sort($groupIds);
                                                                        foreach($groupIds as $groupId) {
                                                                            $allSensoriGroups[$groupId] = $groupedSensoriByGroup[$groupId];
                                                                        }
                                                                        
                                                                        // If no data, create empty default group
                                                                        if(empty($allSensoriGroups)) {
                                                                            $allSensoriGroups['default'] = [];
                                                                        }
                                                                    @endphp
                                                                    @foreach($allSensoriGroups as $groupKey => $sensoriGroup)
                                                                        <div class="sensori-group mb-3" data-group="{{ $groupKey }}">
                                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                                <h5 class="mb-0">Grup Sensori {{ $loop->iteration }}</h5>
                                                                                <button type="button" class="btn btn-sm btn-danger removeSensoriGroupBtn">
                                                                                    <i class="fas fa-trash"></i> Hapus Grup Ini
                                                                                </button>
                                                                            </div>
                                                                            <div class="table-responsive">
                                                                                <table class="table table-bordered table-sm sensori-table">
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
                                                                                                    <div class="custom-control custom-radio">
                                                                                                        <input type="radio" class="custom-control-input" 
                                                                                                               id="sensori_{{ $groupKey }}_{{ $key }}_ok" 
                                                                                                               name="sensori_{{ $groupKey }}_{{ $key }}" 
                                                                                                               value="OK"
                                                                                                               {{ isset($sensoriGroup[$key]) && $sensoriGroup[$key] == 'OK' ? 'checked' : '' }}>
                                                                                                        <label class="custom-control-label" for="sensori_{{ $groupKey }}_{{ $key }}_ok">
                                                                                                            <i class="fas fa-check text-success"></i>
                                                                                                        </label>
                                                                                                    </div>
                                                                                                </td>
                                                                                                <td class="text-center">
                                                                                                    <div class="custom-control custom-radio">
                                                                                                        <input type="radio" class="custom-control-input" 
                                                                                                               id="sensori_{{ $groupKey }}_{{ $key }}_tidak_ok" 
                                                                                                               name="sensori_{{ $groupKey }}_{{ $key }}" 
                                                                                                               value="Tidak OK"
                                                                                                               {{ isset($sensoriGroup[$key]) && $sensoriGroup[$key] == 'Tidak OK' ? 'checked' : '' }}>
                                                                                                        <label class="custom-control-label" for="sensori_{{ $groupKey }}_{{ $key }}_tidak_ok">
                                                                                                            <i class="fas fa-times text-danger"></i>
                                                                                                        </label>
                                                                                                    </div>
                                                                                                </td>
                                                                                            </tr>
                                                                                        @endforeach
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @error('sensori')
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-warning ">
                                        <i class="fas fa-save"></i> Update
                                    </button>
                                    <a href="{{ route('hasil-proses-roasting.index') }}" class="ml-2 btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sensoriItems = {
        'kematangan': 'Kematangan',
        'kenampakan': 'Kenampakan',
        'warna': 'Warna',
        'rasa': 'Rasa',
        'bau': 'Bau',
        'tekstur': 'Tekstur'
    };

    // ===== Dynamic Aktual Suhu Pusat =====
    const addSuhuBtn = document.getElementById('addSuhuBtn');
    const suhuContainer = document.getElementById('suhuContainer');

    if (addSuhuBtn) {
        addSuhuBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const newRow = document.createElement('div');
            newRow.className = 'input-group mb-2 suhu-row';
            newRow.innerHTML = `
                <input type="text" 
                       class="form-control aktual-suhu-input" 
                       name="aktual_suhu_pusat[]" 
                       placeholder="Contoh: 75°C" required>
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger removeSuhuBtn">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            suhuContainer.appendChild(newRow);
        });
    }

    document.addEventListener('click', function(e) {
        if (e.target.closest('.removeSuhuBtn')) {
            const rows = suhuContainer.querySelectorAll('.suhu-row').length;
            if (rows > 1) {
                e.target.closest('.suhu-row').remove();
            } else {
                alert('Minimal harus ada 1 input Suhu Pusat');
            }
        }
    });

    // ===== Dynamic Sensori Parameters =====
    const addSensoriBtn = document.getElementById('addSensoriBtn');
    const sensoriContainer = document.getElementById('sensoriContainer');

    if (addSensoriBtn) {
        addSensoriBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const timestamp = Date.now();
            const groupKey = 'sensori_' + timestamp;

            const newGroup = document.createElement('div');
            newGroup.className = 'sensori-group mb-3';
            newGroup.setAttribute('data-group', groupKey);

            let tableHTML = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Grup Sensori</h5>
                    <button type="button" class="btn btn-sm btn-danger removeSensoriGroupBtn">
                        <i class="fas fa-trash"></i> Hapus Grup Ini
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm sensori-table">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 40%;">Parameter</th>
                                <th style="width: 30%;" class="text-center">OK</th>
                                <th style="width: 30%;" class="text-center">Tidak OK</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            for (const [key, label] of Object.entries(sensoriItems)) {
                tableHTML += `
                    <tr>
                        <td>
                            <strong>${label}</strong>
                        </td>
                        <td class="text-center">
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input" 
                                       id="sensori_${groupKey}_${key}_ok" 
                                       name="sensori_${groupKey}_${key}" 
                                       value="OK">
                                <label class="custom-control-label" for="sensori_${groupKey}_${key}_ok">
                                    <i class="fas fa-check text-success"></i>
                                </label>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input" 
                                       id="sensori_${groupKey}_${key}_tidak_ok" 
                                       name="sensori_${groupKey}_${key}" 
                                       value="Tidak OK">
                                <label class="custom-control-label" for="sensori_${groupKey}_${key}_tidak_ok">
                                    <i class="fas fa-times text-danger"></i>
                                </label>
                            </div>
                        </td>
                    </tr>
                `;
            }

            tableHTML += `
                        </tbody>
                    </table>
                </div>
            `;

            newGroup.innerHTML = tableHTML;
            sensoriContainer.appendChild(newGroup);
        });
    }

    document.addEventListener('click', function(e) {
        if (e.target.closest('.removeSensoriGroupBtn')) {
            const groups = sensoriContainer.querySelectorAll('.sensori-group').length;
            if (groups > 1) {
                e.target.closest('.sensori-group').remove();
            } else {
                alert('Minimal harus ada 1 grup Parameter Sensori');
            }
        }
    });
});
</script>
@endsection