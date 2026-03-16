<?php

namespace App\Http\Controllers;

use App\Models\DataShift;
use App\Models\JenisProduk;
use App\Models\PembuatanSample;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\PembuatanSampleLog;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PembuatanSampleTemplateExport;
use App\Imports\PembuatanSampleImport;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Str;
class PembuatanSampleController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $search = trim((string) request('search', ''));
        $perPage = request('per_page', 10);
        
        $query = PembuatanSample::with(['plan', 'produk', 'shift', 'createdBy', 'qcApprover', 'produksiApprover', 'spvApprover'])
            ->orderBy('created_at', 'desc');

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('kode_produksi', 'like', "%{$search}%")
                    ->orWhere('jenis_sample', 'like', "%{$search}%")
                    ->orWhereHas('produk', function ($qp) use ($search) {
                        $qp->where('nama_produk', 'like', "%{$search}%");
                    });
            });
        }

        $data = $query->paginate($perPage);
        $data->appends(['search' => $search, 'per_page' => $perPage]);
        
        return view('qc-sistem.pembuatan_sample.index', compact('data', 'search', 'perPage'));
    }

    public function create()
    {
        $user = Auth::user();

        if ($user->role == 'superadmin') {
            $produks = JenisProduk::all();
            $shifts = DataShift::all();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        }

        return view('qc-sistem.pembuatan_sample.create', compact('produks', 'shifts'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $isSpecialRole = ($user->id_role == 2 || $user->id_role == 3);
        
        // Validasi berbeda berdasarkan role
        if ($isSpecialRole) {
            $validatedData = $request->validate([
                'id_produk' => 'required|exists:jenis_produk,id',
                'id_shift' => 'required|exists:data_shift,id',
                'kode_produksi' => 'required|string|max:255',
                'tanggal' => 'required|date_format:d-m-Y', // Hanya validasi format tanggal saja
                'jam' => 'required',
                'tanggal_expired' => 'required|date',
                'jumlah' => 'required|integer',
                'berat' => 'required|numeric',
                'berat_sampling' => 'nullable|numeric',
                'jenis_sample' => 'required|string|max:255',
            ]);
        } else {
            $validatedData = $request->validate([
                'id_produk' => 'required|exists:jenis_produk,id',
                'id_shift' => 'required|exists:data_shift,id',
                'kode_produksi' => 'required|string|max:255',
                'tanggal' => 'required|date_format:d-m-Y H:i:s',
                'jam' => 'required',
                'tanggal_expired' => 'required|date',
                'jumlah' => 'required|integer',
                'berat' => 'required|numeric',
                'berat_sampling' => 'nullable|numeric',
                'jenis_sample' => 'required|string|max:255',
            ]);
        }

        // Transform the date format
        if ($isSpecialRole) {
            // Untuk user dengan role 2 atau 3, gunakan format tanggal dari request tapi waktu dari now()
            $dateOnly = \Carbon\Carbon::createFromFormat('d-m-Y', $request->tanggal)->format('Y-m-d');
            $timeNow = now()->format('H:i:s');
            $validatedData['tanggal'] = $dateOnly . ' ' . $timeNow;
        } else {
            // Untuk user lain, gunakan format tanggal dan waktu dari request
            $validatedData['tanggal'] = Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s');
        }

        $validatedData['id_plan'] = Auth::user()->id_plan;
        $validatedData['created_by'] = Auth::user()->id;
        $validatedData['jam'] = $request->jam;

        PembuatanSample::create($validatedData);


        return redirect()->route('pembuatan-sample.index')->with('success', 'Data Sample berhasil ditambahkan.');
    }

    public function edit($uuid)
    {
        $user = Auth::user();

        if ($user->role == 'superadmin') {
            $produks = JenisProduk::all();
            $shifts = DataShift::all();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        }
        $pembuatanSample = PembuatanSample::where('uuid', $uuid)->firstOrFail();
        return view('qc-sistem.pembuatan_sample.edit', compact('pembuatanSample', 'produks', 'shifts'));
    }

    public function update(Request $request, $uuid)
    {
        $pembuatanSample = PembuatanSample::where('uuid', $uuid)->firstOrFail();
        $validatedData = $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'id_shift' => 'required|exists:data_shift,id',
            'kode_produksi' => 'required|string|max:255',
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'tanggal_expired' => 'required|date',
            'jumlah' => 'required|integer',
            'berat' => 'required|numeric',
            'berat_sampling' => 'nullable|numeric',
            'jenis_sample' => 'required|string|max:255',
        ]);

        $validatedData['tanggal'] = Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s');

        $pembuatanSample->update($validatedData);

        return redirect()->route('pembuatan-sample.index')->with('success', 'Data Sample berhasil diperbarui.');
    }

    public function destroy($uuid)
    {   
        $pembuatanSample = PembuatanSample::where('uuid', $uuid)->firstOrFail();
        $pembuatanSample->delete();
        return redirect()->route('pembuatan-sample.index')->with('success', 'Data Sample berhasil dihapus.');
    }
    /**
     * Tampilkan halaman logs untuk pembuatan sample
     */
    public function showLogs($uuid)
    {
        $item = PembuatanSample::where('uuid', $uuid)->firstOrFail();
        
        $logs = PembuatanSampleLog::where('pembuatan_sample_id', $item->id)
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->simplePaginate(5);
        
        return view('qc-sistem.pembuatan_sample.logs', compact('item', 'logs'));
    }

    /**
     * Get logs data untuk DataTables (jika diperlukan)
     */
    public function getLogsJson($uuid)
    {
        $pembuatanSample = PembuatanSample::where('uuid', $uuid)->firstOrFail();
        
        $logs = PembuatanSampleLog::where('pembuatan_sample_uuid', $uuid)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($log) {
                return [
                    'tanggal' => $log->created_at->format('d/m/Y H:i:s'),
                    'user' => $log->user_name,
                    'field' => $log->nama_field,
                    'perubahan' => $log->deskripsi_perubahan,
                    'ip_address' => $log->ip_address
                ];
            });

        return response()->json(['data' => $logs]);
    }

    public function bulkExportPdf(Request $request)
    {
        $request->validate([
            'tanggal' => 'nullable|date',
            'id_shift' => 'nullable|exists:data_shift,id',
            'id_produk' => 'nullable|exists:jenis_produk,id',
            'kode_form' => 'required|string|max:255'
        ]);

        $user = Auth::user();
        $query = PembuatanSample::with(['plan', 'produk', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover']);

        // Filter berdasarkan role user
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        // Apply filters
        if ($request->tanggal) {
            $tanggal = Carbon::parse($request->tanggal);
            $query->whereDate('tanggal', $tanggal);
        }

        if ($request->id_shift) {
            $query->where('id_shift', $request->id_shift);
        }

        if ($request->id_produk) {
            $query->where('id_produk', $request->id_produk);
        }

        $data = $query->orderBy('jenis_sample', 'asc')->orderBy('tanggal', 'asc')->orderBy('created_at', 'asc')->get();
        
        // Group data by jenis_sample for PDF display
        $groupedData = $data->groupBy('jenis_sample');

        // Update kode_form for all filtered data (only from modal)
        if (!$data->isEmpty()) {
            $dataIds = $data->pluck('id')->toArray();
            PembuatanSample::whereIn('id', $dataIds)->update(['kode_form' => $request->kode_form]);
            
            // Refresh data to include updated kode_form
            $data = $data->map(function ($item) use ($request) {
                $item->kode_form = $request->kode_form;
                return $item;
            });
        }

        // Check if no data found
        if ($data->isEmpty()) {
            $html = '<!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>Data Tidak Ditemukan</title>
                <style>
                    body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
                    .no-data { color: #666; font-size: 18px; }
                </style>
            </head>
            <body>
                <div class="no-data">
                    <h2>Tidak Ada Data Ditemukan</h2>
                    <p>Tidak ada data Pembuatan Sample yang sesuai dengan filter yang dipilih.</p>
                    <p><strong>Filter:</strong></p>
                    <p>Tanggal: ' . ($request->tanggal ? Carbon::parse($request->tanggal)->format('d-m-Y') : 'Semua') . '</p>
                    <p>Shift: ' . ($request->id_shift ? DataShift::find($request->id_shift)->shift ?? 'Tidak Diketahui' : 'Semua') . '</p>
                    <p>Produk: ' . ($request->id_produk ? JenisProduk::find($request->id_produk)->nama_produk ?? 'Tidak Diketahui' : 'Semua') . '</p>
                    <p>Kode Form: ' . $request->kode_form . '</p>
                </div>
            </body>
            </html>';
            
            return response($html)->header('Content-Type', 'text/html');
        }

        // Prepare filter info for PDF
        $filterInfo = [
            'tanggal' => $request->tanggal ? Carbon::parse($request->tanggal)->format('d-m-Y') : 'Semua Tanggal',
            'shift' => $request->id_shift ? DataShift::find($request->id_shift)->shift ?? 'Tidak Diketahui' : 'Semua Shift',
            'produk' => $request->id_produk ? JenisProduk::find($request->id_produk)->nama_produk ?? 'Tidak Diketahui' : 'Semua Produk',
            'kode_form' => $request->kode_form
        ];

        // Generate PDF
        $pdf = PDF::loadView('qc-sistem.pembuatan_sample.export_pdf', compact('data', 'groupedData', 'filterInfo'));
        $pdf->setPaper('A4', 'landscape');

        $safeKodeForm = (string) ($filterInfo['kode_form'] ?? '');
        $safeKodeForm = str_replace(['/', '\\', ':'], '-', $safeKodeForm);
        $safeKodeForm = trim($safeKodeForm);
        
        $filename = 'Pembuatan_Sample_' . $safeKodeForm . '_' . date('Y-m-d_H-i-s') . '.pdf';
        
        return $pdf->download($filename);
    }

    public function downloadTemplate()
    {
        $user = Auth::user();
        $filename = 'template_pembuatan_sample.xlsx';

        return Excel::download(new PembuatanSampleTemplateExport($user), $filename);
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $user = Auth::user();

        if (!$user || !$user->id_plan) {
            return redirect()->route('pembuatan-sample.index')->with('error', 'Plan user tidak ditemukan. Tidak dapat melakukan import.');
        }

        $import = new PembuatanSampleImport($user);
        Excel::import($import, $request->file('file'));

        $insertedCount = $import->getInsertedCount();
        $importErrors = $import->getImportErrors();

        if ($insertedCount <= 0) {
            return redirect()->route('pembuatan-sample.index')
                ->with('warning', 'Tidak ada data valid untuk di-import. Pastikan format kolom dan master Shift/Produk sesuai.')
                ->with('import_errors', array_slice($importErrors, 0, 20));
        }

        $successMessage = 'Import data pembuatan sample berhasil. Total: ' . $insertedCount . ' baris.';
        if (!empty($importErrors)) {
            return redirect()->route('pembuatan-sample.index')
                ->with('success', $successMessage)
                ->with('import_errors', array_slice($importErrors, 0, 20))
                ->with('info', 'Ada beberapa baris yang gagal di-import. Silakan cek detail di bawah.');
        }

        return redirect()->route('pembuatan-sample.index')->with('success', $successMessage);
    }

    public function approve(Request $request, $uuid)
    {
        $request->validate([
            'type' => 'required|in:qc,produksi,spv'
        ]);

        $pembuatanSample = PembuatanSample::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        $type = $request->type;

        // Role-based access control following Seasoning pattern
        $allowedRoles = [
            'qc' => [1, 3, 5], // Role 1&5 (all buttons), Role 3 (QC only)
            'produksi' => [1, 2, 5], // Role 1&5 (all buttons), Role 2 (produksi only)
            'spv' => [1, 4, 5] // Role 1&5 (all buttons), Role 4 (SPV only)
        ];

        if (!in_array($user->id_role, $allowedRoles[$type])) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk melakukan approval ini.'
            ], 403);
        }

        // Sequential approval validation (QC → Produksi → SPV)
        if ($type === 'produksi' && !$pembuatanSample->approved_by_qc) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh QC terlebih dahulu.'
            ], 400);
        }

        if ($type === 'spv' && !$pembuatanSample->approved_by_produksi) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh Produksi terlebih dahulu.'
            ], 400);
        }

        // Check if already approved
        $approvalField = "approved_by_{$type}";
        if ($pembuatanSample->$approvalField) {
            return response()->json([
                'success' => false,
                'message' => 'Data sudah disetujui sebelumnya.'
            ], 400);
        }

        // Update approval
        $pembuatanSample->update([
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
