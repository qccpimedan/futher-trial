@extends('layouts.app')

@section('container')
<style>
/* Hilangkan semua checkbox styling dari form control */
input[type="text"].form-control,
input[type="number"].form-control,
select.form-control {
    background-image: none !important;
    padding-left: 12px !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    appearance: none !important;
}

/* Align table cells */
#bahanNonFormingTable td,
#bahanNonFormingTable th {
    vertical-align: middle;
}

/* Input di dalam tabel */
#bahanNonFormingTable .form-control-sm {
    height: calc(1.5em + 0.5rem + 2px);
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
</style>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h1 class="m-0">Tambah Data Bahan Baku Tumbling</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('bahan-baku-tumbling.index') }}">Bahan Baku Tumbling</a></li>
                            <li class="breadcrumb-item active">Tambah Data</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-lg-12 col-md-10">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Form Pemeriksaan Bahan Baku Tumbling</h3>
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <h5 class="alert-heading">
                                        <i class="fas fa-exclamation-circle"></i> Validasi Error
                                    </h5>
                                    <hr>
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li class="mb-2">
                                                <strong>{{ $error }}</strong>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    <h5 class="alert-heading">
                                        <i class="fas fa-info-circle"></i> Peringatan
                                    </h5>
                                    <p class="mb-0">{{ session('error') }}</p>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <h5 class="alert-heading">
                                        <i class="fas fa-check-circle"></i> Sukses
                                    </h5>
                                    <p class="mb-0">{{ session('success') }}</p>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('bahan-baku-tumbling.store') }}" class="form-horizontal">
                                @csrf
                                <div class="card-body">
                                    {{-- Stepper Indikator Proses --}}
                                    @include('components.stepper-tumbling', ['step' => 1])

                                    {{-- Informasi Dasar --}}
                                    <div class="card card-outline card-primary mb-3">
                                        <div class="card-header py-2">
                                            <h6 class="mb-0">Informasi Dasar</h6>
                                        </div>
                                        <div class="card-body pt-3 pb-1">
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                                        </div>
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
                                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="jam">Jam <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="far fa-clock"></i></span>
                                                        </div>
                                                        <input type="time" name="jam" id="jam"
                                                            class="form-control @error('jam') is-invalid @enderror"
                                                            value="{{ old('jam', \Carbon\Carbon::now('Asia/Jakarta')->format('H:i')) }}">
                                                        @error('jam')
                                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="shift_id">Shift <span class="text-danger">*</span></label>
                                                    <select class="form-control select2 @error('shift_id') is-invalid @enderror"
                                                        id="shift_id" name="shift_id" required>
                                                        <option value="">Pilih Shift</option>
                                                        @foreach($shifts as $shift)
                                                            <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>
                                                                {{ $shift->shift }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('shift_id')
                                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Detail Bahan Baku --}}
                                    <div class="card card-outline card-primary">
                                        <div class="card-header py-2">
                                            <h6 class="mb-0">Detail Bahan Baku</h6>
                                        </div>
                                        <div class="card-body pt-3">
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="id_produk">Nama Produk <span class="text-danger">*</span></label>
                                                    <select name="id_produk" id="id_produk" class="form-control @error('id_produk') is-invalid @enderror" required>
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
                                                <div class="form-group col-md-6">
                                                    <label for="kode_produksi">Kode Produksi <span class="text-danger">*</span></label>
                                                    <input type="text" name="kode_produksi" id="kode_produksi"
                                                        class="form-control @error('kode_produksi') is-invalid @enderror"
                                                        value="{{ old('kode_produksi') }}" placeholder="Masukkan kode produksi" required>
                                                    @error('kode_produksi')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- Nomor Formula Non Forming --}}
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="nomor_formula_non_forming">Nomor Formula Non Forming <span class="text-danger">*</span></label>
                                                    <select name="nomor_formula_non_forming" id="nomor_formula_non_forming" class="form-control" required>
                                                        <option value="">Pilih Formula</option>
                                                    </select>
                                                </div>
                                            </div>

                                            {{-- Tabel Bahan Non Forming --}}
                                            <div class="form-row mb-3" id="bahanNonFormingContainer" style="display: none;">
                                                <div class="col-12">
                                                    <h6 class="mb-2">Data Bahan Non Forming</h6>
                                                    <p class="text-muted small">Isi data kode produksi, suhu, dan kondisi daging untuk setiap bahan</p>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered" id="bahanNonFormingTable">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th width="5%">No</th>
                                                                    <th width="18%">Nama RM</th>
                                                                    <th width="20%">Berat RM (kg)</th>
                                                                    <th width="20%">Kode Produksi Bahan Baku</th>
                                                                    <th width="12%">Suhu (°C)</th>
                                                                    <th width="12%">Kondisi Daging</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="bahanNonFormingBody">
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Hidden inputs for database mode --}}
                                            <div id="databaseInputSection" style="display: none;">
                                                <input type="hidden" name="id_bahan_nonforming" id="id_bahan_nonforming">
                                                <div id="database_bahan_container"></div>
                                            </div>

                                            {{-- Form Salinity dan Hasil Pencampuran --}}
                                            <div class="form-row">
                                                <div class="form-group col-md-4">
                                                    <label for="salinity">Salinity <span class="text-danger">*</span></label>
                                                    <input type="text" name="salinity" id="salinity"
                                                        class="form-control @error('salinity') is-invalid @enderror"
                                                        value="{{ old('salinity') }}" placeholder="Masukkan salinity" required>
                                                    @error('salinity')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="hasil_pencampuran">Hasil Pencampuran <span class="text-danger">*</span></label>
                                                    <select class="form-control @error('hasil_pencampuran') is-invalid @enderror" name="hasil_pencampuran" id="hasil_pencampuran" required>
                                                        <option value="">Pilih Hasil Pencampuran</option>
                                                        <option value="✓" {{ old('hasil_pencampuran') == '✓' ? 'selected' : '' }}>✓</option>
                                                        <option value="✘" {{ old('hasil_pencampuran') == '✘' ? 'selected' : '' }}>✘</option>
                                                    </select>
                                                    @error('hasil_pencampuran')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Simpan Data
                                    </button>
                                    <a href="{{ route('bahan-baku-tumbling.index') }}" class="btn btn-secondary ml-2">
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
    const idProdukSelect = document.getElementById('id_produk');
    const nomorFormulaSelect = document.getElementById('nomor_formula_non_forming');
    const bahanNonFormingContainer = document.getElementById('bahanNonFormingContainer');
    const bahanNonFormingBody = document.getElementById('bahanNonFormingBody');
    const databaseInputSection = document.getElementById('databaseInputSection');
    const idBahanNonFormingInput = document.getElementById('id_bahan_nonforming');

    console.log('Script loaded successfully');

    // Load Nomor Formula ketika Produk berubah
    if (idProdukSelect) {
        $(idProdukSelect).on('select2:select', function() {
            const produkId = $(this).val();
            console.log('Produk changed:', produkId);
            nomorFormulaSelect.innerHTML = '<option value="">Pilih Formula</option>';
            bahanNonFormingContainer.style.display = 'none';
            bahanNonFormingBody.innerHTML = '';
            databaseInputSection.style.display = 'none';

            if (produkId) {
                const baseUrl = window.location.pathname.includes('/paperless_futher/') ? '/paperless_futher' : '';
                const url = `${baseUrl}/qc-sistem/api/nomor-formula-non-forming/${produkId}`;
                fetch(url)
                    .then(response => response.ok ? response.json() : Promise.reject(`HTTP ${response.status}`))
                    .then(data => {
                        data.forEach(formula => {
                            const option = document.createElement('option');
                            option.value = formula.id;
                            option.textContent = formula.nomor_formula || formula.id;
                            nomorFormulaSelect.appendChild(option);
                        });
                    })
                    .catch(err => console.error('Error loading formulas:', err));
            }
        });
    }

    // Load Bahan Non Forming ketika Formula berubah
    if (nomorFormulaSelect) {
        nomorFormulaSelect.addEventListener('change', function() {
            const formulaId = this.value;
            console.log('Formula changed:', formulaId);
            bahanNonFormingBody.innerHTML = '';
            
            // Set id_bahan_nonforming hidden input
            if (idBahanNonFormingInput) {
                idBahanNonFormingInput.value = formulaId;
                console.log('id_bahan_nonforming set to:', formulaId);
            }

            if (formulaId) {
                const baseUrl = window.location.pathname.includes('/paperless_futher/') ? '/paperless_futher' : '';
                const url = `${baseUrl}/qc-sistem/api/bahan-non-forming/${formulaId}`;
                fetch(url)
                    .then(response => response.ok ? response.json() : Promise.reject(`HTTP ${response.status}`))
                    .then(data => {
                        console.log('Bahan loaded:', data);
                        if (data.length > 0) {
                            bahanNonFormingContainer.style.display = 'block';
                            databaseInputSection.style.display = 'block';
                            
                            data.forEach((bahan, index) => {
                                const row = document.createElement('tr');
                                row.dataset.id = bahan.id;
                                row.dataset.index = index;
                                row.dataset.nama = bahan.nama_rm;
                                row.dataset.berat = bahan.berat_rm;
                                const beratUniqueId = `berat_${index}`;
                                
                                row.innerHTML = `
                                    <td class="text-center align-middle">${index + 1}</td>
                                    <td class="align-middle">${bahan.nama_rm}</td>
                                    <td class="align-middle">
                                        <div class="input-group input-group-sm">
                                            <select class="form-control berat-source-select" data-index="${index}" data-db-value="${bahan.berat_rm}" aria-label="Sumber berat">
                                                <option value="db">Master (${bahan.berat_rm})</option>
                                                <option value="manual">Manual Input</option>
                                            </select>
                                            <input type="text" id="${beratUniqueId}" class="form-control berat-input" data-index="${index}" value="${bahan.berat_rm}" placeholder="Berat" disabled>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <input type="text" 
                                            class="form-control form-control-sm kode-produksi-input" 
                                            data-index="${index}"
                                            placeholder="Masukkan kode produksi"
                                            >
                                    </td>
                                    <td class="align-middle">
                                        <input type="text" 
                                            class="form-control form-control-sm suhu-input" 
                                            data-index="${index}"
                                            placeholder="Suhu"
                                            >
                                    </td>
                                    <td class="align-middle">
                                        <select class="form-control form-control-sm kondisi-input" 
                                            data-index="${index}"
                                            required>
                                            <option value="✓">✓</option>
                                            <option value="✘">✘</option>
                                        </select>
                                    </td>
                                `;
                                bahanNonFormingBody.appendChild(row);
                            });

                            // Add event listeners to inputs untuk auto-update saat user mengetik
                            document.querySelectorAll('.kode-produksi-input, .suhu-input, .kondisi-input, .berat-input, .berat-source-select').forEach(input => {
                                input.addEventListener('input', function() {
                                    console.log('Input changed, will update on submit');
                                });
                            });

                            // Toggle berat source (db/manual)
                            document.querySelectorAll('.berat-source-select').forEach(select => {
                                select.addEventListener('change', function() {
                                    const idx = this.dataset.index;
                                    const input = document.querySelector(`.berat-input[data-index="${idx}"]`);
                                    if (!input) return;

                                    if (this.value === 'manual') {
                                        input.value = '';
                                        input.disabled = false;
                                        input.setAttribute('required', 'required');
                                        input.focus();
                                    } else {
                                        input.value = this.dataset.dbValue || '';
                                        input.disabled = true;
                                        input.removeAttribute('required');
                                    }
                                });
                            });

                        } else {
                            bahanNonFormingContainer.style.display = 'none';
                            databaseInputSection.style.display = 'none';
                        }
                    })
                    .catch(err => console.error('Error loading bahan:', err));
            } else {
                bahanNonFormingContainer.style.display = 'none';
                databaseInputSection.style.display = 'none';
            }
        });
    }
    
    // Handle form submission
    document.addEventListener('submit', function(e) {
        if (e.target.tagName === 'FORM') {
            e.preventDefault();
            console.log('=== FORM SUBMIT EVENT ===');
            
            const form = e.target;
            const hasDatabaseBahan = bahanNonFormingBody && bahanNonFormingBody.children.length > 0;
            
            console.log('hasDatabaseBahan:', hasDatabaseBahan);
            
            // JIKA ADA DATABASE BAHAN, BUAT HIDDEN INPUTS
            if (hasDatabaseBahan) {
                console.log('Creating hidden inputs for database bahan...');
                
                const rows = document.querySelectorAll('#bahanNonFormingBody tr');
                const container = document.getElementById('database_bahan_container');
                
                if (container) {
                    container.innerHTML = ''; // Clear previous
                    
                    rows.forEach((row, index) => {
                        const rowIndex = row.dataset.index;
                        const kodeInput = document.querySelector(`.kode-produksi-input[data-index="${rowIndex}"]`);
                        const beratInput = document.querySelector(`.berat-input[data-index="${rowIndex}"]`);
                        const suhuInput = document.querySelector(`.suhu-input[data-index="${rowIndex}"]`);
                        const kondisiInput = document.querySelector(`.kondisi-input[data-index="${rowIndex}"]`);
                        
                        const html = `
                            <input type="hidden" name="database_bahan[${index}][nama_bahan_baku]" value="${row.dataset.nama || ''}">
                            <input type="hidden" name="database_bahan[${index}][jumlah]" value="${beratInput ? beratInput.value : (row.dataset.berat || '')}">
                            <input type="hidden" name="database_bahan[${index}][kode_produksi_bahan_baku]" value="${kodeInput ? kodeInput.value : ''}">
                            <input type="hidden" name="database_bahan[${index}][suhu]" value="${suhuInput ? suhuInput.value : ''}">
                            <input type="hidden" name="database_bahan[${index}][kondisi_daging]" value="${kondisiInput ? kondisiInput.value : '✓'}">
                        `;
                        container.innerHTML += html;
                    });
                    
                    console.log('Hidden inputs created:', container.innerHTML);
                    console.log('Submitting form manually...');
                    form.submit(); // SUBMIT MANUAL SETELAH HIDDEN INPUTS DIBUAT
                    return;
                }
            } else {
                // JIKA TIDAK ADA DATA, SHOW ERROR
                console.log('No data found - preventing submit');
                alert('Pilih formula untuk menampilkan bahan baku');
            }
        }
    }, true); // true = capture phase untuk memastikan event ter-trigger
});
</script>

@endsection