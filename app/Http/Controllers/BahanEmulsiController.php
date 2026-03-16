<?php

namespace App\Http\Controllers;

use App\Models\BahanEmulsi;
use App\Models\NomorEmulsi;
use App\Models\TotalPemakaianEmulsi;
use App\Models\JenisProduk;
use App\Models\JenisEmulsi;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class BahanEmulsiController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = BahanEmulsi::with(['nomor_emulsi', 'total_pemakaian', 'produk', 'emulsi', 'plan', 'user'])
            ->orderBy('id_plan')
            ->orderBy('id_produk')
            ->orderBy('nomor_emulsi_id')
            ->orderBy('total_pemakaian_id')
            ->orderBy('nama_emulsi_id')
            ->orderBy('id');
    
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
    
        $data = $query->get();
        
        return view('super-admin.bahan_emulsi.index', compact('data'));
    }

    public function create()
    {
        $user = Auth::user();
        $nomorEmulsis = NomorEmulsi::all();
        $totalPemakaians = TotalPemakaianEmulsi::all();
        $emulsis = JenisEmulsi::all();

        if ($user->role == 'superadmin') {
            $produks = JenisProduk::all();
            $plans = Plan::all();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $plans = Plan::where('id', $user->id_plan)->get();
        }

        return view('super-admin.bahan_emulsi.create', compact('nomorEmulsis', 'totalPemakaians', 'produks', 'emulsis', 'plans'));
    }

    public function store(Request $request)
    {
        // Validasi
        $request->validate([
            'id_plan' => 'required',
            'id_produk' => 'required',
            'nama_emulsi_id' => 'required',
            'total_pemakaian_id' => 'required',
            'nomor_emulsi_id' => 'required',
            'nama_rm' => 'required|array',
            'nama_rm.*' => 'required|string',
            'berat_rm' => 'required|array',
            'berat_rm.*' => 'required|string',
        ]);

        // Loop untuk setiap RM dan simpan sebagai record terpisah
        foreach($request->nama_rm as $index => $nama) {
            BahanEmulsi::create([
                'uuid' => Str::uuid(),
                'id_plan' => $request->id_plan,
                'id_produk' => $request->id_produk,
                'nama_emulsi_id' => $request->nama_emulsi_id,
                'total_pemakaian_id' => $request->total_pemakaian_id,
                'nomor_emulsi_id' => $request->nomor_emulsi_id,
                'nama_rm' => $nama,
                'berat_rm' => $request->berat_rm[$index],
                'user_id' => auth()->user()->id,
            ]);
        }

        return redirect()->route('bahan-emulsi.index')->with('success', 'Data berhasil disimpan');
    }
    public function edit($uuid)
    {
        $item = BahanEmulsi::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
       // $nomorEmulsis = NomorEmulsi::all();
       // $totalPemakaians = TotalPemakaianEmulsi::all();
        $emulsis = JenisEmulsi::all();

        if ($user->role == 'superadmin') {
            $produks = JenisProduk::all();
            $plans = Plan::all();
             $nomorEmulsis = NomorEmulsi::all();
             $totalPemakaians = TotalPemakaianEmulsi::all();
              $emulsis = JenisEmulsi::all();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $plans = Plan::where('id', $user->id_plan)->get();
              $nomorEmulsis = NomorEmulsi::where('id_plan', $user->id_plan)->get();
              $totalPemakaians = TotalPemakaianEmulsi::where('id_plan', $user->id_plan)->get();
               $emulsis = JenisEmulsi::where('id_plan', $user->id_plan)->get();
        }

        return view('super-admin.bahan_emulsi.edit', compact('item', 'nomorEmulsis', 'totalPemakaians', 'produks', 'emulsis', 'plans'));
    }

    public function update(Request $request, $uuid)
    {
        $request->validate([
            'nama_rm' => 'required',
            'berat_rm' => 'required|string',
            'nomor_emulsi_id' => 'required|integer',
            'total_pemakaian_id' => 'required|integer',
            'id_produk' => 'required|integer',
            'nama_emulsi_id' => 'required|integer',
            'id_plan' => 'required|integer',
        ]);

        $item = BahanEmulsi::where('uuid', $uuid)->firstOrFail();
        $item->update([
            'nama_rm' => $request->nama_rm,
            'berat_rm' => $request->berat_rm,
            'nomor_emulsi_id' => $request->nomor_emulsi_id,
            'total_pemakaian_id' => $request->total_pemakaian_id,
            'id_produk' => $request->id_produk,
            'nama_emulsi_id' => $request->nama_emulsi_id,
            'id_plan' => $request->id_plan,
            'user_id' => auth()->user()->id,
        ]);

        return redirect()->route('bahan-emulsi.index')->with('success', 'Data berhasil diupdate');
    }

    public function destroy($uuid)
    {
        $item = BahanEmulsi::where('uuid', $uuid)->firstOrFail();
        $item->delete();
        return redirect()->route('bahan-emulsi.index')->with('success', 'Data berhasil dihapus');
    }

    public function getNomorEmulsi(Request $request)
    {
        $query = \App\Models\NomorEmulsi::query();

        if ($request->id_plan) {
            $query->where('id_plan', $request->id_plan);
        }
        if ($request->id_produk) {
            $query->where('id_produk', $request->id_produk);
        }
        if ($request->nama_emulsi_id) {
            $query->where('nama_emulsi_id', $request->nama_emulsi_id);
        }
        if ($request->total_pemakaian_id) {
        $query->where('total_pemakaian_id', $request->total_pemakaian_id);
    }

        $data = $query->get(['id', 'nomor_emulsi']);
        return response()->json($data);
    }
}
