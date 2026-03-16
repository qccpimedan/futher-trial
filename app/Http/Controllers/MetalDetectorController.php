<?php

namespace App\Http\Controllers;

use App\Models\InputMetalDetector;
use App\Models\InputMetalDetectorLog;
use App\Models\Plan;
use App\Models\DataShift;
use App\Models\JenisProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MetalDetectorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10);

        $query = InputMetalDetector::with(['plan', 'user', 'shift', 'produk', 'qcApprover', 'produksiApprover', 'spvApprover']);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_produksi', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%")
                    ->orWhereHas('produk', function ($q) use ($search) {
                        $q->where('nama_produk', 'like', "%{$search}%");
                    });
            });
        }

        $perPage = (int) $perPage;
        if ($perPage <= 0) {
            $perPage = 10;
        }

        $metalDetector = $query->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends($request->query());

        return view('qc-sistem.input_metal_detector.index', compact('metalDetector', 'search', 'perPage'));
    }
    public function create()
    {
        $user = Auth::user();
        if ($user->role === 'superadmin') {
            $produks = JenisProduk::all();
            $plans = Plan::all();
            $shifts = DataShift::all();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $plans = Plan::where('id', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        }
        return view('qc-sistem.input_metal_detector.create', compact('produks', 'plans','shifts'));
    }
    public function store(Request $request)
    {
         $user = Auth::user();
    $isSpecialRole = ($user->id_role == 2 || $user->id_role == 3);
    
    // Validasi berbeda berdasarkan role
    if ($isSpecialRole) {
        $request->validate([
            'id_shift.*' => 'required|exists:data_shift,id',
            'line.*' => 'required|in:1,2,3,4,5,6,7,8',
            'tanggal.*' => 'required|date_format:d-m-Y', // Hanya validasi format tanggal saja
            'jam.*' => 'required',
            'id_produk.*' => 'required|exists:jenis_produk,id',
            'kode_produksi.*' => 'required|string|max:255',
            'berat_produk.*' => 'required|string|max:255',
            'fe_depan_aktual.*' => 'required|in:✔,✘',
            'fe_tengah_aktual.*' => 'required|in:✔,✘',
            'fe_belakang_aktual.*' => 'required|in:✔,✘',
            'non_fe_depan_aktual.*' => 'required|in:✔,✘',
            'non_fe_tengah_aktual.*' => 'required|in:✔,✘',
            'non_fe_belakang_aktual.*' => 'required|in:✔,✘',
            'sus_depan_aktual.*' => 'required|in:✔,✘',
            'sus_tengah_aktual.*' => 'required|in:✔,✘',
            'sus_belakang_aktual.*' => 'required|in:✔,✘',
            'keterangan.*' => 'nullable|string|max:255',
        ]);
    } else {
        $request->validate([
            'id_shift.*' => 'required|exists:data_shift,id',
            'line.*' => 'required|in:1,2,3,4,5,6,7,8',
            'tanggal.*' => 'required|date_format:d-m-Y H:i:s',
            'jam.*' => 'required',
            'id_produk.*' => 'required|exists:jenis_produk,id',
            'kode_produksi.*' => 'required|string|max:255',
            'berat_produk.*' => 'required|string|max:255',
            'fe_depan_aktual.*' => 'required|in:✔,✘',
            'fe_tengah_aktual.*' => 'required|in:✔,✘',
            'fe_belakang_aktual.*' => 'required|in:✔,✘',
            'non_fe_depan_aktual.*' => 'required|in:✔,✘',
            'non_fe_tengah_aktual.*' => 'required|in:✔,✘',
            'non_fe_belakang_aktual.*' => 'required|in:✔,✘',
            'sus_depan_aktual.*' => 'required|in:✔,✘',
            'sus_tengah_aktual.*' => 'required|in:✔,✘',
            'sus_belakang_aktual.*' => 'required|in:✔,✘',
            'keterangan.*' => 'nullable|string|max:255',
        ]);
    }

    $shiftIds = Arr::wrap($request->input('id_shift'));
    $lines = Arr::wrap($request->input('line'));
    $jams = Arr::wrap($request->input('jam'));
    $tanggalArr = Arr::wrap($request->input('tanggal'));
    $produkIds = Arr::wrap($request->input('id_produk'));
    $kodeProduksiArr = Arr::wrap($request->input('kode_produksi'));
    $beratProdukArr = Arr::wrap($request->input('berat_produk'));
    $feDepanArr = Arr::wrap($request->input('fe_depan_aktual'));
    $feTengahArr = Arr::wrap($request->input('fe_tengah_aktual'));
    $feBelakangArr = Arr::wrap($request->input('fe_belakang_aktual'));
    $nonFeDepanArr = Arr::wrap($request->input('non_fe_depan_aktual'));
    $nonFeTengahArr = Arr::wrap($request->input('non_fe_tengah_aktual'));
    $nonFeBelakangArr = Arr::wrap($request->input('non_fe_belakang_aktual'));
    $susDepanArr = Arr::wrap($request->input('sus_depan_aktual'));
    $susTengahArr = Arr::wrap($request->input('sus_tengah_aktual'));
    $susBelakangArr = Arr::wrap($request->input('sus_belakang_aktual'));
    $keteranganArr = Arr::wrap($request->input('keterangan'));

    $count = count($shiftIds);
    $savedCount = 0;

    for ($i = 0; $i < $count; $i++) {
        // Transform the date format
        if ($isSpecialRole) {
            // Untuk user dengan role 2 atau 3, gunakan format tanggal dari request tapi waktu dari now()
            $dateOnly = \Carbon\Carbon::createFromFormat('d-m-Y', $tanggalArr[$i])->format('Y-m-d');
            $timeNow = now()->format('H:i:s');
            $tanggal = $dateOnly . ' ' . $timeNow;
        } else {
            // Untuk user lain, gunakan format tanggal dan waktu dari request
            $tanggal = Carbon::createFromFormat('d-m-Y H:i:s', $tanggalArr[$i])->format('Y-m-d H:i:s');
        }

        $data = [
            'id_plan' => $user->id_plan,
            'user_id' => $user->id,
            'uuid' => Str::uuid(),
            'id_shift' => $shiftIds[$i] ?? null,
            'line' => $lines[$i] ?? null,
            'tanggal' => $tanggal,
            'jam' => $jams[$i] ?? null,
            'id_produk' => $produkIds[$i] ?? null,
            'kode_produksi' => $kodeProduksiArr[$i] ?? null,
            'berat_produk' => $beratProdukArr[$i] ?? null,
            'fe_depan_aktual' => $feDepanArr[$i] ?? null,
            'fe_tengah_aktual' => $feTengahArr[$i] ?? null,
            'fe_belakang_aktual' => $feBelakangArr[$i] ?? null,
            'non_fe_depan_aktual' => $nonFeDepanArr[$i] ?? null,
            'non_fe_tengah_aktual' => $nonFeTengahArr[$i] ?? null,
            'non_fe_belakang_aktual' => $nonFeBelakangArr[$i] ?? null,
            'sus_depan_aktual' => $susDepanArr[$i] ?? null,
            'sus_tengah_aktual' => $susTengahArr[$i] ?? null,
            'sus_belakang_aktual' => $susBelakangArr[$i] ?? null,
            'keterangan' => $keteranganArr[$i] ?? null,
        ];

        InputMetalDetector::create($data);
        $savedCount++;
    }


        return redirect()->route('input-metal-detector.index')->with('success', "$savedCount Data Metal Detector berhasil disimpan.");
    }

    public function edit($uuid)
    {
        $metalDetector = InputMetalDetector::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $metalDetector->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        if ($user->role === 'superadmin') {
            $produks = JenisProduk::all();
            $plans = Plan::all();
            $shifts = DataShift::all();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $plans = Plan::where('id', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        }
   
        return view('qc-sistem.input_metal_detector.edit', compact('metalDetector', 'produks', 'plans', 'shifts'));
    }

    public function update(Request $request, $uuid)
    {
        $metalDetector = InputMetalDetector::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $metalDetector->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk memperbarui data ini.');
        }

        $validatedData = $request->validate([
            'id_shift' => 'required|exists:data_shift,id',
            'line' => 'required|in:1,2,3,4,5,6,7,8',
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'id_produk' => 'required|exists:jenis_produk,id',
            'kode_produksi' => 'required|string|max:255',
            'berat_produk' => 'required|string|max:255',
            'fe_depan_aktual' => 'required|in:✔,✘',
            'fe_tengah_aktual' => 'required|in:✔,✘',
            'fe_belakang_aktual' => 'required|in:✔,✘',
            'non_fe_depan_aktual' => 'required|in:✔,✘',
            'non_fe_tengah_aktual' => 'required|in:✔,✘',
            'non_fe_belakang_aktual' => 'required|in:✔,✘',
            'sus_depan_aktual' => 'required|in:✔,✘',
            'sus_tengah_aktual' => 'required|in:✔,✘',
            'sus_belakang_aktual' => 'required|in:✔,✘',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $validatedData['id_plan'] = $user->id_plan;
        $validatedData['tanggal'] = Carbon::createFromFormat('d-m-Y H:i:s', $validatedData['tanggal'])->format('Y-m-d H:i:s');


        $metalDetector->update($validatedData);

        return redirect()->route('input-metal-detector.index')->with('success', 'Data Metal Detector berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $item = InputMetalDetector::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        $item->delete();
        return redirect()->route('input-metal-detector.index')->with('success', 'Data berhasil dihapus');
    }

    /**
     * Show logs for specific metal detector record
     */
    public function showLogs($uuid)
    {
        $item = InputMetalDetector::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk melihat log data ini.');
        }

        $logs = InputMetalDetectorLog::where('input_metal_detector_uuid', $uuid)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('qc-sistem.input_metal_detector.logs', compact('item', 'logs'));
    }

    /**
     * Get logs data in JSON format for DataTables
     */
    public function getLogsJson($uuid)
    {
        $item = InputMetalDetector::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $logs = InputMetalDetectorLog::where('input_metal_detector_uuid', $uuid)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $logs->map(function ($log) {
                return [
                    'tanggal' => $log->created_at->format('d/m/Y H:i:s'),
                    'user_name' => $log->user_name,
                    'user_role' => $log->user_role,
                    'aksi' => ucfirst($log->aksi),
                    'field_yang_diubah' => $log->field_yang_diubah ? implode(', ', array_map(function($field) use ($log) {
                        return $log->getNamaFieldSingle($field);
                    }, $log->field_yang_diubah)) : '-',
                    'deskripsi_perubahan' => $log->deskripsi_perubahan ?: '-'
                ];
            })
        ]);
    }

    public function bulkExportPdf(Request $request)
    {
        $request->validate([
            'tanggal' => 'nullable|date',
            'shift_id' => 'nullable|exists:data_shift,id',
            'id_produk' => 'nullable|exists:jenis_produk,id',
            'kode_form' => 'required|string|max:255'
        ]);

        $user = Auth::user();
        $query = InputMetalDetector::with(['plan', 'user', 'shift', 'produk', 'qcApprover', 'produksiApprover', 'spvApprover']);

        // Filter berdasarkan role user
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        // Apply filters
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('shift_id')) {
            $query->where('id_shift', $request->shift_id);
        }

        if ($request->filled('id_produk')) {
            $query->where('id_produk', $request->id_produk);
        }

        $data = $query->orderBy('tanggal', 'asc')->get();

        // Update kode_form untuk semua data yang difilter (hanya jika ada data)
        if ($data->count() > 0) {
            $dataIds = $data->pluck('id')->toArray();
            InputMetalDetector::whereIn('id', $dataIds)->update(['kode_form' => $request->kode_form]);
            
            // Refresh data untuk mendapatkan kode_form yang baru disimpan
            $data = $query->orderBy('tanggal', 'asc')->get();
        }

        // Jika tidak ada data, tampilkan halaman error HTML (tidak download PDF)
        if ($data->isEmpty()) {
            $errorMessage = 'Tidak ada data Metal Detector yang sesuai dengan filter yang dipilih.';
            $filterInfo = [];
            
            if ($request->tanggal) {
                $filterInfo[] = 'Tanggal: ' . Carbon::parse($request->tanggal)->format('d-m-Y');
            }
            if ($request->shift_id) {
                $shift = DataShift::find($request->shift_id);
                $filterInfo[] = 'Shift: ' . ($shift ? $shift->shift : 'Tidak Diketahui');
            }
            if ($request->id_produk) {
                $produk = JenisProduk::find($request->id_produk);
                $filterInfo[] = 'Produk: ' . ($produk ? $produk->nama_produk : 'Tidak Diketahui');
            }
            $filterInfo[] = 'Kode Form: ' . $request->kode_form;
            
            $html = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>Data Tidak Ditemukan - Metal Detector</title>
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
                    <div class="icon">🔍</div>
                    <h1>Data Metal Detector Tidak Ditemukan</h1>
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
                        Silakan coba dengan filter yang berbeda atau pastikan data Metal Detector sudah tersedia di sistem.
                    </p>
                    <a href="javascript:window.close()" class="btn">Tutup</a>
                </div>
            </body>
            </html>';
            
            return response($html)->header('Content-Type', 'text/html');
        }

        // Prepare filter info for PDF
        $filterInfo = [
            'tanggal' => $request->tanggal ? Carbon::parse($request->tanggal)->format('d-m-Y') : 'Semua Tanggal',
            'shift' => $request->shift_id ? DataShift::find($request->shift_id)->shift ?? 'Tidak Diketahui' : 'Semua Shift',
            'produk' => $request->id_produk ? JenisProduk::find($request->id_produk)->nama_produk ?? 'Tidak Diketahui' : 'Semua Produk',
            'kode_form' => $request->kode_form
        ];

        $pdf = \PDF::loadView('qc-sistem.input_metal_detector.export_pdf', compact('data', 'filterInfo'));
        $pdf->setPaper('A4', 'landscape');
        
        $filename = 'Metal_Detector_' . ($request->tanggal ? Carbon::parse($request->tanggal)->format('Y-m-d') : 'All') . '_' . time() . '.pdf';
        
        return $pdf->download($filename);
    }

    public function approve(Request $request, $uuid)
    {
        $request->validate([
            'type' => 'required|in:qc,produksi,spv'
        ]);

        $metalDetector = InputMetalDetector::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        $type = $request->type;

        // Role-based access control
        $allowedRoles = [
            'qc' => [1, 3, 5], // QC roles
            'produksi' => [2], // Produksi role
            'spv' => [4] // SPV role
        ];

        if (!in_array($user->id_role, $allowedRoles[$type])) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk melakukan persetujuan ini.'
            ], 403);
        }

        // Sequential approval validation
        if ($type === 'produksi' && !$metalDetector->approved_by_qc) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh QC terlebih dahulu.'
            ], 400);
        }

        if ($type === 'spv' && !$metalDetector->approved_by_produksi) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh Produksi terlebih dahulu.'
            ], 400);
        }

        // Check if already approved
        $approvalField = "approved_by_{$type}";
        if ($metalDetector->$approvalField) {
            return response()->json([
                'success' => false,
                'message' => 'Data sudah disetujui sebelumnya.'
            ], 400);
        }

        // Update approval
        $metalDetector->update([
            $approvalField => true,
            "{$type}_approved_by" => $user->id,
            "{$type}_approved_at" => now()
        ]);

        // Log the approval activity
        InputMetalDetectorLog::create([
            'uuid' => Str::uuid(),
            'input_metal_detector_id' => $metalDetector->id,
            'input_metal_detector_uuid' => $metalDetector->uuid,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->role,
            'aksi' => 'approve',
            'field_yang_diubah' => [$approvalField],
            'deskripsi_perubahan' => "Data disetujui oleh {$type}: {$user->name}"
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disetujui.'
        ]);
    }
}
