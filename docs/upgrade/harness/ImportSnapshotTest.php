<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Upgrade harness (not part of the project): imports a students file through
 * the manager screen (xlsx, then the same rows as an Arabic csv; valid and
 * invalid rows), then updates the created students, and writes what was
 * stored to SNAPSHOT_DIR/import.txt. Everything is rolled back.
 */
class ImportSnapshotTest extends TestCase
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

    public function test_import()
    {
        $manager = SmokeRoutesTest::manager();
        $school = SmokeRoutesTest::school();
        $yearId = $school->available_year_id;

        // Name, Student ID, Grade, Nationality, Date Of Birth, Gender, Citizen,
        // SEN, Arab, Grade Name, G&T
        $rows = [
            ['Upgrade Check Alpha One', 'UPG-1001', 3, 'Emirati', 42005, 'Boy', 'yes', 'no', 'Yes', '3A', 'no'],
            ['Upgrade Check Beta Two', 'UPG-1002', 5, 'Egyptian', '2015-04-12', 'female', 'N', 'Y', 'no', '5B', '1'],
            [null, 'UPG-1003', 13, 'Syrian', '2014-01-01', 'boy', 'yes', 'no', 'yes', '13A', 'no'],
            ['Upgrade Check Gamma Three', 'UPG-1004', 7, 'Jordanian', '31/12/2012', 'g', '0', '0', '1', '7C', '0'],
            ['Upgrade Check Delta Four', 'UPG-1005', 2, 'Omani', 'not a date', 'm', 'no', 'no', 'yes', '2D', 'no'],
            ['Upgrade Check Epsilon Five', 'UPG-1006', 4, 'Saudi', '2016-02-02', 'x', 'no', 'no', 'yes', '4E', 'no'],
            ['طالب فحص الترقية', 'UPG-1007', 6, 'إماراتي', null, 1, 1, 0, 0, '6F', 0],
            ['Upgrade Check Zeta Six', 'UPG-1008', 8, 'Indian', '2011-07-07', 2, 'maybe', 'no', 'no', '8G', 'no'],
            ['Upgrade Check Eta Seven', null, 9, 'Lebanese', '2010-03-03', 'boy', 'no', 'no', 'no', '9H', 'no'],
        ];

        $out = [];
        $out['create xlsx'] = $this->import($manager, $school, $yearId, $rows, 'Xlsx',
            ['process_type' => 'create', 'status' => 'create', 'with_abt_id' => 0, 'username_type' => 'student_name']);

        // update the rows just created, found by their student id
        $rows[0][2] = 4;
        $rows[0][9] = '4A';
        $rows[1][7] = 'no';
        $rows[6][0] = 'طالب فحص الترقية المعدل';
        $out['update csv'] = $this->import($manager, $school, $yearId, $rows, 'Csv',
            // the form always sends with_abt_id; without it the import fails with a 500 on both versions
            ['process_type' => 'update', 'status' => 'update', 'with_abt_id' => 0, 'username_type' => 'student_name', 'search_by_column' => 'student_id']);

        // the same file again as new students, by student id usernames
        foreach ($rows as $i => $row) {
            if ($row[1]) {
                $rows[$i][1] = str_replace('UPG-', 'UPH-', $row[1]);
            }
        }
        $out['create csv'] = $this->import($manager, $school, $yearId, $rows, 'Csv',
            ['process_type' => 'create', 'status' => 'create', 'with_abt_id' => 0, 'username_type' => 'student_id']);

        file_put_contents(getenv('SNAPSHOT_DIR') . '/import.txt', json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $this->assertTrue(true);
    }

    private function import($manager, $school, $yearId, array $rows, $type, array $options)
    {
        $ext = strtolower($type);
        $tmp = sys_get_temp_dir() . '/students-upgrade-check-' . getmypid() . '.' . $ext;
        $book = \PhpOffice\PhpSpreadsheet\IOFactory::load(public_path('Students Example.xlsx'));
        $sheet = $book->getActiveSheet();
        foreach ($rows as $i => $row) {
            $sheet->fromArray($row, null, 'A' . ($i + 2), true);
        }
        \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($book, $type)->save($tmp);
        $mime = $type === 'Csv' ? 'text/csv' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        $file = new UploadedFile($tmp, 'Students Example.' . $ext, $mime, null, true);

        $maxStudent = (int) DB::table('students')->max('id');
        $maxFile = (int) DB::table('student_import_files')->max('id');
        $before = $this->students($school);

        $this->actingAs($manager, 'manager');
        $response = $this->post('/manager/students_files_import', ['school_id' => $school->id, 'year_id' => $yearId, 'file' => $file] + $options);

        $out = ['status' => $response->getStatusCode(), 'location' => $response->headers->get('Location')];
        try {
            $e = $response->exception;
        } catch (\Throwable $ignored) {
            $e = null;
        }
        $out['exception'] = $e ? get_class($e) . ': ' . $e->getMessage() : null;
        $out['message'] = session('message');
        $out['errors'] = is_object(session('errors')) ? session('errors')->getBag('default')->toArray() : null;

        $importFile = DB::table('student_import_files')->where('id', '>', $maxFile)->first();
        if ($importFile) {
            $row = (array) $importFile;
            $path = public_path($row['path']);
            unset($row['id'], $row['created_at'], $row['updated_at'], $row['file_name'], $row['path']);
            $out['file'] = $row;
            $strip = function ($r) {
                $r = (array) $r;
                unset($r['id'], $r['student_import_file_id'], $r['created_at'], $r['updated_at']);
                return $r;
            };
            $out['logs'] = DB::table('student_import_file_logs')->where('student_import_file_id', $importFile->id)->orderBy('id')->get()->map($strip)->all();
            $out['file_errors'] = DB::table('import_student_file_errors')->where('student_import_file_id', $importFile->id)->orderBy('id')->get()->map($strip)->all();
            if (is_file($path)) {
                unlink($path);
            }
        }
        $out['new_students'] = DB::table('students')->where('id', '>', $maxStudent)->orderBy('id')->get()
            ->map(function ($r) { return $this->clean((array) $r); })->all();
        $after = $this->students($school);
        $out['changed_students'] = $after === $before ? 'none' : $after;
        $this->flushSession();
        ResponseSnapshotTest::resetAuth();
        @unlink($tmp);

        return $out;
    }

    private function students($school)
    {
        return DB::table('students')->where('school_id', $school->id)
            ->where(function ($q) {
                $q->where('id_number', 'like', 'UPG-%')->orWhere('id_number', 'like', 'UPH-%');
            })
            ->orderBy('id')->get()
            ->map(function ($r) { return $this->clean((array) $r); })->all();
    }

    private function clean(array $r)
    {
        unset($r['id'], $r['created_at'], $r['updated_at'], $r['password'], $r['file_id'], $r['remember_token'], $r['abt_id']);
        // usernames may end with random digits
        $r['email'] = preg_replace('/\d+(@|$)/', 'N$1', (string) $r['email']);

        return $r;
    }
}
