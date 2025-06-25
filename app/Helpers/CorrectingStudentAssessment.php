<?php

namespace App\Helpers;

use App\Models\ArticleQuestionResult;
use App\Models\FillBlankAnswer;
use App\Models\MatchQuestionResult;
use App\Models\OptionQuestionResult;
use App\Models\Question;
use App\Models\QuestionStandard;
use App\Models\SortQuestionResult;
use App\Models\StudentTerm;
use App\Models\StudentTermStandard;
use App\Models\Subject;
use App\Models\TFQuestionResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CorrectingStudentAssessment
{
    public function correcting(Request $request,$id){
        $student_term = StudentTerm::query()->where('id',$id)->first();
        $student_id =$student_term->student_id;

        $term_questions = Question::with(['tf_question','option_question','match_question','sort_question','fill_blank_question'])
            ->where('term_id',$student_term->term_id)->get();
        // dd($term_questions->toArray());

        DB::transaction(function () use ($request,$id,$student_term,$student_id,$term_questions){

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
                for($i = 0; $i < count($subjects_marks); $i++){
                    if ($subjects_marks[$i]['subject_id'] == $question['subject_id']){
                        $subjects_marks[$i]['mark'] += $mark;
                        $status = true;
                        break;
                    }
                }
                if (!$status){
                    $subjects_marks[] = ['subject_id' => $question['subject_id'], 'mark' => $mark];
                }




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
}
