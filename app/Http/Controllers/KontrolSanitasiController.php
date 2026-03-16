<?php

namespace App\Http\Controllers;

use App\Models\KontrolSanitasi;
use App\Models\DataShift;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\KontrolSanitasiLog;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KontrolSanitasiTemplateExport;
use App\Imports\KontrolSanitasiImport;
use Illuminate\Support\Facades\Validator;

class KontrolSanitasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        
        $query = KontrolSanitasi::with(['user', 'plan', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover']);
        
        // Role-based data filtering
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('tanggal', 'LIKE', '%' . $search . '%');
            });
        }
        
        $kontrolSanitasi = $query->orderBy('tanggal', 'desc')->paginate(10);

        return view('qc-sistem.kontrol_sanitasi.index', compact('kontrolSanitasi', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Get shifts based on user role
        if ($user->role === 'superadmin') {
            $shifts = DataShift::with('plan')->get();
            $plans = Plan::all();
        } else {
            $shifts = DataShift::with('plan')
                ->where('id_plan', $user->id_plan)
                ->get();
            $plans = Plan::where('id', $user->id_plan)->get();
        }

        return view('qc-sistem.kontrol_sanitasi.create', compact('shifts', 'plans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
            'jam' => 'required|date_format:H:i',
            'suhu_air' => 'required|string|max:255',
            'kadar_klorin_food_basin' => 'required|string|max:255',
            'kadar_klorin_hand_basin' => 'required|string|max:255',
            'hasil_verifikasi' => 'required|string|max:500',
        ]);

        // Get the selected shift to validate plan access and get plan_id
        $selectedShift = DataShift::findOrFail($request->shift_id);
        
        // Additional validation for non-superadmin users
        if ($user->role !== 'superadmin') {
            if ($selectedShift->id_plan !== $user->id_plan) {
                return back()->withErrors(['shift_id' => 'Anda tidak memiliki akses ke shift ini.']);
            }
        }

        KontrolSanitasi::create([
            'user_id' => $user->id, // Auto-fill dari controller
            'id_plan' => $selectedShift->id_plan, // Auto-fill dari shift yang dipilih
            'shift_id' => $request->shift_id,
            'tanggal' => $request->tanggal,
            'jam' => $request->jam,
            'suhu_air' => $request->suhu_air,
            'kadar_klorin_food_basin' => $request->kadar_klorin_food_basin,
            'kadar_klorin_hand_basin' => $request->kadar_klorin_hand_basin,
            'hasil_verifikasi' => $request->hasil_verifikasi,
        ]);

        return redirect()->route('kontrol-sanitasi.index')
            ->with('success', 'Data kontrol sanitasi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $kontrolSanitasi = KontrolSanitasi::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check access for non-superadmin users
        if ($user->role !== 'superadmin' && $kontrolSanitasi->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return view('qc-sistem.kontrol_sanitasi.show', compact('kontrolSanitasi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $kontrolSanitasi = KontrolSanitasi::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check access for non-superadmin users
        if ($user->role !== 'superadmin' && $kontrolSanitasi->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        // Get shifts based on user role
        if ($user->role === 'superadmin') {
            $shifts = DataShift::with('plan')->get();
            $plans = Plan::all();
        } else {
            $shifts = DataShift::with('plan')
                ->where('id_plan', $user->id_plan)
                ->get();
            $plans = Plan::where('id', $user->id_plan)->get();
        }

        return view('qc-sistem.kontrol_sanitasi.edit', compact('kontrolSanitasi', 'shifts', 'plans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $kontrolSanitasi = KontrolSanitasi::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check access for non-superadmin users
        if ($user->role !== 'superadmin' && $kontrolSanitasi->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        $request->validate([
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
            'suhu_air' => 'required|string|max:255',
            'kadar_klorin_food_basin' => 'required|string|max:255',
            'kadar_klorin_hand_basin' => 'required|string|max:255',
            'hasil_verifikasi' => 'required|string|max:500',
        ]);

        // Get the selected shift to validate plan access
        $selectedShift = DataShift::findOrFail($request->shift_id);
        
        // Additional validation for non-superadmin users
        if ($user->role !== 'superadmin') {
            if ($selectedShift->id_plan !== $user->id_plan) {
                return back()->withErrors(['shift_id' => 'Anda tidak memiliki akses ke shift ini.']);
            }
        }

        $kontrolSanitasi->update([
            'id_plan' => $selectedShift->id_plan, // Update plan sesuai shift
            'shift_id' => $request->shift_id,
            'tanggal' => $request->tanggal,
            'suhu_air' => $request->suhu_air,
            'kadar_klorin_food_basin' => $request->kadar_klorin_food_basin,
            'kadar_klorin_hand_basin' => $request->kadar_klorin_hand_basin,
            'hasil_verifikasi' => $request->hasil_verifikasi,
        ]);

        return redirect()->route('kontrol-sanitasi.index')
            ->with('success', 'Data kontrol sanitasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $kontrolSanitasi = KontrolSanitasi::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check access for non-superadmin users
        if ($user->role !== 'superadmin' && $kontrolSanitasi->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        $kontrolSanitasi->delete();

        return redirect()->route('kontrol-sanitasi.index')
            ->with('success', 'Data kontrol sanitasi berhasil dihapus.');
    }
    /**
     * Show logs for the specified resource.
     */
    public function showLogs($uuid)
    {
        $item = KontrolSanitasi::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        
        // Authorization check
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized action.');
        }

        $logs = KontrolSanitasiLog::where('kontrol_sanitasi_id', $item->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('qc-sistem.kontrol_sanitasi.logs', compact('item', 'logs'));
    }

    /**
     * Get logs JSON for the specified resource.
     */
    public function getLogsJson($uuid)
    {
        $item = KontrolSanitasi::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        
        // Authorization check
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $logs = KontrolSanitasiLog::where('kontrol_sanitasi_id', $item->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($logs);
    }
    /**
     * Export PDF with filters
     */
    public function bulkExportPdf(Request $request)
    {
        try {
            $request->validate([
                'tanggal' => 'nullable|date',
                'shift_id' => 'nullable|integer',
                'kode_form' => 'required|string|max:50'
            ]);

            $user = auth()->user();
            
            $query = KontrolSanitasi::with(['plan', 'user', 'shift'])
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                });

            // Apply filters
            if ($request->tanggal) {
                $query->whereDate('tanggal', $request->tanggal);
            }
            
            if ($request->shift_id) {
                $query->where('shift_id', $request->shift_id);
            }

            $data = $query->orderBy('created_at', 'desc')->get();

            // Save kode_form to all filtered records
            if ($data->isNotEmpty()) {
                $query->update(['kode_form' => $request->kode_form]);
            }

            if ($data->isEmpty()) {
                $errorMessage = 'Tidak ada data yang sesuai dengan filter yang dipilih.';
                $filterInfo = [];
                
                if ($request->tanggal) {
                    $filterInfo[] = 'Tanggal: ' . Carbon::parse($request->tanggal)->format('d-m-Y');
                }
                if ($request->shift_id) {
                    $shift = DataShift::find($request->shift_id);
                    $filterInfo[] = 'Shift: ' . ($shift ? $shift->shift : 'Unknown');
                }
                
                $html = '
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="utf-8">
                    <title>Data Tidak Ditemukan</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 40px; text-align: center; background-color: #f8f9fa; }
                        .container { max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                        .icon { font-size: 64px; color: #ffc107; margin-bottom: 20px; }
                        h1 { color: #495057; margin-bottom: 20px; }
                        .message { color: #6c757d; margin-bottom: 30px; font-size: 16px; }
                        .filter-info { background: #e9ecef; padding: 15px; border-radius: 5px; margin: 20px 0; text-align: left; }
                        .filter-info h4 { margin: 0 0 10px 0; color: #495057; }
                        .filter-info ul { margin: 0; padding-left: 20px; }
                        .btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 20px; }
                        .btn:hover { background: #0056b3; }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <div class="icon">⚠️</div>
                        <h1>Data Tidak Ditemukan</h1>
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
                        <p style="color: #6c757d; font-size: 14px;">
                            Silakan coba dengan filter yang berbeda atau pastikan data sudah tersedia di sistem.
                        </p>
                        <a href="javascript:window.close()" class="btn">Tutup</a>
                    </div>
                </body>
                </html>';
                
                return response($html)->header('Content-Type', 'text/html');
            }

            $kode_form = $request->kode_form;
            $pdf = \PDF::loadView('qc-sistem.kontrol_sanitasi.export_pdf', compact('data', 'kode_form'));
            $pdf->setPaper('A4', 'landscape');
            
            $filename = 'kontrol_sanitasi_' . date('Y-m-d_H-i-s') . '.pdf';
            return $pdf->download($filename);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor PDF: ' . $e->getMessage()
            ], 500);
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

        $kontrolSanitasi = KontrolSanitasi::where('uuid', $uuid)->firstOrFail();
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
        if ($type === 'produksi' && !$kontrolSanitasi->approved_by_qc) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh QC terlebih dahulu.'
            ], 400);
        }

        if ($type === 'spv' && !$kontrolSanitasi->approved_by_produksi) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh Produksi terlebih dahulu.'
            ], 400);
        }

        // Check if already approved
        $approvalField = "approved_by_{$type}";
        if ($kontrolSanitasi->$approvalField) {
            return response()->json([
                'success' => false,
                'message' => 'Data sudah disetujui sebelumnya.'
            ], 400);
        }

        // Update approval
        $kontrolSanitasi->update([
            $approvalField => true,
            "{$type}_approved_by" => $user->id,
            "{$type}_approved_at" => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disetujui.'
        ]);
    }

    public function downloadTemplate()
    {
        $user = auth()->user();
        $filename = 'template_kontrol_sanitasi.xlsx';

        return Excel::download(new KontrolSanitasiTemplateExport($user), $filename);
    }

    public function importExcel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls'
        ]);

        if ($validator->fails()) {
            return redirect()->route('kontrol-sanitasi.index')->with('error', 'File yang diupload harus berupa Excel.');
        }

        $user = auth()->user();

        if (!$user || !$user->id_plan) {
            return redirect()->route('kontrol-sanitasi.index')->with('error', 'Plan user tidak ditemukan. Tidak dapat melakukan import.');
        }

        $import = new KontrolSanitasiImport($user);
        Excel::import($import, $request->file('file'));

        $insertedCount = $import->getInsertedCount();
        $importErrors = $import->getImportErrors();

        if ($insertedCount <= 0) {
            return redirect()->route('kontrol-sanitasi.index')
                ->with('warning', 'Tidak ada data valid untuk di-import. Pastikan format kolom dan master Shift sesuai.')
                ->with('import_errors', array_slice($importErrors, 0, 20));
        }

        $successMessage = 'Import data Kontrol Sanitasi berhasil. Total: ' . $insertedCount . ' baris.';
        if (!empty($importErrors)) {
            return redirect()->route('kontrol-sanitasi.index')
                ->with('success', $successMessage)
                ->with('import_errors', array_slice($importErrors, 0, 20))
                ->with('info', 'Ada beberapa baris yang gagal di-import. Silakan cek detail di bawah.');
        }

        return redirect()->route('kontrol-sanitasi.index')->with('success', $successMessage);
    }
}
