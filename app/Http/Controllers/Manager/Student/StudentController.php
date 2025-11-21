<?php

namespace App\Http\Controllers\Manager\Student;

use App\Exports\StudentExport;
use App\Exports\StudentMarksExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\SchoolAbtGroupRequest;
use App\Http\Requests\Manager\StudentRequest;
use App\Models\ArticleQuestionResult;
use App\Models\FillBlankAnswer;
use App\Models\Level;
use App\Models\MatchQuestionResult;
use App\Models\OptionQuestionResult;
use App\Models\School;
use App\Models\SortQuestionResult;
use App\Models\Student;
use App\Models\StudentTerm;
use App\Models\StudentTermStandard;
use App\Models\TFQuestionResult;
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
    public function __construct()
    {
        $this->middleware('permission:show students')->only('index');
        $this->middleware('permission:add students')->only(['create', 'store']);
        $this->middleware('permission:edit students')->only(['edit', 'update', 'studentsTermsTime']);
        $this->middleware('permission:delete students')->only('delete');
        $this->middleware('permission:export students')->only('studentExport');
        $this->middleware('permission:export students marks')->only('studentMarksExport');
        $this->middleware('permission:export students cards')->only(['studentsCards', 'studentCard']);
        $this->middleware('permission:restore deleted students')->only('restoreStudent');
        $this->middleware('permission:student login')->only('studentLogin');
        $this->middleware('permission:restore deleted students')->only('restoreStudent');

        $this->middleware('permission:show abt grouping students')->only(['storeAbtSchoolGroup', 'createAbtSchoolGroup', 'abtStudents']);
        $this->middleware('permission:student link with abt id')->only(['studentLinkWithAbtId', 'studentUnlinkWithAbtId']);
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
                    return '<div class="d-flex flex-column"><span class="copy-txt cursor-pointer" data-id="n-txt-'.$student->id.'" data-txt="'.$student->name.'">'.$student->name.'</span><span id="e-txt-'.$student->id.'" class="text-danger cursor-pointer copy-txt" data-txt="'.$student->email.'">' . $student->email . '</span><span>SID: <span id="idn-'.$student->id_number.'" class="text-info fw-bold copy-txt cursor-pointer" data-txt="'.$student->id_number.'">'.$student->id_number.'</span></span></div>';
                })
                ->addColumn('school', function ($student) {
                    return "<a class='text-info' target='_blank' href='" . route('manager.school.edit', $student->school->id) . "'>" . $student->school->name . "</a>";
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
        if (!$data['demo']) {
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
        if ($student->demo && $student->demo_data) {
            $demo_levels = $levels->whereNotIn('id', $student->demo_data->levels)->where('year_id', $student->demo_data->year_id);
            $selected_demo_levels = $levels->whereIn('id', $student->demo_data->levels)->where('year_id', $student->demo_data->year_id);
            return view('manager.student.edit', compact('title', 'student', 'schools', 'years', 'levels', 'selected_demo_levels', 'demo_levels'));
        }
        return view('manager.student.edit', compact('title', 'student', 'schools', 'years', 'levels'));
    }

    public function update(StudentRequest $request, $id)
    {
        $student = Student::query()->findOrFail($id);
        $data = $request->validated();
        $data['password'] = $request->get('password', false) ? bcrypt($request->get('password', 123456)) : $student->password;
        if (!$data['demo']) {
            $data['demo_data'] = null;
        }
        $student->update($data);
        return redirect()->route('manager.student.index')->with('message', t('Successfully Updated'));
    }

    public function delete(Request $request)
    {
        $request->validate(['row_id' => 'required']);
        Student::query()->whereIn('id', $request->get('row_id'))->get()->each(function ($student) {
            $student->delete();
        });
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
        if (!$request->has('row_id')) {
            $request->validate([
                'school_id' => 'required',
                //'year_id' => 'required',
            ]);
        }

        $students = Student::query()->search($request)->get()->chunk(6);
        $student_login_url = config('app.url') . '/student/login';
        $school_id = $request->get('school_id') ? $request->get('school_id') : 0;
        $school = School::query()->find($school_id);
        $title = $school ? $school->name . ' | ' . t('Students Cards') : t('Students Cards');

        return view('general.cards_and_qr', compact('students', 'student_login_url', 'school', 'title'));
    }

    public function studentCard(Request $request, $id)
    {
        $students = Student::with('school')->search($request)->where('id', $id)->get();
        $school = $students->first()->school;
        $students = $students->chunk(6);
        $title = $school ? $school->name . ' | ' . t('Student Card') : t('Student Card');
        return view('general.cards_and_qr', compact('students', 'school', 'title'));

    }

    public function studentLogin($id)
    {
        Auth::guard('student')->loginUsingId($id);
        return redirect()->route('student.home');
    }

//    public function studentReport($id)
//    {
//        $report = new StudentReport($id);
//        return $report->report();
//    }

    public function getSectionsByYear(Request $request)
    {
        $year = $request->get('id') ?: $request->get('year_id') ?: false;
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

    public function studentsTermsTime(Request $request)
    {
        $request->validate([
            'row_id' => 'required|array|min:1',
        ], [
            'row_id.required' => t('Please Select at least one student'),
        ]);
        $students = Student::query()->search($request)->update([
            'assessment_opened' => 0,
        ]);
        return $this->sendResponse($request->all(), t('Assessments Updated Successfully for Students').' : ' . $students);
    }

    public function restoreStudent(Request $request)
    {
        $total_restored = 0;
        $total_skipped = 0;
        $id = $request->get('id');
        if (!$id) {
            $request->validate(['school_id' => 'required', 'year_id' => 'required']);
        } else {
            $request->validate(['id' => 'required']);
        }
        $students = Student::query()->search($request)->whereNotNull('deleted_at')->withTrashed()->get();

        foreach ($students as $student) {
            //check email if exist in other students
            $other_students = Student::query()->where('email', $student->email)->where('id', '!=', $student->id)->get();
            if ($other_students->count() > 0) {
                if (is_array($id) || ($request->get('school_id') && $request->get('year_id'))) {
                    $total_skipped++;
                    continue;
                }
                return $this->sendError(t('Cannot Restore Student Before Email Already Exist'), 402);
            }
            $student->restore();
            $total_restored++;
            //Restore Student Terms
            $student_terms = StudentTerm::query()->where('student_id', $student->id)->withTrashed()->get();

            foreach ($student_terms as $student_term) {

                $other_terms = StudentTerm::query()->where('student_id', $student->id)->where('term_id', $student_term->term_id)->get();

                //check if has the same term
                if ($other_terms->count() <= 0) {
                    $student_term->restore();
                    MatchQuestionResult::query()->where('student_term_id', $student_term->id)->restore();
                    TFQuestionResult::query()->where('student_term_id', $student_term->id)->restore();
                    SortQuestionResult::query()->where('student_term_id', $student_term->id)->restore();
                    FillBlankAnswer::query()->where('student_term_id', $student_term->id)->restore();
                    OptionQuestionResult::query()->where('student_term_id', $student_term->id)->restore();
                    ArticleQuestionResult::query()->where('student_term_id', $student_term->id)->restore();
                    StudentTermStandard::query()->where('student_term_id', $student_term->id)->restore();
                }

            }
        }
        return $this->sendResponse(null, t('Successfully Restored').' '.t('Total').':('.$total_restored.') '.t('Students').' '.t('Skipped').':('.$total_skipped.') ');
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
        $all_request_data = $request->all();
        //create query string from all request data except _token
        $query_string = http_build_query(array_filter($all_request_data, function ($key) {
            return $key !== '_token' && $key !== 'grade_name';
        }, ARRAY_FILTER_USE_KEY));
        $urls = [];
        foreach ($sections as $section) {
            $url = '/student-cards?' . $query_string . '&grade_name=' . $section;
            $urls[] = (object)[
                'section' => str_replace('/', '-', $section),
                'url' => $url,
            ];
        }
//        Log::alert($urls);
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
    }


}
