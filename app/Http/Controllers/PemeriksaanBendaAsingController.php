<?php

namespace App\Http\Controllers;

use App\Models\PemeriksaanBendaAsing;
use App\Models\JenisProduk;
use App\Models\Plan;
use App\Models\DataShift;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\PemeriksaanBendaAsingLog;
use Barryvdh\DomPDF\Facade\Pdf;

class PemeriksaanBendaAsingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $query = PemeriksaanBendaAsing::with(['plan', 'user', 'shift', 'produk', 'qcApprover', 'produksiApprover', 'spvApprover']);

        // Role-based filtering
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        // Handle search
        $search = request('search');
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('jenis_kontaminasi', 'like', '%' . $search . '%')
                  ->orWhere('kode_produksi', 'like', '%' . $search . '%')
                  ->orWhereHas('produk', function($produkQuery) use ($search) {
                      $produkQuery->where('nama_produk', 'like', '%' . $search . '%');
                  });
            });
        }

        // Handle per_page
        $perPage = request()->get('per_page', 10);
        $perPage = in_array($perPage, [5, 10, 25, 50, 100]) ? $perPage : 10;

        $data = $query->orderBy('created_at', 'desc')->paginate($perPage);
        
        return view('qc-sistem.pemeriksaan_benda_asing.index', compact('data', 'search', 'perPage'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Get data berdasarkan role
        if ($user->role === 'superadmin') {
            $produks = JenisProduk::all();
            $shifts = DataShift::all();
            $plans = Plan::all();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $plans = Plan::where('id', $user->id_plan)->get();
        }

        return view('qc-sistem.pemeriksaan_benda_asing.create', compact('produks', 'shifts', 'plans'));
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
                 'shift_id' => 'required|exists:data_shift,id',
                 'id_produk' => 'required|exists:jenis_produk,id',
                 'berat' => 'nullable|numeric',
                'tanggal' => 'required|date_format:d-m-Y', // Hanya validasi format tanggal saja
                 'jam' => 'required',
                 'jenis_kontaminasi' => 'required|string|max:255',
                 'bukti' => 'nullable|image|mimes:jpeg,png,jpg',
                 'kode_produksi' => 'required|string|max:255',
                 'ukuran_kontaminasi' => 'required|string|max:255',
                 'ditemukan' => 'required|string|max:255',
                 'analisa_masalah' => 'nullable|string',
                 'koreksi' => 'nullable|string',
                 'tindak_korektif' => 'nullable|string',
                 'diketahui' => 'nullable|string|max:255',
             ]);

         }else{
            $request->validate([
                 'shift_id' => 'required|exists:data_shift,id',
                 'id_produk' => 'required|exists:jenis_produk,id',
                 'berat' => 'nullable|numeric',
                  'tanggal' => 'required|date_format:d-m-Y H:i:s',
                  'jam' => 'required',
                 'jenis_kontaminasi' => 'required|string|max:255',
                 'bukti' => 'nullable|image|mimes:jpeg,png,jpg',
                 'kode_produksi' => 'required|string|max:255',
                 'ukuran_kontaminasi' => 'required|string|max:255',
                 'ditemukan' => 'required|string|max:255',
                 'analisa_masalah' => 'nullable|string',
                 'koreksi' => 'nullable|string',
                 'tindak_korektif' => 'nullable|string',
                 'diketahui' => 'nullable|string|max:255',
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
        
        // Get shift untuk mendapatkan id_plan
        $selectedShift = DataShift::findOrFail($request->shift_id);

        // Handle file upload bukti
        $bukti_path = null;
        if ($request->hasFile('bukti')) {
            $file = $request->file('bukti');
            $filename = time() . '_' . uniqid() . '_bukti.jpg';
            $image = Image::make($file)->encode('jpg', 70);
            Storage::disk('public')->put('uploads/pemeriksaan_benda_asing/' . $filename, $image);
            $bukti_path = 'uploads/pemeriksaan_benda_asing/' . $filename;
        }

        // Create data
        PemeriksaanBendaAsing::create([
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'id_plan' => $selectedShift->id_plan,
            'shift_id' => $request->shift_id,
            'id_produk' => $request->id_produk,
            'berat' => $request->berat,
            'tanggal' => $tanggalData,
            'jam' => $request->jam,
            'jenis_kontaminasi' => $request->jenis_kontaminasi,
            'bukti' => $bukti_path,
            'kode_produksi' => $request->kode_produksi,
            'ukuran_kontaminasi' => $request->ukuran_kontaminasi,
            'ditemukan' => $request->ditemukan,
            'analisa_masalah' => $request->analisa_masalah,
            'koreksi' => $request->koreksi,
            'tindak_korektif' => $request->tindak_korektif,
            'diketahui' => $request->diketahui,
        ]);

        return redirect()->route('pemeriksaan-benda-asing.index')
            ->with('success', 'Data pemeriksaan benda asing berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $pemeriksaan = PemeriksaanBendaAsing::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check access
        if (!$pemeriksaan->canAccess($user)) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data ini.');
        }

        return view('qc-sistem.pemeriksaan_benda_asing.show', compact('pemeriksaan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $pemeriksaan = PemeriksaanBendaAsing::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check access
        if (!$pemeriksaan->canAccess($user)) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        // Get data berdasarkan role
        if ($user->role === 'superadmin') {
            $produks = JenisProduk::all();
            $shifts = DataShift::all();
            $plans = Plan::all();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $plans = Plan::where('id', $user->id_plan)->get();
        }

        return view('qc-sistem.pemeriksaan_benda_asing.edit', compact('pemeriksaan', 'produks', 'shifts', 'plans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $request->validate([
            'shift_id' => 'required|exists:data_shift,id',
            'id_produk' => 'required|exists:jenis_produk,id',
            'berat' => 'nullable|numeric',
            'tanggal' => 'required|date',
            'jam' => 'required',
            'jenis_kontaminasi' => 'required|string|max:255',
            'bukti' => 'nullable|image|mimes:jpeg,png,jpg',
            'kode_produksi' => 'required|string|max:255',
            'ukuran_kontaminasi' => 'required|string|max:255',
            'ditemukan' => 'required|string|max:255',
            'analisa_masalah' => 'nullable|string',
            'koreksi' => 'nullable|string',
            'tindak_korektif' => 'nullable|string',
            'diketahui' => 'nullable|string|max:255',
        ]);

        $pemeriksaan = PemeriksaanBendaAsing::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check access
        if (!$pemeriksaan->canAccess($user)) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        // Handle file upload bukti
        $bukti_path = $pemeriksaan->bukti;
        if ($request->hasFile('bukti')) {
            // Delete old file if exists
            if ($bukti_path && Storage::disk('public')->exists($bukti_path)) {
                Storage::disk('public')->delete($bukti_path);
            }
            
            $file = $request->file('bukti');
            $filename = time() . '_' . uniqid() . '_bukti.jpg';
            $image = Image::make($file)->encode('jpg', 70);
            Storage::disk('public')->put('uploads/pemeriksaan_benda_asing/' . $filename, $image);
            $bukti_path = 'uploads/pemeriksaan_benda_asing/' . $filename;
        }

        // Update data
        $pemeriksaan->update([
            'shift_id' => $request->shift_id,
            'id_produk' => $request->id_produk,
            'berat' => $request->berat,
            'tanggal' => $request->tanggal,
            'jam' => $request->jam,
            'jenis_kontaminasi' => $request->jenis_kontaminasi,
            'bukti' => $bukti_path,
            'kode_produksi' => $request->kode_produksi,
            'ukuran_kontaminasi' => $request->ukuran_kontaminasi,
            'ditemukan' => $request->ditemukan,
            'analisa_masalah' => $request->analisa_masalah,
            'koreksi' => $request->koreksi,
            'tindak_korektif' => $request->tindak_korektif,
            'diketahui' => $request->diketahui,
        ]);

        return redirect()->route('pemeriksaan-benda-asing.index')
            ->with('success', 'Data pemeriksaan benda asing berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $pemeriksaan = PemeriksaanBendaAsing::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check access
        if (!$pemeriksaan->canAccess($user)) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        // Delete file if exists
        if ($pemeriksaan->bukti && Storage::disk('public')->exists($pemeriksaan->bukti)) {
            Storage::disk('public')->delete($pemeriksaan->bukti);
        }

        $pemeriksaan->delete();

        return redirect()->route('pemeriksaan-benda-asing.index')
            ->with('success', 'Data pemeriksaan benda asing berhasil dihapus.');
    }
    /**
    * Show logs for the specified resource.
    */
    public function showLogs($uuid)
    {
        $item = PemeriksaanBendaAsing::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        
        // Authorization check
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized action.');
        }

        $logs = PemeriksaanBendaAsingLog::where('pemeriksaan_benda_asing_id', $item->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('qc-sistem.pemeriksaan_benda_asing.logs', compact('item', 'logs'));
    }

    /**
     * Get logs JSON for the specified resource.
     */
    public function getLogsJson($uuid)
    {
        $item = PemeriksaanBendaAsing::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        
        // Authorization check
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $logs = PemeriksaanBendaAsingLog::where('pemeriksaan_benda_asing_id', $item->id)
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
                'id_produk' => 'nullable|integer',
                'kode_form' => 'required|string|max:50'
            ]);

            $user = Auth::user();
            
            $query = PemeriksaanBendaAsing::with(['plan', 'user', 'shift', 'produk', 'qcApprover', 'produksiApprover', 'spvApprover'])
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

            if ($request->id_produk) {
                $query->where('id_produk', $request->id_produk);
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
                if ($request->id_produk) {
                    $produk = JenisProduk::find($request->id_produk);
                    $filterInfo[] = 'Produk: ' . ($produk ? $produk->nama_produk : 'Unknown');
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
                'id_produk' => $request->id_produk,
                'kode_form' => $request->kode_form
            ];

            // Generate PDF
            $pdf = Pdf::loadView('qc-sistem.pemeriksaan_benda_asing.export_pdf', compact('data', 'filters'))
                ->setPaper('a4', 'portrait');

            $filename = 'pemeriksaan_benda_asing_' . date('Y-m-d_H-i-s') . '.pdf';
            
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

        $pemeriksaanBendaAsing = PemeriksaanBendaAsing::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        $type = $request->type;

        // Role-based access control following Produk Forming pattern
        $allowedRoles = [
            'qc' => [1, 3, 5], // Role 1&5 (QC only), Role 3 (QC only)
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
        if ($type === 'produksi' && !$pemeriksaanBendaAsing->approved_by_qc) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh QC terlebih dahulu.'
            ], 400);
        }

        if ($type === 'spv' && !$pemeriksaanBendaAsing->approved_by_produksi) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh Produksi terlebih dahulu.'
            ], 400);
        }

        // Check if already approved
        $approvalField = "approved_by_{$type}";
        if ($pemeriksaanBendaAsing->$approvalField) {
            return response()->json([
                'success' => false,
                'message' => 'Data sudah disetujui sebelumnya.'
            ], 400);
        }

        // Update approval
        $pemeriksaanBendaAsing->update([
            $approvalField => true,
            "{$type}_approved_by" => $user->id,
            "{$type}_approved_at" => now()
        ]);

        // Log the approval activity
        PemeriksaanBendaAsingLog::create([
            'uuid' => Str::uuid(),
            'pemeriksaan_benda_asing_id' => $pemeriksaanBendaAsing->id,
            'pemeriksaan_benda_asing_uuid' => $pemeriksaanBendaAsing->uuid,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->role,
            'aksi' => 'approve',
            'field_yang_diubah' => [$approvalField],
            'nilai_lama' => ['false'],
            'nilai_baru' => ['true'],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'keterangan' => "Data disetujui oleh {$type}: {$user->name}"
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disetujui.'
        ]);
    }
    
}
