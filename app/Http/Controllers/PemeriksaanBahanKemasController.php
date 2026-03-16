<?php

namespace App\Http\Controllers;

use App\Models\PemeriksaanBahanKemas;
use App\Models\PemeriksaanBahanKemasLog;
use App\Models\DataShift;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PemeriksaanBahanKemasTemplateExport;
use App\Imports\PemeriksaanBahanKemasImport;

class PemeriksaanBahanKemasController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $search = trim((string) request('search', ''));
        $perPage = request('per_page', 10);

        $query = PemeriksaanBahanKemas::with(['user', 'plan', 'shift']);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nama_kemasan', 'like', "%{$search}%")
                    ->orWhere('kode_produksi', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('tanggal', 'desc')->paginate($perPage);
        $items->appends(['search' => $search, 'per_page' => $perPage]);

        return view('qc-sistem.pemeriksaan_bahan_kemas.index', compact('items', 'search', 'perPage'));
    }

    public function create()
    {
        $user = Auth::user();

        if ($user->role === 'superadmin') {
            $shifts = DataShift::with('plan')->get();
            $plans = Plan::all();
        } else {
            $shifts = DataShift::with('plan')->where('id_plan', $user->id_plan)->get();
            $plans = Plan::where('id', $user->id_plan)->get();
        }

        return view('qc-sistem.pemeriksaan_bahan_kemas.create', compact('shifts', 'plans'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.jam' => 'required|date_format:H:i',
            'items.*.nama_kemasan' => 'required|string|max:255',
            'items.*.kode_produksi' => 'required|string|max:255',
            'items.*.kondisi_bahan_kemasan' => 'required|in:OK,Tidak OK',
            'items.*.keterangan' => 'nullable|string|max:1000',
        ]);

        $selectedShift = DataShift::findOrFail($request->shift_id);

        if ($user->role !== 'superadmin' && (int) $selectedShift->id_plan !== (int) $user->id_plan) {
            return back()->withErrors(['shift_id' => 'Anda tidak memiliki akses ke shift ini.'])->withInput();
        }

        $effectivePlanId = ($user->role === 'superadmin') ? $selectedShift->id_plan : $user->id_plan;

        foreach ((array) $request->items as $row) {
            PemeriksaanBahanKemas::create([
                'user_id' => $user->id,
                'id_plan' => $effectivePlanId,
                'shift_id' => $request->shift_id,
                'tanggal' => $request->tanggal,
                'jam' => $row['jam'] ?? null,
                'nama_kemasan' => $row['nama_kemasan'] ?? null,
                'kode_produksi' => $row['kode_produksi'] ?? null,
                'kondisi_bahan_kemasan' => $row['kondisi_bahan_kemasan'] ?? null,
                'keterangan' => $row['keterangan'] ?? null,
            ]);
        }

        return redirect()->route('pemeriksaan-bahan-kemas.index')
            ->with('success', 'Data pemeriksaan bahan kemas berhasil ditambahkan.');
    }

    public function show($uuid)
    {
        $item = PemeriksaanBahanKemas::with(['user', 'plan', 'shift'])->where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return view('qc-sistem.pemeriksaan_bahan_kemas.show', compact('item'));
    }

    public function showLogs($uuid)
    {
        $user = Auth::user();

        $item = PemeriksaanBahanKemas::where('uuid', $uuid)
            ->with(['shift', 'plan'])
            ->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403, 'Unauthorized access to logs');
        }

        $logs = PemeriksaanBahanKemasLog::where('pemeriksaan_bahan_kemas_uuid', $uuid)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('qc-sistem.pemeriksaan_bahan_kemas.logs', compact('item', 'logs'));
    }

    public function bulkExportPdf(Request $request)
    {
        try {
            $request->validate([
                'tanggal' => 'nullable|date',
                'shift_id' => 'nullable|integer',
                'kode_form' => 'required|string|max:50',
            ]);

            $user = auth()->user();

            $query = PemeriksaanBahanKemas::with(['plan', 'shift', 'user'])
                ->when($user->role !== 'superadmin', function ($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                });

            if ($request->tanggal) {
                $query->whereDate('tanggal', $request->tanggal);
            }

            if ($request->shift_id) {
                $query->where('shift_id', $request->shift_id);
            }

            $data = $query->orderBy('created_at', 'desc')->get();

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

            $filters = [
                'tanggal' => $request->tanggal,
                'shift_id' => $request->shift_id,
                'kode_form' => $request->kode_form,
            ];

            $kode_form = $request->kode_form;
            $pdf = Pdf::loadView('qc-sistem.pemeriksaan_bahan_kemas.export_pdf', compact('data', 'kode_form', 'filters'));
            $pdf->setPaper('A4', 'landscape');

            $filename = 'pemeriksaan_bahan_kemas_' . date('Y-m-d_H-i-s') . '.pdf';
            return $pdf->download($filename);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor PDF: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function edit($uuid)
    {
        $item = PemeriksaanBahanKemas::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        if ($user->role === 'superadmin') {
            $shifts = DataShift::with('plan')->get();
            $plans = Plan::all();
        } else {
            $shifts = DataShift::with('plan')->where('id_plan', $user->id_plan)->get();
            $plans = Plan::where('id', $user->id_plan)->get();
        }

        return view('qc-sistem.pemeriksaan_bahan_kemas.edit', compact('item', 'shifts', 'plans'));
    }

    public function update(Request $request, $uuid)
    {
        $item = PemeriksaanBahanKemas::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        $request->validate([
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
            'jam' => 'required|date_format:H:i',
            'nama_kemasan' => 'required|string|max:255',
            'kode_produksi' => 'required|string|max:255',
            'kondisi_bahan_kemasan' => 'required|in:OK,Tidak OK',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        $selectedShift = DataShift::findOrFail($request->shift_id);

        if ($user->role !== 'superadmin' && (int) $selectedShift->id_plan !== (int) $user->id_plan) {
            return back()->withErrors(['shift_id' => 'Anda tidak memiliki akses ke shift ini.'])->withInput();
        }

        $effectivePlanId = ($user->role === 'superadmin') ? $selectedShift->id_plan : $user->id_plan;

        $item->update([
            'id_plan' => $effectivePlanId,
            'shift_id' => $request->shift_id,
            'tanggal' => $request->tanggal,
            'jam' => $request->jam,
            'nama_kemasan' => $request->nama_kemasan,
            'kode_produksi' => $request->kode_produksi,
            'kondisi_bahan_kemasan' => $request->kondisi_bahan_kemasan,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('pemeriksaan-bahan-kemas.index')
            ->with('success', 'Data pemeriksaan bahan kemas berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $item = PemeriksaanBahanKemas::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        $item->delete();

        return redirect()->route('pemeriksaan-bahan-kemas.index')
            ->with('success', 'Data pemeriksaan bahan kemas berhasil dihapus.');
    }

    public function approve(Request $request, $uuid)
    {
        try {
            $request->validate([
                'type' => 'required|in:qc,produksi,spv',
            ]);

            $user = Auth::user();
            $userRole = $user->id_role ?? null;
            $type = $request->type;

            $allowedRoles = [
                'qc' => [1, 3, 5],
                'produksi' => [1, 2, 5],
                'spv' => [1, 4, 5],
            ];

            if (!isset($allowedRoles[$type]) || !in_array($userRole, $allowedRoles[$type])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk melakukan approval ini',
                ], 403);
            }

            $item = PemeriksaanBahanKemas::where('uuid', $uuid)->firstOrFail();

            if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }

            $approvalField = 'approved_by_' . $type;
            if ($item->$approvalField) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data sudah disetujui oleh ' . strtoupper($type),
                ], 400);
            }

            if ($type === 'produksi' && !$item->approved_by_qc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data harus disetujui oleh QC terlebih dahulu',
                ], 400);
            }

            if ($type === 'spv' && !$item->approved_by_produksi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data harus disetujui oleh Produksi terlebih dahulu',
                ], 400);
            }

            $updateData = [
                $approvalField => true,
                $type . '_approved_by' => $user->id,
                $type . '_approved_at' => now(),
            ];

            $item->update($updateData);

            PemeriksaanBahanKemasLog::create([
                'pemeriksaan_bahan_kemas_id' => $item->id,
                'pemeriksaan_bahan_kemas_uuid' => $uuid,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role,
                'aksi' => 'approve',
                'field_yang_diubah' => [$approvalField],
                'nilai_lama' => [$approvalField => false],
                'nilai_baru' => [$approvalField => true],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'keterangan' => 'Approved by ' . strtoupper($type),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disetujui oleh ' . strtoupper($type),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui data: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function downloadTemplate()
    {
        $user = auth()->user();
        $filename = 'template_pemeriksaan_bahan_kemas.xlsx';

        return Excel::download(new PemeriksaanBahanKemasTemplateExport($user), $filename);
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $user = auth()->user();

        if (!$user || !$user->id_plan) {
            return redirect()->route('pemeriksaan-bahan-kemas.index')->with('error', 'Plan user tidak ditemukan. Tidak dapat melakukan import.');
        }

        $import = new PemeriksaanBahanKemasImport($user);
        Excel::import($import, $request->file('file'));

        $insertedCount = $import->getInsertedCount();
        $importErrors = $import->getImportErrors();

        if ($insertedCount <= 0) {
            return redirect()->route('pemeriksaan-bahan-kemas.index')
                ->with('warning', 'Tidak ada data valid untuk di-import. Pastikan format kolom dan master Shift sesuai.')
                ->with('import_errors', array_slice($importErrors, 0, 20));
        }

        $successMessage = 'Import data pemeriksaan bahan kemas berhasil. Total: ' . $insertedCount . ' baris.';
        if (!empty($importErrors)) {
            return redirect()->route('pemeriksaan-bahan-kemas.index')
                ->with('success', $successMessage)
                ->with('import_errors', array_slice($importErrors, 0, 20))
                ->with('info', 'Ada beberapa baris yang gagal di-import. Silakan cek detail di bawah.');
        }

        return redirect()->route('pemeriksaan-bahan-kemas.index')->with('success', $successMessage);
    }
}
