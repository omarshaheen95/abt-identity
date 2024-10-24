<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolGrade;
use App\Models\Year;
use Illuminate\Http\Request;

class SchedulingController extends Controller
{
    public function index()
    {
        $id = \Auth::guard('school')->user()->id;
        $grades = SchoolGrade::query()->where('school_id',$id)->get();
        $school = School::query()->where('id',$id)->first();
        $years = Year::query()->get();
        $title = t('Terms Scheduling');
        return view('school.terms.scheduling',compact('grades','school','years','title'));
    }

    public function update(Request $request)
    {
        $request->validate(['year_id'=>'required']);

        //dd($request->toArray());
        $id = \Auth::guard('school')->user()->id;

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
        return redirect()->back()->with('message',t('Terms scheduling updated successfully '));
    }

}