<?php

namespace App\Http\Controllers;

use App\Models\Seasoning;
use App\Models\SeasoningLog;
use App\Models\DataShift;
use App\Models\Plan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\DataSeasoning;
// use PDF;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SeasoningTemplateExport;
use App\Imports\SeasoningImport;


class SeasoningController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = Seasoning::with(['plan', 'shift.user', 'createdBy']);
           
        if($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $search = request('search');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_rm', 'LIKE', '%' . $search . '%')
                  ->orWhere('kode_produksi', 'LIKE', '%' . $search . '%');
            });
        }

        $seasonings = $query->orderBy('tanggal', 'desc')
                            ->orderBy('jam', 'desc')
                            ->paginate(10);
        
        return view('qc-sistem.seasoning.index', compact('seasonings'));
    }

    public function create()
    {
        $user = auth()->user();
        
        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $shifts = DataShift::all();
            $dataSeasoning = DataSeasoning::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $dataSeasoning = DataSeasoning::where('id_plan', $user->id_plan)->get();
        }
        
        return view('qc-sistem.seasoning.create', compact('shifts', 'plans', 'dataSeasoning'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            // Common fields (shared across all entries)
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'jam' => 'required|date_format:H:i',
            'shift_id' => 'required|integer',
            
            // Entry arrays (repeater fields)
            'entries' => 'required|array|min:1',
            'entries.*.nama_rm' => 'required|string',
            'entries.*.kode_produksi' => 'required|string',
            'entries.*.berat' => 'required|string',
            'entries.*.sensori' => 'required|string',
            'entries.*.kemasan' => 'required|string',
            'entries.*.keterangan' => 'nullable|string',
        ]);

        $user = auth()->user();
        $successCount = 0;
        $errors = [];

        try {
            foreach ($data['entries'] as $index => $entry) {
                try {
                    Seasoning::create([
                        'uuid' => Str::uuid(),
                        'id_plan' => $user->id_plan,
                        'created_by' => $user->id,
                        'tanggal' => Carbon::createFromFormat('d-m-Y H:i:s', $data['tanggal'])->format('Y-m-d H:i:s'),
                        'jam' => $data['jam'],
                        'shift_id' => $data['shift_id'],
                        'nama_rm' => $entry['nama_rm'],
                        'kode_produksi' => $entry['kode_produksi'],
                        'berat' => $entry['berat'],
                        'sensori' => $entry['sensori'],
                        'kemasan' => $entry['kemasan'],
                        'keterangan' => $entry['keterangan'] ?? null,
                    ]);
                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Entry " . ($index + 1) . ": " . $e->getMessage();
                }
            }

            if ($successCount > 0) {
                $message = "Berhasil menyimpan {$successCount} data seasoning";
                if (!empty($errors)) {
                    $message .= ". Beberapa data gagal disimpan: " . implode(', ', $errors);
                }
                return redirect()->route('seasoning.index')->with('success', $message);
            } else {
                return redirect()->back()->withErrors($errors)->withInput();
            }

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit($uuid)
    {
        $seasoning = Seasoning::where('uuid', $uuid)->firstOrFail();
        
        $user = auth()->user();
        
        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $shifts = DataShift::all();
            $dataSeasoning = DataSeasoning::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $dataSeasoning = DataSeasoning::where('id_plan', $user->id_plan)->get();
        }
        
        return view('qc-sistem.seasoning.edit', compact('seasoning', 'shifts', 'plans', 'dataSeasoning'));
    }
    public function update(Request $request, $uuid)
    {
        $seasoning = Seasoning::where('uuid', $uuid)->firstOrFail();
        $validated = $request->validate([
            'nama_rm' => 'required|string',
            'kode_produksi' => 'required|string',
            'shift_id' => 'required',
            'tanggal_seasoning' => 'required|date_format:d-m-Y H:i:s',
            'jam_seasoning' => 'required|date_format:H:i',
            'berat' => 'required|string',
          
            'sensori' => 'required|string',
            'kemasan' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        $seasoning->update([
            'shift_id' => $request->shift_id,
            'nama_rm' => $request->nama_rm,
            'kode_produksi' => $request->kode_produksi,
            'berat' => $request->berat,
            'tanggal' => Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal_seasoning)->format('Y-m-d H:i:s'),
            'jam' => $request->jam_seasoning,
            'sensori' => $request->sensori,
            'kemasan' => $request->kemasan,
            'keterangan' => $request->keterangan,
        ]);
        // var_dump($data);
        // exit;
        // $seasoning->update($data);
        return redirect()->route('seasoning.index')->with('success', 'Data berhasil diupdate');
    }

    public function destroy($uuid)
    {
        $seasoning = Seasoning::where('uuid', $uuid)->firstOrFail();
        $seasoning->delete();
        return redirect()->route('seasoning.index')->with('success', 'Data berhasil dihapus');
    }

    /**
     * Menampilkan riwayat log perubahan data
     */
    public function showLogs($uuid)
    {
        $item = Seasoning::where('uuid', $uuid)->firstOrFail();
        
        // Check authorization
        $user = auth()->user();
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized');
        }
        
        $logs = SeasoningLog::where('seasoning_id', $item->id)
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->paginate(5);
        
        return view('qc-sistem.seasoning.logs', compact('item', 'logs'));
    }

    /**
     * API untuk mendapatkan log dalam format JSON (untuk AJAX)
     */
    public function getLogsJson($uuid)
    {
        $item = Seasoning::where('uuid', $uuid)->firstOrFail();
        
        // Check authorization
        $user = auth()->user();
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $logs = SeasoningLog::where('seasoning_id', $item->id)
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function($log) {
                        return [
                            'id' => $log->id,
                            'tanggal' => $log->created_at->format('d/m/Y H:i:s'),
                            'user' => $log->user_name,
                            'role' => $log->user_role,
                            'aksi' => ucfirst($log->aksi),
                            'field_diubah' => implode(', ', $log->field_yang_diubah ?? []),
                            'deskripsi' => $log->deskripsi_perubahan,
                            'ip_address' => $log->ip_address
                        ];
                    });
        
        return response()->json($logs);
    }

    /**
     * Bulk export PDF with filters
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
            
            $query = Seasoning::with(['plan', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover'])
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
            $pdf = \PDF::loadView('qc-sistem.seasoning.export_pdf', compact('data', 'kode_form', 'filters'));
            $pdf->setPaper('A4', 'landscape');
            
            $filename = 'seasoning_' . date('Y-m-d_H-i-s') . '.pdf';
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

            $item = Seasoning::where('uuid', $request->uuid)->firstOrFail();
            
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
                'produksi' => [2], // Role 2 bisa approve Produksi
                'spv' => [4] // Role 4 bisa approve SPV
            ];

            if (!in_array($userRole, $allowedRoles[$type])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk melakukan persetujuan ini'
                ], 403);
            }

            $item = Seasoning::where('uuid', $uuid)->firstOrFail();

            // Sequential approval validation
            if ($type === 'produksi' && !$item->approved_by_qc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data harus disetujui oleh QC terlebih dahulu sebelum dapat disetujui oleh Produksi'
                ], 422);
            }

            if ($type === 'spv' && !$item->approved_by_produksi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data harus disetujui oleh Produksi terlebih dahulu sebelum dapat disetujui oleh SPV'
                ], 422);
            }

            // Check if already approved
            $approvalField = 'approved_by_' . $type;
            if ($item->$approvalField) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data sudah disetujui oleh ' . strtoupper($type)
                ], 422);
            }
            
            // Update field approval yang sesuai
            $approverField = $type . '_approved_by';
            $approvalDateField = $type . '_approved_at';
            
            $item->update([
                $approvalField => true,
                $approverField => $user->id,
                $approvalDateField => now()
            ]);

            // Log approval activity
            SeasoningLog::create([
                'uuid' => Str::uuid(),
                'seasoning_id' => $item->id,
                'seasoning_uuid' => $item->uuid,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role ?? 'Unknown',
                'aksi' => 'approval',
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

    public function downloadTemplate()
    {
        $user = auth()->user();
        $filename = 'template_seasoning.xlsx';

        return Excel::download(new SeasoningTemplateExport($user), $filename);
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $user = auth()->user();

        if (!$user || !$user->id_plan) {
            return redirect()->route('seasoning.index')->with('error', 'Plan user tidak ditemukan. Tidak dapat melakukan import.');
        }

        $import = new SeasoningImport($user);
        Excel::import($import, $request->file('file'));

        $insertedCount = $import->getInsertedCount();
        $importErrors = $import->getImportErrors();

        if ($insertedCount <= 0) {
            return redirect()->route('seasoning.index')
                ->with('warning', 'Tidak ada data valid untuk di-import. Pastikan format kolom dan master Shift/Seasoning sesuai.')
                ->with('import_errors', array_slice($importErrors, 0, 20));
        }

        $successMessage = 'Import data seasoning berhasil. Total: ' . $insertedCount . ' baris.';
        if (!empty($importErrors)) {
            return redirect()->route('seasoning.index')
                ->with('success', $successMessage)
                ->with('import_errors', array_slice($importErrors, 0, 20))
                ->with('info', 'Ada beberapa baris yang gagal di-import. Silakan cek detail di bawah.');
        }

        return redirect()->route('seasoning.index')->with('success', $successMessage);
    }
}
