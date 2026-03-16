<?php

namespace App\Http\Controllers\QcSistem;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\VerifCip;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class VerifCipController extends Controller
{
    private function normalizeTanggal(string $rawTanggal, bool $isSpecialRole): string
    {
        if ($isSpecialRole) {
            $dateOnly = Carbon::parse($rawTanggal)->format('Y-m-d');
            $timeNow = now()->format('H:i:s');
            return $dateOnly . ' ' . $timeNow;
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $rawTanggal)) {
            $timeNow = now()->format('H:i:s');
            return $rawTanggal . ' ' . $timeNow;
        }

        return Carbon::parse($rawTanggal)->format('Y-m-d H:i:s');
    }

    public function index()
    {
        $user = Auth::user();

        $search = trim((string) request('search', ''));
        $perPage = (int) request('perPage', 15);
        $allowedPerPage = [5, 10, 25, 50, 100];
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 15;
        }

        $query = VerifCip::with(['plan', 'user'])
            ->orderByDesc('tanggal')
            ->orderByDesc('id');

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $items = $query->get();

        $rows = [];
        foreach ($items as $item) {
            $forms = $item->payload['forms'] ?? [];
            if (!is_array($forms) || count($forms) === 0) {
                $forms = [
                    [
                        'tanggal' => $item->tanggal,
                        'details' => [],
                    ],
                ];
            }

            foreach ($forms as $formIndex => $form) {
                $formTanggal = data_get($form, 'tanggal') ?: $item->tanggal;
                $details = data_get($form, 'details', []);
                if (!is_array($details) || count($details) === 0) {
                    $details = [null];
                }

                foreach ($details as $detailIndex => $detail) {
                    $rows[] = [
                        'item' => $item,
                        'formIndex' => $formIndex,
                        'detailIndex' => is_int($detailIndex) ? $detailIndex : 0,
                        'formTanggal' => $formTanggal,
                        'detail' => $detail,
                    ];
                }
            }
        }

        usort($rows, function ($a, $b) {
            $da = $a['formTanggal'] ? strtotime($a['formTanggal']) : 0;
            $db = $b['formTanggal'] ? strtotime($b['formTanggal']) : 0;
            if ($da !== $db) {
                return $da <=> $db;
            }

            if ($a['formIndex'] !== $b['formIndex']) {
                return $a['formIndex'] <=> $b['formIndex'];
            }

            return ($a['detailIndex'] ?? 0) <=> ($b['detailIndex'] ?? 0);
        });

        if ($search !== '') {
            $needle = $search;
            $trimNeedle = trim($needle);
            $isSingleLetter = preg_match('/^[a-zA-Z]$/', $trimNeedle) === 1;
            $needleUpper = strtoupper($trimNeedle);
            $rows = array_values(array_filter($rows, function ($row) use ($needle, $isSingleLetter, $needleUpper) {
                $item = $row['item'] ?? null;
                if (!$item) {
                    return false;
                }

                $uuid = (string) ($item->uuid ?? '');
                $tanggal = (string) ($item->tanggal ?? '');
                $userName = (string) (optional($item->user)->name ?? '');
                $planNama = (string) (optional($item->plan)->nama_plan ?? '');
                $planJenisMoldrum = (string) (optional($item->plan)->jenis_moldrum ?? '');
                $planJenisMouldrum = (string) (optional($item->plan)->jenis_mouldrum ?? '');

                $detail = $row['detail'] ?? [];
                $detailJenisMoldrum = (string) (data_get($detail, 'jenis_moldrum') ?? '');
                $detailJenisMouldrum = (string) (data_get($detail, 'jenis_mouldrum') ?? '');
                $detailJson = is_array($detail) ? (string) json_encode($detail) : (string) $detail;

                if ($isSingleLetter) {
                    $detailJenisMoldrumUpper = strtoupper(trim($detailJenisMoldrum));
                    $detailJenisMouldrumUpper = strtoupper(trim($detailJenisMouldrum));
                    $planJenisMoldrumUpper = strtoupper(trim($planJenisMoldrum));
                    $planJenisMouldrumUpper = strtoupper(trim($planJenisMouldrum));

                    return $detailJenisMoldrumUpper === $needleUpper
                        || $detailJenisMouldrumUpper === $needleUpper
                        || $planJenisMoldrumUpper === $needleUpper
                        || $planJenisMouldrumUpper === $needleUpper;
                }

                return stripos($uuid, $needle) !== false
                    || stripos($tanggal, $needle) !== false
                    || stripos($userName, $needle) !== false
                    || stripos($planNama, $needle) !== false
                    || stripos($planJenisMoldrum, $needle) !== false
                    || stripos($planJenisMouldrum, $needle) !== false
                    || stripos($detailJenisMoldrum, $needle) !== false
                    || stripos($detailJenisMouldrum, $needle) !== false
                    || stripos($detailJson, $needle) !== false;
            }));
        }

        $page = (int) request('page', 1);
        $page = $page > 0 ? $page : 1;
        $total = count($rows);
        $results = array_slice($rows, ($page - 1) * $perPage, $perPage);

        $rowsPaginator = new LengthAwarePaginator(
            $results,
            $total,
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );

        return view('qc-sistem.verif_cip.index', compact('rowsPaginator', 'search', 'perPage'));
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

        return view('qc-sistem.verif_cip.create', [
            'plans' => $plans,
            'selectedPlanId' => $selectedPlanId,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $isSpecialRole = ((int) ($user->id_role ?? 0) === 2 || (int) ($user->id_role ?? 0) === 3);

        $rules = [
            'payload' => 'required|string',
            'tanggal' => 'required|date',
        ];

        if ($user->role === 'superadmin') {
            $rules['id_plan'] = 'required|exists:plan,id';
        }

        $data = $request->validate($rules);

        $data['tanggal'] = $this->normalizeTanggal($data['tanggal'], $isSpecialRole);

        $planId = $user->role === 'superadmin' ? (int) $data['id_plan'] : (int) $user->id_plan;

        $payload = json_decode($data['payload'], true);
        if (!is_array($payload)) {
            return back()->withErrors(['payload' => 'Payload tidak valid.'])->withInput();
        }

        $item = VerifCip::create([
            'id_plan' => $planId,
            'user_id' => $user->id,
            'tanggal' => $data['tanggal'],
            'payload' => $payload,
        ]);

        return redirect()->route('verif-cip.show', $item->uuid)
            ->with('success', 'Data Verif CIP berhasil ditambahkan.');
    }

    public function show($uuid)
    {
        $user = Auth::user();

        $item = VerifCip::with(['plan', 'user'])->where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        return view('qc-sistem.verif_cip.show', compact('item'));
    }

    public function edit($uuid)
    {
        $user = Auth::user();

        $item = VerifCip::with(['plan', 'user'])->where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        $plans = null;
        $selectedPlanId = (int) $item->id_plan;
        if ($user->role === 'superadmin') {
            $plans = Plan::orderBy('nama_plan')->get();
        }

        return view('qc-sistem.verif_cip.edit', [
            'item' => $item,
            'plans' => $plans,
            'selectedPlanId' => $selectedPlanId,
        ]);
    }

    public function update(Request $request, $uuid)
    {
        $user = Auth::user();
        $isSpecialRole = ((int) ($user->id_role ?? 0) === 2 || (int) ($user->id_role ?? 0) === 3);

        $item = VerifCip::where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        $data = $request->validate([
            'payload' => 'required|string',
            'tanggal' => 'required|date',
        ]);

        $data['tanggal'] = $this->normalizeTanggal($data['tanggal'], $isSpecialRole);

        $payload = json_decode($data['payload'], true);
        if (!is_array($payload)) {
            return back()->withErrors(['payload' => 'Payload tidak valid.'])->withInput();
        }

        $item->update([
            'tanggal' => $data['tanggal'],
            'payload' => $payload,
        ]);

        return redirect()->route('verif-cip.show', $item->uuid)
            ->with('success', 'Data Verif CIP berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $user = Auth::user();

        $item = VerifCip::where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        $item->delete();

        return redirect()->route('verif-cip.index')
            ->with('success', 'Data Verif CIP berhasil dihapus.');
    }

    public function approve(Request $request, $uuid)
    {
        try {
            $request->validate([
                'type' => 'required|in:qc,produksi,spv',
            ]);

            $item = VerifCip::where('uuid', $uuid)->firstOrFail();
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
                'kode_form' => 'required|string|max:50',
            ]);

            $user = Auth::user();

            $query = VerifCip::with(['plan', 'user'])
                ->when($user->role !== 'superadmin', function ($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                });

            if ($request->tanggal) {
                $query->whereDate('tanggal', $request->tanggal);
            }

            $data = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->get();

            if ($data->isEmpty()) {
                $errorMessage = 'Tidak ada data yang sesuai dengan filter yang dipilih.';
                $filterInfo = [];

                if ($request->tanggal) {
                    $filterInfo[] = 'Tanggal: ' . \Carbon\Carbon::parse($request->tanggal)->format('d-m-Y');
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
            $pdf = \PDF::loadView('qc-sistem.verif_cip.export_pdf', compact('data', 'kode_form'));
            $pdf->setPaper('A3', 'landscape');

            $filename = 'verif_cip_' . date('Y-m-d_H-i-s') . '.pdf';
            return $pdf->download($filename);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor PDF: ' . $e->getMessage(),
            ], 500);
        }
    }
}
