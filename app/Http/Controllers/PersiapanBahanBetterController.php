<?php

namespace App\Http\Controllers;

use App\Models\PersiapanBahanBetter;
use App\Models\AktualBetter;
use App\Models\Plan;
use App\Models\JenisProduk;
use App\Models\JenisBetter;
use App\Models\StdSalinitasViskositas;
use App\Models\PersiapanBahanBetterLog;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class PersiapanBahanBetterController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = PersiapanBahanBetter::with(['user', 'plan', 'produk', 'better', 'aktuals.std', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover']);
        
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        // Search functionality
        $search = request('search');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('produk', function ($produkQuery) use ($search) {
                    $produkQuery->where('nama_produk', 'like', '%' . $search . '%');
                })->orWhere('kode_produksi_produk', 'like', '%' . $search . '%');
            });
        }

        // Per page handling
        $perPage = request()->get('per_page', 10);
        $perPage = in_array($perPage, [5, 10, 25, 50, 100]) ? $perPage : 10;

        $data = $query->orderBy('tanggal', 'desc')->orderBy('jam', 'desc')->paginate($perPage);
        
        return view('qc-sistem.persiapan_bahan_better.index', compact('data', 'search', 'perPage'));
    }

    public function create()
    {
        $user = auth()->user();
        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $produks = JenisProduk::all();
            $betters = JenisBetter::all();
            $shifts = \App\Models\DataShift::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $betters = JenisBetter::where('id_plan', $user->id_plan)->get();
            $shifts = \App\Models\DataShift::where('id_plan', $user->id_plan)->get();
        }
        return view('qc-sistem.persiapan_bahan_better.create', compact('plans', 'produks', 'betters', 'shifts'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $data = $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'id_better' => 'required|exists:jenis_better,id',
            'kode_produksi_produk' => 'required|string',
            'kode_form' => 'nullable|string',
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'jam' => 'required|date_format:H:i',
            'better_rows' => 'required|array|min:1',
            'better_rows.*.master_nama_formula_better' => 'nullable|string',
            'better_rows.*.master_berat' => 'required|numeric',
            'better_rows.*.kode_produksi_better' => 'required|string',
            'better_rows.*.suhu_air' => 'required|numeric',
            'better_rows.*.sensori' => 'nullable|string',
            'aktual_vis' => 'required|array',
            'aktual_sal' => 'required|array',
            'aktual_suhu_air' => 'required|array',
            'id_std_salinitas_viskositas' => 'required|array',
        ]);

        $rows = $data['better_rows'] ?? [];
        $totalBeratBetter = 0;
        $sumSuhuAir = 0;
        $countSuhuAir = 0;

        foreach ($rows as $r) {
            $b = $r['master_berat'] ?? null;
            if ($b !== null && $b !== '') {
                $n = is_numeric($b) ? (float) $b : (float) preg_replace('/[^0-9.\-]/', '', (string) $b);
                if (!is_nan($n)) {
                    $totalBeratBetter += $n;
                }
            }
            $suhu = $r['suhu_air'] ?? null;
            if ($suhu !== null && $suhu !== '' && is_numeric($suhu)) {
                $sumSuhuAir += (float) $suhu;
                $countSuhuAir++;
            }
        }

        // Pastikan field berat_air tidak tersimpan lagi di JSON rows (kompatibilitas data lama)
        foreach ($rows as $idx => $r) {
            if (is_array($r) && array_key_exists('berat_air', $r)) {
                unset($r['berat_air']);
                $rows[$idx] = $r;
            }
        }

        $firstRow = $rows[0] ?? [];
        $data['better_rows'] = $rows;
        $data['berat_better'] = $totalBeratBetter;
        $data['suhu_air'] = $countSuhuAir > 0 ? ($sumSuhuAir / $countSuhuAir) : null;
        $data['sensori'] = $firstRow['sensori'] ?? null;
        $data['kode_produksi_better'] = $firstRow['kode_produksi_better'] ?? null;

        $data['user_id'] = $user->id;
        $data['id_plan'] = $user->id_plan;
        // Normalisasi format tanggal ke Y-m-d H:i:s untuk database
        $data['tanggal'] = Carbon::createFromFormat('d-m-Y H:i:s', $data['tanggal'])->format('Y-m-d H:i:s');
        // Simpan jam
        $data['jam'] = $data['jam'];
        // Simpan suhu_air ke tabel persiapan_bahan_better
        $persiapan = PersiapanBahanBetter::create($data);

        foreach ($data['id_std_salinitas_viskositas'] as $i => $id_std) {
            AktualBetter::create([
                'id_std_salinitas_viskositas' => $id_std,
                'id_persiapan_bahan_better' => $persiapan->id,
                'aktual_vis' => $data['aktual_vis'][$i],
                'aktual_sal' => $data['aktual_sal'][$i],
                'aktual_suhu_air' => $data['aktual_suhu_air'][$i],
            ]);
        }

        return redirect()->route('persiapan-bahan-better.index')->with('success', 'Data berhasil disimpan');
    }

    public function show($uuid)
    {
        $user = auth()->user();
        $item = PersiapanBahanBetter::where('uuid', $uuid)->with(['aktuals.std', 'shift', 'produk', 'better'])->firstOrFail();
        
        // Check access for non-superadmin users
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return view('qc-sistem.persiapan_bahan_better.show', compact('item'));
    }

    public function edit($uuid)
    {
        $user = auth()->user();
        $item = PersiapanBahanBetter::where('uuid', $uuid)->with(['aktuals.std', 'shift'])->firstOrFail();
        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $produks = JenisProduk::all();
            $betters = JenisBetter::all();
            $shifts = \App\Models\DataShift::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $betters = JenisBetter::where('id_plan', $user->id_plan)->get();
            $shifts = \App\Models\DataShift::where('id_plan', $user->id_plan)->get();
        }
        return view('qc-sistem.persiapan_bahan_better.edit', compact('item', 'plans', 'produks', 'betters', 'shifts'));
    }

    public function update(Request $request, $uuid)
    {
        $user = auth()->user();
        $data = $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'id_better' => 'required|exists:jenis_better,id',
            'kode_produksi_produk' => 'required|string',
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'id_std_salinitas_viskositas' => 'required|array',
            'aktual_vis' => 'required|array',
            'aktual_sal' => 'required|array',
            'aktual_suhu_air' => 'required|array',
            'better_rows' => 'required|array|min:1',
            'better_rows.*.master_nama_formula_better' => 'nullable|string',
            'better_rows.*.master_berat' => 'required|numeric',
            'better_rows.*.kode_produksi_better' => 'required|string',
            'better_rows.*.suhu_air' => 'required|numeric',
            'better_rows.*.sensori' => 'nullable|string',
        ]);

        $rows = $data['better_rows'] ?? [];
        $totalBeratBetter = 0;
        $sumSuhuAir = 0;
        $countSuhuAir = 0;

        foreach ($rows as $r) {
            $b = $r['master_berat'] ?? null;
            if ($b !== null && $b !== '') {
                $n = is_numeric($b) ? (float) $b : (float) preg_replace('/[^0-9.\-]/', '', (string) $b);
                if (!is_nan($n)) {
                    $totalBeratBetter += $n;
                }
            }
            $suhu = $r['suhu_air'] ?? null;
            if ($suhu !== null && $suhu !== '' && is_numeric($suhu)) {
                $sumSuhuAir += (float) $suhu;
                $countSuhuAir++;
            }
        }

        // Pastikan field berat_air tidak tersimpan lagi di JSON rows (kompatibilitas data lama)
        foreach ($rows as $idx => $r) {
            if (is_array($r) && array_key_exists('berat_air', $r)) {
                unset($r['berat_air']);
                $rows[$idx] = $r;
            }
        }

        $firstRow = $rows[0] ?? [];
        $data['better_rows'] = $rows;
        $data['berat_better'] = $totalBeratBetter;
        $data['suhu_air'] = $countSuhuAir > 0 ? ($sumSuhuAir / $countSuhuAir) : null;
        $data['sensori'] = $firstRow['sensori'] ?? null;
        $data['kode_produksi_better'] = $firstRow['kode_produksi_better'] ?? null;

        $data['user_id'] = $user->id;
        $data['id_plan'] = $user->id_plan;
        // Normalisasi tanggal
        $data['tanggal'] = Carbon::createFromFormat('d-m-Y H:i:s', $data['tanggal'])->format('Y-m-d H:i:s');

        $persiapan = PersiapanBahanBetter::where('uuid', $uuid)->firstOrFail();
        $persiapan->update($data);

        // Hapus data aktual lama
        AktualBetter::where('id_persiapan_bahan_better', $persiapan->id)->delete();

        // Insert data aktual baru
        foreach ($data['id_std_salinitas_viskositas'] as $i => $id_std) {
            AktualBetter::create([
                'id_std_salinitas_viskositas' => $id_std,
                'id_persiapan_bahan_better' => $persiapan->id,
                'aktual_vis' => $data['aktual_vis'][$i],
                'aktual_sal' => $data['aktual_sal'][$i],
                'aktual_suhu_air' => $data['aktual_suhu_air'][$i],
            ]);
        }

        return redirect()->route('persiapan-bahan-better.index')->with('success', 'Data berhasil diupdate');
    }

    public function destroy($uuid)
    {
        $persiapan = PersiapanBahanBetter::where('uuid', $uuid)->firstOrFail();
        AktualBetter::where('id_persiapan_bahan_better', $persiapan->id)->delete();
        $persiapan->delete();
        return redirect()->route('persiapan-bahan-better.index')->with('success', 'Data berhasil dihapus');
    }

    /**
     * Menampilkan riwayat log perubahan data
     */
    public function showLogs($uuid)
    {
        $item = PersiapanBahanBetter::where('uuid', $uuid)->firstOrFail();
        
        $logs = PersiapanBahanBetterLog::where('persiapan_bahan_better_id', $item->id)
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->paginate(5);
        
        return view('qc-sistem.persiapan_bahan_better.logs', compact('item', 'logs'));
    }

    /**
     * API untuk mendapatkan log dalam format JSON (untuk AJAX)
     */
    public function getLogsJson($uuid)
    {
        $item = PersiapanBahanBetter::where('uuid', $uuid)->firstOrFail();
        
        $logs = PersiapanBahanBetterLog::where('persiapan_bahan_better_id', $item->id)
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
     * Export data to PDF with filters
     */
    public function exportPdf(Request $request)
    {
        $user = auth()->user();
        
        // Validasi input
        $request->validate([
            'kode_form' => 'required|string',
            'produk_filter' => 'nullable|exists:jenis_produk,id',
            'tanggal_dari' => 'nullable|date',
            'tanggal_sampai' => 'nullable|date',
            'shift_filter' => 'nullable|exists:data_shift,id'
        ]);

        // Query data dengan filter
        $query = PersiapanBahanBetter::with(['user', 'plan', 'produk', 'better', 'aktuals.std', 'shift']);
        
        // Filter berdasarkan role user
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        // Apply filters
        if ($request->produk_filter) {
            $query->where('id_produk', $request->produk_filter);
        }

        if ($request->tanggal_dari) {
            if ($request->tanggal_sampai) {
                // Range filter jika ada tanggal_sampai
                $query->whereBetween('tanggal', [
                    Carbon::parse($request->tanggal_dari)->startOfDay(),
                    Carbon::parse($request->tanggal_sampai)->endOfDay()
                ]);
            } else {
                // Single date filter jika hanya tanggal_dari
                $query->whereDate('tanggal', Carbon::parse($request->tanggal_dari)->format('Y-m-d'));
            }
        }

        if ($request->shift_filter) {
            $query->where('shift_id', $request->shift_filter);
        }

        $data = $query->orderBy('tanggal', 'desc')->get();

        // Data untuk PDF
        $pdfData = [
            'data' => $data,
            'kode_form' => $request->kode_form,
            'filters' => [
                'produk' => $request->produk_filter ? JenisProduk::find($request->produk_filter)->nama_produk : 'Semua Produk',
                'tanggal_dari' => $request->tanggal_dari ? Carbon::parse($request->tanggal_dari)->format('d/m/Y') : '-',
                'tanggal_sampai' => $request->tanggal_sampai ? Carbon::parse($request->tanggal_sampai)->format('d/m/Y') : '-',
                'shift' => $request->shift_filter ? \App\Models\DataShift::find($request->shift_filter)->shift : 'Semua Shift'
            ],
            'exported_at' => Carbon::now()->format('d/m/Y H:i:s'),
            'exported_by' => $user->name
        ];

        // Generate PDF
        $pdf = Pdf::loadView('qc-sistem.persiapan_bahan_better.export_pdf', $pdfData);
        $pdf->setPaper('A4', 'landscape');

        // Nama file (sanitasi agar tidak ada '/' dan '\\')
        $safeKodeForm = str_replace(['/', '\\', ':'], '-', (string) $request->kode_form);
        $safeKodeForm = trim($safeKodeForm);
        $filename = 'Persiapan_Bahan_Better_' . $safeKodeForm . '_' . Carbon::now()->format('YmdHis') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Handle approval requests
     */
    public function approve(Request $request, $uuid)
    {
        try {
            $user = auth()->user();
            $type = $request->input('type'); // qc, produksi, spv

            // Validate approval type
            if (!in_array($type, ['qc', 'produksi', 'spv'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipe persetujuan tidak valid.'
                ], 400);
            }

            // Find the record
            $persiapanBahanBetter = PersiapanBahanBetter::where('uuid', $uuid)->firstOrFail();

            // Role-based authorization
            $userRole = $user->id_role;
            $approvalField = "approved_by_{$type}";

            // Check if user has permission for this approval type
            if (!$this->canApprove($userRole, $type)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk melakukan persetujuan ini.'
                ], 403);
            }

            // Check if already approved
            if ($persiapanBahanBetter->{$approvalField}) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data sudah disetujui sebelumnya.'
                ], 400);
            }

            // Sequential approval validation
            if ($type === 'produksi' && !$persiapanBahanBetter->approved_by_qc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data harus disetujui oleh QC terlebih dahulu.'
                ], 400);
            }

            if ($type === 'spv' && !$persiapanBahanBetter->approved_by_produksi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data harus disetujui oleh Produksi terlebih dahulu.'
                ], 400);
            }

            // Update approval
            $persiapanBahanBetter->update([
                $approvalField => true,
                "{$type}_approved_by" => $user->id,
                "{$type}_approved_at" => now()
            ]);

            // Log the approval activity
            PersiapanBahanBetterLog::create([
                'persiapan_bahan_better_id' => $persiapanBahanBetter->id,
                'persiapan_bahan_better_uuid' => $persiapanBahanBetter->uuid,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->id_role,
                'aksi' => 'approve',
                'field_yang_diubah' => [$approvalField],
                'nilai_lama' => [$approvalField => false],
                'nilai_baru' => [$approvalField => true],
                'keterangan' => "Disetujui oleh {$type}: {$user->name}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disetujui.',
                'data' => $persiapanBahanBetter->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if user can approve based on role and approval type
     */
    private function canApprove($userRole, $type)
    {
        $permissions = [
            1 => ['qc'], // Role 1: QC only
            2 => ['produksi'], // Role 2: Produksi only  
            3 => ['qc'], // Role 3: QC only
            4 => ['spv'], // Role 4: SPV only
            5 => ['qc'] // Role 5: QC only
        ];
        
        return isset($permissions[$userRole]) && in_array($type, $permissions[$userRole]);
    }
}