<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Inspection;
use App\Models\Manager;
use App\Models\School;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:show activity logs')->only('index', 'show');
        $this->middleware('permission:delete activity logs')->only('destroy');
    }

    //function return Index Page
    public function index(Request $request)
    {
        $title = t('Activity Log');
        if ($request->ajax()) {
            $rows = Activity::query()->with(['causer'])->filter()->latest();
            return DataTables::make($rows)
                ->escapeColumns([])
                ->addColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->toDateTimeString();
                })
                ->addColumn('subject', function ($row) {
                    //get class name from model
                    if (!is_null($row->action_route)) {
                        return class_basename($row->subject_type) . '<br> <a href="' . $row->action_route . '">' . t('Show') . '</a>';
                    } else {
                        return class_basename($row->subject_type) . '<br> <a href="' . $row->action_route . '">' . t('Show') . '</a>';
                    }
                })
                ->addColumn('causer', function ($row) {
                    return optional($row->causer)->name ?? t('System');
                })
                ->addColumn('actions', function ($row) {
                    return view('general.action_menu')->with('actions', $row->action_buttons);
                })
                ->make();
        }
        $models = getAllModels();
        $causers = ['Manager' => Manager::class, 'School' => School::class, 'Inspection' => Inspection::class];
        return view('manager.activity-log.index', compact('title', 'models', 'causers'));
    }

    public function show($id)
    {
        $activity = Activity::query()->find($id);
        $log_obj = \GuzzleHttp\json_decode($activity->getAttribute('properties'));
        //dd($log_obj);
        $new = json_encode($log_obj->attributes ?? '{No Data}');
        $old = json_encode($log_obj->old ?? '{No Data}');
        return view('manager.activity-log.edit', compact('new', 'old', 'activity'));
    }

    public function destroy(Request $request)
    {
        $request->validate(['row_id' => 'required']);
        Activity::destroy($request->get('row_id'));
        return $this->sendResponse(null, t('Successfully Deleted'));
    }
}
