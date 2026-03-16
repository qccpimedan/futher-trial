@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Tambah Data Shoestring</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('shoestring.index') }}">Shoestring</a></li>
                        <li class="breadcrumb-item active">Tambah Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Form Input Data Shoestring</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('shoestring.store') }}" method="POST" id="main-shoestring-form" enctype="multipart/form-data">                                
                                @csrf
                                <!-- Informasi Umum (Shared Fields) -->
                                <div class="card card-info card-outline mb-3">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-info-circle"></i> Informasi Umum</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i> <strong>Informasi:</strong> Tanggal dan Shift akan digunakan untuk semua data yang diinput.
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="tanggal">Tanggal Produksi <span class="text-danger">*</span></label>
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
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="jam">Jam <span class="text-danger">*</span></label>
                                                    <input type="time" class="form-control @error('jam') is-invalid @enderror" id="jam" name="jam" value="{{ old('jam', \Carbon\Carbon::now('Asia/Jakarta')->format('H:i')) }}" required>
                                                    @error('jam')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="shift_id">Shift <span class="text-danger">*</span></label>
                                                    <select class="form-control select2" name="shift_id" id="shift_id" required>
                                                        <option value="">Pilih Shift</option>
                                                        @foreach($shifts as $shift)
                                                            <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>{{ $shift->shift }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Summary Cards -->
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <div class="info-box bg-info">
                                            <span class="info-box-icon"><i class="fas fa-list"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Total Entries</span>
                                                <span class="info-box-number" id="total-entries">1</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-box bg-success">
                                            <span class="info-box-icon"><i class="fas fa-check"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Valid Entries</span>
                                                <span class="info-box-number" id="valid-entries">0</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-box bg-warning">
                                            <span class="info-box-icon"><i class="fas fa-exclamation"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Incomplete</span>
                                                <span class="info-box-number" id="incomplete-entries">1</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dynamic Entries Container -->
                                <div id="entries-container">
                                    <div class="entry-card" data-entry="1">
                                        <div class="card card-primary card-outline">
                                            <div class="card-header">
                                                <h3 class="card-title"><i class="fas fa-box"></i> Entry #1</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Nama Produsen <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control entry-field" name="entries[0][nama_produsen]" 
                                                                   placeholder="Masukkan nama produsen" value="{{ old('entries.0.nama_produsen') }}" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Kode Produksi <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control entry-field" name="entries[0][kode_produksi]" 
                                                                   placeholder="Masukkan kode produksi" value="{{ old('entries.0.kode_produksi') }}" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Tanggal Expired <span class="text-danger">*</span></label>
                                                            <input type="date" class="form-control entry-field" name="entries[0][best_before]" value="{{ old('entries.0.best_before') }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Pemeriksaan Defect <span class="text-danger">* dapat memilih lebih dari satu</span></label>
                                                            <select name="entries[0][sampling_defect][]" id="sampling_defect_0" multiple="multiple" class="form-control entry-field select-defect">
                                                                @foreach($dataDefect as $defect)
                                                                    <option value="{{ $defect->id }}" data-jenis="{{ $defect->jenis_defect }}" data-spec="{{ $defect->spec_defect }}">
                                                                        {{ $defect->jenis_defect }}{{ $defect->spec_defect ? ' - ' . $defect->spec_defect : '' }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="form-group" id="defect-qty-container-0">
                                                        </div>

                                                        <div class="form-group">
                                                            <label>Total Defect</label>
                                                            <input type="text" class="form-control entry-field" name="entries[0][total_defect]" id="total_defect_0" value="0" readonly>
                                                        </div>

                                                        <div class="form-group">
                                                            <label>Keterangan</label>
                                                            <textarea class="form-control entry-field" name="entries[0][catatan]" 
                                                                rows="4" placeholder="Masukkan keterangan tambahan"></textarea>
                                                        </div>

                                                        <div class="form-group">
                                                            <label>Dokumentasi (Foto) <span class="text-danger">* dapat memilih lebih dari satu max 1mb/file (auto compress)</span></label>
                                                            <input type="file" class="form-control-file dokumentasi-input" accept="image/*" capture="camera" multiple>
                                                            <div class="dokumentasi-preview mt-2" style="display: flex; flex-wrap: wrap; gap: 10px;"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="">
                                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                                <i class="fas fa-save"></i> Simpan <span id="submit-count">1</span> Data
                                            </button>
                                            <button type="button" class="btn btn-success ml-2" id="add-entry-btn">
                                                <i class="fas fa-plus"></i> Tambah Entry
                                            </button>
                                            <a href="{{ route('shoestring.index') }}" class="btn btn-secondary ml-2">
                                                <i class="fas fa-arrow-left"></i> Kembali
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
@push('styles')
<style>
    .select2-container .select2-selection--single {
        height: 38px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    let entryCounter = 0;
    
    // Function to update summary
    function updateSummary() {
        const totalEntries = $('.entry-card').length;
        let validEntries = 0;
        let incompleteEntries = 0;
        
        $('.entry-card').each(function() {
            const requiredFields = $(this).find('.entry-field[required]');
            let isComplete = true;
            
            requiredFields.each(function() {
                const $field = $(this);
                let fieldValue = $field.val();
                
                // Handle multi-select (array)
                if (Array.isArray(fieldValue)) {
                    if (fieldValue.length === 0 || (fieldValue.length === 1 && fieldValue[0] === '')) {
                        isComplete = false;
                        return false;
                    }
                } 
                // Handle regular input
                else {
                    if (!fieldValue || fieldValue.trim() === '') {
                        isComplete = false;
                        return false;
                    }
                }
            });
            
            if (isComplete) {
                validEntries++;
            } else {
                incompleteEntries++;
            }
        });
        
        $('#total-entries').text(totalEntries);
        $('#valid-entries').text(validEntries);
        $('#incomplete-entries').text(incompleteEntries);
        $('#submit-count').text(totalEntries);
    }
    
    // Function to reindex entries
    function reindexEntries() {
        $('.entry-card').each(function(index) {
            const entryNum = index;
            $(this).attr('data-entry', index + 1);
            $(this).find('.card-title').html(`<i class="fas fa-box"></i> Entry #${index + 1}`);
            
            // Update name attributes
            $(this).find('input, select, textarea').each(function() {
                const name = $(this).attr('name');
                if (name && name.startsWith('entries[')) {
                    // Extract field name after 'entries[X]'
                    const match = name.match(/^entries\[\d+\](.+)$/);
                    if (match) {
                        const fieldPart = match[1]; // Includes [field_name] or [field_name][]
                        $(this).attr('name', `entries[${entryNum}]${fieldPart}`);
                    }
                }
            });
            
            // Show/hide remove button
            const removeBtn = $(this).find('.remove-entry-btn');
            if ($('.entry-card').length > 1) {
                removeBtn.show();
            } else {
                removeBtn.hide();
            }
        });
    }
    
    // Add new entry
    $('#add-entry-btn').click(function() {
        entryCounter++;
        const newEntryNum = $('.entry-card').length;
        
        const newEntryHtml = `
        <div class="entry-card" data-entry="${newEntryNum + 1}">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-box"></i> Entry #${newEntryNum + 1}</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-danger btn-sm remove-entry-btn">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama Produsen <span class="text-danger">*</span></label>
                                <input type="text" class="form-control entry-field" name="entries[${newEntryNum}][nama_produsen]" 
                                       placeholder="Masukkan nama produsen" required>
                            </div>
                            <div class="form-group">
                                <label>Kode Produksi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control entry-field" name="entries[${newEntryNum}][kode_produksi]" 
                                       placeholder="Masukkan kode produksi" required>
                            </div>
                            <div class="form-group">
                                <label>Tanggal Expired <span class="text-danger">*</span></label>
                                <input type="date" class="form-control entry-field" name="entries[${newEntryNum}][best_before]" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Pemeriksaan Defect <span class="text-danger">* dapat memilih lebih dari satu</span></label>
                                <select name="entries[${newEntryNum}][sampling_defect][]" multiple="multiple" id="sampling_defect_${newEntryNum}" class="form-control entry-field select-defect">
                                    @foreach($dataDefect as $defect)
                                        <option value="{{ $defect->id }}" data-jenis="{{ $defect->jenis_defect }}" data-spec="{{ $defect->spec_defect }}">
                                            {{ $defect->jenis_defect }}{{ $defect->spec_defect ? ' - ' . $defect->spec_defect : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group" id="defect-qty-container-${newEntryNum}">
                            </div>

                            <div class="form-group">
                                <label>Total Defect</label>
                                <input type="text" class="form-control entry-field" name="entries[${newEntryNum}][total_defect]" id="total_defect_${newEntryNum}" value="0" readonly>
                            </div>

                            <div class="form-group">
                                <label>Keterangan</label>
                                <textarea class="form-control entry-field" name="entries[${newEntryNum}][catatan]" 
                                    rows="4" placeholder="Masukkan keterangan tambahan"></textarea>
                            </div>

                            <div class="form-group">
                                <label>Dokumentasi (Foto) <span class="text-danger">* dapat memilih lebih dari satu max 1mb/file (auto compress)</span></label>
                                <input type="file" class="form-control-file dokumentasi-input" accept="image/*" capture="camera" multiple>
                                <div class="dokumentasi-preview mt-2" style="display: flex; flex-wrap: wrap; gap: 10px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        `;

        $('#entries-container').append(newEntryHtml);
        
        // Initialize Select2 untuk dropdown yang baru ditambahkan
        $(`#sampling_defect_${newEntryNum}`).select2({
            placeholder: "Pilih Jenis Defect",
            allowClear: true,
            width: '100%',
            multiple: true,
            closeOnSelect: false
        });
        
        reindexEntries();
        updateSummary();
        
        // Show notification
        if (typeof toastr !== 'undefined') {
            toastr.success('Entry baru berhasil ditambahkan');
        }
        
        // Scroll to new entry
        const newEntry = $('.entry-card').last();
        $('html, body').animate({
            scrollTop: newEntry.offset().top - 100
        }, 500);
    });
    
    // Remove entry
    $(document).on('click', '.remove-entry-btn', function() {
        if ($('.entry-card').length > 1) {
            if (confirm('Apakah Anda yakin ingin menghapus entry ini?')) {
                $(this).closest('.entry-card').remove();
                reindexEntries();
                updateSummary();
                
                if (typeof toastr !== 'undefined') {
                    toastr.info('Entry berhasil dihapus');
                }
            }
        } else {
            if (typeof toastr !== 'undefined') {
                toastr.warning('Minimal harus ada 1 entry');
            }
        }
    });
    
    // Update summary on field change
    $(document).on('input change', '.entry-field', function() {
        updateSummary();
    });

    function renderDefectQtyInputs(entryIndex, $select) {
        const containerId = `#defect-qty-container-${entryIndex}`;
        const $container = $(containerId);
        if ($container.length === 0) return;

        const selected = $select.val() || [];

        if (!selected.length) {
            $container.html('');
            return;
        }

        let html = '<label>Jumlah per Defect</label>';
        selected.forEach(defectId => {
            const opt = $select.find(`option[value="${defectId}"]`);
            const jenis = opt.data('jenis') || opt.text();
            const spec = opt.data('spec') || '';
            const label = spec ? `${jenis} - ${spec}` : `${jenis}`;
            html += `
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text" style="min-width: 220px;">${label}</span>
                    </div>
                    <input type="text" class="form-control entry-field" name="entries[${entryIndex}][sampling_defect_qty][${defectId}]" placeholder="Jumlah">
                </div>
            `;
        });
        $container.html(html);

        updateTotalDefect(entryIndex);
    }

    function updateTotalDefect(entryIndex) {
        const $entryCard = $('.entry-card').eq(entryIndex);
        const $totalField = $entryCard.find(`#total_defect_${entryIndex}`);
        if ($totalField.length === 0) return;

        let total = 0;
        $entryCard.find(`input[name^="entries[${entryIndex}][sampling_defect_qty]"]`).each(function() {
            const v = $(this).val();
            if (v !== null && v !== '' && !isNaN(v)) {
                total += parseFloat(v);
            }
        });
        $totalField.val(total);
    }

    $(document).on('change', '.select-defect', function() {
        const $entryCard = $(this).closest('.entry-card');
        const entryIndex = $entryCard.index();
        renderDefectQtyInputs(entryIndex, $(this));
    });

    $(document).on('input', 'input[name*="[sampling_defect_qty]"]', function() {
        const $entryCard = $(this).closest('.entry-card');
        const entryIndex = $entryCard.index();
        updateTotalDefect(entryIndex);
    });

    // Form submit - simple validation only
    $('#main-shoestring-form').on('submit', function(e) {
        e.preventDefault();
        
        console.log('=== FORM SUBMIT STARTED ===');
        
        // Debug: Cek semua input fields
        console.log('Checking all inputs:');
        $('input[name^="entries"]').each(function() {
            const name = $(this).attr('name');
            const value = $(this).val();
            const type = $(this).attr('type');
            console.log('Input:', name, '| Type:', type, '| Value:', value, '| Length:', value ? value.length : 0);
        });
        
        console.log('\nChecking textarea:');
        $('textarea[name^="entries"]').each(function() {
            console.log('Textarea:', $(this).attr('name'), '=', $(this).val());
        });
        
        console.log('\nChecking select:');
        $('select[name^="entries"]').each(function() {
            console.log('Select:', $(this).attr('name'), '=', $(this).val());
        });
        
        console.log('\nForm Data (serialized):');
        const formData = $(this).serializeArray();
        formData.forEach(item => {
            console.log(item.name, '=', item.value);
        });
        
        // Cek apakah shift sudah dipilih
        const shift = $('#shift_id').val();
        if (!shift || shift === '') {
            if (typeof toastr !== 'undefined') {
                toastr.error('Shift harus dipilih');
            } else {
                alert('Shift harus dipilih');
            }
            $('#shift_id').addClass('is-invalid');
            return false;
        }
        
        // Validasi minimal entries
        const namaProdusen = $('input[name="entries[0][nama_produsen]"]').val();
        const kodeProduksi = $('input[name="entries[0][kode_produksi]"]').val();
        const bestBefore = $('input[name="entries[0][best_before]"]').val();
        
        console.log('\nValidation check:');
        console.log('Nama Produsen:', namaProdusen);
        console.log('Kode Produksi:', kodeProduksi);
        console.log('Best Before:', bestBefore);
        
        if (!namaProdusen || !kodeProduksi || !bestBefore) {
            alert('Mohon lengkapi semua field yang required (Nama Produsen, Kode Produksi, Tanggal Expired)');
            return false;
        }
        
        // Jika semua validasi pass, submit form
        console.log('=== ALL VALIDATION PASSED, SUBMITTING FORM ===');
        this.submit();
    });

    // Select2 untuk defect sudah diinisialisasi secara global di app.blade.php
    // Tidak perlu inisialisasi ulang di sini

    // Logic untuk kompresi dokumentasi (image upload)
    $(document).on('change', '.dokumentasi-input', function(e) {
        const $container = $(this).closest('.form-group');
        const $previewContainer = $container.find('.dokumentasi-preview');
        // Ambil index dengan tepat, jika closest is not correct, use data-entry
        const entryNumAttr = $(this).closest('.entry-card').attr('data-entry');
        const entryIndex = parseInt(entryNumAttr) - 1; // data-entry is 1-based, index is 0-based
        
        $previewContainer.empty();
        $container.find('input[type="hidden"].dokumentasi-base64').remove();
        
        const files = e.target.files;
        if (!files || files.length === 0) return;
        
        Array.from(files).forEach((file, fileIndex) => {
            if (!file.type.match('image.*')) return;
            
            const reader = new FileReader();
            reader.onload = function(readerEvent) {
                const img = new Image();
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    let width = img.width;
                    let height = img.height;
                    const MAX_WIDTH = 1200;
                    const MAX_HEIGHT = 1200;
                    
                    if (width > height) {
                        if (width > MAX_WIDTH) { height *= MAX_WIDTH / width; width = MAX_WIDTH; }
                    } else {
                        if (height > MAX_HEIGHT) { width *= MAX_HEIGHT / height; height = MAX_HEIGHT; }
                    }
                    
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);
                    
                    // Kompresi (0.7 quality -> JPEG)
                    const dataUrl = canvas.toDataURL('image/jpeg', 0.7);
                    
                    // Buat Preview
                    const imgElement = $(`<img src="${dataUrl}" class="img-thumbnail" style="height: 100px; width: 100px; object-fit: cover;">`);
                    $previewContainer.append(imgElement);
                    
                    // Buat Base64 hidden input untuk disubmit ke form controller
                    const hiddenInput = $(`<input type="hidden" name="entries[${entryIndex}][dokumentasi_base64][]" class="dokumentasi-base64">`);
                    hiddenInput.val(dataUrl);
                    $container.append(hiddenInput);
                };
                img.src = readerEvent.target.result;
            };
            reader.readAsDataURL(file);
        });
    });

// Initialize
reindexEntries();
updateSummary();
});
</script>
@endpush

@endsection