<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Check StdFan data
echo "=== Checking StdFan Table Data ===\n";
$stdFans = \App\Models\StdFan::with(['produk', 'suhuBlok'])->get();

foreach ($stdFans->take(10) as $stdFan) {
    echo "ID: {$stdFan->id}\n";
    echo "Produk: " . ($stdFan->produk->nama_produk ?? 'N/A') . "\n";
    echo "Suhu Blok: " . ($stdFan->suhuBlok->suhu_blok ?? 'N/A') . "\n";
    echo "Fan 3: " . ($stdFan->fan_3 ?? 'NULL') . "\n";
    echo "Fan 4: " . ($stdFan->fan_4 ?? 'NULL') . "\n";
    echo "Std Lama Proses: " . ($stdFan->std_lama_proses ?? 'NULL') . "\n";
    echo "---\n";
}

// Check ProsesRoastingFan data
echo "\n=== Checking ProsesRoastingFan Data ===\n";
$prosesRoastingFans = \App\Models\ProsesRoastingFan::with(['stdFan'])->take(5)->get();

foreach ($prosesRoastingFans as $proses) {
    echo "UUID: {$proses->uuid}\n";
    echo "Is Grouped: " . ($proses->is_grouped ? 'Yes' : 'No') . "\n";
    
    if ($proses->is_grouped && $proses->blok_data) {
        echo "Blok Data Count: " . count($proses->blok_data) . "\n";
        foreach ($proses->blok_data as $index => $blok) {
            $stdFan = \App\Models\StdFan::find($blok['id_std_fan'] ?? null);
            echo "  Blok {$index}: StdFan ID = " . ($blok['id_std_fan'] ?? 'NULL') . "\n";
            if ($stdFan) {
                echo "    Fan 3: " . ($stdFan->fan_3 ?? 'NULL') . "\n";
                echo "    Fan 4: " . ($stdFan->fan_4 ?? 'NULL') . "\n";
                echo "    Std Lama Proses: " . ($stdFan->std_lama_proses ?? 'NULL') . "\n";
            }
        }
    } else {
        echo "StdFan ID: " . ($proses->id_std_fan ?? 'NULL') . "\n";
        if ($proses->stdFan) {
            echo "  Fan 3: " . ($proses->stdFan->fan_3 ?? 'NULL') . "\n";
            echo "  Fan 4: " . ($proses->stdFan->fan_4 ?? 'NULL') . "\n";
            echo "  Std Lama Proses: " . ($proses->stdFan->std_lama_proses ?? 'NULL') . "\n";
        }
    }
    echo "---\n";
}
