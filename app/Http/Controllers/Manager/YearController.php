<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\YearRequest;
use App\Models\Year;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class YearController extends Controller
{
    public function __construct()
    {
//        $this->middleware('permission:show years')->only('index');
//        $this->middleware('permission:add years')->only(['create','store']);
//        $this->middleware('permission:edit years')->only(['edit','update']);
//        $this->middleware('permission:delete years')->only('deleteLevel');
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $rows = Year::query()->search($request)->latest();
            return DataTables::make($rows)
                ->escapeColumns([])
                ->addColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('actions', function ($row) {
                    return $row->action_buttons;
                })
                ->make();
        }
        $title = t('Years');
        return view('manager.year.index', compact('title'));
    }

    public function create()
    {
        $title = t('Create Year');
        return view('manager.year.edit', compact('title'));
    }

    public function store(YearRequest $request){
        $data = $request->validated();
        if ($request->get('default')){
            $data['default'] = 1;
        }
        $year = Year::query()->create($data);

        if ($request->get('default')){
            Year::query()->where('id','!=',$year->id)->update(['default'=>0]);
        }

        return redirect()->route('manager.year.index')->with('message', t('Successfully Created'));
    }

    public function edit($id)
    {
        $title = t('Edit Year');
        $year = Year::query()->findOrFail($id);
        return view('manager.year.edit', compact('title', 'year', ));
    }
    public function update(YearRequest $request,$id){
        $data = $request->validated();
        if ($request->get('default')){
            $data['default'] = 1;
        }
        Year::query()->where('id',$id)->update($data);
        if ($request->get('default')){
            Year::query()->where('id','!=',$id)->update(['default'=>0]);
        }
        return redirect()->route('manager.year.index')->with('message', t('Successfully Updated'));
    }


    public function deleteYear(Request $request)
    {
        Year::destroy( $request->get('row_id'));
        return $this->sendResponse(null, t('Successfully Deleted'));
    }
}
