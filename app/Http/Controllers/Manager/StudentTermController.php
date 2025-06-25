<?php

namespace App\Http\Controllers\Manager;

use App\Exports\StudentTermExport;
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
                ->addColumn('student_id', function ($row) {
                    return $row->student->id??'-';
                })
                ->addColumn('name', function ($row) {
                    $html = '<div class="d-flex flex-column">';
                    $html .= '<div class="mb-1">'.$row->student->name??'-'.'</div>';
                    $html .= '<div class="mb-1"><span class="badge badge-info">' . t('Grade') . '</span> <span>' . $row->term->level->grade . '</span></div></div>';
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
        $student_term = StudentTerm::with(['student','term'])->where('id',$id)->first();
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
                if ($question->type == 'true_false') {
                    if (count($question->tf_question_result) > 0) {
                        $student_result = $question->tf_question_result[0];
                    }

                    $main_result = $question->tf_question;
                    if ($process_type == 'upgrade') {
                        if (isset($student_result) && isset($main_result) && optional($student_result)->result != optional($main_result)->result) {
                            $student_result->update([
                                'result' => optional($main_result)->result,
                            ]);
                            $updated_marks += $question->mark;
                        }
                    } else {
                        if (isset($student_result) && isset($main_result) && optional($student_result)->result == optional($main_result)->result) {
                            $student_result->update([
                                'result' => optional($main_result)->result == 1 ? 0 : 1,
                            ]);
                            $updated_marks += $question->mark;
                        }
                    }

                }
                elseif ($question->type == 'multiple_choice') {
                    $student_result = $question->option_question_result->first();

                    if ($student_result) {
                        $main_result = $question->option_question->where('id', $student_result->option_id)->first();
                    }


                    if ($process_type == 'upgrade') {
                        if ($student_result && isset($main_result->result) && $main_result->result != 1) {
                            $student_result->update([
                                'option_id' => $question->option_question->where('result', 1)->first()->id,
                            ]);
                            $updated_marks += $question->mark;
                        }
                    } else {
                        if ($student_result && isset($main_result->result) && $main_result->result == 1) {
                            $student_result->update([
                                'option_id' => $question->option_question->where('result', 0)->first()->id,
                            ]);
                            $updated_marks += $question->mark;
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


}

