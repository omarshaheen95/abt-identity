<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\MatchQuestionResult;
use App\Models\OptionQuestionResult;
use App\Models\Question;
use App\Models\StudentTerm;
use App\Models\TFQuestionResult;
use App\Models\Term;
use App\Services\CorrectionService;
use Illuminate\Support\Facades\DB;

class TransferStudentController extends Controller
{
    // الأنواع المستثناة من النقل نهائياً
    private const EXCLUDED_TYPES = ['fill_blank', 'sorting', 'article'];

    public function transferFullStudentTerm()
    {
        // ============================================================
        // 1. تعريف بيانات المصدر (المراد النقل منها)
        // ============================================================
        $source_school_id = 100;
        $source_year_id   = 2;
        $source_round     = 'may';

        // ============================================================
        // 2. تعريف بيانات الهدف (المراد النقل إليها)
        // ============================================================
        $target_year_id = 3;
        $target_round   = 'september';

        // ============================================================
        // 3. جلب اختبارات الطلاب المراد نقلها (المصدر)
        //    الشرط: طالب من المدرسة والسنة المحددة + جولة المصدر
        // ============================================================
        $sourceStudentTerms = StudentTerm::with(['student', 'term.level'])
//            ->where('id', 82651)
            ->whereHas('student', function ($q) use ($source_school_id, $source_year_id) {
                $q->where('school_id', $source_school_id)
                  ->where('year_id', $source_year_id);
            })
            ->whereHas('term', function ($q) use ($source_round) {
                $q->where('round', $source_round);
            })
            ->where('corrected', 1) // شرط إضافي: فقط الاختبارات المصححة
                ->limit(100) // تقييد عدد الاختبارات المنقولة في كل مرة لتجنب الضغط الزائد
            ->get();



        // ============================================================
        // 4. جلب student_ids الموجودة مسبقاً في الهدف
        //    لفحص التكرار بكفاءة (lookup بـ O(1) باستخدام flip)
        // ============================================================
        $existingTargetStudentIds = StudentTerm::whereHas('term', function ($q) use ($target_year_id, $target_round) {
                $q->where('round', $target_round)
                  ->whereHas('level', function ($q) use ($target_year_id) {
                      $q->where('year_id', $target_year_id);
                  });
            })
            ->pluck('student_id')
            ->flip()
            ->all();

//        dd($sourceStudentTerms, $existingTargetStudentIds);

        // ============================================================
        // 5. جلب الـ Terms الهدف مفهرسة بـ "{grade}_{arab}"
        //    لأن levels السنة الهدف مختلفة عن السنة المصدر،
        //    والمطابقة تتم بالصف ونوع العرب/غير العرب
        // ============================================================
        $targetTerms = Term::with('level')
            ->where('round', $target_round)
            ->whereHas('level', function ($q) use ($target_year_id) {
                $q->where('year_id', $target_year_id);
            })
            ->get()
            ->keyBy(function ($term) {
                return $term->level->grade . '_' . (int) $term->level->arab;
            });

        // ============================================================
        // 6. بدء النقل
        // ============================================================
        $transferred = 0;
        $skipped     = 0;

        foreach ($sourceStudentTerms as $sourceStudentTerm) {

            // فحص: هل الطالب موجود مسبقاً في السنة والجولة الهدف؟
            if (isset($existingTargetStudentIds[$sourceStudentTerm->student_id])) {
                $skipped++;
                continue;
            }

            // تحديد الـ Term الهدف بمطابقة الصف ونوع العرب/غير العرب
            // من Level التابع لـ Term الخاص باختبار الطالب المصدر
            $sourceLevel = $sourceStudentTerm->term->level;
            $lookupKey   = $sourceLevel->grade . '_' . (int) $sourceLevel->arab;
            $targetTerm  = $targetTerms->get($lookupKey);

            if (!$targetTerm) {
                $skipped++;
                continue;
            }

            // تمرير الثابت كمتغير لضمان توفره داخل الـ closures في PHP 7.4
            $excludedTypes = self::EXCLUDED_TYPES;

            // تغليف كل عملية نقل في transaction لضمان الاتساق:
            // إما يتم النقل والتصحيح والحذف كاملاً، أو لا شيء
            DB::transaction(function () use ($sourceStudentTerm, $targetTerm, $excludedTypes) {

                // --------------------------------------------------------
                // أ. تحميل أسئلة الاختبار المصدر مع إجابات الطالب
                //    الأنواع المستثناة (fill_blank/sorting/article) لا تُحمَّل
                // --------------------------------------------------------
                $sourceStudentTermId = $sourceStudentTerm->id;

                $sourceQuestions = Question::with([
                    'tf_question',
                    'option_question',
                    'match_question',
                    'tf_question_result' => function ($q) use ($sourceStudentTermId) {
                        $q->where('student_term_id', $sourceStudentTermId);
                    },
                    'option_question_result' => function ($q) use ($sourceStudentTermId) {
                        $q->where('student_term_id', $sourceStudentTermId);
                    },
                    'match_question_result' => function ($q) use ($sourceStudentTermId) {
                        $q->where('student_term_id', $sourceStudentTermId);
                    },
                ])
                ->whereNotIn('type', $excludedTypes)
                ->where('term_id', $sourceStudentTerm->term_id)
                ->get();

                // --------------------------------------------------------
                // ب. فهرسة أسئلة المصدر بـ "{subject_id}_{type}" + ترتيب
                //    لمطابقة كل سؤال بما يقابله في الاختبار الجديد
                // --------------------------------------------------------
                $sourceQuestionsIndex = [];
                foreach ($sourceQuestions as $sq) {
                    $key = $sq->subject_id . '_' . $sq->type;
                    $sourceQuestionsIndex[$key][] = $sq;
                }

                // --------------------------------------------------------
                // ج. إنشاء StudentTerm جديد للطالب بالـ Term الهدف
                //    الـ trigger يضبط active_key = 0 تلقائياً عند الإدراج
                //    dates_at: نحتفظ بتاريخ التقديم من الاختبار القديم
                //    (correctStudentTerm يقرأ dates_at الموجودة ويُضيف corrected_at فوقها)
                // --------------------------------------------------------
                $sourceDates = $sourceStudentTerm->dates_at ?? [];

                $newStudentTerm = StudentTerm::create([
                    'student_id' => $sourceStudentTerm->student_id,
                    'term_id'    => $targetTerm->id,
                    'dates_at'   => [
                        'started_at'   => $sourceDates['started_at']   ?? null,
                        'submitted_at' => $sourceDates['submitted_at'] ?? $sourceStudentTerm->created_at->format('Y-m-d H:i:s'),
                    ],
                ]);

                // --------------------------------------------------------
                // د. جلب أسئلة الاختبار الجديد مع هياكل إجاباتها الصحيحة
                //    الأنواع المستثناة (fill_blank/sorting/article) لا تُجلب
                // --------------------------------------------------------
                $targetQuestions = Question::with([
                    'tf_question',
                    'option_question',
                    'match_question',
                ])
                ->whereNotIn('type', $excludedTypes)
                ->where('term_id', $targetTerm->id)
                ->get();

                // --------------------------------------------------------
                // هـ. مقارنة ونقل الإجابات سؤالاً بسؤال
                //    المطابقة: نفس subject_id + نفس type + نفس الترتيب
                // --------------------------------------------------------
                $positionCounters = [];

                foreach ($targetQuestions as $targetQuestion) {
                    $key = $targetQuestion->subject_id . '_' . $targetQuestion->type;

                    if (!isset($positionCounters[$key])) {
                        $positionCounters[$key] = 0;
                    }
                    $pos = $positionCounters[$key]++;

                    // لا يوجد سؤال مقابل في المصدر → تخطي (لا إجابة)
                    if (!isset($sourceQuestionsIndex[$key][$pos])) {
                        continue;
                    }

                    $sourceQuestion = $sourceQuestionsIndex[$key][$pos];

                    if ($targetQuestion->type === 'matching') {
                        // Matching: تحويل عدد الصح بنسبة تناسبية
                        // إذا اختلف عدد خيارات المصدر عن الهدف
                        // (match_question محمّل مسبقاً → count() لا يُطلق query)
                        $sourceTotal   = $sourceQuestion->match_question->count();
                        $correctCount  = $this->getCorrectMatchCount($sourceQuestion);
                        $targetTotal   = $targetQuestion->match_question->count();

                        $scaledCorrect = ($sourceTotal > 0)
                            ? (int) round($correctCount / $sourceTotal * $targetTotal)
                            : 0;

                        $this->transferMatchingAnswer(
                            $targetQuestion,
                            $newStudentTerm,
                            $sourceStudentTerm->student_id,
                            $scaledCorrect
                        );
                    } else {
                        // true_false / multiple_choice: صح كامل أو خطأ كامل
                        $wasCorrect = $this->wasStudentCorrect($sourceQuestion);
                        $this->transferAnswer(
                            $targetQuestion,
                            $newStudentTerm,
                            $sourceStudentTerm->student_id,
                            $wasCorrect
                        );
                    }
                }

                // --------------------------------------------------------
                // و. تصحيح الاختبار الجديد واحتساب الدرجة
                //    في حال فشل التصحيح يُرمى exception لـ rollback الـ transaction
                // --------------------------------------------------------
                $correctionData = (new CorrectionService())->correctStudentTerm($newStudentTerm);

                if (isset($correctionData['error']) && $correctionData['error']) {
                    throw new \RuntimeException(
                        'Correction failed for student ' . $sourceStudentTerm->student_id
                        . ': ' . ($correctionData['message'] ?? 'unknown error')
                    );
                }

                $newStudentTerm->update($correctionData);

                // --------------------------------------------------------
                // ز. حذف الاختبار القديم (soft delete)
                //    الـ trigger يضبط active_key = id تلقائياً
                //    CascadeSoftDeletes يتسلسل لكل النتائج المرتبطة:
                //    tf_results, option_results, match_results,
                //    sort_results, fill_blank_answers, article_results,
                //    standards, proctor_images
                // --------------------------------------------------------
                $sourceStudentTerm->delete();
            });

            $transferred++;
        }

        return response()->json([
            'status'      => 'done',
            'transferred' => $transferred,
            'skipped'     => $skipped,
            'total'       => $sourceStudentTerms->count(),
        ]);
    }

    /**
     * تحديد هل أجاب الطالب بشكل صحيح على سؤال true_false أو multiple_choice
     */
    private function wasStudentCorrect(Question $question): bool
    {
        switch ($question->type) {

            case 'true_false':
                if ($question->tf_question_result->isEmpty()) {
                    return false;
                }
                // result مخزن كـ boolean (0/1) في DB
                return (int) $question->tf_question->result === (int) $question->tf_question_result[0]->result;

            case 'multiple_choice':
                if ($question->option_question_result->isEmpty()) {
                    return false;
                }
                $selectedOption = $question->option_question
                    ->where('id', $question->option_question_result[0]->option_id)
                    ->first();
                return $selectedOption && (int) $selectedOption->result === 1;
        }

        return false;
    }

    /**
     * إحصاء عدد الأزواج الصحيحة في إجابة الطالب على سؤال matching في المصدر
     */
    private function getCorrectMatchCount(Question $question): int
    {
        $correctCount = 0;
        foreach ($question->match_question_result as $result) {
            $matched = $question->match_question
                ->where('id', $result->match_id)
                ->where('uid', $result->match_question_answer_uid)
                ->first();
            if ($matched) {
                $correctCount++;
            }
        }
        return $correctCount;
    }

    /**
     * نقل إجابة matching بحيث عدد الصح = $correctCount والباقي خطأ
     *
     * الخوارزمية:
     *   - أول $correctCount عناصر → uid العنصر نفسه (صح)
     *   - الباقي (مجموعة الخاطئين) → يتداولون UIDs بينهم فقط بالدوران
     *
     * ضمانات:
     *   1. كل uid مأخوذ من خيارات نفس السؤال الهدف فقط
     *   2. كل uid يُستخدم مرة واحدة (لا تكرار بين الصحيحين والخاطئين)
     *   3. كل إجابة خاطئة uid ≠ uid العنصر نفسه (طالما total > 1)
     *   4. $correctCount لا يتجاوز العدد الكلي للعناصر
     */
    private function transferMatchingAnswer(
        Question $targetQuestion,
        StudentTerm $newStudentTerm,
        int $studentId,
        int $correctCount
    ): void {
        $matchItems   = $targetQuestion->match_question->values();
        $total        = $matchItems->count();

        if ($total === 0) {
            return;
        }

        // تقييد $correctCount بحد أقصى = عدد العناصر الكلي
        $correctCount = min($correctCount, $total);
        $wrongCount   = $total - $correctCount;

        foreach ($matchItems as $index => $matchItem) {
            if ($index < $correctCount) {
                // صح: uid العنصر نفسه (من خيارات نفس السؤال)
                $answerUid = $matchItem->uid;
            } else {
                if ($wrongCount > 1) {
                    // الخاطئون يتداولون UIDs بينهم بالدوران ← لا يتكرر uid مع الصحيحين
                    // مثال: خاطئون [C,D,E] → C يأخذ uid_D، D يأخذ uid_E، E يأخذ uid_C
                    $wrongPos        = $index - $correctCount;
                    $rotatedWrongPos = ($wrongPos + 1) % $wrongCount;
                    $answerUid       = $matchItems[$correctCount + $rotatedWrongPos]->uid;
                } else {
                    // عنصر خاطئ واحد: يأخذ uid العنصر التالي دورياً من كل العناصر
                    // (مختلف عن uid نفسه طالما total > 1)
                    $answerUid = $matchItems[($index + 1) % $total]->uid;
                }
            }

            MatchQuestionResult::create([
                'student_id'                => $studentId,
                'student_term_id'           => $newStudentTerm->id,
                'question_id'               => $targetQuestion->id,
                'match_id'                  => $matchItem->id,
                'match_question_answer_uid' => $answerUid,
            ]);
        }
    }

    /**
     * نقل إجابة true_false أو multiple_choice
     * صح في المصدر → الإجابة الصحيحة في الجديد
     * خطأ في المصدر → إجابة خاطئة في الجديد
     */
    private function transferAnswer(
        Question $targetQuestion,
        StudentTerm $newStudentTerm,
        int $studentId,
        bool $wasCorrect
    ): void {
        switch ($targetQuestion->type) {

            case 'true_false':
                // result مخزن كـ boolean (0/1) → نقلب القيمة لإعطاء إجابة خاطئة
                $correctResult = (int) $targetQuestion->tf_question->result;
                $answer        = $wasCorrect ? $correctResult : ($correctResult === 1 ? 0 : 1);
                TFQuestionResult::create([
                    'student_id'      => $studentId,
                    'student_term_id' => $newStudentTerm->id,
                    'question_id'     => $targetQuestion->id,
                    'result'          => $answer,
                ]);
                break;

            case 'multiple_choice':
                // result في option_questions: 1 = صحيح، 0 = خاطئ
                $option = $wasCorrect
                    ? $targetQuestion->option_question->where('result', 1)->first()
                    : $targetQuestion->option_question->where('result', 0)->first();
                if ($option) {
                    OptionQuestionResult::create([
                        'student_id'      => $studentId,
                        'student_term_id' => $newStudentTerm->id,
                        'question_id'     => $targetQuestion->id,
                        'option_id'       => $option->id,
                    ]);
                }
                break;
        }
    }
}
