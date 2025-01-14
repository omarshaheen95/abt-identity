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
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class StudentExport implements WithMapping, Responsable, WithHeadings, FromCollection, WithEvents, ShouldAutoSize
{
    use Exportable;
    public $length;
    public $request;
    private $school_ids;
    public function __construct(Request $request,array $school_ids = null)
    {
        $this->request = $request;
        $this->length = 1;
        $this->school_ids=$school_ids;
    }

    public function headings(): array
    {
        $headers =  [
            'Student ID',
            'Name',
            'Email',
            'School',
            'Year',
            'Level',
            'Nationality',
            'Grade',
            'Grade Name',
            'Sen',
            'G&T',
            'Arab',
            'Citizen',
            'Gender',
            'Date Of Birth',
            'Citizen',
            'Last Login',
            'Devise Info',
        ];

        // Wrap each header in re() for translation
        return array_map(function($header) {
            return re($header);
        }, $headers);
    }

    public function map($row): array
    {
        return [
            $row->id_number,
            $row->name,
            $row->email,
            $row->school->name,
            $row->year->name,
            $row->level->name,
            $row->nationality?:'-',
            $row->level->grade,
            $row->grade_name,
            $row->sen?re('SEN'):'-',
            $row->g_t?re('Yes'):re('No'),
            $row->arab?re('Arab'):re('Non-Arab'),
            $row->citizen?re('Yes'):re('No'),
            $row->gender,
            $row->dob,
            $row->citizen?re('Citizen'):re('Non Citizen'),
            $row->last_login,
            $row->last_login_info,
        ];
    }

    public function collection()
    {
        $rows = Student::query()->with(['school','year','level'])->search($this->request);

        if ($this->school_ids){
            $rows = $rows->whereIn('school_id',$this->school_ids)->latest();
        }else{
            $rows = $rows->latest();
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
                $this->last_cell = $event->sheet->getHighestColumn();
                $this->last_row = $event->sheet->getHighestRow();
                $cellRange = 'A1:'.$this->last_cell.'1';
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
