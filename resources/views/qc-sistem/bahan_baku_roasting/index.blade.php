@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Bahan Baku Roasting</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Bahan Baku Roasting</li>
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
                            <h3 class="card-title">Data Bahan Baku Roasting</h3>
                            <div class="card-tools">
                                @if(auth()->user()->hasPermissionTo('create-bahan-baku-roasting'))
                                <!--
                                    @if(auth()->user()->hasPermissionTo('create-bahan-baku-roasting')) <a href="{{ route('bahan-baku-roasting.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Tambah Data
                                </a> @endif
-->
                                @endif
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
                                <table id="myTable" class="table text-center table-bordered table-striped" style="white-space: nowrap;">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Shift</th>
                                            <th>Tanggal</th>
                                            <!-- <th>Plan</th> -->
                                            <th>Nama Produk</th>
                                            <th>Kode Produksi RM</th>
                                            <th>Standar Suhu RM</th>
                                            <th>Aktual Suhu RM</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($bahanBakuRoasting as $index => $item)
                                            <tr>
                                                <td>{{ $bahanBakuRoasting->firstItem() + $index }}</td>
                                                <td>
                                                @if($item->shift_data && $item->shift_data->shift == 1)
                                                    <span class="badge bg-primary">Shift 1</span>
                                                @elseif($item->shift_data && $item->shift_data->shift == 2)
                                                    <span class="badge bg-success">Shift 2</span>
                                                @elseif($item->shift_data && $item->shift_data->shift == 3)
                                                    <span class="badge bg-secondary">Shift 3</span>
                                                @else
                                                    <span class="badge bg-info">{{ $item->shift_data->shift ?? '-' }}</span>
                                                @endif
                                                </td>
                                                <td><span class="badge bg-secondary">{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y H:i:s') : '-' }}</span></td> 
                                                <td>{{ $item->produk->nama_produk ?? '-' }}
                                                    @if($item->inputRoasting && $item->inputRoasting->berat_produk)
                                                        ({{ $item->inputRoasting->berat_produk }}gram)
                                                    @endif
                                                </td>
                                                <td>{{ $item->kode_produksi_rm }}</td>
                                                <td>{{ $item->standart_suhu_rm }}</td>
                                                <td>{{ $item->aktual_suhu_rm }}</td>
                                                <td>
                                                    <!-- <div class="btn-vertical">
@if(auth()->user()->hasPermissionTo('edit-bahan-baku-roasting'))
                                                        <a href="{{ route('bahan-baku-roasting.edit', $item->uuid) }}" class="btn btn-warning btn-sm">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        @endif
@if(auth()->user()->hasPermissionTo('view-bahan-baku-roasting'))
<a href="{{ route('bahan-baku-roasting.logs', $item->uuid) }}" class="btn btn-info btn-sm" title="Lihat Riwayat Perubahan">
                                                            <i class="fas fa-history"></i>
                                                        </a>
                                                        @endif
@if(auth()->user()->hasPermissionTo('delete-bahan-baku-roasting'))
<form action="{{ route('bahan-baku-roasting.destroy', $item->uuid) }}" method="POST" style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
</div> -->
                                                    @php
                                                        $queryParams = [
                                                            'bahan_baku_roasting_uuid' => $item->uuid,
                                                            'input_roasting_uuid' => $item->input_roasting_uuid
                                                        ];
                                                    @endphp
                                                    @if($item->prosesRoastingFanCount == 0)
                                                        <a href="{{ route('proses-roasting-fan.create', $queryParams) }}" 
                                                           class="btn btn-success btn-sm" title="Lanjut ke Proses Roasting Fan">
                                                            <i class="fas fa-arrow-right"></i> Lanjut Proses Roasting Fan
                                                        </a>
                                                    @else
                                                        <span class="btn btn-success btn-sm disabled" 
                                                              title="Berhasil Input">
                                                            <i class="fas fa-thumbs-up"></i>
                                                        </span>
                                                    @endif
                                                    <x-action-buttons :item="$item" route-prefix="bahan-baku-roasting" :show-view="false" />
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
                            
                            <!-- Pagination -->
                            <div class="d-flex justify-content-center">
                                {{ $bahanBakuRoasting->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
