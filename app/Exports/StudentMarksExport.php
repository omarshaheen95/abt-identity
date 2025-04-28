<?php

namespace App\Exports;

use App\Helpers\Constant;
use App\Models\Inspection;
use App\Models\School;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentMarksExport implements WithMapping, Responsable, WithHeadings, FromArray, WithEvents, WithDefaultStyles, ShouldAutoSize
{
    use Exportable;

    public $length;
    public $request;
    private $schools_ids;
    private $rounds_key;
    private $rounds = Constant::ROUNDS;


    public function __construct(Request $request, array $schools_ids = null)
    {
        $this->request = $request;
        $this->length = 1;
        $this->schools_ids = $schools_ids;
        if (!is_null($this->schools_ids))
        {
            $school = School::find($this->schools_ids[0]);
        }else{
            $school = Auth::guard('school')->user();
        }
        if ($school == 'Indian') {
            $this->rounds_key = Constant::OTHER_ROUNDS_KEY;
        } else {
            $this->rounds_key = Constant::ROUNDS_KEY;
        }
    }

    public function headings(): array
    {
        $headers = [
            'ID',
            'Name',
            'School',
            'Grade',
            'Grade Name',
            'Gender',
            'Arab',
            'Citizen',
            'Sen',
            'G & T',

            'The Assessment - Round 1',
            'Culture',
            'Judgment',
            'Values',
            'Judgment',
            'Citizenship',
            'Judgment',
            'Total',
            'Attainment & Expectations',
            'The Progress',

            'The Assessment - Round 2',
            'Culture',
            'Judgment',
            'Values',
            'Judgment',
            'Citizenship',
            'Judgment',
            'Total',
            'Attainment & Expectations',
            'The Progress',

            'The Assessment - Round 3',
            'Culture',
            'Judgment',
            'Values',
            'Judgment',
            'Citizenship',
            'Judgment',
            'Total',
            'Attainment & Expectations',
            'The Progress',
        ];


        // Wrap each header in re() for translation
        return array_map(function ($header) {
            return re($header);
        }, $headers);
    }

    public function map($row): array
    {
        return $row;
    }

    public function array(): array
    {
        $results = array();
        if ($this->schools_ids) {
            $rows = Student::query()->with(['level', 'year','student_terms' => function ($query) {
                $query->where('corrected', 1);
            }, 'student_terms.term'])
                ->whereIn('school_id', $this->schools_ids)->search($this->request)
                ->latest();
        } else {
            $rows = Student::query()->with(['level', 'year','student_terms' => function ($query) {
                $query->where('corrected', 1);
            }, 'student_terms.term'])
                ->search($this->request)
                ->latest();
        }
        $rows = $rows->get();
        $subjects = Subject::query()->get();
        foreach ($rows as $student) {
            $result = [
                $student->id_number,
                $student->name,
                optional($student->school)->name,
                optional($student->level)->grade,
                $student->grade_name,
                re(ucfirst($student->gender)),
                $student->arab ? re('Yes') : re('No'),
                $student->citizen ? re('Yes') : re('No'),
                $student->sen ? re('Yes') : re('No'),
                $student->g_t ? re('Yes') : re('No'),
            ];
            $terms = $student->student_terms;
            $terms_ordered = [];
            foreach ($this->rounds_key as $round_key) {
                $round = $this->rounds[$round_key];
                $term = $terms->filter(function ($term) use ($round) {
                    return $term->term->round == strtolower($round);
                })->first();
                if ($term) {
                    $terms_ordered[] = $term;
                }else{
                    $terms_ordered[] = null;
                }
            }

            if (count($terms_ordered) > 0)
            {
                $this->getSubjectsAssessmentsProgress($terms_ordered);

                foreach ($terms_ordered as $term) {
                    if ($term) {
                        $result[] = re(ucfirst($term->term->round));
                        foreach ($subjects as $skill)
                        {
                            $is_skill_presented = (object)collect($term->subjects_marks)->firstWhere('subject_id', $skill->id);
                            $result[] = $is_skill_presented ? $is_skill_presented->mark : 'N/A';
                            $result[] = $is_skill_presented ? getSubjectAttainment($is_skill_presented, $subjects) : 'N/A';
                        }
                        $result[] = "$term->total";
                        $result[] = $term->expectations;
                        $result[] = $term->progress ?? '-';
                    } else {
                        $result[] = '-';
                        foreach ($subjects as $skill) {
                            $result[] = '-';
                            $result[] = '-';
                        }
                        $result[] = '-';
                        $result[] = '-';
                        $result[] = '-';
                    }
                }
            }
            $results[] = $result;
        }
        return $results;
    }


    public function drawings()
    {
        return new Drawing();
    }

    public function registerEvents(): array
    {
        Sheet::macro('cStyleCells', function (Sheet $sheet, string $cellRange, array $style) {
            $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
            if (app()->getLocale() == 'ar') {
                $sheet->setRightToLeft(true);
            }
        });
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $last_cell = $event->sheet->getHighestColumn();
                $last_row = $event->sheet->getHighestRow();

                $cellRange = 'A1:' . $last_cell . $last_row;
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold('bold')->setSize(12);
                $event->sheet->cStyleCells(
                    "A1:" . $last_cell . "1",
                    [
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        ],
                    ]
                );

                $event->sheet->cStyleCells(
                    "A1:" . $last_cell . "1",
                    [
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['argb' => Color::COLOR_YELLOW],
                        ],
                        'font' => [
                            'color' => ['argb' => Color::COLOR_RED],
                        ],

                    ]
                );

                $redStyle = new Style(false, true);
                $redStyle->getFont()->setColor(new Color(Color::COLOR_RED));
                $greenStyle = new Style(false, true);
                $greenStyle->getFont()->setColor(new Color(Color::COLOR_GREEN));
                $orangeStyle = new Style(false, true);
                $orangeStyle->getFont()->setColor(new Color("FFC107"));

                $cellRange = "K1:" . $last_cell . $last_row;
                $conditionalStyles = [];
                $wizardFactory = new Wizard($cellRange);
                /** @var Wizard\TextValue $textWizard */
                $textWizard = $wizardFactory->newRule(Wizard::TEXT_VALUE);

                $textWizard->contains("Above")->setStyle($greenStyle);
                $conditionalStyles[] = $textWizard->getConditional();

                $textWizard->contains("Inline")->setStyle($orangeStyle);
                $conditionalStyles[] = $textWizard->getConditional();

                $textWizard->contains("Below")->setStyle($redStyle);
                $conditionalStyles[] = $textWizard->getConditional();

                $textWizard->contains("Better than expected progress")->setStyle($greenStyle);
                $conditionalStyles[] = $textWizard->getConditional();

                $textWizard->contains("Expected progress")->setStyle($orangeStyle);
                $conditionalStyles[] = $textWizard->getConditional();

                $textWizard->contains("Below expected progress")->setStyle($redStyle);
                $conditionalStyles[] = $textWizard->getConditional();

                $event->sheet
                    ->getStyle($textWizard->getCellRange())
                    ->setConditionalStyles($conditionalStyles);
            },
        ];
    }

    public function defaultStyles(Style $defaultStyle)
    {
        return [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ];
    }


    public function getSubjectsAssessmentsProgress($assessments = [])
    {
        $assessments_count = count($assessments);
        if ($assessments_count > 1) {
            $assess_1 = $assessments[0];
            $assess_2 = $assessments[1];
            $assess_3 = $assessments[2];
            if ($assess_1)
            {
                $assessments[0]->progress = '-';
            }
            if ($assess_2)
            {
                $assessments[1]->progress = '-';
            }
            if ($assess_3)
            {
                $assessments[2]->progress = '-';
            }
            if ($assess_1 && $assess_2) {
                $assess_1_total = $assess_1->total;
                $assess_2_total = $assess_2->total;
                $assessments[1]->progress = $this->getProgressLabel($assess_1_total, ($assess_2_total - $assess_1_total));
            }
            if ($assess_2 && $assess_3) {
                $assess_2_total = $assess_2->total;
                $assess_3_total = $assess_3->total;
                $assessments[2]->progress = $this->getProgressLabel($assess_2_total, ($assess_3_total - $assess_2_total));
            }
            if ($assess_1 && $assess_3) {
                $assess_1_total = $assess_1->total;
                $assess_3_total = $assess_3->total;
                $assessments[2]->progress = $this->getProgressLabel($assess_1_total, ($assess_3_total - $assess_1_total));
            }
        }
        if ($assessments_count == 1) {
            $assessments[0]->progress = "-";
        }
    }
    private function getProgressLabel($startPoint, $subTotalMarks)
    {
        if ($startPoint >= 70) {
            return $this->evaluateProgress($subTotalMarks, -5, -10);
        } elseif ($startPoint >= 50) {
            return $this->evaluateProgress($subTotalMarks, 5, 0);
        } else {
            return $this->evaluateProgress($subTotalMarks, 10, 5);
        }
    }
    private function evaluateProgress($subTotalMarks, $betterThreshold, $expectedThreshold)
    {
        if ($subTotalMarks > $betterThreshold) {
            return re('Better than expected progress');
        } elseif ($subTotalMarks >= $expectedThreshold) {
            return re('Expected progress');
        } else {
            return re('Below expected progress');
        }
    }

}
