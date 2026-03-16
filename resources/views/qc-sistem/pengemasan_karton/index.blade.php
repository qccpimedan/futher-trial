@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Pengemasan Karton</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Pengemasan Karton</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Data Pengemasan Karton</h3>
                            <div class="card-tools">
                                <!--
                                    @if(auth()->user()->hasPermissionTo('create-pengemasan-karton')) <a href="{{ route('pengemasan-karton.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Tambah Data
                                </a> @endif
-->
                            </div>
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    {{ session('success') }}
                                </div>
                            @endif
                            <div class="table-responsive">
                                <!-- Form Pencarian Server-Side -->
                                <div class="row mb-3 mt-3">
                                    <div class="col-md-4 offset-md-8">
                                        <form action="{{ route('pengemasan-karton.index') }}" method="GET">
                                            <div class="input-group">
                                                <input type="text" name="search" class="form-control" placeholder="Cari Nama Produk" value="{{ request('search') }}">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="submit">
                                                        <i class="fas fa-search"></i> Cari
                                                    </button>
                                                    @if(request('search'))
                                                        <a href="{{ route('pengemasan-karton.index') }}" class="btn btn-outline-danger" title="Clear Search">
                                                            <i class="fas fa-times"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <table style="white-space: nowrap;" class="table table-bordered table-striped text-center">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Shift</th>
                                            <th>Tanggal</th>
                                            <th>Jam</th>
                                            <!-- <th>Plan</th> -->
                                            <th>Nama Produk</th>
                                            <th>Kode Produksi</th>
                                            <th>Identitas Produk Pada Karton (Tinta)</th>
                                            <th>Standar Jumlah Karton (pcs)</th>
                                            <th>Aktual Jumlah Karton (pcs)</th>
                                        
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pengemasanKarton as $index => $item)
                                            <tr>
                                                <td>{{ $pengemasanKarton->firstItem() + $index }}</td>
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
                                                    <span class="badge badge-info">{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</span>
                                                </td>
                                                <!-- <td>{{ $item->plan->nama_plan ?? '-' }}</td> -->
                                                <td>{{ optional(optional($item->pengemasanProduk)->produk)->nama_produk ?? '-' }}  {{ optional($item->pengemasanProduk)->berat ?? '-' }} gram</td>
                                                <td>{{ optional($item->pengemasanProduk)->kode_produksi ?? '-' }}</td>
                                                <td>{{ ($item->identitas_produk_pada_karton ?? null) !== null && ($item->identitas_produk_pada_karton ?? '') !== '' ? trim(str_replace([
                                                    "\u{00E2}\u{0153}\u{201D}",
                                                    "\u{00E2}\u{0153}\u{201C}",
                                                    "\u{00E2}\u{0153}\u{201E}",
                                                    "\u{00E2}\u{0153}\u{00BB}",
                                                    "\u{00E2}\u{0153}\u{00BC}",
                                                    "\u{00C2}",
                                                    "\u{00C3}",
                                                ], [
                                                    "✓",
                                                    "✓",
                                                    "✓",
                                                    "✓",
                                                    "✓",
                                                    "",
                                                    "",
                                                ], str_replace([
                                                    "\u{00E2}\u{0153}\u{2014}",
                                                    "\u{00E2}\u{0153}\u{02DC}",
                                                ], [
                                                    "✘",
                                                    "✘",
                                                ], $item->identitas_produk_pada_karton))) : '-' }}</td>
                                                <td>{{ ($item->standar_jumlah_karton ?? null) !== null && ($item->standar_jumlah_karton ?? '') !== '' ? $item->standar_jumlah_karton : '-' }} pcs</td>
                                                <td>{{ $item->aktual_jumlah_karton}} pcs</td>
                                                <td>
                                                    @if(($item->dokumentasi_count ?? 0) > 0)
                                                        <button type="button" class="btn btn-secondary btn-sm" title="Dokumentasi sudah diinput" disabled>
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    @else
                                                        <a href="{{ route('dokumentasi.create', ['id_pengemasan_karton' => $item->id, 'shift_id' => $item->shift_id]) }}" class="btn btn-success btn-sm" title="Lanjut ke Dokumentasi">
                                                            <i class="fas fa-arrow-right"></i>
                                                        </a>
                                                    @endif
                                                    <!-- <div class="btn-vertical">
@if(auth()->user()->hasPermissionTo('edit-pengemasan-karton'))      
                                                        <a href="{{ route('pengemasan-karton.edit', $item->uuid) }}" class="btn btn-warning btn-sm">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        @endif
@if(auth()->user()->hasPermissionTo('view-pengemasan-karton'))
<a href="{{ route('pengemasan-karton.logs', $item->uuid) }}" class="btn btn-info btn-sm" title="Lihat Riwayat">
                                                            <i class="fas fa-history"></i>
                                                        </a>
                                                        @endif
@if(auth()->user()->hasPermissionTo('delete-pengemasan-karton'))
<form action="{{ route('pengemasan-karton.destroy', $item->uuid) }}" method="POST" style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
</div> -->
                                                    <x-action-buttons :item="$item" route-prefix="pengemasan-karton" :show-view="false" />
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center">Tidak ada data</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Menampilkan Navigasi Pagination -->
                            <div class="d-flex justify-content-center mt-3">
                                {{ $pengemasanKarton->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
