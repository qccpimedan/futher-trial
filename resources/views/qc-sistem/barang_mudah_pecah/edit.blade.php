@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Barang Mudah Pecah</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('barang-mudah-pecah.index') }}">Barang Mudah Pecah</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Form Edit Barang Mudah Pecah</h3>
                        </div>
                        
                        <form action="{{ route('barang-mudah-pecah.update', $barangMudahPecah->uuid) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Tanggal</label>
                                            <input type="text" class="form-control" value="{{ $barangMudahPecah->tanggal ? \Carbon\Carbon::parse($barangMudahPecah->tanggal)->format('d-m-Y') : '-' }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Jam</label>
                                            <input type="text" class="form-control" value="{{ $barangMudahPecah->jam ? \Carbon\Carbon::parse($barangMudahPecah->jam)->format('H:i') : '-' }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Shift</label>
                                            <input type="text" class="form-control" value="{{ $barangMudahPecah->shift->shift ?? '-' }}" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped" id="barang-mudah-pecah-table">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 60px;">No</th>
                                                        <th>Nama Barang</th>
                                                        <th style="width: 120px;">Jumlah</th>
                                                        <th style="width: 170px;">Kondisi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $no = 1;
                                                        $rowIndex = 0;
                                                        $grouped = isset($groupItems) ? $groupItems->groupBy('id_area') : collect();
                                                    @endphp

                                                    @forelse($grouped as $areaId => $items)
                                                        @php $areaName = optional($items->first()->area)->area; @endphp
                                                        <tr class="table-secondary">
                                                            <td colspan="4" class="font-weight-bold">
                                                                {{ $areaName ?? 'Area' }}
                                                            </td>
                                                        </tr>

                                                        @foreach($items as $item)
                                                            <tr>
                                                                <td>{{ $no++ }}</td>
                                                                <td>
                                                                    @if($item->is_manual && !empty($item->nama_barang_manual))
                                                                        {{ $item->nama_barang_manual }}
                                                                    @else
                                                                        {{ $item->namaBarang->nama_barang ?? 'Data tidak ditemukan' }}
                                                                    @endif
                                                                    <input type="hidden" name="items[{{ $rowIndex }}][uuid]" value="{{ $item->uuid }}">
                                                                </td>
                                                                <td>
                                                                    <input type="number" class="form-control" value="{{ $item->jumlah ?? 0 }}" readonly>
                                                                </td>
                                                                <td>
                                                                    <select class="form-control" name="items[{{ $rowIndex }}][kondisi]" required>
                                                                        <option value="OK" {{ old("items.$rowIndex.kondisi", $item->kondisi) == 'OK' ? 'selected' : '' }}>OK</option>
                                                                        <option value="Tidak OK" {{ old("items.$rowIndex.kondisi", $item->kondisi) == 'Tidak OK' ? 'selected' : '' }}>Tidak OK</option>
                                                                    </select>
                                                                    @error("items.$rowIndex.kondisi")
                                                                        <div class="text-danger" style="font-size: 12px;">{{ $message }}</div>
                                                                    @enderror
                                                                    @error("items.$rowIndex.uuid")
                                                                        <div class="text-danger" style="font-size: 12px;">{{ $message }}</div>
                                                                    @enderror
                                                                </td>
                                                            </tr>
                                                            @php $rowIndex++; @endphp
                                                        @endforeach
                                                    @empty
                                                        <tr>
                                                            <td colspan="4" class="text-center text-muted">Data tidak ditemukan.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="temuan_ketidaksesuaian">Temuan Ketidaksesuaian</label>
                                            <textarea class="form-control @error('temuan_ketidaksesuaian') is-invalid @enderror" 
                                                      id="temuan_ketidaksesuaian" name="temuan_ketidaksesuaian" 
                                                      rows="4" placeholder="Masukkan temuan ketidaksesuaian (jika ada)">{{ old('temuan_ketidaksesuaian', $barangMudahPecah->temuan_ketidaksesuaian) }}</textarea>
                                            @error('temuan_ketidaksesuaian')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-footer">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Update Data
                                </button>
                                <a href="{{ route('barang-mudah-pecah.index') }}" class="btn btn-secondary">
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
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
    });
</script>
@endpush