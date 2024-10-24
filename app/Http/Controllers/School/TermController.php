<?php

namespace App\Http\Controllers\School;

use App\Exports\StudentNotSubmittedTermExport;
use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentTerm;
use App\Models\Year;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class TermController extends Controller
{
    public function index(Request $request,string $type)
    {
        if ($type == 'corrected') {
            $request['corrected'] = 1;
            $title = t('Corrected Student Assessments');
        }else if ($type=='uncorrected'){
            $request['corrected'] = 2;
            $title = t('Uncorrected Student Assessments');

        }else{
            return redirect()->route('school.home');
        }
        if ($request->ajax()) {
            $request['school_id'] = \Auth::guard('school')->user()->id;
            $rows = StudentTerm::with(['student','term'])->search($request)->latest();
            return DataTables::make($rows)
                ->escapeColumns([])
                ->addColumn('term', function ($row) {
                    return $row->term->name;
                })
                ->addColumn('year', function ($row) {
                    return $row->student->year->name;
                })->addColumn('round', function ($row) {
                    return $row->term->round;
                })
                ->addColumn('status',function ($row){
                    if ($row->corrected == 0 && $row->end_at){
                        return '<a><span class="badge badge-warning">'.t('Uncorrected').'</span></a>';
                    } if ($row->corrected == 0 && !$row->end_at){
                        return '<a><span class="badge badge-danger">'.t('Uncompleted').'</span></a>';
                    }
                        return '<a><span class="badge badge-success">'.t('Corrected').'</span></a>';
                })

                ->make();
        }
        $years = Year::query()->get();
        return view('school.terms.index', compact('title','years','type'));
    }
    public function studentsNotSubmittedTerms(Request $request){
        // $request->validate(['row_id'=>'required|array']);

        if (request()->ajax()) {
            $students = Student::with(['school', 'level', 'level.year'])
                ->search($request)
                ->where('school_id',Auth::guard('school')->user()->id)
                ->whereDoesntHave('student_terms',function (Builder $query) use ($request){
                    $query->when($value = $request->get('round'),function (Builder $query)use ($value){
                        $query->whereHas('term',function (Builder $query) use ($value){
                            $query->where('round',$value);
                        });
                    });
                })->latest();

            return \Yajra\DataTables\DataTables::make($students)
                ->escapeColumns([])
                ->addColumn('name', function ($student) {
                    return '<div class="d-flex flex-column"><span>'.$student->name.'</span><span class="text-danger cursor-pointer" data-clipboard-text="'.$student->email.'" onclick="copyToClipboard(this)">' . $student->email . '</span></div>';
                })
                ->addColumn('sid', function ($student) {
                    return '<div class="d-flex flex-column align-items-center"><span class="cursor-pointer" data-clipboard-text="'.$student->id.'" onclick="copyToClipboard(this)">' . $student->id . '</span></div>';
                })
                ->addColumn('level', function ($student) {
                    if (!is_null($student->level))
                    {
                        $year_name = $student->level->year->name;
                        $grade = $student->grade;
                        $arab = $student->arab?'Arab':'NonArab';

                        return $year_name.' - '.'Grade '.$grade.' - '.$arab;
                    }else{
                        return "<span class='text-danger'>".t('not assigned to a level')."</span>";
                    }

                })->make();
        }
        $years = Year::query()->get();
        return view('school.terms.students_not_submitted_term',compact('years'));

    }

    public function studentsNotSubmittedTermsExport(Request $request)
    {
        return (new StudentNotSubmittedTermExport($request,Auth::guard('school')->user()->id))->download('Students Information.xlsx');
    }
}
