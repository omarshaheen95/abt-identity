<?php

namespace App\Http\Controllers\Manager;

use App\Exports\SchoolExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\SchoolRequest;
use App\Models\School;
use App\Models\SchoolGrade;
use App\Models\User;
use App\Models\Year;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class SchoolController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:edit schools')->only('forcePasswordChange');
        $this->middleware('permission:show schools')->only('index');
        $this->middleware('permission:add schools')->only(['create','store']);
        $this->middleware('permission:edit schools')->only(['edit','update']);
        $this->middleware('permission:delete schools')->only('deleteSchool');
        $this->middleware('permission:export schools')->only('schoolExport');
        $this->middleware('permission:school login')->only('schoolLogin');
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $rows = School::query()->search($request)->latest();
            return DataTables::make($rows)
                ->escapeColumns([])
                ->addColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('last_login', function ($row) {
                    return $row->last_login ? Carbon::parse($row->last_login)->toDateTimeString() : '';
                })
                ->addColumn('logo', function ($row) {
                    return is_null($row->logo) ? '-':'<img src="'.$row->logo.'" width="50" />';
                })
                ->addColumn('active', function ($row) {
                    return $row->active ? t('Active') : t('Inactive');
                })
                ->addColumn('actions', function ($row) {
                    return $row->action_buttons;
                })
                ->make();
        }
        $title = t('Schools');
        return view('manager.school.index', compact('title'));
    }

    public function create()
    {
        $title = t('Create School');
        $years = Year::get();
        return view('manager.school.edit', compact('title','years'));
    }

    public function store(SchoolRequest $request)
    {
        $data = $request->validated();
        $data['active'] = $request->get('active', false) ? 1 : 0;
        $data['student_login'] = $request->get('student_login', false) ? 1 : 0;

        if ($request->hasFile('logo')) {
            $logo = uploadFile($request->file('logo'), 'schools');
            $data['logo'] = $logo['path'];
        }
        $data['password'] = bcrypt($request->get('password'));
        $data['force_password_change'] = $request->get('force_password_change', 0);
        $data['password_changed_at'] = now();
        $data['proctoring_settings'] = School::prepareProctoringSettings($request);
        School::query()->create($data);

        return redirect()->route('manager.school.index')->with('message', t('Successfully Created'));
    }

    public function edit($id)
    {
        $title = t('Edit School');
        $school = School::query()->findOrFail($id);
        $years = Year::get();
        return view('manager.school.edit', compact('title', 'school','years'));
    }

    public function update(SchoolRequest $request, $id)
    {
        $school = School::query()->findOrFail($id);
        $data = $request->validated();
        $data['active'] = $request->get('active', false) ? 1 : 0;
        $data['student_login'] = $request->get('student_login', false) ? 1 : 0;
        if ($request->hasFile('logo')) {
            $logo = uploadFile($request->file('logo'), 'schools');
            $data['logo'] = $logo['path'];
        }
        $password_changed = (bool) $request->get('password', false);
        $data['password'] = $password_changed ? bcrypt($request->get('password')) : $school->password;
        if ($password_changed) {
            $data['password_changed_at'] = now();
        }
        // a password handed over by a manager is temporary by default
        $data['force_password_change'] = $request->get('force_password_change', $password_changed ? 1 : 0);
        $data['proctoring_settings'] = School::prepareProctoringSettings($request);
        $school->update($data);
        return redirect()->route('manager.school.index')->with('message', t('Successfully Updated'));
    }

    public function deleteSchool(Request $request)
    {
        $schools_ids = $request->get('row_id');
        $schools = School::query()->whereIn('id', $schools_ids)->get();
        foreach ($schools as $school)
        {
            if ($school->students_count > 0) {
                return $this->sendError(t('Can not delete school because there are students in it please delete students first').'(' .$school->name.')');
            }else{
                $school->delete();
            }
        }
        return $this->sendResponse(null, t('Successfully Deleted'));
    }


    public function schoolLogin($id)
    {
        $user = School::query()->findOrFail($id);
        Auth::guard('school')->loginUsingId($id);
        \App\Http\Middleware\ForcePasswordChange::impersonate('school', $id);
        return redirect()->route('school.home');
    }

    public function schoolExport(Request $request)
    {
        return (new SchoolExport($request))
            ->download('Schools Information.xlsx');
    }

    public function bulkUpdateProctoringSettings(Request $request)
    {
        $request->validate([
            'row_id' => 'nullable|array',
            'countries' => 'nullable|array',
            'proctoring_settings' => 'nullable|array',
        ]);
        $proctoringSettings = School::prepareProctoringSettings($request);
        $query = School::query();
        $rowIds = array_filter((array) $request->get('row_id', []));
        $countries = array_filter(array_map('strtolower', (array) $request->get('countries', [])));
        if (!empty($rowIds)) {
            $query->whereIn('id', $rowIds);
        } elseif (!empty($countries)) {
            $query->whereIn(\DB::raw('LOWER(country)'), $countries);
        }
        $count = $query->update(['proctoring_settings' => json_encode($proctoringSettings)]);
        return $this->sendResponse(null, t('Proctoring settings updated for') . ' ' . $count . ' ' . t('schools'));
    }

    /**
     * Raise the forced password change flag on the accounts the table is
     * currently showing. Same contract as the export: the filters come from the
     * #filter form and row_id narrows it down to the checked rows.
     */
    public function forcePasswordChange(Request $request)
    {
        $query = School::query()->search($request);

        // count the matched rows, not the changed ones: MySQL does not report a
        // row that already carried the flag
        $affected = (clone $query)->count();
        $query->update(['force_password_change' => 1]);

        return $this->sendResponse(['affected' => $affected],
            t('Password change was enforced on :count account(s).', ['count' => $affected]));
    }
}
