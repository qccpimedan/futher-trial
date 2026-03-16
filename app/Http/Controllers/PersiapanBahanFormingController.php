<?php

namespace App\Http\Controllers;

use App\Models\PersiapanBahanForming;
use App\Models\BahanForming;
use App\Models\SuhuForming;
use App\Models\JenisProduk;
use App\Models\DataShift;
use App\Models\AktualSuhuAdonan;
use App\Models\SuhuAdonan;
use App\Models\PersiapanBahanNonForming;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PersiapanBahanFormingExport;
use App\Models\PersiapanBahanFormingLog;

class PersiapanBahanFormingController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $search = $request->get('search');
        $perPage = $request->get('per_page', 5);

        $page = (int) $request->get('page', 1);
        $perPage = (int) $perPage;
        if ($perPage <= 0) {
            $perPage = 5;
        }

        $query = PersiapanBahanForming::with([
            'plan',
            'formula.produk',
            'suhuForming.bahanForming',
            'shift',
            'aktualSuhuAdonan',
            'suhuAdonan',
            'user',
            'qcApprover',
            'produksiApprover',
            'spvApprover'
        ])
        ->when($user->role !== 'superadmin', function($q) use ($user) {
            $q->where('plan_id', $user->id_plan);
        });

        // Add search functionality
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('kode_produksi_emulsi', 'like', "%{$search}%")
                  ->orWhere('kode_produksi_emulsi_oil', 'like', "%{$search}%")
                  ->orWhere('kondisi', 'like', "%{$search}%")
                  ->orWhere('catatan', 'like', "%{$search}%")
                  ->orWhereHas('plan', function($q) use ($search) {
                      $q->where('nama_plan', 'like', "%{$search}%");
                  })
                  ->orWhereHas('formula.produk', function($q) use ($search) {
                      $q->where('nama_produk', 'like', "%{$search}%");
                  })
                  ->orWhereHas('formula', function($q) use ($search) {
                      $q->where('nomor_formula', 'like', "%{$search}%");
                  })
                  ->orWhereHas('suhuForming.bahanForming', function($q) use ($search) {
                      $q->where('nama_rm', 'like', "%{$search}%");
                  });
            });
        }

        // Opsi A: tampil 1 baris per UUID (forming)
        $dataForming = $query->orderBy('created_at', 'desc')->limit(500)->get();

        $queryNonForming = PersiapanBahanNonForming::with([
            'formulaNonForming.produk',
            'shift',
            'suhuAdonan',
            'user',
        ])
        ->when($user->role !== 'superadmin', function ($q) use ($user) {
            $q->where('plan_id', $user->id_plan);
        });

        if ($search) {
            $queryNonForming->where(function ($q) use ($search) {
                $q->where('kode_produksi', 'like', "%{$search}%")
                  ->orWhere('kondisi', 'like', "%{$search}%")
                  ->orWhere('catatan', 'like', "%{$search}%")
                  ->orWhereHas('formulaNonForming', function ($q) use ($search) {
                      $q->where('nomor_formula', 'like', "%{$search}%");
                  })
                  ->orWhereHas('formulaNonForming.produk', function ($q) use ($search) {
                      $q->where('nama_produk', 'like', "%{$search}%");
                  });
            });
        }

        $dataNonForming = $queryNonForming->orderBy('created_at', 'desc')->limit(500)->get();

        $formingRecords = $dataForming->map(function ($item) {
            return (object) [
                'jenis' => 'Forming',
                'uuid' => $item->uuid,
                'shift' => $item->shift->shift ?? '-',
                'shift_id' => $item->shift_id,
                'tanggal' => $item->tanggal,
                'jam' => $item->jam,
                'nama_produk' => $item->formula->produk->nama_produk ?? '-',
                'kode_produksi' => $item->kode_produksi_emulsi ?? '-',
                'nomor_formula' => $item->formula->nomor_formula ?? '-',
                'kondisi' => $item->kondisi,
                'catatan' => $item->catatan,
                'dibuat_oleh' => $item->user->name ?? '-',
                'created_at' => $item->created_at,
            ];
        });

        $nonFormingRecords = $dataNonForming->map(function ($item) {
            return (object) [
                'jenis' => 'Non Forming',
                'uuid' => $item->uuid,
                'shift' => $item->shift->shift ?? '-',
                'shift_id' => $item->shift_id,
                'tanggal' => $item->tanggal,
                'jam' => $item->jam,
                'nama_produk' => $item->formulaNonForming->produk->nama_produk ?? '-',
                'kode_produksi' => $item->kode_produksi ?? '-',
                'nomor_formula' => $item->formulaNonForming->nomor_formula ?? '-',
                'kondisi' => $item->kondisi,
                'catatan' => $item->catatan,
                'dibuat_oleh' => $item->user->name ?? '-',
                'created_at' => $item->created_at,
            ];
        });

        $merged = $formingRecords
            ->concat($nonFormingRecords)
            ->sortByDesc(function ($r) {
                return $r->created_at;
            })
            ->values();

        $total = $merged->count();
        $items = $merged->slice(($page - 1) * $perPage, $perPage)->values();

        $records = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('qc-sistem.persiapan_bahan_forming.index', compact('records', 'search', 'perPage'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();

        if ($user->role === 'superadmin') {
            $jenis_produk = JenisProduk::all();
            $list_formula = \App\Models\NomorFormula::all();
            $produks = JenisProduk::all();
            $shifts = DataShift::all();
            $suhu_adonan = SuhuAdonan::all();
        } else {
            $jenis_produk = JenisProduk::where('id_plan', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $list_formula = \App\Models\NomorFormula::where('id_plan', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $suhu_adonan = SuhuAdonan::where('id_plan', $user->id_plan)->get();
        }

        $id_formula = $request->input('id_formula');
        $selectedProdukId = $request->query('id_produk');
        $bahan_forming = collect();
        if ($id_formula) {
            $bahan_forming = BahanForming::with('formula')
                ->where('id_formula', $id_formula)
                ->get();
        }

        return view('qc-sistem.persiapan_bahan_forming.create', [
            'bahan' => $bahan_forming,
            'id_formula' => $id_formula,
            'list_formula' => $list_formula,
            'jenis_produk' => $jenis_produk,
            'produks' => $produks,
            'id_plan' => $user->id_plan,
            'shifts' => $shifts,
            'suhu_adonan' => $suhu_adonan,
            'selectedProdukId' => $selectedProdukId,
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $data = $request->validate([
            'id_formula' => 'required|integer|exists:nomor_formula,id',
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'jam' => 'required|date_format:H:i',
            'kode_produksi_emulsi' => 'nullable|string',
            'kondisi' => 'nullable|string',
            'kode_produksi_emulsi_oil' => 'nullable|array',
            'kode_produksi_emulsi_oil.*' => 'nullable|string',
            'rework' => 'nullable|string',
            'catatan' => 'nullable|string',
            'id_bahan_forming' => 'required|array',
            'suhu' => 'required|array',
            'kode_produksi_bahan' => 'required|array',
            'id_suhu_adonan' => 'required|exists:suhu_adonan,id',
            'waktu_mulai_mixing' => 'nullable|string',
            'waktu_selesai_mixing' => 'nullable|string',
            'aktual_suhu_1' => 'nullable|numeric',
            'aktual_suhu_2' => 'nullable|numeric',
            'aktual_suhu_3' => 'nullable|numeric',
            'aktual_suhu_4' => 'nullable|numeric',
            'aktual_suhu_5' => 'nullable|numeric',
        ]);
        $kodeEmulsiOil = [];
        if (isset($data['kode_produksi_emulsi_oil']) && is_array($data['kode_produksi_emulsi_oil'])) {
            $kodeEmulsiOil = array_values(array_filter($data['kode_produksi_emulsi_oil'], function($value) {
                return !empty(trim($value));
            }));
        }

        $persiapan = PersiapanBahanForming::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'plan_id' => $user->id_plan,
            'user_id' => $user->id,
            'id_formula' => $data['id_formula'],
            'shift_id' => $data['shift_id'],
            'tanggal' => Carbon::createFromFormat('d-m-Y H:i:s', $data['tanggal'])->format('Y-m-d H:i:s'),
            'jam' => $data['jam'],
            'kode_produksi_emulsi' => $data['kode_produksi_emulsi'] ?? null,
            'kondisi' => $data['kondisi'] ?? null,
            'kode_produksi_emulsi_oil' => $kodeEmulsiOil, // GANTI: dari $data['kode_produksi_emulsi_oil'] ?? null
            'rework' => $data['rework'] ?? null,
            'catatan' => $data['catatan'] ?? null,
            'id_suhu_adonan' => $data['id_suhu_adonan'],
            'waktu_mulai_mixing' => $data['waktu_mulai_mixing'] ?? null,
            'waktu_selesai_mixing' => $data['waktu_selesai_mixing'] ?? null,
        ]);

        foreach ($request->id_bahan_forming as $idx => $bahanId) {
            SuhuForming::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'id_persiapan_bahan_forming' => $persiapan->id,
                'id_bahan_forming' => $bahanId,
                'suhu' => $request->suhu[$idx],
                'kode_produksi_bahan' => $request->kode_produksi_bahan[$idx],
            ]);
        }

        // Simpan Aktual Suhu Adonan
        $aktuals = [
            $request->aktual_suhu_1,
            $request->aktual_suhu_2,
            $request->aktual_suhu_3,
            $request->aktual_suhu_4,
            $request->aktual_suhu_5,
        ];
        $filled = collect($aktuals)->filter(fn($v) => $v !== null && $v !== '' && is_numeric($v));
        $sum = $filled->sum();
        $count = $filled->count();
        $avg = $count > 0 ? $sum / $count : null;
        AktualSuhuAdonan::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'id_persiapan_bahan_forming' => $persiapan->id,
            'id_suhu_adonan' => $data['id_suhu_adonan'],
            'aktual_suhu_1' => $request->aktual_suhu_1,
            'aktual_suhu_2' => $request->aktual_suhu_2,
            'aktual_suhu_3' => $request->aktual_suhu_3,
            'aktual_suhu_4' => $request->aktual_suhu_4,
            'aktual_suhu_5' => $request->aktual_suhu_5,
            'total_aktual_suhu' => $avg,
        ]);

        return redirect()->route('persiapan-bahan-forming.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($uuid)
    {
        $item = PersiapanBahanForming::where('uuid', $uuid)
            ->with(['formula', 'suhuForming', 'shift', 'aktualSuhuAdonan'])
            ->firstOrFail();

        $user = auth()->user();
        // Authorization check
        if ($user->role !== 'superadmin' && $item->plan_id !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        $bahan_forming = BahanForming::where('id_formula', $item->id_formula)->get();
        
        if ($user->role === 'superadmin') {
            $shifts = DataShift::all();
            $suhu_adonan = SuhuAdonan::all();
        } else {
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $suhu_adonan = SuhuAdonan::where('id_plan', $user->id_plan)->get();
        }

        return view('qc-sistem.persiapan_bahan_forming.edit', compact('item', 'bahan_forming', 'shifts', 'suhu_adonan'));
    }

    public function update(Request $request, $uuid)
    {
        $request->validate([
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'kode_produksi_emulsi' => 'nullable|string',
            'kondisi' => 'required|in:✔,✘',
            'kode_produksi_emulsi_oil' => 'nullable|array',
            'kode_produksi_emulsi_oil.*' => 'nullable|string',
            'rework' => 'nullable|string',
            'catatan' => 'nullable|string',
            'id_suhu_forming' => 'required|array',
            'suhu' => 'required|array',
            'suhu.*' => 'required',
            'kode_produksi_bahan' => 'required|array',
            'id_suhu_adonan' => 'required|exists:suhu_adonan,id',
            'waktu_mulai_mixing' => 'nullable|string',
            'waktu_selesai_mixing' => 'nullable|string',
            'aktual_suhu_1' => 'nullable|numeric',
            'aktual_suhu_2' => 'nullable|numeric',
            'aktual_suhu_3' => 'nullable|numeric',
            'aktual_suhu_4' => 'nullable|numeric',
            'aktual_suhu_5' => 'nullable|numeric',
        ]);
        $kodeEmulsiOil = [];
        if (isset($request->kode_produksi_emulsi_oil) && is_array($request->kode_produksi_emulsi_oil)) {
            $kodeEmulsiOil = array_values(array_filter($request->kode_produksi_emulsi_oil, function($value) {
                return !empty(trim($value));
            }));
        }
        $item = PersiapanBahanForming::where('uuid', $uuid)->firstOrFail();

        // Update data - Observer akan otomatis mencatat log perubahan
        $item->update([
            'shift_id' => $request->shift_id,
            'tanggal' => Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s'),
            'kode_produksi_emulsi' => $request->kode_produksi_emulsi,
            'kondisi' => $request->kondisi,
            'kode_produksi_emulsi_oil' => $kodeEmulsiOil,
            'rework' => $request->rework,
            'catatan' => $request->catatan,
            'id_suhu_adonan' => $request->id_suhu_adonan,
            'waktu_mulai_mixing' => $request->waktu_mulai_mixing,
            'waktu_selesai_mixing' => $request->waktu_selesai_mixing,
        ]);

        // Update detail suhu forming
        foreach ($request->id_suhu_forming as $idx => $id) {
            SuhuForming::where('id', $id)->update([
                'suhu' => $request->suhu[$idx],
                'kode_produksi_bahan' => $request->kode_produksi_bahan[$idx],
            ]);
        }

        // Update/Create Aktual Suhu Adonan
        $aktuals = [
            $request->aktual_suhu_1,
            $request->aktual_suhu_2,
            $request->aktual_suhu_3,
            $request->aktual_suhu_4,
            $request->aktual_suhu_5,
        ];
        $filled = collect($aktuals)->filter(fn($v) => $v !== null && $v !== '' && is_numeric($v));
        $sum = $filled->sum();
        $count = $filled->count();
        $avg = $count > 0 ? $sum / $count : null;
        
        $aktual = AktualSuhuAdonan::where('id_persiapan_bahan_forming', $item->id)->first();
        if ($aktual) {
            $aktual->update([
                'id_suhu_adonan' => $request->id_suhu_adonan,
                'aktual_suhu_1' => $request->aktual_suhu_1,
                'aktual_suhu_2' => $request->aktual_suhu_2,
                'aktual_suhu_3' => $request->aktual_suhu_3,
                'aktual_suhu_4' => $request->aktual_suhu_4,
                'aktual_suhu_5' => $request->aktual_suhu_5,
                'total_aktual_suhu' => $avg,
            ]);
        } else {
            AktualSuhuAdonan::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'id_persiapan_bahan_forming' => $item->id,
                'id_suhu_adonan' => $request->id_suhu_adonan,
                'aktual_suhu_1' => $request->aktual_suhu_1,
                'aktual_suhu_2' => $request->aktual_suhu_2,
                'aktual_suhu_3' => $request->aktual_suhu_3,
                'aktual_suhu_4' => $request->aktual_suhu_4,
                'aktual_suhu_5' => $request->aktual_suhu_5,
                'total_aktual_suhu' => $avg,
            ]);
        }

        return redirect()->route('persiapan-bahan-forming.index')
                        ->with('success', 'Data berhasil diperbarui dan perubahan telah dicatat secara otomatis');
    }

    /**
     * Method untuk membuat log perubahan data
     */
    private function buatLogPerubahan($persiapanBahanForming, $dataLama, $request)
    {
        $user = auth()->user();
        $dataBaru = $persiapanBahanForming->getAttributes();
        
        // Cari field yang berubah
        $fieldYangBerubah = [];
        $nilaiLama = [];
        $nilaiBaru = [];
        
        foreach ($dataBaru as $field => $nilai) {
            // Skip field yang tidak perlu di-log
            if (in_array($field, ['id', 'uuid', 'created_at', 'updated_at'])) {
                continue;
            }
            
            $nilaiLamaField = $dataLama[$field] ?? null;
            $nilaiBaruField = $nilai;
            
            // Jika nilai berubah
            if ($nilaiLamaField != $nilaiBaruField) {
                $fieldYangBerubah[] = $field;
                $nilaiLama[$field] = $nilaiLamaField;
                $nilaiBaru[$field] = $nilaiBaruField;
            }
        }
        
        // Jika ada perubahan, buat log
        if (!empty($fieldYangBerubah)) {
            \App\Models\PersiapanBahanFormingLog::create([
                'persiapan_bahan_forming_id' => $persiapanBahanForming->id,
                'persiapan_bahan_forming_uuid' => $persiapanBahanForming->uuid,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role,
                'aksi' => 'update',
                'field_yang_diubah' => $fieldYangBerubah,
                'nilai_lama' => $nilaiLama,
                'nilai_baru' => $nilaiBaru,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'keterangan' => 'Data diperbarui melalui form edit'
            ]);
        }
    }

    public function show($uuid)
    {
        $data = PersiapanBahanForming::where('uuid', $uuid)
            ->with([
                'plan',
                'formula.produk',
                'suhuForming.bahanForming',
                'shift',
                'aktualSuhuAdonan',
                'suhuAdonan',
                'user'
            ])
            ->firstOrFail();

        $user = auth()->user();
        
        // Check access permission
        if ($user->role !== 'superadmin' && $data->plan_id !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data ini.');
        }

        return view('qc-sistem.persiapan_bahan_forming.show', compact('data'));
    }

    public function destroy($uuid)
    {
        try {
            $item = PersiapanBahanForming::where('uuid', $uuid)->firstOrFail();
            SuhuForming::where('id_persiapan_bahan_forming', $item->id)->delete();
            $item->aktuals()->delete();
            $item->delete();
            
            return redirect()->route('persiapan-bahan-forming.index')->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('persiapan-bahan-forming.index')->with('error', 'Gagal menghapus data');
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
                'produksi' => [1, 2, 5], // Role 1, 2, dan 5 bisa approve Produksi
                'spv' => [1, 4, 5] // Role 1, 4, dan 5 bisa approve SPV
            ];

            if (!in_array($userRole, $allowedRoles[$type])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk melakukan approval ini'
                ], 403);
            }

            $persiapanBahanForming = PersiapanBahanForming::where('uuid', $uuid)->firstOrFail();

            // Check authorization - user hanya bisa approve data dari plan mereka sendiri
            if ($user->role !== 'superadmin' && $persiapanBahanForming->plan_id !== $user->id_plan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            // Cek apakah sudah diapprove sebelumnya
            $approvalField = 'approved_by_' . $type;
            if ($persiapanBahanForming->$approvalField) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data sudah disetujui oleh ' . strtoupper($type)
                ], 400);
            }

            // Sequential approval validation
            if ($type === 'produksi' && !$persiapanBahanForming->approved_by_qc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data harus disetujui oleh QC terlebih dahulu'
                ], 400);
            }

            if ($type === 'spv' && !$persiapanBahanForming->approved_by_produksi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data harus disetujui oleh Produksi terlebih dahulu'
                ], 400);
            }

            // Update approval status
            $updateData = [
                $approvalField => true,
                $type . '_approved_by' => $user->id,
                $type . '_approved_at' => now()
            ];

            $persiapanBahanForming->update($updateData);

            // Log activity
            PersiapanBahanFormingLog::create([
                'persiapan_bahan_forming_id' => $persiapanBahanForming->id,
                'persiapan_bahan_forming_uuid' => $uuid,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role,
                'aksi' => 'approve',
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

    // AJAX
    public function getNomorFormulaOptions()
    {
        $user = auth()->user();
        $query = \App\Models\NomorFormula::select('id', 'nomor_formula', 'id_produk')->with('produk');

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $formula = $query->get();
        return response()->json($formula);
    }

    public function getBahanFormingByProduk($id_produk)
    {
        $bahan = BahanForming::where('id_produk', $id_produk)->get();
        return response()->json($bahan);
    }

    public function getBahanFormingByFormula($id_formula)
    {
        $user = auth()->user();
        $query = BahanForming::with('formula:id,nomor_formula')
            ->where('id_formula', $id_formula);

        if ($user->role !== 'superadmin') {
            $query->whereHas('formula', function($q) use ($user) {
                $q->where('id_plan', $user->id_plan);
            });
        }

        $bahan = $query->get(['id', 'nama_rm', 'berat_rm', 'id_formula']);
        return response()->json($bahan);
    }

    public function searchAjax(Request $request)
    {
        $user = auth()->user();
        $search = $request->get('search');
        $perPage = $request->get('per_page', 5);

        $page = (int) $request->get('page', 1);
        $perPage = (int) $perPage;
        if ($perPage <= 0) {
            $perPage = 5;
        }

        $query = PersiapanBahanForming::with([
            'plan',
            'formula.produk',
            'suhuForming.bahanForming',
            'shift',
            'aktualSuhuAdonan',
            'suhuAdonan',
            'user',
            'qcApprover',
            'produksiApprover',
            'spvApprover'
        ])
        ->when($user->role !== 'superadmin', function($q) use ($user) {
            $q->where('plan_id', $user->id_plan);
        });

        // Add search functionality
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('kode_produksi_emulsi', 'like', "%{$search}%")
                  ->orWhere('kode_produksi_emulsi_oil', 'like', "%{$search}%")
                  ->orWhere('kondisi', 'like', "%{$search}%")
                  ->orWhere('catatan', 'like', "%{$search}%")
                  ->orWhereHas('plan', function($q) use ($search) {
                      $q->where('nama_plan', 'like', "%{$search}%");
                  })
                  ->orWhereHas('formula.produk', function($q) use ($search) {
                      $q->where('nama_produk', 'like', "%{$search}%");
                  })
                  ->orWhereHas('formula', function($q) use ($search) {
                      $q->where('nomor_formula', 'like', "%{$search}%");
                  })
                  ->orWhereHas('suhuForming.bahanForming', function($q) use ($search) {
                      $q->where('nama_rm', 'like', "%{$search}%");
                  });
            });
        }

        $dataForming = $query->orderBy('created_at', 'desc')->limit(500)->get();

        $queryNonForming = PersiapanBahanNonForming::with([
            'formulaNonForming.produk',
            'shift',
            'suhuAdonan',
            'user',
        ])
        ->when($user->role !== 'superadmin', function ($q) use ($user) {
            $q->where('plan_id', $user->id_plan);
        });

        if ($search) {
            $queryNonForming->where(function ($q) use ($search) {
                $q->where('kode_produksi', 'like', "%{$search}%")
                  ->orWhere('kondisi', 'like', "%{$search}%")
                  ->orWhere('catatan', 'like', "%{$search}%")
                  ->orWhereHas('formulaNonForming', function ($q) use ($search) {
                      $q->where('nomor_formula', 'like', "%{$search}%");
                  })
                  ->orWhereHas('formulaNonForming.produk', function ($q) use ($search) {
                      $q->where('nama_produk', 'like', "%{$search}%");
                  });
            });
        }

        $dataNonForming = $queryNonForming->orderBy('created_at', 'desc')->limit(500)->get();

        $formingRecords = $dataForming->map(function ($item) {
            return [
                'jenis' => 'Forming',
                'uuid' => $item->uuid,
                'shift' => $item->shift,
                'shift_id' => $item->shift_id,
                'tanggal' => $item->tanggal ? Carbon::parse($item->tanggal)->format('Y-m-d H:i:s') : null,
                'jam' => $item->jam ? Carbon::parse($item->jam)->format('H:i') : null,
                'nama_produk' => $item->formula->produk->nama_produk ?? null,
                'kode_produksi' => $item->kode_produksi_emulsi,
                'nomor_formula' => $item->formula->nomor_formula ?? null,
                'kondisi' => $item->kondisi,
                'catatan' => $item->catatan,
                'dibuat_oleh' => $item->user->name ?? null,
                'created_at' => $item->created_at ? Carbon::parse($item->created_at)->timestamp : 0,
            ];
        });

        $nonFormingRecords = $dataNonForming->map(function ($item) {
            return [
                'jenis' => 'Non Forming',
                'uuid' => $item->uuid,
                'shift' => $item->shift,
                'shift_id' => $item->shift_id,
                'tanggal' => $item->tanggal ? Carbon::parse($item->tanggal)->format('Y-m-d H:i:s') : null,
                'jam' => $item->jam ? Carbon::parse($item->jam)->format('H:i') : null,
                'nama_produk' => $item->formulaNonForming->produk->nama_produk ?? null,
                'kode_produksi' => $item->kode_produksi,
                'nomor_formula' => $item->formulaNonForming->nomor_formula ?? null,
                'kondisi' => $item->kondisi,
                'catatan' => $item->catatan,
                'dibuat_oleh' => $item->user->name ?? null,
                'created_at' => $item->created_at ? Carbon::parse($item->created_at)->timestamp : 0,
            ];
        });

        $merged = $formingRecords
            ->concat($nonFormingRecords)
            ->sortByDesc('created_at')
            ->values();

        $total = $merged->count();
        $items = $merged->slice(($page - 1) * $perPage, $perPage)->values();

        $lastPage = (int) ceil($total / $perPage);
        $from = $total > 0 ? (($page - 1) * $perPage + 1) : null;
        $to = $total > 0 ? min($page * $perPage, $total) : null;

        return response()->json([
            'data' => $items,
            'pagination' => [
                'current_page' => $page,
                'last_page' => $lastPage,
                'per_page' => $perPage,
                'total' => $total,
                'from' => $from,
                'to' => $to,
                'has_pages' => $lastPage > 1,
            ],
        ]);
    }

    public function exportPdf($uuid = null)
    {
        $user = auth()->user();
        
        $query = PersiapanBahanForming::with([
            'plan',
            'formula.produk',
            'suhuForming.bahanForming',
            'shift',
            'aktualSuhuAdonan',
            'suhuAdonan',
            'qcApprover',
            'produksiApprover',
            'spvApprover'
        ])
        ->when($user->role !== 'superadmin', function($q) use ($user) {
            $q->where('plan_id', $user->id_plan);
        });

        // If UUID is provided, export single record
        if ($uuid) {
            $query->where('uuid', $uuid);
            $filename = 'persiapan_bahan_forming_' . $uuid . '_' . date('Y-m-d_H-i-s') . '.pdf';
        } else {
            // Export all records
            $filename = 'persiapan_bahan_forming_' . date('Y-m-d_H-i-s') . '.pdf';
        }

        $data = $query->orderBy('created_at', 'desc')->get();

        if ($data->isEmpty()) {
            $errorMessage = 'Tidak ada data yang sesuai dengan filter yang dipilih.';
            $filterInfo = [];
            
            if ($request->tanggal) {
                $filterInfo[] = 'Tanggal: ' . \Carbon\Carbon::parse($request->tanggal)->format('d-m-Y');
            }
            if ($request->shift_id) {
                $shift = \App\Models\DataShift::find($request->shift_id);
                $filterInfo[] = 'Shift: ' . ($shift ? $shift->shift : 'Unknown');
            }
            if ($request->produk_id) {
                $produk = \App\Models\JenisProduk::find($request->produk_id);
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

        $pdf = PDF::loadView('qc-sistem.persiapan_bahan_forming.export_pdf', compact('data'));
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download($filename);
    }

    public function exportExcel($uuid = null)
    {
        if ($uuid) {
            $filename = 'persiapan_bahan_forming_' . $uuid . '_' . date('Y-m-d_H-i-s') . '.xlsx';
        } else {
            $filename = 'persiapan_bahan_forming_' . date('Y-m-d_H-i-s') . '.xlsx';
        }
        
        return Excel::download(new PersiapanBahanFormingExport($uuid), $filename);
    }

    public function saveKode(Request $request)
    {
        try {
            $request->validate([
                'uuid' => 'required|string',
                'kode_form' => 'required|string|max:50'
            ]);

            $item = PersiapanBahanForming::where('uuid', $request->uuid)->firstOrFail();
            
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

    public function bulkExportPdf(Request $request)
    {
        try {
            // Debug: tampilkan parameter yang diterima
            if ($request->has('debug')) {
                dd($request->all());
            }
            
            $request->validate([
                'tanggal' => 'nullable|date',
                'shift_id' => 'nullable|exists:data_shift,id',
                'produk_id' => 'nullable|exists:jenis_produk,id',
                'kode_form' => 'required|string|max:50'
            ]);

            $user = auth()->user();
            
            $query = PersiapanBahanForming::with([
                'plan',
                'formula.produk',
                'suhuForming.bahanForming',
                'shift',
                'aktualSuhuAdonan',
                'suhuAdonan',
                'qcApprover',
                'produksiApprover',
                'spvApprover'
            ])
            ->when($user->role !== 'superadmin', function($q) use ($user) {
                $q->where('plan_id', $user->id_plan);
            });

            // Apply filters
            if ($request->tanggal) {
                $query->whereDate('tanggal', $request->tanggal);
            }
            
            if ($request->shift_id) {
                $query->where('shift_id', $request->shift_id);
            }
            
            if ($request->produk_id) {
                $query->whereHas('formula.produk', function($q) use ($request) {
                    $q->where('id', $request->produk_id);
                });
            }

            $dataForming = $query->orderBy('created_at', 'desc')->get();

            $queryNonForming = PersiapanBahanNonForming::with([
                'formulaNonForming.produk',
                'details.bahanNonForming',
                'shift',
                'suhuAdonan',
                'user',
            ])->when($user->role !== 'superadmin', function ($q) use ($user) {
                $q->where('plan_id', $user->id_plan);
            });

            if ($request->tanggal) {
                $queryNonForming->whereDate('tanggal', $request->tanggal);
            }

            if ($request->shift_id) {
                $queryNonForming->where('shift_id', $request->shift_id);
            }

            if ($request->produk_id) {
                $queryNonForming->whereHas('formulaNonForming.produk', function ($q) use ($request) {
                    $q->where('id', $request->produk_id);
                });
            }

            $dataNonForming = $queryNonForming->orderBy('created_at', 'desc')->get();

            if ($dataForming->isEmpty() && $dataNonForming->isEmpty()) {
                $errorMessage = 'Tidak ada data yang sesuai dengan filter yang dipilih.';
                $filterInfo = [];
                
                if ($request->tanggal) {
                    $filterInfo[] = 'Tanggal: ' . \Carbon\Carbon::parse($request->tanggal)->format('d-m-Y');
                }
                if ($request->shift_id) {
                    $shift = \App\Models\DataShift::find($request->shift_id);
                    $filterInfo[] = 'Shift: ' . ($shift ? $shift->shift : 'Unknown');
                }
                if ($request->produk_id) {
                    $produk = \App\Models\JenisProduk::find($request->produk_id);
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
            $dataForming->each(function($item) use ($request) {
                $item->update([
                    'kode_form' => $request->kode_form
                ]);
            });

            $dataNonForming->each(function ($item) use ($request) {
                $item->update([
                    'kode_form' => $request->kode_form
                ]);
            });

            $filename = 'persiapan_bahan_bulk_' . date('Y-m-d_H-i-s') . '.pdf';
            
            // Test view rendering first
            try {
                $view = view('qc-sistem.persiapan_bahan_forming.export_pdf_bulk_combined', [
                    'dataForming' => $dataForming,
                    'dataNonForming' => $dataNonForming,
                    'kode_form' => $request->kode_form,
                ]);
                $html = $view->render();
            } catch (\Exception $viewError) {
                return response()->json([
                    'error' => 'Error rendering view: ' . $viewError->getMessage(),
                    'line' => $viewError->getLine(),
                    'file' => $viewError->getFile()
                ], 500);
            }
            
            $pdf = PDF::loadView('qc-sistem.persiapan_bahan_forming.export_pdf_bulk_combined', [
                'dataForming' => $dataForming,
                'dataNonForming' => $dataNonForming,
                'kode_form' => $request->kode_form,
            ]);
            $pdf->setPaper('A4', 'landscape');
            
            return $pdf->download($filename);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $e->validator->errors()->all()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Exception occurred',
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }

    /**
     * Menampilkan riwayat log perubahan data
     */
    public function showLogs($uuid)
    {
        $item = PersiapanBahanForming::where('uuid', $uuid)->firstOrFail();
        
        $logs = PersiapanBahanFormingLog::where('persiapan_bahan_forming_id', $item->id)
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->simplePaginate(5);
        
        return view('qc-sistem.persiapan_bahan_forming.logs', compact('item', 'logs'));
    }

    /**
     * API untuk mendapatkan log dalam format JSON (untuk AJAX)
     */
    public function getLogsJson($uuid)
    {
        $item = PersiapanBahanForming::where('uuid', $uuid)->firstOrFail();
        
        $logs = PersiapanBahanFormingLog::where('persiapan_bahan_forming_id', $item->id)
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
     * AJAX endpoint untuk mendapatkan nomor formula berdasarkan produk
     */
    public function getFormulaByProduk($id_produk)
    {
        $user = auth()->user();
        
        if ($user->role === 'superadmin') {
            $formulas = \App\Models\NomorFormula::where('id_produk', $id_produk)->get();
        } else {
            $formulas = \App\Models\NomorFormula::where('id_produk', $id_produk)
                        ->where('id_plan', $user->id_plan)
                        ->get();
        }
        
        return response()->json($formulas);
    }

    /**
     * AJAX endpoint untuk mendapatkan suhu adonan berdasarkan produk
     */
    public function getSuhuAdonanByProduk($id_produk)
    {
        $user = auth()->user();
        
        if ($user->role === 'superadmin') {
            $suhuAdonan = \App\Models\SuhuAdonan::where('id_produk', $id_produk)->get();
        } else {
            $suhuAdonan = \App\Models\SuhuAdonan::where('id_produk', $id_produk)
                          ->where('id_plan', $user->id_plan)
                          ->get();
        }
        
        return response()->json($suhuAdonan);
    }
}
