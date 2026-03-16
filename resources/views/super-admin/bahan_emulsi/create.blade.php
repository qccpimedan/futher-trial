@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Tambah Bahan Emulsi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('bahan-emulsi.index') }}">Bahan Emulsi</a></li>
                        <li class="breadcrumb-item active">Tambah Data</li>
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
                            <h3 class="card-title">Tambah Data Bahan Emulsi</h3>
                        </div>
                        <form action="{{ route('bahan-emulsi.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="form-group mb-2">
                                    <label>Nama Plan</label>
                                    <select name="id_plan" id="id_plan_select" class="form-control" required>
                                        <option value="">Pilih Plan</option>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}">{{ $plan->nama_plan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-2">
                                    <label>Nama Produk</label>
                                    <select name="id_produk" id="id_produk_select" class="form-control" required>
                                        <option value="">Pilih Produk</option>
                                        @foreach($produks as $produk)
                                            <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-2">
                                    <label>Nama Emulsi</label>
                                    <select name="nama_emulsi_id" id="nama_emulsi_id" class="form-control" required>
                                        <option value="">Pilih Emulsi</option>
                                        @foreach($emulsis as $emulsi)
                                            <option value="{{ $emulsi->id }}">{{ $emulsi->nama_emulsi }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-2">
                                    <label>Total Pemakaian</label>
                                    <select name="total_pemakaian_id" id="total_pemakaian_id" class="form-control" required>
                                        <option value="">Pilih Total Pemakaian</option>
                                        {{-- Akan diisi otomatis --}}
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Nomor Proses Emulsi</label>
                                    <select name="nomor_emulsi_id" id="nomor_emulsi_id" class="form-control" required>
                                        <option value="">Pilih Nomor Emulsi</option>
                                        {{-- Akan diisi otomatis --}}
                                    </select>
                                </div>
                                <!-- Dynamic Form untuk Nama RM dan Berat RM -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title">Data Bahan Raw Material</h5>
                                        <button type="button" class="btn btn-success btn-sm float-right" id="addRM">
                                            <i class="fas fa-plus"></i> Tambah RM
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div id="rm-container">
                                            <div class="rm-item mb-3" data-index="0">
                                                <div class="row">
                                                    <div class="col-md-5">
                                                        <label>Nama RM</label>
                                                        <input type="text" name="nama_rm[]" class="form-control" required>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <label>Berat RM (kg)</label>
                                                        <input type="text" name="berat_rm[]" class="form-control" placeholder="000" required>                                                    </div>
                                                    <div class="col-md-2">
                                                        <label>&nbsp;</label>
                                                        <button type="button" class="btn btn-danger btn-block remove-rm" disabled>
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Data</button>
                                <a href="{{ route('bahan-emulsi.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let rmIndex = 1;
    
    // Tambah RM baru
    document.getElementById('addRM').addEventListener('click', function() {
        const container = document.getElementById('rm-container');
        const newRM = `
            <div class="rm-item mb-3" data-index="${rmIndex}">
                <div class="row">
                    <div class="col-md-5">
                        <label>Nama RM</label>
                        <input type="text" name="nama_rm[]" class="form-control" required>
                    </div>
                    <div class="col-md-5">
                        <label>Berat RM (kg)</label>
                        <input type="number" step="0.01" name="berat_rm[]" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-block remove-rm">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newRM);
        rmIndex++;
        updateRemoveButtons();
    });
    
    // Hapus RM
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-rm') || e.target.closest('.remove-rm')) {
            const rmItem = e.target.closest('.rm-item');
            rmItem.remove();
            updateRemoveButtons();
        }
    });
    
    // Update status tombol hapus
    function updateRemoveButtons() {
        const rmItems = document.querySelectorAll('.rm-item');
        const removeButtons = document.querySelectorAll('.remove-rm');
        
        removeButtons.forEach(button => {
            button.disabled = rmItems.length <= 1;
        });
    }
});
</script>
@endsection