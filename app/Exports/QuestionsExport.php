<?php

namespace App\Exports;

use App\Models\Level;
use App\Models\Manager;
use App\Models\Question;
use App\Models\Term;
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

class QuestionsExport implements WithMapping, Responsable, WithHeadings, FromCollection, WithEvents, ShouldAutoSize
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
        $headers =  [
            'ID',
            'Assessment Name',
            'Content',
            'Subject',
            'Type',
            'Year',
            'Level',
            'Mark',
        ];

        // Wrap each header in re() for translation
        return array_map(function($header) {
            return re($header);
        }, $headers);
    }

    public function map($row): array
    {
        $type = '';
        if ($row->type == 1){
            $type= re('TrueOrFalse');
        }elseif ($row->type == 2){
            $type= re('Choose').'|'.re('subject');
        }elseif ($row->type == 3){
            $type= re('Sort');
        }elseif ($row->type == 4){
            $type= re('Match');
        }elseif ($row->type == 5){
            $type= re('Article');
        }
        return [
            $row->id,
            $row->term['name'],
            $row->content,
            $row->subject,
            $type,
            $row->term['level']['year']['name'],
            $row->term['level']['name'],
            $row->mark
        ];

    }

    public function collection()
    {
        return Question::with('term.level.year')->search($this->request)->latest()->get();
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

