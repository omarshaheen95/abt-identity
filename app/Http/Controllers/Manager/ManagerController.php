<?php

namespace App\Http\Controllers\Manager;

use App\Exports\ManagerExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\ManagerPasswordRequest;
use App\Http\Requests\Manager\ManagerProfileRequest;
use App\Http\Requests\Manager\ManagerRequest;
use App\Models\Manager;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\DataTables;

class ManagerController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:show managers')->only('index');
        $this->middleware('permission:add managers')->only(['create','store']);
        $this->middleware('permission:edit managers')->only(['edit','update']);
        $this->middleware('permission:export managers')->only('export');
        $this->middleware('permission:delete managers')->only('deleteManager');
        $this->middleware('permission:edit managers permissions')->only(['editPermissions','updatePermissions']);
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $rows = Manager::query()->search($request)->latest();
            return DataTables::make($rows)
                ->escapeColumns([])
                ->addColumn('email', function ($row) {
                    return '<span class="cursor-pointer" data-clipboard-text="'.$row->email.'" onclick="copyToClipboard(this)">' . $row->email . '</span>';
                })
                ->addColumn('last_login', function ($row) {
                    return $row->last_login ? Carbon::parse($row->last_login)->toDateTimeString() : '';
                })
                ->addColumn('approved', function ($row) {
                    return $row->approved ? t('Approved') : t('Not Approved');
                })
                ->addColumn('actions', function ($row) {
                    return $row->action_buttons;
                })
                ->make();
        }
        $title = t('Managers');
        return view('manager.manager.index', compact('title', ));
    }

    public function create()
    {
        $title = t('Create Manager');
        return view('manager.manager.edit', compact('title'));
    }

    public function store(ManagerRequest $request)
    {
        $data = $request->validated();
        $data['password'] = bcrypt($request->get('password'));
        $data['approved'] = $request->get('approved', 0);
        Manager::query()->create($data);
        return redirect()->route('manager.manager.index')->with('message', t('Successfully Created'));
    }

    public function edit($id)
    {
        $title = t('Edit Manager');
        $manager = Manager::query()->findOrFail($id);
        return view('manager.manager.edit', compact('title', 'manager'));
    }

    public function update(ManagerRequest $request, $id)
    {
        $manager = Manager::query()->findOrFail($id);
        $data = $request->validated();
        $data['approved'] = $request->get('approved', 0);
        $data['password'] = $request->get('password', false) ? bcrypt($request->get('password', 123456)) : $manager->password;
        $manager->update($data);
        return redirect()->route('manager.manager.index')->with('message', t('Successfully Updated'));
    }

    public function editPermissions($id)
    {
        $title = t('Edit Permissions');
        $manager_id = $id;
        $permissions = Permission::query()->where('guard_name','manager')
            ->get()->groupBy('group');
        $manager_permissions = \DB::table('model_has_permissions')->where('model_id',$id)->get();

        return view('manager.manager.permissions', compact('title', 'permissions','manager_permissions','manager_id'));
    }

    public function updatePermissions(Request $request)
    {
        $request->validate(['manager_id'=>'required','permissions'=>'nullable']);

        $manager = Manager::query()->findOrFail($request->get('manager_id'));
        $manager->syncPermissions($request->get('permissions'));

        return redirect()->route('manager.manager.index')->with('message', t('Successfully Updated'));
    }
    public function deleteManager(Request $request)
    {
        $managers = Manager::query()->whereIn('id', $request->get('row_id'))->get();
        foreach ($managers as $manager) {
            $manager->delete();
        }
        return $this->sendResponse(null, t('Successfully Deleted'));
    }
    public function export(Request $request)
    {
        return (new ManagerExport($request))->download('Managers Details.xlsx');
    }
    public function viewUpdateProfile()
    {
        $title = t('Update Profile');
        return view('manager.manager.profile', compact('title'));
    }
    public function updateProfile(ManagerProfileRequest $request)
    {
        $data = $request->validated();
        $manager = Auth::guard('manager')->user();
        $manager->update($data);
        return redirect()->back()->with('message', t('Successfully Updated'));
    }
    public function viewUpdatePassword()
    {
        $title = t('Update Password');
        return view('manager.manager.password', compact('title'));
    }
    public function updatePassword(ManagerPasswordRequest $request)
    {
        $data = $request->validated();
        $manager = Auth::guard('manager')->user();
        if (Hash::check($request->get('old_password'), $manager->password)) {
            $data['password'] = bcrypt($request->get('password'));
            $manager->update($data);
            return redirect()->back()->with('message', t('Successfully Updated'))->with('m-class', 'success');
        } else {
            return redirect()->back()->withErrors([t('Current Password Invalid')])->with('message', t('Current Password Invalid'))->with('m-class', 'error');
        }
    }
}
