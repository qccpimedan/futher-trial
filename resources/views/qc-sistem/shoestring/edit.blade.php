@extends('layouts.app')

@php
    $isLocalhost = in_array(request()->getHost(), ['localhost', '127.0.0.1', '10.68.1.37']);
    $assetPath = $isLocalhost ? 'public/' : '';
@endphp

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Data Shoestring</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('shoestring.index') }}">Shoestring</a></li>
                        <li class="breadcrumb-item active">Edit Data</li>
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
                            <h3 class="card-title">Form Edit Data Shoestring</h3>
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

                            <form action="{{ route('shoestring.update', $shoestring->uuid) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="card card-outline card-info">
                                    <div class="card-header">
                                        <h3 class="card-title">Data Utama</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Nama Produsen <span class="text-danger">*</span></label>
                                            <div class="col-sm-9">
                                                <input type="text" name="nama_produsen" class="form-control" 
                                                       value="{{ old('nama_produsen', $shoestring->nama_produsen) }}" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Kode Produksi <span class="text-danger">*</span></label>
                                            <div class="col-sm-9">
                                                <input type="text" name="kode_produksi" class="form-control" 
                                                       value="{{ old('kode_produksi', $shoestring->kode_produksi) }}" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Shift <span class="text-danger">*</span></label>
                                            <div class="col-sm-9">
                                                <select class="form-control select2" name="shift_id" required>
                                                    <option value="">Pilih Shift</option>
                                                    @foreach($shifts as $shift)
                                                        <option value="{{ $shift->id }}" {{ old('shift_id', $shoestring->shift_id) == $shift->id ? 'selected' : '' }}>
                                                            {{ $shift->shift }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Tanggal <span class="text-danger">*</span></label>
                                            <div class="col-sm-9">
                                                <input type="text" name="tanggal" class="form-control" 
                                                       value="{{ old('tanggal', $shoestring->tanggal ? \Carbon\Carbon::parse($shoestring->tanggal)->format('d-m-Y H:i:s') : '') }}" readonly required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Jam <span class="text-danger">*</span></label>
                                            <div class="col-sm-9">
                                                <input type="time" name="jam" class="form-control @error('jam') is-invalid @enderror" 
                                                       value="{{ old('jam', $shoestring->jam ? \Carbon\Carbon::parse($shoestring->jam)->format('H:i') : '') }}" required>
                                                @error('jam')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Tanggal Expired <span class="text-danger">*</span></label>
                                            <div class="col-sm-9">
                                                <input type="date" name="tgl_exp" class="form-control" 
                                                       value="{{ old('tgl_exp', $shoestring->best_before ? \Carbon\Carbon::parse($shoestring->best_before)->format('Y-m-d') : '') }}" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Pemeriksaan Defect <span class="text-danger">* dapat memilih lebih dari satu</span></label>
                                            <div class="col-sm-9">
                                                @php
                                                    $selectedDefectNames = explode(', ', old('sampling_defect_names', $shoestring->sampling_defect ?? ''));
                                                    $selectedDefectQty = old('sampling_defect_qty', $shoestring->sampling_defect_qty ?? []);
                                                    if (!is_array($selectedDefectQty)) {
                                                        $selectedDefectQty = [];
                                                    }
                                                @endphp
                                                <select name="sampling_defect[]" id="sampling_defect" multiple="multiple" class="form-control select-defect">
                                                    @foreach($dataDefect as $defect)
                                                        <option value="{{ $defect->id }}" data-jenis="{{ $defect->jenis_defect }}" data-spec="{{ $defect->spec_defect }}"
                                                            {{ in_array($defect->jenis_defect, $selectedDefectNames) ? 'selected' : '' }}>
                                                            {{ $defect->jenis_defect }}{{ $defect->spec_defect ? ' - ' . $defect->spec_defect : '' }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                <div class="mt-2" id="defect-qty-container">
                                                    @foreach($dataDefect as $defect)
                                                        @if(in_array($defect->jenis_defect, $selectedDefectNames))
                                                            <div class="input-group mb-2">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" style="min-width: 220px;">
                                                                        {{ $defect->jenis_defect }}{{ $defect->spec_defect ? ' - ' . $defect->spec_defect : '' }}
                                                                    </span>
                                                                </div>
                                                                <input type="text" class="form-control" name="sampling_defect_qty[{{ $defect->id }}]" placeholder="Jumlah"
                                                                    value="{{ $selectedDefectQty[$defect->id] ?? '' }}">
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>

                                                <div class="mt-2">
                                                    <label>Total Defect</label>
                                                    <input type="text" class="form-control" id="total_defect" name="total_defect" value="{{ old('total_defect', $shoestring->total_defect ?? '0') }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Catatan</label>
                                            <div class="col-sm-9">
                                                <textarea name="catatan" class="form-control" rows="3" 
                                                          placeholder="Masukkan catatan tambahan">{{ old('catatan', $shoestring->catatan) }}</textarea>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Dokumentasi (Foto) <span class="text-danger">* max 1mb/file (auto compress)</span></label>
                                            <div class="col-sm-9">
                                                @if($shoestring->dokumentasi && is_array($shoestring->dokumentasi) && count($shoestring->dokumentasi) > 0)
                                                    <div class="mb-3">
                                                        <label>Foto Saat Ini:</label>
                                                        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                                                            @foreach($shoestring->dokumentasi as $doc)
                                                                <div class="position-relative">
                                                                    <a href="{{ asset($assetPath . 'storage/' . $doc) }}" target="_blank">
                                                                        <img src="{{ asset($assetPath . 'storage/' . $doc) }}" class="img-thumbnail" style="height: 100px; width: 100px; object-fit: cover;">
                                                                    </a>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                                <input type="file" id="dokumentasi-input" class="form-control-file" accept="image/*" capture="camera" multiple>
                                                <div id="dokumentasi-preview" class="mt-2" style="display: flex; flex-wrap: wrap; gap: 10px;"></div>
                                                <div id="dokumentasi-base64-container"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="float-left">
                                            <button type="submit" class="btn btn-warning">
                                                <i class="fas fa-save"></i> Update Data
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
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        function renderDefectQtyInputs($select) {
            const $container = $('#defect-qty-container');
            if ($container.length === 0) return;

            const selected = $select.val() || [];
            if (!selected.length) {
                $container.html('');
                return;
            }

            let html = '';
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
                        <input type="text" class="form-control" name="sampling_defect_qty[${defectId}]" placeholder="Jumlah">
                    </div>
                `;
            });
            $container.html(html);
        }

        $(document).on('change', '#sampling_defect', function() {
            renderDefectQtyInputs($(this));
        });

        function updateTotalDefect() {
            let total = 0;
            $('input[name^="sampling_defect_qty"]').each(function() {
                const v = $(this).val();
                if (v !== null && v !== '' && !isNaN(v)) {
                    total += parseFloat(v);
                }
            });
            $('#total_defect').val(total);
        }

        $(document).on('input', 'input[name^="sampling_defect_qty"]', function() {
            updateTotalDefect();
        });

        updateTotalDefect();

        // Logic untuk kompresi dokumentasi (image upload)
        $('#dokumentasi-input').on('change', function(e) {
            const $previewContainer = $('#dokumentasi-preview');
            const $base64Container = $('#dokumentasi-base64-container');
            
            $previewContainer.empty();
            $base64Container.empty();
            
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
                        
                        const dataUrl = canvas.toDataURL('image/jpeg', 0.7);
                        
                        const imgElement = $(`<img src="${dataUrl}" class="img-thumbnail" style="height: 100px; width: 100px; object-fit: cover;">`);
                        $previewContainer.append(imgElement);
                        
                        const hiddenInput = $(`<input type="hidden" name="dokumentasi_base64[]">`);
                        hiddenInput.val(dataUrl);
                        $base64Container.append(hiddenInput);
                    };
                    img.src = readerEvent.target.result;
                };
                reader.readAsDataURL(file);
            });
        });
    });
</script>
@endpush