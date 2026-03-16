@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Tambah Data Area Proses</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('area-proses.index') }}">Data Area Proses</a></li>
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
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-plus text-success mr-2"></i>
                                Form Tambah Data Area Proses
                            </h3>
                        </div>
                        <form action="{{ route('area-proses.store') }}" method="POST">
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
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label for="tanggal" class="font-weight-bold">Tanggal <span class="text-danger">*</span></label>
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
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="shift_id" class="font-weight-bold">Shift <span class="text-danger">*</span></label>
                                            <select class="form-control @error('shift_id') is-invalid @enderror" 
                                                    id="shift_id" name="shift_id" required>
                                                <option value="">Pilih Shift</option>
                                                @foreach($shifts as $shift)
                                                    <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>
                                                        {{ $shift->shift }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('shift_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="area_id" class="font-weight-bold">Area <span class="text-danger">*</span></label>
                                            <select class="form-control @error('area_id') is-invalid @enderror" 
                                                    id="id_area_select_area_proses" name="area_id" required>
                                                <option value="">Pilih Area</option>
                                                @foreach($areas as $area)
                                                    <option value="{{ $area->id }}" {{ old('area_id') == $area->id ? 'selected' : '' }}>
                                                        {{ $area->area }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('area_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="jam" class="font-weight-bold">Jam <span class="text-danger">*</span></label>
                                            <input type="time" 
                                                   name="jam" 
                                                   id="jam" 
                                                   class="form-control @error('jam') is-invalid @enderror" 
                                                   value="{{ old('jam', date('H:i')) }}" 
                                                   required>
                                            @error('jam')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label for="kebersihan_ruangan" class="font-weight-bold">Kebersihan Ruangan <span class="text-danger">*</span></label>
                                            <select class="form-control @error('kebersihan_ruangan') is-invalid @enderror" 
                                                    id="kebersihan_ruangan" name="kebersihan_ruangan" required>
                                                <option value="">Pilih Status</option>
                                                <option value="OK" {{ old('kebersihan_ruangan') == 'OK' ? 'selected' : '' }}>
                                                    ✓ OK
                                                </option>
                                                <option value="Kotor" {{ old('kebersihan_ruangan') == 'Kotor' ? 'selected' : '' }}>
                                                    ✗ Kotor
                                                </option>
                                            </select>
                                            @error('kebersihan_ruangan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3" id="kondisi_barang_container" style="display:none;">
                                        <div class="form-group mb-3">
                                            <label for="kondisi_barang" class="font-weight-bold">Kondisi Barang <span class="text-danger">*</span></label>
                                            <select class="form-control @error('kondisi_barang') is-invalid @enderror" 
                                                    id="kondisi_barang" name="kondisi_barang">
                                                <option value="">Pilih Kondisi</option>
                                                <option value="Baik" {{ old('kondisi_barang') == 'Baik' ? 'selected' : '' }}> ✓ OK</option>
                                                <option value="Rusak" {{ old('kondisi_barang') == 'Rusak' ? 'selected' : '' }}>✗ Tidak Ok</option>
                                            </select>
                                            @error('kondisi_barang')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label for="kebersihan_karyawan" class="font-weight-bold">Kebersihan Karyawan <span class="text-danger">*</span></label>
                                            <select class="form-control @error('kebersihan_karyawan') is-invalid @enderror" 
                                                    id="kebersihan_karyawan" name="kebersihan_karyawan" required>
                                                <option value="">Pilih Status</option>
                                                <option value="OK" {{ old('kebersihan_karyawan') == 'OK' ? 'selected' : '' }}>
                                                    ✓ OK
                                                </option>
                                                <option value="Kotor" {{ old('kebersihan_karyawan') == 'Kotor' ? 'selected' : '' }}>
                                                    ✗ Kotor
                                                </option>
                                            </select>
                                            @error('kebersihan_karyawan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label for="pemeriksaan_suhu_ruang" class="font-weight-bold">Pemeriksaan Suhu Ruang <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   class="form-control @error('pemeriksaan_suhu_ruang') is-invalid @enderror" 
                                                   id="pemeriksaan_suhu_ruang" 
                                                   name="pemeriksaan_suhu_ruang" 
                                                   value="{{ old('pemeriksaan_suhu_ruang') }}"
                                                   placeholder="Masukkan suhu ruang (°C)"
                                                   required>
                                            @error('pemeriksaan_suhu_ruang')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="ketidaksesuaian" class="font-weight-bold">Ketidaksesuaian</label>
                                            <textarea class="form-control @error('ketidaksesuaian') is-invalid @enderror" 
                                                      id="ketidaksesuaian" 
                                                      name="ketidaksesuaian" 
                                                      rows="3"
                                                      placeholder="Masukkan keterangan ketidaksesuaian jika ada">{{ old('ketidaksesuaian') }}</textarea>
                                            @error('ketidaksesuaian')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="tindakan_koreksi" class="font-weight-bold">Tindakan Koreksi</label>
                                            <textarea class="form-control @error('tindakan_koreksi') is-invalid @enderror" 
                                                      id="tindakan_koreksi" 
                                                      name="tindakan_koreksi" 
                                                      rows="3"
                                                      placeholder="Masukkan tindakan koreksi yang dilakukan">{{ old('tindakan_koreksi') }}</textarea>
                                            @error('tindakan_koreksi')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="">
                                    <a href="{{ route('area-proses.index') }}" class="btn btn-md btn-secondary">
                                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                                    </a>
                                    <button type="submit" class="btn btn-md btn-primary">
                                        <i class="fas fa-save mr-2"></i>Simpan Data
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        const kondisiBarangContainer = $('#kondisi_barang_container');

        function toggleKondisiBarang() {
            let selectedText = $('#id_area_select_area_proses option:selected').text().trim().toLowerCase();
            console.log('Selected area:', selectedText); // Debug cek nilai yang dipilih

            if (selectedText === 'chillroom' || selectedText === 'seasoning') {
                kondisiBarangContainer.show();
                $('#kondisi_barang').attr('required', 'required');
            } else {
                kondisiBarangContainer.hide();
                $('#kondisi_barang').removeAttr('required');
            }
        }

        $('#id_area_select_area_proses').on('change', toggleKondisiBarang);

        // Jalankan sekali saat halaman load untuk cek default value
        toggleKondisiBarang();
    });
</script>
@endpush
@endsection
