<?php

namespace App\Http\Controllers\Manager;

use App\Exports\StudentTermExport;
use App\Http\Controllers\Controller;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

class StudentTermController extends Controller
{


    public function __construct()
    {
        $this->middleware('permission:show students terms')->only('index');
        $this->middleware('permission:edit students terms')->only(['edit','updateTerm']);
        $this->middleware('permission:restore deleted students terms')->only('restore');
        $this->middleware('permission:delete students terms')->only('deleteStudentTerm');
        $this->middleware('permission:restore deleted students terms')->only('restore');
        $this->middleware('permission:auto correct students terms')->only('autoCorrect');
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
                    return $row->student->name??'-';
                })
                ->addColumn('email', function ($row) {
                    return $row->student->email??'-';
                })
                ->addColumn('grade_name', function ($row) {
                    return $row->student->grade_name??'-';
                }) ->addColumn('school', function ($row) {
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
        $student_term = StudentTerm::with('student')->where('id',$id)->first();
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


        return view('manager.student_term.term_correcting.index',compact('student','questions','student_term','questions_count','marks','subjects','correct_mode'));
    }

    //correcting
    public function updateTerm(Request $request,$id){
        $request->validate(['questions'=>'required|array']);
        //dd($request['questions']);
        $student_term = StudentTerm::query()->where('id',$id)->first();
        $student_id =$student_term->student_id;
        $subjects = Subject::all();

        $term_questions = Question::with(['tf_question','option_question','match_question','sort_question','fill_blank_question'])
        ->where('term_id',$student_term->term_id)->get();
       // dd($term_questions->toArray());

        DB::transaction(function () use ($request,$id,$student_term,$student_id,$term_questions,$subjects){

            $total = 0;
            $subjects_marks = [];

            //delete old result
            SortQuestionResult::query()->where('student_term_id',$id)->forceDelete();
            MatchQuestionResult::query()->where('student_term_id',$id)->forceDelete();
            FillBlankAnswer::query()->where('student_term_id',$id)->forceDelete();

            foreach ($request['questions'] as $question_id=>$question){
                $mark = 0;

                switch ($question['type']) {
                    case 'true_false':
                        if (isset($question['answer'])){
                            $mark = $this->saveTFResultAndCorrect($student_id, $id, $question_id, $question, $term_questions);
                        }
                        break;
                    case 'multiple_choice':
                        if (isset($question['answer_option_id'])){
                            $mark = $this->saveOptionResultAndCorrect($student_id, $id, $question_id, $question, $term_questions);
                        }
                        break;
                    case 'matching':
                        if (isset($question['options'])){
                            $mark = $this->saveMatchResultAndCorrect($student_id, $id, $question_id, $question['options'], $term_questions);
                        }
                        break;
                    case 'sorting' :
                        if (isset($question['options'])){
                            $mark = $this->saveSortResult($student_id, $id, $question_id, $question['options'], $term_questions);
                        }
                        break;
                    case 'fill_blank' :
                        if(isset($question['blanks'])){
                            $mark = $this->saveFillBlankResult($id, $question_id, $question, $term_questions);
                        }
                        break;
                    case 'article':
                        $mark = $this->saveArticleResult($id, $question_id, $question, $term_questions);

                        break;
                }

               $total+= $mark;

                //set subject mark
                $status = false;
                foreach ($subjects_marks as $subject_mark){
                    if ($subject_mark['subject_id'] == $question['subject_id']){
                        $subject_mark['mark'] += $mark;
                        $status = true;
                        break;
                    }
                }
                if (!$status){
                    $subjects_marks[] = ['subject_id' => $question['subject_id'], 'mark' => $mark];
                }
//



                $this->saveStudentTermStandard($id,$question_id,$mark);

            }
            if (!is_null($student_term->dates_at)) {
                $dates_at = $student_term->dates_at;
            } else {
                $dates_at = [
                    'started_at' => null,
                    'submitted_at' => null,
                ];
            }
            $dates_at['corrected_at'] = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
            $dates_at['corrected_by'] = Auth::guard('manager')->user()->id;
            $student_term->update([
                'subjects_marks'=>$subjects_marks,
                'total'=>$total,
                'corrected'=>true,
                'dates_at'=>$dates_at
            ]);

        });
        return redirect()->route('manager.student_term.index',['status'=>'corrected'])->with('message',t('The term has been corrected'));
    }

    private function saveTFResultAndCorrect($student_id,$student_term_id,$question_id,$result,$term_questions){
        if (isset($result['question_result_id'])){
            //update
            TFQuestionResult::query()
                ->where('id',$result['question_result_id'])
                ->update(['result'=>$result['answer']]);
        }else{
            //create
            TFQuestionResult::query()->create([
                'student_id'=>$student_id,
                'student_term_id'=>$student_term_id,
                'question_id'=>$question_id,
                'result'=>$result['answer'],
            ]);
        }

        //correct question
        $question = $term_questions->where('id',$question_id)->first();
        if ($question->tf_question->result == $result['answer']){
            return $question->mark;
        }
        return 0;

    }

    private function saveOptionResultAndCorrect($student_id,$student_term_id,$question_id,$result,$term_questions){
        if (isset($result['question_result_id'])){
            //update
            OptionQuestionResult::query()
                ->where('id',$result['question_result_id'])
                ->update(['option_id'=>$result['answer_option_id']]);

        }else{
            //create
            OptionQuestionResult::query()->create([
                'student_id'=>$student_id,
                'student_term_id'=>$student_term_id,
                'question_id'=>$question_id,
                'option_id'=>$result['answer_option_id'],
            ]);
        }

        //correct question
        $question = $term_questions->where('id',$question_id)->first();
        $correct_option = collect($question->option_question)->where('id',$result['answer_option_id'])->first();

        if ($correct_option->result == 1){
            return $question->mark;
        }

        return 0;
    }

    private function saveMatchResultAndCorrect($student_id,$student_term_id,$question_id,array $matches,$term_questions){
        $question = $term_questions->where('id',$question_id)->first();
        $mark_for_match = $question->mark/$question->match_question->count();
        $mark = 0;
        $answers = [];
        foreach ($matches as $uid => $match_id) {
            if (!is_null($match_id)) {
                $answers[] = [
                    'student_id'=>$student_id,
                    'student_term_id'=>$student_term_id,
                    'question_id'=>$question_id,
                    'match_id' => $match_id,
                    'match_question_answer_uid' => $uid,
                    'created_at'=>now(),
                    'updated_at'=>now()
                ];
                if ($question->match_question->where('id',$match_id)->where('uid',$uid)->first()){
                    $mark +=$mark_for_match;
                }
            }
        }
        if ($answers){
            MatchQuestionResult::insert($answers);
        }
        return $mark;
    }

    private function saveSortResult($student_id,$student_term_id,$question_id,array $sort,$term_questions){
        $question = $term_questions->where('id',$question_id)->first();
        $mark_for_sort = $question->mark/$question->sort_question->count();
        $count = 0;
        $mark = 0;
        $answers = [];
        foreach ($sort as $uid=>$value){

            if (!is_null($value))
            {
                $answers[]= [
                        'student_id'=>$student_id,
                        'student_term_id'=>$student_term_id,
                        'question_id'=>$question_id,
                        'sort_question_uid'=>$uid, //mean: word - character
                        'created_at'=>now(),
                        'updated_at'=>now(),
                    ];
                if ($question->sort_question[$count]['uid'] == $uid){
                    $mark +=$mark_for_sort;
                }
            }


            $count++;
        }

        if ($answers){
            SortQuestionResult::insert($answers);
        }
        return $mark;
    }

    private function saveArticleResult($student_term_id,$question_id,$question,$term_questions){
        $article_question = $term_questions->where('id',$question_id)->first();
        if (isset($question['mark'])&& $question['mark'] > $article_question->mark){
            throw ValidationException::withMessages([t('The entered mark is greater than the question mark')]);
        }
        $data = [
            'student_term_id' => $student_term_id,
            'question_id' => $question_id,
            'mark' => $question['mark']
        ];

        if (isset($question['text_answer'])){
            $data['text_answer'] = $question['answer_text'];
        }

        ArticleQuestionResult::query()->updateOrCreate(
            ['student_term_id' => $student_term_id,'question_id' => $question_id], $data);

        return $question['mark'];
    }

    private function saveFillBlankResult($student_term_id,$question_id,$question,$term_questions)
    {
        $t_question = $term_questions->where('id',$question_id)->first();
        $mark_for_blank = $t_question->mark/$t_question->fill_blank_question->count();
        $mark = 0;
        $answers = [];
        foreach ($question['blanks'] as $uid => $fillBlank_question_id) {
            if($fillBlank_question_id)
            {
                $answers [] = [
                        'student_term_id' => $student_term_id,
                        'question_id' => $question_id,
                        'fill_blank_question_id' => $fillBlank_question_id,
                        'answer_fill_blank_question_uid' => $uid,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                if ($t_question->fill_blank_question->where('id', $fillBlank_question_id)->where('uid', $uid)->first()) {
                    $mark += $mark_for_blank;
                }
            }

        }

        if ($answers){
            FillBlankAnswer::insert($answers);
        }

         return $mark;
    }
    private function saveStudentTermStandard($student_term_id, $question_id, $mark)
    {
        $question_standard = QuestionStandard::query()->where('question_id', $question_id)->first();
        if ($question_standard) {
            StudentTermStandard::query()->updateOrCreate(
                [
                    'student_term_id' => $student_term_id,
                    'question_standard_id' => $question_standard->id,
                ],
                [
                    'student_term_id' => $student_term_id,
                    'question_standard_id' => $question_standard->id,
                    'mark' => $mark,
                ]
            );
        }
    }

    public function deleteStudentTerm(Request $request){
        $request->validate(['row_id'=>'required']);
        StudentTerm::query()->whereIn('id',$request->get('row_id'))->delete();
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

        $subjects = Subject::all();

        foreach ($students_terms as $student_term){
            $student_term_id = $student_term->id;
            $student_id = $student_term->student_id;
            $term_questions = Question::with(
                ['tf_question','option_question','match_question','sort_question','fill_blank_question',
                    'tf_question_result'=>function($query)use($student_term_id){
                        $query->where('student_term_id',$student_term_id);
                    }, 'option_question_result'=>function($query)use($student_term_id){
                    $query->where('student_term_id',$student_term_id);
                },'sort_question_result'=>function($query)use($student_term_id){
                    $query->where('student_term_id',$student_term_id);
                },'match_question_result'=>function($query)use($student_term_id){
                    $query->where('student_term_id',$student_term_id);
                },'article_question_result'=>function($query)use($student_term_id){
                    $query->where('student_term_id',$student_term_id);
                },'fill_blank_answer'=>function($query)use($student_term_id){
                    $query->where('student_term_id',$student_term_id);
                },
                ])
                ->where('term_id',$student_term->term_id)->get();

            DB::transaction(function () use ($request,$student_term_id,$student_term,$term_questions,$subjects){

                $total = 0;
                $subjects_marks= [];


                foreach ($term_questions as $question){
                    $mark = 0;

                    if ($question->type == 'true_false' && $question->tf_question_result->isNotEmpty()){
                        //correct question
                        if ($question->tf_question->result == $question->tf_question_result[0]->result){
                            $mark= $question->mark;
                        }
                    }else if ($question->type == 'multiple_choice' && $question->option_question_result->isNotEmpty()){
                        //correct question
                        $correct_option = collect($question->option_question)->where('id',$question->option_question_result[0]->option_id)->first();
                        if ($correct_option->result == 1){
                            $mark= $question->mark;
                        }
                    }else if ($question->type == 'matching' && $question->match_question_result->isNotEmpty()){
                        $mark_for_match = $question->mark/count($question->match_question);
                        $count = 0;
                        //create new result
                        foreach ($question->match_question_result as $match_result){
                            if ($question->match_question->where('id',$match_result->match_id)->where('uid',$match_result->match_question_answer_uid)->first()){
                                $mark +=$mark_for_match;
                            }
                        }
                    }else if ($question->type == 'sorting'  && $question->sort_question_result->isNotEmpty()){
                        $mark_for_sort = $question->mark/count($question->sort_question);
                        $count = 0;
                        //create new result
                        foreach ($question->sort_question_result as $sort_result){
                            if ($question->sort_question[$count]['uid'] == $sort_result->sort_question_uid){
                                $mark +=$mark_for_sort;
                            }
                            $count++;
                        }
                    }else if ($question->type == 'fill_blank'  && $question->fill_blank_answer->isNotEmpty()){
                        $mark_for_sort = $question->mark/count($question->fill_blank_question);
                        //create new result
                        foreach ($question->fill_blank_answer as $answer){
                            if ($question->fill_blank_question->where('id',$answer->fill_blank_question_id)->where('uid',$answer->answer_fill_blank_question_uid)->first()){
                                $mark +=$mark_for_sort;
                            }
                        }
                    }

                    $total+= $mark;

                    //set subject mark
                    $status = false;
                    foreach ($subjects_marks as $subject_mark){
                        if ($subject_mark['subject_id'] == $question->subject_id){
                            $subject_mark['mark'] += $mark;
                            $status = true;
                            break;
                        }
                    }
                    if (!$status){
                        $subjects_marks[] = ['subject_id' => $question->subject_id, 'mark' => $mark];
                    }

                    $this->saveStudentTermStandard($student_term_id,$question->id,$mark);

                }
                if (!is_null($student_term->dates_at)) {
                    $dates_at = $student_term->dates_at;
                } else {
                    $dates_at = [
                        'started_at' => null,
                        'submitted_at' => null,
                    ];
                }
                $dates_at['corrected_at'] = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
                $dates_at['corrected_by'] = Auth::guard('manager')->user()->id;
                $student_term->update([
                    'subjects_marks'=>$subjects_marks,
                    'total'=>$total,
                    'corrected'=>true,
                    'dates_at'=>$dates_at
                ]);

            });
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
             StudentTermStandard::query()->where('student_term_id',$id)->withTrashed()->update(['deleted_at' => null]);
            return $this->sendResponse(null, t('Successfully Restored'));
        }else{
            return $this->sendError( t('Student Term Not Restored'),402);
        }
    }
}
