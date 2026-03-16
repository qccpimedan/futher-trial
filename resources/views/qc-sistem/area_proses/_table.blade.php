@php
use Illuminate\Support\Facades\Schema;
@endphp

@if(isset($areaProses) && count($areaProses))
<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover" style="white-space: nowrap;">
        <thead class="thead-light">
            <tr>
                <th>No</th>
                <th class="text-center">Shift</th>
                <th class="text-center">Tanggal</th>
                <th class="text-center">Jam</th>
                <th class="text-center">Area</th>
                <th class="text-center">Kebersihan Ruangan</th>
                <th class="text-center">Kebersihan Karyawan</th>
                <th class="text-center">Pemeriksaan Suhu Ruang °C</th>
                <th class="text-center">Ketidaksesuaian</th>
                <th class="text-center">Tindakan Koreksi</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($areaProses as $index => $item)
            <tr>
                <td class="text-center">{{ $areaProses->firstItem() + $index }}</td>
                    @if($item->shift_id == 1)
                        <span class="badge bg-primary">Shift {{ $item->shift->shift ?? 'Shift 1' }}</span>
                    @elseif($item->shift_id == 2)
                        <span class="badge bg-success">Shift {{ $item->shift->shift ?? 'Shift 2' }}</span>
                    @else
                        <span class="badge bg-secondary">Shift {{ $item->shift->shift ?? 'Shift ' . $item->shift_id }}</span>
                    @endif
                </td>
                <td>
                    <span class="badge badge-secondary">
                        @php
                            $userRole = auth()->user()->id_role ?? null;
                            $showTime = in_array($userRole, [1, 2, 5]); // superadmin, admin, spv
                            $format = $showTime ? 'd-m-Y H:i:s' : 'd-m-Y';
                        @endphp
                        {{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format($format) : '-' }}
                    </span>
                </td>
                <td>
                    {{ isset($item->jam) && $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}
                </td>
                <td class="text-center">
                    <span class="text-truncate" style="max-width: 150px; display: inline-block;" title="{{ $item->area->area ?? '-' }}">
                        {{ Str::limit($item->area->area ?? '-', 30) }}
                    </span>
                </td>
                <td class="text-center">
                    <span class="badge {{ $item->getStatusBadgeClass('kebersihan_ruangan') }}">
                        <i class="{{ $item->getStatusIcon('kebersihan_ruangan') }}"></i>
                        {{ $item->kebersihan_ruangan }}
                    </span>
                  @if(in_array(strtolower($item->area->area ?? ''), ['chillroom', 'seasoning']))
                    <button type="button" class="btn btn-sm btn-info ml-2" onclick="showKondisiBarang('{{ $item->kondisi_barang ?? 'Belum diisi' }}')">
                        Cek Kondisi Barang
                    </button>
                @endif
                </td>
                <td class="text-center">
                    <span class="badge {{ $item->getStatusBadgeClass('kebersihan_karyawan') }}">
                        <i class="{{ $item->getStatusIcon('kebersihan_karyawan') }}"></i>
                        {{ $item->kebersihan_karyawan }}
                    </span>
                </td>
                <td class="text-center">
                    {{ $item->pemeriksaan_suhu_ruang }} °C
                </td>
                <td class="text-center">
                    {{ $item->ketidaksesuaian }}
                </td>
                <td class="text-center">
                    {{ $item->tindakan_koreksi }}
                </td>
                <td class="text-center">
                    <div class="btn-vertical">
                        <div class="btn-vertical mb-1">
                            <!-- @php $q = request('group_uuid') ? ('?group_uuid=' . request('group_uuid')) : ''; @endphp
                            @if(!request('group_uuid'))
@if(auth()->user()->hasPermissionTo('edit-area-proses'))
                                <a href="{{ route('area-proses.edit', $item->uuid) }}" class="btn btn-warning btn-sm" title="Edit Data">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endif

@if(auth()->user()->hasPermissionTo('edit-area-proses.twohour'))<a href="{{ route('area-proses.twohour.edit', $item->uuid) }}" class="btn btn-info btn-sm" title="Edit Per 2 Jam (buat record baru)">
                                    <i class="fas fa-clock"></i>
                                </a>
                                @endif
<a href="{{ route('area-proses.index', ['group_uuid' => ($item->group_uuid ?? $item->uuid)]) }}" class="btn btn-secondary btn-sm" title="Lihat Riwayat per 2 Jam">
                                    <i class="fas fa-history"></i>
                                </a>
                            @endif
                            <form action="{{ route('area-proses.destroy', $item->uuid) . $q }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm" title="Hapus Data">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form> -->
                            <x-area-proses-buttons :item="$item" />

                        </div>
                        
                        <!-- Approval Buttons -->
                        @php
                            $userRole = auth()->user()->id_role ?? null;
                            $hasApprovalColumns = Schema::hasColumn('area_proses', 'approved_by_qc');
                        @endphp

                        <!-- Role-Based Button Display -->
                        @if($hasApprovalColumns)
                        <div class="btn-group-vertical mb-1" role="group">
                            @if(in_array($userRole, [1, 5]))
                                <!-- Role 1 dan 5: Tampilkan QC button yang bisa diklik -->
                                <button type="button" 
                                        class="btn btn-sm {{ isset($item->approved_by_qc) && $item->approved_by_qc ? 'btn-success' : 'btn-outline-success' }} approve-btn" 
                                        data-id="{{ $item->uuid }}" 
                                        data-type="qc"
                                        title="Disetujui oleh QC"
                                        {{ isset($item->approved_by_qc) && $item->approved_by_qc ? 'disabled' : '' }}>
                                    <i class="fas {{ isset($item->approved_by_qc) && $item->approved_by_qc ? 'fa-check-circle' : 'fa-check' }}"></i> QC
                                </button>
                                <!-- Produksi button (read-only untuk role 1,5) -->
                                <button type="button" 
                                        class="btn btn-sm {{ $item->approved_by_produksi ? 'btn-primary' : 'btn-secondary' }}" 
                                        title="{{ $item->approved_by_produksi ? 'Sudah disetujui Produksi' : 'Menunggu persetujuan Produksi' }}"
                                        disabled>
                                    <i class="fas {{ $item->approved_by_produksi ? 'fa-check-circle' : 'fa-clock' }}"></i> FM/FL PRODUKSI
                                </button>
                                <!-- SPV button (read-only untuk role 1,5) -->
                                <button type="button" 
                                        class="btn btn-sm {{ $item->approved_by_spv ? 'btn-dark' : 'btn-secondary' }}" 
                                        title="{{ $item->approved_by_spv ? 'Sudah disetujui SPV' : 'Menunggu persetujuan SPV' }}"
                                        disabled>
                                    <i class="fas {{ $item->approved_by_spv ? 'fa-check-circle' : 'fa-clock' }}"></i> SPV
                                </button>

                            @elseif($userRole == 2)
                                <!-- Role 2: Hanya tampilkan tombol Produksi -->
                                <button type="button" 
                                        class="btn btn-sm {{ $item->approved_by_produksi ? 'btn-primary' : ($item->approved_by_qc ? 'btn-outline-primary' : 'btn-secondary') }} {{ $item->approved_by_qc && !$item->approved_by_produksi ? 'approve-btn' : '' }}" 
                                        data-id="{{ $item->uuid }}" 
                                        data-type="produksi"
                                        title="{{ !$item->approved_by_qc ? 'Menunggu persetujuan QC terlebih dahulu' : ($item->approved_by_produksi ? 'Sudah disetujui Produksi' : 'Disetujui oleh Produksi') }}"
                                        {{ !$item->approved_by_qc || $item->approved_by_produksi ? 'disabled' : '' }}>
                                    <i class="fas {{ $item->approved_by_produksi ? 'fa-check-circle' : (!$item->approved_by_qc ? 'fa-clock' : 'fa-check') }}"></i> Disetujui oleh Produksi
                                </button>

                            @elseif($userRole == 3)
                                <!-- Role 3: Hanya tampilkan tombol QC -->
                                <button type="button" 
                                        class="btn btn-sm {{ $item->approved_by_qc ? 'btn-success' : 'btn-outline-success' }} approve-btn" 
                                        data-id="{{ $item->uuid }}" 
                                        data-type="qc"
                                        title="Disetujui oleh QC"
                                        {{ $item->approved_by_qc ? 'disabled' : '' }}>
                                    <i class="fas {{ $item->approved_by_qc ? 'fa-check-circle' : 'fa-check' }}"></i> Disetujui oleh QC
                                </button>

                            @elseif($userRole == 4)
                                <!-- Role 4: Hanya tampilkan tombol SPV -->
                                <button type="button" 
                                        class="btn btn-sm {{ $item->approved_by_spv ? 'btn-dark' : ($item->approved_by_produksi ? 'btn-outline-dark' : 'btn-secondary') }} {{ $item->approved_by_produksi && !$item->approved_by_spv ? 'approve-btn' : '' }}" 
                                        data-id="{{ $item->uuid }}" 
                                        data-type="spv"
                                        title="{{ !$item->approved_by_produksi ? 'Menunggu persetujuan Produksi terlebih dahulu' : ($item->approved_by_spv ? 'Sudah disetujui SPV' : 'Disetujui oleh SPV') }}"
                                        {{ !$item->approved_by_produksi || $item->approved_by_spv ? 'disabled' : '' }}>
                                    <i class="fas {{ $item->approved_by_spv ? 'fa-check-circle' : (!$item->approved_by_produksi ? 'fa-clock' : 'fa-check') }}"></i> Disetujui oleh SPV
                                </button>
                            @endif
                        </div>

                        <!-- Status Persetujuan -->
                        <div class="mt-1">
                            @if($item->approved_by_qc)
                                <small class="badge badge-success d-block mb-1">✓ QC</small>
                            @endif
                            @if($item->approved_by_produksi)
                                <small class="badge badge-primary d-block mb-1">✓ FM/FL PRODUKSI</small>
                            @endif
                            @if($item->approved_by_spv)
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
    <h5>Belum ada data Area Proses</h5>
    <p class="mb-0">Silakan tambah data baru dengan mengklik tombol "Tambah Data" di atas.</p>
</div>
@endif


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
   function showKondisiBarang(kondisi) {
    let icon = '';
    let text = kondisi;

    if (kondisi.toLowerCase() === 'baik') {
        icon = '✓ OK';
    } else if (kondisi.toLowerCase() === 'rusak') {
        icon = '✗ Tidak OK';
    } else {
        icon = kondisi; // misal 'Belum diisi'
    }

    Swal.fire({
        title: 'Kondisi Barang',
        html: `<strong>${icon}</strong>`,
        icon: 'info',
        confirmButtonText: 'Tutup'
    });
}
</script>
@endpush