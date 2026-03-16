<?php

namespace App\Http\Controllers;

use App\Models\PemasakanNasi;
use App\Models\PemasakanNasiLog;
use App\Models\JenisProduk;
use App\Models\DataShift;
use Illuminate\Http\Request;


class PemasakanNasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $search = $request->get('search');
        
        $query = PemasakanNasi::with(['plan', 'user', 'shift', 'produk', 'qcApprover', 'produksiApprover', 'spvApprover']);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('kode_produksi', 'LIKE', '%' . $search . '%')
                  ->orWhereHas('produk', function($qp) use ($search) {
                      $qp->where('nama_produk', 'LIKE', '%' . $search . '%');
                  })
                  ->orWhere('tanggal', 'LIKE', '%' . $search . '%');
            });
        }
        
        $items = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('qc-sistem.pemasakan_nasi.index', compact('items', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        
        if ($user->role === 'superadmin') {
            $products = JenisProduk::orderBy('nama_produk')->get();
            $shifts = DataShift::orderBy('shift')->get();
        } else {
            $products = JenisProduk::where('id_plan', $user->id_plan)
                ->orderBy('nama_produk')
                ->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)
                ->orderBy('shift')
                ->get();
        }

        return view('qc-sistem.pemasakan_nasi.create', compact('products', 'shifts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $isSpecialRole = ($user->id_role == 2 || $user->id_role == 3);

        if($isSpecialRole){

            $request->validate([
                'id_produk' => 'required|exists:jenis_produk,id',
                'shift_id' => 'required|exists:data_shift,id',
                'tanggal' => 'required|date_format:d-m-Y', 
                 'jam' => 'required',
                'kode_produksi' => 'required|string|max:255',
                'waktu_start' => 'required|string',
                'waktu_stop' => 'required|string',
                'proses' => 'required|string|max:255',
                'waktu' => 'required|string|max:255',
                'jenis_bahan' => 'required|array|min:1',
                'jenis_bahan.*' => 'required|string|max:255',
                'jumlah' => 'required|array|min:1',
                'jumlah.*' => 'required|string|max:255',
                'sensori_kondisi' => 'required|in:OK,Tidak OK',
                'status_cooking' => 'required|boolean',
                'lama_proses' => 'required|string|max:255',
                'temp_std_1' => 'required|string|max:255',
                'temp_std_2' => 'required|string|max:255',
                'temp_std_3' => 'required|string|max:255',
                'organo_warna' => 'required|in:OK,Tidak OK',
                'organo_aroma' => 'required|in:OK,Tidak OK',
                'organo_rasa' => 'required|in:OK,Tidak OK',
                'organo_tekstur' => 'required|in:OK,Tidak OK',
                'catatan' => 'nullable|string'
            ]);
        } else{
              $request->validate([
                'id_produk' => 'required|exists:jenis_produk,id',
                'shift_id' => 'required|exists:data_shift,id',
                 'tanggal' => 'required|date_format:d-m-Y H:i:s',
                 'jam' => 'required',
                'kode_produksi' => 'required|string|max:255',
                'waktu_start' => 'required|string',
                'waktu_stop' => 'required|string',
                'proses' => 'required|string|max:255',
                'waktu' => 'required|string|max:255',
                'jenis_bahan' => 'required|array|min:1',
                'jenis_bahan.*' => 'required|string|max:255',
                'jumlah' => 'required|array|min:1',
                'jumlah.*' => 'required|string|max:255',
                'sensori_kondisi' => 'required|in:OK,Tidak OK',
                'status_cooking' => 'required|boolean',
                'lama_proses' => 'required|string|max:255',
                'temp_std_1' => 'required|string|max:255',
                'temp_std_2' => 'required|string|max:255',
                'temp_std_3' => 'required|string|max:255',
                'organo_warna' => 'required|in:OK,Tidak OK',
                'organo_aroma' => 'required|in:OK,Tidak OK',
                'organo_rasa' => 'required|in:OK,Tidak OK',
                'organo_tekstur' => 'required|in:OK,Tidak OK',
                'catatan' => 'nullable|string'
            ]);
        }


        $data = $request->all();
      
        $tanggalData = $request->tanggal;
    if ($isSpecialRole) {
        // Untuk user dengan role 2 atau 3, gunakan format tanggal dari request tapi waktu dari now()
        $dateOnly = \Carbon\Carbon::createFromFormat('d-m-Y', $request->tanggal)->format('Y-m-d');
        $timeNow = now()->format('H:i:s');
        $tanggalData = $dateOnly . ' ' . $timeNow;
    } else {
        // Untuk user lain, gunakan format tanggal dan waktu dari request
        $tanggalData = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s');
    }
      $data['id_plan'] = $user->id_plan;
        $data['user_id'] = $user->id;
        $data['tanggal']=$tanggalData;
        $data['jam']=$request->jam;
        PemasakanNasi::create($data);

        return redirect()->route('pemasakan-nasi.index')
            ->with('success', 'Data pemasakan nasi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $pemasakanNasi = PemasakanNasi::where('uuid', $uuid)->firstOrFail();
        $pemasakanNasi->load(['plan', 'user', 'shift', 'produk']);
        return view('qc-sistem.pemasakan_nasi.show', compact('pemasakanNasi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $pemasakanNasi = PemasakanNasi::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        
        // Authorization check
        if ($user->role !== 'superadmin' && $pemasakanNasi->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized action.');
        }

        if ($user->role === 'superadmin') {
            $products = JenisProduk::orderBy('nama_produk')->get();
            $shifts = DataShift::orderBy('shift')->get();
        } else {
            $products = JenisProduk::where('id_plan', $user->id_plan)
                ->orderBy('nama_produk')
                ->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)
                ->orderBy('shift')
                ->get();
        }

        return view('qc-sistem.pemasakan_nasi.edit', compact('pemasakanNasi', 'products', 'shifts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $pemasakanNasi = PemasakanNasi::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        
        // Authorization check
        if ($user->role !== 'superadmin' && $pemasakanNasi->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
            'kode_produksi' => 'required|string|max:255',
            'waktu_start' => 'required|string',
            'waktu_stop' => 'required|string',
            'proses' => 'required|string|max:255',
            'waktu' => 'required|string|max:255',
            'jenis_bahan' => 'required|array|min:1',
            'jenis_bahan.*' => 'required|string|max:255',
            'jumlah' => 'required|array|min:1',
            'jumlah.*' => 'required|string|max:255',
            'sensori_kondisi' => 'required|in:OK,Tidak OK',
            'status_cooking' => 'required|boolean',
            'lama_proses' => 'required|string|max:255',
            'temp_std_1' => 'required|string|max:255',
            'temp_std_2' => 'required|string|max:255',
            'temp_std_3' => 'required|string|max:255',
            'organo_warna' => 'required|in:OK,Tidak OK',
            'organo_aroma' => 'required|in:OK,Tidak OK',
            'organo_rasa' => 'required|in:OK,Tidak OK',
            'organo_tekstur' => 'required|in:OK,Tidak OK',
            'catatan' => 'nullable|string'
        ]);

        $data = $request->all();
        $data['id_plan'] = $user->id_plan;
        $data['user_id'] = $user->id;

        $pemasakanNasi->update($data);

        return redirect()->route('pemasakan-nasi.index')
            ->with('success', 'Data pemasakan nasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $pemasakanNasi = PemasakanNasi::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        
        // Authorization check
        if ($user->role !== 'superadmin' && $pemasakanNasi->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized action.');
        }

        $pemasakanNasi->delete();

        return redirect()->route('pemasakan-nasi.index')
            ->with('success', 'Data pemasakan nasi berhasil dihapus.');
    }
    /**
    * Show logs for the specified resource.
    */
    public function showLogs($uuid)
    {
        $item = PemasakanNasi::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();

        // Authorization check
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized action.');
        }

        $logs = PemasakanNasiLog::where('pemasakan_nasi_id', $item->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('qc-sistem.pemasakan_nasi.logs', compact('item', 'logs'));
    }

    /**
    * Get logs JSON for the specified resource.
    */
    public function getLogsJson($uuid)
    {
        $item = PemasakanNasi::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();

        // Authorization check
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $logs = PemasakanNasiLog::where('pemasakan_nasi_id', $item->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($logs);
    }

    /**
     * Approve data with role-based validation
     */
    public function approve(Request $request, $uuid)
    {
        $request->validate([
            'type' => 'required|in:qc,produksi,spv'
        ]);

        $pemasakanNasi = PemasakanNasi::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        $type = $request->type;

        // Role-based access control following Produk Forming pattern
        $allowedRoles = [
            'qc' => [1, 3, 5], // Role 1&5 (QC only), Role 3 (QC only)
            'produksi' => [2], // Role 2 (produksi only)
            'spv' => [4] // Role 4 (SPV only)
        ];

        if (!in_array($user->id_role, $allowedRoles[$type])) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk melakukan approval ini.'
            ], 403);
        }

        // Sequential approval validation (QC → Produksi → SPV)
        if ($type === 'produksi' && !$pemasakanNasi->approved_by_qc) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh QC terlebih dahulu.'
            ], 400);
        }

        if ($type === 'spv' && !$pemasakanNasi->approved_by_produksi) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh Produksi terlebih dahulu.'
            ], 400);
        }

        // Check if already approved
        $approvalField = "approved_by_{$type}";
        if ($pemasakanNasi->$approvalField) {
            return response()->json([
                'success' => false,
                'message' => 'Data sudah disetujui sebelumnya.'
            ], 400);
        }

        // Update approval
        $pemasakanNasi->update([
            $approvalField => true,
            "{$type}_approved_by" => $user->id,
            "{$type}_approved_at" => now()
        ]);

        // Log the approval activity
        PemasakanNasiLog::create([
            'pemasakan_nasi_id' => $pemasakanNasi->id,
            'pemasakan_nasi_uuid' => $pemasakanNasi->uuid,
            'user_name' => $user->name,
            'user_role' => $user->role ?? 'Unknown',
            'aksi' => 'approved',
            'field_yang_diubah' => [$approvalField, "{$type}_approved_by", "{$type}_approved_at"],
            'nilai_lama' => [
                $approvalField => false,
                "{$type}_approved_by" => null,
                "{$type}_approved_at" => null
            ],
            'nilai_baru' => [
                $approvalField => true,
                "{$type}_approved_by" => $user->id,
                "{$type}_approved_at" => now()->toDateTimeString()
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'keterangan' => "Data disetujui oleh {$type}: {$user->name}"
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disetujui.'
        ]);
    }

    /**
     * Bulk export PDF with filters
     */
    public function bulkExportPdf(Request $request)
    {
        $request->validate([
            'tanggal' => 'nullable|date',
            'shift' => 'nullable|string',
            'produk' => 'nullable|string',
            'kode_form' => 'required|string|max:50'
        ]);

        $user = auth()->user();
        
        // Build query with approval relationships
        $query = PemasakanNasi::with(['plan', 'user', 'shift', 'produk', 'qcApprover', 'produksiApprover', 'spvApprover']);
        
        // Filter berdasarkan plan user jika bukan superadmin
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        // Apply filters
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }
        
        if ($request->filled('shift')) {
            $query->whereHas('shift', function($q) use ($request) {
                $q->where('shift', $request->shift);
            });
        }
        
        if ($request->filled('produk')) {
            $query->whereHas('produk', function($q) use ($request) {
                $q->where('nama_produk', $request->produk);
            });
        }

        $data = $query->orderBy('tanggal', 'desc')->get();

        if ($data->isEmpty()) {
            return response('<div style="text-align: center; margin-top: 50px;"><h3>Tidak ada data yang ditemukan dengan filter yang diterapkan.</h3></div>', 404);
        }

        // Update kode_form for matching records
        $kodeForm = $request->kode_form;
        $dataIds = $data->pluck('id')->toArray();
        
        PemasakanNasi::whereIn('id', $dataIds)->update([
            'kode_form' => $kodeForm
        ]);

        // Get plan information
        $plan = $user->role === 'superadmin' ? 
            (isset($data[0]) ? $data[0]->plan : null) : 
            $user->plan;

        // Prepare filters for display
        $filters = [
            'tanggal' => $request->tanggal,
            'shift' => $request->shift,
            'produk' => $request->produk,
            'kode_form' => $kodeForm
        ];

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('qc-sistem.pemasakan_nasi.export_pdf', compact('data', 'plan', 'filters'));
        $pdf->setPaper('A4', 'landscape');
        
        $filename = 'pemasakan-nasi-' . date('Y-m-d-H-i-s') . '.pdf';
        
        return $pdf->download($filename);
    }
        
}
