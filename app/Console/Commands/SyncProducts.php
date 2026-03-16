<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\JenisProduk;
use Illuminate\Support\Str;

class SyncProducts extends Command
{
    protected $signature = 'sync:products';
    protected $description = 'Tarik data produk terbaru dari Web-Product Pusat';

    public function handle()
    {
        // Ganti dengan URL dan Token asli dari API Gateway Web-Product
        $endpoint = 'http:/10.68.1.37/products-trial/api/products?type=label';
        $token = '1|7kixIWyjXs2PFuFjjdbUwKtmSdcGZhAzRAG0PpF9b6bf3034';

        $this->info('Menghubungi server pusat...');

        $response = Http::withToken($token)->acceptJson()->get($endpoint);

        if ($response->successful()) {
            $products = $response->json()['data'];

            foreach ($products as $item) {
                JenisProduk::updateOrCreate(
                    // Cocokkan berdasarkan ID Pusat
                    ['id_produk_pusat' => $item['id_produk']], 
                    
                    // Update data terbaru
                    [
                        'nama_produk' => $item['nama_produk'],
                        'uuid' => $item['uuid'] ?? (string) Str::uuid(),
                        'updated_at' => now(),
                    ]
                );
            }
            $this->info('Sinkronisasi berhasil! ' . count($products) . ' produk diperbarui.');
        } else {
            $this->error('Gagal sinkronisasi. Cek koneksi atau token.');
        }
    }
}