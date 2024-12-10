<?php

namespace App\Http\Controllers\Manager\Student;

use App\Exports\StudentExport;
use App\Exports\StudentMarksExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\StudentRequest;
use App\Models\Level;
use App\Models\School;
use App\Models\Student;
use App\Models\Year;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:show students')->only('index');
        $this->middleware('permission:add students')->only(['create', 'store']);
        $this->middleware('permission:edit students')->only(['edit', 'update']);
        $this->middleware('permission:delete students')->only('delete');
        $this->middleware('permission:export students')->only('studentExport');
        $this->middleware('permission:export students marks')->only('studentMarksExport');
        $this->middleware('permission:export students cards')->only(['studentsCards','studentCard']);
        $this->middleware('permission:restore deleted students')->only('restoreStudent');
        $this->middleware('permission:student login')->only('studentLogin');
        $this->middleware('permission:restore deleted students')->only('restoreStudent');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $rows = Student::with(['level', 'school', 'year'])
                ->withCount(['student_terms'])->search($request)->latest();
            return DataTables::make($rows)
                ->escapeColumns([])
                ->addColumn('level', function ($row) {
                    if (!is_null($row->level)) {
                        $year_name = $row->level->year->name;
                        $grade = $row->level->grade;
                        $arab = $row->level->arab ? 'Arab' : 'NonArab';

                        return $year_name . ' - ' . 'Grade -' . $grade . ' - ' . $arab;
                    } else {
                        return "<span class='text-danger'>" . t('not assigned to a level') . "</span>";
                    }
                })
                ->addColumn('name', function ($student) {
                    return '<div class="d-flex flex-column"><span>'.$student->name.'</span><span class="text-danger cursor-pointer" data-clipboard-text="'.$student->email.'" onclick="copyToClipboard(this)">' . $student->email . '</span></div>';
                })
                ->addColumn('sid', function ($student) {
                    return '<div class="d-flex flex-column align-items-center"><span class="cursor-pointer" data-clipboard-text="'.$student->id.'" onclick="copyToClipboard(this)">' . $student->id . '</span><span class="badge badge-primary text-center">'.$student->student_terms_count.'</span></div>';
                })
                ->addColumn('school', function ($student) {
                    return "<a class='text-info' target='_blank' href='" . route('manager.school.edit', $student->school->id) . "'>" . $student->school->name . "</a>". (is_null($student->id_number) ? '' : "<br><span class='text-danger cursor-pointer' data-clipboard-text=".$student->id_number." onclick='copyToClipboard(this)' >" . t('SID Num') .': '.$student->id_number. "</span> " ) ;
                })
                ->addColumn('year', function ($row) {
                    return $row->year->name;
                })
                ->addColumn('actions', function ($row) {
                    return $row->action_buttons;
                })
                ->make();
        }
        $schools = School::query()->active()->get();
//        $levels = Level::query()->get();
        $years = Year::query()->get();

        $title = t('Students');

        return view('manager.student.index', compact('title', 'schools', 'years'));
    }

    public function create()
    {
        $title = t('Create Student');
        $schools = School::query()->active()->get();
        $years = Year::query()->get();
        return view('manager.student.edit', compact('title', 'schools', 'years'));
    }

    public function store(StudentRequest $request)
    {
        $data = $request->validated();
        $data['password'] = bcrypt($request->get('password'));
        if (!$data['demo']){
            $data['demo_data'] = null;
        }
        Student::query()->create($data);
        return redirect()->route('manager.student.index')->with('message', t('Successfully Created'));
    }

    public function edit($id)
    {
        $title = t('Edit Student');
        $student = Student::query()->findOrFail($id);
        $schools = School::query()->active()->get();
        $years = Year::query()->get();
        $levels = Level::query()->get();
        if ($student->demo && $student->demo_data){
            $demo_levels = $levels->whereNotIn('id',$student->demo_data->levels)->where('year_id',$student->demo_data->year_id);
            $selected_demo_levels = $levels->whereIn('id',$student->demo_data->levels)->where('year_id',$student->demo_data->year_id);
            return view('manager.student.edit', compact('title', 'student', 'schools', 'years', 'levels', 'selected_demo_levels','demo_levels'));
        }
        return view('manager.student.edit', compact('title', 'student', 'schools', 'years', 'levels'));
    }

    public function update(StudentRequest $request, $id)
    {
        $student = Student::query()->findOrFail($id);
        $data = $request->validated();
        $data['password'] = $request->get('password', false) ? bcrypt($request->get('password', 123456)) : $student->password;
        if (!$data['demo']){
            $data['demo_data'] = null;
        }
        $student->update($data);
        return redirect()->route('manager.student.index')->with('message', t('Successfully Updated'));
    }

    public function delete(Request $request)
    {
        Student::destroy($request->get('row_id'));
        return $this->sendResponse(null, t('Successfully Deleted'));
    }

    public function studentExport(Request $request)
    {
        return (new StudentExport($request))->download('Students Information.xlsx');
    }

    public function studentMarksExport(Request $request)
    {
        return (new StudentMarksExport($request))->download('Students Marks.xlsx');
    }

    public function studentsCards(Request $request)
    {
        if (!$request->has('row_id'))
        {
            $request->validate([
                'school_id' => 'required',
                //'year_id' => 'required',
            ]);
        }

        $students = Student::query()->search($request)->get()->chunk(6);
        $student_login_url = config('app.url') . '/student/login';
        $school_id = $request->get('school_id') ? $request->get('school_id') :0;
        $school = School::query()->find($school_id);
        $title = $school ? $school->name . ' | ' . t('Students Cards') : t('Students Cards');

        return view('general.cards_and_qr', compact('students', 'student_login_url', 'school', 'title'));
    }
    public function studentCard(Request $request,$id)
    {
        $students = Student::with('school')->search($request)->where('id',$id)->get();
        $school = $students->first()->school;
        $students = $students->chunk(6);
        $title = $school ? $school->name . ' | ' . t('Student Card') : t('Student Card');
        return view('general.cards_and_qr', compact('students', 'school','title'));

    }

    public function studentLogin($id)
    {
        Auth::guard('student')->loginUsingId($id);
        return redirect()->route('student.home');
    }

    public function getSectionsByYear(Request $request)
    {
        $year = $request->get('id', false);
        $school = $request->get('school_id', false);
        $sections = Student::query()
            ->when($year, function (Builder $query) use ($year) {
                $query->whereHas('level', function ($q) use ($year) {
                    $q->where('year_id', $year);
                });
            })
            ->when($school, function (Builder $query) use ($school) {
                $query->where('school_id', $school);
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


    public function restoreStudent($id)
    {
        $student = Student::query()->where('id', $id)->withTrashed()->first();
        if ($student) {
            //check email if exist in other students
            $other_students = Student::query()->where('email', $student->email)->where('id', '!=', $student->id)->get();
            if ($other_students->count() > 0) {
                return $this->sendError(t('Cannot Restore Student Before Email Already Exist'), 402);
            }
            return $this->sendResponse(null, t('Successfully Restored'));
        }
        return $this->sendError(t('Student Not Restored'), 402);
    }

    public function studentCardBySections(Request $request)
    {
        $request->validate([
            'school_id' => 'required',
            'year_id' => 'required',
        ]);
        $request['school_id'] = $request->get('school_id');
        $students = Student::query()->with(['level', 'school'])->search($request)->get();
        $sections = $students->whereNotNull('grade_name')->pluck('grade_name')->unique();
        $students_type = $request->get('arab_status', false);
        $students_type_request = $students_type ? '&arab_status=' . $students_type : '';
        $urls = [];
        foreach ($sections as $section) {
            $url = '/student-cards?school_id=' . $request['school_id'] . '&year_id=' . $request['year_id'] . '&grade_name=' . $section.$students_type_request;
            $urls[] = (object)[
                'section' => str_replace('/', '-', $section),
                'url' => $url,
            ];
        }
        Log::alert($urls);
        $client = new \GuzzleHttp\Client([
            'timeout' => 36000,
        ]);

        $data = [];
        $res = $client->request('POST', 'https://pdfservice.arabic-uae.com/getpdf.php', [
            'form_params' => [
                'platform' => 'abt-identity',
                'urls' => $urls,
                'data' => $data,
            ],
        ]);
        $data = json_decode($res->getBody());
//        Log::error($res->getBody());
        $url = $data->url;
        $fileContent = file_get_contents($url);
        if ($fileContent === false) {
            throw new \Exception('Unable to download file');
        } else {
            return response($fileContent, 200, [
                'Content-Type' => 'application/zip',
                'Content-Disposition' => 'inline; filename="reports.zip"'
            ]);
        }
        return redirect($data->url);
    }

}
