<?php

namespace App\Http\Controllers;

use App\Models\BarangMudahPecah;
use App\Models\DataShift;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\BarangMudahPecahLog;
use App\Models\DataBarang;
use App\Models\InputArea;
use App\Models\SubArea;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class BarangMudahPecahController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        
        $query = BarangMudahPecah::with(['user', 'plan', 'shift', 'namaBarang', 'qcApprover', 'produksiApprover', 'spvApprover', 'area']);        
        // Role-based data filtering
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('namaBarang', function($qb) use ($search) {
                    $qb->where('nama_barang', 'LIKE', '%' . $search . '%');
                })
                ->orWhereHas('area', function($qa) use ($search) {
                    $qa->where('area', 'LIKE', '%' . $search . '%');
                })
                ->orWhere('tanggal', 'LIKE', '%' . $search . '%');
            });
        }
        
        $barangMudahPecah = $query->orderBy('tanggal', 'desc')->get();

        $groupedBarangMudahPecah = $barangMudahPecah
            ->groupBy(function ($item) {
                $tanggalKey = $item->tanggal ? Carbon::parse($item->tanggal)->format('Y-m-d') : 'no-date';
                $jamKey = $item->jam ? Carbon::parse($item->jam)->format('H:i') : 'no-time';
                $shiftKey = $item->shift_id ?? 'no-shift';
                $areaKey = $item->id_area ?? 'no-area';

                return $tanggalKey . '|' . $jamKey . '|' . $shiftKey . '|' . $areaKey;
            })
            ->sortByDesc(function ($group) {
                $first = $group->first();
                $tanggalKey = $first && $first->tanggal ? Carbon::parse($first->tanggal)->format('Y-m-d') : '0000-00-00';
                $jamKey = $first && $first->jam ? Carbon::parse($first->jam)->format('H:i') : '00:00';
                return $tanggalKey . ' ' . $jamKey;
            });

        // Paginate the collection manually
        $page = $request->get('page', 1);
        $perPage = 10;
        $paginatedGrouped = new \Illuminate\Pagination\LengthAwarePaginator(
            $groupedBarangMudahPecah->forPage($page, $perPage),
            $groupedBarangMudahPecah->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $inputAreas = \App\Models\InputArea::all();

        return view('qc-sistem.barang_mudah_pecah.index', [
            'groupedBarangMudahPecah' => $paginatedGrouped,
            'inputAreas' => $inputAreas,
            'search' => $search
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $areas = collect();
        $barangByArea = collect();
        
        // Get shifts based on user role
        if ($user->role === 'superadmin') {
            $shifts = DataShift::with('plan')->get();
            $plans = Plan::all();
            $dataBarang = DataBarang::all();
        } else {
            $shifts = DataShift::with('plan')
                ->where('id_plan', $user->id_plan)
                ->get();
            $plans = Plan::where('id', $user->id_plan)->get();
            $dataBarang = DataBarang::where('id_plan', $user->id_plan)->get();

            $areas = InputArea::where('id_plan', $user->id_plan)
                ->orderBy('area')
                ->get(['id', 'area']);

            $barangByArea = DataBarang::where('id_plan', $user->id_plan)
                ->orderBy('nama_barang')
                ->get(['id', 'id_area', 'nama_barang', 'jumlah'])
                ->groupBy('id_area');
        }

        return view('qc-sistem.barang_mudah_pecah.create', compact('shifts', 'plans', 'dataBarang', 'areas', 'barangByArea'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($request->input('id_sub_area_manual') === '') {
            $request->merge(['id_sub_area_manual' => null]);
        }

        $request->validate([
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
            'jam' => 'required|date_format:H:i',
            'items' => 'required|array|min:1',
            'items.*.id_area' => 'required|exists:input_area,id',
            'items.*.id_nama_barang' => 'required|exists:data_barang,id',
            'items.*.jumlah' => 'required|integer',
            'items.*.kondisi' => 'required|in:OK,Tidak OK',
            'is_manual' => 'nullable|boolean',
            'id_area_manual' => 'required_if:is_manual,1|exists:input_area,id',
            'id_sub_area_manual' => 'nullable|exists:sub_area,id',
            'nama_barang_manual' => 'required_if:is_manual,1|string|max:255',
            'jumlah_manual' => 'required_if:is_manual,1|integer',
            'nama_karyawan' => 'nullable|string|max:255',
            'kondisi_manual' => 'required_if:is_manual,1|in:OK,Tidak OK',
            'temuan_ketidaksesuaian' => 'nullable|string|max:1000',
        ]);

        // Get the selected shift to validate plan access and get plan_id
        $selectedShift = DataShift::findOrFail($request->shift_id);
        
        // Additional validation for non-superadmin users
        if ($user->role !== 'superadmin') {
            if ($selectedShift->id_plan !== $user->id_plan) {
                return back()->withErrors(['shift_id' => 'Anda tidak memiliki akses ke shift ini.']);
            }
        }

        $effectivePlanId = ($user->role === 'superadmin') ? $selectedShift->id_plan : $user->id_plan;

        foreach ((array) $request->items as $idx => $row) {
            $selectedArea = InputArea::findOrFail($row['id_area']);
            if ((int) $selectedArea->id_plan !== (int) $effectivePlanId) {
                return back()->withErrors(["items.$idx.id_area" => 'Area tidak sesuai dengan plan yang dipilih.'])->withInput();
            }

            $barang = DataBarang::findOrFail($row['id_nama_barang']);
            if ((int) $barang->id_plan !== (int) $effectivePlanId || (int) $barang->id_area !== (int) $row['id_area']) {
                return back()->withErrors(["items.$idx.id_nama_barang" => 'Nama barang tidak sesuai dengan Area yang dipilih.'])->withInput();
            }

            BarangMudahPecah::create([
                'user_id' => $user->id,
                'id_plan' => $effectivePlanId,
                'shift_id' => $request->shift_id,
                'tanggal' => $request->tanggal,
                'jam' => $request->jam,
                'id_area' => $row['id_area'],
                'id_sub_area' => null,
                'is_manual' => false,
                'id_nama_barang' => $row['id_nama_barang'],
                'nama_barang_manual' => null,
                'jumlah' => $row['jumlah'],
                'nama_karyawan' => null,
                'kondisi' => $row['kondisi'],
                'temuan_ketidaksesuaian' => $request->temuan_ketidaksesuaian,
            ]);
        }

        $isManual = $request->has('is_manual');
        if ($isManual) {
            $manualArea = InputArea::findOrFail($request->id_area_manual);
            if ((int) $manualArea->id_plan !== (int) $effectivePlanId) {
                return back()->withErrors(['id_area_manual' => 'Area manual tidak sesuai dengan plan yang dipilih.'])->withInput();
            }

            if ($request->filled('id_sub_area_manual')) {
                $subArea = SubArea::findOrFail($request->id_sub_area_manual);
                if ((int) $subArea->id_input_area !== (int) $request->id_area_manual) {
                    return back()->withErrors(['id_sub_area_manual' => 'Sub Area tidak sesuai dengan Area yang dipilih.'])->withInput();
                }
            }

            BarangMudahPecah::create([
                'user_id' => $user->id,
                'id_plan' => $effectivePlanId,
                'shift_id' => $request->shift_id,
                'tanggal' => $request->tanggal,
                'jam' => $request->jam,
                'id_area' => $request->id_area_manual,
                'id_sub_area' => $request->id_sub_area_manual,
                'is_manual' => true,
                'id_nama_barang' => null,
                'nama_barang_manual' => $request->nama_barang_manual,
                'jumlah' => $request->jumlah_manual,
                'nama_karyawan' => $request->nama_karyawan,
                'kondisi' => $request->kondisi_manual,
                'temuan_ketidaksesuaian' => $request->temuan_ketidaksesuaian,
            ]);
        }

        return redirect()->route('barang-mudah-pecah.index')
            ->with('success', 'Data barang mudah pecah berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $barangMudahPecah = BarangMudahPecah::with(['user', 'plan', 'shift', 'namaBarang', 'area', 'subArea'])
            ->where('uuid', $uuid)
            ->firstOrFail();
        $user = Auth::user();
        
        // Check access for non-superadmin users
        if ($user->role !== 'superadmin' && $barangMudahPecah->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        $groupItems = BarangMudahPecah::with(['user', 'plan', 'shift', 'namaBarang', 'area', 'subArea'])
            ->where('id_plan', $barangMudahPecah->id_plan)
            ->where('shift_id', $barangMudahPecah->shift_id)
            ->where('id_area', $barangMudahPecah->id_area)
            ->where('jam', $barangMudahPecah->jam)
            ->whereDate('tanggal', Carbon::parse($barangMudahPecah->tanggal)->format('Y-m-d'))
            ->orderBy('created_at', 'asc')
            ->get();

        return view('qc-sistem.barang_mudah_pecah.show', compact('barangMudahPecah', 'groupItems'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $barangMudahPecah = BarangMudahPecah::with(['shift', 'area'])->where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check access for non-superadmin users
        if ($user->role !== 'superadmin' && $barangMudahPecah->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        $groupItems = BarangMudahPecah::with(['namaBarang', 'area', 'subArea'])
            ->where('id_plan', $barangMudahPecah->id_plan)
            ->where('shift_id', $barangMudahPecah->shift_id)
            ->where('id_area', $barangMudahPecah->id_area)
            ->where('jam', $barangMudahPecah->jam)
            ->whereDate('tanggal', Carbon::parse($barangMudahPecah->tanggal)->format('Y-m-d'))
            ->orderBy('created_at', 'asc')
            ->get();

        // Get shifts based on user role
        if ($user->role === 'superadmin') {
            $shifts = DataShift::with('plan')->get();
            $plans = Plan::all();
            $dataBarang = DataBarang::all();
        } else {
            $shifts = DataShift::with('plan')
                ->where('id_plan', $user->id_plan)
                ->get();
            $plans = Plan::where('id', $user->id_plan)->get();
            $dataBarang = DataBarang::where('id_plan', $user->id_plan)->get();
        }

        return view('qc-sistem.barang_mudah_pecah.edit', compact('barangMudahPecah', 'groupItems', 'shifts', 'plans', 'dataBarang'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $barangMudahPecah = BarangMudahPecah::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check access for non-superadmin users
        if ($user->role !== 'superadmin' && $barangMudahPecah->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        // Jika edit via tabel (seperti create): update massal kondisi per item
        if ($request->has('items')) {
            $request->validate([
                'items' => 'required|array|min:1',
                'items.*.uuid' => 'required|exists:barang_mudah_pecah,uuid',
                'items.*.kondisi' => 'required|in:OK,Tidak OK',
                'temuan_ketidaksesuaian' => 'nullable|string|max:1000',
            ]);

            foreach ((array) $request->items as $idx => $row) {
                $item = BarangMudahPecah::where('uuid', $row['uuid'])->firstOrFail();

                if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
                    abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
                }

                if ((int) $item->id_plan !== (int) $barangMudahPecah->id_plan
                    || (int) $item->shift_id !== (int) $barangMudahPecah->shift_id
                    || (int) $item->id_area !== (int) $barangMudahPecah->id_area
                    || (string) $item->jam !== (string) $barangMudahPecah->jam
                    || Carbon::parse($item->tanggal)->format('Y-m-d') !== Carbon::parse($barangMudahPecah->tanggal)->format('Y-m-d')) {
                    return back()->withErrors(["items.$idx.uuid" => 'Item tidak termasuk pada form yang sedang diedit.'])->withInput();
                }

                $item->update([
                    'kondisi' => $row['kondisi'],
                    'temuan_ketidaksesuaian' => $request->temuan_ketidaksesuaian,
                ]);
            }

            return redirect()->route('barang-mudah-pecah.index')
                ->with('success', 'Data barang mudah pecah berhasil diperbarui.');
        }

        // Fallback: update lama (single row)
        $request->validate([
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
            'id_area' => 'required|exists:input_area,id',
            'id_sub_area' => 'nullable|exists:sub_area,id',
            'is_manual' => 'nullable|boolean',
            'id_nama_barang' => 'required_if:is_manual,null|exists:data_barang,id',
            'nama_barang_manual' => 'required_if:is_manual,1|string|max:255',
            'jumlah' => 'required|integer',
            'nama_karyawan' => 'nullable|string|max:255',
            'kondisi' => 'required|in:OK,Tidak OK',
            'temuan_ketidaksesuaian' => 'nullable|string|max:1000',
        ]);

        // Get the selected shift to validate plan access
        $selectedShift = DataShift::findOrFail($request->shift_id);
        
        // Additional validation for non-superadmin users
        if ($user->role !== 'superadmin') {
            if ($selectedShift->id_plan !== $user->id_plan) {
                return back()->withErrors(['shift_id' => 'Anda tidak memiliki akses ke shift ini.']);
            }
        }

        $effectivePlanId = ($user->role === 'superadmin') ? $selectedShift->id_plan : $user->id_plan;

        $selectedArea = InputArea::findOrFail($request->id_area);
        if ((int) $selectedArea->id_plan !== (int) $effectivePlanId) {
            $areaMessage = ($user->role === 'superadmin')
                ? 'Area tidak sesuai dengan plan shift yang dipilih.'
                : 'Area tidak sesuai dengan plan yang sedang login.';
            return back()->withErrors(['id_area' => $areaMessage])->withInput();
        }

        if ($request->filled('id_sub_area')) {
            $subArea = SubArea::findOrFail($request->id_sub_area);
            if ((int) $subArea->id_input_area !== (int) $request->id_area) {
                return back()->withErrors(['id_sub_area' => 'Sub Area tidak sesuai dengan Area yang dipilih.'])->withInput();
            }
        }

        $isManual = $request->boolean('is_manual');

        if (!$isManual) {
            $barang = DataBarang::findOrFail($request->id_nama_barang);
            if ((int) $barang->id_plan !== (int) $effectivePlanId || (int) $barang->id_area !== (int) $request->id_area) {
                return back()->withErrors(['id_nama_barang' => 'Nama barang tidak sesuai dengan Area yang dipilih.'])->withInput();
            }
        }

        $barangMudahPecah->update([
            'id_plan' => $effectivePlanId,
            'shift_id' => $request->shift_id,
            'tanggal' => $request->tanggal,
            'id_area' => $request->id_area,
            'id_sub_area' => $request->id_sub_area,
            'is_manual' => $isManual,
            'id_nama_barang' => !$isManual ? $request->id_nama_barang : null,
            'nama_barang_manual' => $isManual ? $request->nama_barang_manual : null,
            'jumlah' => $request->jumlah,
            'nama_karyawan' => $isManual ? $request->nama_karyawan : null,
            'kondisi' => $request->kondisi,
            'temuan_ketidaksesuaian' => $request->temuan_ketidaksesuaian,
        ]);

        return redirect()->route('barang-mudah-pecah.index')
            ->with('success', 'Data barang mudah pecah berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $barangMudahPecah = BarangMudahPecah::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check access for non-superadmin users
        if ($user->role !== 'superadmin' && $barangMudahPecah->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        $barangMudahPecah->delete();

        return redirect()->route('barang-mudah-pecah.index')
            ->with('success', 'Data barang mudah pecah berhasil dihapus.');
    }
    /**
    * Show logs for the specified resource.
    */
    public function showLogs($uuid)
    {
        $item = BarangMudahPecah::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();

        // Authorization check
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized action.');
        }

        $logs = BarangMudahPecahLog::where('barang_mudah_pecah_id', $item->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('qc-sistem.barang_mudah_pecah.logs', compact('item', 'logs'));
    }

    /**
    * Get logs JSON for the specified resource.
    */
    public function getLogsJson($uuid)
    {
        $item = BarangMudahPecah::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();

        // Authorization check
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $logs = BarangMudahPecahLog::where('barang_mudah_pecah_id', $item->id)
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
                'id_area' => 'nullable|integer|exists:input_area,id',
                'kode_form' => 'required|string|max:50'
            ]);

            $user = auth()->user();
            
            $query = BarangMudahPecah::with(['plan', 'user', 'shift', 'namaBarang'])
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

            $kode_form = $request->kode_form;
            $pdf = \PDF::loadView('qc-sistem.barang_mudah_pecah.export_pdf', compact('data', 'kode_form'));
            $pdf->setPaper('A4', 'landscape');
            
            $filename = 'barang_mudah_pecah_' . date('Y-m-d_H-i-s') . '.pdf';
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
    public function getSubAreasByArea($areaId)
    {
        $subAreas = SubArea::where('id_input_area', $areaId)->get();
        return response()->json($subAreas);
    }

    /**
     * Approve data with role-based validation
     */
    public function approve(Request $request, $uuid)
    {
        $request->validate([
            'type' => 'required|in:qc,produksi,spv'
        ]);

        $barangMudahPecah = BarangMudahPecah::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        $type = $request->type;

        // Role-based authorization
        $allowedRoles = [
            'qc' => [1, 3, 5], // Role IDs yang bisa approve QC
            'produksi' => [2], // Role IDs yang bisa approve Produksi  
            'spv' => [4] // Role IDs yang bisa approve SPV
        ];

        if (!in_array($user->id_role, $allowedRoles[$type])) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki wewenang untuk melakukan persetujuan ini.'
            ], 403);
        }

        // Sequential approval validation (QC → Produksi → SPV)
        if ($type === 'produksi' && !$barangMudahPecah->approved_by_qc) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh QC terlebih dahulu.'
            ], 400);
        }

        if ($type === 'spv' && !$barangMudahPecah->approved_by_produksi) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh Produksi terlebih dahulu.'
            ], 400);
        }

        // Check if already approved
        $approvalField = "approved_by_{$type}";
        if ($barangMudahPecah->$approvalField) {
            return response()->json([
                'success' => false,
                'message' => 'Data sudah disetujui sebelumnya.'
            ], 400);
        }

        // Update approval
        $barangMudahPecah->update([
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
