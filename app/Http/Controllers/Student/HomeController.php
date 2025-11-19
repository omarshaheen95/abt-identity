<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\SchoolGrade;
use App\Models\Student;
use App\Models\StudentTerm;
use App\Models\Term;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $student = Auth::guard('student')->user();

        $completed_terms = [];
        $available_rounds = [];
        $available_terms = [];

        //get completed terms for student
        $completed_terms = StudentTerm::query()->with(['term'])->where('student_id', $student->id)->get();

        if ($student->demo) {
            $available_terms = Term::query()
                ->whereHas('level', function ($query) use ($student) {
                    $query->whereIn('id', $student->demo_data->levels);
                })
                ->whereIn('round', $student->demo_data->rounds)
                ->get();
        } else {

            //get completed terms for student
            $completed_terms = StudentTerm::query()->with(['term'])->where('student_id', $student->id)->get();

            if (in_array($student->school->curriculum_type, ['Indian', 'Pakistan', 'Bangladeshi'])) {
                //get grade to check available round
                $grade = SchoolGrade::query()
                    ->where('grade', $student->level->grade)
                    ->where('arab', $student->level->arab)
                    ->where('school_id', $student->school_id)
                    ->whereHas('school', function ($query) use ($student) {
                        $query->where('available_year_id', $student->school->available_year_id);
                    })->first();
            } else {
                $grade = SchoolGrade::query()
                    ->where('grade', $student->level->grade)
                    ->where('arab', $student->level->arab)
                    ->where('school_id', $student->school_id)
                    ->whereHas('school', function ($query) use ($student) {
                        $query->where('available_year_id', $student->level->year_id);
                    })->first();
            }
            //get grade to check available round
//            $grade =  SchoolGrade::with('school')
//                ->where('grade',$student->level->grade)
//                ->where('arab',$student->level->arab)
//                ->where('school_id',$student->school_id)
//                ->whereHas('school',function ($query) use ($student){
//                    $query->where('available_year_id',$student->level->year_id);
//                })->first();

            if ($grade) {
                //set active round in array
                if ($grade['september']) {
                    $available_rounds[] = 'september';
                }
                if ($grade['february']) {
                    $available_rounds[] = 'february';
                }
                if ($grade['may']) {
                    $available_rounds[] = 'may';
                }

                if (in_array($student->school->curriculum_type, ['Indian', 'Pakistan', 'Bangladeshi'])) {
                    //get terms
                    $available_terms = Term::query()
                        ->whereNotIn('id', $completed_terms->pluck('term_id'))
                        ->whereIn('round', $available_rounds)
                        ->whereHas('level', function (Builder $query) use ($student) {
                            $query->where('year_id', $student->school->available_year_id);
                            $query->where('grade', $student->level->grade);
                            $query->where('arab', $student->level->arab);
                        })
                        ->where('active', 1)
                        ->get();
                } else {
                    //get terms
                    $available_terms = Term::query()
                        ->whereNotIn('id', $completed_terms->pluck('term_id'))
                        ->whereIn('round', $available_rounds)
                        ->where('level_id', $student->level_id)
                        ->where('year_id', $student->level->year_id)
                        ->where('active', 1)
                        ->get();
                }
            }

        }


        return view('student.home', compact('completed_terms', 'available_terms', 'student'));
    }

}
