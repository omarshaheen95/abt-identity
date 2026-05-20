<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Reports\NewReports;

use App\Models\Level;
use App\Models\Student;
use App\Models\StudentTerm;
use App\Models\Term;
use App\Models\Year;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ComparisonReport
{
    protected $request;
    protected $school;

    public function __construct($request, $school)
    {
        $this->request = $request;
        $this->school = $school;
    }

    public function report()
    {
        ini_set('max_execution_time', 300);

        // Check cache for processed data (1 hour)
        $cacheKey = $this->getCacheKey();
//        if (Cache::has($cacheKey)) {
//            $cachedData = Cache::get($cacheKey);
//            // Return view with cached data
//            return view('general.new_reports.comparison.comparison_report', $cachedData);
//        }

        //--------------School--------------//
        $school = $this->school;
//        dd($school);
        $request = $this->request;
        //-------------Parameters------------------//
        $curriculums = $request->get('curriculums'); // All = 13
        $countries = $request->get('countries', []); // All = 8
        $students_gender = $request->get('gender');
        $selected_round = $request->get('round');
        $students_SEN = $request->get('sen_students', false);
        $years = $request->get('year_id');
        $grades = $request->get('grades', []);
        $section = $this->request->get('student_type');

        $title = 'Comparison Report';

        //--------------------------------------//
        //Table Data
        $data = [];
        switch ($students_gender) {
            case "boy":
                $data['gender'] = "Boys";
                break;
            case "girl":
                $data['gender'] = "Girls";
                break;
            default:
                $data['gender'] = "All";
        }
        switch ($students_SEN) {
            case 1:
                $data['sen_students'] = "Yes";
                break;
            case 2:
                $data['sen_students'] = "No";
                break;
            default:
                $data['sen_students'] = "All";
        }
//        switch (count($countries)) {
//            case 8:
//                $data['countries'] = "All";
//                break;
//            default:
//                $data['countries'] = $countries;
//        }
        switch (count($curriculums)) {
            case 14:
                $data['curriculums'] = "All";
                break;
            default:
                $data['curriculums'] = $curriculums;
        }
        $data['years'] = Year::query()->find($years)->name ?? '';
        $section == 1 ? $data['section'] = 'Arabs' : ($section == 2 ? $data['section'] = 'Non-Arabs' : $data['section'] = 'All');


        $school_student = Student::query()->whereHas('level', function (Builder $query) use ($years) {
            $query->where('year_id', $years);
        })->where('school_id', $school->id)->get();

        $total_school_students = $school_student->count();
        $boys_school_students = $school_student->where('gender', 'boy')->count();
        $girls_school_students = $school_student->where('gender', 'girl')->count();
        $max_arab_level = 0;
        $min_arab_level = 0;
        $max_non_arab_level = 0;
        $min_non_arab_level = 0;
        if ($section == 1 || $section == 0) {
            $max_arab_level = max($grades);
            $min_arab_level = min($grades);
        } else if ($section == 2 || $section == 0) {
            $max_non_arab_level = max($grades);
            $min_non_arab_level = min($grades);
        }

        if ($max_arab_level && $min_arab_level) {
            $data['arab_levels'] = 'Arabs From Grade ' . $min_arab_level . ' To ' . $max_arab_level . '';
        } else {
            $data['arab_levels'] = false;
        }
        if ($max_non_arab_level && $min_non_arab_level) {
            $data['non_arab_levels'] = 'Non-Arabs From Grade ' . $min_non_arab_level . ' To ' . $max_non_arab_level . '';
        } else {
            $data['non_arab_levels'] = false;
        }
        //--------------------------------------------------//

        if ($section == 1 || $section == 0) {
            $arabs_grade_ranking = $this->getGradesRank($school, $years, $selected_round, 1, $grades);
        }else{
            $arabs_grade_ranking = [];
        }
        if ($section == 2 || $section == 0) {
            $non_arabs_grade_ranking = $this->getGradesRank($school, $years, $selected_round, 0, $grades);
        }else{
            $non_arabs_grade_ranking = [];
        }

//        dd($arabs_grade_ranking);

        $levels = Level::query()->whereIn('grade', $grades)
            ->where('year_id', $years)
            ->when($section == 1, function ($q) {
                $q->where('arab', 1);
            })
            ->when($section == 2, function ($q) {
                $q->where('arab', 0);
            })
            ->get();
        $terms = Term::query()
            ->whereIn('level_id', $levels->pluck('id'))
            ->where('round', $selected_round)
            ->with(['level'])
            ->get();

        $term_1 = $terms->where('level.grade', '<=', 9);
        $term_10 = $terms->where('level.grade', '>', 9);
        $data_mark_1 = getMarksRanges(1, true);
        $data_mark_10 = getMarksRanges(10, true);
        //--------------curriculums statistic (OPTIMIZED)-----------------//
        $curriculums_data = $this->getCurriculumsStatistics(
            $curriculums,
            $countries,
            $students_gender,
            $students_SEN,
            $levels,
            $selected_round,
            $term_1,
            $term_10,
            $data_mark_1,
            $data_mark_10
        );

        //--------------countries statistic (OPTIMIZED)-----------------//
        $countries_data = [];
//            $this->getCountriesStatistics(
//            $countries,
//            $curriculums,
//            $students_gender,
//            $students_SEN,
//            $levels,
//            $selected_round,
//            $term_1,
//            $data_mark_1,
//        );

        //--------------------School statistic-----------------------//
        $students_terms = StudentTerm::query()
            ->whereHas('student', function (Builder $query) use ($school, $students_gender, $students_SEN) {
                $query->where('school_id', $school->id);
                $query->when($students_gender, function (Builder $query) use ($students_gender) {
                    $query->where('gender', $students_gender);
                });
                $query->when($students_SEN, function (Builder $query) use ($students_SEN) {
                    $query->where('sen', $students_SEN);
                });
            })
            ->whereHas('term', function (Builder $query) use ($levels, $selected_round) {
                $query->where('round', $selected_round);
                $query->whereIn('level_id', $levels->pluck('id'));
            })
            ->where('corrected', 1)
            ->get();

        $school_student_count = $students_terms->count();
        $students_terms_1 = $students_terms->whereIn('term_id', $term_1->pluck('id'));
        $students_terms_10 = $students_terms->whereIn('term_id', $term_10->pluck('id'));
        $school_above =
            $students_terms_1->where('total', '>=', $data_mark_1['above'])->count() +
            $students_terms_10->where('total', '>=', $data_mark_10['above'])->count();
        $school_inline =
            $students_terms_1->where('total', '>=', $data_mark_1['from_inline'])->where('total', '<', $data_mark_1['to_inline'])->count() +
            $students_terms_10->where('total', '>=', $data_mark_10['from_inline'])->where('total', '<', $data_mark_10['to_inline'])->count();
        $school_below =
            $students_terms_1->where('total', '<', $data_mark_1['below'])->count() +
            $students_terms_10->where('total', '<', $data_mark_10['below'])->count();
        $school_data = (object)[
            'student_count' => $school_student_count,
            'above' => $school_above,
            'inline' => $school_inline,
            'below' => $school_below,
            'percent_above' => $school_above > 0 && $school_student_count > 0 ? round(($school_above / $school_student_count), 3) * 100 : 0,
            'percent_inline' => $school_inline > 0 && $school_student_count > 0 ? round(($school_inline / $school_student_count), 3) * 100 : 0,
            'percent_below' => $school_below > 0 && $school_student_count > 0 ? round(($school_below / $school_student_count), 3) * 100 : 0,
        ];
        //------------School Gender----------------------//
        $students_terms = StudentTerm::query()
            ->whereHas('student', function (Builder $query) use ($school, $students_gender, $students_SEN) {
                $query->where('school_id', $school->id);
                $query->where('gender', 'boy');
                $query->when($students_SEN, function (Builder $query) use ($students_SEN) {
                    $query->where('sen', $students_SEN);
                });
            })
            ->whereHas('term', function (Builder $query) use ($levels, $selected_round) {
                $query->where('round', $selected_round);
                $query->whereIn('level_id', $levels->pluck('id'));
            })
            ->where('corrected', 1)
            ->get();
        $school_student_count = $students_terms->count();

        $students_terms_1 = $students_terms->whereIn('term_id', $term_1->pluck('id'));
        $students_terms_10 = $students_terms->whereIn('term_id', $term_10->pluck('id'));
        $school_above =
            $students_terms_1->where('total', '>=', $data_mark_1['above'])->count() +
            $students_terms_10->where('total', '>=', $data_mark_10['above'])->count();
        $school_inline =
            $students_terms_1->where('total', '>=', $data_mark_1['from_inline'])->where('total', '<', $data_mark_1['to_inline'])->count() +
            $students_terms_10->where('total', '>=', $data_mark_10['from_inline'])->where('total', '<', $data_mark_10['to_inline'])->count();
        $school_below =
            $students_terms_1->where('total', '<', $data_mark_1['below'])->count() +
            $students_terms_10->where('total', '<', $data_mark_10['below'])->count();
        $school_boys = (object)[
            'student_count' => $school_student_count,
            'above' => $school_above,
            'inline' => $school_inline,
            'below' => $school_below,
            'percent_above' => $school_above > 0 && $school_student_count > 0 ? round(($school_above / $school_student_count), 3) * 100 : 0,
            'percent_inline' => $school_inline > 0 && $school_student_count > 0 ? round(($school_inline / $school_student_count), 3) * 100 : 0,
            'percent_below' => $school_below > 0 && $school_student_count > 0 ? round(($school_below / $school_student_count), 3) * 100 : 0,
        ];
        $students_terms = StudentTerm::query()
            ->whereHas('student', function (Builder $query) use ($school, $students_gender, $students_SEN) {
                $query->where('school_id', $school->id);
                $query->where('gender', 'girl');
                $query->when($students_SEN, function (Builder $query) use ($students_SEN) {
                    $query->where('sen', $students_SEN);
                });
            })
            ->whereHas('term', function (Builder $query) use ($levels, $selected_round) {
                $query->where('round', $selected_round);
                $query->whereIn('level_id', $levels->pluck('id'));
            })
            ->where('corrected', 1)
            ->get();
        $school_student_count = $students_terms->count();

        $students_terms_1 = $students_terms->whereIn('term_id', $term_1->pluck('id'));
        $students_terms_10 = $students_terms->whereIn('term_id', $term_10->pluck('id'));
        $school_above =
            $students_terms_1->where('total', '>=', $data_mark_1['above'])->count() +
            $students_terms_10->where('total', '>=', $data_mark_10['above'])->count();
        $school_inline =
            $students_terms_1->where('total', '>=', $data_mark_1['from_inline'])->where('total', '<', $data_mark_1['to_inline'])->count() +
            $students_terms_10->where('total', '>=', $data_mark_10['from_inline'])->where('total', '<', $data_mark_10['to_inline'])->count();
        $school_below =
            $students_terms_1->where('total', '<', $data_mark_1['below'])->count() +
            $students_terms_10->where('total', '<', $data_mark_10['below'])->count();
        $school_girls = (object)[
            'student_count' => $school_student_count,
            'above' => $school_above,
            'inline' => $school_inline,
            'below' => $school_below,
            'percent_above' => $school_above > 0 && $school_student_count > 0 ? round(($school_above / $school_student_count), 3) * 100 : 0,
            'percent_inline' => $school_inline > 0 && $school_student_count > 0 ? round(($school_inline / $school_student_count), 3) * 100 : 0,
            'percent_below' => $school_below > 0 && $school_student_count > 0 ? round(($school_below / $school_student_count), 3) * 100 : 0,
        ];
        //------------School SEN----------------------//
        $students_terms = StudentTerm::query()
            ->whereHas('student', function (Builder $query) use ($school, $students_gender, $students_SEN) {
                $query->where('school_id', $school->id);
                $query->when($students_gender, function (Builder $query) use ($students_gender) {
                    $query->where('gender', $students_gender);
                });
                $query->where('sen', 1);
            })
            ->whereHas('term', function (Builder $query) use ($levels, $selected_round) {
                $query->where('round', $selected_round);
                $query->whereIn('level_id', $levels->pluck('id'));
            })
            ->where('corrected', 1)
            ->get();
        $school_student_count = $students_terms->count();

        $students_terms_1 = $students_terms->whereIn('term_id', $term_1->pluck('id'));
        $students_terms_10 = $students_terms->whereIn('term_id', $term_10->pluck('id'));
        $school_above =
            $students_terms_1->where('total', '>=', $data_mark_1['above'])->count() +
            $students_terms_10->where('total', '>=', $data_mark_10['above'])->count();
        $school_inline =
            $students_terms_1->where('total', '>=', $data_mark_1['from_inline'])->where('total', '<', $data_mark_1['to_inline'])->count() +
            $students_terms_10->where('total', '>=', $data_mark_10['from_inline'])->where('total', '<', $data_mark_10['to_inline'])->count();
        $school_below =
            $students_terms_1->where('total', '<', $data_mark_1['below'])->count() +
            $students_terms_10->where('total', '<', $data_mark_10['below'])->count();
        $school_sen = (object)[
            'student_count' => $school_student_count,
            'above' => $school_above,
            'inline' => $school_inline,
            'below' => $school_below,
            'percent_above' => $school_above > 0 && $school_student_count > 0 ? round(($school_above / $school_student_count), 3) * 100 : 0,
            'percent_inline' => $school_inline > 0 && $school_student_count > 0 ? round(($school_inline / $school_student_count), 3) * 100 : 0,
            'percent_below' => $school_below > 0 && $school_student_count > 0 ? round(($school_below / $school_student_count), 3) * 100 : 0,
        ];

        $school_data->boys = $school_boys;
        $school_data->girls = $school_girls;
        $school_data->sen = $school_sen;
        //--------General SEN----------------//
        $students_terms = StudentTerm::query()
            ->whereHas('student', function (Builder $query) use ($curriculums, $countries, $students_gender) {
                $query->whereHas('school', function (Builder $query) use ($curriculums, $countries) {
                    $query->whereIn('curriculum_type', $curriculums);
//                    $query->whereIn('country', $countries);
                });
                $query->where('sen', 1);
                $query->when($students_gender, function (Builder $query) use ($students_gender) {
                    $query->where('gender', $students_gender);
                });
            })
            ->whereHas('term', function (Builder $query) use ($levels, $selected_round) {
                $query->where('round', $selected_round);
                $query->whereIn('level_id', $levels->pluck('id'));
            })
            ->where('corrected', 1)
            ->get();

        $general_sen_student_count = $students_terms->count();
        $students_terms_1 = $students_terms->whereIn('term_id', $term_1->pluck('id'));
        $students_terms_10 = $students_terms->whereIn('term_id', $term_10->pluck('id'));
        $general_sen_above =
            $students_terms_1->where('total', '>=', $data_mark_1['above'])->count() +
            $students_terms_10->where('total', '>=', $data_mark_10['above'])->count();
        $general_sen_inline =
            $students_terms_1->where('total', '>=', $data_mark_1['from_inline'])->where('total', '<', $data_mark_1['to_inline'])->count() +
            $students_terms_10->where('total', '>=', $data_mark_10['from_inline'])->where('total', '<', $data_mark_10['to_inline'])->count();
        $general_sen_below =
            $students_terms_1->where('total', '<', $data_mark_1['below'])->count() +
            $students_terms_10->where('total', '<', $data_mark_10['below'])->count();
        $general_sen = (object)[
            'student_count' => $general_sen_student_count,
            'above' => $general_sen_above,
            'inline' => $general_sen_inline,
            'below' => $general_sen_below,
            'percent_above' => $general_sen_above > 0 && $general_sen_student_count > 0 ? round(($general_sen_above / $general_sen_student_count), 3) * 100 : 0,
            'percent_inline' => $general_sen_inline > 0 && $general_sen_student_count > 0 ? round(($general_sen_inline / $general_sen_student_count), 3) * 100 : 0,
            'percent_below' => $general_sen_below > 0 && $general_sen_student_count > 0 ? round(($general_sen_below / $general_sen_student_count), 3) * 100 : 0,
        ];
        //--------General Boy----------------//
        $students_terms = StudentTerm::query()
            ->whereHas('student', function (Builder $query) use ($students_SEN, $curriculums, $countries, $students_gender) {
                $query->whereHas('school', function (Builder $query) use ($curriculums, $countries) {
                    $query->whereIn('curriculum_type', $curriculums);
//                    $query->whereIn('country', $countries);
                });
                $query->where('gender', 'boy');
                $query->when($students_SEN, function (Builder $query) use ($students_SEN) {
                    $query->where('sen', $students_SEN);
                });
            })
            ->whereHas('term', function (Builder $query) use ($levels, $selected_round) {
                $query->where('round', $selected_round);
                $query->whereIn('level_id', $levels->pluck('id'));
            })
            ->where('corrected', 1)
            ->get();

        $general_boy_student_count = $students_terms->count();
        $students_terms_1 = $students_terms->whereIn('term_id', $term_1->pluck('id'));
        $students_terms_10 = $students_terms->whereIn('term_id', $term_10->pluck('id'));
        $general_boy_above =
            $students_terms_1->where('total', '>=', $data_mark_1['above'])->count() +
            $students_terms_10->where('total', '>=', $data_mark_10['above'])->count();
        $general_boy_inline =
            $students_terms_1->where('total', '>=', $data_mark_1['from_inline'])->where('total', '<', $data_mark_1['to_inline'])->count() +
            $students_terms_10->where('total', '>=', $data_mark_10['from_inline'])->where('total', '<', $data_mark_10['to_inline'])->count();
        $general_boy_below =
            $students_terms_1->where('total', '<', $data_mark_1['below'])->count() +
            $students_terms_10->where('total', '<', $data_mark_10['below'])->count();
        $general_boy = (object)[
            'student_count' => $general_boy_student_count,
            'above' => $general_boy_above,
            'inline' => $general_boy_inline,
            'below' => $general_boy_below,
            'percent_above' => $general_boy_above > 0 && $general_boy_student_count > 0 ? round(($general_boy_above / $general_boy_student_count), 3) * 100 : 0,
            'percent_inline' => $general_boy_inline > 0 && $general_boy_student_count > 0 ? round(($general_boy_inline / $general_boy_student_count), 3) * 100 : 0,
            'percent_below' => $general_boy_below > 0 && $general_boy_student_count > 0 ? round(($general_boy_below / $general_boy_student_count), 3) * 100 : 0,
        ];
        //--------General Girl----------------//
        $students_terms = StudentTerm::query()
            ->whereHas('student', function (Builder $query) use ($students_SEN, $curriculums, $countries, $students_gender) {
                $query->whereHas('school', function (Builder $query) use ($curriculums, $countries) {
                    $query->whereIn('curriculum_type', $curriculums);
//                    $query->whereIn('country', $countries);
                });
                $query->where('gender', 'girl');
                $query->when($students_SEN, function (Builder $query) use ($students_SEN) {
                    $query->where('sen', $students_SEN);
                });
            })
            ->whereHas('term', function (Builder $query) use ($levels, $selected_round) {
                $query->where('round', $selected_round);
                $query->whereIn('level_id', $levels->pluck('id'));
            })
            ->where('corrected', 1)
            ->get();

        $general_girl_student_count = $students_terms->count();

        $students_terms_1 = $students_terms->whereIn('term_id', $term_1->pluck('id'));
        $students_terms_10 = $students_terms->whereIn('term_id', $term_10->pluck('id'));
        $general_girl_above =
            $students_terms_1->where('total', '>=', $data_mark_1['above'])->count() +
            $students_terms_10->where('total', '>=', $data_mark_10['above'])->count();
        $general_girl_inline =
            $students_terms_1->where('total', '>=', $data_mark_1['from_inline'])->where('total', '<', $data_mark_1['to_inline'])->count() +
            $students_terms_10->where('total', '>=', $data_mark_10['from_inline'])->where('total', '<', $data_mark_10['to_inline'])->count();
        $general_girl_below =
            $students_terms_1->where('total', '<', $data_mark_1['below'])->count() +
            $students_terms_10->where('total', '<', $data_mark_10['below'])->count();
        $general_girl = (object)[
            'student_count' => $general_girl_student_count,
            'above' => $general_girl_above,
            'inline' => $general_girl_inline,
            'below' => $general_girl_below,
            'percent_above' => $general_girl_above > 0 && $general_girl_student_count > 0 ? round(($general_girl_above / $general_girl_student_count), 3) * 100 : 0,
            'percent_inline' => $general_girl_inline > 0 && $general_girl_student_count > 0 ? round(($general_girl_inline / $general_girl_student_count), 3) * 100 : 0,
            'percent_below' => $general_girl_below > 0 && $general_girl_student_count > 0 ? round(($general_girl_below / $general_girl_student_count), 3) * 100 : 0,
        ];

        //--------All Schools (OPTIMIZED)----------------//
        list($general_schools, $general_rank, $local_rank, $type_rank, $array_general_schools, $array_local_schools, $array_type_schools) =
            $this->getAllSchoolsStatistics($school, $curriculums, $countries, $students_gender, $students_SEN, $levels, $selected_round, $term_1, $term_10, $data_mark_1, $data_mark_10);


        // Prepare data for caching (array only, no View objects)
        $viewData = compact('school', 'data', 'general_boy', 'general_girl',
            'general_sen', 'school_data', 'countries_data', 'curriculums_data', 'general_schools',
            'general_rank', 'local_rank', 'type_rank', 'total_school_students', 'girls_school_students', 'boys_school_students', 'selected_round', 'arabs_grade_ranking', 'non_arabs_grade_ranking'
            , 'array_general_schools', 'array_local_schools', 'array_type_schools', 'title');

        // Cache data for 1 hour (data only, not the view)
        Cache::put($cacheKey, $viewData, now()->addHour());

        return view('general.new_reports.comparison.comparison_report', $viewData);
    }

    public function getGradesRank($school, $year, $round, $arabs, $grades)
    {
        // OPTIMIZED: Use aggregation and caching
        $cacheKey = "grades_rank_{$school->id}_{$year}_{$round}_{$arabs}_" . implode('_', $grades);

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $data_grades = [];

        foreach ($grades as $grade) {
            // Get local average using aggregation
            $local = DB::table('student_terms as st')
                ->join('students as s', 'st.student_id', '=', 's.id')
                ->join('terms as t', 'st.term_id', '=', 't.id')
                ->join('levels as l', 't.level_id', '=', 'l.id')
                ->where('s.school_id', $school->id)
                ->where('t.round', $round)
                ->where('l.year_id', $year)
                ->where('l.arab', $arabs)
                ->where('l.grade', $grade)
                ->where('st.corrected', 1)
                ->avg('st.total');

            // Get country average using aggregation
            $country = DB::table('student_terms as st')
                ->join('students as s', 'st.student_id', '=', 's.id')
                ->join('schools as d', 's.school_id', '=', 'd.id')
                ->join('terms as t', 'st.term_id', '=', 't.id')
                ->join('levels as l', 't.level_id', '=', 'l.id')
                ->where('d.country', $school->country)
                ->where('t.round', $round)
                ->where('l.year_id', $year)
                ->where('l.arab', $arabs)
                ->where('l.grade', $grade)
                ->where('st.corrected', 1)
                ->avg('st.total');

//            // Get global average using aggregation
//            $global = DB::table('student_terms as st')
//                ->join('terms as t', 'st.term_id', '=', 't.id')
//                ->join('levels as l', 't.level_id', '=', 'l.id')
//                ->where('t.round', $round)
//                ->where('l.year_id', $year)
//                ->where('l.arab', $arabs)
//                ->where('l.grade', $grade)
//                ->where('st.corrected', 1)
//                ->avg('st.total');

//            $data_grades['global'][$grade] = $global > 0 ? round($global, 2) . '%' : '-';
            $data_grades['country'][$grade] = $country > 0 ? round($country, 2) . '%' : '-';
            $data_grades['local'][$grade] = $local > 0 ? round($local, 2) . '%' : '-';
        }

        Cache::put($cacheKey, $data_grades, now()->addHour());

        return $data_grades;
    }

    /**
     * OPTIMIZED: Get curriculums statistics using aggregation
     */
    protected function getCurriculumsStatistics($curriculums, $countries, $students_gender, $students_SEN, $levels, $selected_round, $term_1, $term_10, $data_mark_1, $data_mark_10)
    {
        $term_1_ids = $term_1->pluck('id')->toArray();
        $term_10_ids = $term_10->pluck('id')->toArray();
        $level_ids = $levels->pluck('id')->toArray();

        if (empty($term_1_ids)) $term_1_ids = [0];
        if (empty($term_10_ids)) $term_10_ids = [0];

        $results = DB::table('student_terms as st')
            ->join('students as s', 'st.student_id', '=', 's.id')
            ->join('schools as d', 's.school_id', '=', 'd.id')
            ->join('terms as t', 'st.term_id', '=', 't.id')
            ->join('levels as l', 't.level_id', '=', 'l.id')
            ->whereIn('d.curriculum_type', $curriculums)
//            ->whereIn('d.country', $countries)
            ->whereIn('t.level_id', $level_ids)
            ->where('t.round', $selected_round)
            ->where('st.corrected', 1)
            ->when($students_gender, function ($q) use ($students_gender) {
                return $q->where('s.gender', $students_gender);
            })
            ->when($students_SEN, function ($q) use ($students_SEN) {
                return $q->where('s.sen', $students_SEN);
            })
            ->select(
                'd.curriculum_type as curriculum_id',
                DB::raw('COUNT(*) as total_students'),
                DB::raw("SUM(CASE
                    WHEN (st.term_id IN (" . implode(',', $term_1_ids) . ") AND st.total >= {$data_mark_1['above']})
                    OR (st.term_id IN (" . implode(',', $term_10_ids) . ") AND st.total >= {$data_mark_10['above']})
                    THEN 1 ELSE 0 END) as above_count"),
                DB::raw("SUM(CASE
                    WHEN (st.term_id IN (" . implode(',', $term_1_ids) . ") AND st.total >= {$data_mark_1['from_inline']} AND st.total < {$data_mark_1['to_inline']})
                    OR (st.term_id IN (" . implode(',', $term_10_ids) . ") AND st.total >= {$data_mark_10['from_inline']} AND st.total < {$data_mark_10['to_inline']})
                    THEN 1 ELSE 0 END) as inline_count"),
                DB::raw("SUM(CASE
                    WHEN (st.term_id IN (" . implode(',', $term_1_ids) . ") AND st.total < {$data_mark_1['below']})
                    OR (st.term_id IN (" . implode(',', $term_10_ids) . ") AND st.total < {$data_mark_10['below']})
                    THEN 1 ELSE 0 END) as below_count")
            )
            ->groupBy('d.curriculum_type')
            ->get();

        $curriculums_data = collect([]);
        foreach ($results as $row) {
            $per_above = $row->above_count > 0 && $row->total_students > 0 ? ($row->above_count / $row->total_students) * 100 : 0;

            $curriculums_data->push((object)[
                'ID' => $row->curriculum_id,
                'above' => $row->above_count,
                'inline' => $row->inline_count,
                'below' => $row->below_count,
                'per_above' => $per_above,
            ]);
        }

        return $curriculums_data->sortByDesc('per_above')->all();
    }

    /**
     * OPTIMIZED: Get countries statistics using aggregation
     */
    protected function getCountriesStatistics($countries, $curriculums, $students_gender, $students_SEN, $levels, $selected_round, $term_1, $term_10, $data_mark_1, $data_mark_10)
    {
        $term_1_ids = $term_1->pluck('id')->toArray();
        $term_10_ids = $term_10->pluck('id')->toArray();
        $level_ids = $levels->pluck('id')->toArray();

        if (empty($term_1_ids)) $term_1_ids = [0];
        if (empty($term_10_ids)) $term_10_ids = [0];

        $results = DB::table('student_terms as st')
            ->join('students as s', 'st.student_id', '=', 's.id')
            ->join('schools as d', 's.school_id', '=', 'd.id')
            ->join('terms as t', 'st.term_id', '=', 't.id')
            ->join('levels as l', 't.level_id', '=', 'l.id')
            ->whereIn('d.curriculum_type', $curriculums)
//            ->whereIn('d.country', $countries)
            ->whereIn('t.level_id', $level_ids)
            ->where('t.round', $selected_round)
            ->where('st.corrected', 1)
            ->when($students_gender, function ($q) use ($students_gender) {
                return $q->where('s.gender', $students_gender);
            })
            ->when($students_SEN, function ($q) use ($students_SEN) {
                return $q->where('s.sen', $students_SEN);
            })
            ->select(
                'd.country as country_id',
                DB::raw('COUNT(*) as total_students'),
                DB::raw("SUM(CASE
                    WHEN (st.term_id IN (" . implode(',', $term_1_ids) . ") AND st.total >= {$data_mark_1['above']})
                    OR (st.term_id IN (" . implode(',', $term_10_ids) . ") AND st.total >= {$data_mark_10['above']})
                    THEN 1 ELSE 0 END) as above_count"),
                DB::raw("SUM(CASE
                    WHEN (st.term_id IN (" . implode(',', $term_1_ids) . ") AND st.total >= {$data_mark_1['from_inline']} AND st.total < {$data_mark_1['to_inline']})
                    OR (st.term_id IN (" . implode(',', $term_10_ids) . ") AND st.total >= {$data_mark_10['from_inline']} AND st.total < {$data_mark_10['to_inline']})
                    THEN 1 ELSE 0 END) as inline_count"),
                DB::raw("SUM(CASE
                    WHEN (st.term_id IN (" . implode(',', $term_1_ids) . ") AND st.total < {$data_mark_1['below']})
                    OR (st.term_id IN (" . implode(',', $term_10_ids) . ") AND st.total < {$data_mark_10['below']})
                    THEN 1 ELSE 0 END) as below_count")
            )
            ->groupBy('d.country')
            ->get();

        $countries_data = collect([]);
        foreach ($results as $row) {
            $per_above = $row->above_count > 0 && $row->total_students > 0 ? ($row->above_count / $row->total_students) * 100 : 0;

            $countries_data->push((object)[
                'ID' => $row->country_id,
                'above' => $row->above_count,
                'inline' => $row->inline_count,
                'below' => $row->below_count,
                'per_above' => $per_above,
            ]);
        }

        return $countries_data->sortByDesc('per_above')->all();
    }

    /**
     * OPTIMIZED: Get all schools statistics using aggregation (BIGGEST IMPROVEMENT)
     */
    protected function getAllSchoolsStatistics($school, $curriculums, $countries, $students_gender, $students_SEN, $levels, $selected_round, $term_1, $term_10, $data_mark_1, $data_mark_10)
    {
        $term_1_ids = $term_1->pluck('id')->toArray();
        $term_10_ids = $term_10->pluck('id')->toArray();
        $level_ids = $levels->pluck('id')->toArray();

        if (empty($term_1_ids)) $term_1_ids = [0];
        if (empty($term_10_ids)) $term_10_ids = [0];

        $results = DB::table('student_terms as st')
            ->join('students as s', 'st.student_id', '=', 's.id')
            ->join('schools as d', 's.school_id', '=', 'd.id')
            ->join('terms as t', 'st.term_id', '=', 't.id')
            ->join('levels as l', 't.level_id', '=', 'l.id')
            ->whereIn('d.curriculum_type', $curriculums)
//            ->whereIn('d.country', $countries)
            ->where('d.active', 1)
            ->whereIn('t.level_id', $level_ids)
            ->where('t.round', $selected_round)
            ->where('st.corrected', 1)
            ->when($students_gender, function ($q) use ($students_gender) {
                return $q->where('s.gender', $students_gender);
            })
            ->when($students_SEN, function ($q) use ($students_SEN) {
                return $q->where('s.sen', $students_SEN);
            })
            ->select(
                'd.id as school_id',
                'd.name',
                'd.curriculum_type as school_type',
                'd.country as school_country',
                DB::raw('COUNT(*) as total_students'),
                DB::raw("SUM(CASE
                    WHEN (st.term_id IN (" . implode(',', $term_1_ids) . ") AND st.total >= {$data_mark_1['above']})
                    OR (st.term_id IN (" . implode(',', $term_10_ids) . ") AND st.total >= {$data_mark_10['above']})
                    THEN 1 ELSE 0 END) as above_count"),
                DB::raw("SUM(CASE
                    WHEN (st.term_id IN (" . implode(',', $term_1_ids) . ") AND st.total >= {$data_mark_1['from_inline']} AND st.total < {$data_mark_1['to_inline']})
                    OR (st.term_id IN (" . implode(',', $term_10_ids) . ") AND st.total >= {$data_mark_10['from_inline']} AND st.total < {$data_mark_10['to_inline']})
                    THEN 1 ELSE 0 END) as inline_count"),
                DB::raw("SUM(CASE
                    WHEN (st.term_id IN (" . implode(',', $term_1_ids) . ") AND st.total < {$data_mark_1['below']})
                    OR (st.term_id IN (" . implode(',', $term_10_ids) . ") AND st.total < {$data_mark_10['below']})
                    THEN 1 ELSE 0 END) as below_count")
            )
            ->groupBy('d.id', 'd.name', 'd.curriculum_type', 'd.country')
            ->havingRaw('COUNT(*) > 0')
            ->get();

        $general_schools = collect([]);
        foreach ($results as $row) {
            $percent_above = $row->above_count > 0 && $row->total_students > 0 ? round(($row->above_count / $row->total_students), 3) * 100 : 0;
            $percent_inline = $row->inline_count > 0 && $row->total_students > 0 ? round(($row->inline_count / $row->total_students), 3) * 100 : 0;
            $percent_below = $row->below_count > 0 && $row->total_students > 0 ? round(($row->below_count / $row->total_students), 3) * 100 : 0;

            $type = $row->school_type == 'International Baccalaureate' ? 'IB' : $row->school_type;
            $locale = app()->getLocale();
            $raw_name = json_decode($row->name, true);
            $localized_name = is_array($raw_name) ? ($raw_name[$locale] ?? $raw_name['en'] ?? $row->name) : $row->name;
            $school_name = ($row->school_id == $school->id) ? $school->name : substr($localized_name, 0, 1) . ' - ' . $type . ' Curriculum';

            $general_schools->push((object)[
                'school_id' => $row->school_id,
                'school_name' => $school_name,
                'student_count' => $row->total_students,
                'school_country' => $row->school_country,
                'school_type' => $row->school_type,
                'above' => $row->above_count,
                'inline' => $row->inline_count,
                'below' => $row->below_count,
                'percent_above' => $percent_above,
                'percent_inline' => $percent_inline,
                'percent_below' => $percent_below,
            ]);
        }

        $general_schools = $general_schools->sortByDesc('percent_inline')
            ->sortByDesc('percent_above')
            ->values();

        $array_general_schools = $general_schools->pluck('school_id')->toArray();
        $array_local_schools = $general_schools->where('school_country', $school->country)->pluck('school_id')->toArray();
        $array_type_schools = $general_schools->where('school_type', $school->type)->pluck('school_id')->toArray();

        $general_rank = in_array($school->id, $array_general_schools) ? array_search($school->id, $array_general_schools) + 1 : 0;
        $local_rank = in_array($school->id, $array_local_schools) ? array_search($school->id, $array_local_schools) + 1 : 0;
        $type_rank = in_array($school->id, $array_type_schools) ? array_search($school->id, $array_type_schools) + 1 : 0;

        $general_schools = $general_schools->chunk(20);

        return [$general_schools, $general_rank, $local_rank, $type_rank, $array_general_schools, $array_local_schools, $array_type_schools];
    }

    protected function getCacheKey()
    {
        $params = [
            'school_id' => $this->school->id,
            'curriculums' => $this->request->get('curriculums'),
            'countries' => $this->request->get('countries', []),
            'gender' => $this->request->get('gender', false),
            'round' => $this->request->get('round', false),
            'sen_students' => $this->request->get('sen_students', false),
            'year' => $this->request->get('year_id', false),
            'grades' => $this->request->get('grades', []),
            'student_type' => $this->request->get('student_type', false),
            'report_type' => $this->request->get('report_type', 1),
        ];

        return 'comparison_report_' . md5(json_encode($params));
    }
}
