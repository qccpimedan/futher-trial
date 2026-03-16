<?php

namespace App\Http\Controllers;

use App\Models\BahanNonForming;
use App\Models\DataShift;
use App\Models\JenisProduk;
use App\Models\MasterProdukNonForming;
use App\Models\PersiapanBahanNonForming;
use App\Models\PersiapanBahanNonFormingDetail;
use App\Models\SuhuAdonan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PersiapanBahanNonFormingController extends Controller
{
    public function index()
    {
        return redirect()->route('persiapan-bahan-non-forming.create');
    }

    public function redirectByProduk($id_produk)
    {
        $user = Auth::user();

        $query = MasterProdukNonForming::where('id_produk', $id_produk);
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $hasNonForming = $query->exists();

        if ($hasNonForming) {
            return redirect()->route('persiapan-bahan-non-forming.create', ['id_produk' => $id_produk]);
        }

        return redirect()->route('persiapan-bahan-forming.create', ['id_produk' => $id_produk]);
    }

    public function create(Request $request)
    {
        $user = Auth::user();

        $produkQuery = JenisProduk::query();
        if ($user->role !== 'superadmin') {
            $produkQuery->where('id_plan', $user->id_plan);
        }
        $produks = $produkQuery->get();

        if ($user->role === 'superadmin') {
            $shifts = DataShift::all();
            $suhuAdonan = SuhuAdonan::all();
        } else {
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $suhuAdonan = SuhuAdonan::where('id_plan', $user->id_plan)->get();
        }

        $selectedProdukId = $request->query('id_produk');

        return view('qc-sistem.persiapan_bahan_non_forming.create', compact('produks', 'shifts', 'suhuAdonan', 'selectedProdukId'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'id_no_formula_non_forming' => 'required|exists:no_formula_non_forming,id',
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'jam' => 'required|date_format:H:i',
            'id_suhu_adonan' => 'nullable|exists:suhu_adonan,id',
            'kode_produksi' => 'nullable|string',
            'kode_produksi_emulsi_oil' => 'nullable|array',
            'kode_produksi_emulsi_oil.*' => 'nullable|string',
            'waktu_mulai_mixing' => 'nullable|date_format:H:i',
            'waktu_selesai_mixing' => 'nullable|date_format:H:i',
            'kondisi' => 'nullable|string',
            'rework' => 'nullable|string',
            'catatan' => 'nullable|string',
            'id_bahan_non_forming' => 'required|array',
            'id_bahan_non_forming.*' => 'required|exists:bahan_rm_non_forming,id',
            'kode_produksi_bahan' => 'required|array',
            'kode_produksi_bahan.*' => 'nullable|string',
            'suhu' => 'required|array',
            'suhu.*' => 'nullable|string',
            'aktual_suhu_1' => 'nullable|numeric',
            'aktual_suhu_2' => 'nullable|numeric',
            'aktual_suhu_3' => 'nullable|numeric',
            'aktual_suhu_4' => 'nullable|numeric',
            'aktual_suhu_5' => 'nullable|numeric',
        ]);

        $kodeEmulsiOil = [];
        if (isset($data['kode_produksi_emulsi_oil']) && is_array($data['kode_produksi_emulsi_oil'])) {
            $kodeEmulsiOil = array_values(array_filter($data['kode_produksi_emulsi_oil'], function ($value) {
                return !empty(trim($value));
            }));
        }

        $persiapan = PersiapanBahanNonForming::create([
            'plan_id' => $user->id_plan,
            'user_id' => $user->id,
            'id_no_formula_non_forming' => $data['id_no_formula_non_forming'],
            'shift_id' => $data['shift_id'],
            'tanggal' => Carbon::createFromFormat('d-m-Y H:i:s', $data['tanggal'])->format('Y-m-d H:i:s'),
            'jam' => $data['jam'],
            'id_suhu_adonan' => $data['id_suhu_adonan'] ?? null,
            'kode_produksi' => $data['kode_produksi'] ?? null,
            'kode_produksi_emulsi_oil' => $kodeEmulsiOil,
            'waktu_mulai_mixing' => $data['waktu_mulai_mixing'] ?? null,
            'waktu_selesai_mixing' => $data['waktu_selesai_mixing'] ?? null,
            'kondisi' => $data['kondisi'] ?? null,
            'rework' => $data['rework'] ?? null,
            'catatan' => $data['catatan'] ?? null,
        ]);

        foreach ($data['id_bahan_non_forming'] as $idx => $bahanId) {
            PersiapanBahanNonFormingDetail::create([
                'id_persiapan_bahan_non_forming' => $persiapan->id,
                'id_bahan_non_forming' => $bahanId,
                'suhu' => $data['suhu'][$idx] ?? null,
                'kode_produksi_bahan' => $data['kode_produksi_bahan'][$idx] ?? null,
            ]);
        }

        // Simpan Aktual Suhu Adonan (polymorphic)
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

        if (
            $request->filled('aktual_suhu_1') ||
            $request->filled('aktual_suhu_2') ||
            $request->filled('aktual_suhu_3') ||
            $request->filled('aktual_suhu_4') ||
            $request->filled('aktual_suhu_5')
        ) {
            $persiapan->aktualSuhuAdonan()->create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'id_suhu_adonan' => $data['id_suhu_adonan'] ?? null,
                'aktual_suhu_1' => $request->aktual_suhu_1,
                'aktual_suhu_2' => $request->aktual_suhu_2,
                'aktual_suhu_3' => $request->aktual_suhu_3,
                'aktual_suhu_4' => $request->aktual_suhu_4,
                'aktual_suhu_5' => $request->aktual_suhu_5,
                'total_aktual_suhu' => $avg,
            ]);
        }

        return redirect()->route('persiapan-bahan-non-forming.create')->with('success', 'Data berhasil ditambahkan');
    }

    public function show(PersiapanBahanNonForming $persiapan_bahan_non_forming)
    {
        $user = Auth::user();
        if ($user->role !== 'superadmin' && (int) $persiapan_bahan_non_forming->plan_id !== (int) $user->id_plan) {
            abort(403);
        }

        $data = PersiapanBahanNonForming::with([
            'formulaNonForming.produk',
            'shift',
            'suhuAdonan',
            'aktualSuhuAdonan',
            'user',
            'details.bahanNonForming',
        ])->where('id', $persiapan_bahan_non_forming->id)->firstOrFail();

        return view('qc-sistem.persiapan_bahan_non_forming.show', compact('data'));
    }

    public function edit(PersiapanBahanNonForming $persiapan_bahan_non_forming)
    {
        $user = Auth::user();
        if ($user->role !== 'superadmin' && (int) $persiapan_bahan_non_forming->plan_id !== (int) $user->id_plan) {
            abort(403);
        }

        $data = PersiapanBahanNonForming::with([
            'formulaNonForming.produk',
            'shift',
            'suhuAdonan',
            'aktualSuhuAdonan',
            'user',
            'details.bahanNonForming',
        ])->where('id', $persiapan_bahan_non_forming->id)->firstOrFail();

        if ($user->role === 'superadmin') {
            $shifts = DataShift::all();
            $suhuAdonan = SuhuAdonan::all();
        } else {
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $suhuAdonan = SuhuAdonan::where('id_plan', $user->id_plan)->get();
        }

        return view('qc-sistem.persiapan_bahan_non_forming.edit', compact('data', 'shifts', 'suhuAdonan'));
    }

    public function update(Request $request, PersiapanBahanNonForming $persiapan_bahan_non_forming)
    {
        $user = Auth::user();
        if ($user->role !== 'superadmin' && (int) $persiapan_bahan_non_forming->plan_id !== (int) $user->id_plan) {
            abort(403);
        }

        $data = $request->validate([
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'jam' => 'required|date_format:H:i',
            'id_suhu_adonan' => 'nullable|exists:suhu_adonan,id',
            'kode_produksi' => 'nullable|string',
            'kode_produksi_emulsi_oil' => 'nullable|array',
            'kode_produksi_emulsi_oil.*' => 'nullable|string',
            'waktu_mulai_mixing' => 'nullable|date_format:H:i',
            'waktu_selesai_mixing' => 'nullable|date_format:H:i',
            'kondisi' => 'nullable|string',
            'rework' => 'nullable|string',
            'catatan' => 'nullable|string',
            'id_bahan_non_forming' => 'required|array',
            'id_bahan_non_forming.*' => 'required|exists:bahan_rm_non_forming,id',
            'kode_produksi_bahan' => 'required|array',
            'kode_produksi_bahan.*' => 'nullable|string',
            'suhu' => 'required|array',
            'suhu.*' => 'nullable|string',
            'aktual_suhu_1' => 'nullable|numeric',
            'aktual_suhu_2' => 'nullable|numeric',
            'aktual_suhu_3' => 'nullable|numeric',
            'aktual_suhu_4' => 'nullable|numeric',
            'aktual_suhu_5' => 'nullable|numeric',
        ]);

        $kodeEmulsiOil = [];
        if (isset($data['kode_produksi_emulsi_oil']) && is_array($data['kode_produksi_emulsi_oil'])) {
            $kodeEmulsiOil = array_values(array_filter($data['kode_produksi_emulsi_oil'], function ($value) {
                return !empty(trim($value));
            }));
        }

        $persiapan_bahan_non_forming->update([
            'shift_id' => $data['shift_id'],
            'tanggal' => Carbon::createFromFormat('d-m-Y H:i:s', $data['tanggal'])->format('Y-m-d H:i:s'),
            'jam' => $data['jam'],
            'id_suhu_adonan' => $data['id_suhu_adonan'] ?? null,
            'kode_produksi' => $data['kode_produksi'] ?? null,
            'kode_produksi_emulsi_oil' => $kodeEmulsiOil,
            'waktu_mulai_mixing' => $data['waktu_mulai_mixing'] ?? null,
            'waktu_selesai_mixing' => $data['waktu_selesai_mixing'] ?? null,
            'kondisi' => $data['kondisi'] ?? null,
            'rework' => $data['rework'] ?? null,
            'catatan' => $data['catatan'] ?? null,
        ]);

        PersiapanBahanNonFormingDetail::where('id_persiapan_bahan_non_forming', $persiapan_bahan_non_forming->id)->delete();
        foreach ($data['id_bahan_non_forming'] as $idx => $bahanId) {
            PersiapanBahanNonFormingDetail::create([
                'id_persiapan_bahan_non_forming' => $persiapan_bahan_non_forming->id,
                'id_bahan_non_forming' => $bahanId,
                'suhu' => $data['suhu'][$idx] ?? null,
                'kode_produksi_bahan' => $data['kode_produksi_bahan'][$idx] ?? null,
            ]);
        }

        // Update/Create Aktual Suhu Adonan (polymorphic)
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

        $aktual = $persiapan_bahan_non_forming->aktualSuhuAdonan;
        $hasAnyAktual = (
            $request->filled('aktual_suhu_1') ||
            $request->filled('aktual_suhu_2') ||
            $request->filled('aktual_suhu_3') ||
            $request->filled('aktual_suhu_4') ||
            $request->filled('aktual_suhu_5')
        );

        if ($aktual) {
            if ($hasAnyAktual) {
                $aktual->update([
                    'id_suhu_adonan' => $data['id_suhu_adonan'] ?? null,
                    'aktual_suhu_1' => $request->aktual_suhu_1,
                    'aktual_suhu_2' => $request->aktual_suhu_2,
                    'aktual_suhu_3' => $request->aktual_suhu_3,
                    'aktual_suhu_4' => $request->aktual_suhu_4,
                    'aktual_suhu_5' => $request->aktual_suhu_5,
                    'total_aktual_suhu' => $avg,
                ]);
            } else {
                $aktual->delete();
            }
        } else {
            if ($hasAnyAktual) {
                $persiapan_bahan_non_forming->aktualSuhuAdonan()->create([
                    'uuid' => \Illuminate\Support\Str::uuid(),
                    'id_suhu_adonan' => $data['id_suhu_adonan'] ?? null,
                    'aktual_suhu_1' => $request->aktual_suhu_1,
                    'aktual_suhu_2' => $request->aktual_suhu_2,
                    'aktual_suhu_3' => $request->aktual_suhu_3,
                    'aktual_suhu_4' => $request->aktual_suhu_4,
                    'aktual_suhu_5' => $request->aktual_suhu_5,
                    'total_aktual_suhu' => $avg,
                ]);
            }
        }

        return redirect()->route('persiapan-bahan-non-forming.show', $persiapan_bahan_non_forming->uuid)->with('success', 'Data berhasil diupdate');
    }

    public function exportPdf($uuid)
    {
        $user = Auth::user();

        $query = PersiapanBahanNonForming::with([
            'formulaNonForming.produk',
            'shift',
            'suhuAdonan',
            'aktualSuhuAdonan',
            'user',
            'details.bahanNonForming',
        ])->where('uuid', $uuid);

        if ($user->role !== 'superadmin') {
            $query->where('plan_id', $user->id_plan);
        }

        $data = $query->firstOrFail();

        $pdf = Pdf::loadView('qc-sistem.persiapan_bahan_non_forming.export_pdf', compact('data'));
        return $pdf->download('persiapan_bahan_non_forming_' . $uuid . '_' . date('Y-m-d_H-i-s') . '.pdf');
    }

    public function getFormulaByProduk($id_produk)
    {
        $user = Auth::user();

        $query = MasterProdukNonForming::where('id_produk', $id_produk);
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $formulas = $query->get(['id', 'nomor_formula']);

        return response()->json($formulas);
    }

    public function getBahanByFormula($id_no_formula_non_forming)
    {
        $user = Auth::user();

        $query = BahanNonForming::where('id_no_formula_non_forming', $id_no_formula_non_forming);
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $bahan = $query->get(['id', 'nama_rm', 'berat_rm', 'id_no_formula_non_forming']);

        return response()->json($bahan);
    }
}
