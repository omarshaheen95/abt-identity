<?php

namespace App\Exports;

use App\Models\School;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
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

class SchoolExport implements WithMapping, Responsable, WithHeadings, FromCollection, WithEvents, ShouldAutoSize
{
    use Exportable;
    public $length;
    public $request;
    public $builder;
    public function __construct(Request $request,Builder $builder = null)
    {
        $this->request = $request;
        $this->length = 1;
        $this->builder = $builder;
    }

    public function headings(): array
    {
        $headers =  [
            'Name AR',
            'Name EN',
            'Email',
            'URL',
            'Mobile',
            'Country',
            'Curriculum Type',
        ];

        // Wrap each header in re() for translation
        return array_map(function($header) {
            return re($header);
        }, $headers);
    }

    public function map($row): array
    {
        return [
            $row->getTranslation('name', 'ar'),
            $row->getTranslation('name', 'en'),
            $row->email,
            $row->url,
            $row->mobile,
            $row->country,
            $row->curriculum_type,
        ];
    }

    public function collection()
    {
        if (!$this->builder){
            $rows = School::query()->search($this->request)
                ->latest();
        }else{
            $rows = $this->builder;
        }

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
                $cellRange = 'A1:G1';
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold('bold')->setSize(12);
                $event->sheet->styleCells(
                    "A1:G$this->length",
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
