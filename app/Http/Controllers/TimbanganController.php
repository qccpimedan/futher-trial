<?php

namespace App\Http\Controllers;

use App\Models\Timbangan;
use App\Models\Plan;
use App\Models\DataShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\TimbanganLog;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TimbanganTemplateExport;
use App\Imports\TimbanganImport;

class TimbanganController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = Timbangan::with(['plan', 'shift', 'user', 'qcApprover', 'produksiApprover', 'spvApprover'])
            ->filterByRole();

        $search = request('search');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('jenis', 'LIKE', '%' . $search . '%')
                  ->orWhere('kode_timbangan', 'LIKE', '%' . $search . '%')
                  ->orWhere('hasil_pengecekan', 'LIKE', '%' . $search . '%');
            });
        }

        $data = $query->orderBy('tanggal', 'desc')
                      ->orderBy('jam', 'desc')
                      ->paginate(10);

        return view('qc-sistem.timbangan.index', compact('data'));
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
            $shifts = DataShift::all();
            $dataTimbangan = \App\Models\DataTimbangan::with('plan')->get();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $dataTimbangan = \App\Models\DataTimbangan::where('id_plan', $user->id_plan)->with('plan')->get();
        }
        
        $hasilPengecekanOptions = Timbangan::getHasilPengecekanOptions();
        $gramOptions = Timbangan::getGramOptions();
        
        return view('qc-sistem.timbangan.create', compact('plans', 'shifts', 'hasilPengecekanOptions', 'gramOptions', 'dataTimbangan'));
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

        // NEW FORMAT: common fields + entries[] from table
        if ($request->has('entries')) {
            $dateRule = $isSpecialRole ? 'required|date_format:d-m-Y' : 'required|date_format:d-m-Y H:i:s';

            $validator = Validator::make($request->all(), [
                'shift_id' => 'required|exists:data_shift,id',
                'tanggal' => $dateRule,
                'jam' => 'required',

                'entries' => 'required|array|min:1',
                'entries.*.jenis' => 'required|string|max:255',
                'entries.*.kode_timbangan' => 'required|string|max:255',
                'entries.*.hasil_pengecekan' => 'required|in:' . implode(',', array_keys(Timbangan::getHasilPengecekanOptions())),
                // NOTE: gram column exists in DB and is required by schema; if not provided we will default.
                'entries.*.gram' => 'nullable|in:' . implode(',', array_keys(Timbangan::getGramOptions())),
                'entries.*.hasil_verifikasi_500' => 'nullable|string|max:255',
                'entries.*.hasil_verifikasi_1000' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            if ($isSpecialRole) {
                $dateOnly = \Carbon\Carbon::createFromFormat('d-m-Y', $request->tanggal)->format('Y-m-d');
                $timeNow = now()->format('H:i:s');
                $tanggalData = $dateOnly . ' ' . $timeNow;
            } else {
                $tanggalData = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s');
            }

            $count = count($request->entries);
            foreach ($request->entries as $entry) {
                $data = [
                    'uuid' => Str::uuid(),
                    'shift_id' => $request->shift_id,
                    'tanggal' => $tanggalData,
                    'jam' => $request->jam,
                    'jenis' => $entry['jenis'] ?? '-',
                    'kode_timbangan' => $entry['kode_timbangan'] ?? '-',
                    'hasil_pengecekan' => $entry['hasil_pengecekan'] ?? 'ok',
                    'gram' => $entry['gram'] ?? '500',
                    'hasil_verifikasi_500' => $entry['hasil_verifikasi_500'] ?? null,
                    'hasil_verifikasi_1000' => $entry['hasil_verifikasi_1000'] ?? null,
                    'user_id' => $user->id,
                    'id_plan' => $user->id_plan,
                ];

                Timbangan::create($data);
            }

            return redirect()->route('timbangan.index')
                ->with('success', 'Data Timbangan berhasil ditambahkan (' . $count . ' data).');
        }

        // LEGACY FORMAT: array-based submission
        if ($isSpecialRole) {
            $validator = Validator::make($request->all(), [
                'shift_id' => 'required|array',
                'shift_id.*' => 'exists:data_shift,id',
                'tanggal' => 'required|array',
                'tanggal.*' => 'required|date_format:d-m-Y',
                'jam' => 'required|array',
                'jam.*' => 'required',
                'jenis' => 'required|array',
                'jenis.*' => 'string|max:255',
                'kode_timbangan' => 'required|array',
                'kode_timbangan.*' => 'string|max:255',
                'hasil_pengecekan' => 'required|array',
                'hasil_pengecekan.*' => 'in:' . implode(',', array_keys(Timbangan::getHasilPengecekanOptions())),
                'gram' => 'required|array',
                'gram.*' => 'in:' . implode(',', array_keys(Timbangan::getGramOptions())),
                'hasil_verifikasi_500' => 'nullable|array',
                'hasil_verifikasi_500.*' => 'nullable|string|max:255',
                'hasil_verifikasi_1000' => 'nullable|array',
                'hasil_verifikasi_1000.*' => 'nullable|string|max:255',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'shift_id' => 'required|array',
                'shift_id.*' => 'exists:data_shift,id',
                'tanggal' => 'required|array',
                'tanggal.*' => 'required|date_format:d-m-Y H:i:s',
                'jam' => 'required|array',
                'jam.*' => 'required',
                'jenis' => 'required|array',
                'jenis.*' => 'string|max:255',
                'kode_timbangan' => 'required|array',
                'kode_timbangan.*' => 'string|max:255',
                'hasil_pengecekan' => 'required|array',
                'hasil_pengecekan.*' => 'in:' . implode(',', array_keys(Timbangan::getHasilPengecekanOptions())),
                'gram' => 'required|array',
                'gram.*' => 'in:' . implode(',', array_keys(Timbangan::getGramOptions())),
                'hasil_verifikasi_500' => 'nullable|array',
                'hasil_verifikasi_500.*' => 'nullable|string|max:255',
                'hasil_verifikasi_1000' => 'nullable|array',
                'hasil_verifikasi_1000.*' => 'nullable|string|max:255',
            ]);
        }

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $count = count($request->tanggal);
        for ($i = 0; $i < $count; $i++) {
            if ($isSpecialRole) {
                $dateOnly = \Carbon\Carbon::createFromFormat('d-m-Y', $request->tanggal[$i])->format('Y-m-d');
                $timeNow = now()->format('H:i:s');
                $tanggalData = $dateOnly . ' ' . $timeNow;
            } else {
                $tanggalData = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal[$i])->format('Y-m-d H:i:s');
            }

            $data = [
                'uuid' => Str::uuid(),
                'shift_id' => $request->shift_id[0],
                'tanggal' => $tanggalData,
                'jam' => $request->jam[$i],
                'jenis' => $request->jenis[$i],
                'kode_timbangan' => $request->kode_timbangan[$i],
                'hasil_pengecekan' => $request->hasil_pengecekan[$i],
                'gram' => $request->gram[$i],
                'hasil_verifikasi_500' => $request->hasil_verifikasi_500[$i] ?? null,
                'hasil_verifikasi_1000' => $request->hasil_verifikasi_1000[$i] ?? null,
                'user_id' => $user->id,
                'id_plan' => $user->id_plan,
            ];

            Timbangan::create($data);
        }

        return redirect()->route('timbangan.index')
            ->with('success', 'Data Timbangan berhasil ditambahkan (' . $count . ' data).');
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function show($uuid)
    {
        $data = Timbangan::with(['plan', 'shift', 'user'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        // Cek akses data
        if (!$data->canAccess()) {
            abort(403, 'Unauthorized access');
        }

        return view('qc-sistem.timbangan.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function edit($uuid)
    {
        $data = Timbangan::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $shifts = DataShift::all();
            $dataTimbangan = \App\Models\DataTimbangan::with('plan')->get();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $dataTimbangan = \App\Models\DataTimbangan::where('id_plan', $user->id_plan)->with('plan')->get();
        }
        
        $hasilPengecekanOptions = Timbangan::getHasilPengecekanOptions();
        $gramOptions = Timbangan::getGramOptions();
        
        return view('qc-sistem.timbangan.edit', compact('data', 'plans', 'shifts', 'hasilPengecekanOptions', 'gramOptions', 'dataTimbangan'));
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
        $data = Timbangan::where('uuid', $uuid)->firstOrFail();

        // Cek akses data
        if (!$data->canAccess()) {
            abort(403, 'Unauthorized access');
        }

        $validator = Validator::make($request->all(), [
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
            'jam' => 'required',
            'jenis' => 'required|string|max:255',
            'kode_timbangan' => 'required|string|max:255',
            'hasil_pengecekan' => 'required|in:' . implode(',', array_keys(Timbangan::getHasilPengecekanOptions())),
            'gram' => 'required|in:' . implode(',', array_keys(Timbangan::getGramOptions())),
            'hasil_verifikasi_500' => 'nullable|string|max:255',
            'hasil_verifikasi_1000' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $updateData = $request->except(['uuid', 'user_id', 'id_plan', 'kode_form']);
        $data->update($updateData);

        return redirect()->route('timbangan.index')
            ->with('success', 'Data Timbangan berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        $data = Timbangan::where('uuid', $uuid)->firstOrFail();

        // Cek akses data
        if (!$data->canAccess()) {
            abort(403, 'Unauthorized access');
        }

        $data->delete();

        return redirect()->route('timbangan.index')
            ->with('success', 'Data Timbangan berhasil dihapus.');
    }
    /**
 * Show logs for the specified resource.
 */
    public function showLogs($uuid)
    {
        $item = Timbangan::where('uuid', $uuid)->firstOrFail();
        
        // Authorization check
        if (!$item->canAccess()) {
            abort(403, 'Unauthorized action.');
        }

        $logs = TimbanganLog::where('timbangan_id', $item->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('qc-sistem.timbangan.logs', compact('item', 'logs'));
    }

    /**
     * Get logs JSON for the specified resource.
     */
    public function getLogsJson($uuid)
    {
        $item = Timbangan::where('uuid', $uuid)->firstOrFail();
        
        // Authorization check
        if (!$item->canAccess()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $logs = TimbanganLog::where('timbangan_id', $item->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($logs);
    }

    /**
     * Bulk export PDF with filters
     */
    public function bulkExportPdf(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date',
            'shift_id' => 'required|exists:data_shift,id',
            'kode_form' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = Auth::user();
        
        // Build query with filters
        $query = Timbangan::with(['plan', 'shift', 'user', 'qcApprover', 'produksiApprover', 'spvApprover'])
            ->whereDate('tanggal', $request->tanggal)
            ->where('shift_id', $request->shift_id);

        // Apply role-based filtering
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $data = $query->orderBy('tanggal', 'asc')->get();

        if ($data->isEmpty()) {
            return redirect()->back()
                ->with('error', 'Tidak ada data yang ditemukan dengan filter yang dipilih.');
        }

        // Update kode_form for matching records
        $query->update(['kode_form' => $request->kode_form]);

        // Get shift and plan info for PDF
        $shift = DataShift::find($request->shift_id);
        $plan = $user->role === 'superadmin' ? $data->first()->plan : $user->plan;

        $filters = [
            'tanggal' => \Carbon\Carbon::parse($request->tanggal)->format('d/m/Y'),
            'shift' => $shift ? 'Shift ' . $shift->shift : '-',
            'kode_form' => $request->kode_form,
            'plan' => $plan ? $plan->nama_plan : '-'
        ];

        $pdf = \PDF::loadView('qc-sistem.timbangan.export_pdf', compact('data', 'filters'))
            ->setPaper('a4', 'portrait');

        $filename = 'timbangan-' . $request->tanggal . '-shift-' . $request->shift_id . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Approve data timbangan
     */
    public function approve(Request $request, $uuid)
    {
        $request->validate([
            'type' => 'required|in:qc,produksi,spv'
        ]);

        $timbangan = Timbangan::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        $type = $request->type;

        // Role-based access control following ProdukYum pattern
        $allowedRoles = [
            'qc' => [1, 3, 5], // Role 1&5 (QC only), Role 3 (QC only)
            'produksi' => [1, 2, 5], // Role 1&5 (produksi only), Role 2 (produksi only)
            'spv' => [1, 4, 5] // Role 1&5 (SPV only), Role 4 (SPV only)
        ];

        if (!in_array($user->id_role, $allowedRoles[$type])) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk melakukan approval ini.'
            ], 403);
        }

        // Sequential approval validation (QC → Produksi → SPV)
        if ($type === 'produksi' && !$timbangan->approved_by_qc) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh QC terlebih dahulu.'
            ], 400);
        }

        if ($type === 'spv' && !$timbangan->approved_by_produksi) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh Produksi terlebih dahulu.'
            ], 400);
        }

        // Check if already approved
        $approvalField = "approved_by_{$type}";
        if ($timbangan->$approvalField) {
            return response()->json([
                'success' => false,
                'message' => 'Data sudah disetujui sebelumnya.'
            ], 400);
        }

        // Update approval
        $timbangan->update([
            $approvalField => true,
            "{$type}_approved_by" => $user->id,
            "{$type}_approved_at" => now()
        ]);

        // Log approval activity
        TimbanganLog::create([
            'timbangan_id' => $timbangan->id,
            'timbangan_uuid' => $timbangan->uuid,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->id_role,
            'aksi' => 'Approval ' . ucfirst($type),
            'field_yang_diubah' => ['approval_' . $type],
            'nilai_lama' => [false],
            'nilai_baru' => [true],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'keterangan' => 'Data disetujui oleh ' . ucfirst($type)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disetujui.'
        ]);
    }

    public function downloadTemplate()
    {
        $user = auth()->user();
        $filename = 'template_timbangan.xlsx';

        return Excel::download(new TimbanganTemplateExport($user), $filename);
    }

    public function importExcel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls'
        ]);

        if ($validator->fails()) {
            return redirect()->route('timbangan.index')->with('error', 'File yang diupload harus berupa Excel.');
        }

        $user = auth()->user();

        if (!$user || !$user->id_plan) {
            return redirect()->route('timbangan.index')->with('error', 'Plan user tidak ditemukan. Tidak dapat melakukan import.');
        }

        $import = new TimbanganImport($user);
        Excel::import($import, $request->file('file'));

        $insertedCount = $import->getInsertedCount();
        $importErrors = $import->getImportErrors();

        if ($insertedCount <= 0) {
            return redirect()->route('timbangan.index')
                ->with('warning', 'Tidak ada data valid untuk di-import. Pastikan format kolom dan master Shift/Timbangan sesuai.')
                ->with('import_errors', array_slice($importErrors, 0, 20));
        }

        $successMessage = 'Import data Timbangan berhasil. Total: ' . $insertedCount . ' baris.';
        if (!empty($importErrors)) {
            return redirect()->route('timbangan.index')
                ->with('success', $successMessage)
                ->with('import_errors', array_slice($importErrors, 0, 20))
                ->with('info', 'Ada beberapa baris yang gagal di-import. Silakan cek detail di bawah.');
        }

        return redirect()->route('timbangan.index')->with('success', $successMessage);
    }
}
