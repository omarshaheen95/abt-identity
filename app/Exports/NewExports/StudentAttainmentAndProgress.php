<?php

namespace App\Exports\NewExports;

use App\Models\School;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;
use PhpOffice\PhpSpreadsheet\Style\Style;

class StudentAttainmentAndProgress implements WithMapping, WithHeadings, FromArray, WithEvents, ShouldAutoSize
{
    use Exportable;



    public $req;
    public $school;
    private $schoolConfig;
    private $schools;
    private $register_year;
    private $year;
    private $subjects;
    public $student_type;

    public function __construct(Request $request, $schools = [])
    {
        $schools = School::query()->whereIn('id', $schools)->get();
        $this->school = $schools->first();
        $this->schools = $schools;
        $this->req = $request;
        $this->student_type = $request->get('student_type', 2);
        $this->schoolConfig = [
            'columns_per_term' => 10,
            'has_french_system' => false,
            'has_scale' => false,
            'extra_columns' => []
        ];

        $this->year = $request->get('year_id');
        if (in_array($this->school->curriculum_type, ['Indian', 'Pakistan', 'Bangladeshi'])) {
            $this->register_year = $this->year - 1;
        } else {
            $this->register_year = $this->year;
        }

        $this->subjects = Subject::query()->get();
    }



    public function headings(): array
    {
        $baseHeaders = [
            re('Student ID'), re('Student Name'), re('School'), re('Section'),
            re('Grade'), re('Grade Name'), re('Gender'), re('Nationality'),
            re('SEN'), re('G&T'), re('Citizen')
        ];

        $termHeaders = [];
        foreach ($this->getMonthsOrder() as $month) {
            $termHeaders = array_merge($termHeaders, $this->getTermHeaders($month));
        }

        return array_merge($baseHeaders, $termHeaders);
    }

    private function getTermHeaders(int $round): array
    {
        $headers = [re("The Assessment - Round {$round}")];

        foreach ($this->subjects as $subject) {
            $headers[] = $subject->name;
            $headers[] = 'Judgment';
        }

        $headers[] = 'Total';
        $headers = array_merge($headers, $this->schoolConfig['extra_columns']);
        $headers[] = 'Expectations';
        $headers[] = 'The Progress';

        return $headers;
    }

    public function map($row): array
    {
        return $row;
    }

    public function array(): array
    {
        // Optimized: Load all data with eager loading and column selection
        $students = $this->loadStudentsWithRelations();

        $result = [];
        $months = $this->getMonthsOrder();

        foreach ($students as $student) {
            $studentData = $this->buildStudentBaseData($student);
            $termData = $this->buildAllTermsData($student, $months);

            $result[] = array_merge($studentData, $termData);
        }

        return $result;
    }

    private function loadStudentsWithRelations()
    {
        $months = $this->getMonthsOrder();
        $this->req->merge(['year_id' => $this->register_year]);
        return Student::query()
            ->select(['id', 'id_number', 'name', 'grade_name', 'gender', 'nationality', 'sen', 'g_t', 'arab', 'school_id', 'level_id'])
            ->with([
                'level' => function ($query) {
                    $query->select(['id', 'grade', 'arab']);
                },
                'school' => function ($query) {
                    $query->select(['id', 'name','curriculum_type']);
                },
                'student_terms' => function ($query) use ($months) {
                    $query->select([
                        'id', 'student_id', 'term_id',
                        'subjects_marks',
                        'total', 'corrected'
                    ])
                        ->where('corrected', 1)
                        ->with(['term' => function ($termQuery) {
                            $termQuery->select(['id', 'name', 'round']);
                        }]);
                }
            ])
            ->search($this->req)
            ->when($this->student_type == 1, function ($query) {
                $query->whereHas('level', function ($query) {
                    $query->where('arab', 1);
                });
            })
            ->when($this->student_type == 0, function ($query) {
                $query->whereHas('level', function ($query) {
                    $query->where('arab', 0);
                });
            })
            ->latest()
            ->get();
    }

    private function preloadStudentTerms($students): void
    {
        // No longer needed! Data is already loaded via eager loading
        // Keep method for backward compatibility but make it empty
    }

    private function getMonthsOrder(): array
    {
        return in_array($this->school->curriculum_type, ['Indian', 'Pakistan', 'Bangladeshi'])
            ? ['may', 'september', 'february']
            : ['september', 'february', 'may'];
    }

    private function buildStudentBaseData($student): array
    {
        return [
            $student->id_number,
            $student->name,
            $student->school->name,
            $student->level->arab ? 'Arabs' : 'Non-Arabs',
            $student->level->grade,
            $student->grade_name,
            ucfirst($student->gender),
            $student->nationality,
            $student->sen ? "SEN" : '-',
            $student->g_t ? "G&T" : '-',
            $student->citizen ? "Yes" : "No",
        ];
    }

    private function buildAllTermsData(Student $student, array $months): array
    {
        $termData = [];
        $termStudents = [];

        // Get student terms from eager-loaded relationship instead of cache
        $studentTermsByMonth = $student->student_terms->groupBy('term.round');


        // Get term data for each month
        foreach ($months as $month) {
            $termStudents[] = $studentTermsByMonth->get($month, collect())->first();
        }

        // Build data for each term
        for ($i = 0; $i < 3; $i++) {
            $termStudent = $termStudents[$i];
            $progress = $this->calculateProgress($termStudents, $i);
//            if (count($studentTermsByMonth) > 1)
//                dd($termStudents, $i);
            $termData = array_merge($termData, $this->getTermMarks($student, $termStudent, $progress));
        }

        return $termData;
    }


    private function getTermMarks($student, $termStudent, string $progress = '-'): array
    {
        if (!$termStudent) {
            return $this->getEmptyTermMarks($progress);
        }

        $marks = $this->calculateStudentMarks($student, $termStudent);

        return array_merge(
            [$termStudent->term->name],
            $marks,
            ["$termStudent->total"],
            [$termStudent->expectations],
            [$progress]
        );
    }
    private function calculateStudentMarks($student, $termStudent): array
    {
        $marks = [];
        foreach ($this->subjects as $subject) {
            $subject_data = (object)collect($termStudent->subjects_marks)->where('subject_id',$subject->id)->first();
            $markValue = $subject_data->mark ?? "0";
            $marks[] ="$markValue";
            $marks[] = getSubjectAttainment($subject_data, $this->subjects);
        }

        return $marks;
    }


    private function getEmptyTermMarks(string $progress): array
    {
        $emptyCount = 9 ;
        return array_merge(
            array_fill(0, $emptyCount, ''),
            [$progress]//"-"
        );
    }

    private function calculateProgress(array $termStudents, int $currentIndex): string
    {
        // Logic for calculating progress between terms
        if ($currentIndex == 0) return '-';

        $current = $termStudents[$currentIndex];
        $previous = $termStudents[$currentIndex - 1];
        if (!$previous & $currentIndex != 1) {
            $previous = $termStudents[$currentIndex - 2];
        }

        if (!$current || !$previous) return '-';

        $difference = $current->total - $previous->total;
        $progressRate = getProgress($previous->total, $difference);

        switch ($progressRate) {
            case 1:
                return 'Better than expected progress';
            case 0:
                return 'Expected progress';
            default:
                return 'Below expected progress';
        }
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $this->applySheetStyling($event);
                $this->applyConditionalFormatting($event);
            }
        ];
    }

    private function applySheetStyling(AfterSheet $event): void
    {
        $lastCell = $event->sheet->getHighestColumn();
        $lastRow = $event->sheet->getHighestRow();

        // Apply header styling
        $event->sheet->getDelegate()->getStyle("A1:{$lastCell}1")
            ->getFont()->setBold(true)->setSize(12);

        // Apply center alignment to all cells
        $event->sheet->getDelegate()->getStyle("A1:{$lastCell}{$lastRow}")
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Apply bold font to assessment columns (from column H onwards)
        $event->sheet->getDelegate()->getStyle("H1:{$lastCell}{$lastRow}")
            ->getFont()->setBold(true);

        // Apply RTL for Arabic locale
        if (app()->getLocale() == 'ar') {
            $event->sheet->setRightToLeft(true);
        }

        // Apply bold styling to specific assessment round headers
        $this->applyAssessmentColumnStyling($event);

        $this->applyHeaderColor($event);
    }

    private function applyAssessmentColumnStyling(AfterSheet $event): void
    {
        // Since column positions may vary, let's apply styling more conservatively
        // Just ensure the basic header row and data columns are properly styled
        $lastRow = $event->sheet->getHighestRow();

        // Apply bold to all assessment data (starting from column K which contains Islamic subjects data)
        $event->sheet->getDelegate()->getStyle("L1:Z{$lastRow}")
            ->getFont()->setBold(true);

        // If we have more columns, style them too (for schools with more assessment data)
        $lastColumn = $event->sheet->getHighestColumn();
        if ($lastColumn > 'Z') {
            $event->sheet->getDelegate()->getStyle("AA1:{$lastColumn}{$lastRow}")
                ->getFont()->setBold(true);
        }
    }

    private function applyConditionalFormatting(AfterSheet $event): void
    {
        $lastCell = $event->sheet->getHighestColumn();
        $lastRow = $event->sheet->getHighestRow();

        $startColumn = 'L';
        $cellRange = "{$startColumn}1:{$lastCell}{$lastRow}";

        $conditionalStyles = $this->createConditionalStyles($cellRange);

        $event->sheet->getStyle($cellRange)->setConditionalStyles($conditionalStyles);
    }

    private function createConditionalStyles(string $cellRange): array
    {
        $wizardFactory = new Wizard($cellRange);
        $textWizard = $wizardFactory->newRule(Wizard::TEXT_VALUE);

        $styles = [
            'Above' => $this->createColorStyle(Color::COLOR_GREEN),
            'Inline' => $this->createColorStyle('FFC107'),
            'Below' => $this->createColorStyle(Color::COLOR_RED),
            'Better than expected progress' => $this->createColorStyle(Color::COLOR_GREEN),
            'Expected progress' => $this->createColorStyle('FFC107'),
            'Below expected progress' => $this->createColorStyle(Color::COLOR_RED),
        ];

        $conditionalStyles = [];
        foreach ($styles as $text => $style) {
            $textWizard->contains($text)->setStyle($style);
            $conditionalStyles[] = $textWizard->getConditional();
        }

        return $conditionalStyles;
    }

    private function createColorStyle(string $color): Style
    {
        $style = new Style(false, true);
        $style->getFont()->setColor(new Color($color));
        return $style;
    }

    private function applyHeaderColor(AfterSheet $event): void
    {
        //color header for student data
        $event->sheet->getDelegate()->getStyle("A1:J1")
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB("E37200");
        $event->sheet->getDelegate()->getStyle("A1:J1")
            ->getFont()
            ->getColor()
            ->setARGB(Color::COLOR_WHITE);


        $colors = ['444DCD', '808080', '2CC306'];
        $firstColumn = 'K';
        $monthColumnsCount = $this->student_type == 1 ? 12:10;
        foreach ($this->getMonthsOrder() as $index => $month)
        {
            //add year columns count to the first column
            $toColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(
                \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($firstColumn) + $monthColumnsCount - 1
            );

            // Apply background color to the header row for each year
            $event->sheet->getDelegate()->getStyle("{$firstColumn}1:{$toColumn}1")
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB($colors[$index]);
            //color font with white
            $event->sheet->getDelegate()->getStyle("{$firstColumn}1:{$toColumn}1")
                ->getFont()
                ->getColor()
                ->setARGB(Color::COLOR_WHITE);

            // Move to the next starting column for the next year
            $firstColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(
                \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($toColumn) + 1
            );
        }
        $lastCell = $event->sheet->getHighestColumn();
        $lastRow = $event->sheet->getHighestRow();
        // Apply center alignment to all cells
        $event->sheet->getDelegate()->getStyle("A1:{$lastCell}{$lastRow}")
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }

}
