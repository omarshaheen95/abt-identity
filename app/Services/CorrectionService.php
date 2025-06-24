<?php

namespace App\Services;

use App\Helpers\Constant;
use App\Helpers\Response;
use App\Models\Question;
use App\Models\StudentTerm;
use App\Models\StudentTermStandard;
use App\Models\QuestionStandard;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CorrectionService
{

    public function correctStudentTerm(StudentTerm $studentTerm): array
    {
        try {
            // Initialize the correction data
            $correctionData = [
                'corrected' => true,
                'total' => 0
            ];

            $subjects_marks = collect([]);
            $student_term_id = $studentTerm->id;

            $term_questions = Question::with(
                ['tf_question', 'option_question', 'match_question', 'sort_question', 'fill_blank_question',
                    'tf_question_result' => function ($query) use ($student_term_id) {
                        $query->where('student_term_id', $student_term_id);
                    }, 'option_question_result' => function ($query) use ($student_term_id) {
                    $query->where('student_term_id', $student_term_id);
                }, 'sort_question_result' => function ($query) use ($student_term_id) {
                    $query->where('student_term_id', $student_term_id);
                }, 'match_question_result' => function ($query) use ($student_term_id) {
                    $query->where('student_term_id', $student_term_id);
                }, 'fill_blank_answer' => function ($query) use ($student_term_id) {
                    $query->where('student_term_id', $student_term_id);
                }
                ])
                ->where('term_id', $studentTerm->term_id)->get();

            foreach ($term_questions as $question) {
                $mark = 0;

                if ($question->type == 'true_false' && $question->tf_question_result->isNotEmpty()) {
                    //correct question
                    if ($question->tf_question->result == $question->tf_question_result[0]->result) {
                        $mark = $question->mark;
                    }
                } else if ($question->type == 'multiple_choice' && $question->option_question_result->isNotEmpty()) {
                    //correct question
                    $correct_option = collect($question->option_question)->where('id', $question->option_question_result[0]->option_id)->first();
                    if ($correct_option->result == 1) {
                        $mark = $question->mark;
                    }
                } else if ($question->type == 'matching' && $question->match_question_result->isNotEmpty()) {
                    $mark_for_match = $question->mark / count($question->match_question);
                    $count = 0;
                    foreach ($question->match_question_result as $match_result) {
                        if ($question->match_question->where('id', $match_result->match_id)->where('uid', $match_result->match_question_answer_uid)->first()) {
                            $mark += $mark_for_match;
                        }
                    }
                } else if ($question->type == 'sorting' && $question->sort_question_result->isNotEmpty()) {
                    $mark_for_sort = $question->mark / count($question->sort_question);
                    $count = 0;
                    foreach ($question->sort_question_result as $sort_result) {
                        if ($question->sort_question[$count]['uid'] == $sort_result->sort_question_uid) {
                            $mark += $mark_for_sort;
                        }
                        $count++;
                    }
                } else if ($question->type == 'fill_blank' && $question->fill_blank_answer->isNotEmpty()) {
                    $mark_for_sort = $question->mark / count($question->fill_blank_question);
                    foreach ($question->fill_blank_answer as $answer) {
                        if ($question->fill_blank_question->where('id', $answer->fill_blank_question_id)->where('uid', $answer->answer_fill_blank_question_uid)->first()) {
                            $mark += $mark_for_sort;
                        }
                    }
                }

                if ($subjects_marks->where('subject_id', $question['subject_id'])->count() > 0) {
                    $subjects_marks = $subjects_marks->map(function ($item) use ($question, $mark) {
                        if ($item['subject_id'] == $question['subject_id']) {
                            $item['mark'] += $mark;
                        }
                        return $item;
                    });
                } else {
                    // Add subject id with mark
                    $subjects_marks->push([
                        'subject_id' => $question['subject_id'],
                        'mark' => $mark // Initial mark value
                    ]);
                }

                // Save standard if present
                $this->saveStudentTermStandard($student_term_id, $question->id, $mark);

                //add mark to total
                $correctionData['total'] += $mark;
            }

            $correctionData['subjects_marks'] = $subjects_marks->toArray();


            // Update correction timestamp
            $dates = $studentTerm->dates_at ?? [
                'started_at' => null,
                'submitted_at' => null,
            ];
            $dates['corrected_at'] = now()->format('Y-m-d H:i:s');

            if (Auth::guard('manager')->check()) {
                $dates['corrected_by'] = Auth::guard('manager')->id();
            }

            $correctionData['dates_at'] = $dates;

            return $correctionData;

        } catch (\Exception $e) {
            Log::error('Auto-correction failed: ' . $e->getMessage(), [
                'student_term_id' => $studentTerm->id,
                'exception' => $e
            ]);

            return [
                'error' => true,
                'message' => 'Auto-correction failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Save student term standard
     *
     * @param int $student_term_id
     * @param int $question_id
     * @param float $mark
     * @return void
     */

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
