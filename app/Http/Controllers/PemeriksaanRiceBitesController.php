<?php

namespace App\Http\Controllers;

use App\Models\PemeriksaanRiceBites;
use App\Models\JenisProduk;
use App\Models\DataShift;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\PemeriksaanRiceBitesLog;

class PemeriksaanRiceBitesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        $query = PemeriksaanRiceBites::with(['plan', 'user', 'shift', 'produk', 'qcApprover', 'produksiApprover', 'spvApprover']);
        
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $search = request('search');
        if (!empty($search)) {
            $query->whereHas('produk', function ($produkQuery) use ($search) {
                $produkQuery->where('nama_produk', 'like', '%' . $search . '%');
            });
        }

        $perPage = request()->get('per_page', 10);
        $perPage = in_array($perPage, [5, 10, 25, 50, 100]) ? $perPage : 10;

        $data = $query->orderBy('created_at', 'desc')->paginate($perPage);
        
        return view('qc-sistem.pemeriksaan_rice_bites.index', compact('data', 'search', 'perPage'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Get shifts filtered by user's plan
        $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        
        // Get products filtered by user's plan
        $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
        
        return view('qc-sistem.pemeriksaan_rice_bites.create', compact('shifts', 'produks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       $user = Auth::user();
    $isSpecialRole = ($user->id_role == 2 || $user->id_role == 3);
    if($isSpecialRole){
          $request->validate([
            'tanggal' => 'required|date_format:d-m-Y',
              'jam' => 'required',
            'shift_id' => 'required|exists:data_shift,id',
            'id_produk' => 'required|exists:jenis_produk,id',
            'batch' => 'required|string|max:255',
            'no_cooking_cycle' => 'required|string|max:255',
            'bahan_baku' => 'required|array|min:1',
            'bahan_baku.*.nama' => 'required|string|max:255',
            'bahan_baku.*.berat' => 'nullable|string|max:255',
            'bahan_baku.*.suhu' => 'nullable|string|max:255',
            'bahan_baku.*.kondisi' => 'nullable|string|max:255',
            'premix' => 'required|array|min:1',
            'premix.*.nama' => 'required|string|max:255',
            'premix.*.berat' => 'nullable|string|max:255',
            'premix.*.kondisi' => 'nullable|string|max:255',
            'parameter_nitrogen' => 'nullable|string|max:255',
            'jumlah_inject_nitrogen' => 'nullable|string|max:255',
            'rpm_cooking_cattle' => 'nullable|string|max:255',
            'cold_mixing' => 'nullable|string|max:255',
            'suhu_aktual_adonan' => 'nullable|array',
            'suhu_aktual_adonan.*' => 'nullable|string|max:255',
            'suhu_adonan_pencampuran' => 'nullable|array',
            'suhu_adonan_pencampuran.*' => 'nullable|string|max:255',
            'rata_rata_suhu' => 'nullable|numeric',
            'hasil_pencampuran' => 'nullable|in:OK,Tidak OK',
            'catatan' => 'nullable|string',
        ]);

    } else{
          $request->validate([
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
              'jam' => 'required',
            'shift_id' => 'required|exists:data_shift,id',
            'id_produk' => 'required|exists:jenis_produk,id',
            'batch' => 'required|string|max:255',
            'no_cooking_cycle' => 'required|string|max:255',
            'bahan_baku' => 'required|array|min:1',
            'bahan_baku.*.nama' => 'required|string|max:255',
            'bahan_baku.*.berat' => 'nullable|string|max:255',
            'bahan_baku.*.suhu' => 'nullable|string|max:255',
            'bahan_baku.*.kondisi' => 'nullable|string|max:255',
            'premix' => 'required|array|min:1',
            'premix.*.nama' => 'required|string|max:255',
            'premix.*.berat' => 'nullable|string|max:255',
            'premix.*.kondisi' => 'nullable|string|max:255',
            'parameter_nitrogen' => 'nullable|string|max:255',
            'jumlah_inject_nitrogen' => 'nullable|string|max:255',
            'rpm_cooking_cattle' => 'nullable|string|max:255',
            'cold_mixing' => 'nullable|string|max:255',
            'suhu_aktual_adonan' => 'nullable|array',
            'suhu_aktual_adonan.*' => 'nullable|string|max:255',
            'suhu_adonan_pencampuran' => 'nullable|array',
            'suhu_adonan_pencampuran.*' => 'nullable|string|max:255',
            'rata_rata_suhu' => 'nullable|numeric',
            'hasil_pencampuran' => 'nullable|in:OK,Tidak OK',
            'catatan' => 'nullable|string',
        ]);
    }
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

        try {
            $user = Auth::user();
            
            PemeriksaanRiceBites::create([
                'uuid' => Str::uuid(),
                'id_plan' => $user->id_plan,
                'user_id' => $user->id,
                'shift_id' => $request->shift_id,
                'id_produk' => $request->id_produk,
                'tanggal' => $tanggalData,
                'jam'  => $request->jam,
                'batch' => $request->batch,
                'no_cooking_cycle' => $request->no_cooking_cycle,
                'bahan_baku' => $request->bahan_baku,
                'premix' => $request->premix,
                'parameter_nitrogen' => $request->parameter_nitrogen,
                'jumlah_inject_nitrogen' => $request->jumlah_inject_nitrogen,
                'rpm_cooking_cattle' => $request->rpm_cooking_cattle,
                'cold_mixing' => $request->cold_mixing,
                'suhu_aktual_adonan' => $request->suhu_aktual_adonan,
                'suhu_adonan_pencampuran' => $request->suhu_adonan_pencampuran,
                'rata_rata_suhu' => $request->rata_rata_suhu,
                'hasil_pencampuran' => $request->hasil_pencampuran,
                'catatan' => $request->catatan,
            ]);

            return redirect()->route('pemeriksaan-rice-bites.index')
                           ->with('success', 'Data pemeriksaan rice bites berhasil ditambahkan.');
                           
        } catch (\Exception $e) {
            Log::error('Error creating pemeriksaan rice bites: ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $user = Auth::user();
        
        $query = PemeriksaanRiceBites::with(['plan', 'user', 'shift', 'produk'])
                                   ->where('uuid', $uuid);
        
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        
        $data = $query->firstOrFail();
        
        return view('qc-sistem.pemeriksaan_rice_bites.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $user = Auth::user();
        
        $query = PemeriksaanRiceBites::where('uuid', $uuid);
        
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        
        $data = $query->firstOrFail();
        
        // Get shifts filtered by user's plan
        $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        
        // Get products filtered by user's plan
        $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
        
        return view('qc-sistem.pemeriksaan_rice_bites.edit', compact('data', 'shifts', 'produks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'shift_id' => 'required|exists:data_shift,id',
            'id_produk' => 'required|exists:jenis_produk,id',
            'batch' => 'required|string|max:255',
            'no_cooking_cycle' => 'required|string|max:255',
            'bahan_baku' => 'required|array|min:1',
            'bahan_baku.*.nama' => 'required|string|max:255',
            'bahan_baku.*.berat' => 'nullable|string|max:255',
            'bahan_baku.*.suhu' => 'nullable|string|max:255',
            'bahan_baku.*.kondisi' => 'nullable|string|max:255',
            'premix' => 'required|array|min:1',
            'premix.*.nama' => 'required|string|max:255',
            'premix.*.berat' => 'nullable|string|max:255',
            'premix.*.kondisi' => 'nullable|string|max:255',
            'parameter_nitrogen' => 'nullable|string|max:255',
            'jumlah_inject_nitrogen' => 'nullable|string|max:255',
            'rpm_cooking_cattle' => 'nullable|string|max:255',
            'cold_mixing' => 'nullable|string|max:255',
            'suhu_aktual_adonan' => 'nullable|array',
            'suhu_aktual_adonan.*' => 'nullable|string|max:255',
            'suhu_adonan_pencampuran' => 'nullable|array',
            'suhu_adonan_pencampuran.*' => 'nullable|string|max:255',
            'rata_rata_suhu' => 'nullable|numeric',
            'hasil_pencampuran' => 'nullable|in:OK,Tidak OK',
            'catatan' => 'nullable|string',
        ]);

        try {
            $user = Auth::user();
            
            $query = PemeriksaanRiceBites::where('uuid', $uuid);
            
            if ($user->role !== 'superadmin') {
                $query->where('id_plan', $user->id_plan);
            }
            
            $data = $query->firstOrFail();
            
            $data->update([
                'shift_id' => $request->shift_id,
                'id_produk' => $request->id_produk,
                'tanggal' => $request->tanggal,
                'batch' => $request->batch,
                'no_cooking_cycle' => $request->no_cooking_cycle,
                'bahan_baku' => $request->bahan_baku,
                'premix' => $request->premix,
                'parameter_nitrogen' => $request->parameter_nitrogen,
                'jumlah_inject_nitrogen' => $request->jumlah_inject_nitrogen,
                'rpm_cooking_cattle' => $request->rpm_cooking_cattle,
                'cold_mixing' => $request->cold_mixing,
                'suhu_aktual_adonan' => $request->suhu_aktual_adonan,
                'suhu_adonan_pencampuran' => $request->suhu_adonan_pencampuran,
                'rata_rata_suhu' => $request->rata_rata_suhu,
                'hasil_pencampuran' => $request->hasil_pencampuran,
                'catatan' => $request->catatan,
            ]);

            return redirect()->route('pemeriksaan-rice-bites.index')
                           ->with('success', 'Data pemeriksaan rice bites berhasil diperbarui.');
                           
        } catch (\Exception $e) {
            Log::error('Error updating pemeriksaan rice bites: ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        try {
            $user = Auth::user();
            
            $query = PemeriksaanRiceBites::where('uuid', $uuid);
            
            if ($user->role !== 'superadmin') {
                $query->where('id_plan', $user->id_plan);
            }
            
            $data = $query->firstOrFail();
            $data->delete();

            return redirect()->route('pemeriksaan-rice-bites.index')
                           ->with('success', 'Data pemeriksaan rice bites berhasil dihapus.');
                           
        } catch (\Exception $e) {
            Log::error('Error deleting pemeriksaan rice bites: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

    /**
     * Tampilkan halaman logs untuk pemeriksaan rice bites
     */
    public function showLogs($uuid)
    {
        $item = PemeriksaanRiceBites::where('uuid', $uuid)->firstOrFail();
        
        $logs = PemeriksaanRiceBitesLog::where('pemeriksaan_rice_bites_id', $item->id)
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->simplePaginate(5);
        
        return view('qc-sistem.pemeriksaan_rice_bites.logs', compact('item', 'logs'));
    }

    /**
     * Get logs data untuk DataTables (jika diperlukan)
     */
    public function getLogsJson($uuid)
    {
        $pemeriksaanRiceBites = PemeriksaanRiceBites::where('uuid', $uuid)->firstOrFail();
        
        $logs = PemeriksaanRiceBitesLog::where('pemeriksaan_rice_bites_uuid', $uuid)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($log) {
                return [
                    'tanggal' => $log->created_at->format('d/m/Y H:i:s'),
                    'user' => $log->user_name,
                    'field' => $log->nama_field,
                    'perubahan' => $log->deskripsi_perubahan,
                    'ip_address' => $log->ip_address
                ];
            });

        return response()->json(['data' => $logs]);
    }
    /**
     * Verify data by QC
     */
    public function verifyQC($uuid)
    {
        try {
            $user = Auth::user();
            
            // Check if user has permission (Spv or admin)
            if (!in_array($user->role, ['Spv', 'admin'])) {
                return redirect()->back()
                               ->with('error', 'Anda tidak memiliki izin untuk melakukan verifikasi QC.');
            }
            
            $query = PemeriksaanRiceBites::where('uuid', $uuid);
            
            if ($user->role !== 'superadmin') {
                $query->where('id_plan', $user->id_plan);
            }
            
            $data = $query->firstOrFail();
            
            // Check if already verified
            if ($data->diverifikasi_qc_status == 1) {
                return redirect()->back()
                               ->with('info', 'Data sudah diverifikasi oleh QC sebelumnya.');
            }
            
            $data->update([
                'diverifikasi_qc' => 'Sudah diverifikasi oleh QC',
                'diverifikasi_qc_status' => 1,
            ]);

            return redirect()->back()
                           ->with('success', 'Data berhasil diverifikasi oleh QC.');
                           
        } catch (\Exception $e) {
            Log::error('Error verifying QC pemeriksaan rice bites: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan saat melakukan verifikasi QC.');
        }
    }

    /**
     * Acknowledge data by Production
     */
    public function acknowledgeProduksi($uuid)
    {
        try {
            $user = Auth::user();
            
            // Check if user has permission (Fm/fl, admin, or Spv)
            if (!in_array($user->role, ['Fm/fl', 'admin', 'Spv'])) {
                return redirect()->back()
                               ->with('error', 'Anda tidak memiliki izin untuk menandai sebagai diketahui produksi.');
            }
            
            $query = PemeriksaanRiceBites::where('uuid', $uuid);
            
            if ($user->role !== 'superadmin') {
                $query->where('id_plan', $user->id_plan);
            }
            
            $data = $query->firstOrFail();
            
            // Check if already acknowledged
            if ($data->diketahui_produksi_status == 1) {
                return redirect()->back()
                               ->with('info', 'Data sudah diketahui oleh produksi sebelumnya.');
            }
            
            $data->update([
                'diketahui_produksi' => 'Sudah diketahui oleh produksi',
                'diketahui_produksi_status' => 1,
            ]);

            return redirect()->back()
                           ->with('success', 'Data berhasil ditandai sebagai diketahui oleh produksi.');
                           
        } catch (\Exception $e) {
            Log::error('Error acknowledging production pemeriksaan rice bites: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan saat menandai sebagai diketahui produksi.');
        }
    }

    /**
     * Approve data with role-based validation
     */
    public function approve(Request $request, $uuid)
    {
        $request->validate([
            'type' => 'required|in:qc,produksi,spv'
        ]);

        $data = PemeriksaanRiceBites::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        $type = $request->type;

        // Role-based access control following ProdukForming pattern
        $allowedRoles = [
            'qc' => [1, 3, 5], // Role 1&5 (all buttons), Role 3 (QC only)
            'produksi' => [1, 2, 5], // Role 1&5 (all buttons), Role 2 (produksi only)
            'spv' => [1, 4, 5] // Role 1&5 (all buttons), Role 4 (SPV only)
        ];

        if (!in_array($user->id_role, $allowedRoles[$type])) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk melakukan approval ini.'
            ], 403);
        }

        // Sequential approval validation (QC → Produksi → SPV)
        if ($type === 'produksi' && !$data->approved_by_qc) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh QC terlebih dahulu.'
            ], 400);
        }

        if ($type === 'spv' && !$data->approved_by_produksi) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh Produksi terlebih dahulu.'
            ], 400);
        }

        // Check if already approved
        $approvalField = "approved_by_{$type}";
        if ($data->$approvalField) {
            return response()->json([
                'success' => false,
                'message' => 'Data sudah disetujui sebelumnya.'
            ], 400);
        }

        // Update approval
        $data->update([
            $approvalField => true,
            "{$type}_approved_by" => $user->id,
            "{$type}_approved_at" => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disetujui.'
        ]);
    }

    /**
     * Export filtered data to PDF
     */
    public function bulkExportPdf(Request $request)
    {
        $request->validate([
            'tanggal' => 'nullable|date',
            'shift' => 'nullable|string',
            'produk' => 'nullable|string',
            'kode_form' => 'required|string'
        ]);

        $user = Auth::user();
        $query = PemeriksaanRiceBites::with(['produk', 'plan', 'shift', 'user', 'qcApprover', 'produksiApprover', 'spvApprover']);
        
        // Filter berdasarkan plan user jika bukan superadmin
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        
        // Apply filters
        if ($request->tanggal) {
            $query->whereDate('tanggal', $request->tanggal);
        }
        
        if ($request->shift) {
            $query->whereHas('shift', function($q) use ($request) {
                $q->where('shift', $request->shift);
            });
        }
        
        if ($request->produk) {
            $query->whereHas('produk', function($q) use ($request) {
                $q->where('nama_produk', $request->produk);
            });
        }
        
        // Debug: Log the query and filters
        \Log::info('PDF Export Query Debug', [
            'tanggal' => $request->tanggal,
            'shift' => $request->shift,
            'produk' => $request->produk,
            'kode_form' => $request->kode_form,
            'user_plan' => $user->id_plan,
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);
        
        // First, update kode_form for matching records before fetching data for PDF
        if ($request->kode_form) {
            $query_for_update = PemeriksaanRiceBites::query();
            
            // Apply same filters for update
            if ($user->role !== 'superadmin') {
                $query_for_update->where('id_plan', $user->id_plan);
            }
            
            if ($request->tanggal) {
                $query_for_update->whereDate('tanggal', $request->tanggal);
            }
            
            if ($request->shift) {
                $query_for_update->whereHas('shift', function($q) use ($request) {
                    $q->where('shift', $request->shift);
                });
            }
            
            if ($request->produk) {
                $query_for_update->whereHas('produk', function($q) use ($request) {
                    $q->where('nama_produk', $request->produk);
                });
            }
            
            // Update kode_form for matching records BEFORE fetching data
            $updated_count = $query_for_update->update(['kode_form' => $request->kode_form]);
            \Log::info('Updated kode_form for records', ['count' => $updated_count, 'kode_form' => $request->kode_form]);
        }
        
        // Now fetch the updated data for PDF
        $data = $query->orderBy('tanggal', 'desc')->get();
        
        \Log::info('PDF Export Data Count', ['count' => $data->count()]);
        
        // Jika tidak ada data, tampilkan halaman error HTML (tidak download PDF)
        if ($data->isEmpty()) {
            $errorMessage = 'Tidak ada data Pemeriksaan Rice Bites yang sesuai dengan filter yang dipilih.';
            $filterInfo = [];
            
            if ($request->tanggal) {
                $filterInfo[] = 'Tanggal: ' . \Carbon\Carbon::parse($request->tanggal)->format('d-m-Y');
            }
            if ($request->shift) {
                $filterInfo[] = 'Shift: ' . $request->shift;
            }
            if ($request->produk) {
                $filterInfo[] = 'Produk: ' . $request->produk;
            }
            if ($request->kode_form) {
                $filterInfo[] = 'Kode Form: ' . $request->kode_form;
            }
            
            $html = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>Data Tidak Ditemukan</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 40px; text-align: center; }
                    .container { max-width: 600px; margin: 0 auto; padding: 40px; border: 2px dashed #ccc; background-color: #f9f9f9; }
                    h1 { color: #d9534f; margin-bottom: 20px; }
                    .message { font-size: 16px; color: #666; margin-bottom: 30px; }
                    .filter-info { background-color: #fff; padding: 20px; border-radius: 5px; margin: 20px 0; }
                    .filter-info h4 { margin-top: 0; color: #333; }
                    .filter-info ul { text-align: left; }
                </style>
            </head>
            <body>
                <div class="container">
                    <h1>Data Pemeriksaan Rice Bites Tidak Ditemukan</h1>
                    <p class="message">' . $errorMessage . '</p>';
                    
            if (!empty($filterInfo)) {
                $html .= '
                    <div class="filter-info">
                        <h4>Filter yang digunakan:</h4>
                        <ul>';
                foreach ($filterInfo as $info) {
                    $html .= '<li>' . $info . '</li>';
                }
                $html .= '
                        </ul>
                    </div>';
            }
            
            $html .= '
                </div>
            </body>
            </html>';
            
            return response($html)->header('Content-Type', 'text/html');
        }

        // Prepare filter info for PDF (include kode_form for identification)
        $filterInfo = [
            'tanggal' => $request->tanggal ? \Carbon\Carbon::parse($request->tanggal)->format('d-m-Y') : 'Semua Tanggal',
            'shift' => $request->shift ?: 'Semua Shift',
            'produk' => $request->produk ?: 'Semua Produk',
            'kode_form' => $request->kode_form
        ];
        
        $pdf = \PDF::loadView('qc-sistem.pemeriksaan_rice_bites.export_pdf', compact('data', 'filterInfo'))
                   ->setPaper('letter', 'portrait');
        
        $filename = 'pemeriksaan-rice-bites-' . date('Y-m-d-H-i-s') . '.pdf';
        
        return $pdf->download($filename);
    }
}
