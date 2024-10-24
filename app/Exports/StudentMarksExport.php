<?php

namespace App\Exports;

use App\Models\Inspection;
use App\Models\School;
use App\Models\Student;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentMarksExport implements WithMapping, Responsable, WithHeadings, FromCollection, WithEvents, ShouldAutoSize,WithStyles
{
    use Exportable;
    public $length;
    public $request;
    private $schools_ids;

    public function __construct(Request $request,array $schools_ids=null)
    {
        $this->request = $request;
        $this->length = 1;
        $this->schools_ids = $schools_ids;
    }

    public function headings(): array
    {
        $headers = [
            'Name',
            'ID',
            'Gender',
            'Nationality',
            'Grade Name',
            'The Assessment - Round 1',
            'Total',
            'Attainment & Expectations',

            'The Assessment - Round 2',
            'Total',
            'Attainment & Expectations',

            'The Assessment - Round 3',
            'Total',
            'Attainment & Expectations',
        ];

        // Wrap each header in re() for translation
        return array_map(function($header) {
            return re($header);
        }, $headers);
    }

    public function map($row): array
    {
       // dd($row->toArray());
        $row_data =  [
            $row->name,
            $row->id_number,
            $row->gender,
            $row->nationality,
            $row->grade_name,
        ];
        // 'september', 'february', 'may',

        if ($row->student_terms){
            foreach (['september','february', 'may'] as $round){
                $student_term = collect($row->student_terms)->where('term.round','=',$round)->first();
                if ($student_term){
                    //dump($student_term->toArray());
                    $row_data[]= $student_term->term->name;
                    $row_data[]= $student_term->total;
                    $row_data[]=  $student_term->expectation;
                }else{
                    $row_data[]=  '';
                    $row_data[]=  '';
                    $row_data[]=  '';
                    //dump('null');

                }
            }


        }else{
            foreach ([0,1,2] as $item){
                $row_data[]=  '';
                $row_data[]=  '';
                $row_data[]=  '';
            }
        }
        return $row_data;
    }

    public function collection()
    {
        if ($this->schools_ids){
            $rows = Student::with(['student_terms'])
                ->whereIn('school_id',$this->schools_ids)->search($this->request)
                ->latest();
        }else{
            $rows = Student::with(['student_terms'])->search($this->request)
                ->latest();
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
        Sheet::macro('cStyleCells', function (Sheet $sheet, string $cellRange, array $style) {
            $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
            if (app()->getLocale() == 'ar'){
                $sheet->setRightToLeft(true);
            }
        });
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:N1';
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold('bold')->setSize(12);
                $event->sheet->cStyleCells(
                    "A1:N$this->length",
                    [
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        ],
                    ]
                );
                $event->sheet->cStyleCells(
                    "A1:N1",
                    [
                        'fill' => [
                            'fillType'   => Fill::FILL_SOLID,
                            'startColor' => ['argb' => Color::COLOR_YELLOW],
                        ],
                        'font' => [
                            'color' => ['argb' => Color::COLOR_RED],
                        ],

                    ]
                );

                $event->sheet->cStyleCells(
                    "A1:N1",
                    [
                        'fill' => [
                            'fillType'   => Fill::FILL_SOLID,
                            'startColor' => ['argb' => Color::COLOR_YELLOW],
                        ],
                        'font' => [
                            'color' => ['argb' => Color::COLOR_RED],
                        ],

                    ]
                );
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
       return [
           'G'  => ['font' => ['bold' => true]],
           'H'  => ['font' => ['bold' => true,'color' => ['argb' => Color::COLOR_RED]]],

           'J'  => ['font' => ['bold' => true]],
           'K'  => ['font' => ['bold' => true,'color' => ['argb' => Color::COLOR_RED]]],

           'M'  => ['font' => ['bold' => true]],
           'N'  => ['font' => ['bold' => true,'color' => ['argb' => Color::COLOR_RED]]],
       ];
    }
}
