<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Reports\NewReports;

use App\Models\StudentTerm;
use App\Models\Subject;
use App\Models\Term;
use App\Models\Year;
use App\Models\School;
use App\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AttainmentReport
{
    private $school;
    private $schools;
    private $request;
    private $markData;
    private $months;
    private $yearData;
    private $steps_count;
    private $subjects;
    private $skills;

    private $isCombined = false;
    private const CHUNK_SIZE = 2000; // Larger chunks for combined approach
    private const MEMORY_THRESHOLD = 80 * 1024 * 1024; // 80MB for combined data

    public function __construct(Request $request, $schools = [])
    {
        $schools = School::query()->whereIn('id', $schools)->get();
        $this->school = $schools->first();
        $this->schools = $schools;
        $this->request = $request;
//        $this->steps_count = Subject::query()->count();
        $this->subjects = Subject::query()->get();
//        $this->skills = $this->subjects->pluck('name')->toArray();
    }

    public function report()
    {
        // Initialize report parameters
        $this->initializeReportParameters();

        $grades = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
        $selectedGrades = $this->request->get('grades', []);
        $data = [];
        $pages = [];
        $student_type = $this->request->get('student_type', false);

        foreach ($grades as $grade) {
                if (!in_array($grade, $selectedGrades)) {
                    continue;
                }
                $gradeData = $this->processGradeData($grade);

                if (!empty($gradeData['general_rounds'])) {
                    $pages[$grade] = $this->formatGradePage($grade, $gradeData);
                }
            }


        $this->yearData['type'] = $student_type;
        $this->yearData['sub_title'] = $this->getSubTitle($student_type);
        $data['student_statistics'] = $this->getGradesStudentsCount($grades);
//        dd($arab_pages, $non_arab_pages);
        return $this->renderReport($pages, $data);
    }

    /**
     * Generate report with combined page included
     * @return mixed
     */
    public function reportCombined()
    {
        // Initialize report parameters
        $this->initializeReportParameters();
        $this->isCombined = true;
        $grades = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
        $selectedGrades = $this->request->get('grades', []);
        $pagesGrades = [];
        $pages = [];
        $student_type = $this->request->get('student_type', false);

        foreach ($grades as $grade) {
            if (!in_array($grade, $selectedGrades)) {
                continue;
            }
            $gradeData = $this->processGradeData($grade);
            $pagesGrades[$grade] = $gradeData;
        }

        // Add combined page if multiple grades selected
//        if (count($selectedGrades) >= 1) {
        $combinedPage = $this->getCombinedGradesPage($pagesGrades);
        $pages[] = $combinedPage;
//        }
        return $this->renderReport($pages);
    }

    /**
     * Get combined data for all grades in a single page format
     * Sums all statistics from all selected grades into one consolidated page
     * @return object|null
     */
    public function getCombinedGradesPage($pagesGrades)
    {
//        $this->initializeReportParameters();

        $selectedGrades = $this->request->get('grades', []);
        if (empty($selectedGrades)) {
            return null;
        }

        // Initialize combined data structure
        $gradesData = [
            'students' => 0,
            'g_t_students' => 0,
            'sen_students' => 0,
            'citizen_students' => 0,
            'boys_students' => 0,
            'girls_students' => 0,
            'boys_citizen_students' => 0,
            'girls_citizen_students' => 0,
            'student_type' => $this->getStudentTypeText($this->yearData['type']),
            ];
        $combinedRounds = [];
        $combinedStepRounds =[];
        foreach ($this->subjects->pluck('id') as $step) {
            $combinedStepRounds[$step] = [];
        }

        $combinedCategoryData = [
            'boys' => [], 'girls' => [], 'sen' => [], 'g_t' => [],
            'citizen' => [], 'boys_citizen' => [], 'girls_citizen' => [],
        ];

        // Initialize accumulators for each round
        $roundAccumulators = [];
        $stepAccumulators = [];
        $categoryAccumulators = [];

        // Initialize accumulators for 3 rounds
        for ($roundIndex = 0; $roundIndex < 3; $roundIndex++) {
            $monthKey = $this->months[$roundIndex];
            $roundAccumulators[$monthKey] = ['total' => 0, 'below' => 0, 'inline' => 0, 'above' => 0];

            // Initialize step accumulators
            foreach ($this->subjects->pluck('id') as $step) {
                $stepAccumulators[$step][$monthKey] = ['total' => 0, 'below' => 0, 'inline' => 0, 'above' => 0];
            }

            // Initialize category accumulators
            foreach ($combinedCategoryData as $category => $data) {
                $categoryAccumulators[$category][$monthKey] = ['total' => 0, 'below' => 0, 'inline' => 0, 'above' => 0];
            }
        }

        // Process each grade and accumulate data
        foreach ($pagesGrades as $gradeData) {
            if (!empty($gradeData['general_rounds'])) {
                // Accumulate general rounds
                foreach ($gradeData['general_rounds'] as $roundData) {
                    $monthKey = $roundData->id;
                    $roundAccumulators[$monthKey]['total'] += $roundData->total;
                    $roundAccumulators[$monthKey]['below'] += $roundData->below;
                    $roundAccumulators[$monthKey]['inline'] += $roundData->inline;
                    $roundAccumulators[$monthKey]['above'] += $roundData->above;
                }

                // Accumulate step data
                foreach ($this->subjects->pluck('id') as $step) {
                    if (!empty($gradeData['step_rounds'][$step])) {
                        foreach ($gradeData['step_rounds'][$step] as $stepData) {
                            $monthKey = $stepData->id;
                            $stepAccumulators[$step][$monthKey]['total'] += $stepData->total;
                            $stepAccumulators[$step][$monthKey]['below'] += $stepData->below;
                            $stepAccumulators[$step][$monthKey]['inline'] += $stepData->inline;
                            $stepAccumulators[$step][$monthKey]['above'] += $stepData->above;
                        }
                    }
                }

                // Accumulate category data
                foreach ($combinedCategoryData as $category => $data) {
                    if (!empty($gradeData['category_data'][$category])) {
                        foreach ($gradeData['category_data'][$category] as $categoryData) {
                            $monthKey = $categoryData->id;
                            $categoryAccumulators[$category][$monthKey]['total'] += $categoryData->total;
                            $categoryAccumulators[$category][$monthKey]['below'] += $categoryData->below;
                            $categoryAccumulators[$category][$monthKey]['inline'] += $categoryData->inline;
                            $categoryAccumulators[$category][$monthKey]['above'] += $categoryData->above;
                        }
                    }
                }

                //Accumulate grade data
                if (isset($gradeData['grade_data'])) {
                    $gradesData['students'] += $gradeData['grade_data']['students'];
                    $gradesData['g_t_students'] += $gradeData['grade_data']['g_t_students'];
                    $gradesData['sen_students'] += $gradeData['grade_data']['sen_students'];
                    $gradesData['boys_students'] += $gradeData['grade_data']['boys_students'];
                    $gradesData['girls_students'] += $gradeData['grade_data']['girls_students'];
                    $gradesData['citizen_students'] += $gradeData['grade_data']['citizen_students'];
                    $gradesData['boys_citizen_students'] += $gradeData['grade_data']['boys_citizen_students'];
                    $gradesData['girls_citizen_students'] += $gradeData['grade_data']['girls_citizen_students'];
                    $gradesData['student_type'] = $this->getStudentTypeText($this->yearData['type']);
                }
            }else{
                if (isset($gradeData['grade_data'])) {
                    $gradesData['students'] += $gradeData['grade_data']['students'];
                    $gradesData['g_t_students'] += $gradeData['grade_data']['g_t_students'];
                    $gradesData['sen_students'] += $gradeData['grade_data']['sen_students'];
                    $gradesData['boys_students'] += $gradeData['grade_data']['boys_students'];
                    $gradesData['girls_students'] += $gradeData['grade_data']['girls_students'];
                    $gradesData['citizen_students'] += $gradeData['grade_data']['citizen_students'];
                    $gradesData['boys_citizen_students'] += $gradeData['grade_data']['boys_citizen_students'];
                    $gradesData['girls_citizen_students'] += $gradeData['grade_data']['girls_citizen_students'];
                    $gradesData['student_type'] = $this->getStudentTypeText($this->yearData['type']);
                }
            }
        }


        // Convert accumulators to final format
        // General rounds
        foreach ($this->months as $month) {
            if (isset($roundAccumulators[$month])) {
                $acc = $roundAccumulators[$month];
                if ($acc['total'] > 0) {
                    $combinedRounds[] = $this->formatStatisticsData($month, $acc['total'], $acc['below'], $acc['inline'], $acc['above']);
                }
            }
        }

        // Step rounds
        foreach ($this->subjects->pluck('id') as $step) {
            foreach ($this->months as $month) {
                if (isset($stepAccumulators[$step][$month])) {
                    $acc = $stepAccumulators[$step][$month];
                    if ($roundAccumulators[$month]['total'] > 0) {
                        $combinedStepRounds[$step][] = $this->formatStatisticsData($month, $acc['total'], $acc['below'], $acc['inline'], $acc['above']);
                    }
                }
            }
        }

        // Category data
        foreach ($combinedCategoryData as $category => $data) {
            foreach ($this->months as $month) {
                if (isset($categoryAccumulators[$category][$month])) {
                    $acc = $categoryAccumulators[$category][$month];
                    if ($roundAccumulators[$month]['total'] > 0) {
                        $combinedCategoryData[$category][] = $this->formatStatisticsData($month, $acc['total'], $acc['below'], $acc['inline'], $acc['above']);
                    }
                }
            }
        }

        // Create combined page
        $gradeList = implode(', ', $selectedGrades);
//        $title = 'Combined Grades (' . $gradeList . ') ' . $this->yearData['sub_title'] . ' - ' . $this->yearData['year']->year;
        $title = re('combined_grades_title', [
            'grades' => $gradeList,
            'subtitle' => $this->yearData['sub_title'],
            'year' => $this->yearData['year']->year,
        ]);
       $data = [
            'title' => $title,
            'grade_data' => (object)$gradesData,
            'rounds' => (object)$combinedRounds,
            'boys' => (object)$combinedCategoryData['boys'],
            'girls' => (object)$combinedCategoryData['girls'],
            'sen' => (object)$combinedCategoryData['sen'],
            'g_t' => (object)$combinedCategoryData['g_t'],
            'citizen' => (object)$combinedCategoryData['citizen'],
            'boys_citizen' => (object)$combinedCategoryData['boys_citizen'],
            'girls_citizen' => (object)$combinedCategoryData['girls_citizen'],
            'student_type' => $this->getStudentTypeText($this->yearData['type']),
        ];

        foreach ($this->subjects->pluck('id') as $step) {
            $data['step_' . $step] = (object)$combinedStepRounds[$step];
        }
        return (object)$data;
    }

    /**
     * Get only the combined page (useful for API or specific use cases)
     * @return array|null
     */

    private function initializeReportParameters()
    {
        $yearId = $this->request->get('year_id', false);
//        $ranges_type = $this->request->get('ranges_type', false);
        $type = $this->request->get('student_type', false);

        $year = Year::query()->findOrFail($yearId);
        $this->yearData = [
            'year' => $year,
            'type' => $type,
            'sub_title' => $this->getSubTitle($type),
//            'ranges_type' => $ranges_type
        ];


        if (in_array($this->school->school_type, ['Indian', 'Pakistan', 'Bangladeshi'])) {
            $this->months = ['may', 'september', 'february'];
            $register_year = $yearId - 1;
        } else {
            $this->months = ['september', 'february', 'may'];
            $register_year = $yearId;
        }

        $this->yearData['register_year'] = $register_year;
    }

    private function getSubTitle($type)
    {
        switch ($type) {
            case 0:
                return 'Non-Arabs';
            case 1:
                return 'Arabs';
            case 2:
                return 'Arabs & Non-arabs';
            default:
                return '';
        }
    }

    private function processGradeData($grade)
    {
        $this->markData = getMarkRange();

        // OPTIMIZED: Fetch ALL terms and student terms for this grade at once (2 queries instead of 6)
        $allTermsData = $this->getAllTermsAndStudentTermsForGrade($grade);

        $termRounds = [];
        $stepRounds =[];
        foreach ($this->subjects->pluck('id') as $step) {
            $stepRounds[$step] = [];
        }
            $categoryData = [
            'boys' => [],
            'girls' => [],
            'sen' => [],
            'g_t' => [],
            'citizen' => [],
            'boys_citizen' => [],
            'girls_citizen' => [],
        ];

        // Process each term round using pre-fetched data
        for ($roundIndex = 0; $roundIndex < 3; $roundIndex++) {
            $monthKey = $this->months[$roundIndex];
            $studentTerms = isset($allTermsData[$monthKey]) ? $allTermsData[$monthKey] : collect();

            if ($studentTerms->count() > 0) {
                // General statistics
                $termRounds[] = $this->calculateGeneralStatistics($studentTerms, $monthKey);

                // Step statistics
                foreach ($this->subjects->pluck('id') as $step) {
                    $stepRounds[$step][] = $this->calculateStepStatistics($studentTerms, $step, $monthKey);
                }

                // Category statistics
                foreach ($categoryData as $category => $data) {
                    $categoryData[$category][] = $this->calculateCategoryStatistics($studentTerms, $category, $monthKey);
                }
            }
        }

        return [
            'general_rounds' => $termRounds,
            'step_rounds' => $stepRounds,
            'category_data' => $categoryData,
            'grade_data' => $this->getGradeStudentsCount($grade)
        ];
    }

    /**
     * OPTIMIZED: Get all terms and student terms for a grade in just 2 queries
     * @param int $grade
     * @return array
     */
    private function getAllTermsAndStudentTermsForGrade($grade)
    {
        $type = $this->yearData['type'];
        $sections = $this->getSections();

        // Query 1: Get ALL terms for this grade across all months by each month
        if ($this->yearData['register_year'] != $this->yearData['year']->id) {
            $firstTerms = Term::query()
                ->with('level')
                ->whereHas('level', function (Builder $query) use ($grade, $type) {
                    $query->where('grade', $grade);
                    if ($type != 2) {
                        $query->where('arab', $type);
                    }
                    $query->where('year_id', $this->yearData['register_year']);
                })
                ->where('round', $this->months[0])
                ->get();
            $secondTerms = Term::query()
                ->with('level')
                ->whereHas('level', function (Builder $query) use ($grade, $type) {
                    $query->where('grade', $grade);
                    if ($type != 2) {
                        $query->where('arab', $type);
                    }
                    $query->where('year_id', $this->yearData['year']->id);
                })
                ->where('round', $this->months[1])
                ->get();
            $lastTerms = Term::query()
                ->with('level')
                ->whereHas('level', function (Builder $query) use ($grade, $type) {
                    $query->where('grade', $grade);
                    if ($type != 2) {
                        $query->where('arab', $type);
                    }
                    $query->where('year_id', $this->yearData['year']->id);
                })
                ->where('round', $this->months[2])
                ->get();
            //merge all terms
            $allTerms = $firstTerms->merge($secondTerms)->merge($lastTerms);
        } else {
            $allTerms = Term::query()
                ->with('level')
                ->whereHas('level', function (Builder $query) use ($grade, $type) {
                    $query->where('grade', $grade);
                    if ($type != 2) {
                        $query->where('arab', $type);
                    }
                    $query->where('year_id', $this->yearData['year']->id);
                })
                ->whereIn('round', $this->months)
                ->get();
        }


        // Group terms by month for easy access
        $termsByMonth = $allTerms->groupBy('round');

        //check count
        $allStudentTermsCount = StudentTerm::query()
            ->whereHas('student', function (Builder $query) use ($sections) {
                if (count($sections) > 0) {
                    $query->whereIn('grade_name', $sections);
                }
                //check included SEN and G&T students
                $query->when(!$this->request->get('include_sen', false), function ($query) {
                    $query->where('sen', 0);
                })->when(!$this->request->get('include_g_t', false), function ($query) {
                    $query->where('g_t', 0);
                });
                $query->whereIn('school_id', $this->schools->pluck('id'));
            })
            ->whereIn('term_id', $allTerms->pluck('id'))
            ->where('corrected', 1)
            ->count();

        // Query 2: Get ALL student terms for this grade in one query
        if ($allStudentTermsCount > self::CHUNK_SIZE) {
            StudentTerm::query()
                ->whereHas('student', function (Builder $query) use ($sections) {
                    if (count($sections) > 0) {
                        $query->whereIn('grade_name', $sections);
                    }
                    $query->whereIn('school_id', $this->schools->pluck('id'));
                })
                ->whereIn('term_id', $allTerms->pluck('id'))
                ->where('corrected', 1)
                ->chunk(self::CHUNK_SIZE, function ($studentTermsChunk) use (&$allStudentTerms) {
                    $studentTermsChunk = $studentTermsChunk->load('student');
                    if (!isset($allStudentTerms)) {
                        $allStudentTerms = collect();
                    }
                    $allStudentTerms = $allStudentTerms->merge($studentTermsChunk);
                });
        } else {
            $allStudentTerms = StudentTerm::query()
                ->with(['student'])
                ->whereHas('student', function (Builder $query) use ($sections) {
                    if (count($sections) > 0) {
                        $query->whereIn('grade_name', $sections);
                    }
                    $query->whereIn('school_id', $this->schools->pluck('id'));
                })
                ->whereIn('term_id', $allTerms->pluck('id'))
                ->where('corrected', 1)
                ->get();
        }

        // Group student terms by month based on their term's month
        $studentTermsByMonth = [];
        foreach ($this->months as $month) {
            $monthTermIds = isset($termsByMonth[$month]) ? $termsByMonth[$month]->pluck('id') : collect();
            $studentTermsByMonth[$month] = $allStudentTerms->whereIn('term_id', $monthTermIds);
        }

        return $studentTermsByMonth;
    }

    private function getSections()
    {
        $sectionsId = $this->request->get('grades_names', []);
        $sections = [];

        foreach ($sectionsId as $sec) {
            if (!is_null($sec)) {
                $sections[] = $sec;
            }
        }

        return $sections;
    }

    private function calculateGeneralStatistics($studentTerms, $monthId)
    {
        $total = $studentTerms->count();
        $below = $studentTerms->where('total', '<=', $this->markData->below->to)->count();
        $inline = $studentTerms->where('total', '>=', $this->markData->inline->from)
            ->where('total', '<=', $this->markData->inline->to)->count();
        $above = $studentTerms->where('total', '>=', $this->markData->above->from)->count();
        $students_achievements['highest_student_mark'] = null;
        $students_achievements['lowest_student_mark'] = null;
        //highest mark student
        $highestMarkStudent = $studentTerms->sortByDesc('total')->first();
        if ($highestMarkStudent) {
            $students_achievements['highest_student_mark'] = $highestMarkStudent->student;
        }
        //lowest mark student
        $lowestMarkStudent = $studentTerms->sortBy('total')->first();
        if ($lowestMarkStudent) {
            $students_achievements['lowest_student_mark'] = $lowestMarkStudent->student;
        }
        $students_achievements['average'] = $studentTerms->avg('total');


        return $this->formatStatisticsData($monthId, $total, $below, $inline, $above, $students_achievements);
    }

    private function calculateStepStatistics($studentTerms, $step, $monthId)
    {
        $total = $studentTerms->count();

        //range by subject id
        $range = $this->subjects->where('id',$step)->first()->marks_range;

        $below = $studentTerms->filter(function ($term) use ($step, $range) {
            return collect($term->subjects_marks)
                ->contains(function ($subject) use ($step, $range) {
                    return $subject['subject_id'] == $step
                        && $subject['mark'] >= $range['below']['from']
                        && $subject['mark'] <= $range['below']['to'];
                });
        })->count();

        $inline = $studentTerms->filter(function ($term) use ($step, $range) {
            return collect($term->subjects_marks)
                ->contains(function ($subject) use ($step, $range) {
                    return $subject['subject_id'] == $step
                        && $subject['mark'] >= $range['inline']['from']
                        && $subject['mark'] <= $range['inline']['to'];
                });
        })->count();

        $above = $studentTerms->filter(function ($term) use ($step, $range) {
            return collect($term->subjects_marks)
                ->contains(function ($subject) use ($step, $range) {
                    return $subject['subject_id'] == $step
                        && $subject['mark'] >= $range['above']['from']
                        && $subject['mark'] <= $range['above']['to'];
                });
        })->count();

        return $this->formatStatisticsData($monthId, $total, $below, $inline, $above);
    }

    private function calculateCategoryStatistics($studentTerms, $category, $monthId)
    {
        $filteredTerms = $this->filterStudentsByCategory($studentTerms, $category);

        $total = $filteredTerms->count();
        if ($total === 0) {
            return $this->formatStatisticsData($monthId, 0, 0, 0, 0);
        }

        $below = $filteredTerms->where('total', '<', $this->markData->below->to)->count();
        $inline = $filteredTerms->where('total', '>=', $this->markData->inline->from)
            ->where('total', '<', $this->markData->inline->to)->count();
        $above = $filteredTerms->where('total', '>=', $this->markData->above->from)
            ->where('total', '<=', $this->markData->above->to)->count();

        //dd($filteredTerms->pluck('total')->toArray());
        return $this->formatStatisticsData($monthId, $total, $below, $inline, $above);
    }

    private function filterStudentsByCategory($studentTerms, $category)
    {
        switch ($category) {
            case 'boys':
                return $studentTerms->filter(function ($term) {
                    return $term->student->gender == 'boy';
                });
            case 'girls':
                return $studentTerms->filter(function ($term) {
                    return $term->student->gender == 'girl';
                });
            case 'sen':
                return $studentTerms->filter(function ($term) {
                    return $term->student->sen == 1;
                });
            case 'g_t':
                return $studentTerms->filter(function ($term) {
                    return $term->student->g_t == 1;
                });
            case 'citizen':
                return $studentTerms->filter(function ($term) {
                    return $term->student->citizen == 1;
                });
            case 'boys_citizen':
                return $studentTerms->filter(function ($term) {
                    return $term->student->citizen == 1 && $term->student->gender == 'boy';
                });
            case 'girls_citizen':
                return $studentTerms->filter(function ($term) {
                    return $term->student->citizen == 1 && $term->student->gender == 'girl';
                });
            default:
                return $studentTerms;
        }
    }

    private function formatStatisticsData($monthId, $total, $below, $inline, $above, $students_achievements = [])
    {
        return (object)[
            'id' => $monthId,
            'total' => $total,
            'below' => $below,
            'inline' => $inline,
            'above' => $above,
            'per_below' => $this->calculatePercentage($below, $total),
            'per_inline' => $this->calculatePercentage($inline, $total),
            'per_above' => $this->calculatePercentage($above, $total),
            'highest_student_mark' => isset($students_achievements['highest_student_mark']) ? $students_achievements['highest_student_mark']->name : null,
            'lowest_student_mark' => isset($students_achievements['lowest_student_mark']) ? $students_achievements['lowest_student_mark']->name : null,
            'average' => isset($students_achievements['average']) ? round($students_achievements['average'], 2) : null,
        ];
    }

    private function calculatePercentage($value, $total)
    {
        if ($value > 0 && $total > 0) {
            return round(($value / $total) * 100, 2);
        }
        return 0;
    }

    private function formatGradePage($grade, $gradeData)
    {
        $yearGrade = $grade + 1;
        $type = $this->isCombined ? __('Combined Attainment') : __('Attainment');
        $title = re('attainment_title', [
            'type' => $type,
            'grade' => $grade,
            'yearGrade' => $yearGrade,
            'subtitle' => $this->yearData['sub_title'],
            'year' => $this->yearData['year']->year,
        ]);

        $data = [
            'title' => $title,
            'student_type' => $this->getStudentTypeText($this->yearData['type']),
            'grade_data' => (object)$gradeData['grade_data'],
            'rounds' => (object)$gradeData['general_rounds'],
            'boys' => (object)$gradeData['category_data']['boys'],
            'girls' => (object)$gradeData['category_data']['girls'],
            'sen' => (object)$gradeData['category_data']['sen'],
            'g_t' => (object)$gradeData['category_data']['g_t'],
            'citizen' => (object)$gradeData['category_data']['citizen'],
            'boys_citizen' => (object)$gradeData['category_data']['boys_citizen'],
            'girls_citizen' => (object)$gradeData['category_data']['girls_citizen'],
        ];

        foreach ($this->subjects->pluck('id') as $step) {
            $data['step_' . $step] = (object)$gradeData['step_rounds'][$step];
        }
        return (object) $data;
    }

    private function getGradeStudentsCount($grade)
    {
        $type = $this->yearData['type'];
        $result = Student::query()
            ->selectRaw('
            COUNT(*) as students,
            SUM(CASE WHEN gender = "boy" THEN 1 ELSE 0 END) as boys_students,
            SUM(CASE WHEN gender = "girl" THEN 1 ELSE 0 END) as girls_students,
            SUM(CASE WHEN g_t = 1 THEN 1 ELSE 0 END) as g_t_students,
            SUM(CASE WHEN sen = 1 THEN 1 ELSE 0 END) as sen_students,
            SUM(CASE WHEN citizen = 1 THEN 1 ELSE 0 END) as citizen_students,
            SUM(CASE WHEN gender = "boy" AND citizen = 1 THEN 1 ELSE 0 END) as boys_citizen_students,
            SUM(CASE WHEN gender = "girl" AND citizen = 1 THEN 1 ELSE 0 END) as girls_citizen_students
        ')
            ->whereHas('level', function (Builder $query) use ($grade, $type) {
                $query->where('grade', $grade)
                    ->where('year_id', $this->yearData['register_year']);
                if ($type != 2) {
                    $query->where('arab', $type);
                }
            })
            ->when(!$this->request->get('include_sen', false), function ($query) {
                $query->where('sen', 0);
            })->when(!$this->request->get('include_g_t', false), function ($query) {
                $query->where('g_t', 0);
            })->whereIn('school_id', $this->schools->pluck('id'))
            ->first();

        $result = $result->toArray();
        $result['student_type'] = $this->getStudentTypeText($type);
        return $result;

    }

    private function getGradesStudentsCount($grades)
    {
        $type = $this->yearData['type'];
        $result = Student::query()
            ->selectRaw('
            COUNT(*) as students,
            SUM(CASE WHEN gender = "boy" THEN 1 ELSE 0 END) as boys_students,
            SUM(CASE WHEN gender = "girl" THEN 1 ELSE 0 END) as girls_students,
            SUM(CASE WHEN g_t = 1 THEN 1 ELSE 0 END) as g_t_students,
            SUM(CASE WHEN sen = 1 THEN 1 ELSE 0 END) as sen_students,
            SUM(CASE WHEN citizen = 1 THEN 1 ELSE 0 END) as citizen_students,
            SUM(CASE WHEN gender = "boy" AND citizen = 1 THEN 1 ELSE 0 END) as boys_citizen_students,
            SUM(CASE WHEN gender = "girl" AND citizen = 1 THEN 1 ELSE 0 END) as girls_citizen_students
        ')
            ->whereHas('level', function (Builder $query) use ($grades, $type) {
                $query->whereIn('grade', $grades)
                    ->where('year_id', $this->yearData['register_year']);
                if ($type != 2) {
                    $query->where('arab', $type);
                }
            })
            ->when(!$this->request->get('include_sen', false), function ($query) {
                $query->where('sen', 0);
            })->when(!$this->request->get('include_g_t', false), function ($query) {
                $query->where('g_t', 0);
            })
            ->whereIn('school_id', $this->schools->pluck('id'))
            ->first();

        return (object)$result->toArray();
    }

    private function renderReport($pages = [], $data = [])
    {
        $school = $this->school;
        $schools = $this->schools;
        $type = $this->yearData['type'];
        $year = $this->yearData['year'];
        $sections = $this->getSections();
        $isCombined = $this->isCombined;
        $title = $this->generateReportTitle();
        $report_info = $this->getReportInfo();
        $reportTitleGroup = $this->reportTitleGroup();
        $subjects = $this->subjects;
        $student_type = $this->request->get('student_type');
        return view('general.new_reports.attainment.attainment_report',
            compact('school', 'student_type','schools', 'type', 'pages', 'year', 'sections', 'data',
                'subjects','isCombined', 'title', 'report_info', 'reportTitleGroup'));
    }

    private function generateReportTitle()
    {
        $school = null;
        if (count($this->schools) == 1) {
            $school = ' - ' . $this->school->name;
        }
        $combinedText = $this->isCombined ? 'Combined ' : '';
        return $combinedText . 'Attainment Report' . $school . ' - ' . $this->yearData['year']->year . ' (' . $this->yearData['sub_title'] . ')';
    }

    private function getReportInfo()
    {
        $year = $this->yearData['year'];
        $grades = implode(', ', $this->request->get('grades', []));
        $sections = implode(',', $this->getSections());
        $student_type = $this->getSubTitle($this->request->get('student_type'));
        return [
            'school' => $this->schools->first()->name,
            'year' => $year->name,
            'grades' => $grades,
            'sections' => $sections,
            'student_type' => $student_type,
            'sen' => $this->request->get('include_sen', false) ? re('Included') : re('Not Included'),
            'g_t' => $this->request->get('include_g_t', false) ? re('Included') : re('Not Included'),
        ];
    }

    private function getStudentTypeText($type)
    {
        switch ($type) {
            case 0:
                return 'non_arabs';
            case 1:
                return 'arabs';
            case 2:
                return 'arabs_non_arabs';
            default:
                return '';
        }
    }

    public function reportTitleGroup()
    {
        $titleGroup = [];
        if ($this->isCombined) {
            $titleGroup['ar'] = 'تقرير التحصيل المدمج <br /> خلال العام الدراسي';
            $titleGroup['en'] = 'The combined attainment report  <br /> within the academic year';
        } else {
            $titleGroup['ar'] = 'تقرير التحصيل خلال العام الدراسي';
            $titleGroup['en'] = 'The attainment within the <br /> academic year';
        }
        return $titleGroup;
    }

}
