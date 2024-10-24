<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ArticleQuestionResult;
use App\Models\FillBlankAnswer;
use App\Models\MatchQuestion;
use App\Models\MatchQuestionResult;
use App\Models\OptionQuestionResult;
use App\Models\Question;
use App\Models\SchoolGrade;
use App\Models\SortQuestionResult;
use App\Models\StudentTerm;
use App\Models\Subject;
use App\Models\Term;
use App\Models\TFQuestion;
use App\Models\TFQuestionResult;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TermController extends Controller
{

    public function termStart(Request $request, $id)
    {
        $student = Auth::guard('student')->user();


        if (!Auth::guard('student')->user()->demo) {
            //get term and check if available for student
            $term = Term::query()
                ->where('id', $id)
                ->where('level_id', $student->level_id)
                ->first();
            if (!$term) {
                return redirect()->route('student.home')->with('term-message', t('Assessment Not Found'));
            }


            //check if term round is available in studentSchool
            $round_is_available = SchoolGrade::with('school')
                ->where('school_id', $student->school_id)
                ->where($term->round, true)
                ->whereHas('school', function ($query) use ($student) {
                    $query->where('available_year_id', $student->level->year_id);
                })->first();

            if (!$round_is_available) {
                return redirect()->route('student.home')->with('term-message', t('Assessment Not Available For You'));
            }

            //check if the term is passed
            $is_passed = StudentTerm::query()
                ->where('student_id', $student->id)
                ->where('term_id', $term->id)
//            ->whereNotNull('end_at')
                ->first();

            if ($is_passed) {
                return redirect()->route('student.home')->with('term-message', t('Assessment Passed Previously'));
            }
        } else {
            $term = Term::query()
                ->where('id', $id)
                ->whereHas('level', function ($query) use ($student) {
                    $query->whereIn('id', $student->demo_data->levels);
                })
                ->whereIn('round', $student->demo_data->rounds)
                ->first();

            if (!$term) {
                return redirect()->route('student.home')->with('term-message', t('Assessment Not Found'));
            }

        }


        $questions = Question::with(['option_question', 'match_question', 'sort_question','fill_blank_question'])
            ->where('term_id', $id)->get()->groupBy('subject_id');

        $questions_count = count($questions);
        $subjects = Subject::all();
        $marks = '100';

        return view('student.term.index', compact('student', 'term', 'questions', 'questions_count', 'marks', 'subjects'));
    }

    public function termLeave()
    {
        StudentTerm::query()->where('id', \session()->get('student_term_id'))->forceDelete();
        \session()->forget('student_term_id');
        return redirect()->route('student.home');
    }


    public function termSave(Request $request, $id)
    {
        $request->validate(['questions' => 'required|array']);

//        dd($request['questions']);
        $student = Auth::guard('student')->user();

        if (!$student->demo) {
            $student_term = StudentTerm::query()->create([
                'student_id' => $student->id,
                'term_id' => $id,
                "dates_at" => [
                    'started_at' => $request->get('started_at', \Carbon\Carbon::now()->format('Y-m-d H:i:s')),
                    'submitted_at' => \Carbon\Carbon::now('Asia/Dubai')->format('Y-m-d H:i:s'),
                    'corrected_at' => null,
                    'corrected_by' => null,
                ]
            ]);

            DB::transaction(function () use ($request, $id, $student, $student_term) {

                foreach ($request['questions'] as $key => $question) {
                    switch ($question['type']) {

                        case 'true_false' && isset($question['answer']):
                            $this->saveTFResult($student->id, $student_term->id, $key, $question['answer']);
                            break;
                        case 'multiple_choice' && isset($question['answer_option_id']):
                            $this->saveOptionResult($student->id, $student_term->id, $key, $question['answer_option_id']);
                            break;
                        case 'matching' && isset($question['options']):
                            $this->saveMatchResult($student->id, $student_term->id, $key, $question['options']);
                            break;
                        case 'sorting' && isset($question['options']):
                            $this->saveSortResult($student->id, $student_term->id, $key, $question['options']);
                            break;
                        case 'fill_blank':
                            $this->saveFillBlank($student_term->id, $key, $question);
                            break;
                        case 'article':
                            $this->saveArticleResult($student_term->id, $key, $question);
                            break;
                    }

                }
            });
        }


//        \session()->forget('student_term_id');

        return redirect()->route('student.home')->with('term-message', t('Assessment passed successfully'));
    }


    private function saveTFResult($student_id, $student_term_id, $question_id, $result)
    {
        TFQuestionResult::query()->create([
            'student_id' => $student_id,
            'student_term_id' => $student_term_id,
            'question_id' => $question_id,
            'result' => $result,

        ]);
        return true;
    }

    private function saveOptionResult($student_id, $student_term_id, $question_id, $option_id)
    {
        OptionQuestionResult::query()->create([
            'student_id' => $student_id,
            'student_term_id' => $student_term_id,
            'question_id' => $question_id,
            'option_id' => $option_id,
        ]);
        return true;
    }


    private function saveMatchResult($student_id, $student_term_id, $question_id, array $matches)
    {
        foreach ($matches as $match_answer_uid => $match_id) {
            MatchQuestionResult::query()->create([
                'student_id' => $student_id,
                'student_term_id' => $student_term_id,
                'question_id' => $question_id,
                'match_id' => $match_id,
                'match_question_answer_uid' => $match_answer_uid,
            ]);
        }
        return true;
    }


    private function saveSortResult($student_id, $student_term_id, $question_id, array $sort)
    {
        foreach ($sort as $key => $value) {
            if ($value) {
                SortQuestionResult::query()->create([
                    'student_id' => $student_id,
                    'student_term_id' => $student_term_id,
                    'question_id' => $question_id,
                    'sort_question_uid' => $key,
                ]);
            }
        }
        return true;
    }

    private function saveArticleResult($student_term_id, $question_id, $question)
    {
        $data = ['student_term_id' => $student_term_id, 'question_id' => $question_id];

        if ($question['answer_type'] == 'file') { //file answer
            if (isset($question['answer_file'])) {
                $upload = uploadFile($question['answer_file']);
                $data['answer_file_path'] = $upload['path'];
            }
        } else { //text answer
            $data['text_answer'] = $question['answer_text'];
        }

        ArticleQuestionResult::query()->create($data);
    }


    public function saveFillBlank($student_term_id, $question_id, $question)
    {

        if (isset($question['blanks']) && count($question['blanks']) > 0) {
            foreach ($question['blanks'] as $uid => $fillBlank_question_id) {
                if (!is_null($fillBlank_question_id)) {
                    FillBlankAnswer::query()->create([
                        'student_term_id' => $student_term_id,
                        'question_id' => $question_id,
                        'fill_blank_question_id' => $fillBlank_question_id,
                        'answer_fill_blank_question_uid' => $uid,
                    ]);
                }
            }
        }
    }


}
