<?php

namespace App\Http\Controllers\Manager;

use App\Exports\StudentTermExport;
use App\Helpers\CorrectingStudentAssessment;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\UpgradeStudentTermRequest;
use App\Models\ArticleQuestionResult;
use App\Models\FillBlankAnswer;
use App\Models\MatchQuestionResult;
use App\Models\OptionQuestionResult;
use App\Models\Question;
use App\Models\QuestionStandard;
use App\Models\School;
use App\Models\SortQuestionResult;
use App\Models\Student;
use App\Models\StudentTerm;
use App\Models\StudentTermStandard;
use App\Models\Subject;
use App\Models\Term;
use App\Models\TFQuestionResult;
use App\Models\Year;
use App\Services\CorrectionService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

class StudentTermController extends Controller
{
    protected $correctionService;

    public function __construct(CorrectionService $correctionService)
    {
        $this->correctionService = $correctionService;
        $this->middleware('permission:show students terms')->only('index');
        $this->middleware('permission:show all students terms')->only('allTerms');
        $this->middleware('permission:edit students terms')->only(['edit','updateTerm']);
        $this->middleware('permission:restore deleted students terms')->only('restore');
        $this->middleware('permission:delete students terms')->only('deleteStudentTerm');
        $this->middleware('permission:restore deleted students terms')->only('restore');
        $this->middleware('permission:auto correct students terms')->only('autoCorrect');
        $this->middleware('permission:show upgrade terms')->only('upgradeStudentTermView');
        $this->middleware('permission:upgrade terms')->only('upgradeStudentTerm');
    }

    public function index(Request $request,$status)
    {
        if ($status == 'corrected') {
            $request['corrected'] = 1;
            $title = t('Corrected Student Assessments');
        }else if ($status =='uncorrected'){
            $request['corrected'] = 2;
            $title = t('Uncorrected Student Assessments');

        }else{
            return redirect()->route('school.home');
        }

        if ($request->ajax()) {
            $rows = StudentTerm::with(['student.school','term.level.year'])->search($request)->latest();
            return DataTables::make($rows)
                ->escapeColumns([])
                ->addColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->toDateTimeString();
                })
                ->addColumn('student_id', function ($row) {
                    return $row->student->id??'-';
                })
                ->addColumn('name', function ($row) {
                    $student = $row->student;
                    $html = '<div class="d-flex flex-column">';
                    $html .= '<div class="mb-1">'.$row->student->name??'-'.'</div>';
                    $html .= '<div><span id="e-txt-'.$student->id.'" class="text-danger cursor-pointer copy-txt" data-txt="'.$student->email.'">' . $student->email . '</span></div>';
                    $html .= '<div><span>SID: <span id="idn-'.$student->id_number.'" class="text-info fw-bold copy-txt cursor-pointer" data-txt="'.$student->id_number.'">'.$student->id_number.'</span></span></div>';
                    $html .= '<div class="mb-1 "><span class="badge badge-info">' . t('Grade') . '</span> <span>' . $row->term->level->grade . '</span></div>';
                    $html .= '<div class="mb-1"><span class="badge badge-info">' . t('Section') . '</span> <span>' . $row->student->grade_name . '</span></div></div>';
                    $html.='</div>';
                    return $html;
                })
                ->addColumn('email', function ($row) {
                    return $row->student->email??'-';
                })
                ->addColumn('school', function ($row) {
                    return $row->student->school->name??'-';
                })
                ->addColumn('year', function ($row) {
                    return $row->term->level->year->name??'-';
                })->addColumn('round', function ($row) {
                    return $row->term->round ?? '-';
                })
                ->addColumn('corrected',function ($row){
                    if ($row->corrected == 0){
                        return '<a><span class="badge badge-danger">'.t('Uncorrected').'</span></a>';
                    }
                        return '<a><span class="badge badge-success">'.t('Corrected').'</span></a>';
                })

                ->addColumn('actions', function ($row) {
                    return $row->action_data;
                })
                ->make();
        }
        $years = Year::query()->get();
        $schools = School::query()->active()->get();
        return view('manager.student_term.index', compact('title','years','schools'));
    }

    //correcting
    public function edit($id){
        $student_term = StudentTerm::with(['student','term','proctorImages'])->where('id',$id)->first();
        $student = $student_term->student;
        $questions = Question::with(
            ['tf_question','match_question','sort_question','option_question','fill_blank_question',
            'tf_question_result'=>function($query)use ($student,$student_term){
                $query->where('student_term_id','=',$student_term->id);
            },
            'match_question_result'=>function($query)use ($student,$student_term){
                $query->where('student_term_id','=',$student_term->id);
            },
            'option_question_result'=>function($query)use ($student,$student_term){
                $query->where('student_term_id','=',$student_term->id);
            },
            'sort_question_result'=>function($query)use ($student,$student_term){
                $query->where('student_term_id','=',$student_term->id);
            },
            'article_question_result'=>function($query)use ($student,$student_term){
                $query->where('student_term_id','=',$student_term->id);
            },
             'fill_blank_answer'=>function($query)use ($student,$student_term){
                $query->where('student_term_id','=',$student_term->id);
            },
        ])->where('term_id',$student_term->term_id)->get();

        foreach ($questions as $question){
            switch ($question['type']){
                case 'true_false':
                    $question['result'] = count($question->tf_question_result)>0? $question->tf_question_result[0]:null;
                    break;
                case 'multiple_choice':
                    $question['result'] =count($question->option_question_result)>0? $question->option_question_result[0]:null;
                    break;
                case 'matching':
                    $question['result'] = $question->match_question_result;
                    break;
                case 'sorting':
                    $question['result'] = $question->sort_question_result;
                    break;
                case 'fill_blank':
                    $question['result'] = count($question->fill_blank_answer)>0? $question->fill_blank_answer:null;
                    break;
                case 'article':
                    $question['result'] = count($question->article_question_result)>0? $question->article_question_result[0]:null;
                    break;
            }

        }

        $questions_count = count($questions);
        $subjects =Subject::all();
        $marks = 100;
        $correct_mode = true;

        $questions = $questions->groupBy('subject_id');
        $term = $student_term->term;


        return view('manager.student_term.term_correcting.index',compact('student','questions','term','student_term','questions_count','marks','subjects','correct_mode'));
    }

    //correcting
    public function updateTerm(Request $request,$id){
        $request->validate(['questions'=>'required|array']);
        //dd($request['questions']);
        $correcting = new CorrectingStudentAssessment();
        return $correcting->correcting($request, $id);
    }


    public function deleteStudentTerm(Request $request){
        $request->validate(['row_id'=>'required']);
        StudentTerm::query()->whereIn('id',$request->get('row_id'))->get()->each(function ($studentTerm){
            $studentTerm->delete();
        });
        return $this->sendResponse(null,t('Student term deleted successfully'));
    }


    public function studentsTermsExport(Request $request)
    {
        return (new StudentTermExport($request))->download('Students Terms Information.xlsx');
    }

    public function autoCorrect(Request $request){
        $request->validate(['year_id'=>'required']);
        //get student term doesnt have article question
        $students_terms = StudentTerm::query()->search($request)
            ->whereDoesntHave('term.question',function (Builder $query){
                $query->where('type','=','article');
            })->get();

        foreach ($students_terms as $student_term){
            $correctionService = new CorrectionService();
            $data = $correctionService->correctStudentTerm($student_term);
            $student_term->update($data);
        }
        return $this->sendResponse(null, t('Students Terms Corrected Successfully, Corrected Terms Number').' ('.$students_terms->count().')');

    }

    public function restore($id){
        $student_term = StudentTerm::query()->where('id',$id)->withTrashed()->first();

        $has_term = StudentTerm::query()
            ->where('term_id',$student_term->term_id)
            ->where('student_id',$student_term->student_id)
            ->get();

        if ($has_term->count()>0){
            return $this->sendError( t('Student has term and student term not restored'),402);
        }
        if ($student_term){
            $student_term->restore();
             //restore result
             TFQuestionResult::query()->where('student_term_id',$id)->withTrashed()->update(['deleted_at' => null]);
             OptionQuestionResult::query()->where('student_term_id',$id)->withTrashed()->update(['deleted_at' => null]);
             MatchQuestionResult::query()->where('student_term_id',$id)->withTrashed()->update(['deleted_at' => null]);
             SortQuestionResult::query()->where('student_term_id',$id)->withTrashed()->update(['deleted_at' => null]);
             ArticleQuestionResult::query()->where('student_term_id',$id)->withTrashed()->update(['deleted_at' => null]);
             FillBlankAnswer::query()->where('student_term_id',$id)->withTrashed()->update(['deleted_at' => null]);
             StudentTermStandard::query()->where('student_term_id',$id)->withTrashed()->update(['deleted_at' => null]);
            return $this->sendResponse(null, t('Successfully Restored'));
        }else{
            return $this->sendError( t('Student Term Not Restored'),402);
        }
    }

    public function deleteDuplicateStudentTerm(Request $request)
    {
        $request->validate([
            'year_id' => 'required|exists:years,id',
        ]);

        $student_terms = StudentTerm::query()
            ->search($request)
            ->get();

        foreach ($student_terms as $student_term){
            $has_term = StudentTerm::query()
                ->where('term_id',$student_term->term_id)
                ->where('student_id',$student_term->student_id)
                ->get();
            if ($has_term->count() > 1){
                $has_term->shift();
                foreach ($has_term as $term){
                    $term->delete();
                    //delete result
                    TFQuestionResult::query()->where('student_term_id',$term->id)->delete();
                    OptionQuestionResult::query()->where('student_term_id',$term->id)->delete();
                    MatchQuestionResult::query()->where('student_term_id',$term->id)->delete();
                    SortQuestionResult::query()->where('student_term_id',$term->id)->delete();
                    ArticleQuestionResult::query()->where('student_term_id',$term->id)->delete();
                    FillBlankAnswer::query()->where('student_term_id',$term->id)->delete();
                    StudentTermStandard::query()->where('student_term_id',$term->id)->delete();
                }
            }
        }
        return $this->sendResponse(null, t('Duplicate Student Terms Deleted Successfully'));
    }

    public function upgradeStudentTermView(Request $request)
    {
        $title = 'Upgrade Downgrade Student Term';
        $schools = School::query()->where('active', 1)->orderBy('name')->get();
        $years = Year::query()->orderBy('id')->get();
        $grades = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
        return view('manager.term.upgrade_student_terms', compact('schools', 'years', 'title', 'grades'));
    }

    public function upgradeStudentTerm(UpgradeStudentTermRequest $request)
    {
        $data = $request->validated();
        $students_terms = StudentTerm::query()
            ->with(['student.school', 'term'])
            ->whereHas('student', function (Builder $query) use ($data) {
                $query->where('school_id', $data['school_id']);
            })
            ->whereHas('term', function (Builder $query) use ($data) {
                $query->where('round', $data['month'])
                    ->whereHas('level', function (Builder $query) use ($data) {
                        $query->where('year_id', $data['year_id']);
                        $query->when($data['arab'] != 2, function ($query) use ($data) {
                            $query->where('arab', $data['arab']);
                        });
                        $query->whereIn('grade', $data['grades']);
                    });
            })
            ->where('total', '>=', $data['from_total_result'])
            ->where('total', '<=', $data['to_total_result'])
            ->when(isset($data['update_date']) && !is_null($data['update_date']), function ($query) use ($data) {
                $query->where('updated_at', $data['update_operator'], $data['update_date']);
            })
            ->where('corrected', 1)
            ->inRandomOrder()
//            ->limit(1)
            ->get();

        if (isset($request['check_counts']) && $request['check_counts'] == 1) {
            return $this->sendResponse('Students Count is ' . $students_terms->count(), t('Student Terms Upgraded Successfully'));
        }
        $mark = $data['mark'];
        $process_type = $data['process_type'];


        $students_terms->each(function ($student_term) use ($data, $mark, $process_type) {
            $questions = Question::query()->whereIn('type', [1, 2])->with([
                'tf_question', 'option_question',
                'tf_question_result' => function ($query) use ($student_term) {
                    $query->where('student_term_id', $student_term->id)
                        ->where('student_id', $student_term->student_id);
                },
                'option_question_result' => function ($query) use ($student_term) {
                    $query->where('student_term_id', $student_term->id)
                        ->where('student_id', $student_term->student_id);
                },
            ])->where('term_id', $student_term->term_id)->inRandomOrder()->get();

            $updated_marks = 0;
            foreach ($questions as $question) {
                $student_result = null;
                $main_result = null;

                if ($question->type == 'true_false') {
                    $student_result = $question->tf_question_result->first();
                    $main_result = $question->tf_question;

                    if ($process_type == 'upgrade') {
                        if (is_null($student_result) && isset($main_result)) {
                            // الطالب ليس لديه إجابة → إنشاء إجابة صحيحة
                            TFQuestionResult::create([
                                'student_id'     => $student_term->student_id,
                                'student_term_id' => $student_term->id,
                                'question_id'    => $question->id,
                                'result'         => $main_result->result,
                            ]);
                            $updated_marks += $question->mark;
                        } elseif (!is_null($student_result) && isset($main_result) && $student_result->result != $main_result->result) {
                            // الطالب لديه إجابة خاطئة → تصحيحها
                            $student_result->update(['result' => $main_result->result]);
                            $updated_marks += $question->mark;
                        }
                    } else {
                        if (!is_null($student_result) && isset($main_result) && $student_result->result == $main_result->result) {
                            $student_result->update([
                                'result' => $main_result->result == 1 ? 0 : 1,
                            ]);
                            $updated_marks += $question->mark;
                        }
                    }

                } elseif ($question->type == 'multiple_choice') {
                    $student_result = $question->option_question_result->first();

                    if ($student_result) {
                        $main_result = $question->option_question->where('id', $student_result->option_id)->first();
                    }

                    if ($process_type == 'upgrade') {
                        if (is_null($student_result)) {
                            // الطالب ليس لديه إجابة → إنشاء إجابة صحيحة
                            $correct_option = $question->option_question->where('result', 1)->first();
                            if ($correct_option) {
                                OptionQuestionResult::create([
                                    'student_id'     => $student_term->student_id,
                                    'student_term_id' => $student_term->id,
                                    'question_id'    => $question->id,
                                    'option_id'      => $correct_option->id,
                                ]);
                                $updated_marks += $question->mark;
                            }
                        } elseif ($student_result && isset($main_result->result) && $main_result->result != 1) {
                            // الطالب لديه إجابة خاطئة → تصحيحها
                            $correct_option = $question->option_question->where('result', 1)->first();
                            if ($correct_option) {
                                $student_result->update(['option_id' => $correct_option->id]);
                                $updated_marks += $question->mark;
                            }
                        }
                    } else {
                        if ($student_result && isset($main_result->result) && $main_result->result == 1) {
                            $wrong_option = $question->option_question->where('result', 0)->first();
                            if ($wrong_option) {
                                $student_result->update(['option_id' => $wrong_option->id]);
                                $updated_marks += $question->mark;
                            }
                        }
                    }
                }

                if ($updated_marks >= $mark) {
                    break;
                }
            }

            $pre_mark = $student_term->total;
            //correct exam
            $data = $this->correctionService->correctStudentTerm($student_term);
            $student_term->update($data);
            Log::alert('Student Term Updated Successfully for Student ID: ' . $student_term->student_id . ' | Old Mark: ' . $pre_mark . ' | New Mark: ' . $data['total']);
        });
        return $this->sendResponse(t('Student Terms Upgraded Successfully'), t('Student Terms Upgraded Successfully'));

    }

    /**
     * Corrected and uncorrected assessments on one page.
     *
     * The status tab travels as the existing `corrected` filter (1 corrected,
     * 2 uncorrected, empty both), which StudentTerm::search already understands,
     * so export / auto correct posted from this page follow the open tab.
     * Gated by its own `show all students terms` permission.
     */
    public function allTerms(Request $request)
    {
        if ($request->ajax()) {
            $draw = (int)$request->input('draw', 1);
            $start = max((int)$request->input('start', 0), 0);
            $length = (int)$request->input('length', 10);
            $corrected = $request->input('corrected');

            // Tab counters: the same filters without the status, in one aggregate
            $countRequest = clone $request;
            $countRequest->query->remove('corrected');
            $counts = StudentTerm::query()
                ->search($countRequest)
                ->reorder()
                ->toBase()
                ->select(DB::raw(
                    'COALESCE(SUM(student_terms.corrected = 1), 0) as corrected_count, ' .
                    'COALESCE(SUM(student_terms.corrected = 0), 0) as uncorrected_count'
                ))
                ->first();

            $correctedCount = (int)$counts->corrected_count;
            $uncorrectedCount = (int)$counts->uncorrected_count;
            $total = $corrected == 1 ? $correctedCount : ($corrected == 2 ? $uncorrectedCount : $correctedCount + $uncorrectedCount);

            $data = $total == 0 ? collect() : StudentTerm::query()
                ->with(['student.school', 'student.year', 'term.level.year'])
                ->search($request)
                // "Latest" by id: same order as created_at, read straight off the primary key
                ->when(in_array($request->get('orderBy', 'latest'), ['latest', ''], true), function ($query) {
                    $query->reorder()->orderByDesc('student_terms.id');
                })
                ->when($length > 0, function ($query) use ($start, $length) {
                    $query->offset($start)->limit($length);
                })
                ->get();

            $manager = Auth::guard('manager')->user();
            $permissions = [
                'correct' => $manager->can('edit students terms'),
                'correct_uncorrected' => $manager->hasDirectPermission('edit students terms'),
                'delete' => $manager->can('delete students terms'),
                'restore' => $manager->hasDirectPermission('restore deleted students terms'),
            ];

            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $total,
                'recordsFiltered' => $total,
                'data' => $data->map(function ($row) use ($permissions) {
                    return $this->allTermsRow($row, $permissions);
                })->values(),
                'counts' => [
                    'all' => $correctedCount + $uncorrectedCount,
                    'corrected' => $correctedCount,
                    'uncorrected' => $uncorrectedCount,
                ],
            ]);
        }

        $title = t('All Students Assessments');
        $years = Year::query()->get();
        $schools = School::query()->active()->get();
        $corrected = in_array($request->get('corrected'), ['1', '2']) ? $request->get('corrected') : '';

        return view('manager.student_term.all', compact('title', 'years', 'schools', 'corrected'));
    }

    private function allTermsRow(StudentTerm $row, array $permissions): array
    {
        $student = $row->student;
        $isCorrected = (bool)$row->corrected;

        $dates = '<div class="text-nowrap">' . ($row->created_at ? $row->created_at->format('Y-m-d H:i') : '-') . '</div>';
        if ($row->deleted_at) {
            $dates .= '<div class="text-danger fs-8 mt-1">' . e(t('Deleted')) . ': ' . Carbon::parse($row->deleted_at)->format('Y-m-d H:i') . '</div>';
        }

        $result = $isCorrected
            ? '<span class="badge badge-light-success fw-bold">' . e(t('Status Corrected')) . '</span>'
            : '<span class="badge badge-light-warning fw-bold">' . e(t('Status Uncorrected')) . '</span>';
        if ($isCorrected) {
            $color = $row->total < 50 ? 'text-danger' : 'text-success';
            $result .= '<div class="fw-bolder fs-5 mt-1 ' . $color . '">' . e($row->total) . '<span class="fs-8 text-muted">/100</span></div>';
        }

        $term = $row->term;
        $level = $term ? $term->level : null;
        $termData = $term
            ? e(t($term->round)) . '<br><span class="text-muted fs-8">' . e($level ? $level->short_name : '') . '</span>'
            : '-';

        $name = '-';
        $school = '-';
        $class = '-';
        if ($student) {
            $name = '<div class="d-flex flex-column">'
                . '<span class="copy-txt text-info cursor-pointer" data-txt="' . e($student->name) . '">' . e($student->name) . '</span>'
                . '<span class="text-danger cursor-pointer copy-txt" data-txt="' . e($student->email) . '">' . e($student->email) . '</span>'
                . '<span>SID: <span class="text-info fw-bold copy-txt cursor-pointer" data-txt="' . e($student->id_number) . '">' . e($student->id_number) . '</span></span>'
                . '</div>';
            $school = $student->school ? e($student->school->name) : '-';

            // Class plus the student's own year, red when it is not the assessment year
            $class = '<div>' . e($student->grade_name ?? '-') . '</div>';
            if ($student->year) {
                $mismatch = $level && $level->year_id != $student->year_id;
                $class .= '<span class="badge ' . ($mismatch ? 'badge-light-danger' : 'badge-light') . ' fs-8 mt-1"'
                    . ' title="' . e($mismatch ? t('Student year differs from the assessment year') : t('Student Year')) . '">'
                    . e($student->year->name) . '</span>';
            }
        }

        return [
            'DT_RowId' => $row->id,
            'DT_RowClass' => $isCorrected ? 'st-corrected' : 'st-uncorrected',
            'id' => $row->id,
            'name' => $name,
            'school' => $school,
            'grade_name' => $class,
            'term_data' => $termData,
            'result' => '<div class="d-flex flex-column gap-1 align-items-start">' . $result . '</div>',
            'created_at' => $dates,
            'actions' => $this->allTermsActions($row, $permissions),
            'sen' => $student ? $student->sen : null,
            'g_t' => $student ? $student->g_t : null,
        ];
    }

    /** Row buttons: correct, certificate and a direct delete (the .delete_row handler of datatable.js). */
    private function allTermsActions(StudentTerm $row, array $permissions): string
    {
        if ($row->deleted_at) {
            return $permissions['restore']
                ? '<button type="button" onclick="restore(' . $row->id . ')" class="btn btn-sm btn-light-warning">' . e(t('Restore')) . '</button>'
                : '';
        }

        $buttons = [];
        // Same rule as action_data: any edit permission for corrected rows, a direct one for uncorrected rows
        $canCorrect = $row->corrected ? $permissions['correct'] : $permissions['correct_uncorrected'];
        if ($canCorrect) {
            $buttons[] = '<a target="_blank" href="' . route('manager.student_term.edit', $row->id) . '"'
                . ' class="btn btn-sm ' . ($row->corrected ? 'btn-light-primary' : 'btn-success') . '">' . e(t('Correct')) . '</a>';
        }

        $school = $row->student ? $row->student->school : null;
        if ($row->corrected && $school && $row->total >= $school->certificate_mark) {
            $buttons[] = '<a target="_blank" href="' . route('manager.student-term.certificate', $row->id) . '"'
                . ' class="btn btn-sm btn-icon btn-light-info" title="' . e(t('Certificate')) . '"><i class="fa fa-certificate"></i></a>';
        }

        if ($permissions['delete']) {
            $buttons[] = '<button type="button" class="btn btn-sm btn-icon btn-light-danger delete_row" data-id="' . $row->id . '"'
                . ' title="' . e(t('Delete')) . '"><i class="fa fa-trash"></i></button>';
        }

        return '<div class="d-flex align-items-center gap-1 text-nowrap">' . implode('', $buttons) . '</div>';
    }
}
