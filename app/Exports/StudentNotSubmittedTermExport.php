<?php

namespace App\Exports;

use App\Models\StudentTerm;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Excel;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class StudentNotSubmittedTermExport implements WithMapping, Responsable, WithHeadings, FromCollection, WithEvents, ShouldAutoSize
{
    use Exportable;

    public $school;
    public $request;
    public function __construct(Request $request, int $school = 0 )
    {
        $this->school = $school;
        $this->request = $request;
    }

    public function headings(): array
    {
        if ($this->school) {
            $headers =  [
                'School',
                'Student ID',
                'Student Name',
                'Username',
                'Gender',
                'Nationality',
                'Grade',
                'Grade Name',
                'Year',
            ];
        } else {
            $headers = [
                'Student ID',
                'Student Name',
                'Username',
                'Gender',
                'Nationality',
                'Grade',
                'Grade Name',
                'Year',
            ];
        }
        // Wrap each header in re() for translation
        return array_map(function($header) {
            return re($header);
        }, $headers);
    }

    public function map($student): array
    {
        return [
            (string)$student->id_number . ' ',
            $student->name,
            $student->email,
            !is_null($student->gender) ? Str::ucfirst($student->gender) : $student->gender,
            $student->nationality,
            $student->level->grade,
            $student->grade_name,
            $student->year->name,
        ];
    }

    public function collection()
    {
        $request =$this->request;

        $students = Student::with('level')->search($request)->whereDoesntHave('student_terms',function (Builder $query) use ($request) {
            $query->when($value = $request->get('round'),function (Builder $query)use ($value){
                $query->whereHas('term',function (Builder $query) use ($value){
                    $query->where('round',$value);
                });
            });
        })->latest();

        if ($this->school != 0){
            $students->where('school_id',$this->school);
        }

        return $students->get();
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
