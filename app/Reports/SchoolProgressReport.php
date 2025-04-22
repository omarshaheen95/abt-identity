<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Reports;

use App\Models\StudentTerm;
use App\Models\Term;
use App\Models\Year;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SchoolProgressReport
{
    public $school;
    public $request;
    public $start;
    public $end;

    public function __construct(Request $request, $school)
    {
        $this->school = $school;
        $this->request = $request;
    }

    public function report()
    {
        $school = $this->school;
        $year_id = $this->request->get('year_id', false);
        $type = $this->request->get('student_type', false);
        $grades = $this->request->get('grades', []);
        $year = Year::query()->findOrFail($year_id);

        if ($type == 0) {
            $sub_title = 'Non-Arabs';
        } elseif ($type == 1) {
            $sub_title = 'Arabs';
        } elseif ($type == 2) {
            $sub_title = 'Arabs & Non-arabs';
        } else {
            $sub_title = '';
        }


        $arr_date = explode("/", $year->name);
        $start = $arr_date[0];
        $end = $arr_date[1];
        if ($school->school_type == "Indian") {
            $months_num = ['May', 'September', 'February'];

            $custom_year = $year_id - 1;
        } else {
            $months_num = ['September', 'February', 'May'];

            $custom_year = $year_id;
        }

        $steps = $rounds = [
            $months_num[0] .' - '. $months_num[1],
            $months_num[1] .' - '. $months_num[2],
            $months_num[0] .' - '. $months_num[2],
        ];


        $arab_pages = [];

        foreach ($grades as $grade) {
            $general_rounds = [];
            $student_terms = collect();
            //September
            $september_terms = Term::query()->whereHas('level', function (Builder $query) use ($grade, $custom_year, $type) {
                $query->where('grade', $grade)
                    ->when($type != 2, function (Builder $query) use ($type) {
                        $query->where('arab', $type);
                    })
                    ->where('year_id', $custom_year);
            })->where('round', strtolower($months_num[0]))->get();
            //February
            $february_terms = Term::query()->whereHas('level', function (Builder $query) use ($grade, $year, $type) {
                $query->where('grade', $grade)
                    ->when($type != 2, function (Builder $query) use ($type) {
                        $query->where('arab', $type);
                    })
                    ->where('year_id', $year->id);
            })->where('round', strtolower($months_num[1]))->get();
            //May
            $may_terms = Term::query()->whereHas('level', function (Builder $query) use ($grade, $year, $type) {
                $query->where('grade', $grade)
                    ->when($type != 2, function (Builder $query) use ($type) {
                        $query->where('arab', $type);
                    })
                    ->where('year_id', $year->id);
            })->where('round', strtolower($months_num[2]))->get();

            $sept_student_terms = StudentTerm::query()->with(['student'])
                ->whereHas('student', function (Builder $query) use ($school) {
                    $query->where('school_id', $school->id);
                })
                ->whereIn('term_id', $september_terms->pluck('id'))
                ->where('corrected', 1)
                ->get();

            $feb_student_terms = StudentTerm::query()->with(['student'])
                ->whereHas('student', function (Builder $query) use ($school) {
                    $query->where('school_id', $school->id);
                })
                ->whereIn('term_id', $february_terms->pluck('id'))
                ->where('corrected', 1)
                ->get();

            $may_student_terms = StudentTerm::query()->with(['student'])
                ->whereHas('student', function (Builder $query) use ($school) {
                    $query->where('school_id', $school->id);
                })
                ->whereIn('term_id', $may_terms->pluck('id'))
                ->where('corrected', 1)
                ->get();


            $student_terms = $student_terms
                ->merge($sept_student_terms->pluck('student_id')->unique())
                ->merge($feb_student_terms->pluck('student_id')->unique())
                ->merge($may_student_terms->pluck('student_id')->unique())
                ->unique();

            if ($sept_student_terms->count() && $feb_student_terms->count()) {
                $above_expected = 0;
                $same_expected = 0;
                $below_expected = 0;
                $male_above_expected = 0;
                $male_same_expected = 0;
                $male_below_expected = 0;
                $female_above_expected = 0;
                $female_same_expected = 0;
                $female_below_expected = 0;
                $sen_above_expected = 0;
                $sen_same_expected = 0;
                $sen_below_expected = 0;
                $uae_above_expected = 0;
                $uae_same_expected = 0;
                $uae_below_expected = 0;
                $uae_male_above_expected = 0;
                $uae_male_same_expected = 0;
                $uae_male_below_expected = 0;
                $uae_female_above_expected = 0;
                $uae_female_same_expected = 0;
                $uae_female_below_expected = 0;
                foreach ($student_terms as $student_term) {
                    $studentTermSept = $sept_student_terms->where('student_id', $student_term)
                        ->whereIn('term_id', $september_terms->pluck('id'))
                        ->first();


                    $studentTermFeb = $feb_student_terms->where('student_id', $student_term)
                        ->whereIn('term_id', $february_terms->pluck('id'))
                        ->first();

                    if ($studentTermSept && $studentTermFeb) {
                        $total_mark_2 = $studentTermFeb->total;
                        $total_mark_1 = $studentTermSept->total;
                        $total_mar_result = $total_mark_2 - $total_mark_1;

                        $progressRate = getProgress($total_mark_1, $total_mar_result);

                        if ($progressRate == 1) {
                            $above_expected++;
                        } elseif ($progressRate == 0) {
                            $same_expected++;
                        } else {
                            $below_expected++;
                        }

                        if ($studentTermSept->student->gender == 'boy') {
                            if ($progressRate == 1) {
                                $male_above_expected++;
                            } elseif ($progressRate == 0) {
                                $male_same_expected++;
                            } else {
                                $male_below_expected++;
                            }
                        }
                        if ($studentTermSept->student->gender == 'girl') {
                            if ($progressRate == 1) {
                                $female_above_expected++;
                            } elseif ($progressRate == 0) {
                                $female_same_expected++;
                            } else {
                                $female_below_expected++;
                            }
                        }
                        if ($studentTermSept->student->uae_student == 1) {
                            if ($progressRate == 1) {
                                $uae_above_expected++;
                            } elseif ($progressRate == 0) {
                                $uae_same_expected++;
                            } else {
                                $uae_below_expected++;
                            }
                        }
                        if ($studentTermSept->student->sen_student == 1) {
                            if ($progressRate == 1) {
                                $sen_above_expected++;
                            } elseif ($progressRate == 0) {
                                $sen_same_expected++;
                            } else {
                                $sen_below_expected++;
                            }
                        }

                        if ($studentTermSept->student->uae_student == 1 && $studentTermSept->student->gender == 'boy') {
                            if ($progressRate == 1) {
                                $uae_male_above_expected++;
                            } elseif ($progressRate == 0) {
                                $uae_male_same_expected++;
                            } else {
                                $uae_male_below_expected++;
                            }
                        }
                        if ($studentTermSept->student->uae_student == 1 && $studentTermSept->student->gender == 'girl') {
                            if ($progressRate == 1) {
                                $uae_female_above_expected++;
                            } elseif ($progressRate == 0) {
                                $uae_female_same_expected++;
                            } else {
                                $uae_female_below_expected++;
                            }
                        }


                    }


                }
                $total = $above_expected + $same_expected + $below_expected;
                $septProgressData = [
                    'name' => $rounds[0],
                    'above' => $above_expected,
                    'above_ratio' => $above_expected > 0 && $total > 0 ? round(($above_expected / $total), 4) * 100 : 0,
                    'inline' => $same_expected,
                    'inline_ratio' => $same_expected > 0 && $total > 0 ? round(($same_expected / $total), 4) * 100 : 0,
                    'below' => $below_expected,
                    'below_ratio' => $below_expected > 0 && $total > 0 ? round(($below_expected / $total), 4) * 100 : 0,
                    'total' => $total,
                ];

                $total = $male_above_expected + $male_same_expected + $male_below_expected;
                $septMaleProgressData = [
                    'name' => $rounds[0],
                    'above' => $male_above_expected,
                    'above_ratio' => $male_above_expected > 0 && $total > 0 ? round(($male_above_expected / $total), 4) * 100 : 0,
                    'inline' => $male_same_expected,
                    'inline_ratio' => $male_same_expected > 0 && $total > 0 ? round(($male_same_expected / $total), 4) * 100 : 0,
                    'below' => $male_below_expected,
                    'below_ratio' => $male_below_expected > 0 && $total > 0 ? round(($male_below_expected / $total), 4) * 100 : 0,
                    'total' => $total,
                ];

                $total = $female_above_expected + $female_same_expected + $female_below_expected;
                $septFemaleProgressData = [
                    'name' => $rounds[0],
                    'above' => $female_above_expected,
                    'above_ratio' => $female_above_expected > 0 && $total > 0 ? round(($female_above_expected / $total), 4) * 100 : 0,
                    'inline' => $female_same_expected,
                    'inline_ratio' => $female_same_expected > 0 && $total > 0 ? round(($female_same_expected / $total), 4) * 100 : 0,
                    'below' => $female_below_expected,
                    'below_ratio' => $female_below_expected > 0 && $total > 0 ? round(($female_below_expected / $total), 4) * 100 : 0,
                    'total' => $total,
                ];

                $total = $sen_above_expected + $sen_same_expected + $sen_below_expected;
                $septSenProgressData = [
                    'name' => $rounds[0],
                    'above' => $sen_above_expected,
                    'above_ratio' => $sen_above_expected > 0 && $total > 0 ? round(($sen_above_expected / $total), 4) * 100 : 0,
                    'inline' => $sen_same_expected,
                    'inline_ratio' => $sen_same_expected > 0 && $total > 0 ? round(($sen_same_expected / $total), 4) * 100 : 0,
                    'below' => $sen_below_expected,
                    'below_ratio' => $sen_below_expected > 0 && $total > 0 ? round(($sen_below_expected / $total), 4) * 100 : 0,
                    'total' => $total,
                ];

                $total = $uae_above_expected + $uae_same_expected + $uae_below_expected;
                $septUaeProgressData = [
                    'name' => $rounds[0],
                    'above' => $uae_above_expected,
                    'above_ratio' => $uae_above_expected > 0 && $total > 0 ? round(($uae_above_expected / $total), 4) * 100 : 0,
                    'inline' => $uae_same_expected,
                    'inline_ratio' => $uae_same_expected > 0 && $total > 0 ? round(($uae_same_expected / $total), 4) * 100 : 0,
                    'below' => $uae_below_expected,
                    'below_ratio' => $uae_below_expected > 0 && $total > 0 ? round(($uae_below_expected / $total), 4) * 100 : 0,
                    'total' => $total,
                ];

                $total = $uae_male_above_expected + $uae_male_same_expected + $uae_male_below_expected;
                $septUaeMaleProgressData = [
                    'name' => $rounds[0],
                    'above' => $uae_male_above_expected,
                    'above_ratio' => $uae_male_above_expected > 0 && $total > 0 ? round(($uae_male_above_expected / $total), 4) * 100 : 0,
                    'inline' => $uae_male_same_expected,
                    'inline_ratio' => $uae_male_same_expected > 0 && $total > 0 ? round(($uae_male_same_expected / $total), 4) * 100 : 0,
                    'below' => $uae_male_below_expected,
                    'below_ratio' => $uae_male_below_expected > 0 && $total > 0 ? round(($uae_male_below_expected / $total), 4) * 100 : 0,
                    'total' => $total,
                ];

                $total = $uae_female_above_expected + $uae_female_same_expected + $uae_female_below_expected;
                $septUaeFemaleProgressData = [
                    'name' => $rounds[0],
                    'above' => $uae_female_above_expected,
                    'above_ratio' => $uae_female_above_expected > 0 && $total > 0 ? round(($uae_female_above_expected / $total), 4) * 100 : 0,
                    'inline' => $uae_female_same_expected,
                    'inline_ratio' => $uae_female_same_expected > 0 && $total > 0 ? round(($uae_female_same_expected / $total), 4) * 100 : 0,
                    'below' => $uae_female_below_expected,
                    'below_ratio' => $uae_female_below_expected > 0 && $total > 0 ? round(($uae_female_below_expected / $total), 4) * 100 : 0,
                    'total' => $total,
                ];

            } else {
                $septProgressData = [
                    'name' => $rounds[0],
                    'above_ratio' => 0,
                    'above' => 0,
                    'inline_ratio' => 0,
                    'inline' => 0,
                    'below_ratio' => 0,
                    'below' => 0,
                    'total' => 0,
                ];
                $septProgressData = [
                    'name' => $rounds[0],
                    'above_ratio' => 0,
                    'above' => 0,
                    'inline_ratio' => 0,
                    'inline' => 0,
                    'below_ratio' => 0,
                    'below' => 0,
                    'total' => 0,
                ];
                $septMaleProgressData = [
                    'name' => $rounds[0],
                    'above_ratio' => 0,
                    'above' => 0,
                    'inline_ratio' => 0,
                    'inline' => 0,
                    'below_ratio' => 0,
                    'below' => 0,
                    'total' => 0,
                ];
                $septFemaleProgressData = [
                    'name' => $rounds[0],
                    'above_ratio' => 0,
                    'above' => 0,
                    'inline_ratio' => 0,
                    'inline' => 0,
                    'below_ratio' => 0,
                    'below' => 0,
                    'total' => 0,
                ];
                $septSenProgressData = [
                    'name' => $rounds[0],
                    'above_ratio' => 0,
                    'above' => 0,
                    'inline_ratio' => 0,
                    'inline' => 0,
                    'below_ratio' => 0,
                    'below' => 0,
                    'total' => 0,
                ];
                $septUaeProgressData = [
                    'name' => $rounds[0],
                    'above_ratio' => 0,
                    'above' => 0,
                    'inline_ratio' => 0,
                    'inline' => 0,
                    'below_ratio' => 0,
                    'below' => 0,
                    'total' => 0,
                ];
                $septUaeMaleProgressData = [
                    'name' => $rounds[0],
                    'above_ratio' => 0,
                    'above' => 0,
                    'inline_ratio' => 0,
                    'inline' => 0,
                    'below_ratio' => 0,
                    'below' => 0,
                    'total' => 0,
                ];
                $septUaeFemaleProgressData = [
                    'name' => $rounds[0],
                    'above_ratio' => 0,
                    'above' => 0,
                    'inline_ratio' => 0,
                    'inline' => 0,
                    'below_ratio' => 0,
                    'below' => 0,
                    'total' => 0,
                ];
            }
            if ($may_student_terms->count() && $feb_student_terms->count()) {
                $above_expected = 0;
                $same_expected = 0;
                $below_expected = 0;

                $male_above_expected = 0;
                $male_same_expected = 0;
                $male_below_expected = 0;
                $female_above_expected = 0;
                $female_same_expected = 0;
                $female_below_expected = 0;
                $sen_above_expected = 0;
                $sen_same_expected = 0;
                $sen_below_expected = 0;
                $uae_above_expected = 0;
                $uae_same_expected = 0;
                $uae_below_expected = 0;
                $uae_male_above_expected = 0;
                $uae_male_same_expected = 0;
                $uae_male_below_expected = 0;
                $uae_female_above_expected = 0;
                $uae_female_same_expected = 0;
                $uae_female_below_expected = 0;
                foreach ($student_terms as $student_term) {

                    $studentTermFeb = $feb_student_terms->where('student_id', $student_term)
                        ->whereIn('term_id', $february_terms->pluck('id'))
                        ->first();

                    $studentTermMay = $may_student_terms->where('student_id', $student_term)
                        ->whereIn('term_id', $may_terms->pluck('id'))
                        ->first();

                    if ($studentTermFeb && $studentTermMay) {
                        $total_mark_2 = $studentTermFeb->total;
                        $total_mark_3 = $studentTermMay->total;
                        $total_mar_result = $total_mark_3 - $total_mark_2;

                        $progressRate = getProgress($total_mark_2, $total_mar_result);

                        if ($progressRate == 1) {
                            $above_expected++;
                        } elseif ($progressRate == 0) {
                            $same_expected++;
                        } else {
                            $below_expected++;
                        }

                        if ($studentTermFeb->student->gender == 'boy') {
                            if ($progressRate == 1) {
                                $male_above_expected++;
                            } elseif ($progressRate == 0) {
                                $male_same_expected++;
                            } else {
                                $male_below_expected++;
                            }
                        }
                        if ($studentTermFeb->student->gender == 'girl') {
                            if ($progressRate == 1) {
                                $female_above_expected++;
                            } elseif ($progressRate == 0) {
                                $female_same_expected++;
                            } else {
                                $female_below_expected++;
                            }
                        }
                        if ($studentTermFeb->student->uae_student == 1) {
                            if ($progressRate == 1) {
                                $uae_above_expected++;
                            } elseif ($progressRate == 0) {
                                $uae_same_expected++;
                            } else {
                                $uae_below_expected++;
                            }
                        }
                        if ($studentTermFeb->student->sen_student == 1) {
                            if ($progressRate == 1) {
                                $sen_above_expected++;
                            } elseif ($progressRate == 0) {
                                $sen_same_expected++;
                            } else {
                                $sen_below_expected++;
                            }
                        }

                        if ($studentTermFeb->student->uae_student == 1 && $studentTermFeb->student->gender == 'boy') {
                            if ($progressRate == 1) {
                                $uae_male_above_expected++;
                            } elseif ($progressRate == 0) {
                                $uae_male_same_expected++;
                            } else {
                                $uae_male_below_expected++;
                            }
                        }
                        if ($studentTermFeb->student->uae_student == 1 && $studentTermFeb->student->gender == 'girl') {
                            if ($progressRate == 1) {
                                $uae_female_above_expected++;
                            } elseif ($progressRate == 0) {
                                $uae_female_same_expected++;
                            } else {
                                $uae_female_below_expected++;
                            }
                        }


                    }


                }
                $total = $above_expected + $same_expected + $below_expected;
                $febProgressData = [
                    'name' => $rounds[1],
                    'above' => $above_expected,
                    'above_ratio' => $above_expected > 0 && $total > 0 ? round(($above_expected / $total), 4) * 100 : 0,
                    'inline' => $same_expected,
                    'inline_ratio' => $same_expected > 0 && $total > 0 ? round(($same_expected / $total), 4) * 100 : 0,
                    'below' => $below_expected,
                    'below_ratio' => $below_expected > 0 && $total > 0 ? round(($below_expected / $total), 4) * 100 : 0,
                    'total' => $total,
                ];

                $total = $male_above_expected + $male_same_expected + $male_below_expected;
                $febMaleProgressData = [
                    'name' => $rounds[1],
                    'above' => $male_above_expected,
                    'above_ratio' => $male_above_expected > 0 && $total > 0 ? round(($male_above_expected / $total), 4) * 100 : 0,
                    'inline' => $male_same_expected,
                    'inline_ratio' => $male_same_expected > 0 && $total > 0 ? round(($male_same_expected / $total), 4) * 100 : 0,
                    'below' => $male_below_expected,
                    'below_ratio' => $male_below_expected > 0 && $total > 0 ? round(($male_below_expected / $total), 4) * 100 : 0,
                    'total' => $total,
                ];

                $total = $female_above_expected + $female_same_expected + $female_below_expected;
                $febFemaleProgressData = [
                    'name' => $rounds[1],
                    'above' => $female_above_expected,
                    'above_ratio' => $female_above_expected > 0 && $total > 0 ? round(($female_above_expected / $total), 4) * 100 : 0,
                    'inline' => $female_same_expected,
                    'inline_ratio' => $female_same_expected > 0 && $total > 0 ? round(($female_same_expected / $total), 4) * 100 : 0,
                    'below' => $female_below_expected,
                    'below_ratio' => $female_below_expected > 0 && $total > 0 ? round(($female_below_expected / $total), 4) * 100 : 0,
                    'total' => $total,
                ];

                $total = $sen_above_expected + $sen_same_expected + $sen_below_expected;
                $febSenProgressData = [
                    'name' => $rounds[1],
                    'above' => $sen_above_expected,
                    'above_ratio' => $sen_above_expected > 0 && $total > 0 ? round(($sen_above_expected / $total), 4) * 100 : 0,
                    'inline' => $sen_same_expected,
                    'inline_ratio' => $sen_same_expected > 0 && $total > 0 ? round(($sen_same_expected / $total), 4) * 100 : 0,
                    'below' => $sen_below_expected,
                    'below_ratio' => $sen_below_expected > 0 && $total > 0 ? round(($sen_below_expected / $total), 4) * 100 : 0,
                    'total' => $total,
                ];

                $total = $uae_above_expected + $uae_same_expected + $uae_below_expected;
                $febUaeProgressData = [
                    'name' => $rounds[1],
                    'above' => $uae_above_expected,
                    'above_ratio' => $uae_above_expected > 0 && $total > 0 ? round(($uae_above_expected / $total), 4) * 100 : 0,
                    'inline' => $uae_same_expected,
                    'inline_ratio' => $uae_same_expected > 0 && $total > 0 ? round(($uae_same_expected / $total), 4) * 100 : 0,
                    'below' => $uae_below_expected,
                    'below_ratio' => $uae_below_expected > 0 && $total > 0 ? round(($uae_below_expected / $total), 4) * 100 : 0,
                    'total' => $total,
                ];

                $total = $uae_male_above_expected + $uae_male_same_expected + $uae_male_below_expected;
                $febUaeMaleProgressData = [
                    'name' => $rounds[1],
                    'above' => $uae_male_above_expected,
                    'above_ratio' => $uae_male_above_expected > 0 && $total > 0 ? round(($uae_male_above_expected / $total), 4) * 100 : 0,
                    'inline' => $uae_male_same_expected,
                    'inline_ratio' => $uae_male_same_expected > 0 && $total > 0 ? round(($uae_male_same_expected / $total), 4) * 100 : 0,
                    'below' => $uae_male_below_expected,
                    'below_ratio' => $uae_male_below_expected > 0 && $total > 0 ? round(($uae_male_below_expected / $total), 4) * 100 : 0,
                    'total' => $total,
                ];

                $total = $uae_female_above_expected + $uae_female_same_expected + $uae_female_below_expected;
                $febUaeFemaleProgressData = [
                    'name' => $rounds[1],
                    'above' => $uae_female_above_expected,
                    'above_ratio' => $uae_female_above_expected > 0 && $total > 0 ? round(($uae_female_above_expected / $total), 4) * 100 : 0,
                    'inline' => $uae_female_same_expected,
                    'inline_ratio' => $uae_female_same_expected > 0 && $total > 0 ? round(($uae_female_same_expected / $total), 4) * 100 : 0,
                    'below' => $uae_female_below_expected,
                    'below_ratio' => $uae_female_below_expected > 0 && $total > 0 ? round(($uae_female_below_expected / $total), 4) * 100 : 0,
                    'total' => $total,
                ];
            } else {
                $febProgressData = [
                    'name' => $rounds[1],
                    'above_ratio' => 0,
                    'above' => 0,
                    'inline_ratio' => 0,
                    'inline' => 0,
                    'below_ratio' => 0,
                    'below' => 0,
                    'total' => 0,
                ];
                $febProgressData = [
                    'name' => $rounds[1],
                    'above_ratio' => 0,
                    'above' => 0,
                    'inline_ratio' => 0,
                    'inline' => 0,
                    'below_ratio' => 0,
                    'below' => 0,
                    'total' => 0,
                ];
                $febMaleProgressData = [
                    'name' => $rounds[1],
                    'above_ratio' => 0,
                    'above' => 0,
                    'inline_ratio' => 0,
                    'inline' => 0,
                    'below_ratio' => 0,
                    'below' => 0,
                    'total' => 0,
                ];
                $febFemaleProgressData = [
                    'name' => $rounds[1],
                    'above_ratio' => 0,
                    'above' => 0,
                    'inline_ratio' => 0,
                    'inline' => 0,
                    'below_ratio' => 0,
                    'below' => 0,
                    'total' => 0,
                ];
                $febSenProgressData = [
                    'name' => $rounds[1],
                    'above_ratio' => 0,
                    'above' => 0,
                    'inline_ratio' => 0,
                    'inline' => 0,
                    'below_ratio' => 0,
                    'below' => 0,
                    'total' => 0,
                ];
                $febUaeProgressData = [
                    'name' => $rounds[1],
                    'above_ratio' => 0,
                    'above' => 0,
                    'inline_ratio' => 0,
                    'inline' => 0,
                    'below_ratio' => 0,
                    'below' => 0,
                    'total' => 0,
                ];
                $febUaeMaleProgressData = [
                    'name' => $rounds[1],
                    'above_ratio' => 0,
                    'above' => 0,
                    'inline_ratio' => 0,
                    'inline' => 0,
                    'below_ratio' => 0,
                    'below' => 0,
                    'total' => 0,
                ];
                $febUaeFemaleProgressData = [
                    'name' => $rounds[1],
                    'above_ratio' => 0,
                    'above' => 0,
                    'inline_ratio' => 0,
                    'inline' => 0,
                    'below_ratio' => 0,
                    'below' => 0,
                    'total' => 0,
                ];
            }
            if ($may_student_terms->count() && $sept_student_terms->count()) {
                $above_expected = 0;
                $same_expected = 0;
                $below_expected = 0;

                $male_above_expected = 0;
                $male_same_expected = 0;
                $male_below_expected = 0;
                $female_above_expected = 0;
                $female_same_expected = 0;
                $female_below_expected = 0;
                $sen_above_expected = 0;
                $sen_same_expected = 0;
                $sen_below_expected = 0;
                $uae_above_expected = 0;
                $uae_same_expected = 0;
                $uae_below_expected = 0;
                $uae_male_above_expected = 0;
                $uae_male_same_expected = 0;
                $uae_male_below_expected = 0;
                $uae_female_above_expected = 0;
                $uae_female_same_expected = 0;
                $uae_female_below_expected = 0;
                foreach ($student_terms as $student_term) {

                    $studentTermSept = $sept_student_terms->where('student_id', $student_term)
                        ->whereIn('term_id', $september_terms->pluck('id'))
                        ->first();

                    $studentTermMay = $may_student_terms->where('student_id', $student_term)
                        ->whereIn('term_id', $may_terms->pluck('id'))
                        ->first();

                    if ($studentTermSept && $studentTermMay) {
                        $total_mark_3 = $studentTermMay->total;
                        $total_mark_1 = $studentTermSept->total;
                        $total_mar_result = $total_mark_3 - $total_mark_1;

                        $progressRate = getProgress($total_mark_1, $total_mar_result);

                        if ($progressRate == 1) {
                            $above_expected++;
                        } elseif ($progressRate == 0) {
                            $same_expected++;
                        } else {
                            $below_expected++;
                        }

                        if ($studentTermSept->student->gender == 'boy') {
                            if ($progressRate == 1) {
                                $male_above_expected++;
                            } elseif ($progressRate == 0) {
                                $male_same_expected++;
                            } else {
                                $male_below_expected++;
                            }
                        }
                        if ($studentTermSept->student->gender == 'girl') {
                            if ($progressRate == 1) {
                                $female_above_expected++;
                            } elseif ($progressRate == 0) {
                                $female_same_expected++;
                            } else {
                                $female_below_expected++;
                            }
                        }
                        if ($studentTermSept->student->uae_student == 1) {
                            if ($progressRate == 1) {
                                $uae_above_expected++;
                            } elseif ($progressRate == 0) {
                                $uae_same_expected++;
                            } else {
                                $uae_below_expected++;
                            }
                        }
                        if ($studentTermSept->student->sen_student == 1) {
                            if ($progressRate == 1) {
                                $sen_above_expected++;
                            } elseif ($progressRate == 0) {
                                $sen_same_expected++;
                            } else {
                                $sen_below_expected++;
                            }
                        }

                        if ($studentTermSept->student->uae_student == 1 && $studentTermSept->student->gender == 'boy') {
                            if ($progressRate == 1) {
                                $uae_male_above_expected++;
                            } elseif ($progressRate == 0) {
                                $uae_male_same_expected++;
                            } else {
                                $uae_male_below_expected++;
                            }
                        }
                        if ($studentTermSept->student->uae_student == 1 && $studentTermSept->student->gender == 'girl') {
                            if ($progressRate == 1) {
                                $uae_female_above_expected++;
                            } elseif ($progressRate == 0) {
                                $uae_female_same_expected++;
                            } else {
                                $uae_female_below_expected++;
                            }
                        }


                    }


                }
                $total = $above_expected + $same_expected + $below_expected;
                $mayProgressData = [
                    'name' => $rounds[2],
                    'above' => $above_expected,
                    'above_ratio' => $above_expected > 0 && $total > 0 ? round(($above_expected / $total), 4) * 100 : 0,
                    'inline' => $same_expected,
                    'inline_ratio' => $same_expected > 0 && $total > 0 ? round(($same_expected / $total), 4) * 100 : 0,
                    'below' => $below_expected,
                    'below_ratio' => $below_expected > 0 && $total > 0 ? round(($below_expected / $total), 4) * 100 : 0,
                    'total' => $total,
                ];

                $male_total = $male_above_expected + $male_same_expected + $male_below_expected;
                $mayMaleProgressData = [
                    'name' => $rounds[2],
                    'above' => $male_above_expected,
                    'above_ratio' => $male_above_expected > 0 && $male_total > 0 ? round(($male_above_expected / $male_total), 4) * 100 : 0,
                    'inline' => $male_same_expected,
                    'inline_ratio' => $male_same_expected > 0 && $male_total > 0 ? round(($male_same_expected / $male_total), 4) * 100 : 0,
                    'below' => $male_below_expected,
                    'below_ratio' => $male_below_expected > 0 && $male_total > 0 ? round(($male_below_expected / $male_total), 4) * 100 : 0,
                    'total' => $male_total,
                ];

                $female_total = $female_above_expected + $female_same_expected + $female_below_expected;
                $mayFemaleProgressData = [
                    'name' => $rounds[2],
                    'above' => $female_above_expected,
                    'above_ratio' => $female_above_expected > 0 && $female_total > 0 ? round(($female_above_expected / $female_total), 4) * 100 : 0,
                    'inline' => $female_same_expected,
                    'inline_ratio' => $female_same_expected > 0 && $female_total > 0 ? round(($female_same_expected / $female_total), 4) * 100 : 0,
                    'below' => $female_below_expected,
                    'below_ratio' => $female_below_expected > 0 && $female_total > 0 ? round(($female_below_expected / $female_total), 4) * 100 : 0,
                    'total' => $female_total,
                ];

                $sen_total = $sen_above_expected + $sen_same_expected + $sen_below_expected;
                $maySenProgressData = [
                    'name' => $rounds[2],
                    'above' => $sen_above_expected,
                    'above_ratio' => $sen_above_expected > 0 && $sen_total > 0 ? round(($sen_above_expected / $sen_total), 4) * 100 : 0,
                    'inline' => $sen_same_expected,
                    'inline_ratio' => $sen_same_expected > 0 && $sen_total > 0 ? round(($sen_same_expected / $sen_total), 4) * 100 : 0,
                    'below' => $sen_below_expected,
                    'below_ratio' => $sen_below_expected > 0 && $sen_total > 0 ? round(($sen_below_expected / $sen_total), 4) * 100 : 0,
                    'total' => $sen_total,
                ];

                $total = $uae_above_expected + $uae_same_expected + $uae_below_expected;
                $mayUaeProgressData = [
                    'name' => $rounds[2],
                    'above' => $uae_above_expected,
                    'above_ratio' => $uae_above_expected > 0 && $total > 0 ? round(($uae_above_expected / $total), 4) * 100 : 0,
                    'inline' => $uae_same_expected,
                    'inline_ratio' => $uae_same_expected > 0 && $total > 0 ? round(($uae_same_expected / $total), 4) * 100 : 0,
                    'below' => $uae_below_expected,
                    'below_ratio' => $uae_below_expected > 0 && $total > 0 ? round(($uae_below_expected / $total), 4) * 100 : 0,
                    'total' => $total,
                ];

                $total = $uae_male_above_expected + $uae_male_same_expected + $uae_male_below_expected;
                $mayUaeMaleProgressData = [
                    'name' => $rounds[2],
                    'above' => $uae_male_above_expected,
                    'above_ratio' => $uae_male_above_expected > 0 && $total > 0 ? round(($uae_male_above_expected / $total), 4) * 100 : 0,
                    'inline' => $uae_male_same_expected,
                    'inline_ratio' => $uae_male_same_expected > 0 && $total > 0 ? round(($uae_male_same_expected / $total), 4) * 100 : 0,
                    'below' => $uae_male_below_expected,
                    'below_ratio' => $uae_male_below_expected > 0 && $total > 0 ? round(($uae_male_below_expected / $total), 4) * 100 : 0,
                    'total' => $total,
                ];

                $total = $uae_female_above_expected + $uae_female_same_expected + $uae_female_below_expected;
                $mayUaeFemaleProgressData = [
                    'name' => $rounds[2],
                    'above' => $uae_female_above_expected,
                    'above_ratio' => $uae_female_above_expected > 0 && $total > 0 ? round(($uae_female_above_expected / $total), 4) * 100 : 0,
                    'inline' => $uae_female_same_expected,
                    'inline_ratio' => $uae_female_same_expected > 0 && $total > 0 ? round(($uae_female_same_expected / $total), 4) * 100 : 0,
                    'below' => $uae_female_below_expected,
                    'below_ratio' => $uae_female_below_expected > 0 && $total > 0 ? round(($uae_female_below_expected / $total), 4) * 100 : 0,
                    'total' => $total,
                ];
            } else {
                $mayProgressData = [
                    'name' => $rounds[2],
                    'above_ratio' => 0,
                    'above' => 0,
                    'inline_ratio' => 0,
                    'inline' => 0,
                    'below_ratio' => 0,
                    'below' => 0,
                    'total' => 0,
                ];
                $mayProgressData = [
                    'name' => $rounds[2],
                    'above_ratio' => 0,
                    'above' => 0,
                    'inline_ratio' => 0,
                    'inline' => 0,
                    'below_ratio' => 0,
                    'below' => 0,
                    'total' => 0,
                ];
                $mayMaleProgressData = [
                    'name' => $rounds[2],
                    'above_ratio' => 0,
                    'above' => 0,
                    'inline_ratio' => 0,
                    'inline' => 0,
                    'below_ratio' => 0,
                    'below' => 0,
                    'total' => 0,
                ];
                $mayFemaleProgressData = [
                    'name' => $rounds[2],
                    'above_ratio' => 0,
                    'above' => 0,
                    'inline_ratio' => 0,
                    'inline' => 0,
                    'below_ratio' => 0,
                    'below' => 0,
                    'total' => 0,
                ];
                $maySenProgressData = [
                    'name' => $rounds[2],
                    'above_ratio' => 0,
                    'above' => 0,
                    'inline_ratio' => 0,
                    'inline' => 0,
                    'below_ratio' => 0,
                    'below' => 0,
                    'total' => 0,
                ];
                $mayUaeProgressData = [
                    'name' => $rounds[2],
                    'above_ratio' => 0,
                    'above' => 0,
                    'inline_ratio' => 0,
                    'inline' => 0,
                    'below_ratio' => 0,
                    'below' => 0,
                    'total' => 0,
                ];
                $mayUaeMaleProgressData = [
                    'name' => $rounds[2],
                    'above_ratio' => 0,
                    'above' => 0,
                    'inline_ratio' => 0,
                    'inline' => 0,
                    'below_ratio' => 0,
                    'below' => 0,
                    'total' => 0,
                ];
                $mayUaeFemaleProgressData = [
                    'name' => $rounds[2],
                    'above_ratio' => 0,
                    'above' => 0,
                    'inline_ratio' => 0,
                    'inline' => 0,
                    'below_ratio' => 0,
                    'below' => 0,
                    'total' => 0,
                ];
            }


            if ($student_terms->count()) {
                $arab_pages[$grade] = (object)[
                    'title' => "Grade $grade $sub_title Progress - $year->name",
                    'septProgress' => $septProgressData,
                    'febProgressData' => $febProgressData,
                    'mayProgressData' => $mayProgressData,
                    'summary_title' => "Summary Grade $grade $sub_title Progress - $year->name",
                    'maleProgressData' => (object)[
                        $rounds[0] => $septMaleProgressData,
                        $rounds[1] => $febMaleProgressData,
                        $rounds[2] => $mayMaleProgressData,
                    ],
                    'femaleProgressData' => (object)[
                        $rounds[0] => $septFemaleProgressData,
                        $rounds[1] => $febFemaleProgressData,
                        $rounds[2] => $mayFemaleProgressData,
                    ],
                    'senProgressData' => (object)[
                        $rounds[0] => $septSenProgressData,
                        $rounds[1] => $febSenProgressData,
                        $rounds[2] => $maySenProgressData,
                    ],
                    'uaeProgressData' => (object)[
                        $rounds[0] => $septUaeProgressData,
                        $rounds[1] => $febUaeProgressData,
                        $rounds[2] => $mayUaeProgressData,
                    ],
                    'uaeMaleProgressData' => (object)[
                        $rounds[0] => $septUaeMaleProgressData,
                        $rounds[1] => $febUaeMaleProgressData,
                        $rounds[2] => $mayUaeMaleProgressData,
                    ],
                    'uaeFemaleProgressData' => (object)[
                        $rounds[0] => $septUaeFemaleProgressData,
                        $rounds[1] => $febUaeFemaleProgressData,
                        $rounds[2] => $mayUaeFemaleProgressData,
                    ],
                ];
            }


        }

        $report_type = 'progress';
        return view('general.reports.progress.school_progress_report', compact('arab_pages', 'school','type', 'sub_title', 'year', 'rounds', 'steps', 'report_type'));

    }
}
