<?php

namespace App\Http\Controllers;

use App\Models\Shoestring;
use App\Models\DataShift;
use App\Models\Plan;
use App\Models\ShoestringLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\DataDefect;
// use PDF;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ShoestringTemplateExport;
use App\Imports\ShoestringImport;

class ShoestringController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = Shoestring::with(['plan', 'shift.user', 'createdBy', 'qcApprover', 'produksiApprover', 'spvApprover']);
           
        if($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $search = request('search');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_produsen', 'LIKE', '%' . $search . '%')
                  ->orWhere('kode_produksi', 'LIKE', '%' . $search . '%');
            });
        }

        $shoestrings = $query->orderBy('tanggal', 'desc')
                             ->orderBy('jam', 'desc')
                             ->paginate(10);
       
        return view('qc-sistem.shoestring.index', compact('shoestrings'));
    }

    public function show($uuid)
    {
        $user = auth()->user();

        $shoestring = Shoestring::where('uuid', $uuid)
            ->with(['plan', 'shift.user', 'createdBy', 'qcApprover', 'produksiApprover', 'spvApprover'])
            ->firstOrFail();

        if ($user->role !== 'superadmin' && $shoestring->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized access');
        }

        $qtyMap = is_array($shoestring->sampling_defect_qty) ? $shoestring->sampling_defect_qty : [];
        $defectIds = array_keys($qtyMap);

        $defectsById = collect();
        if (!empty($defectIds)) {
            $defectsById = DataDefect::whereIn('id', $defectIds)->get()->keyBy('id');
        }

        return view('qc-sistem.shoestring.show', compact('shoestring', 'qtyMap', 'defectsById'));
    }

    public function create()
    {
        $user = auth()->user();
        
        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $shifts = DataShift::all();
            $dataDefect = DataDefect::all();  // ← TAMBAHKAN
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $dataDefect = DataDefect::where('id_plan', $user->id_plan)->get();  // ← TAMBAHKAN
        }
        
        return view('qc-sistem.shoestring.create', compact('shifts', 'plans', 'dataDefect'));  // ← UPDATE
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            // Common fields (shared across all entries)
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'shift_id' => 'required|integer',
            'jam' => 'required|date_format:H:i',
            
            // Entry arrays (repeater fields)
            'entries' => 'required|array|min:1',
            'entries.*.nama_produsen' => 'required|string',
            'entries.*.kode_produksi' => 'required|string',
            'entries.*.best_before' => 'required|date',
            'entries.*.sampling_defect' => 'nullable|array', // ← UBAH: required → nullable
            'entries.*.sampling_defect.*' => 'nullable|integer|exists:data_defect,id',
            'entries.*.sampling_defect_qty' => 'nullable|array',
            'entries.*.sampling_defect_qty.*' => 'nullable|string|max:255',
            'entries.*.catatan' => 'nullable|string',
            'entries.*.dokumentasi_base64' => 'nullable|array',
            'entries.*.dokumentasi_base64.*' => 'string',
        ]);
    
        $user = auth()->user();
        $successCount = 0;
        $errors = [];
    
        try {
            foreach ($data['entries'] as $index => $entry) {
                try {
                    $selectedDefectIds = isset($entry['sampling_defect']) && is_array($entry['sampling_defect']) ? $entry['sampling_defect'] : [];
                    $selectedDefectIds = array_values(array_filter($selectedDefectIds, fn ($v) => $v !== null && $v !== ''));

                    $defectNames = [];
                    if (!empty($selectedDefectIds)) {
                        $defectQuery = DataDefect::query();
                        if ($user->role !== 'superadmin') {
                            $defectQuery->where('id_plan', $user->id_plan);
                        }

                        $defectNames = $defectQuery->whereIn('id', $selectedDefectIds)
                            ->pluck('jenis_defect')
                            ->toArray();
                    }

                    $qtyMap = [];
                    if (isset($entry['sampling_defect_qty']) && is_array($entry['sampling_defect_qty'])) {
                        foreach ($entry['sampling_defect_qty'] as $defectId => $qty) {
                            if ($qty === null || trim((string) $qty) === '') {
                                continue;
                            }
                            $qtyMap[(string) $defectId] = (string) $qty;
                        }
                    }

                    $totalDefect = 0;
                    foreach ($qtyMap as $qty) {
                        if (is_numeric($qty)) {
                            $totalDefect += (float) $qty;
                        }
                    }

                    $dokumentasiPaths = [];
                    if (!empty($entry['dokumentasi_base64'])) {
                        foreach ($entry['dokumentasi_base64'] as $base64Image) {
                            if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                                $fileData = substr($base64Image, strpos($base64Image, ',') + 1);
                                $type = strtolower($type[1]); // jpg, png, gif
                                if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
                                    continue;
                                }
                                $fileData = base64_decode($fileData);
                                if ($fileData === false) {
                                    continue;
                                }
                                $filename = 'shoestring_docs/' . Str::uuid() . '.' . $type;
                                \Storage::disk('public')->put($filename, $fileData);
                                $dokumentasiPaths[] = $filename;
                            }
                        }
                    }

                    Shoestring::create([
                        'uuid' => Str::uuid(),
                        'id_plan' => $user->id_plan,
                        'created_by' => $user->id,
                        'jam' => $data['jam'],
                        'tanggal' => Carbon::createFromFormat('d-m-Y H:i:s', $data['tanggal'])->format('Y-m-d H:i:s'),
                        'shift_id' => $data['shift_id'],
                        'nama_produsen' => $entry['nama_produsen'],
                        'kode_produksi' => $entry['kode_produksi'],
                        'best_before' => $entry['best_before'],
                        'sampling_defect' => !empty($defectNames) ? implode(', ', $defectNames) : null,
                        'sampling_defect_qty' => !empty($qtyMap) ? $qtyMap : null,
                        'total_defect' => $totalDefect > 0 ? (string) $totalDefect : null,
                        'catatan' => $entry['catatan'] ?? null,
                        'dokumentasi' => !empty($dokumentasiPaths) ? $dokumentasiPaths : null,
                    ]);
                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Entry " . ($index + 1) . ": " . $e->getMessage();
                }
            }
    
            if ($successCount > 0) {
                $message = "Berhasil menyimpan {$successCount} data shoestring";
                if (!empty($errors)) {
                    $message .= ". Beberapa data gagal disimpan: " . implode(', ', $errors);
                }
                return redirect()->route('shoestring.index')->with('success', $message);
            } else {
                return redirect()->back()->withErrors($errors)->withInput();
            }
    
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit($uuid)
    {
        $shoestring = Shoestring::where('uuid', $uuid)->firstOrFail();
     
         $user = auth()->user();
           if ($user->role === 'superadmin') {
           
              $plans = Plan::all();
                $shifts = DataShift::all();
                $dataDefect = DataDefect::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $dataDefect = DataDefect::where('id_plan', $user->id_plan)->get();
           
        }
      
        return view('qc-sistem.shoestring.edit', compact('shoestring', 'shifts', 'plans', 'dataDefect'));
    }

    public function update(Request $request, $uuid)
    {
        $shoestring = Shoestring::where('uuid', $uuid)->firstOrFail();
        $data = $request->validate([
            'nama_produsen' => 'required|string',
            'kode_produksi' => 'required|string',
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'jam' => 'required|date_format:H:i',
            'shift_id' => 'required',
            'tgl_exp' => 'required|string',
            'sampling_defect' => 'required|array|min:1', // ← UBAH ke array
            'sampling_defect.*' => 'required|integer|exists:data_defect,id',
            'sampling_defect_qty' => 'nullable|array',
            'sampling_defect_qty.*' => 'nullable|string|max:255',
            'catatan' => 'nullable|string',
            'dokumentasi_base64' => 'nullable|array',
            'dokumentasi_base64.*' => 'string',
        ]);

        $user = auth()->user();
        $defectQuery = DataDefect::query();
        if ($user->role !== 'superadmin') {
            $defectQuery->where('id_plan', $user->id_plan);
        }

        $defectNames = $defectQuery->whereIn('id', $request->sampling_defect)
            ->pluck('jenis_defect')
            ->toArray();

        $qtyMap = [];
        if (is_array($request->sampling_defect_qty)) {
            foreach ($request->sampling_defect_qty as $defectId => $qty) {
                if ($qty === null || trim((string) $qty) === '') {
                    continue;
                }
                $qtyMap[(string) $defectId] = (string) $qty;
            }
        }

        $totalDefect = 0;
        foreach ($qtyMap as $qty) {
            if (is_numeric($qty)) {
                $totalDefect += (float) $qty;
            }
        }
    
        $dokumentasiPaths = $shoestring->dokumentasi ?? [];
        if (!empty($request->dokumentasi_base64)) {
            foreach ($request->dokumentasi_base64 as $base64Image) {
                if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                    $fileData = substr($base64Image, strpos($base64Image, ',') + 1);
                    $type = strtolower($type[1]); // jpg, png, gif
                    if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
                        continue;
                    }
                    $fileData = base64_decode($fileData);
                    if ($fileData === false) {
                        continue;
                    }
                    $filename = 'shoestring_docs/' . Str::uuid() . '.' . $type;
                    \Storage::disk('public')->put($filename, $fileData);
                    $dokumentasiPaths[] = $filename;
                }
            }
        }
    
        $shoestring->update([
            'nama_produsen' => $request->nama_produsen,
            'kode_produksi' => $request->kode_produksi,
            'tanggal' => $request->tanggal,
            'jam' => $request->jam,
            'shift_id' => $request->shift_id,
            'best_before' => $request->tgl_exp,
            'sampling_defect' => !empty($defectNames) ? implode(', ', $defectNames) : null,
            'sampling_defect_qty' => !empty($qtyMap) ? $qtyMap : null,
            'total_defect' => $totalDefect > 0 ? (string) $totalDefect : null,
            'catatan' => $request->catatan,
            'dokumentasi' => !empty($dokumentasiPaths) ? array_values($dokumentasiPaths) : null,
        ]);
        
        return redirect()->route('shoestring.index')->with('success', 'Data berhasil diupdate');
    }

    public function destroy($uuid)
    {
        $shoestring = Shoestring::where('uuid', $uuid)->firstOrFail();
        $shoestring->delete();
        return redirect()->route('shoestring.index')->with('success', 'Data berhasil dihapus');
    }

    /**
     * Approve data
     */
    public function approve(Request $request, $uuid)
    {
        try {
            $request->validate([
                'type' => 'required|in:qc,produksi,spv'
            ]);

            $user = auth()->user();
            $userRole = $user->id_role;
            $type = $request->type;

            // Validasi role dan type yang diizinkan
            $allowedRoles = [
                'qc' => [1, 3, 5], // Role 1, 3, dan 5 bisa approve QC
                'produksi' => [1, 2, 5], // Role 1, 2, dan 5 bisa approve Produksi
                'spv' => [1, 4, 5] // Role 1, 4, dan 5 bisa approve SPV
            ];

            if (!in_array($userRole, $allowedRoles[$type])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk melakukan approval ini'
                ], 403);
            }

            $shoestring = Shoestring::where('uuid', $uuid)->firstOrFail();

            // Check authorization - user hanya bisa approve data dari plan mereka sendiri
            if ($user->role !== 'superadmin' && $shoestring->id_plan !== $user->id_plan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            // Cek apakah sudah diapprove sebelumnya
            $approvalField = 'approved_by_' . $type;
            if ($shoestring->$approvalField) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data sudah disetujui oleh ' . strtoupper($type)
                ], 400);
            }

            // Sequential approval validation
            if ($type === 'produksi' && !$shoestring->approved_by_qc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data harus disetujui oleh QC terlebih dahulu'
                ], 400);
            }

            if ($type === 'spv' && !$shoestring->approved_by_produksi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data harus disetujui oleh Produksi terlebih dahulu'
                ], 400);
            }

            // Update approval status
            $updateData = [
                $approvalField => true,
                $type . '_approved_by' => $user->id,
                $type . '_approved_at' => now()
            ];

            $shoestring->update($updateData);

            // Log activity
            ShoestringLog::create([
                'shoestring_id' => $shoestring->id,
                'shoestring_uuid' => $uuid,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role,
                'aksi' => 'approve',
                'field_yang_diubah' => [$approvalField],
                'nilai_lama' => [$approvalField => false],
                'nilai_baru' => [$approvalField => true],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'keterangan' => 'Approved by ' . strtoupper($type)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disetujui oleh ' . strtoupper($type)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan halaman log perubahan data shoestring
     */
    public function showLogs($uuid)
    {
        $user = auth()->user();
        
        // Cari data shoestring berdasarkan UUID
        $item = Shoestring::where('uuid', $uuid)->with(['shift', 'plan'])->firstOrFail();
        
        // Cek authorization - user hanya bisa melihat log dari plan mereka sendiri
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized access to logs');
        }
        
        // Ambil log dengan pagination
        $logs = ShoestringLog::where('shoestring_uuid', $uuid)
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);
        
        return view('qc-sistem.shoestring.logs', compact('item', 'logs'));
    }

    /**
     * API endpoint untuk mendapatkan data log dalam format JSON
     */
    public function getLogsJson($uuid)
    {
        $user = auth()->user();
        
        // Cari data shoestring berdasarkan UUID
        $item = Shoestring::where('uuid', $uuid)->firstOrFail();
        
        // Cek authorization
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Ambil log
        $logs = ShoestringLog::where('shoestring_uuid', $uuid)
                    ->orderBy('created_at', 'desc')
                    ->get();
        
        $formattedLogs = $logs->map(function($log) {
            return [
                'id' => $log->id,
                'tanggal' => $log->created_at->format('d/m/Y H:i:s'),
                'user_name' => $log->user_name,
                'user_role' => $log->user_role,
                'aksi' => $log->aksi,
                'field_yang_diubah' => $log->field_yang_diubah,
                'deskripsi_perubahan' => $log->getDeskripsiPerubahanAttribute(),
                'keterangan' => $log->keterangan
            ];
        });
        
        return response()->json($formattedLogs);
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
            
            $query = Shoestring::with(['plan', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover'])
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

            // Prepare filters for PDF view
            $filters = [
                'tanggal' => $request->tanggal,
                'shift_id' => $request->shift_id,
                'kode_form' => $request->kode_form
            ];

            $kode_form = $request->kode_form;
            $pdf = PDF::loadView('qc-sistem.shoestring.export_pdf', compact('data', 'kode_form', 'filters'));
            $pdf->setPaper('A4', 'landscape');
            
            $filename = 'shoestring_' . date('Y-m-d_H-i-s') . '.pdf';
            return $pdf->download($filename);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save kode form for specific record
     */
    public function saveKode(Request $request)
    {
        try {
            $request->validate([
                'uuid' => 'required|string',
                'kode_form' => 'required|string|max:50'
            ]);

            $item = Shoestring::where('uuid', $request->uuid)->firstOrFail();
            
            // Update kode form
            $item->update([
                'kode_form' => $request->kode_form
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kode form berhasil disimpan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan kode form: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadTemplate()
    {
        $user = auth()->user();
        $filename = 'template_shoestring.xlsx';

        return Excel::download(new ShoestringTemplateExport($user), $filename);
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $user = auth()->user();

        if (!$user || !$user->id_plan) {
            return redirect()->route('shoestring.index')->with('error', 'Plan user tidak ditemukan. Tidak dapat melakukan import.');
        }

        $import = new ShoestringImport($user);
        Excel::import($import, $request->file('file'));

        $insertedCount = $import->getInsertedCount();
        $importErrors = $import->getImportErrors();

        if ($insertedCount <= 0) {
            return redirect()->route('shoestring.index')
                ->with('warning', 'Tidak ada data valid untuk di-import. Pastikan format kolom dan master Shift/Defect sesuai.')
                ->with('import_errors', array_slice($importErrors, 0, 20));
        }

        $successMessage = 'Import data shoestring berhasil. Total: ' . $insertedCount . ' baris.';
        if (!empty($importErrors)) {
            return redirect()->route('shoestring.index')
                ->with('success', $successMessage)
                ->with('import_errors', array_slice($importErrors, 0, 20))
                ->with('info', 'Ada beberapa baris yang gagal di-import. Silakan cek detail di bawah.');
        }

        return redirect()->route('shoestring.index')->with('success', $successMessage);
    }
}
