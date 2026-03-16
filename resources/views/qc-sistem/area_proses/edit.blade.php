@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Data Area Proses</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('area-proses.index') }}">Data Area Proses</a></li>
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
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-edit text-warning mr-2"></i>
                                Form Edit Data Area Proses
                            </h3>
                        </div>
                        <form action="{{ isset($twoHour) && $twoHour ? route('area-proses.twohour.store', $areaProses->uuid) : route('area-proses.update', $areaProses->uuid) }}" method="POST">
                            @csrf
                            @if(!isset($twoHour) || !$twoHour)
                                @method('PUT')
                            @endif
                            
                            {{-- Hidden required fields untuk mode edit per 2 jam --}}
                            @if(isset($twoHour) && $twoHour)
                                <input type="hidden" name="tanggal" value="{{ \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s') }}">
                                <input type="hidden" name="jam" value="{{ \Carbon\Carbon::now('Asia/Jakarta')->format('H:i') }}">
                            @endif
                            
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

                                @if(isset($twoHour) && $twoHour)
                                    <div class="alert alert-info">
                                        <i class="fas fa-clock"></i>
                                        <strong>Mode Edit Per 2 Jam:</strong> Data akan disimpan sebagai record baru dengan waktu yang diperbarui. Data asli tetap tersimpan.
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="shift_id" class="font-weight-bold">Shift <span class="text-danger">*</span></label>
                                            <select class="form-control @error('shift_id') is-invalid @enderror" 
                                                    id="shift_id" name="shift_id" required>
                                                <option value="">Pilih Shift</option>
                                                @foreach($shifts as $shift)
                                                    <option value="{{ $shift->id }}" 
                                                            {{ (old('shift_id') ?? $areaProses->shift_id) == $shift->id ? 'selected' : '' }}>
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
                                                    <option value="{{ $area->id }}" 
                                                            {{ (old('area_id') ?? $areaProses->area_id) == $area->id ? 'selected' : '' }}>
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
                                            @if(isset($twoHour) && $twoHour)
                                                <input type="text" 
                                                       class="form-control" 
                                                       value="{{ \Carbon\Carbon::now('Asia/Jakarta')->format('H:i') }}" 
                                                       readonly>
                                            @else
                                                <div class="input-group">
                                                    <input type="time" 
                                                           class="form-control @error('jam') is-invalid @enderror" 
                                                           id="jam" 
                                                           name="jam" 
                                                           value="{{ old('jam') ?? $areaProses->jam }}" 
                                                           >
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary" type="button" onclick="setNowJam()">Sekarang</button>
                                                    </div>
                                                </div>
                                            @endif
                                            @error('jam')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label for="tanggal" class="font-weight-bold">Tanggal <span class="text-danger">*</span></label>
                                            @if(isset($twoHour) && $twoHour)
                                                <input type="text" 
                                                       class="form-control" 
                                                       value="{{ \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s') }}" 
                                                       readonly>
                                            @else
                                                <div class="input-group">
                                                    <input type="text" 
                                                           class="form-control @error('tanggal') is-invalid @enderror" 
                                                           id="tanggal" 
                                                           name="tanggal" 
                                                           value="{{ old('tanggal') ?? ($areaProses->tanggal ? $areaProses->tanggal->format('Y-m-d H:i:s') : \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s')) }}" 
                                                           >
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary" type="button" onclick="setNowTanggal()">Sekarang</button>
                                                    </div>
                                                </div>
                                            @endif
                                            @error('tanggal')
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
                                                <option value="OK" {{ (old('kebersihan_ruangan') ?? $areaProses->kebersihan_ruangan) == 'OK' ? 'selected' : '' }}>
                                                    ✓ OK
                                                </option>
                                                <option value="Kotor" {{ (old('kebersihan_ruangan') ?? $areaProses->kebersihan_ruangan) == 'Kotor' ? 'selected' : '' }}>
                                                    ✗ Kotor
                                                </option>
                                            </select>
                                            @error('kebersihan_ruangan')
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
                                                <option value="OK" {{ (old('kebersihan_karyawan') ?? $areaProses->kebersihan_karyawan) == 'OK' ? 'selected' : '' }}>
                                                    ✓ OK
                                                </option>
                                                <option value="Kotor" {{ (old('kebersihan_karyawan') ?? $areaProses->kebersihan_karyawan) == 'Kotor' ? 'selected' : '' }}>
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
                                                   value="{{ old('pemeriksaan_suhu_ruang') ?? $areaProses->pemeriksaan_suhu_ruang }}"
                                                   placeholder="Masukkan suhu ruang (°C)"
                                                   required>
                                            @error('pemeriksaan_suhu_ruang')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3">
                                          <div class="form-group" id="kondisi_barang_container" style="display:none;">
                                            <label for="kondisi_barang" class="font-weight-bold">Kondisi Barang <span class="text-danger">*</span></label>
                                            <select name="kondisi_barang" id="kondisi_barang" class="form-control @error('kondisi_barang') is-invalid @enderror">
                                                <option value="">Pilih Kondisi</option>
                                                <option value="Baik" {{ old('kondisi_barang', $areaProses->kondisi_barang) == 'Baik' ? 'selected' : '' }}>Baik</option>
                                                <option value="Rusak" {{ old('kondisi_barang', $areaProses->kondisi_barang) == 'Rusak' ? 'selected' : '' }}>Rusak</option>
                                            </select>
                                            @error('kondisi_barang')
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
                                                      placeholder="Masukkan keterangan ketidaksesuaian jika ada">{{ old('ketidaksesuaian') ?? $areaProses->ketidaksesuaian }}</textarea>
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
                                                      placeholder="Masukkan tindakan koreksi yang dilakukan">{{ old('tindakan_koreksi') ?? $areaProses->tindakan_koreksi }}</textarea>
                                            @error('tindakan_koreksi')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="">
                                    <div class="">
                                        <button type="submit" class="btn btn-warning btn-sm mr-2">
                                            <i class="fas fa-save"></i>
                                            @isset($twoHour) Simpan Data per 2 Jam (buat riwayat) @else Update Data @endisset
                                        </button>
                                        <a href="{{ route('area-proses.index') }}" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-arrow-left"></i> Kembali
                                        </a>
                                        <div>
                                            <!-- <a href="{{ route('area-proses.show', $areaProses->uuid) }}" class="btn btn-info mr-2">
                                                <i class="fas fa-eye mr-2"></i>Lihat Detail
                                            </a> -->
                                        </div>
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
@push('scripts')
<script>
function setNowTanggal() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    
    const formattedDateTime = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
    document.getElementById('tanggal').value = formattedDateTime;
}

function setNowJam() {
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    
    const formattedTime = `${hours}:${minutes}`;
    document.getElementById('jam').value = formattedTime;
}
</script>

<script>
    $(document).ready(function() {
        const kondisiBarangContainer = $('#kondisi_barang_container');

        function toggleKondisiBarang() {
            let selectedText = $('#id_area_select_area_proses option:selected').text().trim().toLowerCase();
         
            if (selectedText === 'chillroom' || selectedText === 'seasoning') {
                kondisiBarangContainer.show();
                $('#kondisi_barang').attr('required', 'required');
            } else {
                kondisiBarangContainer.hide();
                $('#kondisi_barang').removeAttr('required');
            }
        }

        $('#id_area_select_area_proses').on('change', toggleKondisiBarang);

        toggleKondisiBarang();
    });
</script>
@endpush
@endsection