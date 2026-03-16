<?php

namespace App\Http\Controllers;

use App\Models\Thermometer;
use App\Models\Plan;
use App\Models\DataShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\ThermometerLog;
use App\Models\DataThermo;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ThermometerTemplateExport;
use App\Imports\ThermometerImport;
class ThermometerController extends Controller
{
    /**
     * Menampilkan daftar data thermometer
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = Thermometer::with(['plan', 'shift', 'user', 'qcApprover', 'produksiApprover', 'spvApprover'])
        ->filterByRole();

        $search = request('search');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('jenis', 'LIKE', '%' . $search . '%')
                  ->orWhere('kode_thermometer', 'LIKE', '%' . $search . '%')
                  ->orWhere('hasil_pengecekan', 'LIKE', '%' . $search . '%');
            });
        }

        $data = $query->orderBy('tanggal', 'desc')
                      ->orderBy('jam', 'desc')
                      ->paginate(10);

        return view('qc-sistem.thermometer.index', compact('data'));
    }

    /**
     * Menampilkan form untuk membuat data thermometer baru
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        
        // Filter data berdasarkan role - id_plan dan user_id akan diisi otomatis dari controller
        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $shifts = DataShift::all();
            $dataThermo = DataThermo::all(); // ← TAMBAH INI
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $dataThermo = DataThermo::where('id_plan', $user->id_plan)->get(); // ← TAMBAH INI
        }
        
        $hasilPengecekanOptions = Thermometer::getHasilPengecekanOptions();
        
        return view('qc-sistem.thermometer.create', compact('plans', 'shifts', 'dataThermo', 'hasilPengecekanOptions')); // ← TAMBAH dataThermo
    }

    /**
     * Menyimpan data thermometer baru ke database
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
           $user = Auth::user();
    $isSpecialRole = ($user->id_role == 2 || $user->id_role == 3);
      if($isSpecialRole){
          $validator = Validator::make($request->all(), [
              'shift_id' => 'required|exists:data_shift,id',
               'tanggal' => 'required|date_format:d-m-Y', // Hanya validasi format tanggal saja
               'jam' => 'required',
              'entries' => 'required|array|min:1',
              'entries.*.jenis' => 'required|string|max:255',
              'entries.*.kode_thermometer' => 'required|string|max:255',
              'entries.*.hasil_pengecekan' => 'required|in:' . implode(',', array_keys(Thermometer::getHasilPengecekanOptions())),
              'entries.*.hasil_verifikasi_0' => 'nullable|string|max:255',
              'entries.*.hasil_verifikasi_100' => 'nullable|string|max:255',
          ]);

      } else{
         $validator = Validator::make($request->all(), [
              'shift_id' => 'required|exists:data_shift,id',
                'tanggal' => 'required|date_format:d-m-Y H:i:s',
              'jam' => 'required',
              'entries' => 'required|array|min:1',
              'entries.*.jenis' => 'required|string|max:255',
              'entries.*.kode_thermometer' => 'required|string|max:255',
              'entries.*.hasil_pengecekan' => 'required|in:' . implode(',', array_keys(Thermometer::getHasilPengecekanOptions())),
              'entries.*.hasil_verifikasi_0' => 'nullable|string|max:255',
              'entries.*.hasil_verifikasi_100' => 'nullable|string|max:255',
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
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = Auth::user();
        $successCount = 0;
        $errors = [];
        
        // Process each entry
        foreach ($request->entries as $index => $entry) {
            try {
                $data = [
                    'uuid' => Str::uuid(),
                    'user_id' => $user->id,
                    'id_plan' => $user->id_plan,
                    'shift_id' => $request->shift_id,
                    'tanggal' => $tanggalData,
                    'jam'=> $request->jam,
                    'hasil_verifikasi_0' => $entry['hasil_verifikasi_0'] ?? null,
                    'hasil_verifikasi_100' => $entry['hasil_verifikasi_100'] ?? null,
                    'jenis' => $entry['jenis'],
                    'kode_thermometer' => $entry['kode_thermometer'],
                    'hasil_pengecekan' => $entry['hasil_pengecekan'],
                ];

                Thermometer::create($data);
                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "Entry " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        if ($successCount > 0) {
            $message = "Berhasil menyimpan {$successCount} data thermometer.";
            if (!empty($errors)) {
                $message .= " Namun ada beberapa error: " . implode(', ', $errors);
            }
            return redirect()->route('thermometer.index')->with('success', $message);
        } else {
            return redirect()->back()
                ->withErrors(['entries' => 'Gagal menyimpan semua data: ' . implode(', ', $errors)])
                ->withInput();
        }
    }

    /**
     * Menampilkan detail data thermometer
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function show($uuid)
    {
        $data = Thermometer::with(['plan', 'shift', 'user'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        // Cek akses data berdasarkan plan user
        if (!$data->canAccess()) {
            abort(403, 'Anda tidak memiliki akses ke data ini');
        }

        return view('qc-sistem.thermometer.show', compact('data'));
    }

    /**
     * Menampilkan form untuk edit data thermometer
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function edit($uuid)
    {
        $data = Thermometer::where('uuid', $uuid)->firstOrFail();
    
        // Cek akses data berdasarkan plan user
        if (!$data->canAccess()) {
            abort(403, 'Anda tidak memiliki akses ke data ini');
        }
    
        $user = Auth::user();
        
        // Filter data berdasarkan role
        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $shifts = DataShift::all();
            $dataThermo = DataThermo::all(); // ← TAMBAH INI
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $dataThermo = DataThermo::where('id_plan', $user->id_plan)->get(); // ← TAMBAH INI
        }
        
        $hasilPengecekanOptions = Thermometer::getHasilPengecekanOptions();
    
        return view('qc-sistem.thermometer.edit', compact('data', 'plans', 'shifts', 'dataThermo', 'hasilPengecekanOptions')); // ← TAMBAH dataThermo
    }

    /**
     * Update data thermometer di database
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $uuid)
    {
        $data = Thermometer::where('uuid', $uuid)->firstOrFail();

        // Cek akses data berdasarkan plan user
        if (!$data->canAccess()) {
            abort(403, 'Anda tidak memiliki akses ke data ini');
        }

        $validator = Validator::make($request->all(), [
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date_format:Y-m-d',
            'jam' => 'required|date_format:H:i',
            'jenis' => 'required|string|max:255',
            'kode_thermometer' => 'required|string|max:255',
            'hasil_verifikasi_0' => 'nullable|string|max:255',
            'hasil_verifikasi_100' => 'nullable|string|max:255',
            'hasil_pengecekan' => 'required|in:' . implode(',', array_keys(Thermometer::getHasilPengecekanOptions())),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $tanggalData = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $request->tanggal . ' ' . $request->jam)
            ->format('Y-m-d H:i:s');

        // Update data tanpa mengubah uuid, user_id, id_plan, dan kode_form
        $updateData = $request->except(['uuid', 'user_id', 'id_plan', 'kode_form']);
        $updateData['tanggal'] = $tanggalData;
        $data->update($updateData);

        return redirect()->route('thermometer.index')
            ->with('success', 'Data Thermometer berhasil diupdate.');
    }

    /**
     * Hapus data thermometer dari database
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        $data = Thermometer::where('uuid', $uuid)->firstOrFail();

        // Cek akses data berdasarkan plan user
        if (!$data->canAccess()) {
            abort(403, 'Anda tidak memiliki akses ke data ini');
        }

        $data->delete();

        return redirect()->route('thermometer.index')
            ->with('success', 'Data Thermometer berhasil dihapus.');
    }
    /**
    * Show logs for the specified resource.
    */
    public function showLogs($uuid)
    {
    $item = Thermometer::where('uuid', $uuid)->firstOrFail();

    // Authorization check
    if (!$item->canAccess()) {
        abort(403, 'Unauthorized action.');
    }

    $logs = ThermometerLog::where('thermometer_id', $item->id)
        ->with('user')
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    return view('qc-sistem.thermometer.logs', compact('item', 'logs'));
    }

    /**
    * Get logs JSON for the specified resource.
    */
    public function getLogsJson($uuid)
    {
    $item = Thermometer::where('uuid', $uuid)->firstOrFail();

    // Authorization check
    if (!$item->canAccess()) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $logs = ThermometerLog::where('thermometer_id', $item->id)
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
        $query = Thermometer::with(['plan', 'shift', 'user', 'qcApprover', 'produksiApprover', 'spvApprover'])
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

        $pdf = \PDF::loadView('qc-sistem.thermometer.export_pdf', compact('data', 'filters'))
            ->setPaper('a4', 'portrait');

        $filename = 'thermometer-' . $request->tanggal . '-shift-' . $request->shift_id . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Approve data thermometer
     */
    public function approve(Request $request, $uuid)
    {
        $request->validate([
            'type' => 'required|in:qc,produksi,spv'
        ]);

        $thermometer = Thermometer::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        $type = $request->type;

        // Role-based access control following Timbangan pattern
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
        if ($type === 'produksi' && !$thermometer->approved_by_qc) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh QC terlebih dahulu.'
            ], 400);
        }

        if ($type === 'spv' && !$thermometer->approved_by_produksi) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh Produksi terlebih dahulu.'
            ], 400);
        }

        // Check if already approved
        $approvalField = "approved_by_{$type}";
        if ($thermometer->$approvalField) {
            return response()->json([
                'success' => false,
                'message' => 'Data sudah disetujui sebelumnya.'
            ], 400);
        }

        // Update approval
        $thermometer->update([
            $approvalField => true,
            "{$type}_approved_by" => $user->id,
            "{$type}_approved_at" => now()
        ]);

        // Log approval activity
        ThermometerLog::create([
            'thermometer_id' => $thermometer->id,
            'thermometer_uuid' => $thermometer->uuid,
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
        $filename = 'template_thermometer.xlsx';

        return Excel::download(new ThermometerTemplateExport($user), $filename);
    }

    public function importExcel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls'
        ]);

        if ($validator->fails()) {
            return redirect()->route('thermometer.index')->with('error', 'File yang diupload harus berupa Excel.');
        }

        $user = auth()->user();

        if (!$user || !$user->id_plan) {
            return redirect()->route('thermometer.index')->with('error', 'Plan user tidak ditemukan. Tidak dapat melakukan import.');
        }

        $import = new ThermometerImport($user);
        Excel::import($import, $request->file('file'));

        $insertedCount = $import->getInsertedCount();
        $importErrors = $import->getImportErrors();

        if ($insertedCount <= 0) {
            return redirect()->route('thermometer.index')
                ->with('warning', 'Tidak ada data valid untuk di-import. Pastikan format kolom dan master Shift/Thermometer sesuai.')
                ->with('import_errors', array_slice($importErrors, 0, 20));
        }

        $successMessage = 'Import data Thermometer berhasil. Total: ' . $insertedCount . ' baris.';
        if (!empty($importErrors)) {
            return redirect()->route('thermometer.index')
                ->with('success', $successMessage)
                ->with('import_errors', array_slice($importErrors, 0, 20))
                ->with('info', 'Ada beberapa baris yang gagal di-import. Silakan cek detail di bawah.');
        }

        return redirect()->route('thermometer.index')->with('success', $successMessage);
    }
}
