@extends('layouts.app')

@section('title', 'Tambah Verifikasi Kebersihan Mesin & Peralatan')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Tambah Verifikasi Kebersihan Ruangan Mesin & Peralatan</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('verif-peralatan.index') }}">Verifikasi Peralatan</a></li>
                            <li class="breadcrumb-item active">Tambah</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Form Verifikasi</h3>
                                <div class="card-tools">
                                    <a href="{{ route('verif-peralatan.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                </div>
                            </div>

                            <form action="{{ route('verif-peralatan.store') }}" method="POST">
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
                                        @if(auth()->user()->role === 'superadmin')
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="id_plan">Plan <span class="text-danger">*</span></label>
                                                    <select name="id_plan" id="id_plan" class="form-control @error('id_plan') is-invalid @enderror" onchange="window.location='{{ route('verif-peralatan.create') }}?plan_id='+this.value" required>
                                                        @foreach($plans as $plan)
                                                            <option value="{{ $plan->id }}" {{ (int) $selectedPlanId === (int) $plan->id ? 'selected' : '' }}>
                                                                {{ $plan->nama_plan }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('id_plan')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endif

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="id_shift">Shift <span class="text-danger">*</span></label>
                                                <select name="id_shift" id="id_shift" class="form-control @error('id_shift') is-invalid @enderror" required>
                                                    <option value="">Pilih Shift</option>
                                                    @foreach($shifts as $shift)
                                                        <option value="{{ $shift->id }}" {{ old('id_shift') == $shift->id ? 'selected' : '' }}>
                                                            {{ $shift->shift }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('id_shift')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                                                <input type="date" name="tanggal" id="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', now()->format('Y-m-d')) }}" required>
                                                @error('tanggal')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="jam">Jam <span class="text-danger">*</span></label>
                                                <input type="time" name="jam" id="jam" class="form-control @error('jam') is-invalid @enderror" value="{{ old('jam', now()->format('H:i')) }}">
                                                @error('jam')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="area_filter">Area</label>
                                                <select id="area_filter" class="form-control">
                                                    <option value="all">All Area</option>
                                                    @foreach($areas as $area)
                                                        <option value="{{ $area->id }}">{{ $area->area }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>

                                    @php
                                        $mesinsByArea = $mesins->groupBy('id_area');
                                    @endphp

                                    @forelse($areas as $area)
                                        @php
                                            $areaMesins = $mesinsByArea->get($area->id, collect());
                                        @endphp

                                        @if($areaMesins->count() > 0)
                                            <div class="card card-outline card-primary mb-3 verif-area-section" data-area-id="{{ $area->id }}">
                                                <div class="card-header">
                                                    <h3 class="card-title">Area: {{ $area->area }}</h3>
                                                    <div class="card-tools">
                                                        <div class="form-check form-check-inline mb-0">
                                                            <input class="form-check-input check-all-ok" type="checkbox" data-area-id="{{ $area->id }}" id="check_all_ok_{{ $area->id }}">
                                                            <label class="form-check-label" for="check_all_ok_{{ $area->id }}">Check All OK</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-striped" style="white-space:nowrap;">
                                                            <thead>
                                                                <tr>
                                                                    <th style="width:140px;" class="text-center">Verifikasi</th>
                                                                    <th>Mesin/Peralatan</th>
                                                                    <th style="min-width:240px;">Keterangan</th>
                                                                    <th style="min-width:240px;">Tindakan Koreksi</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($areaMesins as $mesin)
                                                                    <tr>
                                                                        <td class="text-center">
                                                                            @php
                                                                                $oldVerif = old("details.{$mesin->id}.verifikasi");
                                                                                $verifVal = ($oldVerif === null) ? '0' : (string) (int) (bool) $oldVerif;
                                                                            @endphp
                                                                            <select name="details[{{ $mesin->id }}][verifikasi]" class="form-control form-control-sm verifikasi-select" data-area-id="{{ $area->id }}">
                                                                                <option value="1" {{ $verifVal === '1' ? 'selected' : '' }}>OK</option>
                                                                                <option value="0" {{ $verifVal === '0' ? 'selected' : '' }}>Tidak OK</option>
                                                                            </select>
                                                                        </td>
                                                                        <td>{{ $mesin->nama_mesin }}</td>
                                                                        <td>
                                                                            <div class="verif-only-notok" data-mesin-id="{{ $mesin->id }}">
                                                                                <textarea name="details[{{ $mesin->id }}][keterangan]" class="form-control" rows="2">{{ old("details.{$mesin->id}.keterangan") }}</textarea>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="verif-only-notok" data-mesin-id="{{ $mesin->id }}">
                                                                                <textarea name="details[{{ $mesin->id }}][tindakan_koreksi]" class="form-control" rows="2">{{ old("details.{$mesin->id}.tindakan_koreksi") }}</textarea>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @empty
                                        <div class="alert alert-warning">Data area belum tersedia.</div>
                                    @endforelse
                                </div>

                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const areaFilter = document.getElementById('area_filter');
    const sections = document.querySelectorAll('.verif-area-section');

    function applyVerifRowVisibility(selectEl) {
        const name = selectEl.getAttribute('name') || '';
        const match = name.match(/details\[(\d+)\]\[verifikasi\]/);
        if (!match) return;
        const mesinId = match[1];
        const show = (selectEl.value === '0');
        document.querySelectorAll('.verif-only-notok[data-mesin-id="' + mesinId + '"]').forEach((el) => {
            el.style.display = show ? '' : 'none';
        });
    }

    function applyAreaFilter() {
        const val = areaFilter.value;
        sections.forEach((el) => {
            if (val === 'all') {
                el.style.display = '';
                return;
            }
            el.style.display = (el.getAttribute('data-area-id') === val) ? '' : 'none';
        });
    }

    areaFilter.addEventListener('change', applyAreaFilter);
    applyAreaFilter();

    document.querySelectorAll('.verifikasi-select').forEach((sel) => {
        applyVerifRowVisibility(sel);
        sel.addEventListener('change', function () {
            applyVerifRowVisibility(sel);
        });
    });

    document.querySelectorAll('.check-all-ok').forEach((chk) => {
        chk.addEventListener('change', function () {
            const areaId = chk.getAttribute('data-area-id');
            const value = chk.checked ? '1' : '0';
            document.querySelectorAll('.verifikasi-select[data-area-id="' + areaId + '"]').forEach((sel) => {
                sel.value = value;
                applyVerifRowVisibility(sel);
            });
        });
    });
});
</script>
@endpush
