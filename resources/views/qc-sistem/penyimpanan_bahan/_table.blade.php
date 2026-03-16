@if(isset($penyimpanan) && count($penyimpanan))
<div class="table-responsive">
    <table id="myTable" class="table table-bordered table-striped table-hover" style="white-space: nowrap;">
        <thead class="thead-light">
            <tr>
                <th>No</th>
                <th class="text-center">Shift</th>
                <th class="text-center">Tanggal</th>
                <th class="text-center">Pemeriksaan Kondisi & Penempatan</th>
                <th class="text-center">Pemeriksaan Kebersihan Ruangan</th>
                <th class="text-center">Pemeriksaan Suhu Ruang</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($penyimpanan as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">
                    @if($item->shift_id == 1)
                        <span class="badge bg-primary">Shift {{ $item->shift->shift ?? 'Shift 1' }}</span>
                    @elseif($item->shift_id == 2)
                        <span class="badge bg-success">Shift {{ $item->shift->shift ?? 'Shift 2' }}</span>
                    @else
                        <span class="badge bg-secondary">Shift {{ $item->shift->shift ?? 'Shift ' . $item->shift_id }}</span>
                    @endif
                </td>
                <td class="text-center">
                    <span class="badge bg-secondary">{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y H:i:s') }}</span>
                </td>
                <td class="text-center">
                    {{ $item->pemeriksaan_kondisi }}
                </td>
                <td class="text-center">
                    {{ $item->pemeriksaan_kebersihan }}
                </td>
                <td class="text-center">
                    {{ $item->kebersihan_ruang }}°C
                </td>
                <td class="text-center">
                    <!-- @php $q = request('group_uuid') ? ('?group_uuid=' . request('group_uuid')) : ''; @endphp
                    @if(!request('group_uuid'))
@if(auth()->user()->hasPermissionTo('edit-penyimpanan-bahan'))
                        <a href="{{ route('penyimpanan-bahan.edit', $item->uuid) }}" class="btn btn-warning btn-sm" title="Edit Data">
                            <i class="fas fa-edit"></i>
                        </a>
                        @endif

@if(auth()->user()->hasPermissionTo('edit-penyimpanan-bahan.twohour'))<a href="{{ route('penyimpanan-bahan.twohour.edit', $item->uuid) }}" class="btn btn-info btn-sm" title="Edit Per 2 Jam (buat record baru)">
                            <i class="fas fa-clock"></i>
                        </a>
                        @endif
<a href="{{ route('penyimpanan-bahan.index', ['group_uuid' => ($item->group_uuid ?? $item->uuid)]) }}" class="btn btn-secondary btn-sm" title="Lihat Riwayat per 2 Jam">
                            <i class="fas fa-history"></i>
                        </a>
                    @endif
                    <form action="{{ route('penyimpanan-bahan.destroy', $item->uuid) . $q }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm" title="Hapus Data">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form> -->
                    <x-penyimpanan-bahan-buttons :item="$item" />
                </td>
                
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<div class="alert alert-info text-center">
    <i class="fas fa-info-circle fa-2x mb-2"></i>
    <h5>Belum ada data Penyimpanan Bahan</h5>
    <p class="mb-0">Silakan tambah data baru dengan mengklik tombol "Tambah Data" di atas.</p>
</div>
@endif