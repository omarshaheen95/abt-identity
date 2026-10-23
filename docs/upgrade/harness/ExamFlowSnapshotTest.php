<?php

namespace Tests\Feature;

use App\Models\Student;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Upgrade harness (not part of the project): a student opens and submits two
 * assessments (one with true/false, multiple choice and matching questions,
 * one of the open round with standards), the manager corrects them with the
 * automatic correction and again from the correction screen, and an older
 * corrected attempt with standards is corrected again. Writes the saved
 * answers, marks and standards to SNAPSHOT_DIR/exam.txt. Everything is rolled
 * back.
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
        \Illuminate\Support\Carbon::setTestNow('2026-10-22 10:00:00');
    }

    public function test_exam()
    {
        $school = SmokeRoutesTest::school();
        $out = [];

        // the assessment with the most question types, and one of the open round
        $mixed = DB::table('questions')->join('terms', 'terms.id', '=', 'questions.term_id')
            ->whereNull('questions.deleted_at')->whereNull('terms.deleted_at')
            ->groupBy('questions.term_id')->orderByRaw('count(distinct questions.type) desc')->orderBy('questions.term_id')
            ->value('questions.term_id');
        $level = DB::table('levels')->where('year_id', $school->available_year_id)->where('grade', 4)->where('arab', 1)->whereNull('deleted_at')->first();
        $open = DB::table('terms')->where('level_id', $level->id)->where('active', 1)->whereNull('deleted_at')
            ->whereExists(function ($q) {
                $q->from('questions')->join('question_standards', 'question_standards.question_id', '=', 'questions.id')
                    ->whereColumn('questions.term_id', 'terms.id')->whereNull('questions.deleted_at');
            })->orderByDesc('id')->value('id');

        // an older corrected attempt with standards, picked before the new ones exist
        $old = DB::table('student_terms')->join('students', 'students.id', '=', 'student_terms.student_id')
            ->where('students.school_id', $school->id)->where('student_terms.corrected', 1)->whereNull('student_terms.deleted_at')
            ->whereExists(function ($q) {
                $q->from('student_term_standards')->whereColumn('student_term_standards.student_term_id', 'student_terms.id');
            })->orderByDesc('student_terms.id')->value('student_terms.id');

        foreach (['mixed' => $mixed, 'open round' => $open] as $label => $termId) {
            $out[$label] = $this->sit($school, $termId);
        }

        // corrected again
        $before = $this->attempt($old);
        $yearId = DB::table('student_terms')->join('terms', 'terms.id', '=', 'student_terms.term_id')->join('levels', 'levels.id', '=', 'terms.level_id')
            ->where('student_terms.id', $old)->value('levels.year_id');
        $studentId = DB::table('student_terms')->where('id', $old)->value('student_id');
        $out['old attempt'] = [
            'before' => $before,
            'auto correct' => $this->manager('post', '/manager/auto_correct_student_term', ['year_id' => $yearId, 'student_id' => $studentId]),
            'after' => $this->attempt($old),
        ];

        file_put_contents(getenv('SNAPSHOT_DIR') . '/exam.txt', json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $this->assertTrue(true);
    }

    /** A student of the school sits the assessment, then the manager corrects it. */
    private function sit($school, $termId)
    {
        $term = DB::table('terms')->find($termId);
        $level = DB::table('levels')->find($term->level_id);
        $student = DB::table('students')->where('school_id', $school->id)->whereNull('deleted_at')
            ->whereNotExists(function ($q) use ($termId) {
                $q->from('student_terms')->whereColumn('student_terms.student_id', 'students.id')->where('term_id', $termId);
            })->orderBy('id')->first();

        // make the assessment available to that student (rolled back)
        DB::table('students')->where('id', $student->id)->update(['level_id' => $level->id, 'year_id' => $level->year_id, 'demo' => 0, 'assessment_opened' => 0]);
        DB::table('schools')->where('id', $school->id)->update(['available_year_id' => $level->year_id]);
        DB::table('terms')->where('id', $termId)->update(['active' => 1]);
        DB::table('school_grades')->where('school_id', $school->id)->where('grade', $level->grade)->where('arab', $level->arab)
            ->update([$term->round => 1]);

        $out = ['term' => $termId, 'round' => $term->round];
        $this->actingAs(Student::query()->find($student->id), 'student');
        $r = $this->get("/student/term/$termId");
        $html = (string) $r->getContent();
        $out['term page'] = [
            'status' => $r->getStatusCode(), 'location' => $r->headers->get('Location'),
            'message' => session('term-message'),
            'questions' => preg_match_all('/questions\[(\d+)\]\[type\]/', $html),
        ];
        $this->flushSession();
        ResponseSnapshotTest::resetAuth();

        // answers picked deterministically: some right, some wrong
        $payload = ['started_at' => '2026-10-22 09:30:00', 'questions' => []];
        $questions = DB::table('questions')->where('term_id', $termId)->whereNull('deleted_at')->orderBy('id')->get();
        foreach ($questions as $i => $q) {
            $answer = ['type' => $q->type];
            if ($q->type === 'true_false') {
                $answer['answer'] = $q->id % 2;
            } elseif ($q->type === 'multiple_choice') {
                $options = DB::table('option_questions')->where('question_id', $q->id)->whereNull('deleted_at')->orderBy('id')->pluck('id');
                if ($options->count()) {
                    $answer['answer_option_id'] = $options[$i % $options->count()];
                }
            } elseif ($q->type === 'matching') {
                // options[uid of the answer] = id of the match it was dropped on;
                // right when both are the same row, every third one is shifted
                $matches = DB::table('match_questions')->where('question_id', $q->id)->whereNull('deleted_at')->orderBy('id')->get(['id', 'uid']);
                foreach ($matches as $j => $match) {
                    $target = $j % 3 === 2 ? $matches[($j + 1) % $matches->count()] : $match;
                    $answer['options'][$match->uid] = $target->id;
                }
            } elseif ($q->type === 'sorting') {
                $sorts = DB::table('sort_questions')->where('question_id', $q->id)->whereNull('deleted_at')->orderBy('id')->pluck('uid')->all();
                if ($i % 2) {
                    $sorts = array_reverse($sorts);
                }
                foreach ($sorts as $k => $uid) {
                    $answer['options'][$uid] = $k + 1;
                }
            } elseif ($q->type === 'fill_blank') {
                $blanks = DB::table('fill_blank_questions')->where('question_id', $q->id)->whereNull('deleted_at')->orderBy('id')->get(['id', 'uid']);
                foreach ($blanks as $k => $blank) {
                    $answer['blanks'][$blank->uid] = $blanks[($k + $i) % $blanks->count()]->id;
                }
            }
            $payload['questions'][$q->id] = $answer;
        }
        $out['types'] = array_count_values($questions->pluck('type')->all());

        $this->actingAs(Student::query()->find($student->id), 'student');
        $r = $this->post("/student/term_save/$termId", $payload, ['X-Requested-With' => 'XMLHttpRequest', 'Accept' => 'application/json']);
        $out['save'] = [$r->getStatusCode(), json_decode((string) $r->getContent(), true)];
        $this->flushSession();
        ResponseSnapshotTest::resetAuth();

        $attemptId = DB::table('student_terms')->where('student_id', $student->id)->where('term_id', $termId)->value('id');
        if (!$attemptId) {
            $out['attempt'] = null;
            return $out;
        }
        $out['saved'] = $this->attempt($attemptId);
        $out['auto correct'] = $this->manager('post', '/manager/auto_correct_student_term', ['year_id' => $level->year_id, 'student_id' => $student->id]);
        $out['after auto correct'] = $this->attempt($attemptId);
        // the correction screen sends the answers back, with the subject of each
        // question and the id of the saved true/false and choice results
        $correction = $payload['questions'];
        foreach ($questions as $q) {
            $correction[$q->id]['subject_id'] = $q->subject_id;
            $table = ['true_false' => 't_f_question_results', 'multiple_choice' => 'option_question_results'][$q->type] ?? null;
            if ($table) {
                $resultId = DB::table($table)->where('student_term_id', $attemptId)->where('question_id', $q->id)->value('id');
                if ($resultId) {
                    $correction[$q->id]['question_result_id'] = $resultId;
                }
            }
        }
        $out['correction screen'] = $this->manager('post', "/manager/update-student-term/$attemptId", ['questions' => $correction]);
        $out['after correction screen'] = $this->attempt($attemptId);

        return $out;
    }

    private function manager($method, $url, $data)
    {
        $this->actingAs(SmokeRoutesTest::manager(), 'manager');
        $r = $this->call(strtoupper($method), $url, $data, [], [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest', 'HTTP_ACCEPT' => 'application/json']);
        try {
            $e = $r->exception;
        } catch (\Throwable $ignored) {
            $e = null;
        }
        $out = [$r->getStatusCode(), json_decode((string) $r->getContent(), true) ?? substr((string) $r->getContent(), 0, 200),
            $e && !($e instanceof \Illuminate\Validation\ValidationException) ? get_class($e) . ': ' . strtok($e->getMessage(), "\n") : null];
        $this->flushSession();
        ResponseSnapshotTest::resetAuth();

        return $out;
    }

    /** The attempt row (without times of the run), its answers and its standards. */
    private function attempt($id)
    {
        $a = (array) DB::table('student_terms')->where('id', $id)->first();
        $dates = json_decode($a['dates_at'], true) ?: [];
        foreach (['submitted_at', 'corrected_at'] as $k) {
            $dates[$k] = isset($dates[$k]) ? 'SET' : null;
        }
        $a['dates_at'] = $dates;
        foreach (array_keys($a) as $k) {
            if (in_array($k, ['id', 'created_at', 'updated_at', 'student_id'], true)) {
                unset($a[$k]);
            } elseif (strpos($k, 'v_') === 0 && $a[$k] !== null && $k !== 'v_started_at') {
                // generated from dates_at
                $a[$k] = 'SET';
            }
        }
        $strip = function ($row) {
            $row = (array) $row;
            unset($row['id'], $row['student_term_id'], $row['student_id'], $row['created_at'], $row['updated_at']);
            return $row;
        };
        $answers = [];
        foreach (['t_f_question_results', 'option_question_results', 'match_question_results', 'sort_question_results', 'fill_blank_answers'] as $table) {
            $answers[$table] = DB::table($table)->where('student_term_id', $id)->whereNull('deleted_at')->orderBy('question_id')->orderBy('id')->get()->map($strip)->all();
        }
        $a['answers'] = $answers;
        $a['standards'] = DB::table('student_term_standards')->where('student_term_id', $id)->whereNull('deleted_at')->orderBy('question_standard_id')->get()->map($strip)->all();

        return $a;
    }
}
