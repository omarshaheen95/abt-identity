<?php

namespace Tests\Feature;

use App\Models\Student;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Upgrade harness (not part of the project): a student opens an assessment they
 * have not started and submits it (true/false, choice and matching answers),
 * then a manager corrects it from the correction screen. Writes the responses,
 * the saved attempt, the answers and the marks to SNAPSHOT_DIR/exam.txt.
 * Everything is rolled back.
 */
class ExamFlowSnapshotTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        // Reported exceptions are posted to Telegram by this job (sync queue).
        // Never send them from a test run.
        \Illuminate\Support\Facades\Bus::fake([\App\Jobs\LogMessageJob::class]);
    }

    public function test_exam()
    {
        $out = [];
        [$student, $termId] = $this->pickStudentAndTerm();
        $this->assertNotNull($student, 'no student with an open assessment');
        $out['student'] = $student->id;
        $out['term'] = $termId;
        $out['reading_level_before'] = $student->reading_level;

        $this->actingAs($student, 'student');
        $r = $this->get("/student/start-term/$termId");
        $html = (string) $r->getContent();
        preg_match_all('/name="tf\[(\d+)\]"/', $html, $mt);
        preg_match_all('/name="option\[(\d+)\]"/', $html, $mo);
        preg_match_all('/name="re\[(\d+)\]\[([^\]]+)\]"/', $html, $mr);
        $out['term_page'] = [
            'status' => $r->getStatusCode(),
            'location' => $r->headers->get('Location'),
            'exception' => $r->exception ? get_class($r->exception) . ': ' . strtok($r->exception->getMessage(), "\n") : null,
            'tf_inputs' => count(array_unique($mt[1])),
            'option_inputs' => count(array_unique($mo[1])),
            're_inputs' => count(array_unique($mr[2])),
        ];
        $this->flushSession();

        // answers picked deterministically: some right, some wrong
        // question types: 1 true/false, 2 multiple choice, 3 matching
        $payload = ['started_at' => '2026-10-22 10:00:00'];
        $questions = DB::table('questions')->where('term_id', $termId)->whereNull('deleted_at')->orderBy('id')->get();
        foreach ($questions as $i => $q) {
            if ((string) $q->type === '1') {
                $payload['tf'][$q->id] = $q->id % 2;
            } elseif ((string) $q->type === '2') {
                $options = DB::table('options')->where('question_id', $q->id)->whereNull('deleted_at')->orderBy('id')->pluck('id');
                if ($options->count()) {
                    $payload['option'][$q->id] = $options[$i % $options->count()];
                }
            } elseif ((string) $q->type === '3') {
                // re[question][uid of the answer] = id of the match it was dropped on;
                // right when both are the same match, every third one is shifted
                $matches = DB::table('matches')->where('question_id', $q->id)->whereNull('deleted_at')->orderBy('id')->get(['id', 'uid']);
                foreach ($matches as $j => $match) {
                    $target = $j % 3 === 2 ? $matches[($j + 1) % $matches->count()] : $match;
                    $payload['re'][$q->id][$match->uid] = $target->id;
                }
            }
        }
        $ajax = ['X-Requested-With' => 'XMLHttpRequest', 'Accept' => 'application/json'];
        $this->actingAs(Student::query()->find($student->id), 'student');
        $r = $this->post("/student/save-term/$termId", $payload, $ajax);
        $out['save'] = [$r->getStatusCode(), $r->getContent()];
        $this->flushSession();

        // a second save keeps the first answers
        $this->actingAs(Student::query()->find($student->id), 'student');
        $r = $this->post("/student/save-term/$termId", ['option' => [], 'tf' => []], $ajax);
        $out['save_again'] = [$r->getStatusCode(), $r->getContent()];
        $this->flushSession();

        $attempt = DB::table('student_terms')->where('student_id', $student->id)->where('term_id', $termId)->first();
        if (!$attempt) {
            file_put_contents(getenv('SNAPSHOT_DIR') . '/exam.txt', json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $this->fail('the attempt was not saved, see exam.txt');
        }
        $out['answers_saved'] = $this->answers($attempt->id);

        // correct it from the manager's correction screen, without changing answers
        $this->actingAs(SmokeRoutesTest::manager(), 'manager');
        $r = $this->post("/manager/student_term/{$attempt->id}/correcting", []);
        $out['correcting'] = [$r->getStatusCode(), $r->headers->get('Location'), session('message'),
            $r->exception ? get_class($r->exception) . ': ' . strtok($r->exception->getMessage(), "\n") : null];
        $this->flushSession();
        // and a second time, as a re-correction does
        $this->actingAs(SmokeRoutesTest::manager(), 'manager');
        $r = $this->post("/manager/student_term/{$attempt->id}/correcting", []);
        $out['correcting_again'] = [$r->getStatusCode(), $r->headers->get('Location')];
        $this->flushSession();

        $a = (array) DB::table('student_terms')->where('id', $attempt->id)->first();
        $dates = json_decode($a['dates_at'], true);
        foreach (['submitted_at', 'corrected_at'] as $k) {
            $dates[$k] = isset($dates[$k]) ? 'SET' : null;
        }
        $dates['corrected_by'] = isset($dates['corrected_by']) ? 'MANAGER' : null;
        foreach (array_keys($a) as $k) {
            if (in_array($k, ['id', 'created_at', 'updated_at', 'dates_at'], true)) {
                unset($a[$k]);
            } elseif (strpos($k, 'v_') === 0 && $a[$k] !== null) {
                // generated from dates_at: the time of the run
                $a[$k] = 'SET';
            }
        }
        $out['attempt'] = $a;
        $out['dates_at'] = $dates;
        $out['reading_level_after'] = DB::table('students')->where('id', $student->id)->value('reading_level');
        $out['answers_after_correction'] = $this->answers($attempt->id);
        $out['student_standards'] = DB::table('student_standards')->where('student_term_id', $attempt->id)->whereNull('deleted_at')
            ->orderBy('standard_id')->orderBy('id')->get(['standard_id', 'mark'])->map(function ($r) { return (array) $r; })->all();

        // The open round has no standards: re-correct the latest corrected attempt
        // of the school that has some, to compare the per-standard marks.
        $old = DB::table('student_terms')
            ->join('students', 'students.id', '=', 'student_terms.student_id')
            ->where('students.school_id', SmokeRoutesTest::school()->id)
            ->where('student_terms.corrected', 1)->whereNull('student_terms.deleted_at')
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))->from('questions')->join('standards', 'standards.question_id', '=', 'questions.id')
                    ->whereColumn('questions.term_id', 'student_terms.term_id')->whereNull('standards.deleted_at');
            })
            ->orderByDesc('student_terms.id')->select('student_terms.*')->first();
        $before = DB::table('student_standards')->where('student_term_id', $old->id)->whereNull('deleted_at')
            ->orderBy('standard_id')->orderBy('id')->get(['standard_id', 'mark'])->map(function ($r) { return (array) $r; })->all();
        $this->actingAs(SmokeRoutesTest::manager(), 'manager');
        $r = $this->post("/manager/student_term/{$old->id}/correcting", []);
        $this->flushSession();
        $after = (array) DB::table('student_terms')->where('id', $old->id)->first();
        $out['recorrected'] = [
            'attempt' => $old->id,
            'status' => $r->getStatusCode(),
            'before' => ['total' => $old->total, 'grade' => $old->grade, 'expectations' => $old->expectations, 'reading_progress' => $old->reading_progress],
            'after' => ['total' => $after['total'], 'grade' => $after['grade'], 'expectations' => $after['expectations'], 'reading_progress' => $after['reading_progress']],
            'standards_before' => $before,
            'standards_after' => DB::table('student_standards')->where('student_term_id', $old->id)->whereNull('deleted_at')
                ->orderBy('standard_id')->orderBy('id')->get(['standard_id', 'mark'])->map(function ($r) { return (array) $r; })->all(),
        ];

        file_put_contents(getenv('SNAPSHOT_DIR') . '/exam.txt', json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private function answers($attemptId)
    {
        $rows = function ($table, $columns) use ($attemptId) {
            return DB::table($table)->where('student_term_id', $attemptId)->whereNull('deleted_at')->orderBy('question_id')->orderBy('id')
                ->get($columns)->map(function ($r) { return (array) $r; })->all();
        };

        return [
            'tf' => $rows('true_false_results', ['question_id', 'result']),
            'option' => $rows('option_results', ['question_id', 'option_id']),
            'match' => $rows('match_results', ['question_id', 'match_id', 'match_answer_uid']),
        ];
    }

    private function pickStudentAndTerm()
    {
        $school = SmokeRoutesTest::school();
        $students = Student::query()
            ->where('school_id', $school->id)
            ->where(function ($q) { $q->whereNull('demo')->orWhere('demo', 0); })
            ->orderByDesc('id')
            ->limit(300)
            ->get();
        foreach ($students as $student) {
            $this->actingAs($student, 'student');
            $html = (string) $this->get('/student/home')->getContent();
            $this->flushSession();
            if (!preg_match_all('#student/start-term/(\d+)#', $html, $m)) {
                continue;
            }
            foreach (array_unique($m[1]) as $termId) {
                $started = DB::table('student_terms')->where('student_id', $student->id)->where('term_id', $termId)->exists();
                if (!$started) {
                    return [$student, (int) $termId];
                }
            }
        }

        return [null, null];
    }
}
