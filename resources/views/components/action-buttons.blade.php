@if($canAccess())
    <div class="btn-vertical">
        @if($showView && $hasRoute($routePrefix . '.show'))
            <a href="{{ route($routePrefix . '.show', $item->uuid) }}" class="btn btn-info btn-sm" title="Lihat Detail">
                <i class="fas fa-eye"></i>
            </a>
        @endif
        
        @if($hasRoute($routePrefix . '.edit') && Auth::user()->hasPermissionTo('edit-' . $permissionPrefix))
            <a href="{{ route($routePrefix . '.edit', $item->uuid) }}" class="btn btn-warning btn-sm" title="Edit">
                <i class="fas fa-edit"></i>
            </a>
        @endif
        
        @if($showHistory && $hasRoute($routePrefix . '.logs'))
            <a href="{{ route($routePrefix . '.logs', $item->uuid) }}" class="btn btn-secondary btn-sm" title="History">
                <i class="fas fa-history"></i>
            </a>
        @endif
        
        @if($hasRoute($routePrefix . '.destroy') && Auth::user()->hasPermissionTo('delete-' . $permissionPrefix))
            <form action="{{ route($routePrefix . '.destroy', $item->uuid) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        @endif
    </div>
@elseif($isViewOnlyRole())
    <!-- Role QC dan Fm/fl hanya bisa lihat -->
    @if($showView && $hasRoute($routePrefix . '.show'))
        <a href="{{ route($routePrefix . '.show', $item->uuid) }}" class="btn btn-info btn-sm" title="Lihat Detail">
            <i class="fas fa-eye"></i> Lihat
        </a>
    @elseif($showHistory && $hasRoute($routePrefix . '.logs'))
        <a href="{{ route($routePrefix . '.logs', $item->uuid) }}" class="btn btn-secondary btn-sm" title="Lihat History">
            <i class="fas fa-history"></i> History
        </a>
    @else
        <span class="badge badge-info">
            <i class="fas fa-eye"></i> View Only ({{ Auth::user()->role }})
        </span>
    @endif
@else
    <!-- Role lain tidak memiliki akses -->
    <span class="badge badge-secondary">
        <i class="fas fa-lock"></i> No Access
    </span>
@endif