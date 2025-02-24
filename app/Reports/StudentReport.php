<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Reports;

use App\Models\QuestionStandard;
use App\Models\Student;
use App\Models\StudentTerm;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StudentReport
{
    public $student_id, $school_id, $data;
    private const CACHE_TTL = 60; // 1 Minute

    public function __construct($student_id, $school_id = null)
    {
        $this->student_id = $student_id;
        $this->school_id = $school_id;
        $this->data = request()->get('data', false);
    }

    private function generateCacheKey(): string
    {
        return sprintf(
            'student_report_%s_%s',
            $this->school_id,
            $this->student_id,
        );
    }

    public function report()
    {

        $cacheKey = $this->generateCacheKey();
        // Cache the data separately
        $reportData = Cache::remember($cacheKey, self::CACHE_TTL, function () {
            $student = Student::query()->with(['school', 'level'])->when($this->school_id && !is_null($this->school_id), function ($query) {
                $query->where('school_id', $this->school_id);
            })->findOrFail($this->student_id);

            $terms = StudentTerm::query()->with(['term', 'term.level'])
                ->where('student_id', $student->id)
                ->where('corrected', 1)
                ->orderBy('term_id')
                ->get();
            $school = $student->school;

            if (in_array($student->school->curriculum_type, ['Indian', 'Pakistan', 'Bangladeshi'])) {
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
//        dd($studentTerms);


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


            $skills = [];
            if ($school->reading) {
                $skills[] = 1;
            }
            if ($school->listening) {
                $skills[] = 2;
            }
            if ($school->writing) {
                $skills[] = 3;
            }
            if ($school->speaking) {
                $skills[] = 4;
            }
            $id_terms = $terms->unique()->pluck('term_id')->all();
            //dd($term);
            $studentId = $student->id;
            $schoolId = $school->id;
            $studentInLevelSql = "
    (SELECT
        IFNULL(
            ROUND(
                COUNT(CASE WHEN sts.mark >=
                    CASE
                        WHEN question_standards.mark = 1 THEN 1
                        WHEN question_standards.mark = 2 THEN 2
                        WHEN question_standards.mark = 3 THEN 3
                        WHEN question_standards.mark = 4 THEN 4
                        WHEN question_standards.mark = 5 THEN 5
                        ELSE 1
                    END
                THEN 1 ELSE NULL END) * 100.0 / COUNT(*), 1
            ), 0
        )
    FROM student_term_standards sts
    JOIN student_terms s_term ON s_term.id = sts.student_term_id
    JOIN students st ON st.id = s_term.student_id
    WHERE st.school_id = $schoolId AND sts.question_standard_id = question_standards.id
    GROUP BY sts.question_standard_id
    LIMIT 1
    ) AS student_in_level";

            $studentInSystemSql = "
    (SELECT
        IFNULL(
            ROUND(
                COUNT(CASE WHEN sts.mark >=
                    CASE
                        WHEN question_standards.mark = 1 THEN 1
                        WHEN question_standards.mark = 2 THEN 2
                        WHEN question_standards.mark = 3 THEN 3
                        WHEN question_standards.mark = 4 THEN 4
                        WHEN question_standards.mark = 5 THEN 5
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
                ->select('question_standards.*', DB::raw($studentInLevelSql), DB::raw($studentInSystemSql))
                ->with(['studentTermStandards' => function ($query) use ($terms) {
                    $query->whereIn('student_term_id', $terms->pluck('id'));
                }])
                ->with('question')
                ->whereHas('question', function (Builder $query) use ($id_terms) {
                    $query->whereIn('term_id', $id_terms);
                })
                ->get();
            $names = array();
            $marks = array();
            $full_marks = array();
            $data_term = array();
            $full_data_term = array();
            $subjects = Subject::query()->get();
            foreach ($terms as $term) {
                foreach ($subjects as $key => $subject) {
                    ${"countQ" . ($key + 1)} = (object)collect($term->subjects_marks)->firstWhere('subject_id', $subject->id);
                }
                $arab_status = $student->level->arab;
                $arab_status = $arab_status == 1 ? re('for Arabs') : re('for Non Arabs');
                $term_name = re($term->term->round) . ' ' . re('Grade') . ' ' . $term->term->level->grade . ' ' . $arab_status;

                $marks[] = array($countQ1, $countQ2, $countQ3, $term_name);
                //array_push($full_marks,array($countQ1+$countQ2+$countQ3+$countQ4,$term->Term->en_name));
                $data_term[] = (object)['skill1' => $countQ1, 'skill2' => $countQ2, 'skill3' => $countQ3, 'term' => $term_name];
                $progress_name = $terms_progress[$term->term->round];

                $full_data_term[] = (object)[
                    'total' => $term->total,
                    'mark_step1' => $countQ1,
                    'mark_step2' => $countQ2,
                    'mark_step3' => $countQ3,
                    'round_name' => $term->term->round,
                    'expectation' => $term->expectations,
                    'term' => $term_name,
                    'progress' => $progress_name,
                    'progress_class' => explode(' ', strtolower($progress_name))[0],
                ];
                $names[] = $term->term->name;
                $full_marks[] = $term->total;
            }

            return [
                'standards' => $standards,
                'names' => $names,
                'full_marks' => $full_marks,
                'full_data_term' => $full_data_term,
                'data_term' => $data_term,
                'student' => $student,
                'marks' => $marks,
                'terms' => $terms,
                'school' => $school,
            ];
        });

        return view('general.reports.student_report', $reportData)->render();
    }

    public function studentReportCard()
    {
        $student = Student::query()->with(['level'])->findOrFail($this->student_id);
        $subjects = Subject::query()->get();
        if ($student->school->type == "Indian") {
            $months = [0 => 'May', 1 => 'September', 2 => 'February'];
        } else {
            $months = [0 => 'September', 1 => 'February', 2 => 'May'];
        }

        $student_terms = StudentTerm::query()->with(['term', 'term.level'])->where('student_id', $this->student_id)->where('corrected', 1)->get();

        $studentTermFirst = $student_terms->filter(function ($value, $key) use ($months){
            return $value->term->month == $months[0];
        })->first();
        $studentTermSecond = $student_terms->filter(function ($value, $key) use ($months){
            return $value->term->month == $months[1];
        })->first();
        $studentTermThird = $student_terms->filter(function ($value, $key) use ($months){
            return $value->term->month == $months[2];
        })->first();

        if ($studentTermFirst && $studentTermSecond && $studentTermThird)
        {
            $studentTermFirst->progress = 'Starting point for the current year';
            $studentTermFirst->progress_class = 'starting';
            if ($studentTermFirst && $studentTermSecond) {
                $total_mark_2 = $studentTermSecond->total;
                $total_mark_1 = $studentTermFirst->total;
                $total_mar_result = $total_mark_2 - $total_mark_1;
                $studentTermSecond->progress = getProgressText($total_mark_2, $total_mar_result);
                $studentTermSecond->progress_class = strtolower(explode(' ', $studentTermSecond->progress)[0]);
            }
            if ($studentTermSecond && $studentTermThird) {
                $total_mark_3 = $studentTermThird->total;
                $total_mark_2 = $studentTermSecond->total;
                $total_mar_result = $total_mark_3 - $total_mark_2;
                $studentTermThird->progress = getProgressText($total_mark_2, $total_mar_result);
                $studentTermThird->progress_class = strtolower(explode(' ', $studentTermThird->progress)[0]);
            }
        }else{
            if ($studentTermSecond && $studentTermThird) {
                $studentTermSecond->progress = 'Starting point for the current year';
                $studentTermSecond->progress_class = 'starting';
                $total_mark_2 = $studentTermThird->total;
                $total_mark_1 = $studentTermSecond->total;
                $total_mar_result = $total_mark_2 - $total_mark_1;
                $studentTermThird->progress = getProgressText($total_mark_2, $total_mar_result);
                $studentTermThird->progress_class = strtolower(explode(' ', $studentTermThird->progress)[0]);
            } elseif ($studentTermFirst && $studentTermSecond) {
                $studentTermFirst->progress = 'Starting point for the current year';
                $studentTermFirst->progress_class = 'starting';
                $total_mark_2 = $studentTermSecond->total;
                $total_mark_1 = $studentTermFirst->total;
                $total_mar_result = $total_mark_2 - $total_mark_1;
                $studentTermSecond->progress = getProgressText($total_mark_2, $total_mar_result);
                $studentTermSecond->progress_class = strtolower(explode(' ', $studentTermSecond->progress)[0]);
            } elseif ($studentTermFirst && $studentTermThird) {
                $studentTermFirst->progress = 'Starting point for the current year';
                $studentTermFirst->progress_class = 'starting';
                $total_mark_2 = $studentTermThird->total;
                $total_mark_1 = $studentTermFirst->total;
                $total_mar_result = $total_mark_2 - $total_mark_1;
                $studentTermThird->progress = getProgressText($total_mark_2, $total_mar_result);
                $studentTermThird->progress_class = strtolower(explode(' ', $studentTermThird->progress)[0]);
            }else{
                if ($studentTermFirst)
                {
                    $studentTermFirst->progress = 'Starting point for the current year';
                    $studentTermFirst->progress_class = 'starting';
                }
                if ($studentTermSecond)
                {
                    $studentTermSecond->progress = 'Starting point for the current year';
                    $studentTermSecond->progress_class = 'starting';
                }
                if ($studentTermThird) {
                    $studentTermThird->progress = 'Starting point for the current year';
                    $studentTermThird->progress_class = 'starting';
                }
            }
        }

        return view('general.reports.student_report_card.report_arabs', compact('student','subjects', 'student_terms'));

//        if ($student->level->year_id >= 4 && $student->level->arab == 0) {
//            $new_non_arabs = true;
//            return view('general.reports.student_report_card.report_non_arabs', compact('student', 'subjects', 'student_terms', 'new_non_arabs'));
//        } else {
//            return view('general.reports.student_report_card.report_arabs', compact('student','subjects', 'student_terms'));
//        }
    }

}
