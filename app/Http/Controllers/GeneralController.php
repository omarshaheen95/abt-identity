<?php

namespace App\Http\Controllers;

use App\Helpers\Response;
use App\Models\Level;
use App\Models\StudentTerm;
use App\Models\Term;
use App\Reports\StudentReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GeneralController extends Controller
{
    public function levelsByYear(Request $request,$id)
    {
        $rows = Level::query()->where('year_id',$id)->get();
        if ((bool)request()->get('multiple', false)) {
            $html = '';
        } else {
            $html = '<option><option/>';
        }
        foreach ($rows as $row) {
            $html .= '<option value="' . $row->id . '">' . $row->name . '</option>';
        }
        return Response::respondSuccess(Response::SUCCESS, $html);
    }
    public function termsByLevel(Request $request,$id)
    {
        $rows = Term::query()->where('level_id',$id)->get();
        if ((bool)request()->get('multiple', false)) {
            $html = '';
        } else {
            $html = '<option><option/>';
        }
        foreach ($rows as $row) {
            $html .= '<option value="' . $row->id . '">' . $row->name . '</option>';
        }
        return Response::respondSuccess(Response::SUCCESS, $html);
    }

    public function studentReport($id)
    {
        $report = new StudentReport($id);
        return $report->report();
    }

    function certificate(Request $request,$id)
    {
        $student_term = StudentTerm::with('student.level')
            ->where('id',$id)
            ->where('total','>=',90)
            ->search($request)->first();
        if ($student_term){
            $name = $student_term->student->name;
            $grade = $student_term->student->level->grade;
            $mark = $student_term->total;
            return view('general.certificate.certificate',compact('name','grade','mark'));
        }else{
            return redirect()->route(getGuard().'.student_term.index')
                ->with('message',t('A certificate cannot be issued for this student'))
                ->with('m-class','error');
        }

    }
}
