<?php

namespace App\Http\Controllers;

use App\Models\PemeriksaanProsesProduksi;
use App\Models\Plan;
use App\Models\DataShift;
use App\Models\InputArea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\PemeriksaanProsesProduksiLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PemeriksaanProsesProduksiTemplateExport;
use App\Imports\PemeriksaanProsesProduksiImport;

class PemeriksaanProsesProduksiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = PemeriksaanProsesProduksi::with(['plan', 'shift', 'area', 'user', 'qcApprover', 'produksiApprover', 'spvApprover'])
            ->filterByRole();

        $search = request('search');
        if($search) {
            $query->where('uraian_permasalahan', 'LIKE', '%' . $search . '%');
        }
        
        $data = $query->orderBy('tanggal', 'desc')->paginate(10);

        return view('qc-sistem.pemeriksaan_proses_produksi.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        
        // Filter data berdasarkan role
        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $areas = InputArea::all();
            $shifts = DataShift::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $areas = InputArea::where('id_plan', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        }
        
        $ketidaksesuaianOptions = PemeriksaanProsesProduksi::getKetidaksesuaianOptions();
        $disposisiOptions = PemeriksaanProsesProduksi::getDisposisiOptions();
        
        return view('qc-sistem.pemeriksaan_proses_produksi.create', compact('plans', 'shifts', 'areas',  'ketidaksesuaianOptions', 'disposisiOptions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         $user = Auth::user();
       $isSpecialRole = ($user->id_role == 2 || $user->id_role == 3);

       if($isSpecialRole){
           $validator = Validator::make($request->all(), [
               'id_area' => 'required|exists:input_area,id',
               'shift_id' => 'required|exists:data_shift,id',
              'tanggal' => 'required|date_format:d-m-Y', // Hanya validasi format tanggal saja
               'jam' => 'required',
               'ketidaksesuaian' => 'required|in:' . implode(',', array_keys(PemeriksaanProsesProduksi::getKetidaksesuaianOptions())),
               'uraian_permasalahan' => 'required|string',
               'analisa_penyebab' => 'required|string',
               'disposisi' => 'required|in:' . implode(',', array_keys(PemeriksaanProsesProduksi::getDisposisiOptions())),
               'tindakan_koreksi' => 'required|string',
           ]);    
       } else {
              $validator = Validator::make($request->all(), [
               'id_area' => 'required|exists:input_area,id',
               'shift_id' => 'required|exists:data_shift,id',
             'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'jam' => 'required',
               'ketidaksesuaian' => 'required|in:' . implode(',', array_keys(PemeriksaanProsesProduksi::getKetidaksesuaianOptions())),
               'uraian_permasalahan' => 'required|string',
               'analisa_penyebab' => 'required|string',
               'disposisi' => 'required|in:' . implode(',', array_keys(PemeriksaanProsesProduksi::getDisposisiOptions())),
               'tindakan_koreksi' => 'required|string',
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

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = Auth::user();
        
        $data = $request->all();
        $data['uuid'] = Str::uuid();
        $data['user_id'] = $user->id;
        $data['id_plan'] = $user->id_plan;
        $data['tanggal'] = $tanggalData;
        $data['jam'] = $request->jam;
        PemeriksaanProsesProduksi::create($data);

        return redirect()->route('pemeriksaan-proses-produksi.index')
            ->with('success', 'Data Pemeriksaan Proses Produksi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function show($uuid)
    {
        $data = PemeriksaanProsesProduksi::with(['plan', 'shift', 'area', 'user'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        // Cek akses data
        if (!$data->canAccess()) {
            abort(403, 'Unauthorized access');
        }

        return view('qc-sistem.pemeriksaan_proses_produksi.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function edit($uuid)
    {
        $data = PemeriksaanProsesProduksi::where('uuid', $uuid)->firstOrFail();

        // Cek akses data
        if (!$data->canAccess()) {
            abort(403, 'Unauthorized access');
        }

        $user = Auth::user();
        
        // Filter data berdasarkan role
        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $areas = InputArea::all();
            $shifts = DataShift::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $areas = InputArea::where('id_plan', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        }
        $ketidaksesuaianOptions = PemeriksaanProsesProduksi::getKetidaksesuaianOptions();
        $disposisiOptions = PemeriksaanProsesProduksi::getDisposisiOptions();

        return view('qc-sistem.pemeriksaan_proses_produksi.edit', compact('data', 'plans', 'shifts', 'areas', 'ketidaksesuaianOptions', 'disposisiOptions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $uuid)
    {
        $data = PemeriksaanProsesProduksi::where('uuid', $uuid)->firstOrFail();

        // Cek akses data
        if (!$data->canAccess()) {
            abort(403, 'Unauthorized access');
        }

        $validator = Validator::make($request->all(), [
            'id_area' => 'required|exists:input_area,id',
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
            'ketidaksesuaian' => 'required|in:' . implode(',', array_keys(PemeriksaanProsesProduksi::getKetidaksesuaianOptions())),
            'uraian_permasalahan' => 'required|string',
            'analisa_penyebab' => 'required|string',
            'disposisi' => 'required|in:' . implode(',', array_keys(PemeriksaanProsesProduksi::getDisposisiOptions())),
            'tindakan_koreksi' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $updateData = $request->except(['uuid', 'user_id', 'id_plan']);
        $data->update($updateData);

        return redirect()->route('pemeriksaan-proses-produksi.index')
            ->with('success', 'Data Pemeriksaan Proses Produksi berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        $data = PemeriksaanProsesProduksi::where('uuid', $uuid)->firstOrFail();

        // Cek akses data
        if (!$data->canAccess()) {
            abort(403, 'Unauthorized access');
        }

        $data->delete();

        return redirect()->route('pemeriksaan-proses-produksi.index')
            ->with('success', 'Data Pemeriksaan Proses Produksi berhasil dihapus.');
    }
    /**
     * Show logs for the specified resource.
     */
    public function showLogs($uuid)
    {
        $item = PemeriksaanProsesProduksi::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        
        // Authorization check
        if (!$item->canAccess($user)) {
            abort(403, 'Unauthorized action.');
        }

        $logs = PemeriksaanProsesProduksiLog::where('pemeriksaan_proses_produksi_id', $item->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('qc-sistem.pemeriksaan_proses_produksi.logs', compact('item', 'logs'));
    }

    /**
     * Get logs JSON for the specified resource.
     */
    public function getLogsJson($uuid)
    {
        $item = PemeriksaanProsesProduksi::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        
        // Authorization check
        if (!$item->canAccess($user)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $logs = PemeriksaanProsesProduksiLog::where('pemeriksaan_proses_produksi_id', $item->id)
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
                'id_area' => 'nullable|integer',
                'kode_form' => 'required|string|max:50'
            ]);

            $user = Auth::user();
            
            $query = PemeriksaanProsesProduksi::with(['plan', 'user', 'shift', 'area', 'qcApprover', 'produksiApprover', 'spvApprover'])
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

            if ($request->id_area) {
                $query->where('id_area', $request->id_area);
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
                if ($request->id_area) {
                    $area = InputArea::find($request->id_area);
                    $filterInfo[] = 'Area: ' . ($area ? $area->area : 'Unknown');
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

            // Prepare filters for PDF view
            $filters = [
                'tanggal' => $request->tanggal,
                'shift_id' => $request->shift_id,
                'id_area' => $request->id_area,
                'kode_form' => $request->kode_form
            ];

            // Generate PDF
            $pdf = Pdf::loadView('qc-sistem.pemeriksaan_proses_produksi.export_pdf', compact('data', 'filters'))
                ->setPaper('a4', 'portrait');

            $filename = 'pemeriksaan_proses_produksi_' . date('Y-m-d_H-i-s') . '.pdf';
            
            return $pdf->download($filename);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
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

        $pemeriksaanProsesProduksi = PemeriksaanProsesProduksi::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
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
        if ($type === 'produksi' && !$pemeriksaanProsesProduksi->approved_by_qc) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh QC terlebih dahulu.'
            ], 400);
        }

        if ($type === 'spv' && !$pemeriksaanProsesProduksi->approved_by_produksi) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh Produksi terlebih dahulu.'
            ], 400);
        }

        // Check if already approved
        $approvalField = "approved_by_{$type}";
        if ($pemeriksaanProsesProduksi->$approvalField) {
            return response()->json([
                'success' => false,
                'message' => 'Data sudah disetujui sebelumnya.'
            ], 400);
        }

        // Update approval
        $pemeriksaanProsesProduksi->update([
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
        $filename = 'template_pemeriksaan_proses_produksi.xlsx';

        return Excel::download(new PemeriksaanProsesProduksiTemplateExport($user), $filename);
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $user = auth()->user();

        if (!$user || !$user->id_plan) {
            return redirect()->route('pemeriksaan-proses-produksi.index')->with('error', 'Plan user tidak ditemukan. Tidak dapat melakukan import.');
        }

        $import = new PemeriksaanProsesProduksiImport($user);
        Excel::import($import, $request->file('file'));

        $insertedCount = $import->getInsertedCount();
        $importErrors = $import->getImportErrors();

        if ($insertedCount <= 0) {
            return redirect()->route('pemeriksaan-proses-produksi.index')
                ->with('warning', 'Tidak ada data valid untuk di-import. Pastikan format kolom dan master Shift/Area sesuai.')
                ->with('import_errors', array_slice($importErrors, 0, 20));
        }

        $successMessage = 'Import data pemeriksaan proses produksi berhasil. Total: ' . $insertedCount . ' baris.';
        if (!empty($importErrors)) {
            return redirect()->route('pemeriksaan-proses-produksi.index')
                ->with('success', $successMessage)
                ->with('import_errors', array_slice($importErrors, 0, 20))
                ->with('info', 'Ada beberapa baris yang gagal di-import. Silakan cek detail di bawah.');
        }

        return redirect()->route('pemeriksaan-proses-produksi.index')->with('success', $successMessage);
    }
}

