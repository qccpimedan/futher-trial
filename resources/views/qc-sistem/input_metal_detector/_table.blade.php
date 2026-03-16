@php
use Illuminate\Support\Facades\Schema;
@endphp

@if(isset($metalDetector) && count($metalDetector))
<div class="table-responsive">
    <table style="white-space: nowrap;" class="text-center table table-bordered table-hover table-striped">
        <thead class="thead-light">
            <tr>
                <th class="text-center align-middle" rowspan="2">No</th>
                <th class="text-center align-middle" rowspan="2">Shift</th>
                <th class="text-center align-middle" rowspan="2">Line</th>
                 <th class="text-center align-middle" rowspan="2">Tanggal</th>
                <th class="text-center align-middle" rowspan="2">Jam</th>
                <!-- <th class="text-center align-middle" rowspan="2">Plan</th> -->
                <th class="text-center align-middle" rowspan="2">Nama Produk</th>
                <th class="text-center align-middle" rowspan="2">Kode Produksi</th>
                <th class="text-center bg-info" colspan="3">Fe 1.5 mm</th>
                <th class="text-center bg-success" colspan="3">Non Fe 2 mm</th>
                <th class="text-center bg-warning" colspan="3">SUS 316 2.5 mm</th>
                <th class="text-center align-middle" rowspan="2">Keterangan</th>
                <th class="text-center align-middle" rowspan="2">Aksi</th>
            </tr>
            <tr>
                <th class="text-center bg-info">Depan</th>
                <th class="text-center bg-info">Tengah</th>
                <th class="text-center bg-info">Belakang</th>
                <th class="text-center bg-success">Depan</th>
                <th class="text-center bg-success">Tengah</th>
                <th class="text-center bg-success">Belakang</th>
                <th class="text-center bg-warning">Depan</th>
                <th class="text-center bg-warning">Tengah</th>
                <th class="text-center bg-warning">Belakang</th>
            </tr>
        </thead>
        <tbody>
            @foreach($metalDetector as $index => $item)
            <tr>
                <td class="text-center">{{ ($metalDetector->currentPage() - 1) * $metalDetector->perPage() + $index + 1 }}</td>
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
                <td class="text-center"><span class="badge badge-warning">Line {{ $item->line ?? '-' }}</span></td>
                <td class="text-center">
                    @if(auth()->user()->id_role == 2 || auth()->user()->id_role == 3)
                        <span class="badge badge-secondary">{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</span>
                    @else
                        <span class="badge badge-secondary">{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y H:i:s') }}</span>
                    @endif
                </td>
                <td class="text-center">
                    <span class="badge badge-info">{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</span>
                </td>
                <!-- <td class="text-center">{{ $item->plan->nama_plan ?? '-' }}</td> -->
                <td class="text-center">{{ $item->produk->nama_produk ?? '-' }} {{ $item->berat_produk ?? '-' }} gram</td>
                <td class="text-center">{{ $item->kode_produksi }}</td>
                <td class="text-center bg-info">{{ $item->fe_depan_aktual }}</td>
                <td class="text-center bg-info">{{ $item->fe_tengah_aktual }}</td>
                <td class="text-center bg-info">{{ $item->fe_belakang_aktual }}</td>
                <td class="text-center bg-success">{{ $item->non_fe_depan_aktual }}</td>
                <td class="text-center bg-success">{{ $item->non_fe_tengah_aktual }}</td>
                <td class="text-center bg-success">{{ $item->non_fe_belakang_aktual }}</td>
                <td class="text-center bg-warning">{{ $item->sus_depan_aktual }}</td>
                <td class="text-center bg-warning">{{ $item->sus_tengah_aktual }}</td>
                <td class="text-center bg-warning">{{ $item->sus_belakang_aktual }}</td>
                <td class="text-center">{{ $item->keterangan ?? '-' }}</td>
                <td class="text-center">
                    <div class="btn-vertical">
                        <!-- Tombol Edit, Logs, dan Delete -->
                        <!-- <div class="mb-1">
@if(auth()->user()->hasPermissionTo('edit-input-metal-detector'))
                            <a href="{{ route('input-metal-detector.edit', $item->uuid) }}" class="btn btn-sm btn-warning" title="Edit Data">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endif
@if(auth()->user()->hasPermissionTo('view-input-metal-detector'))
<a href="{{ route('input-metal-detector.logs', $item->uuid) }}" class="btn btn-sm btn-info" title="History">
                                <i class="fas fa-history"></i>
                            </a>
                            @endif
@if(auth()->user()->hasPermissionTo('delete-input-metal-detector'))
<form action="{{ route('input-metal-detector.destroy', $item->uuid) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus Data" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        @endif
</div> -->
                        <x-action-buttons :item="$item" route-prefix="input-metal-detector" :show-view="false" />

                        <!-- Tombol Persetujuan berdasarkan Role -->
                        @php
                            $userRole = auth()->user()->id_role ?? null;
                            $hasApprovalColumns = Schema::hasColumn('input_metal_detector', 'approved_by_qc');
                        @endphp

                        <!-- Role-Based Button Display -->
                        @if($hasApprovalColumns)
                        <div class="btn-group-vertical" role="group">
                            @if(in_array($userRole, [1, 5]))
                                <!-- Role 1 dan 5: Tampilkan semua tombol dengan QC yang bisa diklik -->
                                <button type="button" 
                                        class="btn btn-sm {{ isset($item->approved_by_qc) && $item->approved_by_qc ? 'btn-success' : 'btn-outline-success' }} approve-btn" 
                                        data-id="{{ $item->uuid }}" 
                                        data-type="qc"
                                        title="Disetujui oleh QC"
                                        {{ isset($item->approved_by_qc) && $item->approved_by_qc ? 'disabled' : '' }}>
                                    <i class="fas {{ isset($item->approved_by_qc) && $item->approved_by_qc ? 'fa-check-circle' : 'fa-check' }}"></i> Verifikasi QC
                                </button>
                                <!-- Produksi button (read-only untuk role 1,5) -->
                                <button type="button" 
                                        class="btn btn-sm {{ $item->approved_by_produksi ? 'btn-primary' : 'btn-secondary' }}" 
                                        title="{{ $item->approved_by_produksi ? 'Sudah disetujui Produksi' : 'Menunggu persetujuan Produksi' }}"
                                        disabled>
                                    <i class="fas {{ $item->approved_by_produksi ? 'fa-check-circle' : 'fa-clock' }}"></i> Verifikasi FM/FL PRODUKSI
                                </button>
                                <!-- SPV button (read-only untuk role 1,5) -->
                                <button type="button" 
                                        class="btn btn-sm {{ $item->approved_by_spv ? 'btn-dark' : 'btn-secondary' }}" 
                                        title="{{ $item->approved_by_spv ? 'Sudah disetujui SPV' : 'Menunggu persetujuan SPV' }}"
                                        disabled>
                                    <i class="fas {{ $item->approved_by_spv ? 'fa-check-circle' : 'fa-clock' }}"></i> Verifikasi SPV
                                </button>

                            @elseif($userRole == 2)
                                <!-- Role 2: Hanya tampilkan tombol Produksi -->
                                <button type="button" 
                                        class="btn btn-sm {{ $item->approved_by_produksi ? 'btn-primary' : ($item->approved_by_qc ? 'btn-outline-primary' : 'btn-secondary') }} {{ $item->approved_by_qc && !$item->approved_by_produksi ? 'approve-btn' : '' }}" 
                                        data-id="{{ $item->uuid }}" 
                                        data-type="produksi"
                                        title="{{ !$item->approved_by_qc ? 'Menunggu persetujuan QC terlebih dahulu' : ($item->approved_by_produksi ? 'Sudah disetujui Produksi' : 'Disetujui oleh Produksi') }}"
                                        {{ !$item->approved_by_qc || $item->approved_by_produksi ? 'disabled' : '' }}>
                                    <i class="fas {{ $item->approved_by_produksi ? 'fa-check-circle' : (!$item->approved_by_qc ? 'fa-clock' : 'fa-check') }}"></i> Verifikasi FM/FL PRODUKSI
                                </button>

                            @elseif($userRole == 3)
                                <!-- Role 3: Hanya tampilkan tombol QC -->
                                <button type="button" 
                                        class="btn btn-sm {{ $item->approved_by_qc ? 'btn-success' : 'btn-outline-success' }} approve-btn" 
                                        data-id="{{ $item->uuid }}" 
                                        data-type="qc"
                                        title="Disetujui oleh QC"
                                        {{ $item->approved_by_qc ? 'disabled' : '' }}>
                                    <i class="fas {{ $item->approved_by_qc ? 'fa-check-circle' : 'fa-check' }}"></i> Verifikasi QC
                                </button>

                            @elseif($userRole == 4)
                                <!-- Role 4: Hanya tampilkan tombol SPV -->
                                <button type="button" 
                                        class="btn btn-sm {{ $item->approved_by_spv ? 'btn-dark' : ($item->approved_by_produksi ? 'btn-outline-dark' : 'btn-secondary') }} {{ $item->approved_by_produksi && !$item->approved_by_spv ? 'approve-btn' : '' }}" 
                                        data-id="{{ $item->uuid }}" 
                                        data-type="spv"
                                        title="{{ !$item->approved_by_produksi ? 'Menunggu persetujuan Produksi terlebih dahulu' : ($item->approved_by_spv ? 'Sudah disetujui SPV' : 'Disetujui oleh SPV') }}"
                                        {{ !$item->approved_by_produksi || $item->approved_by_spv ? 'disabled' : '' }}>
                                    <i class="fas {{ $item->approved_by_spv ? 'fa-check-circle' : (!$item->approved_by_produksi ? 'fa-clock' : 'fa-check') }}"></i> Verifikasi SPV
                                </button>

                            @else
                                <!-- Role lain: Tampilkan semua tombol sebagai read-only -->
                                <button type="button" 
                                        class="btn btn-sm {{ $item->approved_by_qc ? 'btn-success' : 'btn-secondary' }}" 
                                        title="{{ $item->approved_by_qc ? 'Sudah disetujui QC' : 'Menunggu persetujuan QC' }}"
                                        disabled>
                                    <i class="fas {{ $item->approved_by_qc ? 'fa-check-circle' : 'fa-clock' }}"></i> Verifikasi QC
                                </button>
                                <button type="button" 
                                        class="btn btn-sm {{ $item->approved_by_produksi ? 'btn-primary' : 'btn-secondary' }}" 
                                        title="{{ $item->approved_by_produksi ? 'Sudah disetujui Produksi' : 'Menunggu persetujuan Produksi' }}"
                                        disabled>
                                    <i class="fas {{ $item->approved_by_produksi ? 'fa-check-circle' : 'fa-clock' }}"></i> Verifikasi FM/FL PRODUKSI
                                </button>
                                <button type="button" 
                                        class="btn btn-sm {{ $item->approved_by_spv ? 'btn-dark' : 'btn-secondary' }}" 
                                        title="{{ $item->approved_by_spv ? 'Sudah disetujui SPV' : 'Menunggu persetujuan SPV' }}"
                                        disabled>
                                    <i class="fas {{ $item->approved_by_spv ? 'fa-check-circle' : 'fa-clock' }}"></i> Verifikasi SPV
                                </button>
                            @endif
                        </div>

                        <!-- Status Persetujuan -->
                        <div class="mt-1">
                            @if(isset($item->approved_by_qc) && $item->approved_by_qc)
                                <small class="badge badge-success d-block mb-1">✓ QC</small>
                            @endif
                            @if(isset($item->approved_by_produksi) && $item->approved_by_produksi)
                                <small class="badge badge-primary d-block mb-1">✓ FM/FL PRODUKSI</small>
                            @endif
                            @if(isset($item->approved_by_spv) && $item->approved_by_spv)
                                <small class="badge badge-dark d-block mb-1">✓ SPV</small>
                            @endif
                        </div>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<div class="alert alert-info text-center">
    <i class="fas fa-info-circle fa-2x mb-2"></i>
    <h5>Belum ada data Metal Detector</h5>
    <p class="mb-0">Silakan tambah data baru dengan mengklik tombol "Tambah Data" di atas.</p>
</div>
@endif