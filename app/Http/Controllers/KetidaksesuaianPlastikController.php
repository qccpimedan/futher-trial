<?php

namespace App\Http\Controllers;

use App\Models\KetidaksesuaianPlastik;
use App\Models\DataShift;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\KetidaksesuaianPlastikLog;

class KetidaksesuaianPlastikController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = KetidaksesuaianPlastik::with(['plan', 'user', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover']);
           
        if($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $search = request('search');
        if($search) {
            $query->where('nama_plastik', 'LIKE', '%' . $search . '%');
        }
        
        $ketidaksesuaianPlastik = $query->orderBy('created_at', 'desc')->paginate(10);
        $shifts = DataShift::all();
       
        return view('qc-sistem.ketidaksesuaian_plastik.index', compact('ketidaksesuaianPlastik', 'shifts'));
    }

    public function create()
    {
        $user = auth()->user();
        
        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $shifts = DataShift::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        }

        return view('qc-sistem.ketidaksesuaian_plastik.create', compact('shifts', 'plans'));
    }

    public function store(Request $request)
    {
         $user = Auth::user();
        $isSpecialRole = ($user->id_role == 2 || $user->id_role == 3);
        
        if($isSpecialRole){

            $request->validate([
                'id_shift' => 'required|exists:data_shift,id',
                 'tanggal' => 'required|date_format:d-m-Y', // Hanya validasi format tanggal saja
                  'jam' => 'required',
                'nama_plastik' => 'required|string|max:255',
                'alasan_hold' => 'required|string',
                'hold_data' => 'required|string',
                'dokumentasi_tagging' => 'nullable|image|mimes:jpeg,png,jpg,gif',
                'dokumentasi_penyimpangan_plastik' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            ]);
        }else{
            $request->validate([
                'id_shift' => 'required|exists:data_shift,id',
                'tanggal' => 'required|date_format:d-m-Y H:i:s',
                'jam' => 'required',
                'nama_plastik' => 'required|string|max:255',
                'alasan_hold' => 'required|string',
                'hold_data' => 'required|string',
                'dokumentasi_tagging' => 'nullable|image|mimes:jpeg,png,jpg,gif',
                'dokumentasi_penyimpangan_plastik' => 'nullable|image|mimes:jpeg,png,jpg,gif',
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

     
        $data = [
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'id_plan' => $user->id_plan,
            'id_shift' => $request->id_shift,
            'tanggal' => $tanggalData,
            'jam'    => $request->jam,
            'nama_plastik' => $request->nama_plastik,
            'alasan_hold' => $request->alasan_hold,
            'hold_data' => $request->hold_data,
        ];

        // Handle file uploads
        if ($request->hasFile('dokumentasi_tagging')) {
            $file = $request->file('dokumentasi_tagging');
            $filename = time() . '_' . uniqid() . '_tagging.jpg';
            $image = Image::make($file)->encode('jpg', 70);
            Storage::disk('public')->put('uploads/ketidaksesuaian_plastik/' . $filename, $image);
            $data['dokumentasi_tagging'] = 'uploads/ketidaksesuaian_plastik/' . $filename;
        }

        if ($request->hasFile('dokumentasi_penyimpangan_plastik')) {
            $file = $request->file('dokumentasi_penyimpangan_plastik');
            $filename = time() . '_' . uniqid() . '_penyimpangan.jpg';
            $image = Image::make($file)->encode('jpg', 70);
            Storage::disk('public')->put('uploads/ketidaksesuaian_plastik/' . $filename, $image);
            $data['dokumentasi_penyimpangan_plastik'] = 'uploads/ketidaksesuaian_plastik/' . $filename;
        }

        KetidaksesuaianPlastik::create($data);

        return redirect()->route('ketidaksesuaian-plastik.index')
            ->with('success', 'Data ketidaksesuaian plastik berhasil ditambahkan.');
    }

    public function show($uuid)
    {
        $ketidaksesuaianPlastik = KetidaksesuaianPlastik::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        
        if ($user->role !== 'superadmin' && $ketidaksesuaianPlastik->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized access.');
        }

        return view('qc-sistem.ketidaksesuaian_plastik.show', compact('ketidaksesuaianPlastik'));
    }

    public function edit($uuid)
    {
        $ketidaksesuaianPlastik = KetidaksesuaianPlastik::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        
        if ($user->role !== 'superadmin' && $ketidaksesuaianPlastik->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized access.');
        }

        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $shifts = DataShift::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        }

        return view('qc-sistem.ketidaksesuaian_plastik.edit', compact('ketidaksesuaianPlastik', 'shifts', 'plans'));
    }

    public function update(Request $request, $uuid)
    {
        $ketidaksesuaianPlastik = KetidaksesuaianPlastik::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        
        if ($user->role !== 'superadmin' && $ketidaksesuaianPlastik->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'id_shift' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
            'nama_plastik' => 'required|string|max:255',
            'alasan_hold' => 'required|string',
            'hold_data' => 'required|string',
            'dokumentasi_tagging' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'dokumentasi_penyimpangan_plastik' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        $data = [
            'id_shift' => $request->id_shift,
            'tanggal' => $request->tanggal,
            'nama_plastik' => $request->nama_plastik,
            'alasan_hold' => $request->alasan_hold,
            'hold_data' => $request->hold_data,
        ];

        // Handle file uploads
        if ($request->hasFile('dokumentasi_tagging')) {
            // Delete old file if exists
            if ($ketidaksesuaianPlastik->dokumentasi_tagging) {
                $oldFile = public_path('uploads/ketidaksesuaian_plastik/' . $ketidaksesuaianPlastik->dokumentasi_tagging);
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }
            
            $file = $request->file('dokumentasi_tagging');
            $filename = time() . '_' . uniqid() . '_tagging.jpg';
            $image = Image::make($file)->encode('jpg', 70);
            Storage::disk('public')->put('uploads/ketidaksesuaian_plastik/' . $filename, $image);
            $data['dokumentasi_tagging'] = 'uploads/ketidaksesuaian_plastik/' . $filename;
        }

        if ($request->hasFile('dokumentasi_penyimpangan_plastik')) {
            // Delete old file if exists
            if ($ketidaksesuaianPlastik->dokumentasi_penyimpangan_plastik) {
                $oldFile = public_path('uploads/ketidaksesuaian_plastik/' . $ketidaksesuaianPlastik->dokumentasi_penyimpangan_plastik);
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }
            
            $file = $request->file('dokumentasi_penyimpangan_plastik');
            $filename = time() . '_' . uniqid() . '_penyimpangan.jpg';
            $image = Image::make($file)->encode('jpg', 70);
            Storage::disk('public')->put('uploads/ketidaksesuaian_plastik/' . $filename, $image);
            $data['dokumentasi_penyimpangan_plastik'] = 'uploads/ketidaksesuaian_plastik/' . $filename;
        }

        $ketidaksesuaianPlastik->update($data);

        return redirect()->route('ketidaksesuaian-plastik.index')
            ->with('success', 'Data ketidaksesuaian plastik berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $ketidaksesuaianPlastik = KetidaksesuaianPlastik::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        
        if ($user->role !== 'superadmin' && $ketidaksesuaianPlastik->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized access.');
        }

        // Delete associated files
        if ($ketidaksesuaianPlastik->dokumentasi_tagging) {
            $file = public_path('uploads/ketidaksesuaian_plastik/' . $ketidaksesuaianPlastik->dokumentasi_tagging);
            if (file_exists($file)) {
                unlink($file);
            }
        }

        if ($ketidaksesuaianPlastik->dokumentasi_penyimpangan_plastik) {
            $file = public_path('uploads/ketidaksesuaian_plastik/' . $ketidaksesuaianPlastik->dokumentasi_penyimpangan_plastik);
            if (file_exists($file)) {
                unlink($file);
            }
        }

        $ketidaksesuaianPlastik->delete();

        return redirect()->route('ketidaksesuaian-plastik.index')
            ->with('success', 'Data ketidaksesuaian plastik berhasil dihapus.');
    }
    /**
     * Show logs for the specified resource.
     */
    public function showLogs($uuid)
    {
        $item = KetidaksesuaianPlastik::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        
        // Authorization check
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized action.');
        }

        $logs = KetidaksesuaianPlastikLog::where('ketidaksesuaian_plastik_id', $item->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('qc-sistem.ketidaksesuaian_plastik.logs', compact('item', 'logs'));
    }

    /**
     * Get logs JSON for the specified resource.
     */
    public function getLogsJson($uuid)
    {
        $item = KetidaksesuaianPlastik::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        
        // Authorization check
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $logs = KetidaksesuaianPlastikLog::where('ketidaksesuaian_plastik_id', $item->id)
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
            // Add debug logging
            \Log::info('Bulk PDF request received', [
                'request_data' => $request->all(),
                'user_id' => auth()->id()
            ]);

            $request->validate([
                'tanggal' => 'nullable|date',
                'shift_id' => 'nullable|integer',
                'kode_form' => 'required|string|max:50'
            ]);

            $user = auth()->user();
            
            $query = KetidaksesuaianPlastik::with(['plan', 'user', 'shift'])
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                });

            // Apply filters
            if ($request->filled('tanggal')) {
                $query->whereDate('tanggal', $request->tanggal);
            }
            
            if ($request->filled('shift_id')) {
                $query->where('id_shift', $request->shift_id);
            }

            $data = $query->orderBy('created_at', 'desc')->get();

            \Log::info('Data query result', [
                'data_count' => $data->count(),
                'query_sql' => $query->toSql()
            ]);

            if ($data->isEmpty()) {
                \Log::warning('No data found for PDF export');
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

            // Save kode_form to all filtered records
            if ($data->isNotEmpty()) {
                $query->update(['kode_form' => $request->kode_form]);
            }

            // Pass kode_form to view
            $kode_form = $request->kode_form ?? '';
            $filename = 'ketidaksesuaian_plastik_bulk_' . date('Y-m-d_H-i-s') . '.pdf';

            \Log::info('Generating PDF', [
                'filename' => $filename,
                'kode_form' => $kode_form,
                'data_count' => $data->count()
            ]);

            $pdf = Pdf::loadView('qc-sistem.ketidaksesuaian_plastik.export_pdf', compact('data', 'kode_form'));
            $pdf->setPaper('A4', 'landscape');
            
            \Log::info('PDF generated successfully, starting download');
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            \Log::error('Bulk PDF generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json(['error' => 'Gagal membuat PDF: ' . $e->getMessage()], 500);
        }
    }

    public function approve(Request $request, $uuid)
    {
        try {
            $ketidaksesuaianPlastik = KetidaksesuaianPlastik::where('uuid', $uuid)->firstOrFail();
            $user = auth()->user();
            $type = $request->input('type');
            
            // Validasi role dan tipe approval
            $allowedRoles = [
                'qc' => [1, 3, 5], // Role 1, 3, 5 bisa approve QC
                'produksi' => [2], // Role 2 bisa approve Produksi
                'spv' => [4] // Role 4 bisa approve SPV
            ];
            
            if (!isset($allowedRoles[$type]) || !in_array($user->id_role, $allowedRoles[$type])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk melakukan approval ini.'
                ], 403);
            }
            
            // Validasi sequential approval
            if ($type === 'produksi' && !$ketidaksesuaianPlastik->approved_by_qc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data harus disetujui oleh QC terlebih dahulu.'
                ], 400);
            }
            
            if ($type === 'spv' && !$ketidaksesuaianPlastik->approved_by_produksi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data harus disetujui oleh Produksi terlebih dahulu.'
                ], 400);
            }
            
            // Cek apakah sudah di-approve sebelumnya
            $approvalField = "approved_by_{$type}";
            if ($ketidaksesuaianPlastik->$approvalField) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data sudah disetujui sebelumnya.'
                ], 400);
            }
            
            // Update approval
            $ketidaksesuaianPlastik->update([
                $approvalField => true,
                "{$type}_approved_by" => $user->id,
                "{$type}_approved_at" => now()
            ]);
            
            // Log activity (jika ada model log)
            // KetidaksesuaianPlastikLog::create([
            //     'ketidaksesuaian_plastik_id' => $ketidaksesuaianPlastik->id,
            //     'user_id' => $user->id,
            //     'action' => 'approve',
            //     'description' => "Data disetujui oleh {$type}: {$user->name}",
            //     'old_values' => json_encode([$approvalField => false]),
            //     'new_values' => json_encode([$approvalField => true])
            // ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disetujui.'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Approval failed', [
                'error' => $e->getMessage(),
                'uuid' => $uuid,
                'type' => $type,
                'user_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyetujui data.'
            ], 500);
        }
    }
}
