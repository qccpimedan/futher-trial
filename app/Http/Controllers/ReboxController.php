<?php

namespace App\Http\Controllers;

use App\Models\Rebox;
use App\Models\DataShift;
use App\Models\Plan;
use App\Models\ReboxLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReboxTemplateExport;
use App\Imports\ReboxImport;

class ReboxController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = Rebox::with(['plan', 'shift.user', 'createdBy', 'qcApprover', 'produksiApprover', 'spvApprover']);
           
        if($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $search = request('search');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_produk', 'LIKE', '%' . $search . '%')
                  ->orWhere('kode_produksi', 'LIKE', '%' . $search . '%');
            });
        }

        $reboxes = $query->orderBy('tanggal', 'desc')
                         ->orderBy('jam', 'desc')
                         ->paginate(10);

        return view('qc-sistem.rebox.index', compact('reboxes'));
    }

    public function create()
    {
        $user = auth()->user();
        
        if ($user->role === 'superadmin' || $user->id_role == 1) {
            $plans = Plan::all();
            $shifts = DataShift::all();
            $jenis_produk = \App\Models\JenisProduk::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $jenis_produk = \App\Models\JenisProduk::where('id_plan', $user->id_plan)->get();
        }
        
        return view('qc-sistem.rebox.create', compact('shifts', 'plans', 'jenis_produk'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_produk' => 'required|array',
            'nama_produk.*' => 'required|string',
            'kode_produksi' => 'required|array',
            'kode_produksi.*' => 'required|string',
            'best_before' => 'required|array',
            'best_before.*' => 'required|date',
            'isi_jumlah' => 'required|array',
            'isi_jumlah.*' => 'required|string',
            'tanggal_rebox' => 'required|date_format:d-m-Y H:i:s',
            'jam' => 'required|date_format:H:i',
            'shift_id' => 'required|integer',
            'labelisasi' => 'required|array',
            'labelisasi.*' => 'required|string',
        ]);

        $count = count($data['nama_produk']);
        $user = auth()->user();
        for ($i = 0; $i < $count; $i++) {
            Rebox::create([
                'id_plan' => $user->id_plan,
                'created_by' => $user->id,
                'nama_produk' => $data['nama_produk'][$i],
                'kode_produksi' => $data['kode_produksi'][$i],
                'best_before' => $data['best_before'][$i],
                'tanggal' => Carbon::createFromFormat('d-m-Y H:i:s', $data['tanggal_rebox'])->format('Y-m-d H:i:s'),
                'jam' => $data['jam'],
                'shift_id' => $data['shift_id'],
                'isi_jumlah' => $data['isi_jumlah'][$i],
                'labelisasi' => $data['labelisasi'][$i],
            ]);
        }

        return redirect()->route('rebox.index')->with('success', 'Data berhasil disimpan');
    }

    public function edit($uuid)
    {
        $rebox = Rebox::where('uuid', $uuid)->firstOrFail();
       
         $user = auth()->user();
           if ($user->role === 'superadmin') {
           
              $plans = Plan::all();
        $shifts = DataShift::all();
        } else {
          
          
            $plans = Plan::where('id', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
           
        }
        return view('qc-sistem.rebox.edit', compact('rebox', 'shifts', 'plans'));
    }

    public function update(Request $request, $uuid)
    {
        $rebox = Rebox::where('uuid', $uuid)->firstOrFail();
        $data = $request->validate([
            'nama_produk' => 'required|string',
            'kode_produksi' => 'required|string',
            'best_before' => 'required|date',
            'isi_jumlah' => 'required|string',
            'tanggal_rebox' => 'required|date_format:d-m-Y H:i:s',
            'shift_id' => 'required|integer',
            'labelisasi' => 'required|string',
        ]);

        $rebox->update([
            'nama_produk' => $request->nama_produk,
            'kode_produksi' => $request->kode_produksi,
            'best_before' => $request->best_before,
            'isi_jumlah' => $request->isi_jumlah,
            'tanggal' => Carbon::createFromFormat('d-m-Y H:i:s', $data['tanggal_rebox'])->format('Y-m-d H:i:s'),
            'shift_id' => $request->shift_id,
            'labelisasi' => $request->labelisasi,
        ]);

        return redirect()->route('rebox.index')->with('success', 'Data berhasil diupdate');
    }

    public function destroy($uuid)
    {
        $rebox = Rebox::where('uuid', $uuid)->firstOrFail();
        $rebox->delete();
        return redirect()->route('rebox.index')->with('success', 'Data berhasil dihapus');
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

            $rebox = Rebox::where('uuid', $uuid)->firstOrFail();

            // Check authorization - user hanya bisa approve data dari plan mereka sendiri
            if ($user->role !== 'superadmin' && $rebox->id_plan !== $user->id_plan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            // Cek apakah sudah diapprove sebelumnya
            $approvalField = 'approved_by_' . $type;
            if ($rebox->$approvalField) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data sudah disetujui oleh ' . strtoupper($type)
                ], 400);
            }

            // Sequential approval validation
            if ($type === 'produksi' && !$rebox->approved_by_qc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data harus disetujui oleh QC terlebih dahulu'
                ], 400);
            }

            if ($type === 'spv' && !$rebox->approved_by_produksi) {
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

            $rebox->update($updateData);

            // Log activity
            ReboxLog::create([
                'rebox_id' => $rebox->id,
                'rebox_uuid' => $uuid,
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
     * Menampilkan halaman log perubahan data rebox
     */
    public function showLogs($uuid)
    {
        $user = auth()->user();
        
        // Cari data rebox berdasarkan UUID
        $item = Rebox::where('uuid', $uuid)->with(['shift', 'plan'])->firstOrFail();
        
        // Cek authorization - user hanya bisa melihat log dari plan mereka sendiri
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized access to logs');
        }
        
        // Ambil log dengan pagination
        $logs = ReboxLog::where('rebox_uuid', $uuid)
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);
        
        return view('qc-sistem.rebox.logs', compact('item', 'logs'));
    }

    /**
     * API endpoint untuk mendapatkan data log dalam format JSON
     */
    public function getLogsJson($uuid)
    {
        $user = auth()->user();
        
        // Cari data rebox berdasarkan UUID
        $item = Rebox::where('uuid', $uuid)->firstOrFail();
        
        // Cek authorization
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Ambil log
        $logs = ReboxLog::where('rebox_uuid', $uuid)
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
            
            $query = Rebox::with(['plan', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover'])
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
            $pdf = PDF::loadView('qc-sistem.rebox.export_pdf', compact('data', 'kode_form', 'filters'));
            $pdf->setPaper('A4', 'landscape');
            
            $filename = 'rebox_' . date('Y-m-d_H-i-s') . '.pdf';
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

            $item = Rebox::where('uuid', $request->uuid)->firstOrFail();
            
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
        $filename = 'template_rebox.xlsx';

        return Excel::download(new ReboxTemplateExport($user), $filename);
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $user = auth()->user();

        if (!$user || !$user->id_plan) {
            return redirect()->route('rebox.index')->with('error', 'Plan user tidak ditemukan. Tidak dapat melakukan import.');
        }

        $import = new ReboxImport($user);
        Excel::import($import, $request->file('file'));

        $insertedCount = $import->getInsertedCount();
        $importErrors = $import->getImportErrors();

        if ($insertedCount <= 0) {
            return redirect()->route('rebox.index')
                ->with('warning', 'Tidak ada data valid untuk di-import. Pastikan format kolom dan master Shift/Produk sesuai.')
                ->with('import_errors', array_slice($importErrors, 0, 20));
        }

        $successMessage = 'Import data rebox berhasil. Total: ' . $insertedCount . ' baris.';
        if (!empty($importErrors)) {
            return redirect()->route('rebox.index')
                ->with('success', $successMessage)
                ->with('import_errors', array_slice($importErrors, 0, 20))
                ->with('info', 'Ada beberapa baris yang gagal di-import. Silakan cek detail di bawah.');
        }

        return redirect()->route('rebox.index')->with('success', $successMessage);
    }
}
