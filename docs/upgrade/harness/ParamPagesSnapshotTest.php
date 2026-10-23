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
        $importFile = DB::table('import_student_files')->whereNull('deleted_at')->orderByDesc('id')->value('id');
        $activity = DB::table('activity_log')->orderByDesc('id')->value('id');
        $batch = DB::table('student_term_upgrades')->orderByDesc('id')->value('id');
        $types = DB::table('schools')->whereNull('deleted_at')->distinct()->orderBy('type')->pluck('type')->all();
        $compare = http_build_query(['schools' => $types, 'years' => [$yearId - 1, $yearId], 'levels' => [$grade, $grade + 1]]);
        $reportData = rawurlencode(json_encode(['report_type' => 'student']));

        $pages = [
            'manager' => [
                "manager/activity-log/$activity",
                "manager/edit-permissions/{$manager->id}",
                "manager/inspection/{$inspection->id}/edit",
                "manager/level/$levelId/edit",
                "manager/manager/{$manager->id}/edit",
                "manager/marking_requests/$markingRequest",
                "manager/marking_requests/$markingRequest/edit",
                "manager/school/{$school->id}/edit",
                "manager/school/{$school->id}/scheduling",
                "manager/school/report/{$school->id}/$yearId?report_type=1",
                "manager/student/$studentId/activity-records",
                "manager/student/$studentId/card",
                "manager/student/$studentId/edit",
                "manager/student_import/$importFile",
                "manager/student_import/$importFile/show_logs",
                "manager/student_report/$studentId",
                "manager/student_term/{$attempt->id}/certificate",
                "manager/student_term/{$attempt->id}/show",
                "manager/student_term/upgrade/batches/$batch",
                "manager/term/$termId/edit",
                "manager/term/$termId/preview",
                "manager/term/$termId/question/edit",
                "manager/term/$termId/standard",
                "manager/terms_names/$levelId",
                "manager/year/$yearId/edit",
                "manager/compare_result?$compare",
                "manager/student?school_id={$school->id}&grade=$grade",
                "manager/student_term/corrected?school_id={$school->id}&year_id=$yearId",
                "manager/students_not_started_yet?school_id={$school->id}&year_id=$yearId",
            ],
            'school' => [
                "school/marking_request/$markingRequest",
                "school/marking_request/$markingRequest/edit",
                "school/student/$studentId",
                "school/student/$studentId/activity-records",
                "school/student/$studentId/card",
                "school/student/$studentId/edit",
                "school/student_report/$studentId",
                "school/student_term/{$attempt->id}/certificate",
                "school/data_analysis/report?year_id=$yearId&report_type=1",
                "school/student_term/corrected?year_id=$yearId",
                "school/student_term/marks?year_id=$yearId",
            ],
            'inspection' => [
                "inspection/schools_report/$yearId?school_id={$school->id}&report_type=1",
                "inspection/student_report/$studentId",
                "inspection/combined_result?school_id={$school->id}&years=$yearId&levels[]=$grade",
                "inspection/student_term?school_id={$school->id}&year_id=$yearId",
            ],
            'guest' => [
                "user/$studentId/report?data=$reportData",
                "user/$certified/certificate",
                "user/$anyAttempt/certificate",
                "user/$certified/certificate?language=ar",
            ],
            // impersonation and language switches change the session: last
            'manager-login' => [
                "manager/school/{$school->id}/login",
                "manager/student/$studentId/login",
                "manager/inspection_login/{$inspection->id}",
                "manager/lang/ar",
            ],
            'school-login' => [
                "school/student/$studentId/login",
            ],
            'inspection-login' => [
                "inspection/school/login/{$school->id}",
            ],
            'guest-login' => [
                'lang/ar',
            ],
        ];

        $users = ['manager' => $manager, 'school' => $school, 'inspection' => $inspection, 'student' => $student, 'guest' => null];

        file_put_contents("$dir/_ids.txt", json_encode(compact('studentId', 'termId', 'levelId', 'yearId', 'certified', 'anyAttempt', 'markingRequest', 'importFile', 'activity', 'batch') + ['attempt' => $attempt->id, 'school' => $school->id, 'manager' => $manager->id, 'inspection' => $inspection->id]));

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
                    $name = $guard . '__' . $mode . '__' . preg_replace('/[^A-Za-z0-9_-]+/', '_', $uri);
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
