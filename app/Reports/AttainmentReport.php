<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Reports;

use App\Helpers\Constant;
use App\Models\Assessment;
use App\Models\Round;
use App\Models\School;
use App\Models\SchoolGradeRange;
use App\Models\SchoolHiddenSkill;
use App\Models\SchoolSkillRange;
use App\Models\Skill;
use App\Models\Student;
use App\Models\StudentTerm;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Year;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class AttainmentReport
{
    public $request;
    public $rounds;
    public $rounds_key;
    public $school;
    public $total_students;
    public function __construct(Request $request)
    {
        $this->request = $request;
        if (getGuard() == 'school') {
            $this->school = auth()->guard('school')->user();
        } else {
            $this->school = School::query()->findOrFail($this->request->school_id);
        }
        if ($this->school == 'Indian') {
            $this->rounds_key = Constant::OTHER_ROUNDS_KEY;
        } else {
            $this->rounds_key = Constant::ROUNDS_KEY;
        }
        $this->rounds = Constant::ROUNDS;
        $this->total_students = 0;
    }

    public function report()
    {

        $school = $this->school;
        $title = re('The attainment Report') . '-' . $this->school->name;
        $grades = $this->request->get('grades', []);
        $year = $this->request->get('year_id', null);

        $student_type = $this->request->get('student_type', null);
        $sections = $this->request->get('sections', []);
        $include_g_t = $this->request->get('include_g_t', 0);
        $include_sen = $this->request->get('include_sen', 0);

        $subjects = Subject::query()->get();

        $arab_grades = [];
        $non_arab_grades = [];
        $ordered_rounds = [];
        foreach ($this->rounds_key as $round_key) {
            $ordered_rounds[] = $this->rounds[$round_key];
        }

        if ($student_type == 1) {
            $arab_grades = $this->processSubject( 1, $this->school, $subjects, $grades, $year, $sections, $include_sen, $include_g_t, $ordered_rounds);
        }

        if ($student_type == 2) {
            $non_arab_grades = $this->processSubject( 2, $this->school, $subjects, $grades, $year, $sections, $include_sen, $include_g_t, $ordered_rounds);
        }

        if ($student_type == 0) {
            $arab_grades = $this->processSubject( 0, $this->school, $subjects, $grades, $year, $sections, $include_sen, $include_g_t, $ordered_rounds);
        }

        $rounds = $ordered_rounds;
        $year = Year::query()->findOrFail($year);

        $students_type = '';
        switch ($student_type) {
            case 0:
            {
                $students_type = re('Arab And Non Arab');
                break;
            }
            case 1:
            {
                $students_type = re('Arab');
                break;
            }
            case 2:
            {
                $students_type = re('Non Arab');
                break;
            }
        }
        $info_page = [
            'total_students' => $this->total_students,
            'year' => $year->name,
            'grades' => implode(',', $grades),
            'sections' => implode(',', $sections),
            'student_type' => $students_type,
            'sen' => $include_sen?re('Included'):re('Not Included'),
            'g&t' => $include_g_t?re('Included'):re('Not Included'),
        ];


        if ($this->request->get('summary', false)) {
            $title = re('Attainment Summary') . '-' . $this->school->name;
            return view('general.reports.attainment.report-summary', compact('school','info_page', 'title', 'non_arab_grades', 'arab_grades', 'rounds', 'grades', 'year', 'sections', 'include_g_t', 'include_sen', 'subjects', 'student_type'));
        }
        return view('general.reports.attainment.report', compact('school', 'info_page','title', 'non_arab_grades', 'arab_grades', 'rounds', 'grades', 'year', 'sections', 'include_g_t', 'include_sen', 'subjects', 'student_type'));
    }

    private function processSubject($is_arabic, $school, $subjects, $grades, $year, $sections, $include_sen, $include_g_t, $rounds)
    {
        $result = [];
        foreach ($grades as $grade) {
            $total = Student::query()
                ->withConditions($school, $grade, $year, $sections, $include_sen, $include_g_t, $is_arabic)
                ->count();

            $grade_rounds = [];
            $sen_grade_rounds = [];
            $g_t_grade_rounds = [];
            $boys_grade_rounds = [];
            $girls_grade_rounds = [];
            $local_grade_rounds = [];
            $boys_local_grade_rounds = [];
            $girls_local_grade_rounds = [];
            $skills_rounds = [];


            $base_assessments_query = StudentTerm::query()
                ->whereHas('student', function (Builder $query) use ($school, $grade, $year, $sections, $include_sen, $include_g_t, $is_arabic) {
                    $query->withConditions($school, $grade, $year, $sections, $include_sen, $include_g_t, $is_arabic);
                })
                ->where('corrected', 1)
                ->with(['student']);

            $above_condition = (object)[
                'from' => 70,
                'to' => 100,
            ];
            $inline_condition = (object)[
                'from' => 50,
                'to' => 69,
           ];
            $below_condition = (object)[
                'from' => 0,
                'to' => 49,
            ];

            if ($total == 0 || !$base_assessments_query->count())
            {
                continue;
            }


            foreach ($rounds as $round) {
                $round_total = 0;
                $sen_round_total = 0;
                $g_t_round_total = 0;
                $boys_round_total = 0;
                $girls_round_total = 0;
                $local_round_total = 0;
                $local_boys_round_total = 0;
                $local_girls_round_total = 0;

                $above_count = 0;
                $inline_count = 0;
                $below_count = 0;

                $sen_above_count = 0;
                $sen_inline_count = 0;
                $sen_below_count = 0;

                $g_t_above_count = 0;
                $g_t_inline_count = 0;
                $g_t_below_count = 0;

                $boys_above_count = 0;
                $boys_inline_count = 0;
                $boys_below_count = 0;

                $girls_above_count = 0;
                $girls_inline_count = 0;
                $girls_below_count = 0;

                $local_above_count = 0;
                $local_inline_count = 0;
                $local_below_count = 0;

                $boys_local_above_count = 0;
                $boys_local_inline_count = 0;
                $boys_local_below_count = 0;

                $girls_local_above_count = 0;
                $girls_local_inline_count = 0;
                $girls_local_below_count = 0;

                $skill_attainments = [];

                // Create a new query instance for each round
                $assessments_query = clone $base_assessments_query;

                $assessments_query->whereRelation('term', 'round', strtolower($round))->chunk(1000, function ($chunk) use (
                    &$round_total, &$above_count, &$inline_count, &$below_count,
                    &$sen_round_total, &$sen_above_count, &$sen_inline_count, &$sen_below_count,
                    &$g_t_round_total, &$g_t_above_count, &$g_t_inline_count, &$g_t_below_count,
                    &$boys_round_total, &$boys_above_count, &$boys_inline_count, &$boys_below_count,
                    &$girls_round_total, &$girls_above_count, &$girls_inline_count, &$girls_below_count,
                    &$local_round_total, &$local_boys_round_total, &$local_girls_round_total, &$local_above_count, &$local_inline_count, &$local_below_count,
                    &$boys_local_above_count, &$boys_local_inline_count, &$boys_local_below_count, &$girls_local_above_count, &$girls_local_inline_count, &$girls_local_below_count,
                    &$skill_attainments, $grade, $above_condition, $inline_condition, $below_condition, $subjects
                ) {
                    foreach ($chunk as $assessment) {
                        $round_total++;
                        $student = $assessment->student;

                        if ($assessment->total >= $above_condition->from && $assessment->total <= $above_condition->to) {
                            $above_count++;
                        } elseif ($assessment->total >= $inline_condition->from && $assessment->total <= $inline_condition->to) {
                            $inline_count++;
                        } elseif ($assessment->total >= $below_condition->from && $assessment->total <= $below_condition->to) {
                            $below_count++;
                        }

                        if ($student->sen) {
                            $sen_round_total++;
                            if ($assessment->total >= $above_condition->from && $assessment->total <= $above_condition->to) {
                                $sen_above_count++;
                            } elseif ($assessment->total >= $inline_condition->from && $assessment->total <= $inline_condition->to) {
                                $sen_inline_count++;
                            } elseif ($assessment->total >= $below_condition->from && $assessment->total <= $below_condition->to) {
                                $sen_below_count++;
                            }
                        }
                        if ($student->citizen) {
                            $local_round_total++;
                            if ($assessment->total >= $above_condition->from && $assessment->total <= $above_condition->to) {
                                $local_above_count++;
                            } elseif ($assessment->total >= $inline_condition->from && $assessment->total <= $inline_condition->to) {
                                $local_inline_count++;
                            } elseif ($assessment->total >= $below_condition->from && $assessment->total <= $below_condition->to) {
                                $local_below_count++;
                            }

                            if ($student->gender == 'boy') {
                                $local_boys_round_total++;
                                if ($assessment->total >= $above_condition->from && $assessment->total <= $above_condition->to) {
                                    $boys_local_above_count++;
                                } elseif ($assessment->total >= $inline_condition->from && $assessment->total <= $inline_condition->to) {
                                    $boys_local_inline_count++;
                                } elseif ($assessment->total >= $below_condition->from && $assessment->total <= $below_condition->to) {
                                    $boys_local_below_count++;
                                }
                            } elseif ($student->gender == 'girl') {
                                $local_girls_round_total++;
                                if ($assessment->total >= $above_condition->from && $assessment->total <= $above_condition->to) {
                                    $girls_local_above_count++;
                                } elseif ($assessment->total >= $inline_condition->from && $assessment->total <= $inline_condition->to) {
                                    $girls_local_inline_count++;
                                } elseif ($assessment->total >= $below_condition->from && $assessment->total <= $below_condition->to) {
                                    $girls_local_below_count++;
                                }
                            }


                        }

                        if ($student->{'g_t'}) {
                            $g_t_round_total++;
                            if ($assessment->total >= $above_condition->from && $assessment->total <= $above_condition->to) {
                                $g_t_above_count++;
                            } elseif ($assessment->total >= $inline_condition->from && $assessment->total <= $inline_condition->to) {
                                $g_t_inline_count++;
                            } elseif ($assessment->total >= $below_condition->from && $assessment->total <= $below_condition->to) {
                                $g_t_below_count++;
                            }
                        }

                        if ($student->gender == 'boy') {
                            $boys_round_total++;
                            if ($assessment->total >= $above_condition->from && $assessment->total <= $above_condition->to) {
                                $boys_above_count++;
                            } elseif ($assessment->total >= $inline_condition->from && $assessment->total <= $inline_condition->to) {
                                $boys_inline_count++;
                            } elseif ($assessment->total >= $below_condition->from && $assessment->total <= $below_condition->to) {
                                $boys_below_count++;
                            }
                        } elseif ($student->gender == 'girl') {
                            $girls_round_total++;
                            if ($assessment->total >= $above_condition->from && $assessment->total <= $above_condition->to) {
                                $girls_above_count++;
                            } elseif ($assessment->total >= $inline_condition->from && $assessment->total <= $inline_condition->to) {
                                $girls_inline_count++;
                            } elseif ($assessment->total >= $below_condition->from && $assessment->total <= $below_condition->to) {
                                $girls_below_count++;
                            }
                        }

                        foreach ($subjects as $skill) {
                            $assessment_skill_marks = (object)collect($assessment->subjects_marks)->firstWhere('subject_id', $skill->id);
                            if ($skill && $assessment_skill_marks) {
                                if (!isset($skill_attainments[$skill->id])) {
                                    $skill_attainments[$skill->id] = (object)[
                                        'skill' => $skill,
                                        'skill_id' => $skill->id,
                                        'total' => 0,
                                        'above' => 0,
                                        'above_percentage' => 0,
                                        'inline' => 0,
                                        'inline_percentage' => 0,
                                        'below' => 0,
                                        'below_percentage' => 0,
                                    ];
                                }

                                $skill_attainments[$skill->id]->total++;
                                $skill_above_condition = (object)$skill->marks_range['above'];
                                $skill_inline_condition = (object)$skill->marks_range['inline'];
                                $skill_below_condition = (object)$skill->marks_range['below'];
//                                dd($assessment_skill_marks, $skill_above_condition, $skill_inline_condition, $skill_below_condition);
                                if ($assessment_skill_marks->mark >= $skill_above_condition->from && $assessment_skill_marks->mark <= $skill_above_condition->to) {
                                    $skill_attainments[$skill->id]->above++;
                                } elseif ($assessment_skill_marks->mark >= $skill_inline_condition->from && $assessment_skill_marks->mark <= $skill_inline_condition->to) {
                                    $skill_attainments[$skill->id]->inline++;
                                } elseif ($assessment_skill_marks->mark >= $skill_below_condition->from && $assessment_skill_marks->mark <= $skill_below_condition->to) {
                                    $skill_attainments[$skill->id]->below++;
                                }
                            }
                        }
                    }
                });


                if ($round_total > 0) {
                    foreach ($skill_attainments as $skill_id => $skill_attainment) {
                        $skill_attainment->above_percentage = $skill_attainment->total > 0 ? round(($skill_attainment->above / $skill_attainment->total) * 100, 2) : 0;
                        $skill_attainment->inline_percentage = $skill_attainment->total > 0 ? round(($skill_attainment->inline / $skill_attainment->total) * 100, 2) : 0;
                        $skill_attainment->below_percentage = $skill_attainment->total > 0 ? round(($skill_attainment->below / $skill_attainment->total) * 100, 2) : 0;
                    }


                    $grade_rounds[$round] = (object)[
                        'round' => $round,
                        'total' => $round_total,
                        'above' => (object)[
                            'count' => $above_count,
                            'percentage' => $round_total > 0 ? round(($above_count / $round_total) * 100, 2) : 0,
                        ],
                        'inline' => (object)[
                            'count' => $inline_count,
                            'percentage' => $round_total > 0 ? round(($inline_count / $round_total) * 100, 2) : 0,
                        ],
                        'below' => (object)[
                            'count' => $below_count,
                            'percentage' => $round_total > 0 ? round(($below_count / $round_total) * 100, 2) : 0,
                        ],
                    ];
                    $sen_grade_rounds[$round] = (object)[
                        'round' => $round,
                        'total' => $sen_round_total,
                        'above' => (object)[
                            'count' => $sen_above_count,
                            'percentage' => $sen_round_total > 0 ? round(($sen_above_count / $sen_round_total) * 100, 2) : 0,
                        ],
                        'inline' => (object)[
                            'count' => $sen_inline_count,
                            'percentage' => $sen_round_total > 0 ? round(($sen_inline_count / $sen_round_total) * 100, 2) : 0,
                        ],
                        'below' => (object)[
                            'count' => $sen_below_count,
                            'percentage' => $sen_round_total > 0 ? round(($sen_below_count / $sen_round_total) * 100, 2) : 0,
                        ],
                    ];
                    $g_t_grade_rounds[$round] = (object)[
                        'round' => $round,
                        'total' => $g_t_round_total,
                        'above' => (object)[
                            'count' => $g_t_above_count,
                            'percentage' => $g_t_round_total > 0 ? round(($g_t_above_count / $g_t_round_total) * 100, 2) : 0,
                        ],
                        'inline' => (object)[
                            'count' => $g_t_inline_count,
                            'percentage' => $g_t_round_total > 0 ? round(($g_t_inline_count / $g_t_round_total) * 100, 2) : 0,
                        ],
                        'below' => (object)[
                            'count' => $g_t_below_count,
                            'percentage' => $g_t_round_total > 0 ? round(($g_t_below_count / $g_t_round_total) * 100, 2) : 0,
                        ],
                    ];
                    $boys_grade_rounds[$round] = (object)[
                        'round' => $round,
                        'total' => $boys_round_total,
                        'above' => (object)[
                            'count' => $boys_above_count,
                            'percentage' => $boys_round_total > 0 ? round(($boys_above_count / $boys_round_total) * 100, 2) : 0,
                        ],
                        'inline' => (object)[
                            'count' => $boys_inline_count,
                            'percentage' => $boys_round_total > 0 ? round(($boys_inline_count / $boys_round_total) * 100, 2) : 0,
                        ],
                        'below' => (object)[
                            'count' => $boys_below_count,
                            'percentage' => $boys_round_total > 0 ? round(($boys_below_count / $boys_round_total) * 100, 2) : 0,
                        ],
                    ];
                    $girls_grade_rounds[$round] = (object)[
                        'round' => $round,
                        'total' => $girls_round_total,
                        'above' => (object)[
                            'count' => $girls_above_count,
                            'percentage' => $girls_round_total > 0 ? round(($girls_above_count / $girls_round_total) * 100, 2) : 0,
                        ],
                        'inline' => (object)[
                            'count' => $girls_inline_count,
                            'percentage' => $girls_round_total > 0 ? round(($girls_inline_count / $girls_round_total) * 100, 2) : 0,
                        ],
                        'below' => (object)[
                            'count' => $girls_below_count,
                            'percentage' => $girls_round_total > 0 ? round(($girls_below_count / $girls_round_total) * 100, 2) : 0,
                        ],
                    ];
                    $local_grade_rounds[$round] = (object)[
                        'round' => $round,
                        'total' => $local_round_total,
                        'above' => (object)[
                            'count' => $local_above_count,
                            'percentage' => $local_round_total > 0 ? round(($local_above_count / $local_round_total) * 100, 2) : 0,
                        ],
                        'inline' => (object)[
                            'count' => $local_inline_count,
                            'percentage' => $local_round_total > 0 ? round(($local_inline_count / $local_round_total) * 100, 2) : 0,
                        ],
                        'below' => (object)[
                            'count' => $local_below_count,
                            'percentage' => $local_round_total > 0 ? round(($local_below_count / $local_round_total) * 100, 2) : 0,
                        ],
                    ];
                    $boys_local_grade_rounds[$round] = (object)[
                        'round' => $round,
                        'total' => $local_boys_round_total,
                        'above' => (object)[
                            'count' => $boys_local_above_count,
                            'percentage' => $local_boys_round_total > 0 ? round(($boys_local_above_count / $local_boys_round_total) * 100, 2) : 0,
                        ],
                        'inline' => (object)[
                            'count' => $boys_local_inline_count,
                            'percentage' => $local_boys_round_total > 0 ? round(($boys_local_inline_count / $local_boys_round_total) * 100, 2) : 0,
                        ],
                        'below' => (object)[
                            'count' => $boys_local_below_count,
                            'percentage' => $local_boys_round_total > 0 ? round(($boys_local_below_count / $local_boys_round_total) * 100, 2) : 0,
                        ],
                    ];
                    $girls_local_grade_rounds[$round] = (object)[
                        'round' => $round,
                        'total' => $local_girls_round_total,
                        'above' => (object)[
                            'count' => $girls_local_above_count,
                            'percentage' => $local_girls_round_total > 0 ? round(($girls_local_above_count / $local_girls_round_total) * 100, 2) : 0,
                        ],
                        'inline' => (object)[
                            'count' => $girls_local_inline_count,
                            'percentage' => $local_girls_round_total > 0 ? round(($girls_local_inline_count / $local_girls_round_total) * 100, 2) : 0,
                        ],
                        'below' => (object)[
                            'count' => $girls_local_below_count,
                            'percentage' => $local_girls_round_total > 0 ? round(($girls_local_below_count / $local_girls_round_total) * 100, 2) : 0,
                        ],
                    ];
                    $skills_rounds[$round] = $skill_attainments;
                }
            }

            if ($is_arabic == 1) {
                $student_type = re('For Arabs');
            } elseif ($is_arabic == 2) {
                $student_type = re('For Non-Arabs');
            } else {
                $student_type = re('For Arabs And Non-Arabs');
            }
            $result[$grade] = (object)[
                'title' => re('Grade') . ' ' . $grade . '/' . re('Year') . ' ' . ($grade + 1) . ' ' . $student_type,
                'grade' => $grade,
                'total' => $total,
                'rounds' => $grade_rounds,
                'skills' => $skills_rounds,
                'sen' => $sen_grade_rounds,
                'g_t' => $g_t_grade_rounds,
                'boys' => $boys_grade_rounds,
                'girls' => $girls_grade_rounds,
                'local' => $local_grade_rounds,
                'local_boys' => $boys_local_grade_rounds,
                'local_girls' => $girls_local_grade_rounds,
            ];
        }

        return $result;
    }
}
