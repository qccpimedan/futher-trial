<?php

namespace App\Http\Controllers;

use App\Models\JenisBetter;
use App\Models\Plan;
use App\Models\User;
use App\Models\JenisProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JenisBetterController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = JenisBetter::with(['user', 'plan', 'produk']);

        if ($user->role != 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $data = $query->get();
        return view('super-admin.jenis_better.index', compact('data'));
    }

    public function create()
    {
        $user = Auth::user();
        if ($user->role == 'superadmin') {
            $users = User::all();
            $plans = Plan::all();
            $produks = JenisProduk::all();
        } else {
            $users = User::where('id_plan', $user->id_plan)->get();
            $plans = Plan::where('id', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
        }
        return view('super-admin.jenis_better.create', compact('users', 'plans', 'produks'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_plan' => 'required|exists:plan,id',
            'id_produk' => 'required|exists:jenis_produk,id',
            'nama_better' => 'required|string',
            'nama_formula_better' => 'nullable',
            'nama_formula_better.*' => 'nullable|string',
            'berat' => 'nullable',
            'berat.*' => 'nullable|string'
        ]);
        $data['user_id'] = auth()->id(); // Set user_id dari user yang login

        $betterItems = [];
        if (is_array($request->nama_formula_better)) {
            foreach ($request->nama_formula_better as $i => $namaFormula) {
                $namaFormula = is_string($namaFormula) ? trim($namaFormula) : '';
                $b = is_array($request->berat ?? null) ? ($request->berat[$i] ?? null) : null;
                $b = is_string($b) ? trim($b) : $b;
                if ($namaFormula !== '' || ($b !== null && $b !== '')) {
                    $betterItems[] = [
                        'nama_formula_better' => $namaFormula !== '' ? $namaFormula : null,
                        'berat' => ($b !== null && $b !== '') ? $b : null,
                    ];
                }
            }
        }

        // fallback kolom lama: isi dari item pertama
        $data['nama_formula_better'] = is_array($request->nama_formula_better) ? (string) ($request->nama_formula_better[0] ?? '') : (string) ($request->nama_formula_better ?? '');
        $data['berat'] = is_array($request->berat) ? (string) ($request->berat[0] ?? '') : (string) ($request->berat ?? '');
        $data['better_items'] = $betterItems;

        JenisBetter::create($data);
        return redirect()->route('jenis-better.index')->with('success', 'Data berhasil ditambah');
    }

    public function edit($uuid)
    {
        $item = JenisBetter::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        if ($user->role == 'superadmin') {
            $users = User::all();
            $plans = Plan::all();
            $produks = JenisProduk::all();
        } else {
            $users = User::where('id_plan', $user->id_plan)->get();
            $plans = Plan::where('id', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
        }
        return view('super-admin.jenis_better.edit', compact('item', 'users', 'plans', 'produks'));
    }

    public function update(Request $request, $uuid)
    {
        $data = $request->validate([
            'id_plan' => 'required|exists:plan,id',
            'id_produk' => 'required|exists:jenis_produk,id',
            'nama_better' => 'required|string',
            'nama_formula_better' => 'nullable',
            'nama_formula_better.*' => 'nullable|string',
            'berat' => 'nullable',
            'berat.*' => 'nullable|string'
        ]);

        $betterItems = [];
        if (is_array($request->nama_formula_better)) {
            foreach ($request->nama_formula_better as $i => $namaFormula) {
                $namaFormula = is_string($namaFormula) ? trim($namaFormula) : '';
                $b = is_array($request->berat ?? null) ? ($request->berat[$i] ?? null) : null;
                $b = is_string($b) ? trim($b) : $b;
                if ($namaFormula !== '' || ($b !== null && $b !== '')) {
                    $betterItems[] = [
                        'nama_formula_better' => $namaFormula !== '' ? $namaFormula : null,
                        'berat' => ($b !== null && $b !== '') ? $b : null,
                    ];
                }
            }
        }
        
        $item = JenisBetter::where('uuid', $uuid)->firstOrFail();

        $data['nama_formula_better'] = is_array($request->nama_formula_better) ? (string) ($request->nama_formula_better[0] ?? '') : (string) ($request->nama_formula_better ?? '');
        $data['berat'] = is_array($request->berat) ? (string) ($request->berat[0] ?? '') : (string) ($request->berat ?? '');
        $data['better_items'] = $betterItems;

        $item->update($data);
        return redirect()->route('jenis-better.index')->with('success', 'Data berhasil diupdate');
    }

    public function destroy($uuid)
    {
        $item = JenisBetter::where('uuid', $uuid)->firstOrFail();
        $item->delete();
        return redirect()->route('jenis-better.index')->with('success', 'Data berhasil dihapus');
    }
}
