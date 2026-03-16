<?php

namespace App\Http\Controllers;

use App\Models\ProdukForming;
use App\Models\ProdukFormingLog;
use App\Models\JenisProduk;
use App\Models\DataShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProdukFormingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        $query = ProdukForming::with(['produk', 'plan', 'shift', 'user', 'qcApprover', 'produksiApprover', 'spvApprover']);
        
        // Filter berdasarkan plan user jika bukan superadmin
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $search = request('search');
        if($search) {
            $query->whereHas('produk', function($q) use ($search) {
                $q->where('nama_produk', 'LIKE', '%' . $search . '%');
            });
        }
        
        $produkFormings = $query->orderBy('tanggal', 'desc')->paginate(10);
        
        return view('qc-sistem.produk_forming.index', compact('produkFormings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Get only Forming products - filter by plan if not superadmin
        $query = JenisProduk::where('status_bahan', 'forming');
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        $produks = $query->get();
        
        if ($user->role === 'superadmin') {      
            $shifts = DataShift::all();
          
        } else {
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        }

     
        
        return view('qc-sistem.produk_forming.create', compact('produks', 'shifts'));
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
             
              'id_produk' => 'required|exists:jenis_produk,id',
              'id_shift' => 'required|exists:data_shift,id',
             'tanggal' => 'required|date_format:d-m-Y', 
             'jam' => 'required',
              'bahan_baku' => 'nullable|array',
              'bahan_penunjang' => 'nullable|array',
              'kemasan_plastik' => 'nullable|integer|min:1|max:6',
              'kemasan_karton' => 'nullable|integer|min:1|max:6',
              'labelisasi_plastik' => 'nullable|integer|min:1|max:2',
              'labelisasi_karton' => 'nullable|integer|min:1|max:2',
              'autogrind' => 'nullable|integer|min:3|max:8',
              'bowlcutter' => 'nullable|integer|min:3|max:8',
              'ayakan_seasoning' => 'nullable|integer|min:3|max:8',
              'unimix' => 'nullable|integer|min:3|max:8',
              'revoformer' => 'nullable|integer|min:3|max:8',
              'better_mixer' => 'nullable|integer|min:3|max:8',
              'wet_coater' => 'nullable|integer|min:3|max:8',
              'breader' => 'nullable|integer|min:3|max:8',
              'frayer_1' => 'nullable|integer|min:3|max:8',
              'frayer_2' => 'nullable|integer|min:3|max:8',
              'iqf_jbt' => 'nullable|integer|min:3|max:8',
              'keranjang' => 'nullable|integer|min:3|max:8',
              'timbangan' => 'nullable|integer|min:3|max:8',
              'mhw' => 'nullable|integer|min:3|max:8',
              'foot_sealer' => 'nullable|integer|min:3|max:8',
              'metal_detector' => 'nullable|integer|min:3|max:8',
              'rotary_table' => 'nullable|integer|min:3|max:8',
              'carton_sealer' => 'nullable|integer|min:3|max:8',
              'meatcar' => 'nullable|integer|min:3|max:8',
              'check_weigher_bag' => 'nullable|integer|min:3|max:8',
              'check_weigher_box' => 'nullable|integer|min:3|max:8',
              'penilaian' => 'nullable|array',
              'tindakan_koreksi' => 'nullable|string',
              'verifikasi' => 'nullable|string|in:✓,✗'
          ]);
      }else{
        $request->validate([
             
              'id_produk' => 'required|exists:jenis_produk,id',
              'id_shift' => 'required|exists:data_shift,id',
              'tanggal' => 'required|date_format:d-m-Y H:i:s',
                'jam' => 'required',
              'bahan_baku' => 'nullable|array',
              'bahan_penunjang' => 'nullable|array',
              'kemasan_plastik' => 'nullable|integer|min:1|max:6',
              'kemasan_karton' => 'nullable|integer|min:1|max:6',
              'labelisasi_plastik' => 'nullable|integer|min:1|max:2',
              'labelisasi_karton' => 'nullable|integer|min:1|max:2',
              'autogrind' => 'nullable|integer|min:3|max:8',
              'bowlcutter' => 'nullable|integer|min:3|max:8',
              'ayakan_seasoning' => 'nullable|integer|min:3|max:8',
              'unimix' => 'nullable|integer|min:3|max:8',
              'revoformer' => 'nullable|integer|min:3|max:8',
              'better_mixer' => 'nullable|integer|min:3|max:8',
              'wet_coater' => 'nullable|integer|min:3|max:8',
              'breader' => 'nullable|integer|min:3|max:8',
              'frayer_1' => 'nullable|integer|min:3|max:8',
              'frayer_2' => 'nullable|integer|min:3|max:8',
              'iqf_jbt' => 'nullable|integer|min:3|max:8',
              'keranjang' => 'nullable|integer|min:3|max:8',
              'timbangan' => 'nullable|integer|min:3|max:8',
              'mhw' => 'nullable|integer|min:3|max:8',
              'foot_sealer' => 'nullable|integer|min:3|max:8',
              'metal_detector' => 'nullable|integer|min:3|max:8',
              'rotary_table' => 'nullable|integer|min:3|max:8',
              'carton_sealer' => 'nullable|integer|min:3|max:8',
              'meatcar' => 'nullable|integer|min:3|max:8',
              'check_weigher_bag' => 'nullable|integer|min:3|max:8',
              'check_weigher_box' => 'nullable|integer|min:3|max:8',
              'penilaian' => 'nullable|array',
              'tindakan_koreksi' => 'nullable|string',
              'verifikasi' => 'nullable|string|in:✓,✗'
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
      $data['user_id'] = $user->id;
        $data['id_plan'] = $user->id_plan;
        $data['tanggal'] = $tanggalData;
        $data['jam'] = $request->jam;
         
        // Ensure arrays are properly formatted
        if (isset($data['bahan_baku']) && is_array($data['bahan_baku'])) {
            $data['bahan_baku'] = array_values(array_filter($data['bahan_baku'], function($item) {
                return is_array($item) && isset($item['nama']) && isset($item['penilaian']) && 
                       trim($item['nama']) !== '' && trim($item['penilaian']) !== '';
            }));
        }
        
        if (isset($data['bahan_penunjang']) && is_array($data['bahan_penunjang'])) {
            $data['bahan_penunjang'] = array_values(array_filter($data['bahan_penunjang'], function($item) {
                return is_array($item) && isset($item['nama']) && isset($item['penilaian']) && 
                       trim($item['nama']) !== '' && trim($item['penilaian']) !== '';
            }));
        }
        
        if (isset($data['penilaian']) && is_array($data['penilaian'])) {
            $data['penilaian'] = array_values(array_filter($data['penilaian'], function($item) {
                return is_array($item) && isset($item['aspek']) && isset($item['nilai']) && 
                       trim($item['aspek']) !== '' && trim($item['nilai']) !== '';
            }));
        }

        ProdukForming::create($data);

        return redirect()->route('produk-forming.index')
                        ->with('success', 'Data produk forming berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $produkForming = ProdukForming::with(['produk', 'plan', 'shift', 'user'])->where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check authorization
        if ($user->role !== 'superadmin' && $produkForming->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('qc-sistem.produk_forming.show', compact('produkForming'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $produkForming = ProdukForming::with(['produk', 'plan', 'shift', 'user'])->where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check authorization
        if ($user->role !== 'superadmin' && $produkForming->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized action.');
        }
        
        // Get only Forming products - filter by plan if not superadmin
        $query = JenisProduk::where('status_bahan', 'forming');
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        $produks = $query->get();
        
        $shifts = DataShift::all();
        
        return view('qc-sistem.produk_forming.edit', compact('produkForming', 'produks', 'shifts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $produkForming = ProdukForming::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check authorization
        if ($user->role !== 'superadmin' && $produkForming->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
        
            'id_produk' => 'required|exists:jenis_produk,id',
            'id_shift' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
            'bahan_baku' => 'nullable|array',
            'bahan_penunjang' => 'nullable|array',
            'kemasan_plastik' => 'nullable|integer|min:1|max:6',
            'kemasan_karton' => 'nullable|integer|min:1|max:6',
            'labelisasi_plastik' => 'nullable|integer|min:1|max:2',
            'labelisasi_karton' => 'nullable|integer|min:1|max:2',
            'autogrind' => 'nullable|integer|min:3|max:8',
            'bowlcutter' => 'nullable|integer|min:3|max:8',
            'ayakan_seasoning' => 'nullable|integer|min:3|max:8',
            'unimix' => 'nullable|integer|min:3|max:8',
            'revoformer' => 'nullable|integer|min:3|max:8',
            'better_mixer' => 'nullable|integer|min:3|max:8',
            'wet_coater' => 'nullable|integer|min:3|max:8',
            'breader' => 'nullable|integer|min:3|max:8',
            'frayer_1' => 'nullable|integer|min:3|max:8',
            'frayer_2' => 'nullable|integer|min:3|max:8',
            'iqf_jbt' => 'nullable|integer|min:3|max:8',
            'keranjang' => 'nullable|integer|min:3|max:8',
            'timbangan' => 'nullable|integer|min:3|max:8',
            'mhw' => 'nullable|integer|min:3|max:8',
            'foot_sealer' => 'nullable|integer|min:3|max:8',
            'metal_detector' => 'nullable|integer|min:3|max:8',
            'rotary_table' => 'nullable|integer|min:3|max:8',
            'carton_sealer' => 'nullable|integer|min:3|max:8',
            'meatcar' => 'nullable|integer|min:3|max:8',
            'check_weigher_bag' => 'nullable|integer|min:3|max:8',
            'check_weigher_box' => 'nullable|integer|min:3|max:8',
            'penilaian' => 'nullable|array',
            'tindakan_koreksi' => 'nullable|string',
            'verifikasi' => 'nullable|string|in:✓,✗'
        ]);

        $data = $request->all();
        
        // Ensure arrays are properly formatted
        if (isset($data['bahan_baku']) && is_array($data['bahan_baku'])) {
            $data['bahan_baku'] = array_values(array_filter($data['bahan_baku'], function($item) {
                return is_array($item) && isset($item['nama']) && isset($item['penilaian']) && 
                       trim($item['nama']) !== '' && trim($item['penilaian']) !== '';
            }));
        }
        
        if (isset($data['bahan_penunjang']) && is_array($data['bahan_penunjang'])) {
            $data['bahan_penunjang'] = array_values(array_filter($data['bahan_penunjang'], function($item) {
                return is_array($item) && isset($item['nama']) && isset($item['penilaian']) && 
                       trim($item['nama']) !== '' && trim($item['penilaian']) !== '';
            }));
        }
        
        if (isset($data['penilaian']) && is_array($data['penilaian'])) {
            $data['penilaian'] = array_values(array_filter($data['penilaian'], function($item) {
                return is_array($item) && isset($item['aspek']) && isset($item['nilai']) && 
                       trim($item['aspek']) !== '' && trim($item['nilai']) !== '';
            }));
        }
        
        $produkForming->update($data);

        return redirect()->route('produk-forming.index')
                        ->with('success', 'Data produk forming berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $produkForming = ProdukForming::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check authorization
        if ($user->role !== 'superadmin' && $produkForming->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized action.');
        }
        
        $produkForming->delete();

        return redirect()->route('produk-forming.index')
                        ->with('success', 'Data produk forming berhasil dihapus.');
    }

    /**
     * Show logs for a specific produk forming record
     */
    public function showLogs($uuid)
    {
        $produkForming = ProdukForming::with(['produk', 'plan', 'shift', 'user'])->where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check authorization
        if ($user->role !== 'superadmin' && $produkForming->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('qc-sistem.produk_forming.logs', compact('produkForming'));
    }

    /**
     * Get logs data in JSON format for DataTables
     */
    public function getLogsJson($uuid)
    {
        $produkForming = ProdukForming::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check authorization
        if ($user->role !== 'superadmin' && $produkForming->id_plan !== $user->id_plan) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $logs = ProdukFormingLog::with('user')
            ->where('produk_forming_uuid', $uuid)
            ->orderBy('created_at', 'desc')
            ->get();

        $data = [];
        foreach ($logs as $log) {
            $data[] = [
                'tanggal' => $log->created_at->format('d-m-Y H:i:s'),
                'user' => $log->user->name ?? 'Unknown',
                'field_yang_diubah' => $log->nama_field,
                'deskripsi_perubahan' => $log->deskripsi_perubahan,
                'ip_address' => $log->ip_address
            ];
        }

        return response()->json(['data' => $data]);
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
        $query = ProdukForming::with(['produk', 'plan', 'shift', 'user', 'qcApprover', 'produksiApprover', 'spvApprover']);
        
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
        
        // Note: kode_form is not used for filtering, only saved to database
        
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
            $query_for_update = ProdukForming::query();
            
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
            $errorMessage = 'Tidak ada data Produk Forming yang sesuai dengan filter yang dipilih.';
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
                    <h1>Data Produk Forming Tidak Ditemukan</h1>
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
        
        $pdf = \PDF::loadView('qc-sistem.produk_forming.export_pdf', compact('data', 'filterInfo'))
                   ->setPaper('letter', 'portrait');
        
        $filename = 'produk-forming-' . date('Y-m-d-H-i-s') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Approve data with role-based validation
     */
    public function approve(Request $request, $uuid)
    {
        $request->validate([
            'type' => 'required|in:qc,produksi,spv'
        ]);

        $produkForming = ProdukForming::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        $type = $request->type;

        // Role-based access control following PembuatanSample pattern
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
        if ($type === 'produksi' && !$produkForming->approved_by_qc) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh QC terlebih dahulu.'
            ], 400);
        }

        if ($type === 'spv' && !$produkForming->approved_by_produksi) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh Produksi terlebih dahulu.'
            ], 400);
        }

        // Check if already approved
        $approvalField = "approved_by_{$type}";
        if ($produkForming->$approvalField) {
            return response()->json([
                'success' => false,
                'message' => 'Data sudah disetujui sebelumnya.'
            ], 400);
        }

        // Update approval
        $produkForming->update([
            $approvalField => true,
            "{$type}_approved_by" => $user->id,
            "{$type}_approved_at" => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disetujui.'
        ]);
    }
}
