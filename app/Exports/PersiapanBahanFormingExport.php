<?php

namespace App\Exports;

use App\Models\PersiapanBahanForming;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PersiapanBahanFormingExport implements FromArray, WithHeadings, WithStyles
{
    protected $uuid;

    public function __construct($uuid = null)
    {
        $this->uuid = $uuid;
    }

    public function array(): array
    {
        $user = auth()->user();
        
        $query = PersiapanBahanForming::with([
            'plan',
            'formula.produk',
            'suhuForming.bahanForming',
            'shift',
            'aktualSuhuAdonan',
            'suhuAdonan'
        ])
        ->when($user->role !== 'superadmin', function($q) use ($user) {
            $q->where('plan_id', $user->id_plan);
        });

        // If UUID is provided, export single record
        if ($this->uuid) {
            $query->where('uuid', $this->uuid);
        }

        $data = $query->orderBy('created_at', 'desc')->get();
        $rows = [];
        $no = 1;

        foreach ($data as $item) {
            foreach ($item->suhuForming as $index => $suhu) {
                $rows[] = [
                    $index == 0 ? $no : '',
                    $index == 0 ? ($item->plan->nama_plan ?? '-') : '',
                    $index == 0 ? ($item->shift->shift ?? '-') : '',
                    $index == 0 ? ($item->tanggal ? $item->tanggal->format('d-m-Y H:i:s') : '-') : '',
                    $index == 0 ? ($item->formula->produk->nama_produk ?? '-') : '',
                    $index == 0 ? ($item->formula->nomor_formula ?? '-') : '',
                    $index == 0 ? ($item->kode_produksi_emulsi ?? '-') : '',
                    $suhu->bahanForming->nama_rm ?? '-',
                    $suhu->suhu . '°C',
                    $index == 0 ? ($item->suhuAdonan->std_suhu ?? '-') : '',
                    $index == 0 ? ($item->aktualSuhuAdonan->aktual_suhu_1 ?? '-') : '',
                    $index == 0 ? ($item->aktualSuhuAdonan->aktual_suhu_2 ?? '-') : '',
                    $index == 0 ? ($item->aktualSuhuAdonan->aktual_suhu_3 ?? '-') : '',
                    $index == 0 ? ($item->aktualSuhuAdonan->aktual_suhu_4 ?? '-') : '',
                    $index == 0 ? ($item->aktualSuhuAdonan->aktual_suhu_5 ?? '-') : '',
                    $index == 0 ? ($item->aktualSuhuAdonan->total_aktual_suhu ? number_format($item->aktualSuhuAdonan->total_aktual_suhu, 1) . '°C' : '-') : '',
                    $index == 0 ? ($item->waktu_mulai_mixing ?? '-') : '',
                    $index == 0 ? ($item->waktu_selesai_mixing ?? '-') : '',
                    $index == 0 ? ($item->kode_produksi_emulsi_oil ?? '-') : '',
                    $index == 0 ? ($item->rework ?? '-') : '',
                    $index == 0 ? ($item->kondisi ?? '-') : '',
                    $index == 0 ? ($item->catatan ?? '-') : '',
                ];
            }
            $no++;
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'No',
            'Plan',
            'Shift',
            'Tanggal',
            'Nama Produk',
            'Nomor Formula',
            'Kode Produksi Emulsi',
            'Bahan Forming',
            'Suhu RM (°C)',
            'Suhu Adonan STD (°C)',
            'Aktual Suhu 1 (°C)',
            'Aktual Suhu 2 (°C)',
            'Aktual Suhu 3 (°C)',
            'Aktual Suhu 4 (°C)',
            'Aktual Suhu 5 (°C)',
            'Total Aktual Suhu (°C)',
            'Waktu Mulai Mixing',
            'Waktu Selesai Mixing',
            'Kode Produksi Emulsi Oil',
            'Rework (gram)',
            'Kondisi',
            'Catatan'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
