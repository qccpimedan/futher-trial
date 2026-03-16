@extends('layouts.app')

@section('title', 'Tambah Data Timbangan')

@section('container')
<!-- Content Header (Page header) -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Buat Laporan Verifikasi Timbangan</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Home</a></li>
                            <!-- <li class="breadcrumb-item"><a href="#">QC Sistem</a></li> -->
                            <li class="breadcrumb-item"><a href="{{ route('timbangan.index') }}">Data Timbangan</a></li>
                            <li class="breadcrumb-item active">Tambah Data</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-balance-scale mr-1"></i>
                                Buat Laporan Verifikasi Timbangan 
                            </h3>
                        </div>
                        <!-- /.card-header -->
                        
                        <form action="{{ route('timbangan.store') }}" method="POST">
                            @csrf
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

                                @php
                                    $user = auth()->user();
                                    $roleId = $user->id_role ?? $user->role ?? 0;
                                @endphp

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tanggal">Tanggal</label>
                                            @if($roleId == 2 || $roleId == 3)
                                                <input type="text" name="tanggal" id="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', now()->format('d-m-Y')) }}" readonly required>
                                            @else
                                                <input type="text" name="tanggal" id="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', now()->format('d-m-Y H:i:s')) }}" readonly required>
                                            @endif
                                            @error('tanggal')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="shift_id">Shift</label>
                                            <select name="shift_id" id="shift_id" class="form-control @error('shift_id') is-invalid @enderror" required>
                                                <option value="">-- Pilih Shift --</option>
                                                @foreach($shifts as $shift)
                                                    <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>
                                                        {{ $shift->shift }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('shift_id')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="jam">Jam</label>
                                            <input type="time" name="jam" id="jam" class="form-control @error('jam') is-invalid @enderror" value="{{ old('jam', date('H:i')) }}" required>
                                            @error('jam')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <ul class="nav nav-tabs" id="verifikasiTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link active" id="tab-timbangan" data-toggle="tab" href="#pane-timbangan" role="tab" aria-controls="pane-timbangan" aria-selected="true">Timbangan</a>
                                    </li>
                                   
                                </ul>

                                <div class="tab-content" id="verifikasiTabsContent">
                                    <div class="tab-pane fade show active pt-3" id="pane-timbangan" role="tabpanel" aria-labelledby="tab-timbangan">
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="timbanganTable" style="white-space: nowrap;">
                                                <thead class="bg-light text-center">
                                                    <tr>
                                                        <th style="width:5%">No</th>
                                                        <th>Jenis dan Kode Timbangan</th>
                                                        <th style="width:20%">Hasil Verifikasi 500 Gr</th>
                                                        <th style="width:20%">Hasil Verifikasi 1000 Gr</th>
                                                        <th style="width:10%">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="timbanganTableBody">
                                                    <tr data-index="0">
                                                        <td class="text-center">1</td>
                                                        <td>
                                                            <select class="form-control select-timbangan" data-index="0" required>
                                                                <option value="">-- Pilih Timbangan --</option>
                                                                @foreach($dataTimbangan as $timbangan)
                                                                    <option value="{{ $timbangan->id }}" data-nama="{{ $timbangan->nama_timbangan }}" data-kode="{{ $timbangan->kode_timbangan }}">
                                                                        {{ $timbangan->nama_timbangan }} - {{ $timbangan->kode_timbangan }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <input type="hidden" name="entries[0][jenis]" id="jenis_0" value="{{ old('entries.0.jenis') }}" required>
                                                            <input type="hidden" name="entries[0][kode_timbangan]" id="kode_timbangan_0" value="{{ old('entries.0.kode_timbangan') }}" required>
                                                            <input type="hidden" name="entries[0][hasil_pengecekan]" value="ok">
                                                            <input type="hidden" name="entries[0][gram]" value="500">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" name="entries[0][hasil_verifikasi_500]" value="{{ old('entries.0.hasil_verifikasi_500') }}" placeholder="Isi hasil">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" name="entries[0][hasil_verifikasi_1000]" value="{{ old('entries.0.hasil_verifikasi_1000') }}" placeholder="Isi hasil">
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-danger btn-sm btn-remove-row" style="display:none;">Hapus</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <button type="button" class="btn btn-success" id="addRowBtn">Tambah Detail Timbangan</button>
                                    </div>
                                 
                                </div>
                            
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Data
                                </button>
                                <a href="{{ route('timbangan.index') }}" class="btn btn-secondary ml-2">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
    </div>
</div>
@push('scripts')
<script>
$(document).ready(function() {
    let rowIndex = 0;

    function updateRemoveButtons() {
        const total = $('#timbanganTableBody tr').length;
        if (total > 1) {
            $('.btn-remove-row').show();
        } else {
            $('.btn-remove-row').hide();
        }
    }

    function reindexRows() {
        $('#timbanganTableBody tr').each(function(i) {
            $(this).attr('data-index', i);
            $(this).find('td').eq(0).text(i + 1);

            $(this).find('.select-timbangan').attr('data-index', i);
            $(this).find('input[name^="entries["][name$="][jenis]"]').attr('name', `entries[${i}][jenis]`).attr('id', `jenis_${i}`);
            $(this).find('input[name^="entries["][name$="][kode_timbangan]"]').attr('name', `entries[${i}][kode_timbangan]`).attr('id', `kode_timbangan_${i}`);
            $(this).find('input[name^="entries["][name$="][hasil_pengecekan]"]').attr('name', `entries[${i}][hasil_pengecekan]`);
            $(this).find('input[name^="entries["][name$="][gram]"]').attr('name', `entries[${i}][gram]`);
            $(this).find('input[name^="entries["][name$="][hasil_verifikasi_500]"]').attr('name', `entries[${i}][hasil_verifikasi_500]`);
            $(this).find('input[name^="entries["][name$="][hasil_verifikasi_1000]"]').attr('name', `entries[${i}][hasil_verifikasi_1000]`);
        });
        rowIndex = $('#timbanganTableBody tr').length - 1;
        updateRemoveButtons();
    }

    $('#addRowBtn').on('click', function() {
        rowIndex++;
        const newRow = `
            <tr data-index="${rowIndex}">
                <td class="text-center">${rowIndex + 1}</td>
                <td>
                    <select class="form-control select-timbangan" data-index="${rowIndex}" required>
                        <option value="">-- Pilih Timbangan --</option>
                        @foreach($dataTimbangan as $timbangan)
                            <option value="{{ $timbangan->id }}" data-nama="{{ $timbangan->nama_timbangan }}" data-kode="{{ $timbangan->kode_timbangan }}">
                                {{ $timbangan->nama_timbangan }} - {{ $timbangan->kode_timbangan }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="entries[${rowIndex}][jenis]" id="jenis_${rowIndex}" required>
                    <input type="hidden" name="entries[${rowIndex}][kode_timbangan]" id="kode_timbangan_${rowIndex}" required>
                    <input type="hidden" name="entries[${rowIndex}][hasil_pengecekan]" value="ok">
                    <input type="hidden" name="entries[${rowIndex}][gram]" value="500">
                </td>
                <td>
                    <input type="text" class="form-control" name="entries[${rowIndex}][hasil_verifikasi_500]" placeholder="Isi hasil">
                </td>
                <td>
                    <input type="text" class="form-control" name="entries[${rowIndex}][hasil_verifikasi_1000]" placeholder="Isi hasil">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm btn-remove-row">Hapus</button>
                </td>
            </tr>
        `;

        $('#timbanganTableBody').append(newRow);
        updateRemoveButtons();
    });

    $(document).on('click', '.btn-remove-row', function() {
        $(this).closest('tr').remove();
        reindexRows();
    });

    $(document).on('change', '.select-timbangan', function() {
        const index = $(this).data('index');
        const opt = $(this).find('option:selected');
        const nama = opt.data('nama') || '';
        const kode = opt.data('kode') || '';

        $(`#jenis_${index}`).val(nama);
        $(`#kode_timbangan_${index}`).val(kode);
    });

    updateRemoveButtons();
});
</script>
@endpush

@endsection