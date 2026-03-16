@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Berat Produk (Bag & Box)</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item active">Berat Produk</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Filter & Aksi</h3>
                    <div class="card-tools">
                        @if(auth()->user()->hasPermissionTo('create-berat-produk-bag-box'))
                            <a href="{{ route('berat-produk.create') }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus"></i> Tambah Data
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('berat-produk.index') }}" method="GET">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Filter Produk</label>
                                    <select name="produk_id" class="form-control select2">
                                        <option value="">Semua Produk</option>
                                        @foreach($produks as $produk)
                                            <option value="{{ $produk->id }}" {{ request('produk_id') == $produk->id ? 'selected' : '' }}>
                                                {{ $produk->nama_produk }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Cari (Nama/Kode)</label>
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control" placeholder="Nama Produk / Kode Produksi" value="{{ request('search') }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="submit">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <a href="{{ route('berat-produk.index') }}" class="btn btn-default btn-block">Reset</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="custom-tabs-four-bag-tab" data-toggle="tab" href="#custom-tabs-four-bag" role="tab" aria-controls="custom-tabs-four-bag" aria-selected="true">Data Berat Produk (Pack)</a>
                        </li>
                        <li class="nav-item btn-success" role="presentation">
                            <a class="nav-link" id="custom-tabs-four-box-tab" data-toggle="tab" href="#custom-tabs-four-box" role="tab" aria-controls="custom-tabs-four-box" aria-selected="false">Data Berat Produk (Box)</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="custom-tabs-four-tabContent">
                        <div class="tab-pane fade show active table-responsive" id="custom-tabs-four-bag" role="tabpanel" aria-labelledby="custom-tabs-four-bag-tab">
                            <table style="white-space: nowrap;" class="table text-center table-bordered table-striped mt-3">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Shift</th>
                                        <th>Tanggal</th>
                                         <th>Jam</th>
                                        <!-- <th>Plan</th> -->
                                        <th>Nama Produk</th>
                                        <th>Kode Produksi</th>
                                        <th>Line</th>
                                        <th>Nilai Standar Pack</th>
                                        <th>Berat Aktual 1</th>
                                        <th>Berat Aktual 2</th>
                                        <th>Berat Aktual 3</th>
                                        <th>Rata Rata Berat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($berat_produk_bag as $index => $item)
                                        <tr>
                                            <td>{{ $berat_produk_bag->firstItem() + $index }}</td>
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
                                            <!-- <td><span class="badge badge-info">{{ $item->plan->nama_plan }}</span></td> -->
                                            
                                            <td>
                                                {{ optional(optional($item->pengemasanProduk)->produk)->nama_produk ?? '-' }} {{ optional($item->pengemasanPlastik)->berat ?? '-' }} gram
                                            </td>
                                            <td>
                                                {{ optional($item->pengemasanProduk)->kode_produksi ?? '-' }} 
                                            </td>
                                            <td>{{ $item->line }}</td>
                                            <td>{{ ($item->data_bag->std_bag ?? null) !== null && ($item->data_bag->std_bag ?? '') !== '' ? trim(str_replace(['Â±', 'Â'], ['±', ''], $item->data_bag->std_bag)) : '-' }}</td>
                                            <td>{{ $item->berat_aktual_1 }}</td>
                                            <td>{{ $item->berat_aktual_2 }}</td>
                                            <td>{{ $item->berat_aktual_3 }}</td>
                                            <td>{{ $item->rata_rata_berat }}</td>
                                            <td>
                                                @if(($item->berat_produk_box_count ?? 0) > 0)
                                                    <button type="button" class="btn btn-secondary btn-sm" title="Berat Produk Box sudah diinput" disabled>
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @else
                                                    <a href="{{ route('berat-produk.create', ['tab' => 'box', 'id_berat_produk_bag' => $item->id, 'id_shift' => $item->id_shift]) }}" class="btn btn-success btn-sm" title="Lanjut ke Berat Produk Box (C2)">
                                                        <i class="fas fa-arrow-right"></i>
                                                    </a>
                                                @endif

                  
                                                @if(Auth::user()->hasPermissionTo('edit-berat-produk-bag-box') || Auth::user()->hasPermissionTo('delete-berat-produk-bag-box') || Auth::user()->hasPermissionTo('view-berat-produk-bag-box'))
                                                    <div class="btn-vertical" role="group">
                                                        @if(Auth::user()->hasPermissionTo('edit-berat-produk-bag-box'))
                                                        <a href="{{ route('berat-produk.edit_bag', $item->uuid) }}" class="btn btn-warning btn-sm" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        @endif
                                                        
                                                        @if(Auth::user()->hasPermissionTo('view-berat-produk-bag-box'))
                                                        <a href="{{ route('berat-produk.bag-logs', $item->uuid) }}" class="btn btn-secondary btn-sm" title="History">
                                                            <i class="fas fa-history"></i>
                                                        </a>
                                                        @endif
                                                        
                                                        @if(Auth::user()->hasPermissionTo('delete-berat-produk-bag-box'))
                                                        <form action="{{ route('berat-produk.destroy_bag', $item->uuid) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="badge badge-secondary">
                                                        <i class="fas fa-lock"></i> No Access
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-center mt-3">
                                {{ $berat_produk_bag->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                        <div class="tab-pane table-responsive fade" id="custom-tabs-four-box" role="tabpanel" aria-labelledby="custom-tabs-four-box-tab">
                            <table class="text-center table table-bordered table-striped mt-3" style="white-space: nowrap;">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Shift</th>
                                        <th>Tanggal</th>
                                          <th>Jam</th> 
                                        <th>Nama Produk</th>
                                        <th>Kode Produksi</th>
                                        <th>Data Box</th>
                                        <th>Berat Aktual 1</th>
                                        <th>Berat Aktual 2</th>
                                        <th>Berat Aktual 3</th>
                                        <th>Rata-rata Berat Aktual</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($berat_produk_box as $index => $item)
                                        <tr>
                                            <td>{{ $berat_produk_box->firstItem() + $index }}</td>
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
                                            <td> {{ optional(optional($item->pengemasanProduk)->produk)->nama_produk ?? '-' }} {{ optional($item->pengemasanPlastik)->berat ?? '-' }} gram</td>
                                            <td>   {{ optional($item->pengemasanProduk)->kode_produksi ?? '-' }} </td>
                                            <td>{{ ($item->data_box->std_box ?? null) !== null && ($item->data_box->std_box ?? '') !== '' ? trim(str_replace(['Â±', 'Â'], ['±', ''], $item->data_box->std_box)) : '-' }}</td>
                                            <td>{{ $item->berat_aktual_1 }}</td>
                                            <td>{{ $item->berat_aktual_2 }}</td>
                                            <td>{{ $item->berat_aktual_3 }}</td>
                                            <td>{{ $item->rata_rata_berat }}</td>
                                            <td>
                                                @if(($item->pengemasan_karton_count ?? 0) > 0)
                                                    <button type="button" class="btn btn-secondary btn-sm" title="Pengemasan Karton sudah diinput" disabled>
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @else
                                                    <a href="{{ route('pengemasan-karton.create', ['id_berat_produk_box' => $item->id, 'shift_id' => $item->id_shift]) }}" class="btn btn-success btn-sm" title="Lanjut ke Pengemasan Karton">
                                                        <i class="fas fa-arrow-right"></i>
                                                    </a>
                                                @endif

                                                @if(Auth::user()->hasPermissionTo('edit-berat-produk-bag-box') || Auth::user()->hasPermissionTo('delete-berat-produk-bag-box') || Auth::user()->hasPermissionTo('view-berat-produk-bag-box'))
                                                    <div class="btn-vertical" role="group">
                                                        @if(Auth::user()->hasPermissionTo('edit-berat-produk-bag-box'))
                                                        <a href="{{ route('berat-produk.edit_box', $item->uuid) }}" class="btn btn-warning btn-sm" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        @endif
                                                        
                                                        @if(Auth::user()->hasPermissionTo('view-berat-produk-bag-box'))
                                                        <a href="{{ route('berat-produk.box-logs', $item->uuid) }}" class="btn btn-secondary btn-sm" title="History">
                                                            <i class="fas fa-history"></i>
                                                        </a>
                                                        @endif
                                                        
                                                        @if(Auth::user()->hasPermissionTo('delete-berat-produk-bag-box'))
                                                        <form action="{{ route('berat-produk.destroy_box', $item->uuid) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="badge badge-secondary">
                                                        <i class="fas fa-lock"></i> No Access
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-center mt-3">
                                {{ $berat_produk_box->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>
    </section>
</div>
@endsection