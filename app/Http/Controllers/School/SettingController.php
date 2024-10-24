<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\LoginSession;
use App\Models\MarkingRequest;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentTerm;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public function home()
    {
        $title = t('Dashboard');
        $school = auth()->guard('school')->user();
        if ($school->active){
            $data['students_count'] = Student::query()->where('school_id', $school->id)->count();
            $data['student_assessments'] = StudentTerm::query()
                ->whereHas('student', function ($query) use ($school) {
                    $query->where('school_id', $school->id);
                })
                ->count();
            $data['corrected_assessments'] = StudentTerm::query()
                ->whereHas('student', function ($query) use ($school) {
                    $query->where('school_id', $school->id);
                })
                ->where('corrected', 1)->count();
            $data['uncorrected_assessments'] = StudentTerm::query()
                ->whereHas('student', function ($query) use ($school) {
                    $query->where('school_id', $school->id);
                })
                ->where('corrected', 0)->count();
            $students_terms_data = StudentTerm::query()
                ->whereHas('student', function ($query) use ($school) {
                    $query->where('school_id', $school->id);
                })
                ->groupBy('date')->orderBy('date')
                ->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])
                ->get(array(
                    DB::raw('DATE_FORMAT(created_at, "%H:00") as date'),
                    DB::raw('COUNT(*) as counts')
                ));
            $term_data = ['categories' => $students_terms_data->pluck('date'), 'data' => $students_terms_data->pluck('counts')];
            $data['new_marking_requests'] = MarkingRequest::query()->where('school_id', $school->id)->where('status', 'Pending')->count();
            $data['marking_requests'] = MarkingRequest::query()->where('school_id', $school->id)->count();

            $students_login_data = LoginSession::query()
            ->whereHasMorph('model', [Student::class], function ($query) use ($school) {
                $query->where('school_id', $school->id);
            })
            ->groupBy('date')->orderBy('date')
            ->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])
            ->get(array(
                DB::raw('DATE_FORMAT(created_at, "%h:00 %p") as date'),
                DB::raw('COUNT(*) as counts')
            ));
            $login_data = ['categories' => $students_login_data->pluck('date'), 'data' => $students_login_data->pluck('counts'), 'total' => "(" . t('Total') . ' : ' . $students_login_data->sum('counts') . ")"];

            return view('school.home', compact('data', 'login_data', 'term_data', 'title'));

        }else{
            return view('school.home', compact( 'title'));

        }
    }

    public function lang($local)
    {
        session(['lang' => $local]);
        if(Auth::guard('school')->check()){
            $user = Auth::guard('school')->user();
            School::query()->where('id',$user->id)->update([
                'lang' => $local,
            ]);
        }
        app()->setLocale($local);
        return back();
    }

    public function getLevelsByYear(Request $request)
    {
        $year = $request->get('year_id', false);
        $levels = Level::query()->where('year_id', $year)->get();
        $selected = $request->get('selected', 0);
        $html = ' <option></option>';
        foreach ($levels as $level) {
            $html .= '<option value="'.$level->id.'">'.$level->name.'</option>';
        }
        return $this->sendResponse($html);
    }

    public function studentLoginData(Request $request)
    {
        $request->validate([
            'start_date' => 'required',
            'end_date' => 'required',
        ]);
        $school = auth()->guard('school')->user();
        if (Carbon::parse($request->start_date)->diffInMonths(Carbon::parse($request->end_date)) > 1)
        {
            $format = "%Y-%m";
        }elseif (Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date)) > 1)
        {
            $format = "%Y-%m-%d";
        }else{
            $format = "%H:00";
        }
        $students_login_data = LoginSession::query()
            ->whereHasMorph('model', [Student::class], function ($query) use ($school) {
                $query->where('school_id', $school->id);
            })
            ->groupBy('date')->orderBy('date')
            ->whereBetween('created_at', [Carbon::parse($request->start_date)->startOfDay(), Carbon::parse($request->end_date)->endOfDay()])
            ->get(array(
                DB::raw('DATE_FORMAT(created_at, "'.$format.'") as date'),
                DB::raw('COUNT(*) as counts')
            ));
        $login_data = ['categories' => $students_login_data->pluck('date'), 'data' => $students_login_data->pluck('counts'), 'total' => "(" . t('Total') . ' : ' . $students_login_data->sum('counts') . ")"];
        return $this->sendResponse($login_data, 'Successfully');
    }
    public function assessmentsData(Request $request)
    {
        $request->validate([
            'start_date' => 'required',
            'end_date' => 'required',
        ]);
        $school = auth()->guard('school')->user();
        if (Carbon::parse($request->start_date)->diffInMonths(Carbon::parse($request->end_date)) > 1)
        {
            $format = "%Y-%m";
        }elseif (Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date)) > 1)
        {
            $format = "%Y-%m-%d";
        }else{
            $format = "%H:00";
        }
        $students_terms_data = StudentTerm::query()
            ->whereHas('student', function ($query) use ($school) {
                $query->where('school_id', $school->id);
            })
            ->groupBy('date')->orderBy('date')
            ->whereBetween('created_at',[Carbon::parse($request->start_date)->startOfDay(), Carbon::parse($request->end_date)->endOfDay()])
            ->get(array(
                DB::raw('DATE_FORMAT(created_at, "'.$format.'") as date'),
                DB::raw('COUNT(*) as counts')
            ));
        $term_data = [
            'categories' => $students_terms_data->pluck('date'),
            'data' => $students_terms_data->pluck('counts'),
        ];
        return $this->sendResponse($term_data, 'Successfully');
    }
}
