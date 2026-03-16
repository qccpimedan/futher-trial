@php use Carbon\Carbon; @endphp

@if(isset($chillroom) && count($chillroom))
<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover text-center" style="white-space: nowrap;">
        <thead class="thead-light">
            <tr>
                <th>No</th>
                <th>Shift</th>
                <th>Jam Kedatangan</th>
                <th>Tanggal</th>
                <th>Nama RM</th>
                <th>Kode Produksi</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            @foreach($chillroom as $i => $item)
            <tr>
                <td>{{ $chillroom->firstItem() + $i }}</td>
                <td>
                    @if($item->datashift->shift == 1)
                        <span class="badge badge-primary">Shift {{ $item->datashift->shift }}</span>
                    @elseif($item->datashift->shift == 2)
                        <span class="badge badge-success">Shift {{ $item->datashift->shift }}</span>
                    @else
                        <span class="badge badge-secondary">Shift {{ $item->datashift->shift }}</span>
                    @endif
                </td>
                <td>
                    <span class="badge badge-info">{{ $item->jam_kedatangan ? \Carbon\Carbon::parse($item->jam_kedatangan)->format('H:i') : '-' }}</span>
                </td>
                <td>
                    <span class="badge badge-secondary">{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y H:i:s') : '-' }}</span>
                </td>
                <td class="font-weight-medium">{{ $item->nama_rm }}</td>
                <td>{{ $item->kode_produksi }}</td>

                <td class="text-center">
                    <div class="btn-group" role="group">
                        @if(auth()->user()->hasPermissionTo('view-chillroom'))
                        <button type="button" 
                                class="btn btn-sm btn-primary btn-show-detail" 
                                data-id="{{ $item->uuid }}"
                                title="Lihat Detail">
                            <i class="fas fa-eye"></i>
                        </button>
                        @endif
                        @if(auth()->user()->hasPermissionTo('edit-chillroom'))
                        <a href="{{ route('chillroom.edit', $item->uuid) }}" 
                           class="btn btn-sm btn-warning" 
                           title="Edit Data">
                            <i class="fas fa-edit"></i>
                        </a>
                        @endif
                        @if(auth()->user()->hasPermissionTo('view-chillroom'))
                        <a href="{{ route('chillroom.logs', $item->uuid) }}" 
                           class="btn btn-sm btn-info" 
                           title="Lihat Log">
                            <i class="fas fa-history"></i>
                        </a>
                        @endif
                        @if(auth()->user()->hasPermissionTo('delete-chillroom'))
                        <form action="{{ route('chillroom.destroy', $item->uuid) }}" 
                              method="POST" 
                              class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="btn btn-sm btn-danger" 
                                    title="Hapus Data"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            
            <!-- Hidden data for modal (stored in data attributes) -->
            <div class="d-none" id="data-{{ $item->uuid }}">
                <div data-shift="{{ $item->datashift->shift }}"></div>
                <div data-jam-kedatangan="{{ $item->jam_kedatangan ? \Carbon\Carbon::parse($item->jam_kedatangan)->format('H:i') : '-' }}"></div>
                <div data-tanggal="{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y H:i:s') : '-' }}"></div>
                <div data-nama-rm="{{ $item->nama_rm }}"></div>
                <div data-kode-produksi="{{ $item->kode_produksi }}"></div>
                <div data-berat="{{ $item->berat ?? '-' }}"></div>
                <div data-suhu="{{ $item->suhu ? $item->suhu . '°C' : '-' }}"></div>
                <div data-sensori="{{ $item->sensori ?? '-' }}"></div>
                <div data-kemasan="{{ $item->kemasan ?? '-' }}"></div>
                <div data-keterangan="{{ $item->keterangan ?? '-' }}"></div>
                <div data-standar-berat="{{ $item->standar_berat ?? '-' }}"></div>
                <div data-status-rm="{{ $item->status_rm ?? '-' }}"></div>
                <div data-catatan-rm="{{ $item->catatan_rm ?? '-' }}"></div>
                <div data-approved-qc="{{ $item->approved_by_qc ? '1' : '0' }}"></div>
                <div data-approved-produksi="{{ $item->approved_by_produksi ? '1' : '0' }}"></div>
                <div data-approved-spv="{{ $item->approved_by_spv ? '1' : '0' }}"></div>
                
                @php
                    $nilaiJumlahRm = json_decode($item->nilai_jumlah_rm, true);
                    $beratSamples = [];
                    
                    if ($nilaiJumlahRm) {
                        foreach ($nilaiJumlahRm as $entry) {
                            if (is_array($entry)) {
                                $beratSamples = $entry;
                                break;
                            }
                        }
                    }
                    
                    $jumlahRmData = json_decode($item->jumlah_rm, true);
                    $agregasiData = [
                        'berat_atas' => 0,
                        'berat_std' => 0,
                        'berat_bawah' => 0
                    ];
                    
                    if ($jumlahRmData) {
                        foreach ($jumlahRmData as $entry) {
                            if (is_array($entry)) {
                                $agregasiData['berat_atas'] = $entry['berat_atas'] ?? 0;
                                $agregasiData['berat_std'] = $entry['berat_std'] ?? 0;
                                $agregasiData['berat_bawah'] = $entry['berat_bawah'] ?? 0;
                                break;
                            }
                        }
                    }
                    
                    $total = $agregasiData['berat_atas'] + $agregasiData['berat_std'] + $agregasiData['berat_bawah'];
                    $persenAtas = $total > 0 ? ($agregasiData['berat_atas'] / $total) * 100 : 0;
                    $persenStd = $total > 0 ? ($agregasiData['berat_std'] / $total) * 100 : 0;
                    $persenBawah = $total > 0 ? ($agregasiData['berat_bawah'] / $total) * 100 : 0;
                @endphp
                
                <div data-berat-samples="{{ json_encode($beratSamples) }}"></div>
                <div data-agregasi="{{ json_encode($agregasiData) }}"></div>
                <div data-total="{{ $total }}"></div>
                <div data-persen-atas="{{ number_format($persenAtas, 2) }}"></div>
                <div data-persen-std="{{ number_format($persenStd, 2) }}"></div>
                <div data-persen-bawah="{{ number_format($persenBawah, 2) }}"></div>
                <div data-user-role="{{ auth()->user()->id_role ?? null }}"></div>
                <div data-uuid="{{ $item->uuid }}"></div>
            </div>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Detail Data -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="detailModalLabel">
                    <i class="fas fa-info-circle"></i> Detail Data Chillroom
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Informasi Dasar -->
                <div class="card card-outline card-primary mb-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0"><i class="fas fa-clipboard-list"></i> Pemeriksaan Kedatangan Bahan Baku</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Shift:</strong>
                                <p id="detail-shift" class="mb-2">-</p>
                            </div>
                            <div class="col-md-3">
                                <strong>Jam Kedatangan:</strong>
                                <p id="detail-jam-kedatangan" class="mb-2">-</p>
                            </div>
                            <div class="col-md-3">
                                <strong>Nama RM:</strong>
                                <p id="detail-nama-rm" class="mb-2 font-weight-bold">-</p>
                            </div>
                            <div class="col-md-3">
                                <strong>Kode Produksi:</strong>
                                <p id="detail-kode-produksi" class="mb-2">-</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Tanggal:</strong>
                                <p id="detail-tanggal" class="mb-2">-</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Berat Perkemasan:</strong>
                                <p id="detail-berat" class="mb-2">-</p>
                            </div>
                            <div class="col-md-3">
                                <strong>Suhu:</strong>
                                <p id="detail-suhu" class="mb-2">-</p>
                            </div>
                            <div class="col-md-3">
                                <strong>Sensori:</strong>
                                <p id="detail-sensori" class="mb-2">-</p>
                            </div>
                            <div class="col-md-3">
                                <strong>Kemasan:</strong>
                                <p id="detail-kemasan" class="mb-2">-</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Keterangan:</strong>
                                <p id="detail-keterangan" class="mb-2">-</p>
                            </div>
                            <div class="col-md-3">
                                <strong>Status:</strong>
                                <p id="detail-status" class="mb-2">-</p>
                            </div>
                            <div class="col-md-3">
                                <strong>Catatan:</strong>
                                <p id="detail-catatan" class="mb-2">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sampling Berat RM Daging SPO -->
                <div class="card card-outline card-warning mb-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0"><i class="fas fa-weight"></i> Sampling Berat RM Daging SPO</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <strong>Standar Berat Per PCS:</strong>
                                <p id="detail-standar-berat" class="mb-2">-</p>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <strong>Hasil Aktual Berat Per PCS:</strong>
                                <div id="detail-berat-samples" class="mt-2">-</div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <strong>Jumlah yang Disampling:</strong>
                                <div id="detail-agregasi" class="mt-2">-</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Persetujuan -->
                <div class="card card-outline card-info mb-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0"><i class="fas fa-check-circle"></i> Status Persetujuan</h6>
                    </div>
                    <div class="card-body">
                        <div id="detail-approval-status" class="row text-center">
                            <!-- Will be populated by JavaScript -->
                        </div>
                        <div id="detail-approval-buttons" class="mt-3">
                            <!-- Approval buttons will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Rekapitulasi Persentase (tetap sama) -->
<div class="modal fade" id="rekapitulasiModal" tabindex="-1" role="dialog" aria-labelledby="rekapitulasiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="rekapitulasiModalLabel">
                    <i class="fas fa-chart-pie"></i> Rekapitulasi Persentase Sampling Berat
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Informasi Produk -->
                <div class="card card-outline card-primary mb-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0"><i class="fas fa-box"></i> Informasi Produk</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Nama RM:</strong>
                                <p id="modal-nama-rm" class="mb-1">-</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Kode Produksi:</strong>
                                <p id="modal-kode-produksi" class="mb-1">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Jumlah Sampling -->
                <div class="card card-outline card-secondary mb-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0"><i class="fas fa-weight"></i> Data Jumlah Sampling</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="info-box bg-success">
                                    <span class="info-box-icon"><i class="fas fa-arrow-up"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Di Atas Standar</span>
                                        <span class="info-box-number" id="modal-jumlah-atas">0</span>
                                        <span class="info-box-text">pcs</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-primary">
                                    <span class="info-box-icon"><i class="fas fa-equals"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Sesuai Standar</span>
                                        <span class="info-box-number" id="modal-jumlah-std">0</span>
                                        <span class="info-box-text">pcs</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-warning">
                                    <span class="info-box-icon"><i class="fas fa-arrow-down"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Di Bawah Standar</span>
                                        <span class="info-box-number" id="modal-jumlah-bawah">0</span>
                                        <span class="info-box-text">pcs</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-dark">
                                    <span class="info-box-icon"><i class="fas fa-calculator"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Sampling</span>
                                        <span class="info-box-number" id="modal-total">0</span>
                                        <span class="info-box-text">pcs</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Persentase dengan Progress Bar -->
                <div class="card card-outline card-info mb-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0"><i class="fas fa-percentage"></i> Persentase Distribusi</h6>
                    </div>
                    <div class="card-body">
                        <!-- Di Atas Standar -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="font-weight-bold text-success">
                                    <i class="fas fa-arrow-up"></i> Di Atas Standar
                                </span>
                                <span class="font-weight-bold text-success">
                                    <span id="modal-persen-atas">0</span>%
                                </span>
                            </div>
                            <div class="progress" style="height: 25px;">
                                <div id="progress-atas" class="progress-bar bg-success progress-bar-striped progress-bar-animated" 
                                     role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                    <span id="modal-persen-atas-bar"></span>%
                                </div>
                            </div>
                        </div>

                        <!-- Sesuai Standar -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="font-weight-bold text-primary">
                                    <i class="fas fa-equals"></i> Sesuai Standar
                                </span>
                                <span class="font-weight-bold text-primary">
                                    <span id="modal-persen-std">0</span>%
                                </span>
                            </div>
                            <div class="progress" style="height: 25px;">
                                <div id="progress-std" class="progress-bar bg-primary progress-bar-striped progress-bar-animated" 
                                     role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                    <span id="modal-persen-std-bar"></span>%
                                </div>
                            </div>
                        </div>

                        <!-- Di Bawah Standar -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="font-weight-bold text-warning">
                                    <i class="fas fa-arrow-down"></i> Di Bawah Standar
                                </span>
                                <span class="font-weight-bold text-warning">
                                    <span id="modal-persen-bawah">0</span>%
                                </span>
                            </div>
                            <div class="progress" style="height: 25px;">
                                <div id="progress-bawah" class="progress-bar bg-warning progress-bar-striped progress-bar-animated" 
                                     role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                    <span id="modal-persen-bawah-bar"></span>%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Interpretasi Hasil -->
                <div id="modal-interpretasi"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- CSS -->
<style>
.btn-group .btn {
    margin: 0 2px;
}

.badge {
    font-weight: 500;
    padding: 0.35em 0.65em;
}

.table td {
    vertical-align: middle;
}

.info-box {
    min-height: 80px;
    margin-bottom: 15px;
}

.info-box-icon {
    font-size: 2rem;
}

.info-box-number {
    font-size: 1.5rem;
    font-weight: bold;
}

.progress {
    border-radius: 0.5rem;
    box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
}

.progress-bar {
    font-size: 0.9rem;
    font-weight: bold;
    line-height: 25px;
    transition: width 0.6s ease;
}

.btn-rekapitulasi {
    white-space: nowrap;
}

.btn-rekapitulasi:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
}

.approval-status-box {
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 10px;
}

@media print {
    .modal-header,
    .modal-footer {
        display: none !important;
    }
    
    .modal-body {
        padding: 0 !important;
    }
}
</style>

<!-- JavaScript -->
@push('scripts')
<script>
$(document).ready(function() {
    // Handle Show Detail Button
    $('.btn-show-detail').click(function() {
        const id = $(this).data('id');
        const dataContainer = $('#data-' + id);
        
        if (dataContainer.length === 0) {
            alert('Data tidak ditemukan');
            return;
        }
        
        // Get all data from hidden container
        const shift = dataContainer.find('[data-shift]').data('shift');
        const jamKedatangan = dataContainer.find('[data-jam-kedatangan]').data('jam-kedatangan');
        const tanggal = dataContainer.find('[data-tanggal]').data('tanggal');
        const namaRm = dataContainer.find('[data-nama-rm]').data('nama-rm');
        const kodeProduksi = dataContainer.find('[data-kode-produksi]').data('kode-produksi');
        const berat = dataContainer.find('[data-berat]').data('berat');
        const suhu = dataContainer.find('[data-suhu]').data('suhu');
        const sensori = dataContainer.find('[data-sensori]').data('sensori');
        const kemasan = dataContainer.find('[data-kemasan]').data('kemasan');
        const keterangan = dataContainer.find('[data-keterangan]').data('keterangan');
        const standarBerat = dataContainer.find('[data-standar-berat]').data('standar-berat');
        const statusRm = dataContainer.find('[data-status-rm]').data('status-rm');
        const catatanRm = dataContainer.find('[data-catatan-rm]').data('catatan-rm');
        const beratSamples = dataContainer.find('[data-berat-samples]').data('berat-samples');
        const agregasi = dataContainer.find('[data-agregasi]').data('agregasi');
        const total = dataContainer.find('[data-total]').data('total');
        const persenAtas = dataContainer.find('[data-persen-atas]').data('persen-atas');
        const persenStd = dataContainer.find('[data-persen-std]').data('persen-std');
        const persenBawah = dataContainer.find('[data-persen-bawah]').data('persen-bawah');
        const approvedQc = dataContainer.find('[data-approved-qc]').data('approved-qc');
        const approvedProduksi = dataContainer.find('[data-approved-produksi]').data('approved-produksi');
        const approvedSpv = dataContainer.find('[data-approved-spv]').data('approved-spv');
        const userRole = dataContainer.find('[data-user-role]').data('user-role');
        const uuid = dataContainer.find('[data-uuid]').data('uuid');
        
        // Populate basic information
        $('#detail-shift').html(getShiftBadge(shift));
        $('#detail-jam-kedatangan').html('<span class="badge badge-info">' + jamKedatangan + '</span>');
        $('#detail-tanggal').html('<span class="badge badge-secondary">' + tanggal + '</span>');
        $('#detail-nama-rm').text(namaRm);
        $('#detail-kode-produksi').text(kodeProduksi);
        $('#detail-berat').text(berat);
        $('#detail-suhu').text(suhu);
        $('#detail-sensori').text(sensori);
        $('#detail-kemasan').text(kemasan);
        $('#detail-keterangan').text(keterangan);
        $('#detail-standar-berat').text(standarBerat);
        
        // Status
        let statusBadge = '-';
        if (statusRm === 'diterima') {
            statusBadge = '<span class="badge badge-success">Diterima</span>';
        } else if (statusRm === 'diretur') {
            statusBadge = '<span class="badge badge-danger">Diretur</span>';
        }
        $('#detail-status').html(statusBadge);
        $('#detail-catatan').text(catatanRm);
        
        // Populate berat samples
        let samplesHtml = '';
        if (beratSamples && Array.isArray(beratSamples) && beratSamples.length > 0) {
            samplesHtml = '<div class="d-flex flex-wrap" style="gap: 5px;">';
            beratSamples.forEach(function(sample, index) {
                if (sample && !isNaN(sample)) {
                    samplesHtml += '<span class="badge badge-primary">Sample ' + (index + 1) + ': ' + parseFloat(sample).toFixed(2) + ' gr</span>';
                }
            });
            samplesHtml += '</div>';
        } else {
            samplesHtml = '<span class="text-muted">-</span>';
        }
        $('#detail-berat-samples').html(samplesHtml);
        
        // Populate agregasi data
        let agregasiHtml = '';
        if (agregasi && (agregasi.berat_atas > 0 || agregasi.berat_std > 0 || agregasi.berat_bawah > 0)) {
            agregasiHtml = '<div class="text-left">';
            if (agregasi.berat_atas > 0) {
                agregasiHtml += '<div class="mb-1"><span class="badge badge-success">▲ Atas: ' + agregasi.berat_atas + ' pcs</span></div>';
            }
            if (agregasi.berat_std > 0) {
                agregasiHtml += '<div class="mb-1"><span class="badge badge-primary">● Std: ' + agregasi.berat_std + ' pcs</span></div>';
            }
            if (agregasi.berat_bawah > 0) {
                agregasiHtml += '<div class="mb-1"><span class="badge badge-warning">▼ Bawah: ' + agregasi.berat_bawah + ' pcs</span></div>';
            }
            agregasiHtml += '<small class="text-muted d-block mt-2">Total: ' + total + ' pcs</small>';
            agregasiHtml += '<button type="button" class="btn btn-sm btn-info mt-2 btn-rekapitulasi-detail" ' +
                'data-atas="' + agregasi.berat_atas + '" ' +
                'data-std="' + agregasi.berat_std + '" ' +
                'data-bawah="' + agregasi.berat_bawah + '" ' +
                'data-total="' + total + '" ' +
                'data-persen-atas="' + persenAtas + '" ' +
                'data-persen-std="' + persenStd + '" ' +
                'data-persen-bawah="' + persenBawah + '" ' +
                'data-nama-rm="' + namaRm + '" ' +
                'data-kode-produksi="' + kodeProduksi + '">' +
                '<i class="fas fa-chart-pie"></i> Lihat Persentase</button>';
            agregasiHtml += '</div>';
        } else {
            agregasiHtml = '<span class="text-muted">-</span>';
        }
        $('#detail-agregasi').html(agregasiHtml);
        
        // Populate approval status
        let approvalStatusHtml = '';
        approvalStatusHtml += '<div class="col-md-4">';
        approvalStatusHtml += '<div class="approval-status-box ' + (approvedQc == 1 ? 'bg-success' : 'bg-secondary') + ' text-white text-center">';
        approvalStatusHtml += '<i class="fas ' + (approvedQc == 1 ? 'fa-check-circle' : 'fa-clock') + ' fa-2x mb-2"></i>';
        approvalStatusHtml += '<h6>QC</h6>';
        approvalStatusHtml += '<p class="mb-0">' + (approvedQc == 1 ? 'Disetujui' : 'Menunggu') + '</p>';
        approvalStatusHtml += '</div></div>';
        
        approvalStatusHtml += '<div class="col-md-4">';
        approvalStatusHtml += '<div class="approval-status-box ' + (approvedProduksi == 1 ? 'bg-primary' : 'bg-secondary') + ' text-white text-center">';
        approvalStatusHtml += '<i class="fas ' + (approvedProduksi == 1 ? 'fa-check-circle' : 'fa-clock') + ' fa-2x mb-2"></i>';
        approvalStatusHtml += '<h6>FM/FL PRODUKSI</h6>';
        approvalStatusHtml += '<p class="mb-0">' + (approvedProduksi == 1 ? 'Disetujui' : 'Menunggu') + '</p>';
        approvalStatusHtml += '</div></div>';
        
        approvalStatusHtml += '<div class="col-md-4">';
        approvalStatusHtml += '<div class="approval-status-box ' + (approvedSpv == 1 ? 'bg-dark' : 'bg-secondary') + ' text-white text-center">';
        approvalStatusHtml += '<i class="fas ' + (approvedSpv == 1 ? 'fa-check-circle' : 'fa-clock') + ' fa-2x mb-2"></i>';
        approvalStatusHtml += '<h6>SPV</h6>';
        approvalStatusHtml += '<p class="mb-0">' + (approvedSpv == 1 ? 'Disetujui' : 'Menunggu') + '</p>';
        approvalStatusHtml += '</div></div>';
        
        $('#detail-approval-status').html(approvalStatusHtml);
        
        // Populate approval buttons based on role
        let approvalButtonsHtml = '<div class="btn-group-vertical w-100" role="group">';
        
        if (userRole == 1 || userRole == 5) {
            // Role 1 dan 5: Tampilkan semua tombol dengan QC yang bisa diklik
            approvalButtonsHtml += '<button type="button" class="btn btn-sm ' + (approvedQc == 1 ? 'btn-success' : 'btn-outline-success') + ' approve-btn-detail mb-2" ' +
                'data-id="' + uuid + '" data-type="qc" ' + (approvedQc == 1 ? 'disabled' : '') + '>' +
                '<i class="fas ' + (approvedQc == 1 ? 'fa-check-circle' : 'fa-check') + '"></i> ' + (approvedQc == 1 ? 'Sudah Disetujui QC' : 'Setujui sebagai QC') + '</button>';
            approvalButtonsHtml += '<button type="button" class="btn btn-sm ' + (approvedProduksi == 1 ? 'btn-primary' : 'btn-secondary') + ' mb-2" disabled>' +
                '<i class="fas ' + (approvedProduksi == 1 ? 'fa-check-circle' : 'fa-clock') + '"></i> ' + (approvedProduksi == 1 ? 'Sudah Disetujui Produksi' : 'Menunggu Persetujuan Produksi') + '</button>';
            approvalButtonsHtml += '<button type="button" class="btn btn-sm ' + (approvedSpv == 1 ? 'btn-dark' : 'btn-secondary') + '" disabled>' +
                '<i class="fas ' + (approvedSpv == 1 ? 'fa-check-circle' : 'fa-clock') + '"></i> ' + (approvedSpv == 1 ? 'Sudah Disetujui SPV' : 'Menunggu Persetujuan SPV') + '</button>';
        } else if (userRole == 2) {
            // Role 2: Hanya tampilkan tombol Produksi
            approvalButtonsHtml += '<button type="button" class="btn btn-sm ' + (approvedProduksi == 1 ? 'btn-primary' : (approvedQc == 1 ? 'btn-outline-primary' : 'btn-secondary')) + ' ' + (approvedQc == 1 && approvedProduksi == 0 ? 'approve-btn-detail' : '') + '" ' +
                'data-id="' + uuid + '" data-type="produksi" ' + (approvedQc == 0 || approvedProduksi == 1 ? 'disabled' : '') + '>' +
                '<i class="fas ' + (approvedProduksi == 1 ? 'fa-check-circle' : (approvedQc == 0 ? 'fa-clock' : 'fa-check')) + '"></i> ' + 
                (approvedQc == 0 ? 'Menunggu Persetujuan QC' : (approvedProduksi == 1 ? 'Sudah Disetujui Produksi' : 'Setujui sebagai Produksi')) + '</button>';
        } else if (userRole == 3) {
            // Role 3: Hanya tampilkan tombol QC
            approvalButtonsHtml += '<button type="button" class="btn btn-sm ' + (approvedQc == 1 ? 'btn-success' : 'btn-outline-success') + ' approve-btn-detail" ' +
                'data-id="' + uuid + '" data-type="qc" ' + (approvedQc == 1 ? 'disabled' : '') + '>' +
                '<i class="fas ' + (approvedQc == 1 ? 'fa-check-circle' : 'fa-check') + '"></i> ' + (approvedQc == 1 ? 'Sudah Disetujui QC' : 'Setujui sebagai QC') + '</button>';
        } else if (userRole == 4) {
            // Role 4: Hanya tampilkan tombol SPV
            approvalButtonsHtml += '<button type="button" class="btn btn-sm ' + (approvedSpv == 1 ? 'btn-dark' : (approvedProduksi == 1 ? 'btn-outline-dark' : 'btn-secondary')) + ' ' + (approvedProduksi == 1 && approvedSpv == 0 ? 'approve-btn-detail' : '') + '" ' +
                'data-id="' + uuid + '" data-type="spv" ' + (approvedProduksi == 0 || approvedSpv == 1 ? 'disabled' : '') + '>' +
                '<i class="fas ' + (approvedSpv == 1 ? 'fa-check-circle' : (approvedProduksi == 0 ? 'fa-clock' : 'fa-check')) + '"></i> ' + 
                (approvedProduksi == 0 ? 'Menunggu Persetujuan Produksi' : (approvedSpv == 1 ? 'Sudah Disetujui SPV' : 'Setujui sebagai SPV')) + '</button>';
        } else {
            // Role lain: Tampilkan semua tombol sebagai read-only
            approvalButtonsHtml += '<button type="button" class="btn btn-sm ' + (approvedQc == 1 ? 'btn-success' : 'btn-secondary') + ' mb-2" disabled>' +
                '<i class="fas ' + (approvedQc == 1 ? 'fa-check-circle' : 'fa-clock') + '"></i> ' + (approvedQc == 1 ? 'Sudah Disetujui QC' : 'Menunggu Persetujuan QC') + '</button>';
            approvalButtonsHtml += '<button type="button" class="btn btn-sm ' + (approvedProduksi == 1 ? 'btn-primary' : 'btn-secondary') + ' mb-2" disabled>' +
                '<i class="fas ' + (approvedProduksi == 1 ? 'fa-check-circle' : 'fa-clock') + '"></i> ' + (approvedProduksi == 1 ? 'Sudah Disetujui Produksi' : 'Menunggu Persetujuan Produksi') + '</button>';
            approvalButtonsHtml += '<button type="button" class="btn btn-sm ' + (approvedSpv == 1 ? 'btn-dark' : 'btn-secondary') + '" disabled>' +
                '<i class="fas ' + (approvedSpv == 1 ? 'fa-check-circle' : 'fa-clock') + '"></i> ' + (approvedSpv == 1 ? 'Sudah Disetujui SPV' : 'Menunggu Persetujuan SPV') + '</button>';
        }
        
        approvalButtonsHtml += '</div>';
        $('#detail-approval-buttons').html(approvalButtonsHtml);
        
        // Show modal
        $('#detailModal').modal('show');
    });
    
    // Handle approval button in detail modal
    $(document).on('click', '.approve-btn-detail', function() {
        const id = $(this).data('id');
        const type = $(this).data('type');
        const button = $(this);
        
        const typeNames = {
            'qc': 'QC',
            'produksi': 'Produksi', 
            'spv': 'SPV'
        };
        
        if (confirm(`Apakah Anda yakin ingin menyetujui data ini sebagai ${typeNames[type]}?`)) {
            button.prop('disabled', true);
            button.html('<i class="fas fa-spinner fa-spin"></i> Processing...');
            
            $.ajax({
                url: '{{ route("chillroom.approve", ":id") }}'.replace(':id', id),
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    type: type
                },
                success: function(response) {
                    if (response.success) {
                        button.removeClass('btn-outline-success btn-outline-primary btn-outline-dark')
                              .addClass('btn-success')
                              .html('<i class="fas fa-check-circle"></i> Approved');
                        
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        alert('Gagal menyetujui data: ' + response.message);
                        button.prop('disabled', false);
                        const originalText = {
                            'qc': '<i class="fas fa-check"></i> Setujui sebagai QC',
                            'produksi': '<i class="fas fa-check"></i> Setujui sebagai Produksi',
                            'spv': '<i class="fas fa-check"></i> Setujui sebagai SPV'
                        };
                        button.html(originalText[type]);
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Terjadi kesalahan saat menyetujui data';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                    button.prop('disabled', false);
                    const originalText = {
                        'qc': '<i class="fas fa-check"></i> Setujui sebagai QC',
                        'produksi': '<i class="fas fa-check"></i> Setujui sebagai Produksi',
                        'spv': '<i class="fas fa-check"></i> Setujui sebagai SPV'
                    };
                    button.html(originalText[type]);
                }
            });
        }
    });
    
    // Handle button rekapitulasi from detail modal
    $(document).on('click', '.btn-rekapitulasi-detail', function() {
        const atas = parseInt($(this).data('atas'));
        const std = parseInt($(this).data('std'));
        const bawah = parseInt($(this).data('bawah'));
        const total = parseInt($(this).data('total'));
        const persenAtas = parseFloat($(this).data('persen-atas'));
        const persenStd = parseFloat($(this).data('persen-std'));
        const persenBawah = parseFloat($(this).data('persen-bawah'));
        const namaRm = $(this).data('nama-rm');
        const kodeProduksi = $(this).data('kode-produksi');

        // Populate modal dengan data
        $('#modal-nama-rm').text(namaRm);
        $('#modal-kode-produksi').text(kodeProduksi);
        
        $('#modal-jumlah-atas').text(atas);
        $('#modal-jumlah-std').text(std);
        $('#modal-jumlah-bawah').text(bawah);
        $('#modal-total').text(total);
        
        $('#modal-persen-atas').text(persenAtas);
        $('#modal-persen-std').text(persenStd);
        $('#modal-persen-bawah').text(persenBawah);
        
        $('#modal-persen-atas-bar').text(persenAtas);
        $('#modal-persen-std-bar').text(persenStd);
        $('#modal-persen-bawah-bar').text(persenBawah);
        
        setTimeout(function() {
            $('#progress-atas').css('width', persenAtas + '%').attr('aria-valuenow', persenAtas);
            $('#progress-std').css('width', persenStd + '%').attr('aria-valuenow', persenStd);
            $('#progress-bawah').css('width', persenBawah + '%').attr('aria-valuenow', persenBawah);
        }, 100);
        
        let interpretasi = '';
        if (persenStd >= 80) {
            interpretasi = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> <strong>Sangat Baik!</strong> Lebih dari 80% sesuai standar.</div>';
        } else if (persenStd >= 60) {
            interpretasi = '<div class="alert alert-info"><i class="fas fa-info-circle"></i> <strong>Baik.</strong> 60-80% sesuai standar.</div>';
        } else if (persenStd >= 40) {
            interpretasi = '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> <strong>Perlu Perhatian.</strong> Kurang dari 60% sesuai standar.</div>';
        } else {
            interpretasi = '<div class="alert alert-danger"><i class="fas fa-times-circle"></i> <strong>Perlu Perbaikan Segera!</strong> Kurang dari 40% sesuai standar.</div>';
        }
        $('#modal-interpretasi').html(interpretasi);
        
        $('#detailModal').modal('hide');
        $('#rekapitulasiModal').modal('show');
    });
    
    // Function to get shift badge
    function getShiftBadge(shift) {
        if (shift == 1) {
            return '<span class="badge badge-primary">Shift ' + shift + '</span>';
        } else if (shift == 2) {
            return '<span class="badge badge-success">Shift ' + shift + '</span>';
        } else {
            return '<span class="badge badge-secondary">Shift ' + shift + '</span>';
        }
    }
});
</script>
@endpush
@else
<div class="alert alert-info">
    <i class="fas fa-info-circle"></i> Tidak ada data chillroom yang tersedia.
</div>
@endif