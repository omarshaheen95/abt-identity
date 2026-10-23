<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Upgrade harness (not part of the project): runs the Excel exports of the
 * three panels (filtered to one school and year where they take filters) and
 * writes every cell value of the produced workbooks to SNAPSHOT_DIR, so two
 * versions of the app can be diffed.
 */
class ExportSnapshotTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        // Reported exceptions are posted to Telegram by this job (sync queue).
        // Never send them from a test run.
        \Illuminate\Support\Facades\Bus::fake([\App\Jobs\LogMessageJob::class]);
    }

    public function test_exports()
    {
        $dir = getenv('SNAPSHOT_DIR');
        @mkdir($dir, 0777, true);

        $manager = SmokeRoutesTest::manager();
        $school = SmokeRoutesTest::school();
        $inspection = SmokeRoutesTest::inspection();
        $importFile = DB::table('import_student_files')->whereNull('deleted_at')->orderByDesc('id')->value('id');
        $yearId = $school->available_year_id;
        $termId = DB::table('terms')->join('levels', 'levels.id', '=', 'terms.level_id')
            ->where('levels.year_id', $yearId)->where('terms.questions', 1)->whereNull('terms.deleted_at')
            ->orderBy('terms.id')->value('terms.id');
        $years = ['year_id' => $yearId, 'assessment_year_id' => $yearId, 'term_year_id' => $yearId, 'register_year_id' => $yearId];
        $bySchool = ['school_id' => $school->id] + $years;

        $calls = [
            ['manager', $manager, 'post', '/manager/manager/export', []],
            ['manager', $manager, 'post', '/manager/school/export', []],
            ['manager', $manager, 'post', '/manager/level/export', []],
            ['manager', $manager, 'post', '/manager/inspection/export', []],
            ['manager', $manager, 'post', '/manager/term/export', []],
            ['manager', $manager, 'post', '/manager/question/export', ['term_id' => $termId]],
            ['manager', $manager, 'post', '/manager/standard/export', ['term_id' => $termId]],
            ['manager', $manager, 'post', '/manager/login_sessions/export', ['model_type' => 'App\Models\School']],
            ['manager', $manager, 'get', "/manager/student_import/$importFile/export_excel", []],
            ['manager', $manager, 'post', '/manager/student/export', $bySchool],
            ['manager', $manager, 'post', '/manager/student/marks_export', $bySchool],
            ['manager', $manager, 'post', '/manager/students_not_started_yet_export', $bySchool],
            ['manager', $manager, 'post', '/manager/student_term/student_term/export/corrected', $bySchool],
            ['manager', $manager, 'post', '/manager/student_term/student_term/export/uncorrected', $bySchool],
            ['manager', $manager, 'post', '/manager/student_term/exportExcelDetails', $bySchool],
            ['school', $school, 'post', '/school/student/export', $years],
            ['school', $school, 'post', '/school/student_term/export/corrected', $years],
            ['school', $school, 'post', '/school/student_term/export/uncorrected', $years],
            ['school', $school, 'post', '/school/student_term/marks/export', $years],
            ['school', $school, 'post', '/school/students_not_started_yet_export', $years],
            ['school', $school, 'post', '/school/assessment_submission/export', $years],
            ['inspection', $inspection, 'post', '/inspection/student_export', $bySchool],
            ['inspection', $inspection, 'post', '/inspection/student_term_export', $bySchool],
        ];

        foreach ($calls as [$guard, $user, $method, $url, $data]) {
            $this->actingAs($user, $guard);
            $response = $method === 'post' ? $this->post($url, $data) : $this->get($url . '?' . http_build_query($data));
            $out = "URL $url\nSTATUS " . $response->getStatusCode() . "\n";
            try {
                $e = $response->exception;
            } catch (\Throwable $ignored) {
                $e = null;
            }
            if ($e) {
                $out .= 'EXCEPTION ' . get_class($e) . ': ' . strtok($e->getMessage(), "\n") . "\n";
            }
            $base = $response->baseResponse;
            if ($base instanceof \Symfony\Component\HttpFoundation\BinaryFileResponse) {
                $out .= 'FILE ' . $response->headers->get('Content-Disposition') . "\n";
                $book = \PhpOffice\PhpSpreadsheet\IOFactory::load($base->getFile()->getPathname());
                foreach ($book->getAllSheets() as $sheet) {
                    $out .= "SHEET " . $sheet->getTitle() . ' ' . $sheet->getHighestColumn() . $sheet->getHighestRow() . "\n";
                    foreach ($sheet->toArray(null, false, false, false) as $row) {
                        $out .= json_encode($row, JSON_UNESCAPED_UNICODE) . "\n";
                    }
                }
            } else {
                $out .= 'LOCATION ' . $response->headers->get('Location') . "\n";
                $out .= 'BODY ' . substr((string) $response->getContent(), 0, 300) . "\n";
            }
            $name = $guard . '_' . trim(preg_replace('/[^A-Za-z0-9]+/', '_', $url), '_');
            file_put_contents("$dir/$name.txt", $out);
            $this->flushSession();
        }

        $this->assertTrue(true);
    }
}
