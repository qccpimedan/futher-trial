@extends('layouts.app')

@section('container')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Edit Proses Roasting Fan</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('proses-roasting-fan.index') }}">Proses Roasting Fan</a></li>
                            <li class="breadcrumb-item">Edit Data</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="card">
                <div class="card-header">
                    <h4>Form Edit Data Session</h4>
                    <p class="text-muted mb-0">Blok yang sudah diisi: 
                        @foreach($sessionRecords as $record)
                            <span class="badge bg-info mr-1">Blok {{ $record->block_number }}</span>
                        @endforeach
                    </p>
                </div>
                <div class="card-body">
                    <form action="{{ route('proses-roasting-fan.update', $firstRecord->uuid) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Informasi Dasar</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label for="tanggal" class="col-sm-2 col-form-label">Tanggal</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" value="{{ $firstRecord->tanggal->format('d-m-Y H:i:s') }}" readonly>
                                    </div>
                                </div>
                                <!-- <div class="form-group row">
                                    <label for="id_shift" class="col-sm-2 col-form-label">Shift</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" value="{{ $firstRecord->shift->shift }}" readonly>
                                    </div>
                                </div> -->
                                <div class="form-group row">
                                    <label for="id_produk" class="col-sm-2 col-form-label">Produk</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" value="{{ $firstRecord->produk->nama_produk }}" readonly>
                                    </div>
                                </div>
                                <!-- <div class="form-group row">
                                    <label for="aktual_lama_proses" class="col-sm-2 col-form-label">Aktual Lama Proses</label>
                                    <div class="col-sm-10">
                                        <div class="input-group">
                                            <input type="text" class="form-control @error('aktual_lama_proses') is-invalid @enderror" 
                                                   id="aktual_lama_proses" name="aktual_lama_proses" 
                                                   value="{{ old('aktual_lama_proses', $firstRecord->aktual_lama_proses) }}" 
                                                   placeholder="Masukkan lama proses aktual">
                                            <div class="input-group-append">
                                                <span class="input-group-text">menit</span>
                                            </div>
                                        </div>
                                        @error('aktual_lama_proses')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div> -->
                                </div>
                            </div>
                        </div>
                        
                        <!-- Dynamic Tables for Each Block -->
                        @php
                            $blockCount = $sessionRecords->count();
                            $halfCount = ceil($blockCount / 2);
                            $firstHalf = $sessionRecords->take($halfCount);
                            $secondHalf = $sessionRecords->skip($halfCount);
                        @endphp
                        
                        <div class="row">
                            <!-- First Table -->
                            <div class="col-md-6">
                                <div class="card card-warning card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">Tabel 1 - Blok 
                                            @foreach($firstHalf as $record)
                                                {{ $record->block_number }}@if(!$loop->last), @endif
                                            @endforeach
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-bordered table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Parameter</th>
                                                    @foreach($firstHalf as $record)
                                                        <th class="text-center">Blok {{ $record->block_number }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="font-weight-bold">Suhu Pemasakan (Standard)%</td>
                                                    @foreach($firstHalf as $record)
                                                        <td class="text-center">{{ $record->suhuBlok->suhu_blok ?? 'N/A' }}°C</td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">Fan 1 (Standard)%</td>
                                                    @foreach($firstHalf as $record)
                                                        <td class="text-center">{{ $record->stdFan->std_fan ?? 'N/A' }}%</td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">Fan 2 (Standard)%</td>
                                                    @foreach($firstHalf as $record)
                                                        <td class="text-center">{{ $record->stdFan->std_fan_2 ?? 'N/A' }}%</td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">Fan 3 (Standard)%</td>
                                                    @foreach($firstHalf as $record)
                                                        <td class="text-center">{{ $record->stdFan->fan_3 ?? 'N/A' }} %</td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">Fan 4 (Standard)%</td>
                                                    @foreach($firstHalf as $record)
                                                        <td class="text-center">{{ $record->stdFan->fan_4 ?? 'N/A' }} %</td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">(Standard) Lama Proses (menit)</td>
                                                    @foreach($firstHalf as $record)
                                                        <td class="text-center">{{ $record->stdFan->std_lama_proses ?? 'N/A' }} menit</td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">Humidity/Steam Valve (Standard)%</td>
                                                    @foreach($firstHalf as $record)
                                                        <td class="text-center">{{ $record->stdFan->std_humadity ?? 'N/A' }}%</td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">Suhu Pemasakan (Aktual)%</td>
                                                    @foreach($firstHalf as $record)
                                                        <td class="text-center">
                                                            <input type="number" step="0.01" class="form-control form-control-sm text-center" 
                                                                   name="records[{{ $record->id }}_{{ $record->block_number }}][suhu_roasting]" 
                                                                   value="{{ old('records.'.$record->id.'_'.$record->block_number.'.suhu_roasting', $record->suhu_roasting) }}">
                                                        </td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">Fan 1 (Aktual)%</td>
                                                    @foreach($firstHalf as $record)
                                                        <td class="text-center">
                                                            <input type="number" step="0.01" class="form-control form-control-sm text-center" 
                                                                   name="records[{{ $record->id }}_{{ $record->block_number }}][fan_1]" 
                                                                   value="{{ old('records.'.$record->id.'_'.$record->block_number.'.fan_1', $record->fan_1) }}">
                                                        </td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">Fan 2 (Aktual)%</td>
                                                    @foreach($firstHalf as $record)
                                                        <td class="text-center">
                                                            <input type="number" step="0.01" class="form-control form-control-sm text-center" 
                                                                   name="records[{{ $record->id }}_{{ $record->block_number }}][fan_2]" 
                                                                   value="{{ old('records.'.$record->id.'_'.$record->block_number.'.fan_2', $record->fan_2) }}">
                                                        </td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">Fan 3 (Aktual)%</td>
                                                    @foreach($firstHalf as $record)
                                                        <td class="text-center">
                                                            <input type="number" step="0.01" class="form-control form-control-sm text-center" 
                                                                   name="records[{{ $record->id }}_{{ $record->block_number }}][fan_3]" 
                                                                   value="{{ old('records.'.$record->id.'_'.$record->block_number.'.fan_3', $record->fan_3) }}">
                                                        </td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">Fan 4 (Aktual)%</td>
                                                    @foreach($firstHalf as $record)
                                                        <td class="text-center">
                                                            <input type="number" step="0.01" class="form-control form-control-sm text-center" 
                                                                   name="records[{{ $record->id }}_{{ $record->block_number }}][fan_4]" 
                                                                   value="{{ old('records.'.$record->id.'_'.$record->block_number.'.fan_4', $record->fan_4) }}">
                                                        </td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">Aktual Lama Proses (menit)</td>
                                                    <td colspan="{{ count($firstHalf) }}" class="text-center">
                                                        <input type="text" class="form-control form-control-sm text-center @error('aktual_lama_proses') is-invalid @enderror" 
                                                        id="aktual_lama_proses" name="aktual_lama_proses" 
                                                        value="{{ old('aktual_lama_proses', $firstRecord->aktual_lama_proses) }}" 
                                                        placeholder="Masukkan lama proses aktual">
                                                    </td>
                                                </tr>
                                                
                                                <tr>
                                                    <td class="font-weight-bold">Humidity/Steam Valve (Aktual)%</td>
                                                    @foreach($firstHalf as $record)
                                                        <td class="text-center">
                                                            <input type="number" step="0.01" class="form-control form-control-sm text-center" 
                                                                   name="records[{{ $record->id }}_{{ $record->block_number }}][aktual_humadity]" 
                                                                   value="{{ old('records.'.$record->id.'_'.$record->block_number.'.aktual_humadity', $record->aktual_humadity) }}">
                                                        </td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">Infra Red</td>
                                                    @foreach($firstHalf as $record)
                                                        <td class="text-center">
                                                            <select class="form-control form-control-sm text-center" 
                                                                    name="records[{{ $record->id }}_{{ $record->block_number }}][infra_red]">
                                                                <option value="">Pilih</option>
                                                                <option value="1" {{ old('records.'.$record->id.'_'.$record->block_number.'.infra_red', $record->infra_red) == '1' ? 'selected' : '' }}>1</option>
                                                                <option value="2" {{ old('records.'.$record->id.'_'.$record->block_number.'.infra_red', $record->infra_red) == '2' ? 'selected' : '' }}>2</option>
                                                                <option value="3" {{ old('records.'.$record->id.'_'.$record->block_number.'.infra_red', $record->infra_red) == '3' ? 'selected' : '' }}>3</option>
                                                            </select>
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Second Table (if there are more blocks) -->
                            @if($secondHalf->count() > 0)
                            <div class="col-md-6">
                                <div class="card card-info card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">Tabel 2 - Blok 
                                            @foreach($secondHalf as $record)
                                                {{ $record->block_number }}@if(!$loop->last), @endif
                                            @endforeach
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-bordered table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Parameter</th>
                                                    @foreach($secondHalf as $record)
                                                        <th class="text-center">Blok {{ $record->block_number }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="font-weight-bold">Suhu Pemasakan (Standard)%</td>
                                                    @foreach($secondHalf as $record)
                                                        <td class="text-center">{{ $record->suhuBlok->suhu_blok ?? 'N/A' }}°C</td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">Fan 1 (Standard)%</td>
                                                    @foreach($secondHalf as $record)
                                                        <td class="text-center">{{ $record->stdFan->std_fan ?? 'N/A' }}%</td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">Fan 2 (Standard)%</td>
                                                    @foreach($secondHalf as $record)
                                                        <td class="text-center">{{ $record->stdFan->std_fan_2 ?? 'N/A' }}%</td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">Fan 3 (Standard)%</td>
                                                    @foreach($secondHalf as $record)
                                                        <td class="text-center">{{ $record->stdFan->fan_3 ?? 'N/A' }} %</td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">Fan 4 (Standard)%</td>
                                                    @foreach($secondHalf as $record)
                                                        <td class="text-center">{{ $record->stdFan->fan_4 ?? 'N/A' }} %</td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">(Standard) Lama Proses (menit)</td>
                                                    @foreach($firstHalf as $record)
                                                        <td class="text-center">{{ $record->stdFan->std_lama_proses ?? 'N/A' }} menit</td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">Humidity/Steam Valve (Standard)%</td>
                                                    @foreach($secondHalf as $record)
                                                        <td class="text-center">{{ $record->stdFan->std_humadity ?? 'N/A' }}%</td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">Suhu Pemasakan (Aktual)%</td>
                                                    @foreach($secondHalf as $record)
                                                        <td class="text-center">
                                                            <input type="number" step="0.01" class="form-control form-control-sm text-center" 
                                                                   name="records[{{ $record->id }}_{{ $record->block_number }}][suhu_roasting]" 
                                                                   value="{{ old('records.'.$record->id.'_'.$record->block_number.'.suhu_roasting', $record->suhu_roasting) }}">
                                                        </td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">Fan 1 (Aktual)%</td>
                                                    @foreach($secondHalf as $record)
                                                        <td class="text-center">
                                                            <input type="number" step="0.01" class="form-control form-control-sm text-center" 
                                                                   name="records[{{ $record->id }}_{{ $record->block_number }}][fan_1]" 
                                                                   value="{{ old('records.'.$record->id.'_'.$record->block_number.'.fan_1', $record->fan_1) }}">
                                                        </td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">Fan 2 (Aktual)%</td>
                                                    @foreach($secondHalf as $record)
                                                        <td class="text-center">
                                                            <input type="number" step="0.01" class="form-control form-control-sm text-center" 
                                                                   name="records[{{ $record->id }}_{{ $record->block_number }}][fan_2]" 
                                                                   value="{{ old('records.'.$record->id.'_'.$record->block_number.'.fan_2', $record->fan_2) }}">
                                                        </td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">Fan 3 (Aktual)%</td>
                                                    @foreach($secondHalf as $record)
                                                        <td class="text-center">
                                                            <input type="number" step="0.01" class="form-control form-control-sm text-center" 
                                                                   name="records[{{ $record->id }}_{{ $record->block_number }}][fan_3]" 
                                                                   value="{{ old('records.'.$record->id.'_'.$record->block_number.'.fan_3', $record->fan_3) }}">
                                                        </td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">Fan 4 (Aktual) %</td>
                                                    @foreach($secondHalf as $record)
                                                        <td class="text-center">
                                                            <input type="number" step="0.01" class="form-control form-control-sm text-center" 
                                                                   name="records[{{ $record->id }}_{{ $record->block_number }}][fan_4]" 
                                                                   value="{{ old('records.'.$record->id.'_'.$record->block_number.'.fan_4', $record->fan_4) }}">
                                                        </td>
                                                    @endforeach
                                                </tr>
                                                <!-- <tr>
                                                    <td class="font-weight-bold">Aktual Lama Proses (menit)</td>
                                                    <td colspan="{{ count($firstHalf) }}" class="text-center">
                                                        <input type="text" class="form-control form-control-sm text-center @error('aktual_lama_proses') is-invalid @enderror" 
                                                        id="aktual_lama_proses" name="aktual_lama_proses" 
                                                        value="{{ old('aktual_lama_proses', $firstRecord->aktual_lama_proses) }}" 
                                                        placeholder="Masukkan lama proses aktual">
                                                    </td>
                                                </tr> -->
                                                <tr>
                                                    <td class="font-weight-bold">Humidity/Steam Valve (Aktual)%</td>
                                                    @foreach($secondHalf as $record)
                                                        <td class="text-center">
                                                            <input type="number" step="0.01" class="form-control form-control-sm text-center" 
                                                                   name="records[{{ $record->id }}_{{ $record->block_number }}][aktual_humadity]" 
                                                                   value="{{ old('records.'.$record->id.'_'.$record->block_number.'.aktual_humadity', $record->aktual_humadity) }}">
                                                        </td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">Infra Red</td>
                                                    @foreach($secondHalf as $record)
                                                        <td class="text-center">
                                                            <select class="form-control form-control-sm text-center" 
                                                                    name="records[{{ $record->id }}_{{ $record->block_number }}][infra_red]">
                                                                <option value="">Pilih</option>
                                                                <option value="1" {{ old('records.'.$record->id.'_'.$record->block_number.'.infra_red', $record->infra_red) == '1' ? 'selected' : '' }}>1</option>
                                                                <option value="2" {{ old('records.'.$record->id.'_'.$record->block_number.'.infra_red', $record->infra_red) == '2' ? 'selected' : '' }}>2</option>
                                                                <option value="3" {{ old('records.'.$record->id.'_'.$record->block_number.'.infra_red', $record->infra_red) == '3' ? 'selected' : '' }}>3</option>
                                                            </select>
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        
                        <div class="card card-success card-outline mt-3">
                            <div class="card-footer text-center">
                                <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Update Data</button>
                                <a href="{{ route('proses-roasting-fan.index') }}" class="btn btn-secondary ml-2"><i class="fas fa-arrow-left"></i> Kembali</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection