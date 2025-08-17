<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Reports\NewReports;

use App\Models\QuestionStandard;
use App\Models\Student;
use App\Models\StudentTerm;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class StudentReport
{
    public $student_id, $school_id, $data,$subjects;

    public function __construct($student_id, $school_id = null)
    {
        $this->student_id = $student_id;
        $this->school_id = $school_id;
        $this->data = request()->get('data', false);
        $this->subjects = Subject::query()->get();
    }

    public function report()
    {

        $data = json_decode($this->data, 1);
        if (request()->get('ranges_type', false)) {
            $rangesType = request()->get('ranges_type', 1);
        } elseif (isset($data['report_type']) && !is_null($data['report_type'])) {
            $rangesType = $data['report_type'];
        } else {
            $rangesType = 2;
        }

        $student = Student::query()->with(['school', 'level'])
            ->when($this->school_id && !is_null($this->school_id), function ($query) {
                $query->where('school_id', $this->school_id);
            })->findOrFail($this->student_id);

        $terms = StudentTerm::query()->with(['term.level.year'])
            ->where('student_id', $student->id)
            ->where('corrected', 1)
            ->orderBy('term_id')
            ->get();
        $school = $student->school;

        if (in_array($student->school->school_type, ['Indian', 'Pakistan', 'Bangladeshi'])) {
            $months = ['may', 'september', 'february'];
        } else {
            $months = ['september', 'february', 'may'];
        }

        $studentTermFirst = $terms->filter(function ($value, $key) use ($months) {
            return $value->term->round == $months[0];
        })->first();
        $studentTermSecond = $terms->filter(function ($value, $key) use ($months) {
            return $value->term->round == $months[1];
        })->first();
        $studentTermThird = $terms->filter(function ($value, $key) use ($months) {
            return $value->term->round == $months[2];
        })->first();


        $terms_progress = [];
        if ($studentTermFirst && $studentTermSecond && $studentTermThird) {
            $terms_progress[$months[0]] = 'Starting point for the current year';
            $terms_progress[$months[1]] = '-';
            $terms_progress[$months[2]] = '-';
        } elseif ($studentTermSecond && $studentTermThird) {
            $terms_progress[$months[1]] = 'Starting point for the current year';
        } elseif ($studentTermFirst && $studentTermSecond) {
            $terms_progress[$months[0]] = 'Starting point for the current year';
        } elseif ($studentTermFirst && $studentTermThird) {
            $terms_progress[$months[0]] = 'Starting point for the current year';
        } else {
            $terms_progress[$months[0]] = '-';
            $terms_progress[$months[1]] = '-';
            $terms_progress[$months[2]] = '-';
        }


        if ($studentTermSecond && $studentTermThird) {
            $total_mark_3 = $studentTermThird->total;
            $total_mark_2 = $studentTermSecond->total;
            $total_mar_result = $total_mark_3 - $total_mark_2;
            $terms_progress[$months[2]] = getProgressText($total_mark_2, $total_mar_result);
        }
        if ($studentTermFirst && $studentTermThird) {
            $total_mark_2 = $studentTermThird->total;
            $total_mark_1 = $studentTermFirst->total;
            $total_mar_result = $total_mark_2 - $total_mark_1;
            $terms_progress[$months[2]] = getProgressText($total_mark_2, $total_mar_result);
        }
        if ($studentTermFirst && $studentTermSecond) {
            $total_mark_2 = $studentTermSecond->total;
            $total_mark_1 = $studentTermFirst->total;
            $total_mar_result = $total_mark_2 - $total_mark_1;
            $terms_progress[$months[1]] = getProgressText($total_mark_2, $total_mar_result);
        }


        $id_terms = $terms->unique()->pluck('term_id')->all();


        $studentId = $student->id;
        $school_id = $school->id;
        $studentInLevelSql = "
(
    SELECT
        IFNULL(
            ROUND(
                COUNT(
                    CASE
                        WHEN sts.mark >=
                            CASE
                                WHEN question_standards.mark = 1 THEN 1
                                WHEN question_standards.mark = 2 THEN 2
                                WHEN question_standards.mark = 10 THEN 10
                                ELSE 1
                            END
                        THEN 1 ELSE NULL
                    END
                ) * 100.0 / COUNT(*), 1
            ),
            0
        )
    FROM student_term_standards sts
    JOIN student_terms stt ON stt.id = sts.student_term_id
    JOIN students st ON st.id = stt.student_id
    WHERE st.school_id = $school_id
      AND sts.question_standard_id = question_standards.id
    GROUP BY sts.question_standard_id
    LIMIT 1
) AS student_in_level
";




        $studentInSystemSql = "
    (SELECT
        IFNULL(
            ROUND(
                COUNT(CASE WHEN sts.mark >=
                    CASE
                        WHEN question_standards.mark = 1 THEN 1
                        WHEN question_standards.mark = 2 THEN 2
                        WHEN question_standards.mark = 10 THEN 10
                        ELSE 1
                    END
                THEN 1 ELSE NULL END) * 100.0 / COUNT(*), 1
            ), 0
        )
    FROM student_term_standards sts
    WHERE sts.question_standard_id = question_standards.id
    GROUP BY sts.question_standard_id
    LIMIT 1
    ) AS student_in_system";

        // Step 3: Query standards with relationships and additional conditions
        $standards = QuestionStandard::query()
            ->select([
                'question_standards.*',
                DB::raw($studentInLevelSql),
                DB::raw($studentInSystemSql)
            ])
            ->with(['studentTermStandards.studentTerm' => function ($query) use ($studentId) {
                $query->where('student_id', $studentId);
            }, 'question'])
            ->whereHas('question', function (Builder $query) use ($id_terms) {
                $query->whereIn('term_id', $id_terms);
                $query->whereIn('subject_id', $this->subjects->pluck('id')->all());
            })
            ->orderBy('question_id', 'asc')
            ->get();


        $student_terms = array();
        foreach ($terms as $term) {
            $progress_name = $terms_progress[$term->term->round];
            $expectation = $term->expectation;
            $data = [
                'total' => $term->total,
                'expectation' => $expectation,
                'css_class' => strtolower($expectation),
                'term' => $term->term->short_name,
                'month' => $term->term->round,
                'progress' => $progress_name,
                'progress_class' => explode(' ', strtolower($progress_name))[0],
            ];
            foreach ($this->subjects as $subject) {
                $data['mark_step'.$subject->id] = collect($term->subjects_marks)->where('subject_id', $subject->id)->first()['mark'];
            }
            $student_terms []= (object) $data;
        }


        $last_term = $terms->sortByDesc('id')->first();


        if ($last_term) {
            $student_internal_and_country = StudentTerm::query()
                ->with(['student' => function ($query) {
                    $query->select('id', 'school_id', 'level_id');
                }])
                ->where('corrected', 1)
                ->when(!in_array($student->school->school_type, ['Indian', 'Pakistan', 'Bangladeshi']), function ($query) use ($student) {
                    $query->whereHas('term', function (Builder $query) use ($student) {
                        $query->where('level_id', $student->level_id);
                    });
                })
                ->where('term_id', $last_term->term_id)
                ->whereHas('student', function (Builder $query) use ($school, $student) {
                    $query->whereHas('school', function (Builder $query) use ($school, $student) {
                        $query->where('country', $school->country);
                    });
                    $query->where('level_id', $student->level_id);
                })
                ->select('id', 'student_id', 'term_id', 'corrected', 'total')
                ->orderByDesc('total')->get();

            //filter by student school
            $student_internal_terms = $student_internal_and_country->filter(function ($value, $key) use ($school, $last_term) {
                return $value->student->school_id == $school->id && $value->term_id == $last_term->term_id;
            });
            $sum_student_internal_terms = $student_internal_terms
                ->sum('total');

            $student_internal_terms_count = $student_internal_terms->count();


            $result_student_internal = array_search($student->id, $student_internal_terms->pluck('student_id')->toArray());
            $student_internal = $result_student_internal >= '0' ? $result_student_internal + 1 : 0;


//            $student_external_terms = StudentTerm::query()
//                ->where('corrected', 1)
//                ->whereHas('term', function (Builder $query) use ($student) {
//                    $query->where('level_id', $student->level_id);
//                })
//                ->selectRaw('id,student_id,term_id,corrected,total,max(id) as last')
//                ->groupBy('student_id')
//                ->orderByDesc('total')->get();

            $lastIds = StudentTerm::query()
                ->selectRaw('MAX(id) as id')
                ->where('corrected', 1)
                ->whereHas('term', function (Builder $query) use ($student) {
                    $query->where('level_id', $student->level_id);
                })
                ->groupBy('student_id');

            $student_external_terms = StudentTerm::query()
                ->whereIn('id', $lastIds)
                ->orderByDesc('total')
                ->get();

            $sum_student_external_terms = $student_external_terms->sum('total');
            $student_external_terms_count = $student_external_terms->count();

            $result_student_external = array_search($student->id, $student_external_terms->pluck('student_id')->toArray());
            $student_external = $result_student_external >= '0' ? $result_student_external + 1 : 0;

            $student_country_terms = $student_internal_and_country;

            $sum_student_country_terms = $student_country_terms->sum('total');
            $student_country_terms_count = $student_country_terms->count();

            $result_student_country = array_search($student->id, $student_country_terms->pluck('student_id')->toArray());
            $student_country = $result_student_country >= '0' ? $result_student_country + 1 : 0;

        } else {
            $student_internal = '-';
            $student_external = '-';
            $student_country = '-';
            $student_internal_terms_count = '-';
            $sum_student_internal_terms = '-';
            $student_external_terms_count = '-';
            $sum_student_external_terms = '-';
            $student_country_terms_count = '-';
            $sum_student_country_terms = '-';
        }

        $subjects = $this->subjects;
        $title = 'The Student Report - ' .$student->name;

        return view('general.new_reports.student.student_report', compact(
            'standards', 'student_terms', 'sum_student_external_terms', 'sum_student_internal_terms',
            'student', 'terms', 'school', 'student_external', 'student_internal', 'student_internal_terms_count',
            'student_external_terms_count', 'student_country', 'student_country_terms_count', 'sum_student_country_terms', 'rangesType', 'subjects', 'title'))->render();
    }

    public function reportCard()
    {
        $data = json_decode($this->data, 1);
        if (request()->get('ranges_type', false)) {
            $rangesType = request()->get('ranges_type', 1);
        } elseif (isset($data['report_type']) && !is_null($data['report_type'])) {
            $rangesType = $data['report_type'];
        } else {
            $rangesType = 2;
        }

        $student = Student::query()
            ->with(['school', 'level'])
            ->when($this->school_id && !is_null($this->school_id), function ($query) {
                $query->where('school_id', $this->school_id);
            })
            ->findOrFail($this->student_id);

        $terms = StudentTerm::query()
            ->with(['term.level'])
            ->where('student_id', $student->id)
            ->where('corrected', 1)
            ->orderBy('term_id')
            ->get();
        $school = $student->school;

        if (in_array($student->school->school_type, ['Indian', 'Pakistan', 'Bangladeshi'])) {
            $months = ['may','september','february'];
        } else {
            $months = ['september','february','may'];
        }

        $studentTermFirst = $terms->filter(function ($value, $key) use ($months) {
            return $value->term->round == $months[0];
        })->first();
        $studentTermSecond = $terms->filter(function ($value, $key) use ($months) {
            return $value->term->round == $months[1];
        })->first();
        $studentTermThird = $terms->filter(function ($value, $key) use ($months) {
            return $value->term->round == $months[2];
        })->first();


        $terms_progress = [];
        if ($studentTermFirst && $studentTermSecond && $studentTermThird) {
            $terms_progress[$months[0]] = 'Starting point for the current year';
            $terms_progress[$months[1]] = '-';
            $terms_progress[$months[2]] = '-';
        } elseif ($studentTermSecond && $studentTermThird) {
            $terms_progress[$months[1]] = 'Starting point for the current year';
        } elseif ($studentTermFirst && $studentTermSecond) {
            $terms_progress[$months[0]] = 'Starting point for the current year';
        } elseif ($studentTermFirst && $studentTermThird) {
            $terms_progress[$months[0]] = 'Starting point for the current year';
        } else {
            $terms_progress[$months[0]] = '-';
            $terms_progress[$months[1]] = '-';
            $terms_progress[$months[2]] = '-';
        }


        if ($studentTermSecond && $studentTermThird) {
            $total_mark_3 = $studentTermThird->total;
            $total_mark_2 = $studentTermSecond->total;
            $total_mar_result = $total_mark_3 - $total_mark_2;
            $terms_progress[$months[2]] = getProgressText($total_mark_2, $total_mar_result, $rangesType);
        }
        if ($studentTermFirst && $studentTermThird) {
            $total_mark_2 = $studentTermThird->total;
            $total_mark_1 = $studentTermFirst->total;
            $total_mar_result = $total_mark_2 - $total_mark_1;
            $terms_progress[$months[2]] = getProgressText($total_mark_2, $total_mar_result, $rangesType);
        }
        if ($studentTermFirst && $studentTermSecond) {
            $total_mark_2 = $studentTermSecond->total;
            $total_mark_1 = $studentTermFirst->total;
            $total_mar_result = $total_mark_2 - $total_mark_1;
            $terms_progress[$months[1]] = getProgressText($total_mark_2, $total_mar_result, $rangesType);
        }


        $names = array();
        $student_terms = array();
        foreach ($terms as $term) {
            $progress_name = $terms_progress[$term->term->round];
            $expectation = $term->expectation;
           $data = [
                'total' => $term->total,
                'month' => $term->term->round,
                'css_class' => strtolower($expectation),
                'expectation' => $expectation,
                'term' => $term->term->short_name,
                'progress' => $progress_name,
                'progress_class' => explode(' ', strtolower($progress_name))[0],
            ];
            foreach ($this->subjects as $subject) {
                $data['mark_step'.$subject->id] = collect($term->subjects_marks)->where('subject_id', $subject->id)->first()['mark'];
            }
            $student_terms[] = (object) $data;
        }

        $subjects = $this->subjects;

        $title = re('The Student Report Card'). '-' .$student->name;
        return view('general.new_reports.student.student_report_card', compact('names', 'student_terms', 'student',
            'title','school','subjects', 'rangesType'))->render();
    }


}
