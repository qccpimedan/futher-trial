<?php

namespace App\Http\Controllers;

use App\Models\AreaProses;
use App\Models\DataShift;
use App\Models\InputArea;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AreaProsesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        
        $query = AreaProses::with(['user', 'plan', 'area', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover']);
        
        // Role-based data filtering
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        
        // Optional filter to show history per group_uuid
        if ($request->filled('group_uuid')) {
            $query->where('group_uuid', $request->get('group_uuid'));
        } else {
            // Default: tampilkan hanya record ORIGINAL (bukan hasil per 2 jam)
            // Original adalah baris di mana group_uuid null (data lama) atau group_uuid == uuid (kepala grup)
            $query->where(function($q) {
                $q->whereNull('group_uuid')
                  ->orWhereColumn('group_uuid', 'uuid');
            });
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('tanggal', 'LIKE', '%' . $search . '%')
                  ->orWhereHas('area', function($sq) use ($search) {
                      $sq->where('area', 'LIKE', '%' . $search . '%');
                  });
            });
        }
        
        $areaProses = $query->orderBy('tanggal', 'desc')->paginate(10);

        return view('qc-sistem.area_proses.index', compact('areaProses', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Get areas based on user role
        if ($user->role === 'superadmin') {
            $areas = InputArea::with('plan')->get();
            $shifts = DataShift::with('plan')->get();
        } else {
            $areas = InputArea::with('plan')
                ->where('id_plan', $user->id_plan)
                ->get();
            $shifts = DataShift::with('plan')
                ->where('id_plan', $user->id_plan)
                ->get();
        }

        return view('qc-sistem.area_proses.create', compact('areas', 'shifts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'area_id' => 'required|exists:input_area,id',
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
            'jam' => 'required|string|max:10',
            'kebersihan_ruangan' => 'required|in:OK,Kotor',
            'kebersihan_karyawan' => 'required|in:OK,Kotor',
            'pemeriksaan_suhu_ruang' => 'required|string|max:50',
            'ketidaksesuaian' => 'nullable|string|max:1000',
            'tindakan_koreksi' => 'nullable|string|max:1000',
              'kondisi_barang' => 'nullable|string', // tambahkan ini
        ]);

        // Get the selected area to validate plan access
        $selectedArea = InputArea::findOrFail($request->area_id);
        $selectedShift = DataShift::findOrFail($request->shift_id);
        
        // Additional validation for non-superadmin users
        if ($user->role !== 'superadmin') {
            if ($selectedArea->id_plan !== $user->id_plan) {
                return back()->withErrors(['area_id' => 'Anda tidak memiliki akses ke area ini.']);
            }
            if ($selectedShift->id_plan !== $user->id_plan) {
                return back()->withErrors(['shift_id' => 'Anda tidak memiliki akses ke shift ini.']);
            }
        }

        AreaProses::create([
            'user_id' => $user->id,
            'id_plan' => $selectedArea->id_plan,
            'area_id' => $request->area_id,
            'shift_id' => $request->shift_id,
            'tanggal' => $request->tanggal,
            'jam' => $request->jam,
            'kebersihan_ruangan' => $request->kebersihan_ruangan,
            'kebersihan_karyawan' => $request->kebersihan_karyawan,
            'pemeriksaan_suhu_ruang' => $request->pemeriksaan_suhu_ruang,
            'ketidaksesuaian' => $request->ketidaksesuaian,
            'tindakan_koreksi' => $request->tindakan_koreksi,
              'kondisi_barang' => $request->kondisi_barang,
        ]);

        return redirect()->route('area-proses.index')
            ->with('success', 'Data area proses berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $user = Auth::user();
        $areaProses = AreaProses::where('uuid', $uuid)->firstOrFail();

        // Check access based on user role
        if ($user->role !== 'superadmin' && $areaProses->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return view('qc-sistem.area_proses.show', compact('areaProses'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $user = Auth::user();
        $areaProses = AreaProses::where('uuid', $uuid)->firstOrFail();

        // Check access based on user role
        if ($user->role !== 'superadmin' && $areaProses->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        // Get areas based on user role
        if ($user->role === 'superadmin') {
            $areas = InputArea::with('plan')->get();
            $shifts = DataShift::with('plan')->get();
        } else {
            $areas = InputArea::with('plan')
                ->where('id_plan', $user->id_plan)
                ->get();
            $shifts = DataShift::with('plan')
                ->where('id_plan', $user->id_plan)
                ->get();
        }

        return view('qc-sistem.area_proses.edit', compact('areaProses', 'areas', 'shifts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $user = Auth::user();
        $areaProses = AreaProses::where('uuid', $uuid)->firstOrFail();

        // Check access based on user role
        if ($user->role !== 'superadmin' && $areaProses->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        $request->validate([
            'area_id' => 'required|exists:input_area,id',
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
            'jam' => 'required|string|max:10',
            'kebersihan_ruangan' => 'required|in:OK,Tidak OK',
            'kebersihan_karyawan' => 'required|in:OK,Tidak OK',
            'pemeriksaan_suhu_ruang' => 'required|string|max:50',
            'ketidaksesuaian' => 'nullable|string|max:1000',
            'tindakan_koreksi' => 'nullable|string|max:1000',
             'kondisi_barang' => 'nullable|string',
        ]);

        // Get the selected area to validate plan access
        $selectedArea = InputArea::findOrFail($request->area_id);
        $selectedShift = DataShift::findOrFail($request->shift_id);
        
        // Additional validation for non-superadmin users
        if ($user->role !== 'superadmin') {
            if ($selectedArea->id_plan !== $user->id_plan) {
                return back()->withErrors(['area_id' => 'Anda tidak memiliki akses ke area ini.']);
            }
            if ($selectedShift->id_plan !== $user->id_plan) {
                return back()->withErrors(['shift_id' => 'Anda tidak memiliki akses ke shift ini.']);
            }
        }

        $areaProses->update([
            'id_plan' => $selectedArea->id_plan,
            'area_id' => $request->area_id,
            'shift_id' => $request->shift_id,
            'tanggal' => $request->tanggal,
            'jam' => $request->jam,
            'kebersihan_ruangan' => $request->kebersihan_ruangan,
            'kebersihan_karyawan' => $request->kebersihan_karyawan,
            'pemeriksaan_suhu_ruang' => $request->pemeriksaan_suhu_ruang,
            'ketidaksesuaian' => $request->ketidaksesuaian,
            'tindakan_koreksi' => $request->tindakan_koreksi,
            'kondisi_barang' => $request->kondisi_barang,
        ]);

        return redirect()->route('area-proses.index')
            ->with('success', 'Data area proses berhasil diperbarui.');
    }

    /**
     * Show the form for editing per 2 hours (create new record).
     */
    public function twoHourEdit($uuid)
    {
        $areaProses = AreaProses::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check access for non-superadmin users
        if ($user->role !== 'superadmin' && $areaProses->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }
        
        if ($user->role === 'superadmin') {
            $areas = InputArea::with('plan')->get();
            $shifts = DataShift::with('plan')->get();
        } else {
            $areas = InputArea::with('plan')
                ->where('id_plan', $user->id_plan)
                ->get();
            $shifts = DataShift::with('plan')
                ->where('id_plan', $user->id_plan)
                ->get();
        }
        
        $twoHour = true; // flag untuk view
        return view('qc-sistem.area_proses.edit', compact('areaProses', 'areas', 'shifts', 'twoHour'));
    }

    /**
     * Store new record for 2 hour edit.
     */
    public function twoHourStore(Request $request, $uuid)
    {
        $user = Auth::user();
        $request->validate([
            'area_id' => 'required|exists:input_area,id',
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
            'jam' => 'required|string|max:10',
            'kebersihan_ruangan' => 'required|in:OK,Kotor',
            'kebersihan_karyawan' => 'required|in:OK,Kotor',
            'pemeriksaan_suhu_ruang' => 'required|string|max:50',
            'ketidaksesuaian' => 'nullable|string|max:1000',
            'tindakan_koreksi' => 'nullable|string|max:1000',
               'kondisi_barang' => 'nullable|string',
            
        ]);

        $original = AreaProses::where('uuid', $uuid)->firstOrFail();
        
        // Check access for non-superadmin users
        if ($user->role !== 'superadmin' && $original->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }
        
        $groupUuid = $original->group_uuid ?: $original->uuid; // gunakan uuid asli sebagai group jika belum ada

        // Get the selected area to validate plan access
        $selectedArea = InputArea::findOrFail($request->area_id);
        $selectedShift = DataShift::findOrFail($request->shift_id);
        
        // Additional validation for non-superadmin users
        if ($user->role !== 'superadmin') {
            if ($selectedArea->id_plan !== $user->id_plan) {
                return back()->withErrors(['area_id' => 'Anda tidak memiliki akses ke area ini.']);
            }
            if ($selectedShift->id_plan !== $user->id_plan) {
                return back()->withErrors(['shift_id' => 'Anda tidak memiliki akses ke shift ini.']);
            }
        }

        AreaProses::create([
            'uuid' => Str::uuid(),
            'group_uuid' => $groupUuid,
            'user_id' => $user->id,
            'id_plan' => $selectedArea->id_plan,
            'area_id' => $request->area_id,
            'shift_id' => $request->shift_id,
            'tanggal' => $request->tanggal,
            'jam' => $request->jam,
            'kebersihan_ruangan' => $request->kebersihan_ruangan,
            'kebersihan_karyawan' => $request->kebersihan_karyawan,
            'pemeriksaan_suhu_ruang' => $request->pemeriksaan_suhu_ruang,
            'ketidaksesuaian' => $request->ketidaksesuaian,
            'tindakan_koreksi' => $request->tindakan_koreksi,
            'kondisi_barang'  => $request->kondisi_barang,
        ]);

        // Pastikan original menyimpan group_uuid agar terhubung dalam riwayat
        if (!$original->group_uuid) {
            $original->update(['group_uuid' => $groupUuid]);
        }

        return redirect()->route('area-proses.index', ['group_uuid' => $groupUuid])
            ->with('success', 'Data per 2 jam berhasil disimpan sebagai record baru');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $user = Auth::user();
        $areaProses = AreaProses::where('uuid', $uuid)->firstOrFail();

        // Check access based on user role
        if ($user->role !== 'superadmin' && $areaProses->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        $areaProses->delete();

        return redirect()->route('area-proses.index')
            ->with('success', 'Data area proses berhasil dihapus.');
    }

    /**
     * Approve data with role-based validation
     */
    public function approve(Request $request, $uuid)
    {
        $request->validate([
            'type' => 'required|in:qc,produksi,spv'
        ]);

        $data = AreaProses::where('uuid', $uuid)->firstOrFail();
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
            'area' => 'nullable|string',
            'kode_form' => 'required|string'
        ]);

        $user = Auth::user();
        $query = AreaProses::with(['area', 'plan', 'shift', 'user', 'qcApprover', 'produksiApprover', 'spvApprover']);
        
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
        
        if ($request->area) {
            $query->whereHas('area', function($q) use ($request) {
                $q->where('area', $request->area);
            });
        }
        
        // Debug: Log the query and filters
        \Log::info('Area Proses PDF Export Query Debug', [
            'tanggal' => $request->tanggal,
            'shift' => $request->shift,
            'area' => $request->area,
            'kode_form' => $request->kode_form,
            'user_plan' => $user->id_plan,
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);
        
        // First, update kode_form for matching records before fetching data for PDF
        if ($request->kode_form) {
            $query_for_update = AreaProses::query();
            
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
            
            if ($request->area) {
                $query_for_update->whereHas('area', function($q) use ($request) {
                    $q->where('area', $request->area);
                });
            }
            
            // Update kode_form for matching records BEFORE fetching data
            $updated_count = $query_for_update->update(['kode_form' => $request->kode_form]);
            \Log::info('Updated kode_form for Area Proses records', ['count' => $updated_count, 'kode_form' => $request->kode_form]);
        }
        
        // Now fetch the updated data for PDF
        $data = $query->orderBy('tanggal', 'desc')->get();
        
        \Log::info('Area Proses PDF Export Data Count', ['count' => $data->count()]);
        
        // Jika tidak ada data, tampilkan halaman error HTML (tidak download PDF)
        if ($data->isEmpty()) {
            $errorMessage = 'Tidak ada data Area Proses yang sesuai dengan filter yang dipilih.';
            $filterInfo = [];
            
            if ($request->tanggal) {
                $filterInfo[] = 'Tanggal: ' . \Carbon\Carbon::parse($request->tanggal)->format('d-m-Y');
            }
            if ($request->shift) {
                $filterInfo[] = 'Shift: ' . $request->shift;
            }
            if ($request->area) {
                $filterInfo[] = 'Area: ' . $request->area;
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
                    <h1>Data Area Proses Tidak Ditemukan</h1>
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
            'area' => $request->area ?: 'Semua Area',
            'kode_form' => $request->kode_form
        ];
        
        $pdf = \PDF::loadView('qc-sistem.area_proses.export_pdf', compact('data', 'filterInfo'))
                   ->setPaper('letter', 'landscape');
        
        $filename = 'area-proses-' . date('Y-m-d-H-i-s') . '.pdf';
        
        return $pdf->download($filename);
    }
}
