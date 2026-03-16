<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataDefect;
use App\Models\Plan;
use Illuminate\Support\Str;

class DataDefectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $query = DataDefect::with(['plan', 'user']);
        
        // Filter berdasarkan role
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        
        $data = $query->orderBy('created_at', 'desc')->get();
        return view('super-admin.data_defect.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        
        // Filter plans berdasarkan role
        if ($user->role === 'superadmin') {
            $plans = Plan::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
        }
        
        return view('super-admin.data_defect.create', compact('plans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'id_plan' => 'required|exists:plan,id',
            'jenis_defect' => 'required|array|min:1',
            'jenis_defect.*' => 'required|string|max:255',
            'spec_defect' => 'nullable|array',
            'spec_defect.*' => 'nullable|string|max:255',
        ], [
            'id_plan.required' => 'Plan harus dipilih',
            'jenis_defect.required' => 'Minimal harus ada 1 Jenis Defect',
            'jenis_defect.*.required' => 'Jenis Defect tidak boleh kosong',
        ]);

        // Cek akses berdasarkan role
        if ($user->role !== 'superadmin' && $request->id_plan != $user->id_plan) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk plan ini');
        }

        // Insert multiple records
        $successCount = 0;
        foreach ($request->jenis_defect as $index => $jenis) {
            if (!empty(trim($jenis))) {
                DataDefect::create([
                    'uuid' => Str::uuid(),
                    'id_plan' => $request->id_plan,
                    'user_id' => $user->id,
                    'jenis_defect' => trim($jenis),
                    'spec_defect' => isset($request->spec_defect[$index]) ? trim($request->spec_defect[$index]) : null,
                ]);
                $successCount++;
            }
        }

        return redirect()->route('data-defect.index')
            ->with('success', $successCount . ' Data Defect berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $uuid)
    {
        $data = DataDefect::where('uuid', $uuid)->with(['plan', 'user'])->firstOrFail();
        
        // Cek akses
        if (!$data->canAccess()) {
            abort(403, 'Anda tidak memiliki akses ke data ini');
        }
        
        return view('super-admin.data_defect.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $uuid)
    {
        $user = auth()->user();
        $data = DataDefect::where('uuid', $uuid)->firstOrFail();
        
        // Cek akses
        if (!$data->canAccess()) {
            abort(403, 'Anda tidak memiliki akses ke data ini');
        }
        
        // Filter plans berdasarkan role
        if ($user->role === 'superadmin') {
            $plans = Plan::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
        }
        
        return view('super-admin.data_defect.edit', compact('data', 'plans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $uuid)
    {
        $user = auth()->user();
        $data = DataDefect::where('uuid', $uuid)->firstOrFail();
        
        // Cek akses
        if (!$data->canAccess()) {
            abort(403, 'Anda tidak memiliki akses ke data ini');
        }
        
        $request->validate([
            'id_plan' => 'required|exists:plan,id',
            'jenis_defect' => 'required|string|max:255',
            'spec_defect' => 'nullable|string|max:255',
        ], [
            'id_plan.required' => 'Plan harus dipilih',
            'jenis_defect.required' => 'Jenis Defect harus diisi',
        ]);

        // Cek akses berdasarkan role
        if ($user->role !== 'superadmin' && $request->id_plan != $user->id_plan) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk plan ini');
        }

        $data->update([
            'id_plan' => $request->id_plan,
            'jenis_defect' => $request->jenis_defect,
            'spec_defect' => $request->spec_defect,
        ]);

        return redirect()->route('data-defect.index')->with('success', 'Data Defect berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $uuid)
    {
        $data = DataDefect::where('uuid', $uuid)->firstOrFail();
        
        // Cek akses
        if (!$data->canAccess()) {
            abort(403, 'Anda tidak memiliki akses ke data ini');
        }
        
        $data->delete();
        
        return redirect()->route('data-defect.index')->with('success', 'Data Defect berhasil dihapus');
    }
}