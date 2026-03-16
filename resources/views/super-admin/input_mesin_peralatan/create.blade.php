@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Tambah Data Input Mesin/Peralatan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('input-mesin-peralatan.index') }}">Input Mesin/Peralatan</a></li>
                        <li class="breadcrumb-item active">Tambah Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-plus text-primary mr-2"></i>
                                Form Tambah Data Input Mesin/Peralatan
                            </h3>
                        </div>

                        <form action="{{ route('input-mesin-peralatan.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                @if($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="id_area">Area <span class="text-danger">*</span></label>
                                            <select class="form-control @error('id_area') is-invalid @enderror" id="id_area" name="id_area" required>
                                                <option value="">Pilih Area</option>
                                                @foreach($areas as $area)
                                                    <option value="{{ $area->id }}" {{ old('id_area') == $area->id ? 'selected' : '' }}>
                                                        {{ $area->area }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('id_area')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Nama Mesin/Peralatan <span class="text-danger">*</span></label>
                                            <div id="nama-mesin-container">
                                                @php
                                                    $oldNamaMesin = old('nama_mesin');
                                                    $rows = (is_array($oldNamaMesin) && count($oldNamaMesin) > 0) ? $oldNamaMesin : [''];
                                                @endphp

                                                @foreach($rows as $val)
                                                    <div class="input-group mb-2">
                                                        <input type="text" name="nama_mesin[]" class="form-control" placeholder="Masukkan nama mesin/peralatan" value="{{ $val }}" required>
                                                        <div class="input-group-append">
                                                            <button class="btn btn-danger remove-nama-mesin" type="button">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <button id="add-nama-mesin" type="button" class="btn btn-success btn-sm mt-2">
                                                <i class="fas fa-plus"></i> Tambah Nama Mesin
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <a href="{{ route('input-mesin-peralatan.index') }}" class="btn btn-md btn-secondary">
                                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                                </a>
                                <button type="submit" class="btn btn-md btn-primary">
                                    <i class="fas fa-save mr-2"></i>Simpan Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('nama-mesin-container');
        const addBtn = document.getElementById('add-nama-mesin');

        const createRow = () => {
            const div = document.createElement('div');
            div.classList.add('input-group', 'mb-2');
            div.innerHTML = `
                <input type="text" name="nama_mesin[]" class="form-control" placeholder="Masukkan nama mesin/peralatan" required>
                <div class="input-group-append">
                    <button class="btn btn-danger remove-nama-mesin" type="button">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            container.appendChild(div);
        };

        addBtn.addEventListener('click', createRow);

        container.addEventListener('click', function (e) {
            if (e.target.closest('.remove-nama-mesin')) {
                const row = e.target.closest('.input-group');
                if (container.querySelectorAll('.input-group').length > 1) {
                    row.remove();
                } else {
                    const input = row.querySelector('input');
                    if (input) input.value = '';
                }
            }
        });
    });
</script>
@endpush
