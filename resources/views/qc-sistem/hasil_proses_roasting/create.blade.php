@extends('layouts.app')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h1 class="m-0">Tambah Data Hasil Proses Roasting</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('hasil-proses-roasting.index') }}">Hasil Proses Roasting</a></li>
                            <li class="breadcrumb-item active">Tambah Data</li>
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
                                    <i class="fas fa-temperature-high"></i> Form Tambah Data Hasil Proses Roasting
                                </h3>
                            </div>
                            <form action="{{ route('hasil-proses-roasting.store') }}" method="POST">
                            @csrf

                            <!-- Error Alert -->
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                                    <h5><i class="fas fa-exclamation-triangle"></i> Terdapat Kesalahan!</h5>
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

                            <!-- Warning Alert jika data UUID kosong -->
                            @if(!$prosesRoastingFanUuid && !$inputRoastingUuid && !$penggorenganUuid)
                                <div class="alert alert-warning alert-dismissible fade show m-3" role="alert">
                                    <h5><i class="fas fa-exclamation-circle"></i> Peringatan!</h5>
                                    <p class="mb-0">Tidak ada data proses sebelumnya yang terhubung. Pastikan Anda telah membuat data Input Roasting atau Proses Roasting Fan terlebih dahulu.</p>
                                    <hr>
                                    <div class="mb-0">
                                        <a href="{{ route('input-roasting.index') }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-arrow-left"></i> Ke Input Roasting
                                        </a>
                                        <a href="{{ route('proses-roasting-fan.index') }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-arrow-left"></i> Ke Proses Roasting Fan
                                        </a>
                                    </div>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                                <!-- Hidden UUID fields -->
                                <input type="hidden" name="proses_roasting_fan_uuid" value="{{ $prosesRoastingFanUuid }}">
                                <input type="hidden" name="input_roasting_uuid" value="{{ $inputRoastingUuid }}">
                                <input type="hidden" name="frayer_uuid" value="{{ $frayerUuid }}">
                                <input type="hidden" name="breader_uuid" value="{{ $breaderUuid }}">
                                <input type="hidden" name="battering_uuid" value="{{ $batteringUuid }}">
                                <input type="hidden" name="predust_uuid" value="{{ $predustUuid }}">
                                <input type="hidden" name="penggorengan_uuid" value="{{ $penggorenganUuid }}">
                                <div class="card-body">
                                    <!-- Informasi Data Terkait -->
                                    @if($inputRoastingData || $prosesRoastingFanData)
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <div class="alert alert-info">
                                                <h5><i class="fas fa-info-circle"></i> Informasi Data Terkait</h5>
                                                
                                                @if($inputRoastingData)
                                                <div class="mb-2">
                                                    <strong>Input Roasting:</strong>
                                                    <span class="badge badge-primary">{{ $inputRoastingData->produk->nama_produk ?? 'N/A' }}</span>
                                                    <span class="badge badge-info">{{ $inputRoastingData->shift->shift ?? 'N/A' }}</span>
                                                    <small class="text-muted">({{ $inputRoastingData->tanggal ? \Carbon\Carbon::parse($inputRoastingData->tanggal)->format('d-m-Y H:i') : 'N/A' }})</small>
                                                </div>
                                                @endif
                                                
                                                @if($prosesRoastingFanData)
                                                <div class="mb-2">
                                                    <strong>Proses Roasting Fan:</strong>
                                                    <span class="badge badge-primary">{{ $prosesRoastingFanData->produk->nama_produk ?? 'N/A' }}</span>
                                                    <span class="badge badge-info">{{ $prosesRoastingFanData->shift->shift ?? 'N/A' }}</span>
                                                    <small class="text-muted">({{ $prosesRoastingFanData->tanggal ? \Carbon\Carbon::parse($prosesRoastingFanData->tanggal)->format('d-m-Y H:i') : 'N/A' }})</small>
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
                                                    <div class="form-group">
                                                        <label for="tanggal">
                                                            <i class="fas fa-calendar-alt"></i> Tanggal
                                                        </label>
                                                        @php
                                                            $userRole = auth()->user()->id_role ?? null;
                                                            $showTime = in_array($userRole, [1, 2, 5]); // superadmin, admin, spv
                                                            $displayFormat = $showTime ? 'd-m-Y H:i:s' : 'd-m-Y';
                                                            $submitFormat = 'd-m-Y H:i:s'; // Always submit with H:i:s
                                                            $now = \Carbon\Carbon::now('Asia/Jakarta');
                                                            $displayValue = $now->format($displayFormat);
                                                            $submitValue = $now->format($submitFormat);
                                                        @endphp
                                                        <input type="hidden" name="tanggal" id="tanggal_hidden" 
                                                                value="{{ old('tanggal', $submitValue) }}">
                                                        <input type="text" class="form-control" id="tanggal_display" 
                                                                value="{{ old('tanggal', $displayValue) }}" readonly required>
                                                        @error('tanggal')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="jam">
                                                            <i class="fas fa-clock"></i> Jam
                                                        </label>
                                                        <input type="time" class="form-control @error('jam') is-invalid @enderror" 
                                                               id="jam" name="jam" value="{{ old('jam', now()->timezone('Asia/Jakarta')->format('H:i')) }}" required>
                                                        @error('jam')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <!-- <div class="form-group">
                                                        <label for="id_produk">
                                                            <i class="fas fa-box"></i> Produk
                                                        </label>
                                                        <select class="form-control @error('id_produk') is-invalid @enderror" 
                                                                id="id_produk" name="id_produk" required>
                                                            <option value="">Pilih Produk</option>
                                                            @foreach($produks as $produk)
                                                                <option value="{{ $produk->id }}" {{ old('id_produk') == $produk->id ? 'selected' : '' }}>
                                                                    {{ $produk->nama_produk }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('id_produk')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div> -->
                                                    <div class="form-group">
                                                        <label for="id_produk">
                                                            <i class="fas fa-box"></i> Produk
                                                        </label>
                                                        <!-- Hidden input untuk plan ID -->
                                                        <input type="hidden" name="id_plan" value="{{ Auth::user()->id_plan }}">
                                                        
                                                        <select class="form-control @error('id_produk') is-invalid @enderror" 
                                                                id="id_produk" name="id_produk" required>
                                                            <option value="">Pilih Produk</option>
                                                            @foreach($produks as $produk)
                                                                <option value="{{ $produk->id }}" {{ old('id_produk') == $produk->id ? 'selected' : '' }}>
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
                                                                id="id_std_suhu_pusat" name="id_std_suhu_pusat" required disabled>
                                                            <option value="">Pilih Std Suhu Pusat</option>
                                                        </select>
                                                        @error('id_std_suhu_pusat')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group">
                                                        <label>
                                                            <i class="fas fa-thermometer-three-quarters"></i> <strong>Aktual Suhu Pusat & Evaluasi Sensori</strong>
                                                        </label>
                                                        <div class="card card-light">
                                                            <div class="card-header bg-light">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <button type="button" class="btn btn-sm btn-success" id="addSuhuBtn">
                                                                            <i class="fas fa-plus"></i> Tambah Suhu
                                                                        </button>
                                                                    </div>
                                                                    <div class="col-md-6 text-right">
                                                                        <button type="button" class="btn btn-sm btn-success" id="addSensoriBtn">
                                                                            <i class="fas fa-plus"></i> Tambah Parameter Sensori
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="card-body">
                                                                <!-- Aktual Suhu Pusat Section -->
                                                                <h6 class="mb-3"><i class="fas fa-thermometer-three-quarters"></i> Aktual Suhu Pusat</h6>
                                                                <div id="suhuContainer" class="mb-4">
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
                                                                </div>

                                                                <hr>

                                                                <!-- Evaluasi Sensori Section -->
                                                                <h6 class="mb-3"><i class="fas fa-eye"></i> Evaluasi Sensori</h6>
                                                                <div id="sensoriGroupsContainer">
                                                                    <!-- Default Group -->
                                                                    <div class="sensori-group-wrapper mb-3" data-group="default">
                                                                        <div class="table-responsive">
                                                                            <table class="table table-bordered table-sm sensori-group-table">
                                                                                <thead class="bg-light">
                                                                                    <tr>
                                                                                        <th style="width: 40%;">Parameter</th>
                                                                                        <th style="width: 30%;" class="text-center">OK</th>
                                                                                        <th style="width: 30%;" class="text-center">Tidak OK</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody class="sensori-group-body" data-group="default">
                                                                                    @php
                                                                                        $sensoriItems = [
                                                                                            'kematangan' => 'Kematangan',
                                                                                            'kenampakan' => 'Kenampakan',
                                                                                            'warna' => 'Warna',
                                                                                            'rasa' => 'Rasa',
                                                                                            'bau' => 'Bau',
                                                                                            'tekstur' => 'Tekstur'
                                                                                        ];
                                                                                    @endphp
                                                                                    @foreach($sensoriItems as $key => $label)
                                                                                        <tr class="sensori-row" data-key="{{ $key }}" data-group="default">
                                                                                            <td>
                                                                                                <strong>{{ $label }}</strong>
                                                                                            </td>
                                                                                            <td class="text-center">
                                                                                                <div class="custom-control custom-radio">
                                                                                                    <input type="radio" class="custom-control-input" 
                                                                                                           id="sensori_{{ $key }}_ok" 
                                                                                                           name="sensori_{{ $key }}" 
                                                                                                           value="OK"
                                                                                                           {{ old("sensori_$key") == 'OK' ? 'checked' : '' }}>
                                                                                                    <label class="custom-control-label" for="sensori_{{ $key }}_ok">
                                                                                                        <i class="fas fa-check text-success"></i>
                                                                                                    </label>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td class="text-center">
                                                                                                <div class="custom-control custom-radio">
                                                                                                    <input type="radio" class="custom-control-input" 
                                                                                                           id="sensori_{{ $key }}_tidak_ok" 
                                                                                                           name="sensori_{{ $key }}" 
                                                                                                           value="Tidak OK"
                                                                                                           {{ old("sensori_$key") == 'Tidak OK' ? 'checked' : '' }}>
                                                                                                    <label class="custom-control-label" for="sensori_{{ $key }}_tidak_ok">
                                                                                                        <i class="fas fa-times text-danger"></i>
                                                                                                    </label>
                                                                                                </div>
                                                                                            </td>
                                                                                        </tr>
                                                                                    @endforeach
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                        <div class="mt-2">
                                                                            <button type="button" class="btn btn-sm btn-danger deleteGroupBtn" data-group="default">
                                                                                <i class="fas fa-trash"></i> Hapus Table Ini
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @error('aktual_suhu_pusat')
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
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
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Simpan
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
    // ===== Dynamic Aktual Suhu Pusat =====
    document.getElementById('addSuhuBtn').addEventListener('click', function(e) {
        e.preventDefault();
        const container = document.getElementById('suhuContainer');
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
        container.appendChild(newRow);
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.removeSuhuBtn')) {
            const rows = document.querySelectorAll('#suhuContainer .suhu-row').length;
            if (rows > 1) {
                e.target.closest('.suhu-row').remove();
            } else {
                alert('Minimal harus ada 1 input Suhu Pusat');
            }
        }
    });

    // ===== Dynamic Sensori Parameters =====
    document.getElementById('addSensoriBtn').addEventListener('click', function(e) {
        e.preventDefault();
        const sensoriItems = {
            'kematangan': 'Kematangan',
            'kenampakan': 'Kenampakan',
            'warna': 'Warna',
            'rasa': 'Rasa',
            'bau': 'Bau',
            'tekstur': 'Tekstur'
        };

        const timestamp = Date.now();
        const groupKey = 'group_' + timestamp;
        const container = document.getElementById('sensoriGroupsContainer');

        // Create wrapper for this group
        const groupWrapper = document.createElement('div');
        groupWrapper.className = 'sensori-group-wrapper mb-3';
        groupWrapper.setAttribute('data-group', groupKey);

        // Create table
        const tableDiv = document.createElement('div');
        tableDiv.className = 'table-responsive';
        
        const table = document.createElement('table');
        table.className = 'table table-bordered table-sm sensori-group-table';
        
        const thead = document.createElement('thead');
        thead.className = 'bg-light';
        thead.innerHTML = `
            <tr>
                <th style="width: 40%;">Parameter</th>
                <th style="width: 30%;" class="text-center">OK</th>
                <th style="width: 30%;" class="text-center">Tidak OK</th>
            </tr>
        `;
        
        const tbody = document.createElement('tbody');
        tbody.className = 'sensori-group-body';
        tbody.setAttribute('data-group', groupKey);

        // Add 6 rows (one for each sensori parameter)
        for (const [key, label] of Object.entries(sensoriItems)) {
            const newKey = groupKey + '_' + key;

            const newRow = document.createElement('tr');
            newRow.className = 'sensori-row';
            newRow.setAttribute('data-key', newKey);
            newRow.setAttribute('data-group', groupKey);
            newRow.innerHTML = `
                <td>
                    <strong>${label}</strong>
                </td>
                <td class="text-center">
                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" 
                               id="sensori_${newKey}_ok" 
                               name="sensori_${newKey}" 
                               value="OK">
                        <label class="custom-control-label" for="sensori_${newKey}_ok">
                            <i class="fas fa-check text-success"></i>
                        </label>
                    </div>
                </td>
                <td class="text-center">
                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" 
                               id="sensori_${newKey}_tidak_ok" 
                               name="sensori_${newKey}" 
                               value="Tidak OK">
                        <label class="custom-control-label" for="sensori_${newKey}_tidak_ok">
                            <i class="fas fa-times text-danger"></i>
                        </label>
                    </div>
                </td>
            `;
            tbody.appendChild(newRow);
        }

        table.appendChild(thead);
        table.appendChild(tbody);
        tableDiv.appendChild(table);
        groupWrapper.appendChild(tableDiv);

        // Add delete button
        const deleteDiv = document.createElement('div');
        deleteDiv.className = 'mt-2';
        deleteDiv.innerHTML = `
            <button type="button" class="btn btn-sm btn-danger deleteGroupBtn" data-group="${groupKey}">
                <i class="fas fa-trash"></i> Hapus Table Ini
            </button>
        `;
        groupWrapper.appendChild(deleteDiv);

        // Add wrapper to container
        container.appendChild(groupWrapper);
    });

    // Delete groups
    document.addEventListener('click', function(e) {
        if (e.target.closest('.deleteGroupBtn')) {
            const groupKey = e.target.closest('.deleteGroupBtn').getAttribute('data-group');
            const groupWrapper = document.querySelector(`.sensori-group-wrapper[data-group="${groupKey}"]`);
            if (groupWrapper) {
                groupWrapper.remove();
            }
        }
    });
});
</script>
@endsection