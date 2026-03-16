@extends('layouts.app')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h1 class="m-0">Tambah Data Hasil Penggorengan</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('hasil-penggorengan.index') }}">Hasil Penggorengan</a></li>
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
                                    <i class="fas fa-plus"></i> Form Tambah Hasil Penggorengan
                                </h3>
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form class="form-horizontal mb-4" method="POST" action="{{ route('hasil-penggorengan.store') }}">
                                @csrf
                                
                                @if($frayerData || $frayer2Data || $breaderData || $batteringData || $predustData || $penggorenganData)
                                    @if($frayerData)
                                        <input type="hidden" name="frayer_uuid" value="{{ $frayerData->uuid }}">
                                    @endif
                                    @if($frayer2Data)
                                        <input type="hidden" name="frayer2_uuid" value="{{ $frayer2Data->uuid }}">
                                    @endif
                                    @if($breaderData)
                                        <input type="hidden" name="breader_uuid" value="{{ $breaderData->uuid }}">
                                    @endif
                                    @if($batteringData)
                                        <input type="hidden" name="battering_uuid" value="{{ $batteringData->uuid }}">
                                    @endif
                                    @if($predustData)
                                        <input type="hidden" name="predust_uuid" value="{{ $predustData->uuid }}">
                                    @endif
                                    @if($penggorenganData)
                                        <input type="hidden" name="penggorengan_uuid" value="{{ $penggorenganData->uuid }}">
                                    @endif
                                @endif
                                
                                @if($frayerData || $frayer2Data || $breaderData || $batteringData || $predustData || $penggorenganData)
                                <div class="card-body">
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h5><i class="fas fa-project-diagram"></i> Data Proses Terkait</h5>
                                            <div class="row">
                                                @if($penggorenganData)
                                                <div class="col-md-3 col-sm-6">
                                                    <div class="info-box bg-info">
                                                        <span class="info-box-icon"><i class="fas fa-fire"></i></span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">Penggorengan</span>
                                                            <span class="info-box-number">{{ $penggorenganData->kode_proses ?? '-' }}</span>
                                                            <span class="progress-description">
                                                                {{ $penggorenganData->produk->nama_produk ?? '' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                @if($predustData)
                                                <div class="col-md-3 col-sm-6">
                                                    <div class="info-box bg-warning">
                                                        <span class="info-box-icon"><i class="fas fa-powder"></i></span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">Predust</span>
                                                            <span class="info-box-number">{{ $predustData->kode_proses ?? '-' }}</span>
                                                            <span class="progress-description">
                                                                {{ $predustData->produk->nama_produk ?? '' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                @if($batteringData)
                                                <div class="col-md-3 col-sm-6">
                                                    <div class="info-box bg-success">
                                                        <span class="info-box-icon"><i class="fas fa-tint"></i></span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">Battering</span>
                                                            <span class="info-box-number">{{ $batteringData->kode_proses ?? '-' }}</span>
                                                            <span class="progress-description">
                                                                {{ $batteringData->produk->nama_produk ?? '' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                @if($breaderData)
                                                <div class="col-md-3 col-sm-6">
                                                    <div class="info-box bg-primary">
                                                        <span class="info-box-icon"><i class="fas fa-bread-slice"></i></span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">Breader</span>
                                                            <span class="info-box-number">{{ $breaderData->kode_proses ?? '-' }}</span>
                                                            <span class="progress-description">
                                                                {{ $breaderData->produk->nama_produk ?? '' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                @if($frayerData)
                                                <div class="col-md-3 col-sm-6">
                                                    <div class="info-box bg-danger">
                                                        <span class="info-box-icon"><i class="fas fa-fire"></i></span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">Frayer 1</span>
                                                            <span class="info-box-number">{{ $frayerData->kode_proses ?? '-' }}</span>
                                                            <span class="progress-description">
                                                                {{ $frayerData->produk->nama_produk ?? '' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                @if($frayer2Data)
                                                <div class="col-md-3 col-sm-6">
                                                    <div class="info-box bg-secondary">
                                                        <span class="info-box-icon"><i class="fas fa-fire"></i></span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">Frayer 2</span>
                                                            <span class="info-box-number">{{ $frayer2Data->suhuFrayer2->suhu_frayer_2 ?? '-' }}°C</span>
                                                            <span class="progress-description">
                                                                {{ $frayer2Data->produk->nama_produk ?? '' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <!-- Informasi Dasar Card -->
                                            <div class="card card-primary card-outline">
                                                <div class="card-header">
                                                    <h3 class="card-title">
                                                        <i class="fas fa-info-circle"></i> Informasi Dasar
                                                    </h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group row">
                                                        <label for="tanggal" class="col-sm-4 col-form-label">
                                                            <i class="fas fa-calendar"></i> Tanggal
                                                        </label>
                                                        <div class="col-sm-8">
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
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="jam" class="col-sm-4 col-form-label">
                                                            <i class="fas fa-clock"></i> Jam <span class="text-danger">*</span>
                                                        </label>
                                                        <div class="col-sm-8">
                                                            <input type="time" class="form-control @error('jam') is-invalid @enderror" name="jam" value="{{ old('jam', \Carbon\Carbon::now('Asia/Jakarta')->format('H:i')) }}" required>
                                                            @error('jam')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="id_produk" class="col-sm-4 col-form-label">
                                                            <i class="fas fa-box"></i> Produk
                                                        </label>
                                                        <div class="col-sm-8">
                                                            <select class="form-control" id="id_produk" name="id_produk" required>
                                                                <option value="">Pilih Produk</option>
                                                                @foreach ($products as $product)
                                                                    <option value="{{ $product->id }}" 
                                                                        @if(($frayerData && $frayerData->id_produk == $product->id) || 
                                                                            ($breaderData && $breaderData->id_produk == $product->id) ||
                                                                            ($batteringData && $batteringData->id_produk == $product->id) ||
                                                                            ($predustData && $predustData->id_produk == $product->id) ||
                                                                            ($penggorenganData && $penggorenganData->id_produk == $product->id))
                                                                        selected
                                                                        @endif
                                                                    >{{ $product->nama_produk }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <!-- Parameter Proses Card -->
                                            <div class="card card-success card-outline">
                                                <div class="card-header">
                                                    <h3 class="card-title">
                                                        <i class="fas fa-fire"></i> Parameter Proses
                                                    </h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group row">
                                                        <label for="std_suhu_pusat_display" class="col-sm-4 col-form-label">
                                                            <i class="fas fa-thermometer-half"></i> Std Suhu Pusat
                                                        </label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control" id="std_suhu_pusat_display" readonly placeholder="Loading...">
                                                            <input type="hidden" name="id_std_suhu_pusat" id="id_std_suhu_pusat">
                                                            <small class="text-muted">Standar suhu akan muncul otomatis berdasarkan Fryer yang digunakan</small>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="aktual_suhu_pusat" class="col-sm-4 col-form-label">
                                                            <i class="fas fa-chart-line"></i> Aktual Suhu Pusat
                                                        </label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control" id="aktual_suhu_pusat" name="aktual_suhu_pusat" placeholder="Masukkan aktual suhu pusat" required>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="sensori" class="col-sm-4 col-form-label">
                                                            <i class="fas fa-eye"></i> Sensori
                                                        </label>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="sensori_kematangan" class="col-sm-4 col-form-label">Kematangan</label>
                                                        <div class="col-sm-8">
                                                            <select class="form-control" id="sensori_kematangan" name="sensori_kematangan">
                                                                <option value="">Pilih</option>
                                                                <option value="✔">✔</option>
                                                                <option value="✘">✘</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="sensori_kenampakan" class="col-sm-4 col-form-label">Kenampakan</label>
                                                        <div class="col-sm-8">
                                                            <select class="form-control" id="sensori_kenampakan" name="sensori_kenampakan">
                                                                <option value="">Pilih</option>
                                                                <option value="✔">✔</option>
                                                                <option value="✘">✘</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="sensori_warna" class="col-sm-4 col-form-label">Warna</label>
                                                        <div class="col-sm-8">
                                                            <select class="form-control" id="sensori_warna" name="sensori_warna">
                                                                <option value="">Pilih</option>
                                                                <option value="✔">✔</option>
                                                                <option value="✘">✘</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="sensori_rasa" class="col-sm-4 col-form-label">Rasa</label>
                                                        <div class="col-sm-8">
                                                            <select class="form-control" id="sensori_rasa" name="sensori_rasa">
                                                                <option value="">Pilih</option>
                                                                <option value="✔">✔</option>
                                                                <option value="✘">✘</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="sensori_bau" class="col-sm-4 col-form-label">Bau</label>
                                                        <div class="col-sm-8">
                                                            <select class="form-control" id="sensori_bau" name="sensori_bau">
                                                                <option value="">Pilih</option>
                                                                <option value="✔">✔</option>
                                                                <option value="✘">✘</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="sensori_tekstur" class="col-sm-4 col-form-label">Tekstur</label>
                                                        <div class="col-sm-8">
                                                            <select class="form-control" id="sensori_tekstur" name="sensori_tekstur">
                                                                <option value="">Pilih</option>
                                                                <option value="✔">✔</option>
                                                                <option value="✘">✘</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Simpan Data
                                            </button>
                                            <a href="{{ route('hasil-penggorengan.index') }}" class="btn btn-secondary ml-2">
                                                <i class="fas fa-arrow-left"></i> Kembali
                                            </a>
                                        </div>
                                    </div>
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
// Pure JavaScript - Tidak pakai jQuery untuk menghindari conflict
(function() {
    'use strict';
    
    // Detect fryer dari URL parameter (lebih robust)
    const urlParams = new URLSearchParams(window.location.search);
    let fryerNumber = null;
    let produkId = null;

    // Prioritas: cek frayer2_uuid dulu (karena lebih spesifik)
    if (urlParams.has('frayer2_uuid') && urlParams.get('frayer2_uuid')) {
        fryerNumber = 2;
        @if(isset($frayer2Data) && $frayer2Data)
            produkId = {{ $frayer2Data->id_produk ?? 'null' }};
        @endif
        console.log('🔍 Detected from URL: Frayer 2, Produk ID:', produkId);
    } else if (urlParams.has('frayer_uuid') && urlParams.get('frayer_uuid')) {
        // Cek apakah ini Frayer 1, 3, 4, atau 5
        @if(isset($frayerData) && $frayerData)
            produkId = {{ $frayerData->id_produk ?? 'null' }};
            
            // Deteksi fryer number dari model class
            const modelClass = '{{ $frayerData ? get_class($frayerData) : '' }}';
            
            if (modelClass.includes('Frayer5')) {
                fryerNumber = 5;
            } else if (modelClass.includes('Frayer4')) {
                fryerNumber = 4;
            } else if (modelClass.includes('Frayer3')) {
                fryerNumber = 3;
            } else if (modelClass.includes('Frayer2')) {
                fryerNumber = 2;
            } else {
                fryerNumber = 1; // Default ProsesFrayer (Frayer 1)
            }
        @endif
        console.log('🔍 Detected from URL: Frayer', fryerNumber, ', Produk ID:', produkId, ', Model:', '{{ $frayerData ? get_class($frayerData) : '' }}');
    }

    console.log('✅ Final Detection - Fryer Number:', fryerNumber, ', Produk ID:', produkId);
        
    // Tunggu DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    function init() {
        try {
            // Load std suhu pusat saat halaman load
            if (fryerNumber && produkId) {
                setTimeout(function() {
                    loadStdSuhuPusat(produkId, fryerNumber);
                }, 1000); // Delay 1 detik untuk memastikan semua ready
            } else {
                setFieldValue('std_suhu_pusat_display', 'Fryer atau Produk tidak terdeteksi');
                console.warn('⚠️ Fryer atau Produk tidak terdeteksi');
            }
            
            // Jika user ganti produk (jika ada dropdown produk)
            const produkSelect = document.getElementById('id_produk');
            if (produkSelect) {
                produkSelect.addEventListener('change', function() {
                    produkId = this.value;
                    if (fryerNumber && produkId) {
                        loadStdSuhuPusat(produkId, fryerNumber);
                    }
                });
            }
        } catch (e) {
            console.error('❌ Error in init:', e);
            setFieldValue('std_suhu_pusat_display', 'Script error');
        }
    }
    
    // Helper function untuk set field value
    function setFieldValue(fieldId, value) {
        const field = document.getElementById(fieldId);
        if (field) {
            field.value = value;
        }
    }
    
    // Function untuk load std suhu pusat via AJAX
    function loadStdSuhuPusat(produkId, fryerNumber) {
        console.log('📡 Loading Std Suhu Pusat for Produk:', produkId, 'Fryer:', fryerNumber);
        
        setFieldValue('std_suhu_pusat_display', 'Loading...');
        const url = "{{ route('get-std-suhu-pusat-by-produk', ['id_produk' => ':id_produk']) }}".replace(':id_produk', produkId);

        // Gunakan Fetch API (modern JavaScript)
        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('✅ Response:', data);
            
            if (data && data.length > 0) {
                // Ambil data pertama (asumsi 1 produk = 1 std suhu pusat)
                const stdSuhu = data[0];
                const suhuArray = stdSuhu.std_suhu_pusat;
                
                console.log('📊 Suhu Array:', suhuArray);
                
                // Cek apakah data tersedia untuk fryer ini
                if (suhuArray && Array.isArray(suhuArray) && suhuArray.length >= fryerNumber) {
                    const suhuValue = suhuArray[fryerNumber - 1]; // Index 0 = Fryer 1
                    
                    if (suhuValue) {
                        setFieldValue('std_suhu_pusat_display', suhuValue + '°C');
                        setFieldValue('id_std_suhu_pusat', stdSuhu.id);
                        console.log('✅ Std Suhu Pusat loaded: Fryer ' + fryerNumber + ' = ' + suhuValue + '°C');
                    } else {
                        setFieldValue('std_suhu_pusat_display', 'Data kosong untuk Fryer ' + fryerNumber);
                        setFieldValue('id_std_suhu_pusat', '');
                    }
                } else {
                    setFieldValue('std_suhu_pusat_display', 'Data tidak tersedia untuk Fryer ' + fryerNumber);
                    setFieldValue('id_std_suhu_pusat', '');
                    
                    showAlert('warning', 'Perhatian', 'Standar suhu untuk Fryer ' + fryerNumber + ' belum diatur untuk produk ini!');
                }
            } else {
                setFieldValue('std_suhu_pusat_display', 'Data tidak ditemukan');
                setFieldValue('id_std_suhu_pusat', '');
                
                showAlert('error', 'Error', 'Standar suhu pusat belum diatur untuk produk ini!');
            }
        })
        .catch(error => {
            setFieldValue('std_suhu_pusat_display', 'Error loading data');
            setFieldValue('id_std_suhu_pusat', '');
            
            console.error('❌ Error loading std suhu pusat:', error);
            
            showAlert('error', 'Error', 'Gagal memuat data standar suhu pusat! ' + error.message);
        });
    }
    
    // Helper function untuk show alert (gunakan SweetAlert jika tersedia, atau alert biasa)
    function showAlert(icon, title, text) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: icon,
                title: title,
                text: text,
                confirmButtonText: 'OK'
            });
        } else {
            alert(title + '\n\n' + text);
        }
    }
})();
</script>
@endsection
