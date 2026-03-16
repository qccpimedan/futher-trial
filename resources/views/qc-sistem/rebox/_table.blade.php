@php use Carbon\Carbon; @endphp
{{-- filepath: resources/views/qc-sistem/rebox/_table.blade.php --}}

@if(isset($reboxes) && count($reboxes))
<div class="table-responsive">
    <table style="white-space: nowrap;" class="text-center table table-bordered table-striped table-hover">
        <thead class="thead-light">
            <tr>
                <th>No</th>
                <th>Shift</th>
                <th>Tanggal</th>
                <th>Jam</th>
                <th>Nama Produk</th>
                <th>Kode Produksi</th>
                <th>Best Before</th>
                <th>Kesesuaian dan Jumlah Produk</th>
                <th>Labelisasi Produk</th>
                <th>Dibuat Oleh</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reboxes as $item)
            <tr>
                <td>{{ $reboxes->firstItem() + $loop->index }}</td>
                <td>
                    @if($item->shift->shift == 1)
                        <span class="badge bg-primary">Shift {{ $item->shift->shift }}</span>
                    @elseif($item->shift->shift == 2)
                        <span class="badge bg-success">Shift {{ $item->shift->shift }}</span>
                    @else
                        <span class="badge bg-secondary">Shift {{ $item->shift->shift }}</span>
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
                    <span class="badge badge-secondary">{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</span>
                </td>
                <td class="font-weight-medium">{{ $item->nama_produk ?? '-' }}</td>
                <td>{{ $item->kode_produksi ?? '-' }}</td>
                <td class="text-center">
                    {{ $item->best_before ? \Carbon\Carbon::parse($item->best_before)->format('d-m-Y') : '-' }}
                </td>
                <td class="text-center">{{ $item->isi_jumlah ?? '-' }}</td>
                <td class="text-center">{{ $item->labelisasi ?? '-' }}</td>
                <td>{{ $item->createdBy->name ?? '-' }}</td>
                <td class="text-center">
                    <div class="btn-vertical">
                        <!-- Tombol Edit, Log, dan Delete -->
                        <div class="mb-1">
@if(auth()->user()->hasPermissionTo('edit-rebox'))
                            <a href="{{ route('rebox.edit', $item->uuid) }}" 
                               class="btn btn-sm btn-warning" 
                               title="Edit Data">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endif
@if(auth()->user()->hasPermissionTo('view-rebox'))
<a href="{{ route('rebox.logs', $item->uuid) }}" 
                               class="btn btn-sm btn-info" 
                               title="Lihat Log">
                                <i class="fas fa-history"></i>
                            </a>
                            @endif
@if(auth()->user()->hasPermissionTo('delete-rebox'))
<form action="{{ route('rebox.destroy', $item->uuid) }}" 
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

                        <!-- Tombol Persetujuan berdasarkan Role -->
                        @php
                            $userRole = auth()->user()->id_role ?? null;
                        @endphp

                        <!-- Role-Based Button Display -->
                        <div class="btn-group-vertical mb-1" role="group">
                            @if(in_array($userRole, [1, 5]))
                                <!-- Role 1 dan 5: Tampilkan semua tombol dengan QC yang bisa diklik -->
                                <button type="button" 
                                        class="btn btn-sm {{ $item->approved_by_qc ? 'btn-success' : 'btn-outline-success' }} approve-btn" 
                                        data-id="{{ $item->uuid }}" 
                                        data-type="qc"
                                        title="Disetujui oleh QC"
                                        {{ $item->approved_by_qc ? 'disabled' : '' }}>
                                    <i class="fas {{ $item->approved_by_qc ? 'fa-check-circle' : 'fa-check' }}"></i> QC
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

                            @else
                                <!-- Role lain: Tampilkan semua tombol sebagai read-only -->
                                <button type="button" 
                                        class="btn btn-sm {{ $item->approved_by_qc ? 'btn-success' : 'btn-secondary' }}" 
                                        title="{{ $item->approved_by_qc ? 'Sudah disetujui QC' : 'Menunggu persetujuan QC' }}"
                                        disabled>
                                    <i class="fas {{ $item->approved_by_qc ? 'fa-check-circle' : 'fa-clock' }}"></i> QC
                                </button>
                                <button type="button" 
                                        class="btn btn-sm {{ $item->approved_by_produksi ? 'btn-primary' : 'btn-secondary' }}" 
                                        title="{{ $item->approved_by_produksi ? 'Sudah disetujui Produksi' : 'Menunggu persetujuan Produksi' }}"
                                        disabled>
                                    <i class="fas {{ $item->approved_by_produksi ? 'fa-check-circle' : 'fa-clock' }}"></i> Produksi
                                </button>
                                <button type="button" 
                                        class="btn btn-sm {{ $item->approved_by_spv ? 'btn-dark' : 'btn-secondary' }}" 
                                        title="{{ $item->approved_by_spv ? 'Sudah disetujui SPV' : 'Menunggu persetujuan SPV' }}"
                                        disabled>
                                    <i class="fas {{ $item->approved_by_spv ? 'fa-check-circle' : 'fa-clock' }}"></i> SPV
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
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Menampilkan Navigasi Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $reboxes->appends(request()->query())->links('pagination::bootstrap-4') }}
    </div>
</div>
<!-- CSS untuk Sequential Approval -->
<style>
.btn-group-vertical .btn {
    margin-bottom: 2px;
}
.btn-outline-success:hover {
    background-color: #28a745;
    border-color: #28a745;
}
.btn-outline-primary:hover {
    background-color: #007bff;
    border-color: #007bff;
}
.btn-outline-dark:hover {
    background-color: #343a40;
    border-color: #343a40;
}
.approval-pending {
    opacity: 0.6;
    cursor: not-allowed;
}
.approval-ready {
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.7); }
    70% { box-shadow: 0 0 0 10px rgba(0, 123, 255, 0); }
    100% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0); }
}
</style>

<!-- JavaScript untuk Handle Approval -->
@push('scripts')
<script>
$(document).ready(function() {
    $('.approve-btn').click(function() {
        const id = $(this).data('id');
        const type = $(this).data('type');
        const button = $(this);
        
        // Konfirmasi sebelum approve
        const typeNames = {
            'qc': 'QC',
            'produksi': 'Produksi', 
            'spv': 'SPV'
        };
        
        if (confirm(`Apakah Anda yakin ingin menyetujui data ini sebagai ${typeNames[type]}?`)) {
            // Disable button sementara dan show loading
            button.prop('disabled', true);
            button.html('<i class="fas fa-spinner fa-spin"></i> Processing...');
            
            // AJAX request untuk approval
            $.ajax({
                url: '{{ route("rebox.approve", ":id") }}'.replace(':id', id),
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    type: type
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        button.removeClass('btn-outline-success btn-outline-primary btn-outline-dark')
                              .addClass('btn-success')
                              .html('<i class="fas fa-check-circle"></i> Approved');
                        
                        // Reload halaman setelah delay singkat
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        alert('Gagal menyetujui data: ' + response.message);
                        button.prop('disabled', false);
                        // Restore original button text
                        const originalText = {
                            'qc': '<i class="fas fa-check"></i> QC',
                            'produksi': '<i class="fas fa-check"></i> Fm/Fl PRODUKSI',
                            'spv': '<i class="fas fa-check"></i> SPV'
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
                    // Restore original button text
                    const originalText = {
                        'qc': '<i class="fas fa-check"></i> QC',
                        'produksi': '<i class="fas fa-check"></i>FM/FL PRODUKSI',
                        'spv': '<i class="fas fa-check"></i> SPV'
                    };
                    button.html(originalText[type]);
                }
            });
        }
    });
});
</script>
@endpush
@else
<div class="alert alert-info">
    <i class="fas fa-info-circle"></i> Tidak ada data rebox yang tersedia.
</div>
@endif