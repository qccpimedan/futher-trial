@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Tambah Data Kontrol Sanitasi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('kontrol-sanitasi.index') }}">Kontrol Sanitasi</a></li>
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
                                <i class="fas fa-plus text-primary mr-2"></i>
                                Form Tambah Data Kontrol Sanitasi
                            </h3>
                        </div>
                        <form action="{{ route('kontrol-sanitasi.store') }}" method="POST">
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
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="jam" class="font-weight-bold">Jam <span class="text-danger">*</span></label>
                                            <input type="time" 
                                                   class="form-control @error('jam') is-invalid @enderror" 
                                                   id="jam" 
                                                   name="jam" 
                                                   value="{{ old('jam') ?? \Carbon\Carbon::now('Asia/Jakarta')->format('H:i') }}" 
                                                   required>
                                            @error('jam')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="shift_id" class="font-weight-bold">Shift <span class="text-danger">*</span></label>
                                            <select class="form-control @error('shift_id') is-invalid @enderror" 
                                                    id="shift_id" name="shift_id" required>
                                                <option value="">Pilih Shift</option>
                                                @foreach($shifts as $shift)
                                                    <option value="{{ $shift->id }}" 
                                                            {{ old('shift_id') == $shift->id ? 'selected' : '' }}>
                                                        {{ $shift->shift }}                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('shift_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="suhu_air" class="font-weight-bold">Suhu Air (°C) <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   class="form-control @error('suhu_air') is-invalid @enderror" 
                                                   id="suhu_air" 
                                                   name="suhu_air" 
                                                   value="{{ old('suhu_air') }}" 
                                                   min="0" 
                                                   max="100"
                                                   placeholder="Masukkan suhu air"
                                                   required>
                                            @error('suhu_air')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="kadar_klorin_food_basin" class="font-weight-bold">Kadar Klorin Foot Basin <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   class="form-control @error('kadar_klorin_food_basin') is-invalid @enderror" 
                                                   id="kadar_klorin_food_basin" 
                                                   name="kadar_klorin_food_basin" 
                                                   value="{{ old('kadar_klorin_food_basin') }}" 
                                                   placeholder="Masukkan kadar klorin food basin"
                                                   required>
                                            @error('kadar_klorin_food_basin')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="kadar_klorin_hand_basin" class="font-weight-bold">Kadar Klorin Hand Basin <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   class="form-control @error('kadar_klorin_hand_basin') is-invalid @enderror" 
                                                   id="kadar_klorin_hand_basin" 
                                                   name="kadar_klorin_hand_basin" 
                                                   value="{{ old('kadar_klorin_hand_basin') }}" 
                                                   placeholder="Masukkan kadar klorin hand basin"
                                                   required>
                                            @error('kadar_klorin_hand_basin')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label for="hasil_verifikasi" class="font-weight-bold">Hasil Verifikasi <span class="text-danger">*</span></label>
                                            <textarea class="form-control @error('hasil_verifikasi') is-invalid @enderror" 
                                                      id="hasil_verifikasi" 
                                                      name="hasil_verifikasi" 
                                                      rows="4"
                                                      placeholder="Masukkan hasil verifikasi kontrol sanitasi"
                                                      required>{{ old('hasil_verifikasi') }}</textarea>
                                            @error('hasil_verifikasi')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="">
                                    <div class="">
                                        <button type="submit" class="btn btn-primary btn-sm mr-2">
                                            <i class="fas fa-save"></i> Simpan Data
                                        </button>
                                        <a href="{{ route('kontrol-sanitasi.index') }}" class="btn btn-sm btn-secondary">
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

@endsection