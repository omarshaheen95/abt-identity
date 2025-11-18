<?php

namespace App\Http\Controllers\Student;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Models\ArticleQuestionResult;
use App\Models\FillBlankAnswer;
use App\Models\MatchQuestion;
use App\Models\MatchQuestionResult;
use App\Models\OptionQuestionResult;
use App\Models\Question;
use App\Models\SchoolGrade;
use App\Models\SortQuestionResult;
use App\Models\Student;
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
        $student = Student::query()->with(['level'])->find(Auth::guard('student')->user()->id);


        if (!$student->demo) {
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
                ->where('arab', $student->level->arab)
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
        $assessment_opened = $student->assessment_opened;
        $student->update(['assessment_opened' => 1]);

        $questions = Question::with(['subject', 'option_question', 'match_question', 'sort_question', 'fill_blank_question'])
            ->where('term_id', $id)->get();

        $questions_count = count($questions);
        $questions = $questions->groupBy(['subject_id', 'type']);
        $subjects = Subject::all();
        $marks = '100';

        app()->setLocale($term->level->arab ? 'ar' : 'en');

        return view('student.term.index', compact('student', 'assessment_opened', 'term', 'questions', 'questions_count', 'marks', 'subjects'));
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

        $student = Auth::guard('student')->user();

        $is_passed = StudentTerm::query()
            ->where('student_id', $student->id)
            ->where('term_id', $id)
            ->first();

        if ($is_passed) {
            return Response::respondError('Assessment passed previously', 422);
        }

        try {
            if (!$student->demo) {

                DB::transaction(function () use ($request, $id, $student) {

                    $student_term = StudentTerm::query()->create([
                        'student_id' => $student->id,
                        'term_id' => $id,
                        "dates_at" => [
                            'started_at' => $request->get('started_at', \Carbon\Carbon::now()->format('Y-m-d H:i:s')),
                            'submitted_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
                            'corrected_at' => null,
                            'corrected_by' => null,
                            'emergency_save'=>$request->get('emergency_save',0)
                        ]
                    ]);
                    $student->update(['assessment_opened' => 0]);

                    foreach ($request['questions'] as $key => $question) {
                        switch ($question['type']) {
                            case 'true_false':
                                if (isset($question['answer'])) {
                                    $this->saveTFResult($student->id, $student_term->id, $key, $question['answer']);
                                }
                                break;

                            case 'multiple_choice':
                                if (isset($question['answer_option_id'])) {
                                    $this->saveOptionResult($student->id, $student_term->id, $key, $question['answer_option_id']);
                                }
                                break;

                            case 'matching':
                                if (isset($question['options'])) {
                                    $this->saveMatchResult($student->id, $student_term->id, $key, $question['options']);
                                }
                                break;

                            case 'sorting':
                                if (isset($question['options'])) {
                                    $this->saveSortResult($student->id, $student_term->id, $key, $question['options']);
                                }
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

        } catch (\Exception $exception) {
            if ($exception->getCode() == 23000) {
                return Response::respondSuccess('** Assessment passed successfully **', route('student.home'));
            } else {
                return Response::respondError($exception->getMessage());
            }
        }

        return Response::respondSuccess('Assessment passed successfully',route('student.home'));
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
        $answers = [];
        foreach ($matches as $match_answer_uid => $match_id) {
            if (!is_null($match_id)) {
                $answers[] = [
                    'student_id' => $student_id,
                    'student_term_id' => $student_term_id,
                    'question_id' => $question_id,
                    'match_id' => $match_id,
                    'match_question_answer_uid' => $match_answer_uid,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        if ($answers) {
            MatchQuestionResult::insert($answers);
        }
        return true;
    }


    private function saveSortResult($student_id, $student_term_id, $question_id, array $sort)
    {
        $answers = [];
        foreach ($sort as $key => $value) {
            if (!is_null($value)) {
                $answers[] = [
                    'student_id' => $student_id,
                    'student_term_id' => $student_term_id,
                    'question_id' => $question_id,
                    'sort_question_uid' => $key,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        if ($answers) {
            SortQuestionResult::insert($answers);
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
            $answers = [];
            foreach ($question['blanks'] as $uid => $fillBlank_question_id) {
                if (!is_null($fillBlank_question_id)) {
                    $answers [] = [
                        'student_term_id' => $student_term_id,
                        'question_id' => $question_id,
                        'fill_blank_question_id' => $fillBlank_question_id,
                        'answer_fill_blank_question_uid' => $uid,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            if ($answers) {
                FillBlankAnswer::insert($answers);
            }
        }
    }


}
