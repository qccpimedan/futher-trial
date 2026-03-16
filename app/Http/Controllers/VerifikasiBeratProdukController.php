<?php

namespace App\Http\Controllers;

use App\Models\VerifikasiBeratProduk;
use App\Models\DataShift;
use App\Models\JenisProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\VerifikasiBeratLog;


class VerifikasiBeratProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Role-based data filtering
        $query = VerifikasiBeratProduk::with(['plan', 'user', 'shift', 'produk']);
        
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $search = request('search');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('kode_produksi', 'LIKE', '%' . $search . '%')
                  ->orWhereHas('produk', function($qProd) use ($search) {
                      $qProd->where('nama_produk', 'LIKE', '%' . $search . '%');
                  });
            });
        }
    
        $verifikasiBeratProduk = $query->orderBy('tanggal', 'desc')
                                      ->orderBy('jam', 'desc')
                                      ->paginate(10);
    
        return view('qc-sistem.verifikasi-berat-produk.index', compact('verifikasiBeratProduk'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Get shifts filtered by user's plan
        $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        
        // Get products filtered by user's plan
        $produks = JenisProduk::where('id_plan', $user->id_plan)->get();

        return view('qc-sistem.verifikasi-berat-produk.create', compact('shifts', 'produks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       $user = Auth::user();
    $isSpecialRole = ($user->id_role == 2 || $user->id_role == 3);
    
    if($isSpecialRole){
        $request->validate([
            'shift_id' => 'required|exists:data_shift,id',
            'id_produk' => 'required|exists:jenis_produk,id',
                'tanggal' => 'required|date_format:d-m-Y', // Hanya validasi format tanggal saja
                'jam' => 'required',
            'kode_produksi' => 'required|string|max:255',
            'gramase' => 'required|numeric|min:0',
            'catatan' => 'nullable|string',
            'jenis_produk_kfc' => 'required|in:KFC,non-KFC',
            'berat_breader' => 'nullable|array',
            'berat_breader.*' => 'nullable|string|max:255',
            'rata_rata_breader' => 'nullable|numeric',
            'pickup_breader' => 'nullable|string|max:255',
            'pickup_total_breader' => 'nullable|string|max:255',
            'after_forming' => 'nullable|array',
            'after_forming.*' => 'nullable|string|max:255',
            'rata_rata_after_forming' => 'nullable|numeric',
            'berat_dry_kfc' => 'nullable|array',
            'berat_dry_kfc.*' => 'nullable|string|max:255',
            'rata_rata_dry_kfc' => 'nullable|numeric',
            'berat_wet_kfc' => 'nullable|array',
            'berat_wet_kfc.*' => 'nullable|string|max:255',
            'rata_rata_wet_kfc' => 'nullable|numeric',
            'pickup_after_forming_kfc' => 'nullable|string|max:255',
            'berat_predusting' => 'nullable|array',
            'berat_predusting.*' => 'nullable|string|max:255',
            'rata_rata_predusting' => 'nullable|numeric',
            'pickup_after_forming_predusting' => 'nullable|string|max:255',
            'berat_battering' => 'nullable|array',
            'berat_battering.*' => 'nullable|string|max:255',
            'rata_rata_battering' => 'nullable|numeric',
            'pickup_after_predusting_battering' => 'nullable|string|max:255',
            'berat_breadering' => 'nullable|array',
            'berat_breadering.*' => 'nullable|string|max:255',
            'rata_rata_breadering' => 'nullable|numeric',
            'pickup_after_battering_breadering' => 'nullable|string|max:255',
            'berat_fryer_1' => 'nullable|array',
            'berat_fryer_1.*' => 'nullable|string|max:255',
            'rata_rata_fryer_1' => 'nullable|numeric',
            'pickup_breadering_fryer_1' => 'nullable|string|max:255',
            'berat_fryer_2' => 'nullable|array',
            'berat_fryer_2.*' => 'nullable|string|max:255',
            'rata_rata_fryer_2' => 'nullable|numeric',
            'pickup_fryer_1_fryer_2' => 'nullable|string|max:255',
            'pickup_total' => 'nullable|string|max:255',
            'pickup_total_fryer_2' => 'nullable|string|max:255',
            'berat_roasting' => 'nullable|array',
            'berat_roasting.*' => 'nullable|string|max:255',
            'rata_rata_roasting' => 'nullable|numeric',
            'pickup_after_breadering_roasting' => 'nullable|string|max:255',
            'pickup_total_roasting' => 'nullable|string|max:255',
            
        ]);
    } else{
          $request->validate([
            'shift_id' => 'required|exists:data_shift,id',
            'id_produk' => 'required|exists:jenis_produk,id',
            'tanggal' => 'required|date_format:d-m-Y H:i:s', // Hanya validasi format tanggal saja
            'jam' => 'required',
            'kode_produksi' => 'required|string|max:255',
            'gramase' => 'required|numeric|min:0',
            'catatan' => 'nullable|string',
            'jenis_produk_kfc' => 'required|in:KFC,non-KFC',
            'berat_breader' => 'nullable|array',
            'berat_breader.*' => 'nullable|string|max:255',
            'rata_rata_breader' => 'nullable|numeric',
            'pickup_breader' => 'nullable|string|max:255',
            'pickup_total_breader' => 'nullable|string|max:255',
            'after_forming' => 'nullable|array',
            'after_forming.*' => 'nullable|string|max:255',
            'rata_rata_after_forming' => 'nullable|numeric',
            'berat_dry_kfc' => 'nullable|array',
            'berat_dry_kfc.*' => 'nullable|string|max:255',
            'rata_rata_dry_kfc' => 'nullable|numeric',
            'berat_wet_kfc' => 'nullable|array',
            'berat_wet_kfc.*' => 'nullable|string|max:255',
            'rata_rata_wet_kfc' => 'nullable|numeric',
            'pickup_after_forming_kfc' => 'nullable|string|max:255',
            'berat_predusting' => 'nullable|array',
            'berat_predusting.*' => 'nullable|string|max:255',
            'rata_rata_predusting' => 'nullable|numeric',
            'pickup_after_forming_predusting' => 'nullable|string|max:255',
            'berat_battering' => 'nullable|array',
            'berat_battering.*' => 'nullable|string|max:255',
            'rata_rata_battering' => 'nullable|numeric',
            'pickup_after_predusting_battering' => 'nullable|string|max:255',
            'berat_breadering' => 'nullable|array',
            'berat_breadering.*' => 'nullable|string|max:255',
            'rata_rata_breadering' => 'nullable|numeric',
            'pickup_after_battering_breadering' => 'nullable|string|max:255',
            'berat_fryer_1' => 'nullable|array',
            'berat_fryer_1.*' => 'nullable|string|max:255',
            'rata_rata_fryer_1' => 'nullable|numeric',
            'pickup_breadering_fryer_1' => 'nullable|string|max:255',
            'berat_fryer_2' => 'nullable|array',
            'berat_fryer_2.*' => 'nullable|string|max:255',
            'rata_rata_fryer_2' => 'nullable|numeric',
            'pickup_fryer_1_fryer_2' => 'nullable|string|max:255',
            'pickup_total' => 'nullable|string|max:255',
            'pickup_total_fryer_2' => 'nullable|string|max:255',
            'berat_roasting' => 'nullable|array',
            'berat_roasting.*' => 'nullable|string|max:255',
            'rata_rata_roasting' => 'nullable|numeric',
            'pickup_after_breadering_roasting' => 'nullable|string|max:255',
            'pickup_total_roasting' => 'nullable|string|max:255',
            
        ]);

    }
      // Transform the date format
    $tanggalData = $request->tanggal;
    if ($isSpecialRole) {
        // Untuk user dengan role 2 atau 3, gunakan format tanggal dari request tapi waktu dari now()
        $dateOnly = \Carbon\Carbon::createFromFormat('d-m-Y', $request->tanggal)->format('Y-m-d');
        $timeNow = now()->format('H:i:s');
        $tanggalData = $dateOnly . ' ' . $timeNow;
    } else {
        // Untuk user lain, gunakan format tanggal dan waktu dari request
        $tanggalData = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s');
    }
        // Verify shift belongs to user's plan
        $shift = DataShift::where('id', $request->shift_id)
            ->where('id_plan', $user->id_plan)
            ->first();

        if (!$shift) {
            return back()->withErrors(['shift_id' => 'Shift tidak valid untuk plan Anda.']);
        }

        // Verify product belongs to user's plan
        $produk = JenisProduk::where('id', $request->id_produk)
            ->where('id_plan', $user->id_plan)
            ->first();

        if (!$produk) {
            return back()->withErrors(['id_produk' => 'Produk tidak valid untuk plan Anda.']);
        }

        // Calculate average from breader values
        $breaderFiltered = $request->berat_breader ? array_filter($request->berat_breader) : null;
        $verifikasiBeratProduk = new VerifikasiBeratProduk();
        $rataRataBreader = $breaderFiltered ? $verifikasiBeratProduk->calculateBreaderAverage($breaderFiltered) : null;

        // Calculate average from after forming values
        $afterFormingFiltered = $request->after_forming ? array_filter($request->after_forming) : null;
        $rataRataAfterForming = $afterFormingFiltered ? $verifikasiBeratProduk->calculateAfterFormingAverage($afterFormingFiltered) : null;

        // Calculate average from KFC dry values
        $dryKfcFiltered = $request->berat_dry_kfc ? array_filter($request->berat_dry_kfc) : null;
        $rataRataDryKfc = $dryKfcFiltered ? $verifikasiBeratProduk->calculateDryKfcAverage($dryKfcFiltered) : null;

        // Calculate average from KFC wet values
        $wetKfcFiltered = $request->berat_wet_kfc ? array_filter($request->berat_wet_kfc) : null;
        $rataRataWetKfc = $wetKfcFiltered ? $verifikasiBeratProduk->calculateWetKfcAverage($wetKfcFiltered) : null;

        // Calculate average from predusting values
        $predustingFiltered = $request->berat_predusting ? array_filter($request->berat_predusting) : null;
        $rataRataPredusting = $predustingFiltered ? $verifikasiBeratProduk->calculatePredustingAverage($predustingFiltered) : null;

        // Calculate average from battering values
        $batteringFiltered = $request->berat_battering ? array_filter($request->berat_battering) : null;
        $rataRataBattering = $batteringFiltered ? $verifikasiBeratProduk->calculateBatteringAverage($batteringFiltered) : null;

        // Calculate average from breadering values
        $breaderingFiltered = $request->berat_breadering ? array_filter($request->berat_breadering) : null;
        $rataRataBreaderingValue = $breaderingFiltered ? $verifikasiBeratProduk->calculateBreaderingAverage($breaderingFiltered) : null;

        // Calculate average from fryer 1 values
        $fryer1Filtered = $request->berat_fryer_1 ? array_filter($request->berat_fryer_1) : null;
        $rataRataFryer1 = $fryer1Filtered ? $verifikasiBeratProduk->calculateFryer1Average($fryer1Filtered) : null;

        // Calculate average from fryer 2 values
        $fryer2Filtered = $request->berat_fryer_2 ? array_filter($request->berat_fryer_2) : null;
        $rataRataFryer2 = $fryer2Filtered ? $verifikasiBeratProduk->calculateFryer2Average($fryer2Filtered) : null;

        // Calculate average from roasting values
        $roastingFiltered = $request->berat_roasting ? array_filter($request->berat_roasting) : null;
        $rataRataRoasting = $roastingFiltered ? $verifikasiBeratProduk->calculateRoastingAverage($roastingFiltered) : null;

        VerifikasiBeratProduk::create([
            'id_plan' => $user->id_plan,
            'user_id' => $user->id,
            'shift_id' => $request->shift_id,
            'id_produk' => $request->id_produk,
             'tanggal' => $tanggalData,
            'jam' => $request->jam,
            'kode_produksi' => $request->kode_produksi,
            'gramase' => $request->gramase,
            'catatan' => $request->catatan,
            'jenis_produk_kfc' => $request->jenis_produk_kfc,
            'berat_breader' => $breaderFiltered,
            'rata_rata_breader' => $rataRataBreader,
            'pickup_breader' => $request->pickup_breader,
            'pickup_total_breader' => $request->pickup_total_breader,
            'after_forming' => $afterFormingFiltered,
            'rata_rata_after_forming' => $rataRataAfterForming,
            'berat_dry_kfc' => $dryKfcFiltered,
            'rata_rata_dry_kfc' => $rataRataDryKfc,
            'berat_wet_kfc' => $wetKfcFiltered,
            'rata_rata_wet_kfc' => $rataRataWetKfc,
            'pickup_after_forming_kfc' => $request->pickup_after_forming_kfc,
            'berat_predusting' => $predustingFiltered,
            'rata_rata_predusting' => $rataRataPredusting,
            'pickup_after_forming_predusting' => $request->pickup_after_forming_predusting,
            'berat_battering' => $batteringFiltered,
            'rata_rata_battering' => $rataRataBattering,
            'pickup_after_predusting_battering' => $request->pickup_after_predusting_battering,
            'berat_breadering' => $breaderingFiltered,
            'rata_rata_breadering' => $rataRataBreaderingValue,
            'pickup_after_battering_breadering' => $request->pickup_after_battering_breadering,
            'berat_fryer_1' => $fryer1Filtered,
            'rata_rata_fryer_1' => $rataRataFryer1,
            'pickup_breadering_fryer_1' => $request->pickup_breadering_fryer_1,
            'berat_fryer_2' => $fryer2Filtered,
            'rata_rata_fryer_2' => $rataRataFryer2,
            'pickup_fryer_1_fryer_2' => $request->pickup_fryer_1_fryer_2,
            'pickup_total' => $request->pickup_total,
            'pickup_total_fryer_2' => $request->pickup_total_fryer_2,
            'berat_roasting' => $roastingFiltered,
            'rata_rata_roasting' => $rataRataRoasting,
            'pickup_after_breadering_roasting' => $request->pickup_after_breadering_roasting,
            'pickup_total_roasting' => $request->pickup_total_roasting,
            
        ]);

        return redirect()->route('verifikasi-berat-produk.index')
            ->with('success', 'Data verifikasi berat produk berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function show($uuid)
    {
        $verifikasiBeratProduk = VerifikasiBeratProduk::with(['plan', 'user', 'shift', 'produk'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        // Check authorization
        // if (!$user->hasRole('superadmin') && $verifikasiBeratProduk->id_plan !== $user->id_plan) {
        //     abort(403, 'Unauthorized action.');
        // }

        return view('qc-sistem.verifikasi-berat-produk.show', compact('verifikasiBeratProduk'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function edit($uuid)
    {
        $verifikasiBeratProduk = VerifikasiBeratProduk::where('uuid', $uuid)->firstOrFail();

        // Check authorization
        // if (!$user->hasRole('superadmin') && $verifikasiBeratProduk->id_plan !== $user->id_plan) {
        //     abort(403, 'Unauthorized action.');
        // }

        $user = Auth::user();

        // Get shifts filtered by user's plan
        $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        
        // Get products filtered by user's plan
        $produks = JenisProduk::where('id_plan', $user->id_plan)->get();

        return view('qc-sistem.verifikasi-berat-produk.edit', compact('verifikasiBeratProduk', 'shifts', 'produks'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $uuid)
    {
        $verifikasiBeratProduk = VerifikasiBeratProduk::where('uuid', $uuid)->firstOrFail();
        
        $user = Auth::user();

        // Check authorization
        // if (!$user->hasRole('superadmin') && $verifikasiBeratProduk->id_plan !== $user->id_plan) {
        //     abort(403, 'Unauthorized action.');
        // }

        $request->validate([
            'shift_id' => 'required|exists:data_shift,id',
            'id_produk' => 'required|exists:jenis_produk,id',
            'tanggal' => 'required|date',
            'kode_produksi' => 'required|string|max:255',
            'gramase' => 'required|numeric|min:0',
            'jenis_produk_kfc' => 'required|in:KFC,non-KFC',
            'berat_breader' => 'nullable|array',
            'berat_breader.*' => 'nullable|string|max:255',
            'rata_rata_breader' => 'nullable|numeric',
            'pickup_breader' => 'nullable|string|max:255',
            'pickup_total_breader' => 'nullable|string|max:255',
            'after_forming' => 'nullable|array',
            'after_forming.*' => 'nullable|string|max:255',
            'rata_rata_after_forming' => 'nullable|numeric',
            'berat_dry_kfc' => 'nullable|array',
            'berat_dry_kfc.*' => 'nullable|string|max:255',
            'rata_rata_dry_kfc' => 'nullable|numeric',
            'berat_wet_kfc' => 'nullable|array',
            'berat_wet_kfc.*' => 'nullable|string|max:255',
            'rata_rata_wet_kfc' => 'nullable|numeric',
            'pickup_after_forming_kfc' => 'nullable|string|max:255',
            'berat_predusting' => 'nullable|array',
            'berat_predusting.*' => 'nullable|string|max:255',
            'rata_rata_predusting' => 'nullable|numeric',
            'pickup_after_forming_predusting' => 'nullable|string|max:255',
            'berat_battering' => 'nullable|array',
            'berat_battering.*' => 'nullable|string|max:255',
            'rata_rata_battering' => 'nullable|numeric',
            'pickup_after_predusting_battering' => 'nullable|string|max:255',
            'berat_breadering' => 'nullable|array',
            'berat_breadering.*' => 'nullable|string|max:255',
            'rata_rata_breadering' => 'nullable|numeric',
            'pickup_after_battering_breadering' => 'nullable|string|max:255',
            'berat_fryer_1' => 'nullable|array',
            'berat_fryer_1.*' => 'nullable|string|max:255',
            'rata_rata_fryer_1' => 'nullable|numeric',
            'pickup_breadering_fryer_1' => 'nullable|string|max:255',
            'pickup_total' => 'nullable|string|max:255',
            'berat_fryer_2' => 'nullable|array',
            'berat_fryer_2.*' => 'nullable|string|max:255',
            'rata_rata_fryer_2' => 'nullable|numeric',
            'pickup_fryer_1_fryer_2' => 'nullable|string|max:255',
            'pickup_total_fryer_2' => 'nullable|string|max:255',
            'berat_roasting' => 'nullable|array',
            'berat_roasting.*' => 'nullable|string|max:255',
            'rata_rata_roasting' => 'nullable|numeric',
            'pickup_after_breadering_roasting' => 'nullable|string|max:255',
            'pickup_total_roasting' => 'nullable|string|max:255',
            'catatan' => 'nullable|string',
        ]);

        // Verify shift belongs to user's plan
        $shift = DataShift::where('id', $request->shift_id)
            ->where('id_plan', $user->id_plan)
            ->first();

        if (!$shift) {
            return back()->withErrors(['shift_id' => 'Shift tidak valid untuk plan Anda.']);
        }

        // Verify product belongs to user's plan
        $produk = JenisProduk::where('id', $request->id_produk)
            ->where('id_plan', $user->id_plan)
            ->first();

        if (!$produk) {
            return back()->withErrors(['id_produk' => 'Produk tidak valid untuk plan Anda.']);
        }

        // Calculate averages from array values (same as store method)
        $breaderFiltered = $request->berat_breader ? array_filter($request->berat_breader) : null;
        $rataRataBreader = $breaderFiltered ? $verifikasiBeratProduk->calculateBreaderAverage($breaderFiltered) : null;

        $afterFormingFiltered = $request->after_forming ? array_filter($request->after_forming) : null;
        $rataRataAfterForming = $afterFormingFiltered ? $verifikasiBeratProduk->calculateAfterFormingAverage($afterFormingFiltered) : null;

        $dryKfcFiltered = $request->berat_dry_kfc ? array_filter($request->berat_dry_kfc) : null;
        $rataRataDryKfc = $dryKfcFiltered ? $verifikasiBeratProduk->calculateDryKfcAverage($dryKfcFiltered) : null;

        $wetKfcFiltered = $request->berat_wet_kfc ? array_filter($request->berat_wet_kfc) : null;
        $rataRataWetKfc = $wetKfcFiltered ? $verifikasiBeratProduk->calculateWetKfcAverage($wetKfcFiltered) : null;

        $predustingFiltered = $request->berat_predusting ? array_filter($request->berat_predusting) : null;
        $rataRataPredusting = $predustingFiltered ? $verifikasiBeratProduk->calculatePredustingAverage($predustingFiltered) : null;

        $batteringFiltered = $request->berat_battering ? array_filter($request->berat_battering) : null;
        $rataRataBattering = $batteringFiltered ? $verifikasiBeratProduk->calculateBatteringAverage($batteringFiltered) : null;

        $breaderingFiltered = $request->berat_breadering ? array_filter($request->berat_breadering) : null;
        $rataRataBreaderingValue = $breaderingFiltered ? $verifikasiBeratProduk->calculateBreaderingAverage($breaderingFiltered) : null;

        $fryer1Filtered = $request->berat_fryer_1 ? array_filter($request->berat_fryer_1) : null;
        $rataRataFryer1 = $fryer1Filtered ? $verifikasiBeratProduk->calculateFryer1Average($fryer1Filtered) : null;

        $fryer2Filtered = $request->berat_fryer_2 ? array_filter($request->berat_fryer_2) : null;
        $rataRataFryer2 = $fryer2Filtered ? $verifikasiBeratProduk->calculateFryer2Average($fryer2Filtered) : null;

        $roastingFiltered = $request->berat_roasting ? array_filter($request->berat_roasting) : null;
        $rataRataRoasting = $roastingFiltered ? $verifikasiBeratProduk->calculateRoastingAverage($roastingFiltered) : null;

        $verifikasiBeratProduk->update([
            'shift_id' => $request->shift_id,
            'id_produk' => $request->id_produk,
            'tanggal' => $request->tanggal,
            'kode_produksi' => $request->kode_produksi,
            'gramase' => $request->gramase,
            'jenis_produk_kfc' => $request->jenis_produk_kfc,
            'berat_breader' => $breaderFiltered,
            'rata_rata_breader' => $rataRataBreader,
            'pickup_breader' => $request->pickup_breader,
            'pickup_total_breader' => $request->pickup_total_breader,
            'after_forming' => $afterFormingFiltered,
            'rata_rata_after_forming' => $rataRataAfterForming,
            'berat_dry_kfc' => $dryKfcFiltered,
            'rata_rata_dry_kfc' => $rataRataDryKfc,
            'berat_wet_kfc' => $wetKfcFiltered,
            'rata_rata_wet_kfc' => $rataRataWetKfc,
            'pickup_after_forming_kfc' => $request->pickup_after_forming_kfc,
            'berat_predusting' => $predustingFiltered,
            'rata_rata_predusting' => $rataRataPredusting,
            'pickup_after_forming_predusting' => $request->pickup_after_forming_predusting,
            'berat_battering' => $batteringFiltered,
            'rata_rata_battering' => $rataRataBattering,
            'pickup_after_predusting_battering' => $request->pickup_after_predusting_battering,
            'berat_breadering' => $breaderingFiltered,
            'rata_rata_breadering' => $rataRataBreaderingValue,
            'pickup_after_battering_breadering' => $request->pickup_after_battering_breadering,
            'berat_fryer_1' => $fryer1Filtered,
            'rata_rata_fryer_1' => $rataRataFryer1,
            'pickup_breadering_fryer_1' => $request->pickup_breadering_fryer_1,
            'pickup_total' => $request->pickup_total,
            'berat_fryer_2' => $fryer2Filtered,
            'rata_rata_fryer_2' => $rataRataFryer2,
            'pickup_fryer_1_fryer_2' => $request->pickup_fryer_1_fryer_2,
            'pickup_total_fryer_2' => $request->pickup_total_fryer_2,
            'berat_roasting' => $roastingFiltered,
            'rata_rata_roasting' => $rataRataRoasting,
            'pickup_after_breadering_roasting' => $request->pickup_after_breadering_roasting,
            'pickup_total_roasting' => $request->pickup_total_roasting,
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('verifikasi-berat-produk.index')
            ->with('success', 'Data verifikasi berat produk berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        $verifikasiBeratProduk = VerifikasiBeratProduk::where('uuid', $uuid)->firstOrFail();
        
        $user = Auth::user();

        // Check authorization
        // if (!$user->hasRole('superadmin') && $verifikasiBeratProduk->id_plan !== $user->id_plan) {
        //     abort(403, 'Unauthorized action.');
        // }

        $verifikasiBeratProduk->delete();

        return redirect()->route('verifikasi-berat-produk.index')
            ->with('success', 'Data verifikasi berat produk berhasil dihapus.');
    }
    /**
     * Tampilkan riwayat perubahan/logs untuk satu data verifikasi berat produk
     */
    public function showLogs($uuid)
    {
        $item = VerifikasiBeratProduk::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        // Otorisasi: hanya superadmin atau data dengan plan yang sama
        if (!$user->hasRole('superadmin') && $item->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized action.');
        }

        // Ambil log terkait data ini (paginasi)
        $logs = VerifikasiBeratLog::where('verifikasi_berat_id', $item->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('qc-sistem.verifikasi-berat-produk.logs', compact('item', 'logs'));
    }
       /**
     * Bulk export PDF untuk Verifikasi Berat Produk
     */
    public function bulkExportPdf(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date',
            'shift_id' => 'required|exists:data_shift,id',
            'kode_form' => 'required|string|max:50',
            'id_produk' => 'nullable|exists:jenis_produk,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = Auth::user();
        
        // Build query with filters
        $query = VerifikasiBeratProduk::with(['plan', 'shift', 'user', 'produk'])
            ->whereDate('tanggal', $request->tanggal)
            ->where('shift_id', $request->shift_id);

        // Add product filter if provided
        if ($request->filled('id_produk')) {
            $query->where('id_produk', $request->id_produk);
        }

        // Apply role-based filtering
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $data = $query->orderBy('tanggal', 'asc')->get();

        if ($data->isEmpty()) {
            return redirect()->back()
                ->with('error', 'Tidak ada data yang ditemukan dengan filter yang dipilih.');
        }

        // Update kode_form for matching records
        $query->update(['kode_form' => $request->kode_form]);

        // Get shift and plan info for PDF
        $shift = DataShift::find($request->shift_id);
        $plan = $user->role === 'superadmin' ? $data->first()->plan : $user->plan;
        $produk = $request->filled('id_produk') ? \App\Models\JenisProduk::find($request->id_produk) : null;

        $filters = [
            'tanggal' => \Carbon\Carbon::parse($request->tanggal)->format('d/m/Y'),
            'shift' => $shift ? 'Shift ' . $shift->shift : '-',
            'kode_form' => $request->kode_form,
            'plan' => $plan ? $plan->nama_plan : '-',
            'produk' => $produk ? $produk->nama_produk : 'Semua Produk'
        ];

        $pdf = \PDF::loadView('qc-sistem.verifikasi-berat-produk.export_pdf', compact('data', 'filters'))
            ->setPaper('a4', 'portrait');

        $filename = 'verifikasi-berat-produk-' . $request->tanggal . '-shift-' . $request->shift_id . '.pdf';
        
        return $pdf->download($filename);
    }
     public function approve(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'uuid' => 'required|exists:verifikasi_berat_produk,uuid',
            'type' => 'required|in:qc,produksi,spv',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $item = VerifikasiBeratProduk::where('uuid', $request->uuid)->firstOrFail();

            // Check role-based permissions
            $userRole = $user->id_role ?? null;
            
            if ($request->type === 'qc') {
                // Only role 1, 3, 5 can approve as QC
                if (!in_array($userRole, [1, 3, 5])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki izin untuk menyetujui sebagai QC'
                    ], 403);
                }
                
                if ($item->approved_by_qc) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data sudah disetujui oleh QC sebelumnya'
                    ], 400);
                }
                
                $item->approved_by_qc = true;
                $item->qc_approved_by = $user->id;
                $item->qc_approved_at = now();
                
            } elseif ($request->type === 'produksi') {
                // Only role 2 can approve as Produksi
                if ($userRole !== 2) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki izin untuk menyetujui sebagai Produksi'
                    ], 403);
                }
                
                // Check if QC approval exists
                if (!$item->approved_by_qc) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Menunggu persetujuan QC terlebih dahulu'
                    ], 400);
                }
                
                if ($item->approved_by_produksi) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data sudah disetujui oleh Produksi sebelumnya'
                    ], 400);
                }
                
                $item->approved_by_produksi = true;
                $item->produksi_approved_by = $user->id;
                $item->produksi_approved_at = now();
                
            } elseif ($request->type === 'spv') {
                // Only role 4 can approve as SPV
                if ($userRole !== 4) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki izin untuk menyetujui sebagai SPV'
                    ], 403);
                }
                
                // Check if Produksi approval exists
                if (!$item->approved_by_produksi) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Menunggu persetujuan Produksi terlebih dahulu'
                    ], 400);
                }
                
                if ($item->approved_by_spv) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data sudah disetujui oleh SPV sebelumnya'
                    ], 400);
                }
                
                $item->approved_by_spv = true;
                $item->spv_approved_by = $user->id;
                $item->spv_approved_at = now();
            }

            $item->save();

         VerifikasiBeratLog::create([
                'verifikasi_berat_id' => $item->id,
                'verifikasi_berat_uuid' => $item->uuid,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role,
                'aksi' => 'approve_' . $request->type,
                'field_yang_diubah' => ['approved_by_' . $request->type],
                'nilai_lama' => [false],
                'nilai_baru' => [true],
                'keterangan' => 'Persetujuan ' . strtoupper($request->type) . ' oleh ' . $user->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disetujui'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

}
