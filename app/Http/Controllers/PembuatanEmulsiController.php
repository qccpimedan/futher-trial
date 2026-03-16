<?php

namespace App\Http\Controllers;

use App\Models\PembuatanEmulsi;
use App\Models\PembuatanEmulsiLog;
use App\Models\NomorEmulsi;
use App\Models\TotalPemakaianEmulsi;
use App\Models\JenisProduk;
use App\Models\Plan;
use App\Models\DataShift;
use App\Models\SuhuEmulsi;
use App\Models\JenisEmulsi;
use App\Models\BahanEmulsi;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use PDF;

class PembuatanEmulsiController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $produks = JenisProduk::all();
        $plans = Plan::all();

        if ($user->role === 'superadmin') {
            $data = PembuatanEmulsi::with(['nomor_emulsi', 'total_pemakaian', 'produk', 'plan', 'user', 'bahan_emulsi', 'suhuEmulsi','shift', 'qcApprover', 'produksiApprover', 'spvApprover'])
                ->orderBy('created_at', 'desc')
                ->simplePaginate(5);
        } else {
            $data = PembuatanEmulsi::with(['nomor_emulsi', 'total_pemakaian', 'produk', 'plan', 'user', 'bahan_emulsi', 'suhuEmulsi', 'qcApprover', 'produksiApprover', 'spvApprover'])
                ->where('id_plan', $user->id_plan)
                ->orderBy('created_at', 'desc')
                ->simplePaginate(5);
        }
        return view('qc-sistem.persiapan_bahan_emulsi.index', compact('data'));
    }

    public function create()
    {
        $user = auth()->user();
        if ($user->role == 'superadmin') {
            $jenis_produk = JenisProduk::all();
            $shifts = DataShift::all();
        } else {
            $jenis_produk = JenisProduk::where('id_plan', $user->id_plan)->get();
            // $plans = Plan::where('id', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        }
        return view('qc-sistem.persiapan_bahan_emulsi.create', compact('jenis_produk', 'shifts'));
    }

    public function store(Request $request)
    {
        // Debug: Log request data
        \Log::info('PembuatanEmulsi Store Request:', $request->all());
        // Validasi input untuk multiple proses
        $request->validate([
            'kode_produksi_emulsi' => 'required|string|max:255',
            'nomor_emulsi_id'      => 'required|integer',
            'nama_emulsi_id'       => 'required|integer',
            'hasil_emulsi'         => 'nullable|string',
            'total_pemakaian_id'   => 'required|integer',
            'id_produk'            => 'required|integer',
            'shift_id'             => 'required|exists:data_shift,id',
            'tanggal'              => 'required|date_format:d-m-Y H:i:s',
            'jam'                  => 'required|date_format:H:i',
            'kondisi'              => 'nullable|string',
            
            // Menjadi:
            'bahan_emulsi_id'      => 'nullable|array',
            'bahan_emulsi_id.*'    => 'nullable|array',
            'bahan_emulsi_id.*.*'  => 'nullable|integer',

            'suhu'                 => 'nullable|array',
            'suhu.*'               => 'nullable|array', 
            'suhu.*.*'             => 'nullable|string|in:✔,✘',

            'proses_ke'            => 'nullable|array',
            'proses_ke.*'          => 'nullable|array',
            'proses_ke.*.*'        => 'nullable|integer',
            
            'kode_produksi_bahan'  => 'nullable|array',
            'kode_produksi_bahan.*' => 'nullable|array',
            'kode_produksi_bahan.*.*' => 'nullable|string',

            'berat_rm'             => 'nullable|array',
            'berat_rm.*'           => 'nullable|array',
            'berat_rm.*.*'         => 'nullable|numeric',

            'kondisi_proses'          => 'required|array',  // Suhu per proses (REQUIRED)
            'kondisi_proses.*'        => 'required|string',

            'hasil_emulsi_proses'     => 'required|array',  // Hasil per proses (REQUIRED)
            'hasil_emulsi_proses.*'   => 'required|string|in:✔,✘',
            
        ], [
            // Custom error messages
            'bahan_emulsi_id.required' => 'Bahan emulsi harus dipilih',
            'suhu.required' => 'Kondisi harus dipilih untuk setiap bahan',
            'suhu.*.*.in' => 'Kondisi harus berupa ✔ atau ✘',
            'proses_ke.required' => 'Proses ke harus diisi',
            'kode_produksi_bahan.required' => 'Kode produksi bahan harus diisi',
        ]);

        try {
            \DB::beginTransaction();

            $id_plan = auth()->user()->id_plan;
            
            // Buat record utama
            $emulsi = PembuatanEmulsi::create([
                'uuid'                 => \Illuminate\Support\Str::uuid(),
                'kode_produksi_emulsi' => $request->kode_produksi_emulsi,
                'nomor_emulsi_id'      => $request->nomor_emulsi_id,
                'nama_emulsi_id'       => $request->nama_emulsi_id,
                'hasil_emulsi'         => $request->hasil_emulsi,
                'total_pemakaian_id'   => $request->total_pemakaian_id,
                'kondisi'              => $request->kondisi ?? '',
                'id_produk'            => $request->id_produk,
                'id_plan'              => $id_plan,
                'user_id'              => auth()->id(),
                'shift_id'             => $request->shift_id,
                'tanggal'              => Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s'),
                'jam'                  => $request->jam,
            ]);

            $createdCount = 0;

            // === DEBUG LOGGING START ===
            \Log::info('=== DEBUGGING SUHU EMULSI DATA ===');
            \Log::info('Full request data', $request->all());
            \Log::info('Debug flags', [
                'has_bahan_emulsi_id' => $request->has('bahan_emulsi_id'),
                'has_suhu' => $request->has('suhu'),
                'has_proses_ke' => $request->has('proses_ke'),
                'emulsi_id' => $emulsi->id,
                'bahan_emulsi_data' => $request->bahan_emulsi_id,
                'suhu_data' => $request->suhu,
                'proses_ke_data' => $request->proses_ke
            ]);
            // === DEBUG LOGGING END ===

            // Loop untuk setiap proses
            if ($request->has('bahan_emulsi_id') && is_array($request->bahan_emulsi_id)) {
                \Log::info('Bahan emulsi data found, starting loop');
                
                foreach ($request->bahan_emulsi_id as $prosesIndex => $bahanIds) {
                    \Log::info("Processing proses index: {$prosesIndex}");
                    \Log::info("Bahan IDs for this proses", $bahanIds);
                    
                    if (is_array($bahanIds)) {
                        foreach ($bahanIds as $bahanIndex => $bahanId) {
                            \Log::info("Processing bahan", [
                                'proses' => $prosesIndex,
                                'index' => $bahanIndex,
                                'id' => $bahanId
                            ]);
                            
                            // Check if suhu and proses_ke data exists
                            $suhuExists = isset($request->suhu[$prosesIndex][$bahanIndex]);
                            $prosesKeExists = isset($request->proses_ke[$prosesIndex][$bahanIndex]);
                            
                            \Log::info("Data check", [
                                'suhu_exists' => $suhuExists,
                                'proses_ke_exists' => $prosesKeExists,
                                'suhu_value' => $request->suhu[$prosesIndex][$bahanIndex] ?? 'NOT_FOUND',
                                'proses_ke_value' => $request->proses_ke[$prosesIndex][$bahanIndex] ?? 'NOT_FOUND'
                            ]);
                            
                            if ($request->has('suhu') && is_array($request->suhu) && 
                                $request->has('proses_ke') && is_array($request->proses_ke) &&
                                $suhuExists && $prosesKeExists) {
                                
                                $dataToInsert = [
                                    'bahan_emulsi_id'     => $bahanId,
                                    'pembuatan_emulsi_id' => $emulsi->id,
                                    'suhu'                => $request->suhu[$prosesIndex][$bahanIndex],
                                    'proses_ke'           => $request->proses_ke[$prosesIndex][$bahanIndex],
                                    'kode_produksi_bahan' => $request->kode_produksi_bahan[$prosesIndex][$bahanIndex],
                                    'berat_bahan'         => $request->berat_rm[$prosesIndex][$bahanIndex] ?? null,
                                ];
                                
                                \Log::info("Attempting to create SuhuEmulsi", $dataToInsert);
                                
                                try {
                                    $suhuEmulsi = SuhuEmulsi::create($dataToInsert);
                                    \Log::info("SuhuEmulsi created successfully", ['id' => $suhuEmulsi->id]);
                                    $createdCount++;
                                } catch (\Exception $e) {
                                    \Log::error("Failed to create SuhuEmulsi", [
                                        'error' => $e->getMessage(),
                                        'data' => $dataToInsert
                                    ]);
                                }
                            } else {
                                \Log::warning("Skipping bahan due to missing data", [
                                    'bahan_id' => $bahanId,
                                    'has_suhu' => $request->has('suhu'),
                                    'suhu_is_array' => is_array($request->suhu ?? null),
                                    'has_proses_ke' => $request->has('proses_ke'),
                                    'proses_ke_is_array' => is_array($request->proses_ke ?? null),
                                    'suhu_exists' => $suhuExists,
                                    'proses_ke_exists' => $prosesKeExists
                                ]);
                            }
                        }
                    } else {
                        \Log::warning("BahanIds is not array", ['proses' => $prosesIndex, 'data' => $bahanIds]);
                    }
                }
                
                \Log::info("Total SuhuEmulsi records created: {$createdCount}");
            } else {
                \Log::warning('No bahan_emulsi_id data found or not array');
                \Log::info('Debug info', [
                    'has_bahan_emulsi_id' => $request->has('bahan_emulsi_id'),
                    'is_array' => is_array($request->bahan_emulsi_id ?? null)
                ]);
            }
            // Simpan Suhu dan Hasil Emulsi per proses
            if ($request->has('kondisi_proses') && is_array($request->kondisi_proses)) {
                $kondisiProsesJson = [];
                $hasilProsesJson = [];
                
                foreach ($request->kondisi_proses as $prosesIndex => $suhuProses) {
                    $kondisiProsesJson[$prosesIndex] = $suhuProses;
                    $hasilProsesJson[$prosesIndex] = $request->hasil_emulsi_proses[$prosesIndex] ?? null;
                    
                    \Log::info("Proses ke-" . ($prosesIndex + 1), [
                        'suhu' => $suhuProses,
                        'hasil' => $request->hasil_emulsi_proses[$prosesIndex] ?? null
                    ]);
                }
                
                // Update record emulsi dengan data per proses
                $emulsi->update([
                    'kondisi' => json_encode($kondisiProsesJson),  // Simpan sebagai JSON
                    'hasil_emulsi' => json_encode($hasilProsesJson)  // Simpan sebagai JSON
                ]);
                
                \Log::info('Kondisi dan Hasil per proses berhasil disimpan', [
                    'kondisi' => $kondisiProsesJson,
                    'hasil' => $hasilProsesJson
                ]);
            }
            \Log::info('=== END DEBUGGING SUHU EMULSI ===');
            \DB::commit();

            $totalProses = $request->has('bahan_emulsi_id') && is_array($request->bahan_emulsi_id) 
            ? count($request->bahan_emulsi_id) 
            : 0;
            $message = $totalProses > 0 
            ? "Data berhasil disimpan! Total {$totalProses} proses dengan {$createdCount} detail bahan."
            : "Data berhasil disimpan! (Tanpa detail bahan emulsi)";
            return redirect()->route('persiapan-bahan-emulsi.index')->with('success', $message);

        } catch (\Exception $e) {
            \DB::rollback();
            
            // Debug: Log the actual error
            \Log::error('PembuatanEmulsi Store Error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);     
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error: ' . $e->getMessage() . ' di file: ' . $e->getFile() . ' baris: ' . $e->getLine()]);        }
    }

    // AJAX: Get Total Pemakaian by Produk
    public function getTotalPemakaian(Request $request)
    {
        $data = TotalPemakaianEmulsi::where('id_produk', $request->id_produk)->get(['id', 'total_pemakaian']);
        return response()->json($data);
    }

    // AJAX: Get Nomor Emulsi by Produk, Total Pemakaian
    public function getNomorEmulsi(Request $request)
    {
        $data = NomorEmulsi::where('id_produk', $request->id_produk)
            ->where('total_pemakaian_id', $request->total_pemakaian_id)
            ->get(['id', 'nomor_emulsi']);
        return response()->json($data);
    }

    public function inputPersiapanBahan()
    {
        $user = auth()->user();
        if ($user->role === 'superadmin') {
            $jenis_produk = JenisProduk::all();
        } else {
            $jenis_produk = JenisProduk::where('id_plan', $user->id_plan)->get();
        }
        $shifts = DataShift::all();
        // Data lain jika perlu
        return view('qc-sistem.input_persiapan_bahan', compact('jenis_produk'));
    }

    public function getTotalPemakaianByProduk($id_produk)
    {
        $user = auth()->user();
        $query = TotalPemakaianEmulsi::where('id_produk', $id_produk);
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        $data = $query->get(['id', 'total_pemakaian']);
        return response()->json($data);
    }

    public function getEmulsiByProduk($id_produk)
    {
        $user = auth()->user();
        $query = JenisEmulsi::where('id_produk', $id_produk);
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        $data = $query->get(['id', 'nama_emulsi']);
        return response()->json($data);
    }

    public function getTotalPemakaianByEmulsiProduk($id_produk, $nama_emulsi_id)
    {
        $user = auth()->user();
        $query = TotalPemakaianEmulsi::where('id_produk', $id_produk)
            ->where('nama_emulsi_id', $nama_emulsi_id);
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        $data = $query->get(['id', 'total_pemakaian']);
        return response()->json($data);
    }

    public function getNomorEmulsiByTotalPemakaian($total_pemakaian_id)
    {
        $data = NomorEmulsi::where('total_pemakaian_id', $total_pemakaian_id)->get(['id', 'nomor_emulsi']);
        return response()->json($data);
    }

    public function getBahanEmulsiByNomorEmulsi($nomor_emulsi_id)
    {
        // Ambil data bahan emulsi berdasarkan nomor_emulsi_id
        $bahan = BahanEmulsi::where('nomor_emulsi_id', $nomor_emulsi_id)->get(['id', 'nama_rm', 'berat_rm']);
        return response()->json($bahan);
    }

    public function edit($uuid)
    {
        $item = PembuatanEmulsi::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        if ($user->role == 'superadmin') {
            $produks = JenisProduk::all();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
        }
        if ($user->role === 'superadmin') {
            $shifts = $item->id_plan
                ? DataShift::where('id_plan', $item->id_plan)->orderBy('shift')->get()
                : DataShift::orderBy('shift')->get();
        } else {
            $shifts = DataShift::where('id_plan', $user->id_plan)->orderBy('shift')->get();
        }
        // Jika ingin dropdown emulsi, total pemakaian, dsb, tambahkan di sini
        return view('qc-sistem.persiapan_bahan_emulsi.edit', compact('item', 'produks','shifts'));
    }
    public function update(Request $request, $uuid)
    {
        $item = PembuatanEmulsi::where('uuid', $uuid)->firstOrFail();

        // Simpan Suhu dan Hasil Emulsi per proses sebagai JSON
        $kondisiJson = null;
        $hasilJson = null;
        
        if ($request->has('kondisi_proses') && is_array($request->kondisi_proses)) {
            $kondisiJson = json_encode($request->kondisi_proses);
            $hasilJson = json_encode($request->hasil_emulsi_proses ?? []);
        }

        $item->update([
            'kode_produksi_emulsi' => $request->kode_produksi_emulsi,
            'hasil_emulsi'         => $hasilJson, // Simpan sebagai JSON
            'kondisi'              => $kondisiJson, // Simpan sebagai JSON
            'shift_id'             => $request->shift_id,
            'tanggal'              => Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s'),
        ]);

        // Update suhu, kode_produksi_bahan, dan berat_bahan per bahan di tabel suhu_emulsi
        if ($request->has('suhu_emulsi_id') && $request->has('suhu')) {
            foreach ($request->suhu_emulsi_id as $idx => $suhuId) {
                $updateData = ['suhu' => $request->suhu[$idx]];
                
                // Tambahkan kode_produksi_bahan jika ada
                if ($request->has('kode_produksi_bahan') && isset($request->kode_produksi_bahan[$idx])) {
                    $updateData['kode_produksi_bahan'] = $request->kode_produksi_bahan[$idx];
                }
                
                // Tambahkan berat_bahan jika ada (dari edit form)
                if ($request->has('berat_rm_edit')) {
                    // Cari index yang sesuai dalam array berat_rm_edit
                    foreach ($request->berat_rm_edit as $prosesIdx => $beratArray) {
                        if (is_array($beratArray) && isset($beratArray[$idx])) {
                            $updateData['berat_bahan'] = $beratArray[$idx];
                            break;
                        }
                    }
                }
                
                SuhuEmulsi::where('id', $suhuId)->update($updateData);
            }
        }
        
        return redirect()->route('persiapan-bahan-emulsi.index')->with('success', 'Data berhasil diupdate');
    }

    public function show($uuid)
    {
        $data = PembuatanEmulsi::where('uuid', $uuid)
            ->with([
                'plan',
                'produk',
                'suhuEmulsi.bahanEmulsi',
                'shift',
                'nama_emulsi',
                'nomor_emulsi',
                'user'
            ])
            ->firstOrFail();

        $user = auth()->user();
        
        // Check access permission
        if ($user->role !== 'superadmin' && $data->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data ini.');
        }

        return view('qc-sistem.persiapan_bahan_emulsi.show', compact('data'));
    }

    public function destroy($uuid)
    {
        $item = PembuatanEmulsi::where('uuid', $uuid)->firstOrFail();
        $item->delete();

        return redirect()->route('persiapan-bahan-emulsi.index')->with('success', 'Data berhasil dihapus');
    }

    /**
     * Export PDF dengan filter
     */
    public function exportPdf(Request $request)
    {
        // Debug logging
        \Log::info('PembuatanEmulsiController::exportPdf called', [
            'request_data' => $request->all(),
            'controller' => 'PembuatanEmulsiController'
        ]);
        
        $user = auth()->user();
        
        $query = PembuatanEmulsi::with([
            'plan',
            'produk', 
            'shift',
            'suhuEmulsi.bahanEmulsi',
            'nama_emulsi',
            'nomor_emulsi',
            'qcApprover',
            'produksiApprover',
            'spvApprover'
        ])
        ->when($user->role !== 'superadmin', function($q) use ($user) {
            $q->where('id_plan', $user->id_plan);
        });
        

        // Handle individual record export with UUID
        if ($request->has('uuid') && $request->uuid) {
            $query->where('uuid', $request->uuid);
            $filename = 'persiapan_bahan_emulsi_' . $request->uuid . '_' . date('Y-m-d_H-i-s') . '.pdf';
            
            // Update kode_form if provided
            if ($request->has('kode_form') && $request->kode_form) {
                $item = PembuatanEmulsi::where('uuid', $request->uuid)->first();
                if ($item) {
                    $item->update(['kode_form' => $request->kode_form]);
                }
            }
        } else {
            // Handle bulk export with filters
            if ($request->tanggal) {
                $query->whereDate('tanggal', $request->tanggal);
            }
            
            if ($request->shift_id) {
                $query->where('shift_id', $request->shift_id);
            }
            
            if ($request->id_produk) {
                $query->whereHas('produk', function($q) use ($request) {
                    $q->where('id', $request->id_produk);
                });
            }
            
            $filename = 'persiapan_bahan_emulsi_bulk_' . date('Y-m-d_H-i-s') . '.pdf';
        }
        
        $data = $query->orderBy('created_at', 'desc')->get();

        \Log::info('Data retrieved for PDF export', [
            'count' => $data->count(),
            'first_item' => $data->first() ? $data->first()->toArray() : null
        ]);

        if ($data->isEmpty()) {
            \Log::warning('No data found for PDF export');
            $errorMessage = 'Tidak ada data yang sesuai dengan filter yang dipilih.';
            $filterInfo = [];
            
            if ($request->tanggal) {
                $filterInfo[] = 'Tanggal: ' . \Carbon\Carbon::parse($request->tanggal)->format('d-m-Y');
            }
            if ($request->shift_id) {
                $shift = DataShift::find($request->shift_id);
                $filterInfo[] = 'Shift: ' . ($shift ? $shift->shift : 'Unknown');
            }
            if ($request->id_produk) {
                $produk = JenisProduk::find($request->id_produk);
                $filterInfo[] = 'Produk: ' . ($produk ? $produk->nama_produk : 'Unknown');
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

        // Update semua data dengan kode form
        if ($data->isNotEmpty()) {
            $data->each(function($item) use ($request) {
                $item->update([
                    'kode_form' => $request->kode_form
                ]);
            });
        }

        // Pass kode_form to view
        $kode_form = $request->kode_form ?? '';
        $pdf = PDF::loadView('qc-sistem.persiapan_bahan_emulsi.export_pdf', compact('data', 'kode_form'));
        $pdf->setPaper('A4', 'landscape');
        
        \Log::info('PDF generated successfully', ['filename' => $filename]);
        
        return $pdf->download($filename);
    }

    /**
     * Menampilkan riwayat log perubahan data
     */
    public function showLogs($uuid)
    {
        $item = PembuatanEmulsi::where('uuid', $uuid)->firstOrFail();
        
        $logs = PembuatanEmulsiLog::where('pembuatan_emulsi_id', $item->id)
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->paginate(5);
        
        return view('qc-sistem.persiapan_bahan_emulsi.logs', compact('item', 'logs'));
    }

    /**
     * API untuk mendapatkan log dalam format JSON (untuk AJAX)
     */
    public function getLogsJson($uuid)
    {
        $item = PembuatanEmulsi::where('uuid', $uuid)->firstOrFail();
        
        $logs = PembuatanEmulsiLog::where('pembuatan_emulsi_id', $item->id)
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

    public function bulkExportPdf(Request $request)
    {
        try {
            $user = auth()->user();
            
            $query = PembuatanEmulsi::with([
                'plan',
                'produk', 
                'shift',
                'suhuEmulsi.bahanEmulsi',
                'nama_emulsi',
                'nomor_emulsi',
                'qcApprover',
                'produksiApprover',
                'spvApprover'
            ])
            ->when($user->role !== 'superadmin', function($q) use ($user) {
                $q->where('id_plan', $user->id_plan);
            });

            // Apply filters
            if ($request->filled('tanggal')) {
                $query->whereDate('created_at', $request->tanggal);
            }

            if ($request->filled('shift_id')) {
                $query->where('shift_id', $request->shift_id);
            }

            if ($request->filled('id_produk')) {
                $query->where('id_produk', $request->id_produk);
            }

            $data = $query->orderBy('created_at', 'desc')->get();

            if ($data->isEmpty()) {
                \Log::warning('No data found for PDF export');
                $errorMessage = 'Tidak ada data yang sesuai dengan filter yang dipilih.';
                $filterInfo = [];
                
                if ($request->tanggal) {
                    $filterInfo[] = 'Tanggal: ' . \Carbon\Carbon::parse($request->tanggal)->format('d-m-Y');
                }
                if ($request->shift_id) {
                    $shift = DataShift::find($request->shift_id);
                    $filterInfo[] = 'Shift: ' . ($shift ? $shift->shift : 'Unknown');
                }
                if ($request->id_produk) {
                    $produk = JenisProduk::find($request->id_produk);
                    $filterInfo[] = 'Produk: ' . ($produk ? $produk->nama_produk : 'Unknown');
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

            // Update semua data dengan kode form
            $data->each(function($item) use ($request) {
                $item->update([
                    'kode_form' => $request->kode_form
                ]);
            });

            // Pass kode_form to view
            $kode_form = $request->kode_form ?? '';
            $filename = 'persiapan_bahan_emulsi_bulk_' . date('Y-m-d_H-i-s') . '.pdf';

            $pdf = PDF::loadView('qc-sistem.persiapan_bahan_emulsi.export_pdf', compact('data', 'kode_form'));
            $pdf->setPaper('A4', 'landscape');
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            \Log::error('Bulk PDF generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }

    /**
     * AJAX search method untuk pencarian data
     */
    public function searchAjax(Request $request)
    {
        try {
            $user = auth()->user();
            $search = $request->get('search', '');
            $perPage = $request->get('per_page', 5);
            
            // Start with basic query first
            $query = PembuatanEmulsi::with([
                'plan',
                'produk',
                'suhuEmulsi.bahanEmulsi',
                'user',
                'shift',
                'nama_emulsi',
                'nomor_emulsi',
                'qcApprover',
                'produksiApprover',
                'spvApprover'
            ]);            
            // Apply user filter if not superadmin
            if ($user && $user->role !== 'superadmin') {
                $query->where('id_plan', $user->id_plan);
            }
            
            // Add search functionality with proper grouping
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('kode_produksi_emulsi', 'like', "%{$search}%")
                      ->orWhereHas('produk', function($subq) use ($search) {
                          $subq->where('nama_produk', 'like', "%{$search}%");
                      })
                      ->orWhereHas('nama_emulsi', function($subq) use ($search) {
                          $subq->where('nama_emulsi', 'like', "%{$search}%");
                      });
                });
            }
            
            $data = $query->orderBy('created_at', 'desc')->paginate($perPage);
            
            return response()->json([
                'data' => $data->items(),
                'pagination' => [
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                    'from' => $data->firstItem(),
                    'to' => $data->lastItem(),
                    'has_pages' => $data->hasPages()
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function approve(Request $request, $uuid)
    {
        try {
            $user = auth()->user();
            $type = $request->input('type'); // qc, produksi, or spv
            
            // Find the record
            $pembuatanEmulsi = PembuatanEmulsi::where('uuid', $uuid)->firstOrFail();
            
            // Role-based validation
            $userRole = $user->id_role;
            
            // Check if user has permission for this approval type
            $allowedRoles = [
                'qc' => [1, 3, 5], // Role 1, 3, 5 can approve QC
                'produksi' => [1, 2, 5], // Role 1, 2, 5 can approve Produksi
                'spv' => [1, 4, 5] // Role 1, 4, 5 can approve SPV
            ];
            
            if (!in_array($userRole, $allowedRoles[$type] ?? [])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk melakukan persetujuan ini.'
                ], 403);
            }
            
            // Sequential approval validation
            if ($type === 'produksi' && !$pembuatanEmulsi->approved_by_qc) {
                return response()->json([
                    'success' => false,
                    'message' => 'QC harus menyetujui terlebih dahulu sebelum Produksi.'
                ], 400);
            }
            
            if ($type === 'spv' && !$pembuatanEmulsi->approved_by_produksi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produksi harus menyetujui terlebih dahulu sebelum SPV.'
                ], 400);
            }
            
            // Check if already approved
            $approvalField = "approved_by_{$type}";
            if ($pembuatanEmulsi->$approvalField) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data sudah disetujui sebelumnya.'
                ], 400);
            }
            
            // Update approval
            $pembuatanEmulsi->update([
                $approvalField => true,
                "{$type}_approved_by" => $user->id,
                "{$type}_approved_at" => now()
            ]);
            
            // Log the approval activity
            PembuatanEmulsiLog::create([
                'pembuatan_emulsi_id' => $pembuatanEmulsi->id,
                'pembuatan_emulsi_uuid' => $pembuatanEmulsi->uuid,
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
                'data' => $pembuatanEmulsi->fresh()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
