@extends('layouts.app')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h1 class="m-0">Edit Proses Tumbling</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('proses-tumbling.index') }}">Proses Tumbling</a></li>
                            <li class="breadcrumb-item active">Edit Data</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-warning">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-cogs"></i> Form Edit Proses Tumbling
                                </h3>
                            </div>
                            <form action="{{ route('proses-tumbling.update', ['uuid' => $prosesTumbling->uuid]) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="id_tumbling" value="{{ $prosesTumbling->id_tumbling }}">
                                <div class="card-body">
                                    {{-- Stepper Indikator Proses --}}
                                    @include('components.stepper-tumbling', [
                                        'step' => 2,
                                        'bahanBakuUuid' => $prosesTumbling->bahan_baku_tumbling_uuid,
                                        'prosesTumblingId' => $prosesTumbling->id,
                                        'prosesTumblingUuid' => $prosesTumbling->uuid,
                                        'prosesAgingUuid' => $prosesTumbling->prosesAging->first()->uuid ?? null
                                    ])
                                    <div class="row">
                                        <!-- Form Input Section -->
                                        <div class="col-md-12">
                                            <div class="card card-outline card-secondary">
                                                <div class="card-header">
                                                    <h3 class="card-title">
                                                        <i class="fas fa-info-circle"></i> Informasi Dasar
                                                    </h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="id_produk">
                                                                    <i class="fas fa-box"></i> Produk
                                                                </label>
                                                                <select class="form-control @error('id_produk') is-invalid @enderror" 
                                                                        id="id_produk" name="id_produk" required>
                                                                    <option value="">Pilih Produk</option>
                                                                    @foreach($produks as $produk)
                                                                        <option value="{{ $produk->id }}" 
                                                                            {{ (old('id_produk', $prosesTumbling->id_produk) == $produk->id) ? 'selected' : '' }}>
                                                                            {{ $produk->nama_produk }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                @error('id_produk')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            @if($prosesTumbling->bahan_baku_tumbling_id)
                                                                <input type="hidden" name="shift_id" value="{{ $prosesTumbling->shift_id }}">
                                                            @else
                                                                <div class="form-group">
                                                                    <label for="shift_id">
                                                                        <i class="fas fa-clock"></i> Shift
                                                                    </label>
                                                                    <select class="form-control @error('shift_id') is-invalid @enderror" 
                                                                         name="shift_id" required>
                                                                        <option value="">Pilih Shift</option>
                                                                        @foreach($shifts as $shift)
                                                                            <option value="{{ $shift->id }}" 
                                                                                {{ (old('shift_id', $prosesTumbling->shift_id) == $shift->id) ? 'selected' : '' }}>
                                                                                {{ $shift->shift }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                    @error('shift_id')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="kode_produksi">
                                                                    <i class="fas fa-barcode"></i> Kode Produksi
                                                                </label>
                                                                <input type="text" class="form-control @error('kode_produksi') is-invalid @enderror" 
                                                                       id="kode_produksi" name="kode_produksi" 
                                                                       value="{{ old('kode_produksi', $prosesTumbling->kode_produksi) }}" required>
                                                                @error('kode_produksi')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            @if($prosesTumbling->bahan_baku_tumbling_id)
                                                                <input type="hidden" name="tanggal" value="{{ $prosesTumbling->tanggal->format('d-m-Y H:i:s') }}">
                                                            @else
                                                                <div class="form-group">
                                                                    <label for="tanggal">
                                                                        <i class="fas fa-calendar"></i> Tanggal
                                                                    </label>
                                                                    <div class="input-group">
                                                                        <div class="input-group-prepend">
                                                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                                                        </div>
                                                                        <input type="text" name="tanggal" id="tanggal" 
                                                                               class="form-control @error('tanggal') is-invalid @enderror" 
                                                                               value="{{ old('tanggal', $prosesTumbling->tanggal->format('d-m-Y H:i:s')) }}" readonly>
                                                                    </div>
                                                                    @error('tanggal')
                                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actual Values Section -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="card card-outline card-warning h-100">
                                                        <div class="card-header">
                                                            <h3 class="card-title">
                                                                <i class="fas fa-wind"></i> Tumbling Vakum
                                                            </h3>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <strong>Standart</strong>
                                                                    <hr class="mt-1 mb-2">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Drum On</label>
                                                                        <input type="text" class="form-control" value="{{ $prosesTumbling->dataTumbling->drum_on ?? '-' }}" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Drum Off</label>
                                                                        <input type="text" class="form-control" value="{{ $prosesTumbling->dataTumbling->drum_off ?? '-' }}" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Speed</label>
                                                                        <input type="text" class="form-control" value="{{ $prosesTumbling->dataTumbling->drum_speed ?? '-' }}" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Total Waktu</label>
                                                                        <input type="text" class="form-control" value="{{ $prosesTumbling->dataTumbling->total_waktu ?? '-' }}" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Tekanan Vakum</label>
                                                                        <input type="text" class="form-control" value="{{ $prosesTumbling->dataTumbling->tekanan_vakum ?? '-' }}" readonly>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <strong>Aktual</strong>
                                                                    <hr class="mt-1 mb-2">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="aktual_drum_on">
                                                                            <i class="fas fa-play"></i> Drum On
                                                                        </label>
                                                                        <input type="text" class="form-control @error('aktual_drum_on') is-invalid @enderror" 
                                                                               id="aktual_drum_on" name="aktual_drum_on" 
                                                                               value="{{ old('aktual_drum_on', $prosesTumbling->aktual_drum_on) }}">
                                                                        @error('aktual_drum_on')
                                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="aktual_drum_off">
                                                                            <i class="fas fa-stop"></i> Drum Off
                                                                        </label>
                                                                        <input type="text" class="form-control @error('aktual_drum_off') is-invalid @enderror" 
                                                                               id="aktual_drum_off" name="aktual_drum_off" 
                                                                               value="{{ old('aktual_drum_off', $prosesTumbling->aktual_drum_off) }}">
                                                                        @error('aktual_drum_off')
                                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="aktual_speed">
                                                                            <i class="fas fa-tachometer-alt"></i> Speed
                                                                        </label>
                                                                        <input type="text" class="form-control @error('aktual_speed') is-invalid @enderror" 
                                                                               id="aktual_speed" name="aktual_speed" 
                                                                               value="{{ old('aktual_speed', $prosesTumbling->aktual_speed) }}">
                                                                        @error('aktual_speed')
                                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="aktual_total_waktu">
                                                                            <i class="fas fa-clock"></i> Total Waktu
                                                                        </label>
                                                                        <input type="text" class="form-control @error('aktual_total_waktu') is-invalid @enderror" 
                                                                               id="aktual_total_waktu" name="aktual_total_waktu" 
                                                                               value="{{ old('aktual_total_waktu', $prosesTumbling->aktual_total_waktu) }}">
                                                                        @error('aktual_total_waktu')
                                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="aktual_vakum">
                                                                            <i class="fas fa-compress-arrows-alt"></i> Vakum
                                                                        </label>
                                                                        <input type="text" class="form-control @error('aktual_vakum') is-invalid @enderror" 
                                                                               id="aktual_vakum" name="aktual_vakum" 
                                                                               value="{{ old('aktual_vakum', $prosesTumbling->aktual_vakum) }}">
                                                                        @error('aktual_vakum')
                                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="waktu_mulai_tumbling">
                                                                            <i class="fas fa-play-circle"></i> Waktu Mulai Tumbling
                                                                        </label>
                                                                        <input type="text" class="form-control @error('waktu_mulai_tumbling') is-invalid @enderror" 
                                                                               id="waktu_mulai_tumbling" name="waktu_mulai_tumbling" 
                                                                               value="{{ old('waktu_mulai_tumbling', $prosesTumbling->waktu_mulai_tumbling) }}">
                                                                        @error('waktu_mulai_tumbling')
                                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="waktu_selesai_tumbling">
                                                                            <i class="fas fa-stop-circle"></i> Waktu Selesai Tumbling
                                                                        </label>
                                                                        <input type="text" class="form-control @error('waktu_selesai_tumbling') is-invalid @enderror" 
                                                                               id="waktu_selesai_tumbling" name="waktu_selesai_tumbling" 
                                                                               value="{{ old('waktu_selesai_tumbling', $prosesTumbling->waktu_selesai_tumbling) }}">
                                                                        @error('waktu_selesai_tumbling')
                                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="card card-outline card-info h-100">
                                                        <div class="card-header">
                                                            <h3 class="card-title">
                                                                <i class="fas fa-wind"></i> Tumbling Non Vakum
                                                            </h3>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <strong>Standart</strong>
                                                                    <hr class="mt-1 mb-2">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Drum On</label>
                                                                        <input type="text" class="form-control" value="{{ $prosesTumbling->dataTumbling->drum_on_non_vakum ?? '-' }}" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Drum Off</label>
                                                                        <input type="text" class="form-control" value="{{ $prosesTumbling->dataTumbling->drum_off_non_vakum ?? '-' }}" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Speed</label>
                                                                        <input type="text" class="form-control" value="{{ $prosesTumbling->dataTumbling->drum_speed_non_vakum ?? '-' }}" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Total Waktu</label>
                                                                        <input type="text" class="form-control" value="{{ $prosesTumbling->dataTumbling->total_waktu_non_vakum ?? '-' }}" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Tekanan</label>
                                                                        <input type="text" class="form-control" value="{{ $prosesTumbling->dataTumbling->tekanan_non_vakum ?? '-' }}" readonly>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <strong>Aktual</strong>
                                                                    <hr class="mt-1 mb-2">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="aktual_drum_on_non_vakum">
                                                                            <i class="fas fa-play"></i> Drum On
                                                                        </label>
                                                                        <input type="text" class="form-control @error('aktual_drum_on_non_vakum') is-invalid @enderror" 
                                                                               id="aktual_drum_on_non_vakum" name="aktual_drum_on_non_vakum" 
                                                                               value="{{ old('aktual_drum_on_non_vakum', $prosesTumbling->aktual_drum_on_non_vakum) }}">
                                                                        @error('aktual_drum_on_non_vakum')
                                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="aktual_drum_off_non_vakum">
                                                                            <i class="fas fa-stop"></i> Drum Off
                                                                        </label>
                                                                        <input type="text" class="form-control @error('aktual_drum_off_non_vakum') is-invalid @enderror" 
                                                                               id="aktual_drum_off_non_vakum" name="aktual_drum_off_non_vakum" 
                                                                               value="{{ old('aktual_drum_off_non_vakum', $prosesTumbling->aktual_drum_off_non_vakum) }}">
                                                                        @error('aktual_drum_off_non_vakum')
                                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="aktual_speed_non_vakum">
                                                                            <i class="fas fa-tachometer-alt"></i> Speed
                                                                        </label>
                                                                        <input type="text" class="form-control @error('aktual_speed_non_vakum') is-invalid @enderror" 
                                                                               id="aktual_speed_non_vakum" name="aktual_speed_non_vakum" 
                                                                               value="{{ old('aktual_speed_non_vakum', $prosesTumbling->aktual_speed_non_vakum) }}">
                                                                        @error('aktual_speed_non_vakum')
                                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="aktual_total_waktu_non_vakum">
                                                                            <i class="fas fa-clock"></i> Total Waktu
                                                                        </label>
                                                                        <input type="text" class="form-control @error('aktual_total_waktu_non_vakum') is-invalid @enderror" 
                                                                               id="aktual_total_waktu_non_vakum" name="aktual_total_waktu_non_vakum" 
                                                                               value="{{ old('aktual_total_waktu_non_vakum', $prosesTumbling->aktual_total_waktu_non_vakum) }}">
                                                                        @error('aktual_total_waktu_non_vakum')
                                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="aktual_tekanan_non_vakum">
                                                                            <i class="fas fa-compress-arrows-alt"></i> Tekanan
                                                                        </label>
                                                                        <input type="text" class="form-control @error('aktual_tekanan_non_vakum') is-invalid @enderror" 
                                                                               id="aktual_tekanan_non_vakum" name="aktual_tekanan_non_vakum" 
                                                                               value="{{ old('aktual_tekanan_non_vakum', $prosesTumbling->aktual_tekanan_non_vakum) }}">
                                                                        @error('aktual_tekanan_non_vakum')
                                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="waktu_mulai_tumbling_non_vakum">
                                                                            <i class="fas fa-play-circle"></i> Waktu Mulai Tumbling
                                                                        </label>
                                                                        <input type="text" class="form-control @error('waktu_mulai_tumbling_non_vakum') is-invalid @enderror" 
                                                                               id="waktu_mulai_tumbling_non_vakum" name="waktu_mulai_tumbling_non_vakum" 
                                                                               value="{{ old('waktu_mulai_tumbling_non_vakum', $prosesTumbling->waktu_mulai_tumbling_non_vakum) }}">
                                                                        @error('waktu_mulai_tumbling_non_vakum')
                                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="waktu_selesai_tumbling_non_vakum">
                                                                            <i class="fas fa-stop-circle"></i> Waktu Selesai Tumbling
                                                                        </label>
                                                                        <input type="text" class="form-control @error('waktu_selesai_tumbling_non_vakum') is-invalid @enderror" 
                                                                               id="waktu_selesai_tumbling_non_vakum" name="waktu_selesai_tumbling_non_vakum" 
                                                                               value="{{ old('waktu_selesai_tumbling_non_vakum', $prosesTumbling->waktu_selesai_tumbling_non_vakum) }}">
                                                                        @error('waktu_selesai_tumbling_non_vakum')
                                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save"></i> Update
                                    </button>
                                    <a href="{{ route('proses-tumbling.index') }}" class="btn btn-secondary ml-2">
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
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize datetimepicker for the main date
        $('#tanggal').datetimepicker({
            format: 'DD-MM-YYYY HH:mm:ss',
            useCurrent: true,
            sideBySide: true
        });
    });
</script>
@endpush