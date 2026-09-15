<?php

namespace App\Http\Controllers\Manager;

use App\Exports\InspectionExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\InspectionRequest;
use App\Models\Inspection;
use App\Models\InspectionSchool;
use App\Models\School;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class InspectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:edit inspections')->only('forcePasswordChange');
        $this->middleware('permission:show inspections')->only('index');
        $this->middleware('permission:add inspections')->only(['create','store']);
        $this->middleware('permission:edit inspections')->only(['edit','update']);
        $this->middleware('permission:delete inspections')->only('deleteInspection');
        $this->middleware('permission:export inspections')->only('inspectionExport');
        $this->middleware('permission:inspection login')->only('inspectionLogin');
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $rows = Inspection::query()->with(['inspection_schools.school'])->search($request)->latest();
            return DataTables::make($rows)
                ->escapeColumns([])
                ->addColumn('last_login', function ($row) {
                    return $row->last_login ? Carbon::parse($row->last_login)->toDateTimeString() : '';
                })
                ->addColumn('email', function ($row) {
                    return '<span class="cursor-pointer" data-clipboard-text="'.$row->email.'" onclick="copyToClipboard(this)">' . $row->email . '</span>';
                })
                ->addColumn('active', function ($row) {
                    return $row->active ? t('Active') : t('Inactive');
                })
                ->addColumn('image', function ($row) {
                    return $row->image?'<img style="width:50px;" src="'.asset($row->image).'"/>':null;
                })
                ->addColumn('school', function ($row) {
                    return collect($row->inspection_schools)->pluck('school.name')->implode(' , ');
                })
                ->addColumn('actions', function ($row) {
                   return $row->action_buttons;
                })
                ->make();
        }
        $title = t('Inspections');
        $schools = School::query()->active()->get();
        return view('manager.inspection.index', compact('title', 'schools'));
    }

    public function create()
    {
        $title = t('Create Inspection');
        $schools = School::query()->active()->get();
        return view('manager.inspection.edit', compact('title', 'schools'));
    }

    public function store(InspectionRequest $request)
    {
        $data = $request->validated();
        $data['active'] = $request->get('active', false) ? 1 : 0;
        $data['password'] = bcrypt($request->get('password'));
        $data['force_password_change'] = $request->get('force_password_change', 0);
        $data['password_changed_at'] = now();

        if ($request->hasFile('image')) {
            $image = uploadFile($request->file('image'), 'image');
            $data['image'] = $image['path'];
        }

        $inspection = Inspection::query()->create($data);

        foreach ($data['schools_ids'] as $school_id){
            InspectionSchool::query()->create([
                'inspection_id' => $inspection->id,
                'school_id' => $school_id
            ]);
        }
        return redirect()->route('manager.inspection.index')->with('message', t('Successfully Created'));
    }

    public function edit($id)
    {
        $title = t('Edit Inspection');
        $inspection = Inspection::query()->findOrFail($id);
        $schools = School::query()->get();
        $inspection_schools_ids= InspectionSchool::query()->where('inspection_id',$id)->get()->pluck('school_id')->toArray();
        return view('manager.inspection.edit', compact('title', 'inspection', 'schools','inspection_schools_ids'));
    }

    public function update(InspectionRequest $request, $id)
    {
        $inspection = Inspection::query()->findOrFail($id);
        $data = $request->validated();
        $data['active'] = $request->get('active', false) ? 1 : 0;
        $password_changed = (bool) $request->get('password', false);
        $data['password'] = $password_changed ? bcrypt($request->get('password')) : $inspection->password;
        if ($password_changed) {
            $data['password_changed_at'] = now();
        }
        // a password handed over by a manager is temporary by default
        $data['force_password_change'] = $request->get('force_password_change', $password_changed ? 1 : 0);

        if ($request->hasFile('image')) {
            $image = uploadFile($request->file('image'), 'image');
            $data['image'] = $image['path'];
        }
        $inspection->update($data);

        InspectionSchool::query()
            ->where('inspection_id',$inspection->id)
            ->whereNotIn('school_id',$data['schools_ids'])
            ->delete();

        foreach ($data['schools_ids'] as $school_id) {
            InspectionSchool::query()->updateOrCreate(
                [
                    'inspection_id' => $inspection->id,
                    'school_id' => $school_id
                ],
                [
                    'inspection_id' => $inspection->id,
                    'school_id' => $school_id
                ],
            );
        }

        return redirect()->route('manager.inspection.index')->with('message', t('Successfully Updated'));
    }

    public function deleteInspection(Request $request)
    {
        $request->validate(['row_id'=>'required|array']);
        Inspection::destroy($request->get('row_id'));
        return $this->sendResponse(null, t('Successfully Deleted'));
    }

    public function inspectionLogin($id)
    {
        Auth::guard('inspection')->loginUsingId($id);
        \App\Http\Middleware\ForcePasswordChange::impersonate('inspection', $id);
        return redirect()->route('inspection.home');
    }

    public function inspectionExport(Request $request)
    {
        return (new InspectionExport($request))->download('Inspections Information.xlsx');
    }

    /**
     * Raise the forced password change flag on the accounts the table is
     * currently showing. Same contract as the export: the filters come from the
     * #filter form and row_id narrows it down to the checked rows.
     */
    public function forcePasswordChange(Request $request)
    {
        $query = Inspection::query()->search($request);

        // count the matched rows, not the changed ones: MySQL does not report a
        // row that already carried the flag
        $affected = (clone $query)->count();
        $query->update(['force_password_change' => 1]);

        return $this->sendResponse(['affected' => $affected],
            t('Password change was enforced on :count account(s).', ['count' => $affected]));
    }
}
