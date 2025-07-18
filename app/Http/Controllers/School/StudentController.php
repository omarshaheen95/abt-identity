<?php

namespace App\Http\Controllers\School;

use App\Exports\StudentExport;
use App\Exports\StudentMarksExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\School\StudentRequest;
use App\Models\Level;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentTerm;
use App\Models\Year;
use App\Reports\StudentReport;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $school_id =Auth::guard('school')->user()->id;
        $request['school_id'] = $school_id;
        if ($request->ajax()) {
            $rows = Student::with(['level','school','year'])
                ->withCount(['student_terms'])->where('school_id',$school_id)->search($request)->latest();
            return DataTables::make($rows)
                ->escapeColumns([])
                ->addColumn('created_at', function ($student) {
                    return Carbon::parse($student->created_at)->toDateString();
                })
                ->addColumn('name', function ($student) {
                    return '<div class="d-flex flex-column"><span>'.$student->name.'</span><span class="text-danger cursor-pointer" data-clipboard-text="'.$student->email.'" onclick="copyToClipboard(this)">' . $student->email . '</span></div>';
                })
                ->addColumn('sid', function ($student) {
                    return '<div class="d-flex flex-column align-items-center"><span class="cursor-pointer" data-clipboard-text="'.$student->id_number.'" onclick="copyToClipboard(this)">' . $student->id_number . '</span><span class="badge badge-primary text-center">'.$student->student_terms_count.'</span></div>';
                })
                ->addColumn('year', function ($student) {
                    return $student->year->name;
                })
                ->addColumn('level', function ($student) {
                    $year_name = $student->level->year->name;
                    $grade = $student->level->grade;
                    $arab = $student->level->arab ? 'Arab' : 'NonArab';
                    return $year_name . ' - ' . 'Grade -' . $grade . ' - ' . $arab;
                })
                ->addColumn('actions', function ($row) {
                    return $row->action_buttons;
                })
                ->make();
        }
        $years = Year::query()->get();

        $title = t('Students');

        return view('school.student.index', compact('title','years'));
    }

    public function create()
    {
        $title = t('Create Student');
        $years = Year::query()->get();
        return view('school.student.edit', compact('title','years'));
    }

    public function store(StudentRequest $request)
    {
        $data = $request->validated();
        $data['password'] = bcrypt($request->get('password'));
        $data['school_id'] = Auth::guard('school')->user()->id;
        Student::query()->create($data);
        return redirect()->route('school.student.index')->with('message', t('Successfully Created'));
    }

    public function edit($id)
    {
        $title = t('Edit Student');
        $student = Student::query()->where('school_id',Auth::guard('school')->user()->id)->findOrFail($id);
        $years = Year::query()->get();
        $levels = Level::query()->get();
        return view('school.student.edit', compact('title', 'student','years','levels'));
    }

    public function update(StudentRequest $request, $id)
    {
        $student = Student::query()->where('school_id',Auth::guard('school')->user()->id)->findOrFail($id);
        $data = $request->validated();
        $data['password'] = $request->get('password', false) ? bcrypt($request->get('password', 123456)) : $student->password;
        $student->update($data);
        return redirect()->route('school.student.index')->with('message', t('Successfully Updated'));
    }

    public function delete(Request $request)
    {
        $request->validate(['row_id'=>'required']);
        Student::query()
            ->where('school_id',Auth::guard('school')->user()->id)
            ->whereIn('id', $request->get('row_id'))
            ->each(function ($student) {
                $student->delete();
            });
        return $this->sendResponse(null, t('Successfully Deleted'));
    }

    public function studentExport(Request $request)
    {
        $request['school_id'] = Auth::guard('school')->user()->id;
        return (new StudentExport($request))->download('School Students Information.xlsx');
    }
    public function studentMarksExport(Request $request)
    {
        $request['school_id'] = Auth::guard('school')->user()->id;
        return (new StudentMarksExport($request))->download('Students Marks.xlsx');
    }

    public function studentsCards(Request $request){
        $school_id = Auth::guard('school')->check() ? Auth::guard('school')->user()->id:$request->get('school_id');
        if(!$school_id){
            throw new \Exception('School id not found');
        }
        $students = Student::query()->where('school_id',$school_id)->search($request)->get();
        $qr = isset($request['qr-code']);
        $students = $students->chunk(6);
        $school = School::query()->where('id',$school_id)->first();
        $student_login_url = config('app.url').'/student/login';
        $title = $school->name.' | Students Card';
        return view('general.cards_and_qr',compact('students','student_login_url','school','qr','title'));
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
        if (Auth::guard('school')->user()->student_login){
            Auth::guard('student')->loginUsingId($id);
            return redirect()->route('student.home');
        }
        return redirect()->route('school.student.index')->with('message',t('You not have permission to student login'));

    }
    public function getSectionsByYear(Request $request)
    {
        $year = $request->get('year_id', false);
        $school = Auth::guard('school')->user()->id;
        $sections = Student::query()
//            ->when($year,function (Builder $query) use ($year){
//                $query->whereHas('level', function ($q) use ($year){
//                    $q->where('year_id',$year);
//                });
//            })
            ->where('year_id', $year)
            ->where('school_id',$school)
            ->whereNotNull('grade_name')
            ->select('grade_name')
            ->orderBy('grade_name')->get()
            ->pluck('grade_name')
            ->unique()
            ->values();
        $html = '';
        foreach ($sections as $section) {
            $html .= '<option value="' . $section . '">' . $section . '</option>';
        }
        return $this->sendResponse($html, t('Successfully Deleted'));
    }

    public function studentCardBySections(Request $request)
    {
        $request->validate([
            'year_id' => 'required|exists:years,id',
        ]);
        $request['school_id'] = Auth::guard('school')->id();
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
    public function pdfReportsCards(Request $request)
    {
        $request->validate([
            'year_id' => 'required',
            'level_id' => 'required|array|min:1|max:1',
        ], [
            'year_id.required' => 'The year field is required.',
            'level_id.required' => 'The level field is required.',
            'level_id.min' => 'The level must be at least 1.',
            'level_id.max' => 'The level may not be greater than 1.',
        ]);

        $school_id = Auth::guard('school')->user()->id;

        $students = Student::with(['level.year','year'])
            ->where('school_id', $school_id)->search($request)
            ->select(['id', 'name as student_name', 'id_number as std_id'])->get()->values()->toArray();
//        dd($students);

        if (count($students) == 0) {
            return $this->sendError(t('No students found'), 404);
        }

        $client = new \GuzzleHttp\Client([
            'timeout'  => 36000,
        ]);

        $data = ['report_type' => 1, 'report_card' => true];
        $res = $client->request('POST', 'https://pdfservice.arabic-uae.com/getpdf.php', [
            'form_params' => [
                'platform' => 'abt-identity',
                'studentid' => $students,
                'data' => $data,
            ],
        ]);
        $data = json_decode($res->getBody());
        $url = $data->url;
        $fileContent = file_get_contents($url);
        if ($fileContent === false) {
            throw new \Exception('Unable to download file');
        }else{
            return response($fileContent, 200, [
                'Content-Type' => 'application/zip',
                'Content-Disposition' => 'inline; filename="reports.zip"'
            ]);
        }
        return redirect($data->url);
    }

    public function studentReport($id)
    {
        $report = new StudentReport($id, Auth::guard('school')->user()->id);
        return $report->report();
    }
    public function studentReportCard($id)
    {
        $report = new StudentReport($id, Auth::guard('school')->user()->id);
        return $report->studentReportCard();
    }

    public function pdfReports(Request $request)
    {
        $request->validate([
            'school_id' => 'required',
            'grade' => 'required|max:1|min:1',
        ],[
            'grade.max' => t('Must be select one grade'),
            'grade.min' => t('Must be select one grade'),
        ]);

        $school_id = Auth::guard('school')->user()->id;

        $students = Student::with(['level.year','year'])
            ->where('school_id', $school_id)
            ->search($request)
            ->select(['id', 'name as student_name', 'id_number as std_id'])
            ->get()->values()->toArray();

        $client = new \GuzzleHttp\Client([
            'timeout'  => 36000,
        ]);

        $res = $client->request('POST', 'https://pdfservice.arabic-uae.com/getpdf.php', [
            'form_params' => [
                'platform' => 'abt-identity',
                'studentid' => $students,
            ],
        ]);
        $data = json_decode($res->getBody());
        $url = $data->url;
        $fileContent = file_get_contents($url);
        if ($fileContent === false) {
            throw new \Exception('Unable to download file');
        }else{
            return response($fileContent, 200, [
                'Content-Type' => 'application/zip',
                'Content-Disposition' => 'inline; filename="reports.zip"'
            ]);
        }
        return redirect($data->url);
    }

    function webCertificate($id)
    {
        $lang = \request()->get('language', 'en');
        app()->setLocale($lang);
        $student_term = StudentTerm::with('student.level')
            ->where('id',$id)
            ->whereHas('student.school', function ($query) {
                $query->whereRaw('student_terms.total >= schools.certificate_mark');
            })
            ->firstOrFail();
        $name = $student_term->student->name;
        $grade = $student_term->student->level->grade;
        $mark = $student_term->total;
        return view('general.certificate.web_certificate',compact('name','grade','mark'));
    }

}
