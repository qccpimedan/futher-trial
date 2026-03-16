<?php

namespace App\Http\Controllers;

use App\Models\GmpKaryawan;
use App\Models\Plan;
use App\Models\DataShift;
use App\Models\InputArea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Models\GmpKaryawanLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GmpKaryawanTemplateExport;
use App\Imports\GmpKaryawanImport;

class GmpKaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $query = GmpKaryawan::with(['plan', 'user', 'shift', 'area']);
        
        if ($user->role != 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $search = request('search');
        if ($search) {
            $query->where('nama_karyawan', 'LIKE', '%' . $search . '%');
        }
        
        $data = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('qc-sistem.gmp_karyawan.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Get plans based on user role
        if ($user->role == 'superadmin') {
            $plans = Plan::all();
            $shifts = DataShift::all();
            $areas = InputArea::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $areas = InputArea::where('id_plan', $user->id_plan)->get();
        }
        
        $temuanOptions = GmpKaryawan::getTemuanOptions();
        
        return view('qc-sistem.gmp_karyawan.create', compact('plans', 'shifts', 'areas', 'temuanOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $isSpecialRole = ($user->id_role == 2 || $user->id_role == 3);

        $validator = Validator::make($request->all(), [
            'id_area' => 'required|exists:input_area,id',
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => ['required', 'regex:/^\d{2}-\d{2}-\d{4}( \d{2}:\d{2}:\d{2})?$/'],
            'jam' => 'required|date_format:H:i',
            'nama_karyawan' => 'required|string|max:255',
            'temuan_ketidaksesuaian' => 'required|in:' . implode(',', array_keys(GmpKaryawan::getTemuanOptions())),
            'verifikasi' => 'required|in:ok,tidak_ok',
            'koreksi_lanjutan' => 'nullable|required_if:verifikasi,tidak_ok|in:ok,tidak_ok',
            'keterangan' => 'nullable|string',
            'tindakan_koreksi' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        try {
            if (preg_match('/^\d{2}-\d{2}-\d{4} \d{2}:\d{2}:\d{2}$/', (string) $request->tanggal)) {
                $tanggalData = Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal, 'Asia/Jakarta')
                    ->format('Y-m-d H:i:s');
            } else {
                $dateOnly = Carbon::createFromFormat('d-m-Y', $request->tanggal, 'Asia/Jakarta')->format('Y-m-d');
                $timeNow = now('Asia/Jakarta')->format('H:i:s');
                $tanggalData = $dateOnly . ' ' . $timeNow;
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['tanggal' => 'Format tanggal tidak valid.'])
                ->withInput();
        }

        if ($isSpecialRole && preg_match('/^\d{2}-\d{2}-\d{4} \d{2}:\d{2}:\d{2}$/', (string) $request->tanggal)) {
            try {
                $dateOnly = Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal, 'Asia/Jakarta')->format('Y-m-d');
                $timeNow = now('Asia/Jakarta')->format('H:i:s');
                $tanggalData = $dateOnly . ' ' . $timeNow;
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withErrors(['tanggal' => 'Format tanggal tidak valid.'])
                    ->withInput();
            }
        }

        $data['tanggal'] = $tanggalData;
        $data['uuid'] = Str::uuid();
        $data['user_id'] = $user->id;
        $data['id_plan'] = $user->id_plan;

        if (($data['verifikasi'] ?? null) === 'ok') {
            $data['koreksi_lanjutan'] = null;
        }

        GmpKaryawan::create($data);

        return redirect()->route('gmp-karyawan.index')
            ->with('success', 'Data GMP Karyawan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $uuid)
    {
        $gmpKaryawan = GmpKaryawan::where('uuid', $uuid)->firstOrFail();
        
        $user = Auth::user();
        if ($user->role != 'superadmin' && $gmpKaryawan->id_plan != $user->id_plan) {
            abort(403, 'Unauthorized access.');
        }
        
        return view('qc-sistem.gmp_karyawan.show', compact('gmpKaryawan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $uuid)
    {
        $gmpKaryawan = GmpKaryawan::where('uuid', $uuid)->firstOrFail();
        
        $user = Auth::user();
        if ($user->role != 'superadmin' && $gmpKaryawan->id_plan != $user->id_plan) {
            abort(403, 'Unauthorized access.');
        }
        
        // Get plans based on user role
        if ($user->role == 'superadmin') {
            $plans = Plan::all();
            $shifts = DataShift::all();
            $areas = InputArea::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $areas = InputArea::where('id_plan', $user->id_plan)->get();
        }
        $temuanOptions = GmpKaryawan::getTemuanOptions();
        
        return view('qc-sistem.gmp_karyawan.edit', compact('gmpKaryawan', 'plans', 'shifts', 'areas', 'temuanOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $uuid)
    {
        $gmpKaryawan = GmpKaryawan::where('uuid', $uuid)->firstOrFail();
        
        $user = Auth::user();
        if ($user->role != 'superadmin' && $gmpKaryawan->id_plan != $user->id_plan) {
            abort(403, 'Unauthorized access.');
        }
        
        $isSpecialRole = ($user->id_role == 2 || $user->id_role == 3);

        $validator = Validator::make($request->all(), [
            'id_area' => 'required|exists:input_area,id',
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => ['required', 'regex:/^\d{2}-\d{2}-\d{4}( \d{2}:\d{2}:\d{2})?$/'],
            'nama_karyawan' => 'required|string|max:255',
            'temuan_ketidaksesuaian' => 'required|in:' . implode(',', array_keys(GmpKaryawan::getTemuanOptions())),
            'verifikasi' => 'required|in:ok,tidak_ok',
            'koreksi_lanjutan' => 'nullable|required_if:verifikasi,tidak_ok|in:ok,tidak_ok',
            'keterangan' => 'nullable|string',
            'tindakan_koreksi' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        try {
            if (preg_match('/^\d{2}-\d{2}-\d{4} \d{2}:\d{2}:\d{2}$/', (string) $request->tanggal)) {
                $tanggalData = Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal, 'Asia/Jakarta')
                    ->format('Y-m-d H:i:s');
            } else {
                $dateOnly = Carbon::createFromFormat('d-m-Y', $request->tanggal, 'Asia/Jakarta')->format('Y-m-d');
                $timeNow = now('Asia/Jakarta')->format('H:i:s');
                $tanggalData = $dateOnly . ' ' . $timeNow;
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['tanggal' => 'Format tanggal tidak valid.'])
                ->withInput();
        }

        if ($isSpecialRole && preg_match('/^\d{2}-\d{2}-\d{4} \d{2}:\d{2}:\d{2}$/', (string) $request->tanggal)) {
            try {
                $dateOnly = Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal, 'Asia/Jakarta')->format('Y-m-d');
                $timeNow = now('Asia/Jakarta')->format('H:i:s');
                $tanggalData = $dateOnly . ' ' . $timeNow;
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withErrors(['tanggal' => 'Format tanggal tidak valid.'])
                    ->withInput();
            }
        }

        $data['tanggal'] = $tanggalData;
        // Keep original user_id and id_plan
        unset($data['user_id'], $data['id_plan']);

        if (($data['verifikasi'] ?? null) === 'ok') {
            $data['koreksi_lanjutan'] = null;
        }
        
        $gmpKaryawan->update($data);

        return redirect()->route('gmp-karyawan.index')
            ->with('success', 'Data GMP Karyawan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $uuid)
    {
        $gmpKaryawan = GmpKaryawan::where('uuid', $uuid)->firstOrFail();
        
        $user = Auth::user();
        if ($user->role != 'superadmin' && $gmpKaryawan->id_plan != $user->id_plan) {
            abort(403, 'Unauthorized access.');
        }
        
        $gmpKaryawan->delete();

        return redirect()->route('gmp-karyawan.index')
            ->with('success', 'Data GMP Karyawan berhasil dihapus.');
    }

    /**
    * Show logs for the specified resource.
    */
    public function showLogs($uuid)
    {
    $item = GmpKaryawan::where('uuid', $uuid)->firstOrFail();
    $user = auth()->user();

    // Authorization check
    if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
        abort(403, 'Unauthorized action.');
    }

    $logs = GmpKaryawanLog::where('gmp_karyawan_id', $item->id)
        ->with('user')
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    return view('qc-sistem.gmp_karyawan.logs', compact('item', 'logs'));
    }

    /**
    * Get logs JSON for the specified resource.
    */
    public function getLogsJson($uuid)
    {
    $item = GmpKaryawan::where('uuid', $uuid)->firstOrFail();
    $user = auth()->user();

    // Authorization check
    if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $logs = GmpKaryawanLog::where('gmp_karyawan_id', $item->id)
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
            
            $query = GmpKaryawan::with(['plan', 'user', 'shift', 'area'])
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
            $pdf = PDF::loadView('qc-sistem.gmp_karyawan.export_pdf', compact('data', 'kode_form'));
            $pdf->setPaper('A4', 'landscape');
            
            $filename = 'gmp_karyawan_' . date('Y-m-d_H-i-s') . '.pdf';
            return $pdf->download($filename);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve data by specific role
     */
    public function approve(Request $request, $uuid)
    {
        try {
            $request->validate([
                'type' => 'required|in:qc,produksi,spv'
            ]);

            $gmpKaryawan = GmpKaryawan::where('uuid', $uuid)->firstOrFail();
            $user = Auth::user();
            $userRole = $user->id_role ?? null;

            // Check authorization based on user role and approval type
            $type = $request->type;
            
            // Role-based authorization
            switch ($type) {
                case 'qc':
                    if (!in_array($userRole, [1, 3, 5])) { // Superadmin, QC, atau role 5
                        return response()->json([
                            'success' => false,
                            'message' => 'Anda tidak memiliki wewenang untuk menyetujui sebagai QC'
                        ], 403);
                    }
                    break;
                case 'produksi':
                    if ($userRole != 2) { // Hanya role Produksi
                        return response()->json([
                            'success' => false,
                            'message' => 'Anda tidak memiliki wewenang untuk menyetujui sebagai Produksi'
                        ], 403);
                    }
                    // Check if QC approval exists first
                    if (!$gmpKaryawan->approved_by_qc) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Data harus disetujui oleh QC terlebih dahulu'
                        ], 400);
                    }
                    break;
                case 'spv':
                    if ($userRole != 4) { // Hanya role SPV
                        return response()->json([
                            'success' => false,
                            'message' => 'Anda tidak memiliki wewenang untuk menyetujui sebagai SPV'
                        ], 403);
                    }
                    // Check if Produksi approval exists first
                    if (!$gmpKaryawan->approved_by_produksi) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Data harus disetujui oleh Produksi terlebih dahulu'
                        ], 400);
                    }
                    break;
            }

            // Check if already approved
            $approvalField = "approved_by_{$type}";
            if ($gmpKaryawan->$approvalField) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data sudah disetujui sebelumnya'
                ], 400);
            }

            // Update approval
            $updateData = [
                $approvalField => true,
                "approved_by_{$type}_at" => now(),
                "approved_by_{$type}_user_id" => $user->id
            ];

            $gmpKaryawan->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disetujui',
                'data' => $gmpKaryawan->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadTemplate()
    {
        $user = auth()->user();
        $filename = 'template_gmp_karyawan.xlsx';

        return Excel::download(new GmpKaryawanTemplateExport($user), $filename);
    }

    public function importExcel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls'
        ]);

        if ($validator->fails()) {
            return redirect()->route('gmp-karyawan.index')->with('error', 'File yang diupload harus berupa Excel.');
        }

        $user = auth()->user();

        if (!$user || !$user->id_plan) {
            return redirect()->route('gmp-karyawan.index')->with('error', 'Plan user tidak ditemukan. Tidak dapat melakukan import.');
        }

        $import = new GmpKaryawanImport($user);
        Excel::import($import, $request->file('file'));

        $insertedCount = $import->getInsertedCount();
        $importErrors = $import->getImportErrors();

        if ($insertedCount <= 0) {
            return redirect()->route('gmp-karyawan.index')
                ->with('warning', 'Tidak ada data valid untuk di-import. Pastikan format kolom dan master Shift/Area sesuai.')
                ->with('import_errors', array_slice($importErrors, 0, 20));
        }

        $successMessage = 'Import data GMP Karyawan berhasil. Total: ' . $insertedCount . ' baris.';
        if (!empty($importErrors)) {
            return redirect()->route('gmp-karyawan.index')
                ->with('success', $successMessage)
                ->with('import_errors', array_slice($importErrors, 0, 20))
                ->with('info', 'Ada beberapa baris yang gagal di-import. Silakan cek detail di bawah.');
        }

        return redirect()->route('gmp-karyawan.index')->with('success', $successMessage);
    }
}
