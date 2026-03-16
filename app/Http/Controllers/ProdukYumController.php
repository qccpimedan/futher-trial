<?php

namespace App\Http\Controllers;

use App\Models\ProdukYum;
use App\Models\ProdukYumLog;
use App\Models\JenisProduk;
use App\Models\DataShift;
use App\Models\DataBag;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ProdukYumController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $query = ProdukYum::with(['produk', 'plan', 'shift', 'user', 'dataBag', 'qcApprover', 'produksiApprover', 'spvApprover']);
        
        // Filter berdasarkan plan user jika bukan superadmin
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        // Search functionality
        $search = request('search');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('produk', function ($produkQuery) use ($search) {
                    $produkQuery->where('nama_produk', 'like', '%' . $search . '%');
                })->orWhere('kode_produksi', 'like', '%' . $search . '%');
            });
        }

        // Per page handling
        $perPage = request()->get('per_page', 10);
        $perPage = in_array($perPage, [5, 10, 25, 50, 100]) ? $perPage : 10;
        
        $produkYums = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return view('qc-sistem.produk_yum.index', compact('produkYums', 'search', 'perPage'));
    }

    public function create()
    {
        $user = Auth::user();
        
        // Filter produk berdasarkan plan user jika bukan superadmin
        $query = JenisProduk::query();
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        $produks = $query->get();
        
        // Filter shift berdasarkan plan user jika bukan superadmin
        $shiftQuery = DataShift::query();
        if ($user->role !== 'superadmin') {
            $shiftQuery->where('id_plan', $user->id_plan);
        }
        $shifts = $shiftQuery->get();
        
        // Filter data bags berdasarkan plan user jika bukan superadmin
        $dataBagQuery = DataBag::query();
        if ($user->role !== 'superadmin') {
            $dataBagQuery->where('id_plan', $user->id_plan);
        }
        $dataBags = $dataBagQuery->get();

        return view('qc-sistem.produk_yum.create', compact('produks', 'shifts', 'dataBags'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $isSpecialRole = ($user->id_role == 2 || $user->id_role == 3);
        
        // Validasi berbeda berdasarkan role
        if ($isSpecialRole) {
            $request->validate([
                'id_produk' => 'required|exists:jenis_produk,id',
                'shift_id' => 'required|exists:data_shift,id',
                'id_data_bag' => 'required|exists:data_bag,id',
                'kode_produksi' => 'required|string|max:255',
                'berat_pcs' => 'nullable|array',
                'berat_pcs.*' => 'nullable|string',
                'jumlah_pcs' => 'required|array',
                'jumlah_pcs.*' => 'required|string',
                'aktual_berat' => 'required|array',
                'aktual_berat.*' => 'required|string',
                'tanggal' => 'required|date_format:d-m-Y', // Hanya validasi format tanggal saja
                'jam' => 'required',
            ]);
        } else {
            $request->validate([
                'id_produk' => 'required|exists:jenis_produk,id',
                'shift_id' => 'required|exists:data_shift,id',
                'id_data_bag' => 'required|exists:data_bag,id',
                'kode_produksi' => 'required|string|max:255',
                'berat_pcs' => 'nullable|array',
                'berat_pcs.*' => 'nullable|string',
                'jumlah_pcs' => 'required|array',
                'jumlah_pcs.*' => 'required|string',
                'aktual_berat' => 'required|array',
                'aktual_berat.*' => 'required|string',
                'tanggal' => 'required|date_format:d-m-Y H:i:s',
                'jam' => 'required',
            ]);
        }

        // Transform the date format
        $tanggalData = $request->all();
        if ($isSpecialRole) {
            // Untuk user dengan role 2 atau 3, gunakan format tanggal dari request tapi waktu dari now()
            $dateOnly = \Carbon\Carbon::createFromFormat('d-m-Y', $request->tanggal)->format('Y-m-d');
            $timeNow = now()->format('H:i:s');
            $tanggalData['tanggal'] = $dateOnly . ' ' . $timeNow;
        } else {
            // Untuk user lain, gunakan format tanggal dan waktu dari request
            $tanggalData['tanggal'] = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s');
        }
        
        $tanggalData['jam'] = $request->jam;
        
        $produkYum = ProdukYum::create($tanggalData + [
            'id_plan' => $user->id_plan,
            'user_id' => $user->id,
        ]);
        return redirect()->route('produk-yum.index')
            ->with('success', 'Data Produk YUM berhasil ditambahkan.');
    }

    public function show($uuid)
    {
        $produkYum = ProdukYum::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check access permission
        if ($user->role !== 'superadmin' && $produkYum->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data ini.');
        }
        
        $produkYum->load(['produk', 'plan', 'shift', 'user', 'dataBag']);
        return view('qc-sistem.produk_yum.show', compact('produkYum'));
    }

    public function edit($uuid)
    {
        $produkYum = ProdukYum::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check access permission
        if ($user->role !== 'superadmin' && $produkYum->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }
        
        // Filter produk berdasarkan plan user jika bukan superadmin
        $query = JenisProduk::query();
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        $produks = $query->get();
        
        // Filter shift berdasarkan plan user jika bukan superadmin
        $shiftQuery = DataShift::query();
        if ($user->role !== 'superadmin') {
            $shiftQuery->where('id_plan', $user->id_plan);
        }
        $shifts = $shiftQuery->get();
        
        // Filter data bags berdasarkan plan user jika bukan superadmin
        $dataBagQuery = DataBag::query();
        if ($user->role !== 'superadmin') {
            $dataBagQuery->where('id_plan', $user->id_plan);
        }
        $dataBags = $dataBagQuery->get();

        return view('qc-sistem.produk_yum.edit', compact('produkYum', 'produks', 'shifts', 'dataBags'));
    }

    public function update(Request $request, $uuid)
    {
        $produkYum = ProdukYum::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check access permission
        if ($user->role !== 'superadmin' && $produkYum->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengupdate data ini.');
        }
        
        $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'shift_id' => 'required|exists:data_shift,id',
            'id_data_bag' => 'required|exists:data_bag,id',
            'kode_produksi' => 'required|string|max:255',
            'berat_pcs' => 'nullable|array',
            'berat_pcs.*' => 'nullable|string',
            'jumlah_pcs' => 'required|array',
            'jumlah_pcs.*' => 'required|string',
            'aktual_berat' => 'required|array',
            'aktual_berat.*' => 'required|string',
            'tanggal' => 'required|date',
        ]);

        $produkYum->update($request->except(['kode_form']) + [
            'id_plan' => $user->id_plan,
            'user_id' => $user->id,
        ]);

        return redirect()->route('produk-yum.index')
            ->with('success', 'Data Produk YUM berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $produkYum = ProdukYum::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check access permission
        if ($user->role !== 'superadmin' && $produkYum->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
        }
        
        $produkYum->delete();
        
        return redirect()->route('produk-yum.index')
            ->with('success', 'Data Produk YUM berhasil dihapus.');
    }

    // AJAX method to get data bags based on selected product
    public function getDataBags(Request $request)
    {
        $dataBags = DataBag::where('id_produk', $request->id_produk)->get();
        return response()->json($dataBags);
    }

    /**
     * Show logs for specific produk yum
     */
    public function showLogs($uuid)
    {
        $produkYum = ProdukYum::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check access permission
        if ($user->role !== 'superadmin' && $produkYum->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data ini.');
        }
        
        $logs = ProdukYumLog::where('produk_yum_uuid', $uuid)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('qc-sistem.produk_yum.logs', compact('produkYum', 'logs'));
    }

    /**
     * Get logs data for DataTables (JSON)
     */
    public function getLogsJson($uuid)
    {
        $produkYum = ProdukYum::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check access permission
        if ($user->role !== 'superadmin' && $produkYum->id_plan !== $user->id_plan) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $logs = ProdukYumLog::where('produk_yum_uuid', $uuid)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'data' => $logs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'user_name' => $log->user->name ?? 'Unknown',
                    'user_role' => $log->user->role ?? 'Unknown',
                    'field_yang_diubah' => $log->nama_field,
                    'deskripsi_perubahan' => $log->deskripsi_perubahan,
                    'ip_address' => $log->ip_address,
                    'created_at' => $log->created_at->format('d/m/Y H:i:s'),
                    'created_at_diff' => $log->created_at->diffForHumans(),
                ];
            })
        ]);
    }

    /**
     * Bulk export PDF with filters
     */
    public function bulkExportPdf(Request $request)
    {
        $request->validate([
            'kode_form' => 'required|string|max:50',
            'tanggal' => 'nullable|date',
            'shift_id' => 'nullable|exists:data_shift,id',
            'id_produk' => 'nullable|exists:jenis_produk,id',
        ]);

        $user = Auth::user();
        
        // Build query with filters
        $query = ProdukYum::with(['produk', 'plan', 'shift', 'user', 'dataBag', 'qcApprover', 'produksiApprover', 'spvApprover']);
        
        // Filter berdasarkan plan user jika bukan superadmin
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        
        // Apply filters
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }
        
        if ($request->filled('shift_id')) {
            $query->where('shift_id', $request->shift_id);
        }
        
        if ($request->filled('id_produk')) {
            $query->where('id_produk', $request->id_produk);
        }
        
        $data = $query->orderBy('tanggal', 'desc')->get();
        
        if ($data->isEmpty()) {
            return response('<div class="alert alert-warning">Tidak ada data yang ditemukan berdasarkan filter yang dipilih.</div>', 404);
        }
        
        // Update kode_form for filtered data
        $data->each(function ($item) use ($request) {
            $item->update(['kode_form' => $request->kode_form]);
        });
        
        // Get filter information for PDF
        $filterInfo = [
            'tanggal' => $request->tanggal ? date('d/m/Y', strtotime($request->tanggal)) : 'Semua Tanggal',
            'shift' => $request->shift_id ? DataShift::find($request->shift_id)->shift : 'Semua Shift',
            'produk' => $request->id_produk ? JenisProduk::find($request->id_produk)->nama_produk : 'Semua Produk',
            'kode_form' => $request->kode_form,
        ];
        
        $pdf = Pdf::loadView('qc-sistem.produk_yum.export_pdf', compact('data', 'filterInfo'))
                  ->setPaper('a4', 'portrait');
        
        $filename = 'produk-yum-' . date('Y-m-d-H-i-s') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Approve data with role-based validation
     */
    public function approve(Request $request, $uuid)
    {
        $request->validate([
            'type' => 'required|in:qc,produksi,spv'
        ]);

        $produkYum = ProdukYum::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        $type = $request->type;

        // Role-based access control following ProdukNonForming pattern
        $allowedRoles = [
            'qc' => [1, 3, 5], // Role 1&5 (QC only), Role 3 (QC only)
            'produksi' => [1, 2, 5], // Role 1&5 (produksi only), Role 2 (produksi only)
            'spv' => [1, 4, 5] // Role 1&5 (SPV only), Role 4 (SPV only)
        ];

        if (!in_array($user->id_role, $allowedRoles[$type])) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk melakukan approval ini.'
            ], 403);
        }

        // Sequential approval validation (QC → Produksi → SPV)
        if ($type === 'produksi' && !$produkYum->approved_by_qc) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh QC terlebih dahulu.'
            ], 400);
        }

        if ($type === 'spv' && !$produkYum->approved_by_produksi) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh Produksi terlebih dahulu.'
            ], 400);
        }

        // Check if already approved
        $approvalField = "approved_by_{$type}";
        if ($produkYum->$approvalField) {
            return response()->json([
                'success' => false,
                'message' => 'Data sudah disetujui sebelumnya.'
            ], 400);
        }

        // Update approval
        $produkYum->update([
            $approvalField => true,
            "{$type}_approved_by" => $user->id,
            "{$type}_approved_at" => now()
        ]);

        // Log approval activity
        ProdukYumLog::create([
            'produk_yum_id' => $produkYum->id,
            'produk_yum_uuid' => $produkYum->uuid,
            'user_id' => $user->id,
            'field_yang_diubah' => ['approval_' . $type],
            'nilai_lama' => [false],
            'nilai_baru' => [true],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disetujui.'
        ]);
    }
}
