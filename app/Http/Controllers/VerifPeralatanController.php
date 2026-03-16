<?php

namespace App\Http\Controllers;

use App\Models\DataShift;
use App\Models\InputArea;
use App\Models\InputMesinPeralatan;
use App\Models\Plan;
use App\Models\VerifPeralatan;
use App\Models\VerifPeralatanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifPeralatanController extends Controller
{
    private function normalizeVerifikasi($value): bool
    {
        if ($value === null) {
            return false;
        }

        if (is_bool($value)) {
            return $value;
        }

        $str = is_string($value) ? trim($value) : $value;

        if ($str === 1 || $str === '1' || $str === 'OK' || $str === 'ok') {
            return true;
        }

        return false;
    }

    public function index()
    {
        $user = Auth::user();

        $query = VerifPeralatan::with(['plan', 'user', 'shift', 'details.mesin.area']);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $search = request('search');
        if ($search) {
            $query->where('tanggal', 'LIKE', '%' . $search . '%');
        }

        $items = $query->orderByDesc('tanggal')
                       ->orderByDesc('id')
                       ->paginate(10);

        return view('qc-sistem.verif_peralatan.index', compact('items'));
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

        $mesins = InputMesinPeralatan::with(['area'])
            ->where('id_plan', $selectedPlanId)
            ->orderBy('nama_mesin')
            ->get();

        $areas = InputArea::with(['subarea'])
            ->where('id_plan', $selectedPlanId)
            ->orderBy('area')
            ->get();

        return view('qc-sistem.verif_peralatan.create', [
            'plans' => $plans,
            'selectedPlanId' => $selectedPlanId,
            'shifts' => $shifts,
            'mesins' => $mesins,
            'areas' => $areas,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'id_shift' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
            'jam' => 'nullable|date_format:H:i',
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

        $header = VerifPeralatan::create([
            'id_plan' => $planId,
            'user_id' => $user->id,
            'id_shift' => (int) $data['id_shift'],
            'tanggal' => $data['tanggal'],
            'jam' => $data['jam'] ?? now()->format('H:i'),
        ]);

        $mesins = InputMesinPeralatan::where('id_plan', $planId)->get();

        $rows = [];
        foreach ($mesins as $mesin) {
            $areaId = (int) $mesin->id_area;

            $path = "details.{$mesin->id}";
            $verifikasiRaw = data_get($request->all(), $path . '.verifikasi', null);
            $verifikasi = $this->normalizeVerifikasi($verifikasiRaw);
            $keterangan = data_get($request->all(), $path . '.keterangan');
            $tindakan = data_get($request->all(), $path . '.tindakan_koreksi');

            $rows[] = [
                'verif_peralatan_id' => $header->id,
                'id_mesin' => (int) $mesin->id,
                'id_area' => $areaId,
                'verifikasi' => $verifikasi,
                'keterangan' => $keterangan,
                'tindakan_koreksi' => $tindakan,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (count($rows) > 0) {
            VerifPeralatanDetail::insert($rows);
        }

        return redirect()->route('verif-peralatan.show', $header->uuid)
            ->with('success', 'Data verifikasi peralatan berhasil ditambahkan.');
    }

    public function show($uuid)
    {
        $user = Auth::user();

        $item = VerifPeralatan::with([
            'plan',
            'user',
            'shift',
            'details.mesin.area',
        ])->where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        $detailsByArea = $item->details
            ->sortBy(function ($d) {
                $area = $d->mesin->area->area ?? '';
                $mesin = $d->mesin->nama_mesin ?? '';
                return $area . '|' . $mesin;
            })
            ->groupBy('id_area');

        return view('qc-sistem.verif_peralatan.show', compact('item', 'detailsByArea'));
    }

    public function edit($uuid)
    {
        $user = Auth::user();

        $item = VerifPeralatan::with(['details'])->where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        $plans = null;
        $selectedPlanId = (int) $item->id_plan;

        if ($user->role === 'superadmin') {
            $plans = Plan::orderBy('nama_plan')->get();
        }

        $shifts = DataShift::where('id_plan', $selectedPlanId)->orderBy('shift')->get();

        $mesins = InputMesinPeralatan::with(['area'])
            ->where('id_plan', $selectedPlanId)
            ->orderBy('nama_mesin')
            ->get();

        $areas = InputArea::where('id_plan', $selectedPlanId)->orderBy('area')->get();

        $existing = $item->details->keyBy(function ($d) {
            return $d->id_mesin;
        });

        return view('qc-sistem.verif_peralatan.edit', [
            'item' => $item,
            'plans' => $plans,
            'selectedPlanId' => $selectedPlanId,
            'shifts' => $shifts,
            'mesins' => $mesins,
            'areas' => $areas,
            'existing' => $existing,
        ]);
    }

    public function update(Request $request, $uuid)
    {
        $user = Auth::user();

        $item = VerifPeralatan::with(['details'])->where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        $rules = [
            'id_shift' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
            'jam' => 'nullable|date_format:H:i',
        ];

        $data = $request->validate($rules);

        $planId = (int) $item->id_plan;

        $shift = DataShift::where('id', $data['id_shift'])->where('id_plan', $planId)->first();
        if (!$shift) {
            return back()->withErrors(['id_shift' => 'Shift tidak valid untuk plan data ini.'])->withInput();
        }

        $updateData = [
            'id_shift' => (int) $data['id_shift'],
            'tanggal' => $data['tanggal'],
        ];

        if (!empty($data['jam'])) {
            $updateData['jam'] = $data['jam'];
        }

        $item->update($updateData);

        VerifPeralatanDetail::where('verif_peralatan_id', $item->id)->delete();

        $mesins = InputMesinPeralatan::where('id_plan', $planId)->get();

        $rows = [];
        foreach ($mesins as $mesin) {
            $areaId = (int) $mesin->id_area;

            $path = "details.{$mesin->id}";
            $verifikasiRaw = data_get($request->all(), $path . '.verifikasi', null);
            $verifikasi = $this->normalizeVerifikasi($verifikasiRaw);
            $keterangan = data_get($request->all(), $path . '.keterangan');
            $tindakan = data_get($request->all(), $path . '.tindakan_koreksi');

            $rows[] = [
                'verif_peralatan_id' => $item->id,
                'id_mesin' => (int) $mesin->id,
                'id_area' => $areaId,
                'verifikasi' => $verifikasi,
                'keterangan' => $keterangan,
                'tindakan_koreksi' => $tindakan,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (count($rows) > 0) {
            VerifPeralatanDetail::insert($rows);
        }

        return redirect()->route('verif-peralatan.show', $item->uuid)
            ->with('success', 'Data verifikasi peralatan berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $user = Auth::user();

        $item = VerifPeralatan::where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        $item->delete();

        return redirect()->route('verif-peralatan.index')
            ->with('success', 'Data verifikasi peralatan berhasil dihapus.');
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

            $query = VerifPeralatan::with(['plan', 'user', 'shift', 'details.mesin.area'])
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
            $pdf = \PDF::loadView('qc-sistem.verif_peralatan.export_pdf', compact('data', 'kode_form'));
            $pdf->setPaper('A4', 'landscape');

            $filename = 'verif_peralatan_' . date('Y-m-d_H-i-s') . '.pdf';
            return $pdf->download($filename);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor PDF: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function approve(Request $request, $uuid)
    {
        try {
            $request->validate([
                'type' => 'required|in:qc,produksi,spv',
            ]);

            $item = VerifPeralatan::where('uuid', $uuid)->firstOrFail();
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
}
