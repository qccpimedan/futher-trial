<?php

namespace App\Http\Controllers;

use App\Models\JenisBreader;
use App\Models\JenisProduk;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class JenisBreaderController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = JenisBreader::with(['plan', 'user', 'produk']);
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        $data = $query->get();
        return view('super-admin.jenis_breader.index', compact('data'));
    }

    public function create()
    {
        $user = auth()->user();
        if ($user->role == 'superadmin') {
            $produks = JenisProduk::all();
            $plans = Plan::all();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $plans = Plan::where('id', $user->id_plan)->get();
        }
        return view('super-admin.jenis_breader.create', compact('produks', 'plans'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'id_plan' => 'required|exists:plan,id', // Tambahkan validasi plan
            'id_produk' => 'required|exists:jenis_produk,id',
            'jenis_breader' => 'required|string|max:255',
        ]);
        JenisBreader::create([
            'uuid' => Str::uuid(),
            'id_plan' => $request->id_plan, // Ambil dari request
            'user_id' => $user->id,
            'id_produk' => $request->id_produk,
            'jenis_breader' => $request->jenis_breader,
        ]);
        return redirect()->route('jenis-breader.index')->with('success', 'Data berhasil disimpan');
    }

    public function edit($uuid)
    {
        $user = auth()->user();
        $item = JenisBreader::where('uuid', $uuid)->firstOrFail();
        if ($user->role == 'superadmin') {
            $produks = JenisProduk::all();
            $plans = Plan::all();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $plans = Plan::where('id', $user->id_plan)->get();
        }
        return view('super-admin.jenis_breader.edit', compact('item', 'produks', 'plans'));
    }

    public function update(Request $request, $uuid)
    {
        $user = auth()->user();
        $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'jenis_breader' => 'required|string|max:255',
        ]);
        $item = JenisBreader::where('uuid', $uuid)->firstOrFail();
        $item->update([
            'id_plan' => $user->id_plan,
            'user_id' => $user->id,
            'id_produk' => $request->id_produk,
            'jenis_breader' => $request->jenis_breader,
        ]);
        return redirect()->route('jenis-breader.index')->with('success', 'Data berhasil diupdate');
    }

    public function destroy($uuid)
    {
        $item = JenisBreader::where('uuid', $uuid)->firstOrFail();
        $item->delete();
        return redirect()->route('jenis-breader.index')->with('success', 'Data berhasil dihapus');
    }
}
