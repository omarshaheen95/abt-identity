<?php

namespace App\Http\Controllers\Inspection;

use App\Exports\StudentExport;
use App\Exports\StudentMarksExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\School\StudentRequest;
use App\Models\Inspection;
use App\Models\InspectionSchool;
use App\Models\Level;
use App\Models\School;
use App\Models\Student;
use App\Models\Year;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $schools = Inspection::getInspectionSchools();

        $schools_ids =collect($schools)->pluck('id');

        if ($request->ajax()) {
            $rows = Student::with(['level','school','year'])->whereIn('school_id',$schools_ids)->search($request)->latest();
            return DataTables::make($rows)
                ->escapeColumns([])
                ->addColumn('level', function ($row) {
                    return $row->level->name;
                })
                ->addColumn('email', function ($row) {
                    return '<span class="cursor-pointer" data-clipboard-text="'.$row->email.'" onclick="copyToClipboard(this)">' . $row->email . '</span>';
                })
                ->addColumn('school', function ($row) {
                    return '<a href="'.route('inspection.school-login',$row->school->id).'">' . $row->school->name . '</a>';
                })
                ->addColumn('year', function ($row) {
                    return $row->year->name;
                })
                ->addColumn('actions', function ($row) {
                    return $row->action_buttons;
                })
                ->make();
        }

        $years = Year::query()->get();

        $title = t('Students');

        return view('inspection.student.index', compact('title','years','schools'));
    }


    public function studentExport(Request $request)
    {
        $schools_ids = collect(Inspection::getInspectionSchools())->pluck('id')->toArray();
        return (new StudentExport($request,$schools_ids))->download('School Students Information.xlsx');
    }
    public function studentMarksExport(Request $request)
    {
        $schools_ids = collect(Inspection::getInspectionSchools())->pluck('id')->toArray();
        return (new StudentMarksExport($request,$schools_ids))->download('Students Marks.xlsx');
    }

    public function studentsCards(Request $request){
        $schools_ids = collect(Inspection::getInspectionSchools())->pluck('id')->toArray();
        $students = Student::query()->whereIn('school_id',$schools_ids)->search($request)->get();
        $with_QR = isset($request['card-qr']);
        $students = collect($students)->chunk(8);
        $student_login_url = config('app.url').'/student/login';
        $title ='Students Cards';
        return view('general.cards_and_qr',compact('students','student_login_url','with_QR','title'));
    }
    public function studentLogin($id)
    {
        Auth::guard('student')->loginUsingId($id);
        return redirect()->route('student.home');
    }


    public function getSections(Request $request)
    {
        $sections = Student::query()
            ->when($value = $request->get('year_id'), function (Builder $query) use ($value) {
                $query->whereHas('level', function ($q) use ($value) {
                    $q->where('year_id', $value);
                });
            })
            ->when($value = $request->get('school_id'), function (Builder $query) use ($value) {
                $query->whereIn('school_id', $value);
            })->when(!$request->get('school_id'), function (Builder $query){
                $schools = Auth::guard('inspection')->user()->schools->pluck('id');
                $query->whereIn('school_id', $schools);
            })->whereNotNull('grade_name')
            ->select('grade_name')
            ->orderBy('grade_name')->get()
            ->pluck('grade_name')
            ->unique()
            ->values();
        $html = '';
        foreach ($sections as $section) {
            $html .= '<option value="' . $section . '">' . $section . '</option>';
        }
        return response()->json(['html' => $html]);
    }

}
