<?php

namespace Tests\Feature;

use App\Models\Student;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Upgrade harness (not part of the project): saves the normalized body of GET
 * pages that take route parameters or query filters (edit forms, student and
 * school reports, certificates, cards, impersonation logins), with real ids
 * picked deterministically from the local database, into SNAPSHOT_DIR.
 */
class ParamPagesSnapshotTest extends TestCase
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

    public function test_snapshots()
    {
        $dir = getenv('SNAPSHOT_DIR');
        @mkdir($dir, 0777, true);

        $manager = SmokeRoutesTest::manager();
        $school = SmokeRoutesTest::school();
        $inspection = SmokeRoutesTest::inspection();

        // the latest corrected attempt of a student of that school, in the school's year
        $attempt = DB::table('student_terms')
            ->join('students', 'students.id', '=', 'student_terms.student_id')
            ->join('terms', 'terms.id', '=', 'student_terms.term_id')
            ->join('levels', 'levels.id', '=', 'terms.level_id')
            ->where('students.school_id', $school->id)
            ->where('levels.year_id', $school->available_year_id)
            ->whereNull('student_terms.deleted_at')->whereNull('students.deleted_at')
            ->where('student_terms.corrected', 1)
            ->orderByDesc('student_terms.id')
            ->select('student_terms.*')->first();
        $studentId = $attempt->student_id;
        $student = Student::query()->find($studentId);
        $termId = $attempt->term_id;
        $levelId = DB::table('terms')->where('id', $termId)->value('level_id');
        $yearId = DB::table('levels')->where('id', $levelId)->value('year_id');
        $grade = DB::table('levels')->where('id', $levelId)->value('grade');
        // an attempt that earns a certificate, and the latest attempt of all
        $certified = DB::table('student_terms')
            ->join('students', 'students.id', '=', 'student_terms.student_id')
            ->join('schools', 'schools.id', '=', 'students.school_id')
            ->where('students.school_id', $school->id)
            ->whereNull('student_terms.deleted_at')
            ->whereRaw('student_terms.total >= schools.certificate_mark')
            ->orderByDesc('student_terms.id')->value('student_terms.id');
        $anyAttempt = DB::table('student_terms')->whereNull('deleted_at')->orderByDesc('id')->value('id');
        $markingRequest = DB::table('marking_requests')->where('school_id', $school->id)->whereNull('deleted_at')->orderByDesc('id')->value('id')
            ?: DB::table('marking_requests')->whereNull('deleted_at')->orderByDesc('id')->value('id');
        $importFile = DB::table('student_import_files')->where('school_id', $school->id)->whereNull('deleted_at')->orderByDesc('id')->value('id');
        $importError = DB::table('student_import_files')->whereNotNull('error')->whereNull('deleted_at')->orderByDesc('id')->value('id');
        $activity = DB::table('activity_log')->orderByDesc('id')->value('id');
        $questionFile = DB::table('question_files')->whereNull('deleted_at')->orderByDesc('id')->value('id');
        $schoolStudent = DB::table('students')->where('school_id', $school->id)->whereNull('deleted_at')->orderBy('id')->value('id');
        $scheduling = DB::table('school_grades')->where('school_id', $school->id)->whereNull('deleted_at')->orderBy('id')->value('id');
        $reportToken = function_exists('encryptStudentId') ? encryptStudentId($studentId) : null;
        $years = [$yearId - 2, $yearId - 1, $yearId];
        $report = ['school_id' => $school->id, 'year_id' => $yearId, 'grades' => [$grade], 'student_type' => 2, 'generated_report_type' => 'attainment'];
        $q = function (array $data) {
            return http_build_query($data);
        };

        $pages = [
            'manager' => [
                "manager/activity-log/$activity",
                "manager/edit-permissions/{$manager->id}",
                "manager/edit_standards/$termId",
                "manager/get-levels-by-year/$yearId",
                "manager/get-terms-by-level/$levelId",
                "manager/inspection/{$inspection->id}/edit",
                "manager/level/$levelId/edit",
                "manager/manager/{$manager->id}/edit",
                "manager/marking_requests/$markingRequest",
                "manager/marking_requests/$markingRequest/edit",
                "manager/question-file/$questionFile",
                "manager/scheduling/{$school->id}",
                "manager/school/{$school->id}/edit",
                "manager/student-import-error/$importError",
                "manager/student-report/$studentId/card",
                "manager/student-report/$studentId/report",
                "manager/student/$studentId/activity-records",
                "manager/student/$studentId/card",
                "manager/student/$studentId/edit",
                "manager/student_term/{$attempt->id}/certificate",
                "manager/student_term/{$attempt->id}/edit",
                "manager/student_term/corrected?" . $q(['school_id' => $school->id, 'year_id' => $yearId]),
                "manager/student_term/uncorrected?" . $q(['school_id' => $school->id, 'year_id' => $yearId]),
                "manager/students_files_import/$importFile/show_logs",
                "manager/term/$termId/edit",
                "manager/term/$termId/preview",
                "manager/term/$termId/questions",
                "manager/term/$termId/questions_structure",
                "manager/terms_names/$levelId",
                "manager/year/$yearId/edit",
                "manager/student?" . $q(['school_id' => $school->id, 'grade' => $grade]),
                "manager/students_not_submitted_terms?" . $q(['school_id' => $school->id, 'year_id' => $yearId, 'round' => 'september']),
                "manager/attainment-report?" . $q($report),
                "manager/attainment-report?" . $q(['generated_report_type' => 'combined'] + $report),
                "manager/progress-report?" . $q($report),
                "manager/student-mark-report?" . $q($report),
                "manager/year-to-year-report?" . $q(['school_id' => $school->id, 'years' => [$yearId - 1, $yearId], 'grades' => [$grade], 'student_type' => 2, 'round' => 'may', 'generated_report_type' => 'attainment']),
                "manager/comparison-report?" . $q(['school_id' => $school->id, 'year_id' => $yearId, 'round' => 'may', 'gender' => 'all', 'student_type' => 2, 'curriculums' => [$school->curriculum_type], 'grades' => [$grade]]),
            ],
            'school' => [
                "school/marking_requests/$markingRequest",
                "school/marking_requests/$markingRequest/edit",
                "school/scheduling/$scheduling",
                "school/scheduling/$scheduling/edit",
                "school/student-report/$studentId/card",
                "school/student-report/$studentId/report",
                "school/student/$studentId/activity-records",
                "school/student/$studentId/card",
                "school/student/$studentId/edit",
                "school/student_term/{$attempt->id}/certificate",
                "school/student_term/corrected?" . $q(['year_id' => $yearId]),
                "school/student_term/uncorrected?" . $q(['year_id' => $yearId]),
                "school/attainment-report?" . $q($report),
                "school/progress-report?" . $q($report),
            ],
            'inspection' => [
                "inspection/school/{$school->id}/edit",
                "inspection/student-report/$studentId/card",
                "inspection/student-report/$studentId/report",
                "inspection/student/$studentId/edit",
                "inspection/student?" . $q(['school_id' => $school->id, 'year_id' => $yearId]),
                "inspection/attainment-report?" . $q($report),
            ],
            'student' => [
                'student/home',
            ],
            'guest' => [
                "user/$studentId/report",
                "user/$certified/certificate",
                "user/$anyAttempt/certificate",
                "student-cards?" . $q(['school_id' => $school->id, 'grade' => $grade, 'year_id' => $yearId]),
                "student-report?" . $q(['token' => $reportToken]),
                "student-report-card?" . $q(['token' => $reportToken]),
            ],
            // impersonation and language switches change the session: last
            'manager-login' => [
                "manager/school_login/{$school->id}",
                "manager/student-login/$studentId",
                "manager/login-inspection/{$inspection->id}",
                "manager/lang/ar",
            ],
            'school-login' => [
                "school/student-login/$schoolStudent",
                "school/lang/ar",
            ],
            'inspection-login' => [
                "inspection/school_login/{$school->id}",
                "inspection/student-login/$schoolStudent",
                "inspection/lang/en",
            ],
            'guest-login' => [
                'lang/ar',
            ],
        ];

        $users = ['manager' => $manager, 'school' => $school, 'inspection' => $inspection, 'student' => $student, 'guest' => null];

        file_put_contents("$dir/_ids.txt", json_encode(compact('studentId', 'termId', 'levelId', 'yearId', 'certified', 'anyAttempt', 'markingRequest', 'importFile', 'importError', 'activity', 'questionFile', 'schoolStudent', 'scheduling') + ['attempt' => $attempt->id, 'school' => $school->id, 'manager' => $manager->id, 'inspection' => $inspection->id]));

        foreach ($pages as $group => $uris) {
            $guard = explode('-', $group)[0];
            foreach ($uris as $uri) {
                foreach (['page', 'ajax'] as $mode) {
                    if ($users[$guard]) {
                        $this->actingAs($users[$guard], $guard);
                    }
                    $headers = $mode === 'ajax'
                        ? ['X-Requested-With' => 'XMLHttpRequest', 'Accept' => 'application/json, text/javascript, */*; q=0.01']
                        : [];
                    $url = '/' . $uri;
                    if ($mode === 'ajax') {
                        $url .= (strpos($url, '?') === false ? '?' : '&') . 'draw=1&start=0&length=10';
                    }
                    $response = $this->get($url, $headers);
                    try {
                        $e = $response->exception;
                    } catch (\Throwable $ignored) {
                        $e = null;
                    }
                    $body = (string) $response->getContent();
                    if ($response->baseResponse instanceof \Symfony\Component\HttpFoundation\BinaryFileResponse) {
                        $body = 'BINARY FILE ' . $response->headers->get('Content-Disposition');
                    } elseif (strpos((string) $response->headers->get('Content-Type'), 'pdf') !== false) {
                        $body = 'PDF ' . strlen($body) . ' bytes';
                    }
                    $authenticated = [];
                    foreach (['manager', 'school', 'inspection', 'student'] as $g) {
                        if (auth($g)->check()) {
                            $authenticated[] = $g . ':' . auth($g)->id();
                        }
                    }
                    // ids stay in the name: two pages of the same route (two certificates) must not share a file
                    // the QR token is encrypted with a random IV on every run
                    $shown = $reportToken ? str_replace(urlencode($reportToken), 'TOKEN', $uri) : $uri;
                    $url = str_replace((string) urlencode((string) $reportToken), 'TOKEN', $url);
                    $name = $guard . '__' . $mode . '__' . preg_replace('/[^A-Za-z0-9_-]+/', '_', $shown);
                    // long query strings would pass the Windows path limit
                    if (strlen($name) > 100) {
                        $name = substr($name, 0, 80) . '_' . md5($name);
                    }
                    file_put_contents("$dir/$name.txt",
                        'URL ' . $url . "\nSTATUS " . $response->getStatusCode() . "\nTYPE " . $response->headers->get('Content-Type') .
                        "\nLOCATION " . $response->headers->get('Location') .
                        "\nEXCEPTION " . ($e ? get_class($e) . ': ' . strtok($e->getMessage(), "\n") : '') .
                        "\nAUTH " . implode(' ', $authenticated) .
                        "\nSESSION " . json_encode(array_intersect_key(session()->all(), array_flip(['lang', 'locale', 'message', 'm-class'])), JSON_UNESCAPED_UNICODE) .
                        "\n\n" . $this->normalize($body, $response->headers->get('Content-Type')));
                    $this->flushSession();
                    foreach (['manager', 'school', 'inspection', 'student'] as $g) {
                        auth($g)->logout();
                    }
                    ResponseSnapshotTest::resetAuth();
                }
            }
        }

        $this->assertTrue(true);
    }

    private function normalize($body, $type)
    {
        if (strpos((string) $type, 'json') !== false) {
            $data = json_decode($body, true);
            if (is_array($data)) {
                unset($data['input'], $data['queries']);
                return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }
        }
        $body = preg_replace('/(name="_token"\s+value=")[^"]+/', '$1X', $body);
        $body = preg_replace('/(name="csrf-token"\s+content=")[^"]+/', '$1X', $body);
        $body = preg_replace("/(['\"]_token['\"]\s*:\s*['\"])[^'\"]+/", '$1X', $body);
        $body = preg_replace("/('X-CSRF-TOKEN'\s*:\s*')[^']+/", '$1X', $body);
        $body = preg_replace('/(_token=)[A-Za-z0-9]+/', '$1X', $body);
        $body = preg_replace('/pw_password_[0-9a-f]+/', 'pw_password_X', $body);
        $body = preg_replace('/"eyJpdiI6[A-Za-z0-9+\/=]+"/', '"ENCRYPTED"', $body);

        return preg_replace("/[ \t]+\r?\n/", "\n", $body);
    }
}
