<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
Optimized Version - Students with StudentTerms Relation
*/

namespace App\Reports\NewReports;

use App\Models\School;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Year;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class ProgressReport
{
    private $school;
    private $schools;
    public $request;
    private const CHUNK_SIZE = 250; // Process students in chunks
    private const MEMORY_THRESHOLD = 50 * 1024 * 1024; // 50MB

    private $months;
    private $yearData;
    private $subjects;

    private $isCombined = false;


    public function __construct(Request $request, $schools = [])
    {
        $schools = School::query()->whereIn('id', $schools)->get();
        $this->school = $schools->first();
        $this->schools = $schools;
        $this->request = $request;
        $this->subjects = Subject::query()->get();
    }

    public function report()
    {
        $this->initializeReportParameters();
        $ranges_type = $this->request->get('ranges_type', false);
        $type = $this->request->get('student_type', false);
        $grades = $this->request->get('grades', []);

        $sections = $this->getSections();

        $rounds = [
            $this->months[0] . ' - ' . $this->months[1],
            $this->months[1] . ' - ' . $this->months[2],
            $this->months[0] . ' - ' . $this->months[2],
        ];

        $pages = [];
        $totalStudentsProcessed = 0;

        foreach ($grades as $grade) {
            $gradeStartTime = microtime(true);

            // Get student count first to determine processing strategy
            $studentCount = $this->getStudentCount($grade, $type, $sections);

            if ($studentCount > 0) {

                $students = $studentCount > self::CHUNK_SIZE
                    ? $this->getStudentsWithTermsChunked($grade, $type, $sections, $this->months)
                    : $this->getStudentsWithTerms($grade, $type, $sections, $this->months);

                if ($students->count()) {

                    $progressData = [
                        $rounds[0] => $this->calculateProgress($students, $this->months[0], $this->months[1], $ranges_type),
                        $rounds[1] => $this->calculateProgress($students, $this->months[1], $this->months[2], $ranges_type),
                        $rounds[2] => $this->calculateProgress($students, $this->months[0], $this->months[2], $ranges_type),
                    ];

                    $pages[$grade] = $this->formatGradeData($progressData, $rounds, $grade);
                    $totalStudentsProcessed += $students->count();
                }
            }
        }

        // Performance logging

        $data['student_statistics'] = $this->getGradesStudentsCount($grades);
        return $this->renderReport($pages, $data);
    }

    public function reportCombined()
    {
        $this->initializeReportParameters();
        $this->isCombined = true;
        $type = $this->request->get('student_type', false);
        $grades = $this->request->get('grades', []);

        $sections = $this->getSections();

        $rounds = [
            $this->months[0] . ' - ' . $this->months[1],
            $this->months[1] . ' - ' . $this->months[2],
            $this->months[0] . ' - ' . $this->months[2],
        ];

        $pages = [];
        $totalStudentsProcessed = 0;

        $gradeStartTime = microtime(true);

        // Get student count first to determine processing strategy
        $studentCount = $this->getStudentCount($grades, $type, $sections);

        if ($studentCount > 0) {

            $students = $studentCount > self::CHUNK_SIZE
                ? $this->getStudentsWithTermsChunked($grades, $type, $sections, $this->months)
                : $this->getStudentsWithTerms($grades, $type, $sections, $this->months);

            if ($students->count()) {

                $progressData = [
                    $rounds[0] => $this->calculateProgress($students, $this->months[0], $this->months[1]),
                    $rounds[1] => $this->calculateProgress($students, $this->months[1], $this->months[2],),
                    $rounds[2] => $this->calculateProgress($students, $this->months[0], $this->months[2]),
                ];

                $pages[] = $this->formatGradeData($progressData, $rounds, $grades);
                $totalStudentsProcessed += $students->count();
            }
        }

        // Memory management
        if (memory_get_usage() > self::MEMORY_THRESHOLD) {
            gc_collect_cycles();
        }
        // Performance logging
        $data = [];
        return $this->renderReport($pages, $data);
    }

    private function initializeReportParameters()
    {
        $yearId = $this->request->get('year_id', false);
        $reportType = $this->request->get('ranges_type', false);
        $type = $this->request->get('student_type', false);

        $year = Year::query()->findOrFail($yearId);
        $this->yearData = [
            'year' => $year,
            'type' => $type,
            'sub_title' => $this->getSubTitle($type),
            'ranges_type' => $reportType
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

    private function getStudentCount($grade, $type, $sections): int
    {
        $year_id = $this->yearData['register_year'];
        return Student::query()
            ->whereIn('school_id', $this->schools->pluck('id'))
            ->when(count($sections), function ($query) use ($sections) {
                $query->whereIn('grade_name', $sections);
            })
            ->whereHas('level', function ($query) use ($grade, $year_id, $type) {
                if (is_array($grade)) {
                    $query->whereIn('grade', $grade);
                } else {
                    $query->where('grade', $grade);
                }
                $query->when($type != 2, function ($q) use ($type) {
                    $q->where('arab', $type);
                })
                    ->where(function ($yearQuery) use ($year_id) {
                        $yearQuery->where('year_id', $year_id);
                    });
            })
            //check included SEN and G&T students
            ->when(!$this->request->get('include_sen', false), function ($query) {
                $query->where('sen', 0);
            })
            ->when(!$this->request->get('include_g_t', false), function ($query) {
                $query->where('g_t', 0);
            })
            ->count();
    }

    private function getStudentsWithTermsChunked($grade, $type, $sections, $months): Collection
    {
        $year_id = $this->yearData['year']->id;
        $register_year = $this->yearData['register_year'];
        $results = collect();

        Student::query()
            ->select('id', 'school_id', 'gender', 'citizen', 'sen', 'g_t', 'grade_name')
            ->whereIn('school_id', $this->schools->pluck('id'))
            ->when(count($sections), function ($query) use ($sections) {
                $query->whereIn('grade_name', $sections);
            })
            //check included SEN and G&T students
            ->when(!$this->request->get('include_sen', false), function ($query) {
                $query->where('sen', 0);
            })
            ->when(!$this->request->get('include_g_t', false), function ($query) {
                $query->where('g_t', 0);
            })
            ->whereHas('level', function ($query) use ($grade, $register_year, $type) {
                if (is_array($grade)) {
                    $query->whereIn('grade', $grade);
                } else {
                    $query->where('grade', $grade);
                }
                $query->when($type != 2, function ($q) use ($type) {
                    $q->where('arab', $type);
                })
                    ->where(function ($yearQuery) use ($register_year) {
                        $yearQuery->where('year_id', $register_year);
                    });
            })
            ->chunk(self::CHUNK_SIZE, function ($students) use (&$results, $year_id, $register_year, $months) {
                $students->load(['student_terms' => function ($query) use ($year_id, $register_year, $months) {
                    $query->select('id', 'student_id', 'term_id', 'total')
                        ->with(['term' => function ($termQuery) {
                            $termQuery->select('id', 'round', 'level_id');
                        }])
                        ->whereHas('term.level', function ($q) use ($year_id, $register_year, $months) {
                            $q->where(function ($yearQuery) use ($year_id, $register_year, $months) {
                                $yearQuery->where(function ($q1) use ($register_year, $months) {
                                    $q1->where('year_id', $register_year)->where('round', $months[0]);
                                })->orWhere(function ($q2) use ($year_id, $months) {
                                    $q2->where('year_id', $year_id)->whereIn('round', [$months[1], $months[2]]);
                                });
                            });
                        })
                        ->where('corrected', 1);
                }]);

                $results = $results->merge($students);
            });

        return $results;
    }

    private function getStudentsWithTerms($grade, $type, $sections, $months): Collection
    {
        $year_id = $this->yearData['year']->id;
        $register_year = $this->yearData['register_year'];
        return Student::query()
            ->select('id', 'school_id', 'gender', 'citizen', 'sen', 'g_t', 'grade_name')
            ->with(['student_terms' => function ($query) use ($year_id, $register_year, $months) {
                $query->select('id', 'student_id', 'term_id', 'total')
                    ->with(['term' => function ($termQuery) {
                        $termQuery->select('id', 'round', 'level_id');
                    }])
                    ->whereHas('term', function ($q) use ($year_id, $register_year, $months) {
                        $q->where(function ($yearQuery) use ($year_id, $register_year, $months) {
                            $yearQuery->whereHas('level',function ($q1) use ($register_year, $months) {
                                $q1->where('year_id', $register_year)->where('round', $months[0]);
                            })->orWhere(function ($q2) use ($year_id, $months) {
                                $q2->whereHas('level',function ($query)use ($year_id, $months){
                                    $query->where('year_id', $year_id)->whereIn('round', [$months[1], $months[2]]);
                                });
                            });
                        });
                    })
                    ->where('corrected', 1);
            }])
            ->whereIn('school_id', $this->schools->pluck('id'))
            ->when(count($sections), function ($query) use ($sections) {
                $query->whereIn('grade_name', $sections);
            })
            //check included SEN and G&T students
            ->when(!$this->request->get('include_sen', false), function ($query) {
                $query->where('sen', 0);
            })
            ->when(!$this->request->get('include_g_t', false), function ($query) {
                $query->where('g_t', 0);
            })
            ->whereHas('level', function ($query) use ($grade, $year_id, $register_year, $type) {
                if (is_array($grade)) {
                    $query->whereIn('grade', $grade);
                } else {
                    $query->where('grade', $grade);
                }
                $query->when($type != 2, function ($q) use ($type) {
                    $q->where('arab', $type);
                })
                    ->where(function ($yearQuery) use ($register_year) {
                        $yearQuery->where('year_id', $register_year);
                    });
            })
            ->get();
    }

    private function calculateProgress($students, $fromMonth, $toMonth): array
    {
        $counters = $this->initializeCounters();

        foreach ($students as $student) {
            // Check if studentTerms relationship is loaded and not empty
            if (!$student->relationLoaded('student_terms') || $student->student_terms->isEmpty()) {
                continue;
            }

            $fromTerm = $student->student_terms->first(function ($studentTerm) use ($fromMonth) {
                return isset($studentTerm->term) && $studentTerm->term->round === $fromMonth;
            });

            $toTerm = $student->student_terms->first(function ($studentTerm) use ($toMonth) {
                return isset($studentTerm->term) && $studentTerm->term->round === $toMonth;
            });

            if ($fromTerm && $toTerm &&
                isset($fromTerm->total) && isset($toTerm->total)) {
                $progressRate = getProgress(
                    $fromTerm->total, $toTerm->total - $fromTerm->total);

                $this->updateCounters($counters, $student, $progressRate);
            }
        }

        return $this->formatProgressData($counters, $fromMonth . ' - ' . $toMonth);
    }

    private function initializeCounters(): array
    {
        return [
            'general' => ['above' => 0, 'same' => 0, 'below' => 0],
            'boys' => ['above' => 0, 'same' => 0, 'below' => 0],
            'girls' => ['above' => 0, 'same' => 0, 'below' => 0],
            'sen' => ['above' => 0, 'same' => 0, 'below' => 0],
            'g_t' => ['above' => 0, 'same' => 0, 'below' => 0],
            'citizen' => ['above' => 0, 'same' => 0, 'below' => 0],
            'citizen_boys' => ['above' => 0, 'same' => 0, 'below' => 0],
            'citizen_girls' => ['above' => 0, 'same' => 0, 'below' => 0],
        ];
    }

    private function updateCounters(&$counters, $student, $progressRate): void
    {
//        $category = match($progressRate) {
//            1 => 'above',
//            0 => 'same',
//            default => 'below'
//        };
        switch ($progressRate) {
            case 1:
                $category = 'above';
                break;
            case 0:
                $category = 'same';
                break;
            default:
                $category = 'below';
                break;
        }

        $counters['general'][$category]++;

        $genderKey = $student->gender == 'boy' ? 'boys' : 'girls';
        $counters[$genderKey][$category]++;

        if ($student->citizen == 1) {
            $counters['citizen'][$category]++;
            $counters['citizen_' . $genderKey][$category]++;
        }

        if ($student->sen == 1) {
            $counters['sen'][$category]++;
        }

        if ($student->g_t == 1) {
            $counters['g_t'][$category]++;
        }
    }

    private function formatProgressData($counters, $name): array
    {
        $result = [];

        foreach ($counters as $type => $data) {
            $total = array_sum($data);
            $result[$type] = [
                'name' => $name,
                'above' => $data['above'],
                'above_ratio' => $total > 0 ? round(($data['above'] / $total) * 100, 2) : 0,
                'inline' => $data['same'],
                'inline_ratio' => $total > 0 ? round(($data['same'] / $total) * 100, 2) : 0,
                'below' => $data['below'],
                'below_ratio' => $total > 0 ? round(($data['below'] / $total) * 100, 2) : 0,
                'total' => $total,
            ];
        }

        return $result;
    }

    private function formatGradeData($progressData, $rounds, $grade): object
    {
        $sub_title = $this->yearData['sub_title'];
        $year = $this->yearData['year'];
        $type = $this->isCombined ? __('Combined Progress') : __('Progress');
        if ($this->isCombined) {
            $title = re('combined_progress_title', [
                'type' => $type,
                'grade' => implode(',', $grade),
                'subtitle' => $this->yearData['sub_title'],
                'year' => $this->yearData['year']->name,
            ]);
        } else {
            $yearGrade = $grade + 1;
            $title = re('progress_title', [
                'type' => $type,
                'grade' => $grade,
                'yearGrade' => $yearGrade,
                'subtitle' => $this->yearData['sub_title'],
                'year' => $this->yearData['year']->name,
            ]);
        }

        return (object)[
            'title' => $title,
            'student_type' => $this->getStudentTypeText($this->yearData['type']),
            'grade_data' => (object)$this->getGradeStudentsData($grade),
            'septProgressData' => $progressData[$rounds[0]]['general'],
            'febProgressData' => $progressData[$rounds[1]]['general'],
            'mayProgressData' => $progressData[$rounds[2]]['general'],
            'boys' => (object)[
                $rounds[0] => $progressData[$rounds[0]]['boys'],
                $rounds[1] => $progressData[$rounds[1]]['boys'],
                $rounds[2] => $progressData[$rounds[2]]['boys'],
            ],
            'girls' => (object)[
                $rounds[0] => $progressData[$rounds[0]]['girls'],
                $rounds[1] => $progressData[$rounds[1]]['girls'],
                $rounds[2] => $progressData[$rounds[2]]['girls'],
            ],
            'sen' => (object)[
                $rounds[0] => $progressData[$rounds[0]]['sen'],
                $rounds[1] => $progressData[$rounds[1]]['sen'],
                $rounds[2] => $progressData[$rounds[2]]['sen'],
            ],
            'g_t' => (object)[
                $rounds[0] => $progressData[$rounds[0]]['g_t'],
                $rounds[1] => $progressData[$rounds[1]]['g_t'],
                $rounds[2] => $progressData[$rounds[2]]['g_t'],
            ],
            'citizen' => (object)[
                $rounds[0] => $progressData[$rounds[0]]['citizen'],
                $rounds[1] => $progressData[$rounds[1]]['citizen'],
                $rounds[2] => $progressData[$rounds[2]]['citizen'],
            ],
            'citizen_boys' => (object)[
                $rounds[0] => $progressData[$rounds[0]]['citizen_boys'],
                $rounds[1] => $progressData[$rounds[1]]['citizen_boys'],
                $rounds[2] => $progressData[$rounds[2]]['citizen_boys'],
            ],
            'citizen_girls' => (object)[
                $rounds[0] => $progressData[$rounds[0]]['citizen_girls'],
                $rounds[1] => $progressData[$rounds[1]]['citizen_girls'],
                $rounds[2] => $progressData[$rounds[2]]['citizen_girls'],
            ],
        ];
    }

    private function getGradeStudentsData($grade)
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
                if (is_array($grade)) {
                    $query->whereIn('grade', $grade);
                } else {
                    $query->where('grade', $grade);
                }
                $query->where('year_id', $this->yearData['register_year']);
                if ($type != 2) {
                    $query->where('arab', $type);
                }
            })
            //check included SEN and G&T students
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
            //check included SEN and G&T students
            ->when(!$this->request->get('include_sen', false), function ($query) {
                $query->where('sen', 0);
            })->when(!$this->request->get('include_g_t', false), function ($query) {
                $query->where('g_t', 0);
            })
            ->whereIn('school_id', $this->schools->pluck('id'))
            ->first();

        return (object)$result->toArray();
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

    private function renderReport($pages = [], $data = [])
    {
        $school = $this->school;
        $schools = $this->schools;
        $type = $this->yearData['type'];
        $year = $this->yearData['year'];
        $rangesType = $this->yearData['ranges_type'];
        $sections = $this->getSections();
        $isCombined = $this->isCombined;
        $title = $this->generateReportTitle();
        $report_info = $this->getReportInfo();
        $rounds = [
            $this->months[0] . ' - ' . $this->months[1],
            $this->months[1] . ' - ' . $this->months[2],
            $this->months[0] . ' - ' . $this->months[2],
        ];
//        dd($pages);
        $reportTitleGroup = $this->reportTitleGroup();
        return view('general.new_reports.progress.progress_report',
            compact('school', 'schools', 'type', 'pages', 'year', 'rangesType', 'sections', 'data', 'isCombined', 'title', 'report_info', 'rounds', 'reportTitleGroup'));
    }

    private function generateReportTitle()
    {
        $school = null;
        if (count($this->schools) == 1) {
            $school = ' - ' . $this->school->name;
        }
        $combinedText = $this->isCombined ? 'Combined ' : '';
        return $combinedText . 'Progress Report' . $school . ' - ' . $this->yearData['year']->name . ' (' . $this->yearData['sub_title'] . ')';
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

    public function reportTitleGroup()
    {
        $titleGroup = [];
        if ($this->isCombined) {
            $titleGroup['ar'] = 'تقرير التقدم المدمج <br /> خلال العام الدراسي';
            $titleGroup['en'] = 'The combined progress report  <br /> within the academic year';
        } else {
            $titleGroup['ar'] = 'تقرير التقدم خلال العام الدراسي';
            $titleGroup['en'] = 'The progress within the <br /> academic year';
        }
        return $titleGroup;
    }
}
