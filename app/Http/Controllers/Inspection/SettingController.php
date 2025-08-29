<?php

namespace App\Http\Controllers\Inspection;

use App\Http\Controllers\Controller;
use App\Models\Inspection;
use App\Models\Level;
use App\Models\School;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function home()
    {
        $title = t('Dashboard');

        $schools = Inspection::getInspectionSchools();
        $data['students_count'] = Student::query()->whereIn('school_id',$schools->pluck('id'))->latest()->count();
        $data['schools_count'] = $schools->count();

        return view('inspection.home', compact('title', 'data' ));
    }

    public function lang($local)
    {
        session(['lang' => $local]);
        if(Auth::guard('inspection')->check()){
            $user = Auth::guard('inspection')->user();
            $user->update([
                'lang' => $local,
            ]);
        }
        app()->setLocale($local);
        return back();
    }

    public function levelGrades(Request $request)
    {
        $levels = Level::query()->with(['year'])->where('year_id', $request->get('year_id'))->get();
        $html = '';
        foreach ($levels as $level ) {
            $name = $level->year->name.'- Grade '.$level->grade.'-'.($level->arab ? 'Arab':'Non-arabs');
            $html .= '<option value="'.$level->id.'">'.$name.'</option>';
        }
        return response()->json(['html'=>$html]);
    }
}
