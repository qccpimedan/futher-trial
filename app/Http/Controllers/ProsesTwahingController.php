<?php

namespace App\Http\Controllers;

use App\Models\DataRm;
use App\Models\DataShift;
use App\Models\Plan;
use App\Models\ProsesTwahing;
use App\Models\ProsesTwahingDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProsesTwahingController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');

        $query = ProsesTwahing::with(['plan', 'user', 'shift', 'details.rm']);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('tanggal', 'LIKE', '%' . $search . '%')
                  ->orWhereHas('details.rm', function ($subQuery) use ($search) {
                      $subQuery->where('nama_rm', 'LIKE', '%' . $search . '%');
                  });
            });
        }

        $items = $query->orderByDesc('tanggal')
                       ->orderByDesc('id')
                       ->paginate(10);

        return view('qc-sistem.proses_twahing.index', compact('items', 'search'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();

        $plans = null;
        $selectedPlanId = $user->id_plan;

        if ($user->role === 'superadmin') {
            $plans = Plan::orderBy('nama_plan')->get();
            $selectedPlanId = $request->get('plan_id') ?: old('id_plan') ?: ($plans->first()->id ?? null);
        }

        if (!$selectedPlanId) {
            abort(404);
        }

        $shifts = DataShift::where('id_plan', $selectedPlanId)->orderBy('shift')->get();
        $rms = DataRm::where('id_plan', $selectedPlanId)->orderBy('nama_rm')->get();

        return view('qc-sistem.proses_twahing.create', [
            'plans' => $plans,
            'selectedPlanId' => $selectedPlanId,
            'shifts' => $shifts,
            'rms' => $rms,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'id_shift' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
            'jam' => 'nullable|date_format:H:i',
            'waktu_thawing_awal' => 'nullable|date_format:H:i',
            'waktu_thawing_akhir' => 'nullable|date_format:H:i',
            'kondisi_kemasan_rm' => 'nullable|in:utuh,sobek',
            'total_waktu_thawing_jam' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string',
            'details' => 'required|array|min:1',
            'details.*.id_rm' => 'nullable|exists:data_rm,id',
            'details.*.kode_produksi' => 'nullable|string|max:255',
            'details.*.kondisi_ruang' => 'nullable|string|max:255',
            'details.*.waktu_pemeriksaan' => 'nullable|date_format:H:i',
            'details.*.suhu_ruang' => 'nullable|numeric',
            'details.*.suhu_air_thawing' => 'nullable|numeric',
            'details.*.suhu_produk' => 'nullable|numeric',
            'details.*.kondisi_produk' => 'nullable|string|max:255',
        ];

        if ($user->role === 'superadmin') {
            $rules['id_plan'] = 'required|exists:plan,id';
        }

        $data = $request->validate($rules);

        $planId = $user->role === 'superadmin' ? (int) $data['id_plan'] : (int) $user->id_plan;

        $shift = DataShift::where('id', $data['id_shift'])->where('id_plan', $planId)->first();
        if (!$shift) {
            return back()->withErrors(['id_shift' => 'Shift tidak valid untuk plan Anda.'])->withInput();
        }

        $header = ProsesTwahing::create([
            'id_plan' => $planId,
            'user_id' => $user->id,
            'id_shift' => (int) $data['id_shift'],
            'tanggal' => $data['tanggal'],
            'jam' => $data['jam'] ?? null,
            'waktu_thawing_awal' => $data['waktu_thawing_awal'] ?? null,
            'waktu_thawing_akhir' => $data['waktu_thawing_akhir'] ?? null,
            'kondisi_kemasan_rm' => $data['kondisi_kemasan_rm'] ?? null,
            'total_waktu_thawing_jam' => $data['total_waktu_thawing_jam'] ?? null,
            'catatan' => $data['catatan'] ?? null,
        ]);

        $rows = [];
        foreach (($data['details'] ?? []) as $row) {
            if (empty($row['id_rm'])) {
                continue;
            }

            $rm = DataRm::where('id', $row['id_rm'])->where('id_plan', $planId)->first();
            if (!$rm) {
                continue;
            }

            $rows[] = [
                'uuid' => (string) Str::uuid(),
                'proses_twahing_id' => $header->id,
                'id_rm' => (int) $row['id_rm'],
                'kode_produksi' => $row['kode_produksi'] ?? null,
                'kondisi_ruang' => $row['kondisi_ruang'] ?? null,
                'waktu_pemeriksaan' => $row['waktu_pemeriksaan'] ?? null,
                'suhu_ruang' => $row['suhu_ruang'] ?? null,
                'suhu_air_thawing' => $row['suhu_air_thawing'] ?? null,
                'suhu_produk' => $row['suhu_produk'] ?? null,
                'kondisi_produk' => $row['kondisi_produk'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (count($rows) === 0) {
            $header->delete();
            return back()->withErrors(['details' => 'Minimal 1 baris detail wajib memilih Nama RM.'])->withInput();
        }

        if (count($rows) > 0) {
            ProsesTwahingDetail::insert($rows);
        }

        return redirect()->route('proses-twahing.show', $header->uuid)
            ->with('success', 'Data pemeriksaan proses thawing berhasil ditambahkan.');
    }

    public function show($uuid)
    {
        $user = Auth::user();

        $item = ProsesTwahing::with(['plan', 'user', 'shift', 'details.rm'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        return view('qc-sistem.proses_twahing.show', compact('item'));
    }

    public function edit(Request $request, $uuid)
    {
        $user = Auth::user();

        $item = ProsesTwahing::with(['details'])->where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        $plans = null;
        $selectedPlanId = (int) $item->id_plan;

        if ($user->role === 'superadmin') {
            $plans = Plan::orderBy('nama_plan')->get();
            if ($request->filled('plan_id')) {
                $selectedPlanId = (int) $request->get('plan_id');
            }
        }

        $shifts = DataShift::where('id_plan', $selectedPlanId)->orderBy('shift')->get();
        $rms = DataRm::where('id_plan', $selectedPlanId)->orderBy('nama_rm')->get();

        return view('qc-sistem.proses_twahing.edit', [
            'item' => $item,
            'plans' => $plans,
            'selectedPlanId' => $selectedPlanId,
            'shifts' => $shifts,
            'rms' => $rms,
        ]);
    }

    public function update(Request $request, $uuid)
    {
        $user = Auth::user();

        $item = ProsesTwahing::with(['details'])->where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        $planId = (int) $item->id_plan;

        $data = $request->validate([
            'id_shift' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
            'jam' => 'nullable|date_format:H:i',
            'waktu_thawing_awal' => 'nullable|date_format:H:i',
            'waktu_thawing_akhir' => 'nullable|date_format:H:i',
            'kondisi_kemasan_rm' => 'nullable|in:utuh,sobek',
            'total_waktu_thawing_jam' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string',
            'details' => 'required|array|min:1',
            'details.*.id_rm' => 'nullable|exists:data_rm,id',
            'details.*.kode_produksi' => 'nullable|string|max:255',
            'details.*.kondisi_ruang' => 'nullable|string|max:255',
            'details.*.waktu_pemeriksaan' => 'nullable|date_format:H:i',
            'details.*.suhu_ruang' => 'nullable|numeric',
            'details.*.suhu_air_thawing' => 'nullable|numeric',
            'details.*.suhu_produk' => 'nullable|numeric',
            'details.*.kondisi_produk' => 'nullable|string|max:255',
        ]);

        $shift = DataShift::where('id', $data['id_shift'])->where('id_plan', $planId)->first();
        if (!$shift) {
            return back()->withErrors(['id_shift' => 'Shift tidak valid untuk plan data ini.'])->withInput();
        }

        $item->update([
            'id_shift' => (int) $data['id_shift'],
            'tanggal' => $data['tanggal'],
            'jam' => $data['jam'] ?? null,
            'waktu_thawing_awal' => $data['waktu_thawing_awal'] ?? null,
            'waktu_thawing_akhir' => $data['waktu_thawing_akhir'] ?? null,
            'kondisi_kemasan_rm' => $data['kondisi_kemasan_rm'] ?? null,
            'total_waktu_thawing_jam' => $data['total_waktu_thawing_jam'] ?? null,
            'catatan' => $data['catatan'] ?? null,
        ]);

        ProsesTwahingDetail::where('proses_twahing_id', $item->id)->delete();

        $rows = [];
        foreach (($data['details'] ?? []) as $row) {
            if (empty($row['id_rm'])) {
                continue;
            }

            $rm = DataRm::where('id', $row['id_rm'])->where('id_plan', $planId)->first();
            if (!$rm) {
                continue;
            }

            $rows[] = [
                'uuid' => (string) Str::uuid(),
                'proses_twahing_id' => $item->id,
                'id_rm' => (int) $row['id_rm'],
                'kode_produksi' => $row['kode_produksi'] ?? null,
                'kondisi_ruang' => $row['kondisi_ruang'] ?? null,
                'waktu_pemeriksaan' => $row['waktu_pemeriksaan'] ?? null,
                'suhu_ruang' => $row['suhu_ruang'] ?? null,
                'suhu_air_thawing' => $row['suhu_air_thawing'] ?? null,
                'suhu_produk' => $row['suhu_produk'] ?? null,
                'kondisi_produk' => $row['kondisi_produk'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (count($rows) === 0) {
            return back()->withErrors(['details' => 'Minimal 1 baris detail wajib memilih Nama RM.'])->withInput();
        }

        ProsesTwahingDetail::insert($rows);

        return redirect()->route('proses-twahing.show', $item->uuid)
            ->with('success', 'Data pemeriksaan proses thawing berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $user = Auth::user();

        $item = ProsesTwahing::where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        $item->delete();

        return redirect()->route('proses-twahing.index')
            ->with('success', 'Data pemeriksaan proses thawing berhasil dihapus.');
    }

    public function approve(Request $request, $uuid)
    {
        try {
            $request->validate([
                'type' => 'required|in:qc,produksi,spv',
            ]);

            $item = ProsesTwahing::where('uuid', $uuid)->firstOrFail();
            $user = Auth::user();
            $userRole = $user->id_role ?? null;

            if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk data ini',
                ], 403);
            }

            $type = $request->type;
            switch ($type) {
                case 'qc':
                    if (!in_array($userRole, [1, 3, 5])) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Anda tidak memiliki wewenang untuk menyetujui sebagai QC',
                        ], 403);
                    }
                    break;
                case 'produksi':
                    if ($userRole != 2) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Anda tidak memiliki wewenang untuk menyetujui sebagai Produksi',
                        ], 403);
                    }
                    if (!$item->approved_by_qc) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Data harus disetujui oleh QC terlebih dahulu',
                        ], 400);
                    }
                    break;
                case 'spv':
                    if ($userRole != 4) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Anda tidak memiliki wewenang untuk menyetujui sebagai SPV',
                        ], 403);
                    }
                    if (!$item->approved_by_produksi) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Data harus disetujui oleh Produksi terlebih dahulu',
                        ], 400);
                    }
                    break;
            }

            $approvalField = "approved_by_{$type}";
            if ($item->$approvalField) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data sudah disetujui sebelumnya',
                ], 400);
            }

            $updateData = [
                $approvalField => true,
                "approved_by_{$type}_at" => now(),
                "approved_by_{$type}_user_id" => $user->id,
            ];

            $item->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disetujui',
                'data' => $item->fresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function bulkExportPdf(Request $request)
    {
        try {
            $request->validate([
                'tanggal' => 'nullable|date',
                'id_shift' => 'nullable|exists:data_shift,id',
                'kode_form' => 'required|string|max:50',
            ]);

            $user = Auth::user();

            $query = ProsesTwahing::with(['plan', 'user', 'shift', 'details.rm'])
                ->when($user->role !== 'superadmin', function ($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                });

            if ($request->tanggal) {
                $query->whereDate('tanggal', $request->tanggal);
            }

            if ($request->id_shift) {
                $query->where('id_shift', $request->id_shift);
            }

            $data = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->get();

            if ($data->isNotEmpty()) {
                $query->update(['kode_form' => $request->kode_form]);
            }

            if ($data->isEmpty()) {
                $errorMessage = 'Tidak ada data yang sesuai dengan filter yang dipilih.';
                $filterInfo = [];

                if ($request->tanggal) {
                    $filterInfo[] = 'Tanggal: ' . \Carbon\Carbon::parse($request->tanggal)->format('d-m-Y');
                }
                if ($request->id_shift) {
                    $shift = DataShift::find($request->id_shift);
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

            $kode_form = $request->kode_form;
            $pdf = \PDF::loadView('qc-sistem.proses_twahing.export_pdf', compact('data', 'kode_form'));
            $pdf->setPaper('A4', 'landscape');

            $filename = 'proses_twahing_' . date('Y-m-d_H-i-s') . '.pdf';
            return $pdf->download($filename);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor PDF: ' . $e->getMessage(),
            ], 500);
        }
    }
}
