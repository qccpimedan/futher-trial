<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\AuthController;
use App\Models\JenisProduk;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/user-sync', [ApiController::class, 'syncUser']);
Route::post('/user-desync', [ApiController::class, 'desyncUser']);
Route::get('/heartbeat', [AuthController::class, 'check']);
Route::post('/password-change', [ApiController::class, 'passwordChange']); 
Route::post('/activation', [ApiController::class, 'activation']);
Route::post('/plant-sync', [ApiController::class, 'syncPlant']);

// Sync Products

Route::post('/receive-products', function (Request $request) {
    // 1. Validasi Kunci Rahasia Statis
    if ($request->bearerToken() !== "CPI12345") {
        return response()->json(['message' => 'Akses Ditolak: Kunci Salah!'], 401);
    }

    // 2. Proses Data dengan "Smart Mapping"
    // Logika ini mencegah duplikasi dan menjaga relasi tabel lama (seperti formula)
    $products = $request->products;

    if ($products) {
        foreach ($products as $item) {
            // A. Cari apakah produk ini sudah pernah tersambung ke pusat?
            $produk = JenisProduk::where('id_produk_pusat', $item['id_produk'])->first();

            if (!$produk) {
                // B. Jika belum, cari apakah ada produk lama dengan NAMA yang sama?
                // Ini penting agar relasi ke tabel formula/transaksi lama tidak rusak.
                $produk = JenisProduk::where('nama_produk', $item['nama_produk'])
                                     ->whereNull('id_produk_pusat')
                                     ->first();
            }

            if ($produk) {
                // C. Jika ditemukan (lewat ID atau Nama), update datanya saja.
                // ID Lokal tetap sama, sehingga data formula tetap aman.
                $produk->update([
                    'id_produk_pusat' => $item['id_produk'],
                    'nama_produk'     => $item['nama_produk'],
                    'updated_at'      => now()
                ]);
            } else {
                // D. Jika benar-benar produk baru, buat baris baru.
                JenisProduk::create([
                    'id_produk_pusat' => $item['id_produk'],
                    'nama_produk'     => $item['nama_produk'],
                    'id_plan'         => 1,
                    'user_id'         => 1,
                ]);
            }
        }
        return response()->json(['message' => 'Data Berhasil Disinkronisasi']);
    }
    return response()->json(['message' => 'Data Kosong'], 400);
});

