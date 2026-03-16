<?php

namespace App\Exports;

use App\Models\DataShift;
use App\Models\DataTimbangan;
use App\Models\Timbangan;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TimbanganTemplateExport implements WithMultipleSheets
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function sheets(): array
    {
        $shiftQuery = DataShift::query();
        $timbanganQuery = DataTimbangan::query();

        if ($this->user && $this->user->role !== 'superadmin') {
            $shiftQuery->where('id_plan', $this->user->id_plan);
            $timbanganQuery->where('id_plan', $this->user->id_plan);
        }

        $shiftList = $shiftQuery->orderBy('shift')->pluck('shift')->toArray();
        $timbanganList = $timbanganQuery->orderBy('nama_timbangan')->pluck('nama_timbangan')->toArray();
        $hasilOptions = array_keys(Timbangan::getHasilPengecekanOptions());
        $gramOptions = array_keys(Timbangan::getGramOptions());

        return [
            new TimbanganTemplateSheet(
                count($shiftList),
                count($timbanganList),
                count($hasilOptions),
                count($gramOptions)
            ),
            new TimbanganMasterSheet($shiftList, $timbanganList, $hasilOptions, $gramOptions),
        ];
    }
}

class TimbanganTemplateSheet implements FromArray, WithTitle, WithEvents
{
    protected int $shiftCount;
    protected int $timbanganCount;
    protected int $hasilCount;
    protected int $gramCount;

    public function __construct($shiftCount, $timbanganCount, $hasilCount, $gramCount)
    {
        $this->shiftCount = $shiftCount;
        $this->timbanganCount = $timbanganCount;
        $this->hasilCount = $hasilCount;
        $this->gramCount = $gramCount;
    }

    public function title(): string
    {
        return 'Template';
    }

    public function array(): array
    {
        return [
            [
                'Shift',
                'Tanggal',
                'Jam',
                'Nama Timbangan',
                'Hasil Pengecekan (ok/tidak_ok)',
                'Gram (500/1000)',
                'Hasil Verifikasi 500',
                'Hasil Verifikasi 1000'
            ],
            [
                null,
                date('d-m-Y'),
                date('H:i'),
                null,
                'ok',
                '500',
                '0',
                null
            ]
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                for ($row = 2; $row <= 1000; $row++) {
                    // Shift Validation (Column A)
                    $valShift = $sheet->getCell('A' . $row)->getDataValidation();
                    $valShift->setType(DataValidation::TYPE_LIST);
                    $valShift->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $valShift->setAllowBlank(true);
                    $valShift->setShowInputMessage(true);
                    $valShift->setShowErrorMessage(true);
                    $valShift->setShowDropDown(true);
                    $valShift->setFormula1('=Master!$A$2:$A$' . ($this->shiftCount + 1));

                    // Timbangan Validation (Column D)
                    $valTimbangan = $sheet->getCell('D' . $row)->getDataValidation();
                    $valTimbangan->setType(DataValidation::TYPE_LIST);
                    $valTimbangan->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $valTimbangan->setAllowBlank(true);
                    $valTimbangan->setShowInputMessage(true);
                    $valTimbangan->setShowErrorMessage(true);
                    $valTimbangan->setShowDropDown(true);
                    $valTimbangan->setFormula1('=Master!$B$2:$B$' . ($this->timbanganCount + 1));

                    // Hasil Validation (Column E)
                    $valHasil = $sheet->getCell('E' . $row)->getDataValidation();
                    $valHasil->setType(DataValidation::TYPE_LIST);
                    $valHasil->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $valHasil->setAllowBlank(true);
                    $valHasil->setShowInputMessage(true);
                    $valHasil->setShowErrorMessage(true);
                    $valHasil->setShowDropDown(true);
                    $valHasil->setFormula1('=Master!$C$2:$C$' . ($this->hasilCount + 1));

                    // Gram Validation (Column F)
                    $valGram = $sheet->getCell('F' . $row)->getDataValidation();
                    $valGram->setType(DataValidation::TYPE_LIST);
                    $valGram->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $valGram->setAllowBlank(true);
                    $valGram->setShowInputMessage(true);
                    $valGram->setShowErrorMessage(true);
                    $valGram->setShowDropDown(true);
                    $valGram->setFormula1('=Master!$D$2:$D$' . ($this->gramCount + 1));
                }

                // Set date and time format
                $sheet->getStyle('B2:B1000')->getNumberFormat()->setFormatCode('dd-mm-yyyy');
                $sheet->getStyle('C2:C1000')->getNumberFormat()->setFormatCode('hh:mm');

                foreach (range('A', 'H') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}

class TimbanganMasterSheet implements FromArray, WithTitle, WithEvents
{
    protected array $shiftList;
    protected array $timbanganList;
    protected array $hasilOptions;
    protected array $gramOptions;

    public function __construct($shiftList, $timbanganList, $hasilOptions, $gramOptions)
    {
        $this->shiftList = $shiftList;
        $this->timbanganList = $timbanganList;
        $this->hasilOptions = $hasilOptions;
        $this->gramOptions = $gramOptions;
    }

    public function title(): string
    {
        return 'Master';
    }

    public function array(): array
    {
        $maxRows = max(
            count($this->shiftList),
            count($this->timbanganList),
            count($this->hasilOptions),
            count($this->gramOptions)
        );

        $rows = [];
        $rows[] = ['Shift', 'Nama Timbangan', 'Hasil Pengecekan', 'Gram'];

        for ($i = 0; $i < $maxRows; $i++) {
            $rows[] = [
                $this->shiftList[$i] ?? null,
                $this->timbanganList[$i] ?? null,
                $this->hasilOptions[$i] ?? null,
                $this->gramOptions[$i] ?? null,
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
