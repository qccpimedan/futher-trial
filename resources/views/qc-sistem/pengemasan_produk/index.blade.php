@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Data Pengemasan Produk</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Pengemasan Produk</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tabel Data Pengemasan Produk</h3>
                                    @if(auth()->user()->hasPermissionTo('create-pengemasan-produk'))
                    <a href="{{ route('pengemasan-produk.create') }}" class="btn btn-primary btn-sm float-right"><i class="fas fa-plus"></i> Tambah Data</a>
                @endif
</div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <!-- Form Pencarian Server-Side -->
                    <div class="row mb-3 mt-3">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('pengemasan-produk.index') }}">
                                <div class="input-group input-group-sm" style="width: 300px;">
                                    <input type="text" class="form-control" name="search" placeholder="Cari nama produk" value="{{ $search ?? '' }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        @if(!empty($search))
                                            <a class="btn btn-outline-danger" href="{{ route('pengemasan-produk.index') }}">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <input type="hidden" name="per_page" value="{{ $perPage ?? 10 }}">
                            </form>
                        </div>
                        <div class="col-md-6">
                            <div class="float-right">
                                <form method="GET" action="{{ route('pengemasan-produk.index') }}">
                                    <select class="form-control form-control-sm" name="per_page" style="width: 80px;" onchange="this.form.submit()">
                                        <option value="5" {{ ($perPage ?? 10) == 5 ? 'selected' : '' }}>5</option>
                                        <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10</option>
                                        <option value="25" {{ ($perPage ?? 10) == 25 ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50</option>
                                        <option value="100" {{ ($perPage ?? 10) == 100 ? 'selected' : '' }}>100</option>
                                    </select>
                                    <input type="hidden" name="search" value="{{ $search ?? '' }}">
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <small class="text-muted">
                                @if(!empty($search))
                                    Hasil pencarian: "<strong>{{ $search }}</strong>"
                                @else
                                    Data Pengemasan Produk
                                @endif
                            </small>
                        </div>
                    </div>

                    <table  style="white-space: nowrap;" class="text-center table-responsive table table-bordered table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Shift</th>
                                
                                <th>Tanggal</th>
                                 <th>Jam</th>
                                <th>Tgl Expired</th>
                                <th>Nama Produk</th>
                                <!-- <th>Plan</th> -->
                                <th>Kode Produksi</th>
                                <th>Standard Suhu Produk IQF</th>
                                <th>Suhu Aktual</th>
                                <th>Waktu Awal Packing</th>
                                <th>Waktu Selesai Packing</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $index => $item)
                                <tr>
                                    <td>{{ $data->firstItem() + $index }}</td>
                                    <td>
                                        @if($item->shift->shift == 1 || $item->shift_id == 1)
                                                        <span class="badge bg-primary">Shift 1</span>
                                                    @elseif($item->shift->shift == 2 || $item->shift_id == 2)
                                                        <span class="badge bg-success">Shift 2</span>
                                                    @elseif($item->shift->shift == 3 || $item->shift_id == 3)
                                                        <span class="badge bg-secondary">Shift 3</span>
                                                    @else
                                                        <span class="badge bg-info">{{ $item->shift->shift ?? '-' }}</span>
                                                    @endif
                                    </td>
                                    <td>
                                        @if(auth()->user()->id_role == 2 || auth()->user()->id_role == 3)
                                            <span class="badge badge-secondary">{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</span>
                                        @else
                                            <span class="badge badge-secondary">{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y H:i:s') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-info"> {{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</span>
                                        
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal_expired)->format('d-m-Y') }}</td>
                                    <td>{{ $item->produk->nama_produk }} {{ $item->berat }} gram</td>
                                    <td>{{ $item->kode_produksi }}</td>
                                    <td>{{ ($item->std_suhu_produk_iqf !== null && $item->std_suhu_produk_iqf !== '') ? (trim(str_replace(['Â', '°C'], '', $item->std_suhu_produk_iqf)) . '°C') : '-' }}</td>
                                    <td>
                                        @if(is_array($item->aktual_suhu_produk))
                                            @foreach($item->aktual_suhu_produk as $suhu)
                                                <span class="badge badge-info">{{ trim(str_replace(['Â', '°C'], '', $suhu)) }}°C</span>
                                            @endforeach
                                        @else
                                            {{ ($item->aktual_suhu_produk !== null && $item->aktual_suhu_produk !== '') ? (trim(str_replace(['Â', '°C'], '', $item->aktual_suhu_produk)) . '°C') : '-' }}
                                        @endif
                                    </td>
                                    <td>{{ $item->waktu_awal_packing }}</td>
                                    <td>{{ $item->waktu_selesai_packing }}</td>
                                    <td>
                                        @if(($item->pengemasan_plastik_count ?? 0) > 0)
                                            <button type="button" class="btn btn-secondary btn-sm" title="Pengemasan Plastik sudah diinput" disabled>
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @else
                                            <a href="{{ route('pengemasan-plastik.create', ['shift_id' => $item->id_shift, 'id_pengemasan_produk' => $item->id]) }}" class="btn btn-success btn-sm" title="Lanjut ke Pengemasan Plastik">
                                                <i class="fas fa-arrow-right"></i>
                                            </a>
                                        @endif
                                        <x-action-buttons :item="$item" route-prefix="pengemasan-produk" :show-view="false" :show-logs="true" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $data->appends(['search' => $search ?? '', 'per_page' => $perPage ?? ''])->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection