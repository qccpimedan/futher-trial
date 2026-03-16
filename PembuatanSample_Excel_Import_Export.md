# Dokumentasi Import/Export Excel — Modul Pembuatan Sample

## Tujuan
Fitur ini menyediakan:

- Download **template Excel** untuk input massal data Pembuatan Sample.
- Import file Excel ke database dengan:
  - Validasi per-baris.
  - Mapping master data berdasarkan `id_plan` user (Shift & Produk).
  - Parsing tanggal yang robust (Excel date serial, DateTime, string lokal, dll).
  - Pelaporan error per-baris (maks 20 ditampilkan).

Implementasi menggunakan package **`maatwebsite/excel`** (Laravel-Excel).

---

## Struktur File

- `app/Http/Controllers/PembuatanSampleController.php`
  - `downloadTemplate()` — download template Excel.
  - `importExcel()` — import Excel menggunakan class import.

- `app/Exports/PembuatanSampleTemplateExport.php`
  - `PembuatanSampleTemplateExport` — export multi-sheet (`Template` + `Master`).
  - `PembuatanSampleTemplateSheet` — sheet yang diisi user.
  - `PembuatanSampleMasterSheet` — sheet sumber dropdown (hidden).

- `app/Imports/PembuatanSampleImport.php`
  - `PembuatanSampleImport` — wrapper import multi-sheet (hanya proses sheet `Template`).
  - `PembuatanSampleTemplateSheetImport` — logic parsing/validasi/insert.

- `resources/views/qc-sistem/pembuatan_sample/index.blade.php`
  - Tombol **Import Excel**, modal upload, tombol **Download Template**.
  - Menampilkan `import_errors` dari session.

---

## Alur Fitur

### A. Download Template
1. User klik **Download Template**.
2. Route memanggil `PembuatanSampleController::downloadTemplate()`.
3. Controller menjalankan:

   - `Excel::download(new PembuatanSampleTemplateExport($user), 'template_pembuatan_sample.xlsx')`

4. Export membuat 2 sheet:

- **Template** (untuk user)
  - Header kolom A–J.
  - Validasi dropdown per baris (hingga row 500).
  - Auto-fill formula:
    - `Tanggal Produksi`: `=TODAY()`
    - `Jam`: `=TIME(HOUR(NOW()),MINUTE(NOW()),0)`

- **Master** (hidden)
  - Sumber list dropdown:
    - Produk (kolom A)
    - Shift (kolom B)
    - Jenis Sample (kolom C)
    - Nilai Berat/Gramase (kolom D)

### B. Import Excel
1. User upload file Excel via modal.
2. Route memanggil `PembuatanSampleController::importExcel()`.
3. Controller:

- Validasi file `xlsx/xls`.
- Cek user dan `id_plan`.
- Jalankan:
  - `$import = new PembuatanSampleImport($user);`
  - `Excel::import($import, $request->file('file'));`

4. Setelah selesai:

- Ambil hasil:
  - `$insertedCount = $import->getInsertedCount()`
  - `$importErrors = $import->getImportErrors()`

- Redirect dengan flash message:
  - `success` jika ada data masuk.
  - `warning` jika tidak ada data valid.
  - `info` jika ada sebagian baris gagal.
  - `import_errors` maksimal 20 error.

---

## Format Kolom Excel (Wajib Sesuai)
Sheet: **Template**

| Index | Kolom | Nama Kolom Template | Keterangan |
|------:|------:|---------------------|-----------|
| 0 | A | Shift | Harus ada di master shift plan user |
| 1 | B | Nama Produk | Harus ada di master produk plan user |
| 2 | C | Berat Produk (gram) | angka atau dropdown nilai |
| 3 | D | Gramase (gram) | angka atau dropdown nilai |
| 4 | E | Kode Produksi | wajib |
| 5 | F | Tanggal Produksi | date (boleh kosong, akan default hari ini) |
| 6 | G | Jam | time (boleh kosong, akan default jam sekarang) |
| 7 | H | Tanggal Expired | wajib valid |
| 8 | I | Jumlah | wajib angka |
| 9 | J | Jenis Sample | wajib (dropdown) |

---

## Penjelasan Kode Utama

### 1) Controller — `downloadTemplate()`
- Mengambil user aktif.
- Mengembalikan file via `Excel::download()`.
- Semua pembuatan sheet & styling ada di Export class.

### 2) Export — `PembuatanSampleTemplateExport`
- Menggunakan `WithMultipleSheets`.
- Mengambil master data berdasarkan role:
  - Jika bukan superadmin, filter `JenisProduk` dan `DataShift` berdasarkan `id_plan` user.

#### `PembuatanSampleTemplateSheet`
- Menulis header.
- Menggunakan event `AfterSheet` untuk:
  - Membuat dropdown validation per baris sampai row 500.
  - Menetapkan formula Tanggal/Jam.
  - Mengatur format tampilan tanggal/jam.

#### `PembuatanSampleMasterSheet`
- Menulis list master.
- Menggunakan event `AfterSheet` untuk:
  - Menyembunyikan sheet (`SHEETSTATE_HIDDEN`).

### 3) Import — `PembuatanSampleImport`
Masalah umum saat ada 2 sheet (Template + Master):

- Jika semua sheet diproses, isi sheet `Master` akan terbaca seperti data input → memunculkan banyak error walau data user sudah masuk.

Solusi:

- `PembuatanSampleImport` mengimplement `WithMultipleSheets`.
- `sheets()` hanya memetakan handler untuk sheet bernama **`Template`**.

### 4) Import Handler — `PembuatanSampleTemplateSheetImport`
Tugas utama:

- Ambil mapping master (berdasarkan `id_plan`):
  - `$produkMap[nama_produk] = id`
  - `$shiftMap[shift] = id`

- Loop row excel:
  - Skip row kosong.
  - Validasi per field.
  - Parse tanggal produksi, jam, tanggal expired.
  - Normalisasi numeric (berat, gramase, jumlah).

- Jika ada error:
  - Simpan pesan seperti: `Row X: alasan1; alasan2; ...`

- Jika valid:
  - Tambahkan ke array `insertData`.

- Insert sekali di akhir:
  - `PembuatanSample::insert($insertData)`

---

## Parsing Tanggal (Robust)
Dukungan input tanggal:

- Excel date serial (numeric): menggunakan `ExcelDate::excelToDateTimeObject()`.
- `DateTimeInterface`.
- `RichText` → diambil plain text.
- String format:
  - `d/m/Y`, `d-m-Y`, `Y-m-d`
  - input dengan titik `31.01.2026` (dinormalisasi jadi `31/01/2026`)
  - pola Jepang `YYYY年M月D日`.
  - string yang mengandung waktu: akan diambil format yang cocok / token pertama.

Kebijakan khusus:

- `Tanggal Produksi` boleh kosong → default `today`.
- `Jam` boleh kosong → default jam sekarang.
- `Tanggal Expired` wajib valid.

---

## Error Handling & UX

- Import mengumpulkan error per baris.
- Controller hanya menampilkan maksimal 20 error pertama:
  - `with('import_errors', array_slice($importErrors, 0, 20))`

Tujuan:

- User tetap dapat memasukkan data valid.
- User bisa memperbaiki baris yang gagal tanpa kehilangan semua import.

---

## Roadmap Pembuatan Fitur (Step-by-step)

### Tahap 1 — Setup Dependency
1. Pastikan `maatwebsite/excel` versi 3.x.
2. Pastikan `phpoffice/phpspreadsheet` kompatibel dengan Laravel-Excel (umumnya 1.x).

### Tahap 2 — Buat Template Export
1. Buat Export class.
2. Buat 2 sheet:
   - Template
   - Master (hidden)
3. Tambahkan dropdown validation & formula via `AfterSheet`.

### Tahap 3 — Buat Import Class
1. Wrapper import: `WithMultipleSheets`.
2. Hanya proses sheet `Template`.
3. Buat handler import untuk parsing & validasi.
4. Implement per-row error collection.
5. Insert batch.

### Tahap 4 — Wiring di Controller + UI
1. Tambahkan method `downloadTemplate()`.
2. Tambahkan method `importExcel()`.
3. Tambahkan UI modal upload dan tombol download.
4. Tampilkan error per-row di halaman index.

### Tahap 5 — Testing
1. Import data valid + invalid campuran.
2. Test tanggal:
   - Excel date serial
   - `31/01/2026`
   - `31.01.2026`
3. Pastikan sheet `Master` tidak diproses.

---

## Troubleshooting

### 1) Banyak error tapi data masuk
Penyebab umum:

- Sheet `Master` ikut diproses.

Solusi:

- Gunakan `WithMultipleSheets` dan hanya proses sheet `Template`.

### 2) Error interface `WithMultipleSheets` not found
Penyebab:

- `maatwebsite/excel` versi lama (1.x).

Solusi:

- Upgrade ke Laravel-Excel 3.x.

### 3) Conflict dependency saat upgrade
Gejala:

- `maatwebsite/excel` konflik dengan `phpoffice/phpspreadsheet`.

Solusi:

- Sesuaikan versi `phpoffice/phpspreadsheet` ke 1.x yang kompatibel.

---

## Code Reference (Controller, Export, Import)

### 1) Controller — `PembuatanSampleController.php`

```php
public function downloadTemplate()
{
    $user = Auth::user();
    $filename = 'template_pembuatan_sample.xlsx';

    return Excel::download(new PembuatanSampleTemplateExport($user), $filename);
}

public function importExcel(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls'
    ]);

    $user = Auth::user();

    if (!$user || !$user->id_plan) {
        return redirect()->route('pembuatan-sample.index')->with('error', 'Plan user tidak ditemukan. Tidak dapat melakukan import.');
    }

    $import = new PembuatanSampleImport($user);
    Excel::import($import, $request->file('file'));

    $insertedCount = $import->getInsertedCount();
    $importErrors = $import->getImportErrors();

    if ($insertedCount <= 0) {
        return redirect()->route('pembuatan-sample.index')
            ->with('warning', 'Tidak ada data valid untuk di-import. Pastikan format kolom dan master Shift/Produk sesuai.')
            ->with('import_errors', array_slice($importErrors, 0, 20));
    }

    $successMessage = 'Import data pembuatan sample berhasil. Total: ' . $insertedCount . ' baris.';
    if (!empty($importErrors)) {
        return redirect()->route('pembuatan-sample.index')
            ->with('success', $successMessage)
            ->with('import_errors', array_slice($importErrors, 0, 20))
            ->with('info', 'Ada beberapa baris yang gagal di-import. Silakan cek detail di bawah.');
    }

    return redirect()->route('pembuatan-sample.index')->with('success', $successMessage);
}
```

### 2) Export — `app/Exports/PembuatanSampleTemplateExport.php`

```php
<?php

namespace App\Exports;

use App\Models\DataShift;
use App\Models\JenisProduk;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PembuatanSampleTemplateExport implements WithMultipleSheets
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function sheets(): array
    {
        $produkQuery = JenisProduk::query();
        $shiftQuery = DataShift::query();

        if ($this->user && $this->user->role !== 'superadmin') {
            $produkQuery->where('id_plan', $this->user->id_plan);
            $shiftQuery->where('id_plan', $this->user->id_plan);
        }

        $produkList = $produkQuery->orderBy('nama_produk')->pluck('nama_produk')->toArray();
        $shiftList = $shiftQuery->orderBy('shift')->pluck('shift')->toArray();

        $jenisSampleList = ['sample rnd', 'sample trial', 'sample retain'];
        $nilaiBeratList = ['-', 55, 100, 200, 225, 250, 300, 315, 400, 450, 500, 700, 900, 1000, 1100, 2000];

        $lastProdukRow = max(2, count($produkList) + 1);
        $lastShiftRow = max(2, count($shiftList) + 1);
        $lastJenisRow = max(2, count($jenisSampleList) + 1);
        $lastNilaiRow = max(2, count($nilaiBeratList) + 1);

        return [
            new PembuatanSampleTemplateSheet($lastProdukRow, $lastShiftRow, $lastJenisRow, $lastNilaiRow),
            new PembuatanSampleMasterSheet($produkList, $shiftList, $jenisSampleList, $nilaiBeratList),
        ];
    }
}

class PembuatanSampleTemplateSheet implements FromArray, WithTitle, WithEvents
{
    protected int $lastProdukRow;
    protected int $lastShiftRow;
    protected int $lastJenisRow;
    protected int $lastNilaiRow;

    public function __construct(int $lastProdukRow, int $lastShiftRow, int $lastJenisRow, int $lastNilaiRow)
    {
        $this->lastProdukRow = $lastProdukRow;
        $this->lastShiftRow = $lastShiftRow;
        $this->lastJenisRow = $lastJenisRow;
        $this->lastNilaiRow = $lastNilaiRow;
    }

    public function title(): string
    {
        return 'Template';
    }

    public function array(): array
    {
        return [
            ['Shift', 'Nama Produk', 'Berat Produk (gram)', 'Gramase (gram)', 'Kode Produksi', 'Tanggal Produksi', 'Jam', 'Tanggal Expired', 'Jumlah', 'Jenis Sample'],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->setCellValue('F2', '=TODAY()');
                $sheet->setCellValue('G2', '=TIME(HOUR(NOW()),MINUTE(NOW()),0)');

                for ($row = 2; $row <= 500; $row++) {
                    if ($row > 2) {
                        $sheet->setCellValue('F' . $row, '=TODAY()');
                        $sheet->setCellValue('G' . $row, '=TIME(HOUR(NOW()),MINUTE(NOW()),0)');
                    }

                    $valShift = $sheet->getCell('A' . $row)->getDataValidation();
                    $valShift->setType(DataValidation::TYPE_LIST);
                    $valShift->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $valShift->setAllowBlank(true);
                    $valShift->setShowInputMessage(true);
                    $valShift->setShowErrorMessage(true);
                    $valShift->setShowDropDown(true);
                    $valShift->setFormula1('=Master!$B$2:$B$' . $this->lastShiftRow);

                    $valProduk = $sheet->getCell('B' . $row)->getDataValidation();
                    $valProduk->setType(DataValidation::TYPE_LIST);
                    $valProduk->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $valProduk->setAllowBlank(true);
                    $valProduk->setShowInputMessage(true);
                    $valProduk->setShowErrorMessage(true);
                    $valProduk->setShowDropDown(true);
                    $valProduk->setFormula1('=Master!$A$2:$A$' . $this->lastProdukRow);

                    $valBerat = $sheet->getCell('C' . $row)->getDataValidation();
                    $valBerat->setType(DataValidation::TYPE_LIST);
                    $valBerat->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $valBerat->setAllowBlank(true);
                    $valBerat->setShowInputMessage(true);
                    $valBerat->setShowErrorMessage(true);
                    $valBerat->setShowDropDown(true);
                    $valBerat->setFormula1('=Master!$D$2:$D$' . $this->lastNilaiRow);

                    $valGramase = $sheet->getCell('D' . $row)->getDataValidation();
                    $valGramase->setType(DataValidation::TYPE_LIST);
                    $valGramase->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $valGramase->setAllowBlank(true);
                    $valGramase->setShowInputMessage(true);
                    $valGramase->setShowErrorMessage(true);
                    $valGramase->setShowDropDown(true);
                    $valGramase->setFormula1('=Master!$D$2:$D$' . $this->lastNilaiRow);

                    $valJenis = $sheet->getCell('J' . $row)->getDataValidation();
                    $valJenis->setType(DataValidation::TYPE_LIST);
                    $valJenis->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $valJenis->setAllowBlank(true);
                    $valJenis->setShowInputMessage(true);
                    $valJenis->setShowErrorMessage(true);
                    $valJenis->setShowDropDown(true);
                    $valJenis->setFormula1('=Master!$C$2:$C$' . $this->lastJenisRow);
                }

                $sheet->getStyle('F2:F500')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                $sheet->getStyle('G2:G500')->getNumberFormat()->setFormatCode('hh:mm');
                $sheet->getStyle('F2')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
                $sheet->getStyle('G2')->getNumberFormat()->setFormatCode('hh:mm');
                $sheet->getStyle('F2')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                $sheet->getStyle('G2')->getNumberFormat()->setFormatCode('hh:mm');
            },
        ];
    }
}

class PembuatanSampleMasterSheet implements FromArray, WithTitle, WithEvents
{
    protected array $produkList;
    protected array $shiftList;
    protected array $jenisSampleList;
    protected array $nilaiBeratList;

    public function __construct(array $produkList, array $shiftList, array $jenisSampleList, array $nilaiBeratList)
    {
        $this->produkList = $produkList;
        $this->shiftList = $shiftList;
        $this->jenisSampleList = $jenisSampleList;
        $this->nilaiBeratList = $nilaiBeratList;
    }

    public function title(): string
    {
        return 'Master';
    }

    public function array(): array
    {
        $maxRows = max(
            count($this->produkList),
            count($this->shiftList),
            count($this->jenisSampleList),
            count($this->nilaiBeratList)
        );

        $rows = [];
        $rows[] = ['Produk', 'Shift', 'Jenis Sample', 'Nilai Berat / Gramase'];

        for ($i = 0; $i < $maxRows; $i++) {
            $rows[] = [
                $this->produkList[$i] ?? null,
                $this->shiftList[$i] ?? null,
                $this->jenisSampleList[$i] ?? null,
                $this->nilaiBeratList[$i] ?? null,
            ];
        }

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->setSheetState(Worksheet::SHEETSTATE_HIDDEN);
            },
        ];
    }
}
```

### 3) Import — `app/Imports/PembuatanSampleImport.php`

Catatan:

- Import dibatasi hanya memproses sheet bernama `Template`.
- File ini berisi wrapper (`PembuatanSampleImport`) + handler sheet (`PembuatanSampleTemplateSheetImport`).

```php
<?php

namespace App\Imports;

use App\Models\DataShift;
use App\Models\JenisProduk;
use App\Models\PembuatanSample;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class PembuatanSampleImport implements WithMultipleSheets
{
    protected $user;

    protected array $importErrors = [];

    protected int $insertedCount = 0;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function sheets(): array
    {
        return [
            'Template' => new PembuatanSampleTemplateSheetImport($this),
        ];
    }

    public function getUser()
    {
        return $this->user;
    }

    public function addImportError(string $message): void
    {
        $this->importErrors[] = $message;
    }

    public function setInsertedCount(int $count): void
    {
        $this->insertedCount = $count;
    }

    public function getInsertedCount(): int
    {
        return $this->insertedCount;
    }

    public function getImportErrors(): array
    {
        return $this->importErrors;
    }
}

class PembuatanSampleTemplateSheetImport implements ToCollection, WithStartRow
{
    protected PembuatanSampleImport $parent;

    public function __construct(PembuatanSampleImport $parent)
    {
        $this->parent = $parent;
    }

    public function startRow(): int
    {
        return 2;
    }

    public function collection(Collection $rows)
    {
        $user = $this->parent->getUser();

        if (!$user || !$user->id_plan) {
            $this->parent->addImportError('Plan user tidak ditemukan. Tidak dapat melakukan import.');
            return;
        }

        $produkMap = JenisProduk::where('id_plan', $user->id_plan)
            ->pluck('id', 'nama_produk')
            ->toArray();

        $shiftMap = DataShift::where('id_plan', $user->id_plan)
            ->pluck('id', 'shift')
            ->toArray();

        $insertData = [];

        $parseDateCell = function ($value) {
            if ($value === null || $value === '') {
                return null;
            }

            if ($value instanceof \DateTimeInterface) {
                return Carbon::instance($value)->format('Y-m-d');
            }

            if ($value instanceof RichText) {
                $value = $value->getPlainText();
            }

            if (is_numeric($value)) {
                try {
                    return ExcelDate::excelToDateTimeObject($value)->format('Y-m-d');
                } catch (\Exception $e) {
                    return null;
                }
            }

            $valueStr = trim((string) $value);
            $valueStr = preg_replace('/\s+/', ' ', $valueStr);
            $valueStr = str_replace('.', '/', $valueStr);

            if (preg_match_all('/(\d{4})年(\d{1,2})月(\d{1,2})日/u', $valueStr, $matches, PREG_SET_ORDER)) {
                $last = end($matches);
                $y = (int) ($last[1] ?? 0);
                $mo = (int) ($last[2] ?? 0);
                $d = (int) ($last[3] ?? 0);
                if ($y >= 1900 && $mo >= 1 && $mo <= 12 && $d >= 1 && $d <= 31) {
                    try {
                        return Carbon::create($y, $mo, $d)->format('Y-m-d');
                    } catch (\Exception $e) {
                        // continue
                    }
                }
            }

            $valueStr = preg_replace('/[^0-9\-\/\:\s]/u', '', $valueStr);
            if ($valueStr === '') {
                return null;
            }

            $digitsOnly = preg_replace('/\D/', '', $valueStr);
            if (strlen($digitsOnly) >= 8) {
                $candidates = [];
                $candidates[] = substr($digitsOnly, -8);
                if (strlen($digitsOnly) === 8) {
                    $candidates[] = $digitsOnly;
                }

                foreach (array_unique($candidates) as $cand) {
                    try {
                        return Carbon::createFromFormat('Ymd', $cand)->format('Y-m-d');
                    } catch (\Exception $e) {
                        // continue
                    }
                }
            }

            $valueStrFirstToken = explode(' ', $valueStr)[0] ?? $valueStr;

            $formats = [
                'd/m/Y',
                'd-m-Y',
                'd.m.Y',
                'Y-m-d',
                'Y.m.d',
                'd/m/y',
                'd-m-y',
                'd.m.y',
                'd/m/Y H:i',
                'd/m/Y H:i:s',
                'd-m-Y H:i',
                'd-m-Y H:i:s',
                'Y-m-d H:i',
                'Y-m-d H:i:s',
            ];

            foreach ($formats as $fmt) {
                try {
                    return Carbon::createFromFormat($fmt, $valueStr)->format('Y-m-d');
                } catch (\Exception $e) {
                    // continue
                }

                if ($valueStrFirstToken !== $valueStr) {
                    try {
                        return Carbon::createFromFormat($fmt, $valueStrFirstToken)->format('Y-m-d');
                    } catch (\Exception $e) {
                        // continue
                    }
                }
            }

            try {
                return Carbon::parse($valueStr)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        };

        foreach ($rows as $i => $row) {
            $row = is_array($row) ? $row : $row->toArray();

            $shiftVal = trim((string) ($row[0] ?? ''));
            $namaProduk = trim((string) ($row[1] ?? ''));
            $berat = trim((string) ($row[2] ?? ''));
            $beratSampling = trim((string) ($row[3] ?? ''));
            $kodeProduksi = trim((string) ($row[4] ?? ''));
            $tanggalVal = $row[5] ?? null;
            $jamVal = $row[6] ?? null;
            $tanggalExpiredVal = $row[7] ?? null;
            $jumlahVal = $row[8] ?? null;
            $jenisSample = trim((string) ($row[9] ?? ''));

            if ($shiftVal === '' && $namaProduk === '' && $kodeProduksi === '') {
                continue;
            }

            $excelRow = $i + 2;
            $reasons = [];

            $shiftKey = is_numeric($shiftVal) ? (int) $shiftVal : $shiftVal;
            $idShift = $shiftMap[$shiftKey] ?? null;
            if (!$idShift) {
                $reasons[] = "Shift tidak ditemukan: '{$shiftVal}'";
            }

            $idProduk = $produkMap[$namaProduk] ?? null;
            if (!$idProduk) {
                $reasons[] = "Produk tidak ditemukan: '{$namaProduk}'";
            }

            if ($kodeProduksi === '') {
                $reasons[] = 'Kode Produksi kosong (cek kolom E)';
            }

            $tanggal = $parseDateCell($tanggalVal);
            if (!$tanggal) {
                $tanggal = now()->format('Y-m-d');
            }

            $jam = null;
            if ($jamVal !== null && $jamVal !== '') {
                if (is_numeric($jamVal)) {
                    try {
                        $jam = ExcelDate::excelToDateTimeObject($jamVal)->format('H:i');
                    } catch (\Exception $e) {
                        $jam = null;
                    }
                } else {
                    try {
                        $jam = Carbon::parse($jamVal)->format('H:i');
                    } catch (\Exception $e) {
                        $jam = null;
                    }
                }
            }

            if (!$jam) {
                $jam = now()->format('H:i');
            }

            $tanggalExpired = $parseDateCell($tanggalExpiredVal);
            if (!$tanggalExpired) {
                $rawExpired = null;
                if ($tanggalExpiredVal instanceof RichText) {
                    $rawExpired = $tanggalExpiredVal->getPlainText();
                } elseif ($tanggalExpiredVal instanceof \DateTimeInterface) {
                    $rawExpired = Carbon::instance($tanggalExpiredVal)->toDateTimeString();
                } elseif (is_scalar($tanggalExpiredVal)) {
                    $rawExpired = (string) $tanggalExpiredVal;
                } else {
                    $rawExpired = gettype($tanggalExpiredVal);
                }
                $reasons[] = 'Tanggal Expired tidak valid/kosong (cek kolom H). Nilai: ' . $rawExpired;
            }

            $beratNormalized = ($berat === '' || $berat === '-') ? null : (float) str_replace(',', '.', $berat);
            $beratSamplingNormalized = ($beratSampling === '' || $beratSampling === '-') ? null : (float) str_replace(',', '.', $beratSampling);
            $jumlahNormalized = ($jumlahVal === '' || $jumlahVal === null) ? null : (int) $jumlahVal;

            if ($beratNormalized === null) {
                $reasons[] = 'Berat Produk kosong/tidak valid (cek kolom C)';
            }

            if (!$jumlahNormalized) {
                $reasons[] = 'Jumlah kosong/tidak valid (cek kolom I)';
            }

            if ($jenisSample === '') {
                $reasons[] = 'Jenis Sample kosong (cek kolom J)';
            }

            if ($kodeProduksi === '' && is_string($tanggalExpiredVal) && $tanggalExpiredVal !== '' && $tanggalExpired === null) {
                $reasons[] = 'Kemungkinan kolom bergeser (Kode Produksi terisi di kolom H/Tanggal Expired)';
            }

            if (!empty($reasons)) {
                $this->parent->addImportError('Row ' . $excelRow . ': ' . implode('; ', $reasons));
                continue;
            }

            $insertData[] = [
                'uuid' => (string) Str::uuid(),
                'id_produk' => $idProduk,
                'id_plan' => $user->id_plan,
                'id_shift' => $idShift,
                'kode_produksi' => $kodeProduksi,
                'tanggal' => $tanggal . ' ' . Carbon::parse($jam)->format('H:i:s'),
                'jam' => Carbon::parse($jam)->format('H:i:s'),
                'tanggal_expired' => $tanggalExpired,
                'jumlah' => $jumlahNormalized,
                'berat' => $beratNormalized,
                'berat_sampling' => $beratSamplingNormalized,
                'jenis_sample' => $jenisSample,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($insertData)) {
            PembuatanSample::insert($insertData);
            $this->parent->setInsertedCount(count($insertData));
        }
    }
}
```

## Catatan Teknis
- Template dibatasi sampai row 500 untuk dropdown dan formula agar performa Excel tetap baik.
- Import dilakukan batch insert untuk performa.
- Semua mapping master mengikuti `id_plan` user agar dropdown dan validasi konsisten.
