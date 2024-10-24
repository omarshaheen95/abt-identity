<?php

namespace App\Exports;

use App\Models\Inspection;
use App\Models\School;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class InspectionExport implements WithMapping, Responsable, WithHeadings, FromCollection, WithEvents, ShouldAutoSize
{
    use Exportable;
    public $length;
    public $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->length = 1;
    }

    public function headings(): array
    {
        $headers =  [
            'Name',
            'Email',
            'Schools',
        ];

        // Wrap each header in re() for translation
        return array_map(function($header) {
            return re($header);
        }, $headers);
    }

    public function map($row): array
    {
        $data =  [
            $row->name,
            $row->email,
        ];

        if (!is_null($row->inspection_schools)){
            $schools = '';
            foreach ( $row->inspection_schools as $i_school){
                $schools= $schools.' - '.$i_school->school->name;
            }
           $data['schools'] = $schools;
        }

        return $data;

    }

    public function collection()
    {
        $rows = Inspection::with(['inspection_schools.school'])->search($this->request)
            ->latest();

        if ($rows->count() >= 1) {
            $this->length = $rows->count() + 1;
        }
        $this->length = $rows->count() + 1;
        return $rows->get();
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
                $cellRange = 'A1:C1';
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold('bold')->setSize(12);
                $event->sheet->styleCells(
                    "A1:C$this->length",
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
