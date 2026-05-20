<?php

namespace App\Reports\NewReports;

use App\Models\Level;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentTerm;
use App\Models\Subject;
use App\Models\Term;
use App\Models\Year;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class GroupComparisonReport
{
    public $request;
    public $inspection;

    public function __construct(Request $request, $inspection)
    {
        $this->request    = $request;
        $this->inspection = $inspection;
    }

    public function report()
    {
        $request    = $this->request;
        $inspection = $this->inspection;

        $schools_id = $request->get('school_id', []);
        $schools    = School::query()
            ->whereIn('id', $schools_id)
            ->get(['id', 'name', 'country', 'curriculum_type']);

        $student_type = $request->get('student_type', 0);
        if ($student_type == 2) {
            $arab_filter = null;
            $arab        = 0;
        } else {
            $arab_filter = (int) $student_type;
            $arab        = (int) $student_type;
        }

        $year_id   = $request->get('year_id');
        $grades    = $request->get('grades', []);
        $year_obj  = Year::find($year_id);
        $year_name = $year_obj ? $year_obj->name : $year_id;

        if (is_null($year_id)) {
            return redirect()->back()
                ->with('message', 'لا توجد سنة محددة')
                ->with('m-class', 'alert-danger');
        }

        $levelsQuery = Level::query()
            ->where('year_id', $year_id)
            ->whereIn('grade', $grades);
        if (!is_null($arab_filter)) {
            $levelsQuery->where('arab', $arab_filter);
        }
        $levels     = $levelsQuery->get(['id', 'grade', 'arab', 'year_id']);
        $levels_ids = $levels->pluck('id')->all();

        $round = $request->get('round', 0);

        $terms = Term::query()
            ->with(['Level:id,grade'])
            ->whereIn('level_id', $levels_ids)
            ->where('round', $round)
            ->get(['id', 'level_id', 'round']);

        // ---- Mark thresholds from allMarksRanges() ----
        $ranges   = allMarksRanges(null);
        $r1       = $ranges->{1};
        $r10      = $ranges->{10};
        $below_1  = $r1->below->to + 1;   // 60
        $above_1  = $r1->above->from;     // 70
        $below_10 = $r10->below->to + 1;  // 50
        $above_10 = $r10->above->from;    // 70

        // ---- Pre-compute term grade buckets ----
        $terms_1 = $terms->filter(function ($t) {
            return $t->Level && $t->Level->grade < 10;
        });
        $terms_10 = $terms->filter(function ($t) {
            return $t->Level && $t->Level->grade >= 10;
        });
        $terms_1_ids     = $terms_1->pluck('id')->all();
        $terms_10_ids    = $terms_10->pluck('id')->all();
        $all_term_ids    = array_values(array_unique(array_merge($terms_1_ids, $terms_10_ids)));
        $terms_1_lookup  = array_flip($terms_1_ids);
        $terms_10_lookup = array_flip($terms_10_ids);

        // ---- All active schools for ranking ----
        $all_schools           = School::query()->where('active', 1)->get(['id', 'name', 'country', 'curriculum_type']);
        $approved_school_ids   = $all_schools->pluck('id')->all();
        $selected_school_ids   = $schools->pluck('id')->all();
        $processing_school_ids = array_values(array_unique(array_merge($approved_school_ids, $selected_school_ids)));

        $sys_nat_key = 'total_' . sysNationality();

        // ---- Bulk student fetch ----
        $students_by_school = collect();
        if (!empty($processing_school_ids) && !empty($levels_ids)) {
            $students_by_school = Student::query()
                ->whereIn('school_id', $processing_school_ids)
                ->whereIn('level_id', $levels_ids)
                ->get(['id', 'school_id', 'gender', 'citizen', 'sen'])
                ->groupBy('school_id');
        }

        // ---- Bulk StudentTerm fetch ----
        $st_rows_by_school = collect();
        if (!empty($processing_school_ids) && !empty($all_term_ids)) {
            $st_rows_by_school = StudentTerm::query()
                ->join('students', 'students.id', '=', 'student_terms.student_id')
                ->whereNull('students.deleted_at')
                ->whereIn('students.school_id', $processing_school_ids)
                ->whereIn('student_terms.term_id', $all_term_ids)
                ->where('student_terms.corrected', 1)
                ->select(
                    'students.school_id as school_id',
                    'students.gender as gender',
                    'students.citizen as citizen',
                    'students.sen as sen',
                    'student_terms.term_id as term_id',
                    'student_terms.total as total',
                    'student_terms.subjects_marks as subjects_marks'
                )
                ->get()
                ->groupBy('school_id');
        }

        // Helper: split rows by grade bucket
        $splitRows = function (Collection $rows) use ($terms_1_lookup, $terms_10_lookup) {
            $r1  = collect();
            $r10 = collect();
            foreach ($rows as $row) {
                if (isset($terms_1_lookup[$row->term_id])) {
                    $r1->push($row);
                } elseif (isset($terms_10_lookup[$row->term_id])) {
                    $r10->push($row);
                }
            }
            return [$r1, $r10];
        };

        // ---- Aggregate header totals from selected schools ----
        $selected_students = collect();
        foreach ($selected_school_ids as $sid) {
            if (isset($students_by_school[$sid])) {
                $selected_students = $selected_students->concat($students_by_school[$sid]);
            }
        }

        $data['total_schools']  = count($schools);
        $data['total_students'] = $selected_students->count();
        $data['total_boys']     = $selected_students->where('gender', 'boy')->count();
        $data['total_girls']    = $selected_students->where('gender', 'girl')->count();
        $data[$sys_nat_key]     = $selected_students->where('citizen', 1)->count();
        $data['total_sen']      = $selected_students->where('sen', 1)->count();

        if ($round == 1) {
            $data['round_name'] = 'September - ' . $year_name;
        } elseif ($round == 2) {
            $data['round_name'] = 'December - ' . $year_name;
        } elseif ($round == 3) {
            $data['round_name'] = 'February - ' . $year_name;
        } elseif ($round == 4) {
            $data['round_name'] = 'May - ' . $year_name;
        } else {
            $data['round_name'] = 'Not Selected';
        }

        // ---- general_all_schools (for ranking) ----
        $general_schools     = collect([]);
        $general_all_schools = collect([]);

        foreach ($all_schools as $gen_school) {
            $rows = $st_rows_by_school[$gen_school->id] ?? collect();
            if ($rows->isEmpty()) {
                continue;
            }

            [$stu_1, $stu_10] = $splitRows($rows);

            $count = $stu_1->count() + $stu_10->count();
            if (!$count) {
                continue;
            }

            $above  = $stu_1->where('total', '>=', $above_1)->count()
                + $stu_10->where('total', '>=', $above_10)->count();
            $inline = $stu_1->where('total', '>=', $below_1)->where('total', '<', $above_1)->count()
                + $stu_10->where('total', '>=', $below_10)->where('total', '<', $above_10)->count();
            $below  = $stu_1->where('total', '<', $below_1)->count()
                + $stu_10->where('total', '<', $below_10)->count();

            $per_ab = $above > 0 && $count > 0 ? round(($above / $count), 3) * 100 : 0;
            $per_in = $inline > 0 && $count > 0 ? round(($inline / $count), 3) * 100 : 0;
            $per_be = $below > 0 && $count > 0 ? round(($below / $count), 3) * 100 : 0;

            $general_all_schools->push((object) [
                'school_id'      => $gen_school->id,
                'school_name'    => $gen_school->name,
                'student_count'  => $count,
                'school_country' => $gen_school->country,
                'school_type'    => $gen_school->curriculum_type,
                'above'          => $above,
                'inline'         => $inline,
                'below'          => $below,
                'percent_above'  => $per_ab,
                'percent_inline' => $per_in,
                'percent_below'  => $per_be,
                'percent_total'  => $per_ab,
            ]);
        }

        $general_all_schools = $general_all_schools->sortBy([
            ['percent_above', 'desc'],
            ['percent_inline', 'desc'],
        ])->values();

        // ---- Load subjects once for per-subject stats ----
        $subjects = Subject::query()->get();

        // Helper: decode subjects_marks JSON string from JOIN row
        $decodeSubjectsMarks = function($row) {
            if (is_null($row->subjects_marks)) {
                return [];
            }
            return is_string($row->subjects_marks) ? json_decode($row->subjects_marks, true) : $row->subjects_marks;
        };

        // Helper: count rows where subject mark falls in a given range
        $countSubjectInRange = function($rows, $subject_id, $range) use ($decodeSubjectsMarks) {
            return $rows->filter(function($row) use ($subject_id, $range, $decodeSubjectsMarks) {
                foreach ($decodeSubjectsMarks($row) as $s) {
                    if ($s['subject_id'] == $subject_id
                        && $s['mark'] >= $range['from']
                        && $s['mark'] <= $range['to']) {
                        return true;
                    }
                }
                return false;
            })->count();
        };

        // ---- Selected schools detail ----
        $schools_information = [];
        foreach ($schools as $school) {
            $students = $students_by_school[$school->id] ?? collect();

            $school_information                 = [];
            $school_information['total_students'] = $students->count();
            $school_information['total_boys']     = $students->where('gender', 'boy')->count();
            $school_information['total_girls']    = $students->where('gender', 'girl')->count();
            $school_information[$sys_nat_key]     = $students->where('citizen', 1)->count();
            $school_information['total_sen']      = $students->where('sen', 1)->count();
            $school_information['curriculum']     = $school->curriculum_type;
            $school_information['school_name']    = $school->name;

            $rows = $st_rows_by_school[$school->id] ?? collect();
            [$stu_1, $stu_10] = $splitRows($rows);

            $all_terms = $school_information['total_terms'] = $stu_1->count() + $stu_10->count();

            $school_above  = $stu_1->where('total', '>=', $above_1)->count()
                + $stu_10->where('total', '>=', $above_10)->count();
            $school_inline = $stu_1->where('total', '>=', $below_1)->where('total', '<', $above_1)->count()
                + $stu_10->where('total', '>=', $below_10)->where('total', '<', $above_10)->count();
            $school_below  = $stu_1->where('total', '<', $below_1)->count()
                + $stu_10->where('total', '<', $below_10)->count();

            $school_information['above']          = $school_above;
            $school_information['inline']         = $school_inline;
            $school_information['below']          = $school_below;
            $school_information['percent_above']  = $school_above > 0 && $all_terms > 0 ? round(($school_above / $all_terms), 3) * 100 : 0;
            $school_information['percent_inline'] = $school_inline > 0 && $all_terms > 0 ? round(($school_inline / $all_terms), 3) * 100 : 0;
            $school_information['percent_below']  = $school_below > 0 && $all_terms > 0 ? round(($school_below / $all_terms), 3) * 100 : 0;

            // ---- Male ----
            $male_1  = $stu_1->where('gender', 'boy');
            $male_10 = $stu_10->where('gender', 'boy');
            $male_total = $male_1->count() + $male_10->count();
            $male_above  = $male_1->where('total', '>=', $above_1)->count() + $male_10->where('total', '>=', $above_10)->count();
            $male_inline = $male_1->where('total', '>=', $below_1)->where('total', '<', $above_1)->count() + $male_10->where('total', '>=', $below_10)->where('total', '<', $above_10)->count();
            $male_below  = $male_1->where('total', '<', $below_1)->count() + $male_10->where('total', '<', $below_10)->count();
            $school_information['male'] = (object) [
                'total'      => $male_total,
                'above'      => $male_above,
                'inline'     => $male_inline,
                'below'      => $male_below,
                'per_above'  => $male_above > 0 && $male_total > 0 ? round(($male_above / $male_total) * 100, 2) : 0,
                'per_inline' => $male_inline > 0 && $male_total > 0 ? round(($male_inline / $male_total) * 100, 2) : 0,
                'per_below'  => $male_below > 0 && $male_total > 0 ? round(($male_below / $male_total) * 100, 2) : 0,
            ];

            // ---- Female ----
            $female_1  = $stu_1->where('gender', 'girl');
            $female_10 = $stu_10->where('gender', 'girl');
            $female_total = $female_1->count() + $female_10->count();
            $female_above  = $female_1->where('total', '>=', $above_1)->count() + $female_10->where('total', '>=', $above_10)->count();
            $female_inline = $female_1->where('total', '>=', $below_1)->where('total', '<', $above_1)->count() + $female_10->where('total', '>=', $below_10)->where('total', '<', $above_10)->count();
            $female_below  = $female_1->where('total', '<', $below_1)->count() + $female_10->where('total', '<', $below_10)->count();
            $school_information['female'] = (object) [
                'total'      => $female_total,
                'above'      => $female_above,
                'inline'     => $female_inline,
                'below'      => $female_below,
                'per_above'  => $female_above > 0 && $female_total > 0 ? round(($female_above / $female_total) * 100, 2) : 0,
                'per_inline' => $female_inline > 0 && $female_total > 0 ? round(($female_inline / $female_total) * 100, 2) : 0,
                'per_below'  => $female_below > 0 && $female_total > 0 ? round(($female_below / $female_total) * 100, 2) : 0,
            ];

            // ---- SEN ----
            $sen_1  = $stu_1->where('sen', 1);
            $sen_10 = $stu_10->where('sen', 1);
            $sen_total = $sen_1->count() + $sen_10->count();
            $sen_above  = $sen_1->where('total', '>=', $above_1)->count() + $sen_10->where('total', '>=', $above_10)->count();
            $sen_inline = $sen_1->where('total', '>=', $below_1)->where('total', '<', $above_1)->count() + $sen_10->where('total', '>=', $below_10)->where('total', '<', $above_10)->count();
            $sen_below  = $sen_1->where('total', '<', $below_1)->count() + $sen_10->where('total', '<', $below_10)->count();
            $school_information['sen'] = (object) [
                'total'      => $sen_total,
                'above'      => $sen_above,
                'inline'     => $sen_inline,
                'below'      => $sen_below,
                'per_above'  => $sen_above > 0 && $sen_total > 0 ? round(($sen_above / $sen_total) * 100, 2) : 0,
                'per_inline' => $sen_inline > 0 && $sen_total > 0 ? round(($sen_inline / $sen_total) * 100, 2) : 0,
                'per_below'  => $sen_below > 0 && $sen_total > 0 ? round(($sen_below / $sen_total) * 100, 2) : 0,
            ];

            // ---- Per-subject (Reading, Listening, Writing, Speaking) ----
            foreach ($subjects as $subject) {
                $subject_key = strtolower($subject->name);
                $marks_range = $subject->marks_range;

                // Grade 1-9 uses key '9', Grade 10+ uses key '12'
                $range_1  = isset($marks_range[9])  ? $marks_range[9]  : (isset($marks_range['9'])  ? $marks_range['9']  : null);
                $range_10 = isset($marks_range[12]) ? $marks_range[12] : (isset($marks_range['12']) ? $marks_range['12'] : null);

                if (!$range_1 || !$range_10) {
                    $school_information[$subject_key] = (object)[
                        'total' => 0, 'below' => 0, 'inline' => 0, 'above' => 0,
                        'per_below' => 0, 'per_inline' => 0, 'per_above' => 0,
                    ];
                    continue;
                }

                $sub_below  = $countSubjectInRange($stu_1,  $subject->id, $range_1['below'])
                            + $countSubjectInRange($stu_10, $subject->id, $range_10['below']);
                $sub_inline = $countSubjectInRange($stu_1,  $subject->id, $range_1['inline'])
                            + $countSubjectInRange($stu_10, $subject->id, $range_10['inline']);
                $sub_above  = $countSubjectInRange($stu_1,  $subject->id, $range_1['above'])
                            + $countSubjectInRange($stu_10, $subject->id, $range_10['above']);

                $school_information[$subject_key] = (object)[
                    'total'      => $all_terms,
                    'below'      => $sub_below,
                    'inline'     => $sub_inline,
                    'above'      => $sub_above,
                    'per_below'  => $sub_below > 0 && $all_terms > 0 ? round(($sub_below / $all_terms) * 100, 2) : 0,
                    'per_inline' => $sub_inline > 0 && $all_terms > 0 ? round(($sub_inline / $all_terms) * 100, 2) : 0,
                    'per_above'  => $sub_above > 0 && $all_terms > 0 ? round(($sub_above / $all_terms) * 100, 2) : 0,
                ];
            }

            // ---- UAE Male ----
            $uae_male_1  = $stu_1->filter(function($v) { return $v->citizen == 1 && $v->gender == 'boy'; });
            $uae_male_10 = $stu_10->filter(function($v) { return $v->citizen == 1 && $v->gender == 'boy'; });
            $uae_male_total = $uae_male_1->count() + $uae_male_10->count();
            $uae_male_above  = $uae_male_1->where('total', '>=', $above_1)->count() + $uae_male_10->where('total', '>=', $above_10)->count();
            $uae_male_inline = $uae_male_1->where('total', '>=', $below_1)->where('total', '<', $above_1)->count() + $uae_male_10->where('total', '>=', $below_10)->where('total', '<', $above_10)->count();
            $uae_male_below  = $uae_male_1->where('total', '<', $below_1)->count() + $uae_male_10->where('total', '<', $below_10)->count();
            $school_information['male_uae'] = (object) [
                'total'      => $uae_male_total,
                'above'      => $uae_male_above,
                'inline'     => $uae_male_inline,
                'below'      => $uae_male_below,
                'per_above'  => $uae_male_above > 0 && $uae_male_total > 0 ? round(($uae_male_above / $uae_male_total) * 100, 2) : 0,
                'per_inline' => $uae_male_inline > 0 && $uae_male_total > 0 ? round(($uae_male_inline / $uae_male_total) * 100, 2) : 0,
                'per_below'  => $uae_male_below > 0 && $uae_male_total > 0 ? round(($uae_male_below / $uae_male_total) * 100, 2) : 0,
            ];

            // ---- UAE Female ----
            $uae_female_1  = $stu_1->filter(function($v) { return $v->citizen == 1 && $v->gender == 'girl'; });
            $uae_female_10 = $stu_10->filter(function($v) { return $v->citizen == 1 && $v->gender == 'girl'; });
            $uae_female_total = $uae_female_1->count() + $uae_female_10->count();
            $uae_female_above  = $uae_female_1->where('total', '>=', $above_1)->count() + $uae_female_10->where('total', '>=', $above_10)->count();
            $uae_female_inline = $uae_female_1->where('total', '>=', $below_1)->where('total', '<', $above_1)->count() + $uae_female_10->where('total', '>=', $below_10)->where('total', '<', $above_10)->count();
            $uae_female_below  = $uae_female_1->where('total', '<', $below_1)->count() + $uae_female_10->where('total', '<', $below_10)->count();
            $school_information['female_uae'] = (object) [
                'total'      => $uae_female_total,
                'above'      => $uae_female_above,
                'inline'     => $uae_female_inline,
                'below'      => $uae_female_below,
                'per_above'  => $uae_female_above > 0 && $uae_female_total > 0 ? round(($uae_female_above / $uae_female_total) * 100, 2) : 0,
                'per_inline' => $uae_female_inline > 0 && $uae_female_total > 0 ? round(($uae_female_inline / $uae_female_total) * 100, 2) : 0,
                'per_below'  => $uae_female_below > 0 && $uae_female_total > 0 ? round(($uae_female_below / $uae_female_total) * 100, 2) : 0,
            ];

            $schools_information[$school->id] = $school_information;

            if ($school_information['total_terms']) {
                $general_schools->push((object) [
                    'school_id'      => $school->id,
                    'school_name'    => $school->name,
                    'student_count'  => $school_information['total_terms'],
                    'school_country' => $school->country,
                    'school_type'    => $school->curriculum_type,
                    'below'          => $school_information['below'],
                    'inline'         => $school_information['inline'],
                    'above'          => $school_information['above'],
                    'percent_below'  => $school_information['percent_below'],
                    'percent_inline' => $school_information['percent_inline'],
                    'percent_above'  => $school_information['percent_above'],
                ]);
            }
        }

        $general_schools = $general_schools->sortBy([
            ['percent_above', 'desc'],
            ['percent_inline', 'desc'],
        ])->values();

        // ---- Ranks ----
        $array_general_schools = $general_all_schools->pluck('school_id')->toArray();
        foreach ($schools as $school) {
            $array_local_schools = $general_all_schools->where('school_country', $school->country)->pluck('school_id')->toArray();
            $array_type_schools  = $general_all_schools->where('school_type', $school->curriculum_type)->pluck('school_id')->toArray();

            $key = $school->id;
            if (!isset($schools_information[$key])) {
                continue;
            }

            $schools_information[$key]['global_rank'] = in_array($school->id, $array_general_schools)
                ? array_search($school->id, $array_general_schools) + 1
                : 0;
            $schools_information[$key]['local_rank'] = in_array($school->id, $array_local_schools)
                ? array_search($school->id, $array_local_schools) + 1
                : 0;
            $schools_information[$key]['curriculum_rank'] = in_array($school->id, $array_type_schools)
                ? array_search($school->id, $array_type_schools) + 1
                : 0;
        }

        $sub_title   = $request->get('sub_title', null);
        $report_type = 1;

        return view('inspection.reports.group_comparison_report', compact(
            'data', 'schools_information', 'grades', 'arab', 'year_id', 'year_name',
            'inspection', 'general_schools', 'sub_title', 'report_type', 'schools'
        ));
    }
}
