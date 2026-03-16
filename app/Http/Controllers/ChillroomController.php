<?php

namespace App\Http\Controllers;

use App\Models\PenerimaanChillroom;
use App\Models\ChillroomLog;
use Illuminate\Http\Request;
use App\Models\DataShift;
use App\Models\Plan;
use App\Models\DataRm;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ChillroomController extends Controller
{
    // Tampilkan semua data
    public function index(Request $request)
    {
        $user = auth()->user();
        $search = $request->get('search');
     
        $query = PenerimaanChillroom::with(['plan', 'user', 'datashift']);
           
        if($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_rm', 'like', "%{$search}%")
                  ->orWhere('kode_produksi', 'like', "%{$search}%");
            });
        }

        $chillroom = $query->orderBy('created_at', 'desc')->paginate(10);
        $shifts = DataShift::all();
       
        return view('qc-sistem.chillroom.index', compact('chillroom', 'shifts', 'search'));
    }
    public function create()
    {
          $user = auth()->user();
           if ($user->role === 'superadmin') {
           
              $plans = Plan::all();
        $shifts = DataShift::all();
        $dataRm = DataRm::all();
        } else {
          
          
            $plans = Plan::where('id', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $dataRm = DataRm::where('id_plan', $user->id_plan)->get();
           
        }

      
        return view('qc-sistem.chillroom.create', compact('shifts', 'plans', 'dataRm'));
    }
    

   // Simpan data baru
    public function store(Request $request)
    {
        // Debug: Log all request data
        \Log::info('Request data received:', $request->all());
        
        $data = $request->validate([
            // Common fields (shared across all entries)
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'shift_id' => 'required|integer',
            'jam_kedatangan' => 'required|date_format:H:i',
            
            // Entry arrays (repeater fields)
            'entries' => 'required|array|min:1',
            'entries.*.nama_rm' => 'required|string',
            'entries.*.kode_produksi' => 'required|string',
            'entries.*.berat' => 'required|string',
            'entries.*.suhu' => 'required|numeric',
            'entries.*.sensori' => 'required|string',
            'entries.*.kemasan' => 'required|string',
            'entries.*.keterangan' => 'nullable|string',
            'entries.*.standar_berat' => 'nullable|string',
            'entries.*.jumlah_rm_value' => 'nullable|array',
            'entries.*.jumlah_rm_value.*' => 'nullable|numeric',
            'entries.*.berat_atas' => 'nullable|numeric',
            'entries.*.berat_std' => 'nullable|numeric',
            'entries.*.berat_bawah' => 'nullable|numeric',
            'entries.*.status_rm' => 'nullable|string',
            'entries.*.catatan_rm' => 'nullable|string',
        ]);

        $user = auth()->user();
        $successCount = 0;
        $errors = [];

        try {
                foreach ($data['entries'] as $index => $entry) {
                    try {
                        // Skip entries yang kosong atau tidak memiliki data penting
                        if (empty($entry['nama_rm']) && empty($entry['kode_produksi']) && empty($entry['berat'])) {
                            continue; // Skip entry kosong
                        }
                        
                        // PERBAIKAN: Siapkan data jumlah_rm dari berat_atas, berat_std, berat_bawah
                        $jumlahRmData = [
                            'entry_' . ($index + 1) => [
                                'berat_atas' => !empty($entry['berat_atas']) ? (int)$entry['berat_atas'] : 0,
                                'berat_std' => !empty($entry['berat_std']) ? (int)$entry['berat_std'] : 0,
                                'berat_bawah' => !empty($entry['berat_bawah']) ? (int)$entry['berat_bawah'] : 0,
                            ]
                        ];
                        
                        // PERBAIKAN: Siapkan data sampel berat terpisah untuk nilai_jumlah_rm
                        $nilaiSampelData = [];
                        if (!empty($entry['jumlah_rm_value']) && is_array($entry['jumlah_rm_value'])) {
                            // Filter out empty values and convert to float
                            $filteredValues = array_filter($entry['jumlah_rm_value'], function($value) {
                                return !empty($value) && is_numeric($value);
                            });
                            
                            if (!empty($filteredValues)) {
                                // Store as array of sample values
                                $nilaiSampelData['entry_' . ($index + 1)] = array_values(array_map('floatval', $filteredValues));
                            }
                        }
                        
                        // If no valid samples, create empty array structure
                        if (empty($nilaiSampelData)) {
                            $nilaiSampelData['entry_' . ($index + 1)] = [];
                        }
                        
                        \Log::info("Entry {$index} - jumlah_rm_data (agregasi):", $jumlahRmData);
                        \Log::info("Entry {$index} - nilai_jumlah_rm (sampel berat):", $nilaiSampelData);
                        
                        PenerimaanChillroom::create([
                            'uuid' => Str::uuid(),
                            'id_plan' => $user->id_plan,
                            'user_id' => $user->id,
                            'tanggal' => Carbon::createFromFormat('d-m-Y H:i:s', $data['tanggal'])->format('Y-m-d H:i:s'),
                            'shift_id' => $data['shift_id'],
                            'jam_kedatangan' => $data['jam_kedatangan'],
                            'nama_rm' => $entry['nama_rm'] ?? '-',
                            'kode_produksi' => $entry['kode_produksi'] ?? '-',
                            'berat' => $entry['berat'] ?? '-',
                            'suhu' => $entry['suhu'] ?? '0',
                            'sensori' => $entry['sensori'] ?? '-',
                            'kemasan' => $entry['kemasan'] ?? '-',
                            'keterangan' => $entry['keterangan'] ?? '-',
                            'standar_berat' => $entry['standar_berat'] ?? '-',
                            'berat_atas' => $entry['berat_atas'] ?? null,
                            'berat_std' => $entry['berat_std'] ?? null,
                            'berat_bawah' => $entry['berat_bawah'] ?? null,
                            // PERBAIKAN: jumlah_rm berisi agregasi berat (atas, std, bawah)
                            'jumlah_rm' => json_encode($jumlahRmData),
                            // PERBAIKAN: nilai_jumlah_rm berisi data sampel berat
                            'nilai_jumlah_rm' => json_encode($nilaiSampelData),
                            'status_rm' => $entry['status_rm'] ?? '-',
                            'catatan_rm' => $entry['catatan_rm'] ?? '-',
                        ]);
                        $successCount++;
                    } catch (\Exception $e) {
                        \Log::error("Error saving entry {$index}: " . $e->getMessage());
                        $errors[] = "Entry " . ($index + 1) . ": " . $e->getMessage();
                    }
                }

                if ($successCount > 0) {
                    $message = "Berhasil menyimpan {$successCount} data chillroom";
                    if (!empty($errors)) {
                        $message .= ". Beberapa data gagal disimpan: " . implode(', ', $errors);
                    }
                    return redirect()->route('chillroom.index')->with('success', $message);
                } else {
                    return redirect()->back()->withErrors($errors)->withInput();
                }

            } catch (\Exception $e) {
                \Log::error("Store error: " . $e->getMessage());
                return redirect()->back()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()])->withInput();
            }
    }

    // Tampilkan detail data
    public function edit($uuid)
    {
        $chillroom = PenerimaanChillroom::where('uuid', $uuid)->firstOrFail();
       
        
                  $user = auth()->user();
           if ($user->role === 'superadmin') {
           
              $plans = Plan::all();
        $shifts = DataShift::all();
        $dataRm = DataRm::all();
        } else {
          
          
            $plans = Plan::where('id', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $dataRm = DataRm::where('id_plan', $user->id_plan)->get();
           
        }
        return view('qc-sistem.chillroom.edit', compact('chillroom', 'shifts', 'plans', 'dataRm'));
    }
   // Update data
public function update(Request $request, $uuid)
{
    try {
        // Find the record
        $data = PenerimaanChillroom::where('uuid', $uuid)->firstOrFail();
        
        // Validate request
        $validated = $request->validate([
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'shift_id' => 'required|integer',
            'jam_kedatangan' => 'required|date_format:H:i',
            'nama_rm' => 'required|string',
            'kode_produksi' => 'required|string',
            'berat' => 'required|string',
            'suhu' => 'required|numeric',
            'sensori' => 'required|string',
            'kemasan' => 'required|string',
            'keterangan' => 'nullable|string',
            'standar_berat' => 'nullable|string',
            'berat_samples' => 'nullable|array',
            'berat_samples.*' => 'nullable|numeric',
            'berat_atas' => 'nullable|numeric',
            'berat_std' => 'nullable|numeric',
            'berat_bawah' => 'nullable|numeric',
            'status_rm' => 'nullable|string',
            'catatan_rm' => 'nullable|string',
        ]);

        // PERBAIKAN: Siapkan data jumlah_rm dari berat_atas, berat_std, berat_bawah
        $jumlahRmData = [
            'entry_1' => [
                'berat_atas' => !empty($validated['berat_atas']) ? (int)$validated['berat_atas'] : 0,
                'berat_std' => !empty($validated['berat_std']) ? (int)$validated['berat_std'] : 0,
                'berat_bawah' => !empty($validated['berat_bawah']) ? (int)$validated['berat_bawah'] : 0,
            ]
        ];
        
        // PERBAIKAN: Siapkan data sampel berat terpisah untuk nilai_jumlah_rm
        $nilaiSampelData = [];
        if (!empty($validated['berat_samples']) && is_array($validated['berat_samples'])) {
            // Filter out empty values and convert to float
            $filteredValues = array_filter($validated['berat_samples'], function($value) {
                return !empty($value) && is_numeric($value);
            });
            
            if (!empty($filteredValues)) {
                // Store as array of sample values
                $nilaiSampelData['entry_1'] = array_values(array_map('floatval', $filteredValues));
            }
        }
        
        // If no valid samples, create empty array structure
        if (empty($nilaiSampelData)) {
            $nilaiSampelData['entry_1'] = [];
        }
        
        // Log untuk debugging
        \Log::info('Update - jumlah_rm_data (agregasi):', $jumlahRmData);
        \Log::info('Update - nilai_jumlah_rm (sampel berat):', $nilaiSampelData);

        // Prepare old data for logging
        $oldData = [
            'tanggal' => $data->tanggal,
            'shift_id' => $data->shift_id,
            'jam_kedatangan' => $data->jam_kedatangan,
            'nama_rm' => $data->nama_rm,
            'kode_produksi' => $data->kode_produksi,
            'berat' => $data->berat,
            'suhu' => $data->suhu,
            'sensori' => $data->sensori,
            'kemasan' => $data->kemasan,
            'keterangan' => $data->keterangan,
            'standar_berat' => $data->standar_berat,
            'berat_atas' => $data->berat_atas,
            'berat_std' => $data->berat_std,
            'berat_bawah' => $data->berat_bawah,
            'jumlah_rm' => $data->jumlah_rm,
            'nilai_jumlah_rm' => $data->nilai_jumlah_rm,
            'status_rm' => $data->status_rm,
            'catatan_rm' => $data->catatan_rm,
        ];

        // Update the record
        $data->update([
            'tanggal' => Carbon::createFromFormat('d-m-Y H:i:s', $validated['tanggal'])->format('Y-m-d H:i:s'),
            'shift_id' => $validated['shift_id'],
            'jam_kedatangan' => $validated['jam_kedatangan'],
            'nama_rm' => $validated['nama_rm'],
            'kode_produksi' => $validated['kode_produksi'],
            'berat' => $validated['berat'],
            'suhu' => $validated['suhu'],
            'sensori' => $validated['sensori'],
            'kemasan' => $validated['kemasan'],
            'keterangan' => $validated['keterangan'] ?? '-',
            'standar_berat' => $validated['standar_berat'] ?? null,
            'berat_atas' => $validated['berat_atas'] ?? null,
            'berat_std' => $validated['berat_std'] ?? null,
            'berat_bawah' => $validated['berat_bawah'] ?? null,
            // PERBAIKAN: jumlah_rm berisi agregasi berat (atas, std, bawah)
            'jumlah_rm' => json_encode($jumlahRmData),
            // PERBAIKAN: nilai_jumlah_rm berisi data sampel berat
            'nilai_jumlah_rm' => json_encode($nilaiSampelData),
            'status_rm' => $validated['status_rm'] ?? '-',
            'catatan_rm' => $validated['catatan_rm'] ?? '-',
        ]);

        // Prepare new data for logging
        $newData = [
            'tanggal' => $data->tanggal,
            'shift_id' => $data->shift_id,
            'jam_kedatangan' => $data->jam_kedatangan,
            'nama_rm' => $data->nama_rm,
            'kode_produksi' => $data->kode_produksi,
            'berat' => $data->berat,
            'suhu' => $data->suhu,
            'sensori' => $data->sensori,
            'kemasan' => $data->kemasan,
            'keterangan' => $data->keterangan,
            'standar_berat' => $data->standar_berat,
            'berat_atas' => $data->berat_atas,
            'berat_std' => $data->berat_std,
            'berat_bawah' => $data->berat_bawah,
            'jumlah_rm' => $data->jumlah_rm,
            'nilai_jumlah_rm' => $data->nilai_jumlah_rm,
            'status_rm' => $data->status_rm,
            'catatan_rm' => $data->catatan_rm,
        ];

        // Detect changed fields
        $changedFields = [];
        foreach ($oldData as $key => $value) {
            if ($value != $newData[$key]) {
                $changedFields[] = $key;
            }
        }

        // Create log entry
        if (!empty($changedFields)) {
            $user = auth()->user();
            
            ChillroomLog::create([
                'uuid' => Str::uuid(),
                'penerimaan_chillroom_id' => $data->id,
                'penerimaan_chillroom_uuid' => $data->uuid,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role ?? 'Unknown',
                'aksi' => 'update',
                'field_yang_diubah' => $changedFields,
                'nilai_lama' => array_intersect_key($oldData, array_flip($changedFields)),
                'nilai_baru' => array_intersect_key($newData, array_flip($changedFields)),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'keterangan' => 'Data updated'
            ]);
        }

        return redirect()->route('chillroom.index')->with('success', 'Data berhasil diupdate');

    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('Validation error during update: ' . json_encode($e->errors()));
        return redirect()->back()
            ->withErrors($e->errors())
            ->withInput();
            
    } catch (\Exception $e) {
        \Log::error('Update error: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return redirect()->back()
            ->withErrors(['error' => 'Gagal mengupdate data: ' . $e->getMessage()])
            ->withInput();
    }
}

    // Hapus data
    public function destroy($uuid)
    {
          $item = PenerimaanChillroom::where('uuid', $uuid)->firstOrFail();
        $item->delete();
        return redirect()->route('chillroom.index')->with('success', 'Data berhasil dihapus');
    }

    /**
     * Show logs for specific chillroom data
     */
    public function showLogs($uuid)
    {
        $item = PenerimaanChillroom::where('uuid', $uuid)->firstOrFail();
        
        $logs = ChillroomLog::where('penerimaan_chillroom_id', $item->id)
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->simplePaginate(5);
        
        return view('qc-sistem.chillroom.logs', compact('item', 'logs'));
    }

    /**
     * API untuk mendapatkan log dalam format JSON (untuk AJAX)
     */
    public function getLogsJson($uuid)
    {
        $item = PenerimaanChillroom::where('uuid', $uuid)->firstOrFail();
        
        $logs = ChillroomLog::where('penerimaan_chillroom_id', $item->id)
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
     * Log chillroom activity
     */
   private function logActivity($chillroomUuid, $action, $oldData = null, $newData = null)
{
    try {
        $user = auth()->user();
        $chillroom = PenerimaanChillroom::where('uuid', $chillroomUuid)->first();
        
        if (!$chillroom) {
            \Log::warning("Chillroom not found for UUID: {$chillroomUuid}");
            return;
        }

        // Detect changed fields
        $changedFields = [];
        $nilaiLama = [];
        $nilaiBaru = [];

        if ($oldData && $newData) {
            foreach ($oldData as $key => $value) {
                if (isset($newData[$key]) && $value != $newData[$key]) {
                    $changedFields[] = $key;
                    $nilaiLama[$key] = $value;
                    $nilaiBaru[$key] = $newData[$key];
                }
            }
        }

        ChillroomLog::create([
            'uuid' => Str::uuid(),
            'penerimaan_chillroom_id' => $chillroom->id,
            'penerimaan_chillroom_uuid' => $chillroom->uuid,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->role ?? 'Unknown',
            'aksi' => $action,
            'field_yang_diubah' => $changedFields,
            'nilai_lama' => $nilaiLama,
            'nilai_baru' => $nilaiBaru,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'keterangan' => ucfirst($action) . ' performed'
        ]);

    } catch (\Exception $e) {
        \Log::error('Error logging activity: ' . $e->getMessage());
    }
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
            
            $query = PenerimaanChillroom::with(['plan', 'user', 'datashift', 'qcApprover', 'produksiApprover', 'spvApprover'])
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
            $pdf = PDF::loadView('qc-sistem.chillroom.export_pdf', compact('data', 'kode_form', 'filters'));
            $pdf->setPaper('A4', 'landscape');
            
            $filename = 'chillroom_' . date('Y-m-d_H-i-s') . '.pdf';
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
    public function updateKodeForm(Request $request)
    {
        try {
            $request->validate([
                'uuid' => 'required|string',
                'kode_form' => 'required|string|max:50'
            ]);

            $item = PenerimaanChillroom::where('uuid', $request->uuid)->firstOrFail();
            
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

            $item = PenerimaanChillroom::where('uuid', $uuid)->firstOrFail();

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
            ChillroomLog::create([
                'uuid' => Str::uuid(),
                'penerimaan_chillroom_id' => $item->id,
                'penerimaan_chillroom_uuid' => $item->uuid,
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
}