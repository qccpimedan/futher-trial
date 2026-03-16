<?php

namespace App\Http\Controllers;

use App\Models\NomorStepFormulaFla;
use App\Models\NamaFormulaFla;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NomorStepFormulaFlaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        
        if ($user->role === 'superadmin') {
            $items = NomorStepFormulaFla::with(['plan', 'user', 'namaFormulaFla.produk'])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $items = NomorStepFormulaFla::with(['plan', 'user', 'namaFormulaFla.produk'])
                ->where('id_plan', $user->id_plan)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('super-admin.nomor_step_fla.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        
        // Get nama formula fla based on user's plan
        if ($user->role === 'superadmin') {
            $namaFormulaFlas = NamaFormulaFla::with('plan')->get();
        } else {
            $namaFormulaFlas = NamaFormulaFla::where('id_plan', $user->id_plan)->get();
        }

        // Get unique products from nama_formula_fla using relationship
        if ($user->role === 'superadmin') {
            $products = NamaFormulaFla::with('produk')
                ->get()
                ->pluck('produk')
                ->filter()
                ->unique('id')
                ->values();
        } else {
            $products = NamaFormulaFla::with('produk')
                ->where('id_plan', $user->id_plan)
                ->get()
                ->pluck('produk')
                ->filter()
                ->unique('id')
                ->values();
        }

        // Proses options
        $prosesOptions = ['sauted', 'mixing', 'stirring'];

        return view('super-admin.nomor_step_fla.create', compact('namaFormulaFlas', 'products', 'prosesOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'id_nama_formula_fla' => 'required|exists:nama-formula-fla,id',
            'proses' => 'required|array|min:1|max:2',
            'proses.*' => 'in:sauted,mixing,stirring',
            'nomor_step' => 'required|string|max:255',
        ], [
            'id_nama_formula_fla.required' => 'Nama formula FLA harus dipilih.',
            'id_nama_formula_fla.exists' => 'Nama formula FLA tidak valid.',
            'proses.required' => 'Proses harus dipilih.',
            'proses.array' => 'Proses harus berupa array.',
            'proses.min' => 'Minimal pilih 1 proses.',
            'proses.max' => 'Maksimal pilih 2 proses.',
            'proses.*.in' => 'Proses harus salah satu dari: sauted, mixing, stirring.',
            'nomor_step.required' => 'Nomor step harus diisi.',
        ]);

        // Authorization check for non-superadmin
        if ($user->role !== 'superadmin') {
            $namaFormulaFla = NamaFormulaFla::findOrFail($request->id_nama_formula_fla);
            if ($namaFormulaFla->id_plan !== $user->id_plan) {
                abort(403, 'Anda tidak memiliki akses untuk membuat data ini.');
            }
        }

        // Convert proses array to comma-separated string
        $prosesString = implode(',', $request->proses);

        NomorStepFormulaFla::create([
            'uuid' => Str::uuid(),
            'id_plan' => $user->id_plan,
            'user_id' => $user->id,
            'id_nama_formula_fla' => $request->id_nama_formula_fla,
            'proses' => $prosesString,
            'nomor_step' => $request->nomor_step,
        ]);

        return redirect()->route('nomor-step-formula-fla.index')
            ->with('success', 'Data nomor step formula FLA berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $user = auth()->user();
        
        $nomorStepFormulaFla = NomorStepFormulaFla::where('uuid', $uuid)->firstOrFail();
        
        // Authorization check
        if ($user->role !== 'superadmin' && $nomorStepFormulaFla->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data ini.');
        }

        $item = $nomorStepFormulaFla->load(['plan', 'user', 'namaFormulaFla.produk']);

        return view('super-admin.nomor_step_fla.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $user = auth()->user();
        
        $nomorStepFormulaFla = NomorStepFormulaFla::where('uuid', $uuid)->firstOrFail();
        
        // Authorization check
        if ($user->role !== 'superadmin' && $nomorStepFormulaFla->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        // Get nama formula fla based on user's plan
        if ($user->role === 'superadmin') {
            $namaFormulaFlas = NamaFormulaFla::with('plan')->get();
        } else {
            $namaFormulaFlas = NamaFormulaFla::where('id_plan', $user->id_plan)->get();
        }

        // Get unique products from nama_formula_fla using relationship
        if ($user->role === 'superadmin') {
            $products = NamaFormulaFla::with('produk')
                ->get()
                ->pluck('produk')
                ->filter()
                ->unique('id')
                ->values();
        } else {
            $products = NamaFormulaFla::with('produk')
                ->where('id_plan', $user->id_plan)
                ->get()
                ->pluck('produk')
                ->filter()
                ->unique('id')
                ->values();
        }

        // Proses options
        $prosesOptions = ['sauted', 'mixing', 'stirring'];

        $item = $nomorStepFormulaFla;

        return view('super-admin.nomor_step_fla.edit', compact('item', 'namaFormulaFlas', 'products', 'prosesOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $user = auth()->user();
        
        $nomorStepFormulaFla = NomorStepFormulaFla::where('uuid', $uuid)->firstOrFail();
        
        // Authorization check
        if ($user->role !== 'superadmin' && $nomorStepFormulaFla->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengupdate data ini.');
        }

        $request->validate([
            'id_nama_formula_fla' => 'required|exists:nama-formula-fla,id',
            'proses' => 'required|array|min:1|max:2',
            'proses.*' => 'in:sauted,mixing,stirring',
            'nomor_step' => 'required|string|max:255',
        ], [
            'id_nama_formula_fla.required' => 'Nama formula FLA harus dipilih.',
            'id_nama_formula_fla.exists' => 'Nama formula FLA tidak valid.',
            'proses.required' => 'Proses harus dipilih.',
            'proses.array' => 'Proses harus berupa array.',
            'proses.min' => 'Minimal pilih 1 proses.',
            'proses.max' => 'Maksimal pilih 2 proses.',
            'proses.*.in' => 'Proses harus salah satu dari: sauted, mixing, stirring.',
            'nomor_step.required' => 'Nomor step harus diisi.',
        ]);

        // Authorization check for non-superadmin
        if ($user->role !== 'superadmin') {
            $namaFormulaFla = NamaFormulaFla::findOrFail($request->id_nama_formula_fla);
            if ($namaFormulaFla->id_plan !== $user->id_plan) {
                abort(403, 'Anda tidak memiliki akses untuk mengupdate data ini.');
            }
        }

        // Convert proses array to comma-separated string
        $prosesString = implode(',', $request->proses);

        $nomorStepFormulaFla->update([
            'id_nama_formula_fla' => $request->id_nama_formula_fla,
            'proses' => $prosesString,
            'nomor_step' => $request->nomor_step,
        ]);

        return redirect()->route('nomor-step-formula-fla.index')
            ->with('success', 'Data nomor step formula FLA berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $user = auth()->user();
        
        $nomorStepFormulaFla = NomorStepFormulaFla::where('uuid', $uuid)->firstOrFail();
        
        // Authorization check
        if ($user->role !== 'superadmin' && $nomorStepFormulaFla->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        $nomorStepFormulaFla->delete();

        return redirect()->route('nomor-step-formula-fla.index')
            ->with('success', 'Data nomor step formula FLA berhasil dihapus.');
    }
}
