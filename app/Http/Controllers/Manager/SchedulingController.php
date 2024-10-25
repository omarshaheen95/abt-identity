<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolGrade;
use App\Models\Year;
use Illuminate\Http\Request;

class SchedulingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:school terms scheduling')->only(['index','update']);
        $this->middleware('permission:schools general scheduling')->only(['updateSchoolsGrades']);

    }

    public function index($id)
    {
        $grades = SchoolGrade::query()->where('school_id',$id)->get();
        $school = School::query()->where('id',$id)->first();
        $years = Year::query()->get();
        $title = t('Assessments Scheduling');
        return view('manager.school.scheduling',compact('grades','school','years','title'));
    }

    public function update(Request $request,$id)
    {
        $request->validate(['year_id'=>'required']);
        School::query()->where('id',$id)->update(['available_year_id'=>$request['year_id']]);

        if ($request->get('grades') !== null) {
            foreach ($request['grades'] as $grade){
                SchoolGrade::query()
                    ->where('id',$grade['id'])
                    ->where('school_id',$id)
                    ->update([
                        'september'=>isset($grade['september'])?1:0,
                        'february'=>isset($grade['february'])?1:0,
                        'may'=>isset($grade['may'])?1:0,
                    ]);
            }
        }
        return redirect()->back()->with('message',t('Assessments scheduling updated successfully '));

    }

    public function updateSchoolsGrades(Request $request)
    {
        $request->validate([
            'round' => 'required|in:september,february,may',
            'status' => 'required|in:1,2',
        ]);

        $round = $request->get('round', false);

        SchoolGrade::query()->update([
            "$round"  => $request->get('status') == 1 ? 1:0,
        ]);

        return redirect()->back()->with('message', t('Successfully Updated'))->with('m-class', 'success');

    }

}
