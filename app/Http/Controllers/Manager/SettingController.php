<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\LoginSession;
use App\Models\MarkingRequest;
use App\Models\School;
use App\Models\Setting;
use App\Models\Student;
use App\Models\StudentTerm;
use Carbon\Carbon;
use Illuminate\Contracts\Cache\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Excel;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:show settings')->only('settings');
        $this->middleware('permission:edit settings')->only(['updateSettings']);
    }
    public function home()
    {
        $title = t('Dashboard');
        if (Auth::guard('manager')->user()->approved && Auth::guard('manager')->user()->hasDirectPermission('show statistics')){
            $data['schools_count'] = School::query()->count();
            $data['schools_unapproved_count'] = School::query()->where('active',0)->count();
            $data['students_count'] = Student::query()->count();
//            $data['levels_count'] = Level::query()->count();
            $data['marking_requests'] = MarkingRequest::query()->count();
            $data['new_marking_requests'] = MarkingRequest::query()->where('status','pending')->count();
            $data['student_assessments'] = StudentTerm::query()->count();
            $data['corrected_assessments'] = StudentTerm::query()->where('corrected', 1)->count();
            $data['uncorrected_assessments'] = StudentTerm::query()->where('corrected', 0)->count();
            $students_terms_data = StudentTerm::query()->groupBy('date')->orderBy('date')
                ->whereBetween('created_at',[now()->startOfDay(), now()->endOfDay()])
                ->get(array(
                    DB::raw('DATE_FORMAT(created_at, "%H:00") as date'),
                    DB::raw('COUNT(*) as counts')
                ));
            $term_data = ['categories' => $students_terms_data->pluck('date'), 'data' => $students_terms_data->pluck('counts')];

            $students_login_data = LoginSession::query()->where('model_type', Student::class)->groupBy('date')->orderBy('date')
                ->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])
                ->get(array(
                    DB::raw('DATE_FORMAT(created_at, "%h:00 %p") as date'),
                    DB::raw('COUNT(*) as counts')
                ));
            $login_data = ['categories' => $students_login_data->pluck('date'), 'data' => $students_login_data->pluck('counts'), 'total' => "(".t('Total') .' : '.$students_login_data->sum('counts').")"];


            return view('manager.home', compact('title', 'data', 'login_data', 'term_data'));
        }else{
            return view('manager.home', compact('title'));

        }

    }

    public function settings()
    {
        $title = t('Show Settings');
        $settings = Setting::query()->get();
        return view('manager.settings.general', compact('settings', 'title'));
    }

    public function updateSettings(Request $request, Factory $cache)
    {
        $settings_data = $request->validate([
            'settings' => 'required|array',
        ]);

        foreach ($settings_data['settings'] as $key => $val) {
            $setting = Setting::query()->where('key', $key)->first();
            if ($setting) {
                if ($setting->type == 'file' && $request->hasFile('settings.'.$key)) {
                    $up_file = uploadFile($request->file('settings.'.$key), 'settings');
                    $file_path = $up_file['path'];
                    $setting->update([
                        'value' => $file_path,
                    ]);
                } else {
                    $setting->update([
                        'value' => $val,
                    ]);
                }
            }
        }
        // When the settings have been updated, clear the cache for the key 'settings'
        $cache->forget('settings');
        $settings = $cache->remember('settings', 60, function () {
            return Setting::query()->get();
        });
        return redirect()->back()->with('message', t('Successfully Updated'))->with('m-class', 'success');
    }

//    public function sendNotification(Request $request)
//    {
//        $request->validate([
//            'recipients' => 'required',
//            'title' => 'required',
//            'content' => 'required',
//            'user_id' => 'required_if:recipients,4',
//        ]);
//
//        if($request->get('recipients') == 1){
//
//            send_push_to_topic('users',['title' => $request->get('title'), 'body' => $request->get('content')]);
//        }elseif($request->get('recipients') == 2){
//
//            send_push_to_topic('patients',['title' => $request->get('title'), 'body' => $request->get('content')]);
//        }elseif($request->get('recipients') == 3){
//
//            send_push_to_topic('doctors',['title' => $request->get('title'), 'body' => $request->get('content')]);
//        }elseif($request->get('recipients') == 4){
//            $user = User::query()->find($request->get('user_id'));
//            if(!$user){
//                return redirect()->back()->withErrors(['message'=> t('User ID Dos\'t Exists')])->withInput();
//            }
//
//            send_push_to_topic('user_'.$user->id,['title' => $request->get('title'), 'body' => $request->get('content')]);
//        }
//
//        return redirect()->back()->with('message', t('Notification Successfully Send'))->with('m-class', 'success');
//    }

    public function lang($local)
    {
        session(['lang' => $local]);
        if(Auth::guard('manager')->check()){
            $user = Auth::guard('manager')->user();
            $user->update([
                'lang' => $local,
            ]);
        }
        app()->setLocale($local);
        return back();
    }



    public function studentLoginData(Request $request)
    {
        $request->validate([
            'start_date' => 'required',
            'end_date' => 'required',
        ]);
        if (Carbon::parse($request->start_date)->diffInMonths(Carbon::parse($request->end_date)) > 1)
        {
            $format = "%Y-%m";
        }elseif (Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date)) > 1)
        {
            $format = "%Y-%m-%d";
        }else{
            $format = "%H:00";
        }
        $students_login_data = LoginSession::query()->where('model_type', Student::class)->groupBy('date')->orderBy('date')
            ->whereBetween('created_at', [Carbon::parse($request->start_date)->startOfDay(), Carbon::parse($request->end_date)->endOfDay()])
            ->get(array(
                DB::raw('DATE_FORMAT(created_at, "'.$format.'") as date'),
                DB::raw('COUNT(*) as counts')
            ));
        $login_data = ['categories' => $students_login_data->pluck('date'), 'data' => $students_login_data->pluck('counts'), 'total' => "(".t('Total') .' : '.$students_login_data->sum('counts').")"];
        return $this->sendResponse($login_data, 'Successfully');
    }
    public function assessmentsData(Request $request)
    {
        $request->validate([
            'start_date' => 'required',
            'end_date' => 'required',
        ]);
        if (Carbon::parse($request->start_date)->diffInMonths(Carbon::parse($request->end_date)) > 1)
        {
            $format = "%Y-%m";
        }elseif (Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date)) > 1)
        {
            $format = "%Y-%m-%d";
        }else{
            $format = "%H:00";
        }
        $students_terms_data = StudentTerm::query()->groupBy('date')->orderBy('date')
            ->whereBetween('created_at',[Carbon::parse($request->start_date)->startOfDay(), Carbon::parse($request->end_date)->endOfDay()])
            ->get(array(
                DB::raw('DATE_FORMAT(created_at, "'.$format.'") as date'),
                DB::raw('COUNT(*) as counts')
            ));
        $term_data = ['categories' => $students_terms_data->pluck('date'), 'data' => $students_terms_data->pluck('counts')];
        return $this->sendResponse($term_data, 'Successfully');
    }

}
