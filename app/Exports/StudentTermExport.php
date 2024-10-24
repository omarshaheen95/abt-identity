<?php

namespace App\Exports;

use App\Models\Level;
use App\Models\Manager;
use App\Models\StudentTerm;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class StudentTermExport implements WithMapping, Responsable, WithHeadings, FromCollection, WithEvents, ShouldAutoSize
{
    use Exportable;

    public $request;
    public $last_cell;
    public $last_row;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function headings(): array
    {
        $headers = [
            'Name',
            'Email',
            'School',
            'Year',
            'Level',
            'Mark',
            'Corrected',
            'Start At',
            'End At',
        ];

        // Wrap each header in re() for translation
        return array_map(function($header) {
            return re($header);
        }, $headers);
    }

    public function map($row): array
    {
        return [
            $row->student->name,
            $row->student->email,
            $row->student->school->name,
            $row->student->year->name,
            $row->student->level->name,
            $row->total,
            $row->corrected?re('Corrected'):re('Uncorrected'),
            $row->start_at,
            $row->end_at,

        ];
    }

    public function collection()
    {
        $request = $this->request;
        return StudentTerm::query()->with(['student.year','student.level','student.school'])->search($request)->latest()->get();
    }

    public function drawings()
    {
        return new Drawing();
    }

    public function registerEvents(): array
    {
        Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
            $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
            if (app()->getLocale() == 'ar'){
                $sheet->setRightToLeft(true);
            }
        });
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $this->last_cell = $event->sheet->getHighestColumn();
                $this->last_row = $event->sheet->getHighestRow();
                $cellRange = 'A'.$this->last_row.':'.$this->last_cell.'1';
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold('bold')->setSize(12);
                $event->sheet->styleCells(
                    "A1:$this->last_cell$this->last_row",
                    [
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        ],

                    ]
                );
            },
        ];

    }
}

