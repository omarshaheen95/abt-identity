<?php

namespace App\Exports\NewExports;

use App\Models\Subject;
use App\Models\Year;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentTerm;
use App\Models\Term;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class YearToYearProgressExport implements WithMapping, Responsable, WithHeadings, FromArray, WithEvents, ShouldAutoSize
{
    use Exportable;


    private $years;
    private $school;
    private $schools;
    private $request;
    private $students;
    private $round_name;
    private $round;
    private $reversedYears;
    private $selectedGrades;
    private $student_type;
    private $lastYear;
    private $yearCollection;
    private $reportRanges;
    private $markData;
    private $header;
    private $subjects;

    public function __construct(Request $request, $schools = [])
    {
        $schools = School::query()->whereIn('id', $schools)->get();
        $this->school = $schools->first();
        $this->schools = $schools;
        $this->request = $request;
        $this->subjects = Subject::query()->get();

        $this->initializeReportParameters();
    }

    public function headings(): array
    {
        $students_name = [
            re('Student ID'), re('Student Name'), re('School'),
            re('Grade'), re('Gender'), re('Nationality'),
            re('SEN'), re('G&T'), re('Citizen')
        ];

        $term_info = [];
        $sub_info = [];
        foreach ($this->yearCollection as $year) {
            $year_data[] =  $year->name . " - " . re($this->round . ' Round');

            foreach ($this->subjects as $subject) {
                $year_data[] = $subject->name;
            }
            $year_data[] = re('Total');
            $year_data[] = re('Attainment & Expectations');
            $year_data[] =re('The Progress');

            $sub_info = array_merge($sub_info, $year_data);
        }

        $term_info = array_merge($term_info, $sub_info);

        return array_merge($students_name, $term_info);
    }

    public function map($row): array
    {
        return $row;
    }

    public function drawings()
    {
        return new Drawing();
    }

    public function array(): array
    {
        $students_results = [];
        if ($this->student_type == 2) {
            $student_types = [1, 0];
        } else {
            $student_types = [$this->student_type];
        }
        foreach ($student_types as $type) {
            $this->student_type = $type;
            $studentsAbtIdsByGrade = $this->getEligibleStudentsOptimized();
            foreach ($studentsAbtIdsByGrade as $grade => $abtIds) {
                if (empty($abtIds)) {
                    continue;
                }
                $lastGrade = null;
                if (is_null($lastGrade)) {
                    $lastGrade = $grade;
                }
                $grade_data = collect();
                foreach ($this->reversedYears as $year) {
                    $result = $this->selectData($abtIds, $year, $lastGrade);
                    $grade_data = $grade_data->merge($result);
                    $lastGrade--;
                }
                $grade_data = $grade_data->groupBy('abt_id')->unique();
                $student_data = [];
                foreach ($grade_data as $abt_id => $years_data) {
                    if (count($years_data) < count($this->reversedYears)) {
                        continue; // Skip students who don't have data for all years
                    }
                    $student = array_first($years_data);
                    $student_data = $this->buildStudentBaseData($student);
                    $termsData = $this->buildAllTermsData($student, $years_data);
                    $students_results[] = array_merge($student_data, $termsData);
                }
            }
        }
        return $students_results;
    }

    private function buildStudentBaseData($student): array
    {
        $school = $this->schools->where('id', $student->school_id)->first();
        return [
            $student->id_number,
            $student->student_name,
            $school->name,
            $student->grade,
            ucfirst($student->gender),
            $student->nationality,
            $student->sen ? re('Yes') : re('No'),
            $student->g_t ? re('Yes') : re('No'),
            $student->citizen ? re('Yes') : re('No'),
        ];
    }

    private function buildAllTermsData($student, $years_data): array
    {
        $termData = [];
        $termStudents = [];

        $first_year = null;
        $first_grade = null;
        // Build data for each term
        foreach (array_reverse($years_data->toArray()) as $year_data) {
            if (is_null($first_year)) {
                $progress = '-';
                $first_year = $year_data;
            } else {
                $progress = $this->calculateProgress($first_year, $year_data);
                $first_year = $year_data;
            }
            $termData = array_merge($termData, $this->getTermMarks($student, $year_data, $progress));
        }

        return $termData;
    }

    private function calculateProgress($first_year, $year_data): string
    {
        $first_mark = $first_year->total;
        $current_mark = $year_data->total;

        if (!$first_year || !$year_data) return '-';

        $difference = $current_mark - $first_mark;
        $progressRate = getProgress($first_mark, $difference);

        switch ($progressRate) {
            case 1:
                return 'Better than expected progress';
            case 0:
                return 'Expected progress';
            default:
                return 'Below expected progress';
        }
    }

    private function getTermMarks($student, $year_data, $progress = '-'): array
    {
        if (!$year_data) {
            return $this->getEmptyTermMarks($progress);
        }

        $marks = $this->calculateStudentMarks($student, $year_data);

        return array_merge(
            $marks,
            ["$year_data->total"],
            [assessmentExpect($year_data->total, $this->reportRanges, $student->grade)],
            [$progress]
        );
    }

    private function calculateStudentMarks($student, $termStudent): array
    {
        $marks = [
            ""
        ];
        foreach ($this->subjects as $subject) {
            $mark = collect($termStudent->subjects_marks)->where('subject_id',$subject->id)->first()['mark'];
            $marks[] =  "".$mark."" ?? "0";
        }

        return $marks;
    }


    private function getEmptyTermMarks(string $progress): array
    {
        $emptyCount = 6 + 3; // 12 marks + extras + expectations
        return array_merge(
            array_fill(0, $emptyCount, ''),
            [$progress]
        );
    }

    private function selectData($abtIds, $yearId, $grade)
    {
        if (empty($abtIds)) {
            return [];
        }
        $schoolIds = $this->schools->pluck('id')->toArray();

        // Get current year marks
        $currentMarks = DB::table('student_terms as st')
            ->select([
                's.abt_id', 'st.total as total_result', 'l.grade as grade', 's.school_id', 'l.year_id', 's.gender',
                's.name as student_name', 's.id_number', 's.nationality', 's.citizen', 's.sen', 's.g_t',
                'st.subjects_marks', 'l.arab'
            ])
            ->join('students as s', 'st.student_id', '=', 's.id')
            ->join('terms as t', 'st.term_id', '=', 't.id')
            ->join('levels as l', 't.level_id', '=', 'l.id')
            ->whereIn('s.school_id', $schoolIds)
            ->whereIn('s.abt_id', $abtIds)
            ->where('l.year_id', $yearId)
            ->where('l.grade', $grade)
            ->where('t.round', $this->round)
            ->where('st.corrected', 1)
            ->whereNull('st.deleted_at')
            ->whereNull('s.deleted_at')
            ->whereNull('t.deleted_at')
            ->whereNull('l.deleted_at')
//            ->pluck('mark', 'abt_id')
//            ->toArray()
            ->get();
//        dd($currentMarks);

        return $currentMarks;
    }

    private function getEligibleStudentsOptimized()
    {
        $studentsAbtIdsByGrade = [];

        foreach ($this->selectedGrades as $grade) {
            if ((count($this->years) == 2 && $grade == 1) || (count($this->years) == 3 && $grade == 2)) {
                continue;
            }

            $abtIds = $this->findEligibleStudentsForGrade($grade);
            if (!empty($abtIds)) {
                $studentsAbtIdsByGrade[$grade] = $abtIds;
            }
        }

        return $studentsAbtIdsByGrade;
    }

    private function findEligibleStudentsForGrade($targetGrade)
    {
        $schoolIds = $this->schools->pluck('id')->toArray();
        $yearCount = count($this->years);

        // Build year-grade pairs for this target grade
        $yearGradePairs = [];
        $currentGrade = $targetGrade;
        foreach ($this->reversedYears as $year) {
            $yearGradePairs[] = ['year_id' => $year, 'grade' => $currentGrade];
            $currentGrade--;
        }

        // Find students who have completed assessments for all required year-grade combinations
        $studentAbtIds = [];

        foreach ($yearGradePairs as $index => $pair) {
            $query = DB::table('students as s')
                ->select('s.abt_id')
                ->join('student_terms as st', 's.id', '=', 'st.student_id')
                ->join('terms as t', 'st.term_id', '=', 't.id')
                ->join('levels as l', 't.level_id', '=', 'l.id')
                ->whereIn('s.school_id', $schoolIds)
                ->whereNotNull('s.abt_id')
                ->where('l.year_id', $pair['year_id'])
                ->where('l.grade', $pair['grade'])
                ->where('t.round', $this->round)
                ->where('st.corrected', 1)
                ->whereNull('s.deleted_at')
                ->whereNull('st.deleted_at')
                ->whereNull('t.deleted_at')
                ->whereNull('l.deleted_at');

            // Add section filter if specified
            if ($this->student_type != 2) {
                $query->where('l.arab', $this->student_type);
            }

            $currentYearAbtIds = $query->pluck('s.abt_id')->toArray();

            if ($index === 0) {
                $studentAbtIds = $currentYearAbtIds;
            } else {
                // Keep only students who appear in all years
                $studentAbtIds = array_intersect($studentAbtIds, $currentYearAbtIds);
            }

            // If no students left, no point continuing
            if (empty($studentAbtIds)) {
                break;
            }
        }

        return array_values(array_unique($studentAbtIds));
    }

    private function initializeReportParameters()
    {
        $this->round = $this->request->get('round', false);
        $this->student_type = $this->request->get('student_type', 0);
        $this->selectedGrades = $this->request->get('grades', []);
        $this->years = $this->request->get('years', []);
        $examsYear = [];
        // Adjust years for Indian/Pakistan/Bangladeshi schools
        if (in_array($this->school->school_type, ['Indian', 'Pakistan', 'Bangladeshi'])) {
            if (in_array($this->round, ['May'])) {
                foreach ($this->years as $year) {
                    $examsYear[] = $year - 1;
                }
            } else {
                // For other rounds, use the years as they are
                $examsYear = $this->years;
            }
        } else {
            // For other school types, use the years as they are
            $examsYear = $this->years;
        }
        $this->reversedYears = array_reverse($examsYear);
        $this->reportRanges = $this->request->get('ranges_type', 1);
        $this->yearCollection = Year::query()->whereIn('id', $examsYear)->orderBy('id')->get();
        $this->lastYear = count($this->years) == 3 ? $this->years[2] : $this->years[1];
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

        // Apply bold font to assessment columns (from column J onwards)
        $event->sheet->getDelegate()->getStyle("J1:{$lastCell}{$lastRow}")
            ->getFont()->setBold(true);

        // Apply RTL for Arabic locale
        if (app()->getLocale() == 'ar') {
            $event->sheet->setRightToLeft(true);
        }

        // Apply bold styling to specific assessment round headers
        $this->applyAssessmentColumnStyling($event);

        //Add Color header by year
       $this->applyHeaderColor($event);
    }

    private function applyAssessmentColumnStyling(AfterSheet $event): void
    {
        // Since column positions may vary, let's apply styling more conservatively
        // Just ensure the basic header row and data columns are properly styled
        $lastRow = $event->sheet->getHighestRow();
        $lastColumn = $event->sheet->getHighestColumn();


        // If we have more columns, style them too (for schools with more assessment data)
        if ($lastColumn > 'Z') {
            // Apply bold to all assessment data (starting from column K which contains Islamic subjects data)
            $event->sheet->getDelegate()->getStyle("L1:Z{$lastRow}")
                ->getFont()->setBold(true);
            $event->sheet->getDelegate()->getStyle("AA1:{$lastColumn}{$lastRow}")
                ->getFont()->setBold(true);
        }else{
            // For schools with fewer columns, just apply to the existing ones
            $event->sheet->getDelegate()->getStyle("K1:{$lastColumn}{$lastRow}")
                ->getFont()->setBold(true);
        }
    }

    private function applyConditionalFormatting(AfterSheet $event): void
    {
        $lastCell = $event->sheet->getHighestColumn();
        $lastRow = $event->sheet->getHighestRow();

        $startColumn =  'J' ;
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
        $event->sheet->getDelegate()->getStyle("A1:I1")
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB("E37200");
        $event->sheet->getDelegate()->getStyle("A1:I1")
            ->getFont()
            ->getColor()
            ->setARGB(Color::COLOR_WHITE);


        $colors = ['444DCD', '808080', '2CC306'];
        $firstColumn = 'J';
        $yearColumnsCount = 10;
        foreach ($this->years as $index => $year)
        {
            //add year columns count to the first column
            $toColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(
                \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($firstColumn) + $yearColumnsCount - 1
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
