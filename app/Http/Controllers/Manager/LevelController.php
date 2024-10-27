<?php

namespace App\Http\Controllers\Manager;

use App\Exports\LevelExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\LevelRequest;
use App\Models\Level;
use App\Models\School;
use App\Models\Student;
use App\Models\Year;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class LevelController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:show levels')->only('index');
        $this->middleware('permission:add levels')->only(['create','store','addGeneralLevels']);
        $this->middleware('permission:edit levels')->only(['edit','update']);
        $this->middleware('permission:delete levels')->only('deleteLevel');
        $this->middleware('permission:levels activation')->only('activation');
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $rows = Level::query()->with(['year'])->search($request)->latest();
            return DataTables::make($rows)
                ->escapeColumns([])
                ->addColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('active', function ($row) {
                    return $row->active ? t('Active') : t('Inactive');
                })
                ->addColumn('year', function ($row) {
                    return $row->year->name;
                })
                ->addColumn('actions', function ($row) {
                    return $row->action_buttons;
                })
                ->make();
        }
        $title = t('Levels');
        $years = Year::query()->get();
        return view('manager.level.index', compact('title', 'years'));
    }

    public function create()
    {
        $title = t('Create Level');
        $years = Year::query()->get();
        $grades = grades();
        return view('manager.level.edit', compact('title', 'years', 'grades'));
    }

    public function store(LevelRequest $request)
    {
        $data = $request->validated();
        $data['active'] = $request->get('active', false) ? 1 : 0;
        $data['arab'] = $request->get('section', 1) != 2 ? 1 : 0;
        Level::query()->create($data);
        return redirect()->route('manager.level.index')->with('message', t('Successfully Created'));
    }

    public function edit($id)
    {
        $title = t('Edit Level');
        $level = Level::query()->findOrFail($id);
        $years = Year::query()->get();
        $grades = grades();
        return view('manager.level.edit', compact('title', 'level', 'years', 'grades'));
    }

    public function update(LevelRequest $request, $id)
    {
        $level = Level::query()->findOrFail($id);
        $data = $request->validated();
        $data['active'] = $request->get('active', false) ? 1 : 0;
        $data['arab'] = $request->get('section', 1) != 2 ? 1 : 0;
        $level->update($data);
        return redirect()->route('manager.level.index')->with('message', t('Successfully Updated'));
    }

    public function deleteLevel(Request $request)
    {
//        Level::destroy( $request->get('row_id'));
        $level = Level::query()->withCount('terms')->whereIn('id',$request->get('row_id'))->get();
        foreach ($level as $level)
        {
            if ($level->terms_count > 0)
            {
                return $this->sendError(t('Level '.$level->name.' containing terms cannot be deleted'));
            }else{
                Student::query()->where('level_id', $level->id)->update([
                    'level_id' => null,
                ]);
                $level->delete();
            }
        }
        return $this->sendResponse(null, t('Successfully Deleted'));
    }

//    public function getLevelsByYear(Request $request)
//    {
//        $year = $request->get('year_id', false);
//        $levels = Level::query()->where('year_id', $year)->get();
//        $selected = $request->get('selected', 0);
//        $html = ' <option></option>';
//        foreach ($levels as $level) {
//            $html .= '<option value="'.$level->id.'">'.$level->name.'</option>';
//        }
//        return $this->sendResponse($html);
//    }

    public function levelGrades(Request $request)
    {
        $levels = Level::query()->with(['year'])->where('year_id', $request->get('id'))->get();
        $html = ''  . !$request->get('multipleOptions', false) ? '<option></option>' : '';
        foreach ($levels as $level ) {
            $html .= '<option value="'.$level->id.'">'.$level->name.'</option>';
        }
        return response()->json(['html'=>$html]);
    }
    public function export(Request $request)
    {
        return (new LevelExport($request))->download('Levels Details.xlsx');
    }

    public function addGeneralLevels(Request $request)
    {
        $request->validate(['year_id' => 'required']);
        $year_id = $request->get('year_id');

        $levels = Level::query()
            ->where('year_id', $year_id)
            ->get();

        if (count($levels) > 0) {
            return $this->sendError('Levels already exist for this year');
        }

        $grades = [1 => 'One', 2 => 'Tow', 3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve'];

        $year = Year::query()->findOrFail($year_id);

        $years = explode('/', $year->name);
        $first_year = $years[0];
        $second_year = $years[1];

        $levels_grade = [];

        foreach ($grades as $grade => $name) {
            $levels_grade [] = [
                'name' => json_encode(
                    [
                        'ar' => "Grade $grade - Sep $first_year to May $second_year",
                        'en' => "Grade $grade - Sep $first_year to May $second_year",
                    ]
                ),
                'year_id' => $year_id,
                'grade' => $grade,
                'active' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        Level::insert($levels_grade);

        return $this->sendResponse(null, t('Successfully Created'));

    }

    public function activation(Request $request)
    {
        $request->validate(['year_id'=>'required','grades'=>'required|array','active'=>'required|in:1,0']);
        Level::query()
            ->where('year_id', $request->get('year_id'))
            ->whereIn('grade', $request->get('grades'))
            ->update(['active' => $request->get('active')]);
        return $this->sendResponse(null,t('Levels Status Updated Successfully to').': '.($request->get('active')?t('Active'):t('Non-Active')));
    }

}
