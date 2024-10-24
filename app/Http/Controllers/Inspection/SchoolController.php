<?php

namespace App\Http\Controllers\Inspection;

use App\Exports\SchoolExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\SchoolRequest;
use App\Models\Inspection;
use App\Models\InspectionSchool;
use App\Models\School;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class SchoolController extends Controller
{
    public function index(Request $request)
    {
        $schools_ids =collect(Inspection::getInspectionSchools())->pluck('id')->toArray();
        if ($request->ajax()) {
            $rows = School::query()->whereIn('id',$schools_ids)->search($request)->latest();
            return DataTables::make($rows)
                ->escapeColumns([])
                ->addColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('email', function ($row) {
                    return '<span class="cursor-pointer" data-clipboard-text="'.$row->email.'" onclick="copyToClipboard(this)">' . $row->email . '</span>';
                })
                ->addColumn('last_login', function ($row) {
                    return $row->last_login ? Carbon::parse($row->last_login)->toDateTimeString() : '';
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
        return view('inspection.school.index', compact('title'));
    }

    public function schoolLogin($id)
    {
        Auth::guard('school')->loginUsingId($id);
        return redirect()->route('school.home');
    }

    public function schoolExport(Request $request)
    {
        $schools_ids =collect(Inspection::getInspectionSchools())->pluck('id')->toArray();
        $builder = School::query()->whereIn('id',$schools_ids)->search($request)->latest();
        return (new SchoolExport($request,$builder))
            ->download('Schools Information.xlsx');
    }
}
