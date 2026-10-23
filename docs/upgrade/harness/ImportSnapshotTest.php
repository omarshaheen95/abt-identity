<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Upgrade harness (not part of the project): imports a students file built from
 * public/Students Example - Reading.xlsx through the manager "student import"
 * screen (a create run with valid and invalid rows, then an update run of the
 * same file) and writes what it created to SNAPSHOT_DIR/import.txt. Database
 * changes are rolled back and the uploaded copies are deleted.
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
    }

    public function test_import()
    {
        $manager = SmokeRoutesTest::manager();
        $school = SmokeRoutesTest::school();
        $yearId = $school->available_year_id;

        // Name, Student ID, Grade, Learning Years, Reading Level, Nationality,
        // Date Of Birth, Gender, Citizen, SEN, Arab, Grade Name, G&T
        $rows = [
            ['Upgrade Check Alpha One', 'UPG-1001', 3, 2, 3, 'Emirati', 42005, 'Boy', 'yes', 'no', 'Yes', '3A', 'no'],
            ['Upgrade Check Beta Two', 'UPG-1002', 5, null, 4, 'Egyptian', '2015-04-12', 'female', 'N', 'Y', 'no', '5B', '1'],
            [null, 'UPG-1003', 13, 1, 13, 'Syrian', '2014-01-01', 'boy', 'yes', 'no', 'yes', '13A', 'no'],
            ['Upgrade Check Gamma Three', 'UPG-1004', 7, 3, 6, 'Jordanian', '31/12/2012', 'g', '0', '0', '1', '7C', '0'],
            ['Upgrade Check Delta Four', 'UPG-1005', 2, 1, 2, 'Omani', 'not a date', 'm', 'no', 'no', 'yes', '2D', 'no'],
            ['Upgrade Check Epsilon Five', 'UPG-1006', 4, 2, 4, 'Saudi', '2016-02-02', 'x', 'no', 'no', 'yes', '4E', 'no'],
            ['اسم عربي', 'UPG-1007', 6, 2, 5, 'Emirati', null, 1, 1, 0, 0, '6F', 0],
            ['Upgrade Check Zeta Six', 'UPG-1008', 8, null, 8, 'Indian', '2011-07-07', 2, 'maybe', 'no', 'no', '8G', 'no'],
            ['Upgrade Check Eta Seven', null, 9, 4, 9, 'Lebanese', '2010-03-03', 'boy', 'no', 'no', 'no', '9H', 'no'],
        ];

        $out = [];
        $out['create'] = $this->import($manager, $school, $yearId, $rows, ['process_type' => 'create', 'status' => 'create', 'username_type' => 'student_name']);

        // update the rows just created, found by their student id
        $rows[0][2] = 4;
        $rows[0][11] = '4A';
        $rows[1][9] = 'no';
        $out['update'] = $this->import($manager, $school, $yearId, $rows, ['process_type' => 'update', 'status' => 'update', 'username_type' => 'student_name', 'search_by_column' => 'student_id']);

        file_put_contents(getenv('SNAPSHOT_DIR') . '/import.txt', json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $this->assertTrue(true);
    }

    private function import($manager, $school, $yearId, array $rows, array $options)
    {
        $tmp = sys_get_temp_dir() . '/students-example-' . getmypid() . '.xlsx';
        $book = \PhpOffice\PhpSpreadsheet\IOFactory::load(public_path('Students Example - Reading.xlsx'));
        $sheet = $book->getActiveSheet();
        foreach ($rows as $i => $row) {
            $sheet->fromArray($row, null, 'A' . ($i + 2), true);
        }
        \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($book, 'Xlsx')->save($tmp);
        $file = new UploadedFile($tmp, 'Students Example - Reading.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        $maxStudent = (int) DB::table('students')->max('id');
        $maxFile = (int) DB::table('import_student_files')->max('id');
        $before = DB::table('students')->where('school_id', $school->id)->where('id_number', 'like', 'UPG-%')->orderBy('id')->get()
            ->map(function ($r) { return $this->clean((array) $r); })->all();

        $this->actingAs($manager, 'manager');
        $response = $this->post('/manager/student_import', ['school_id' => $school->id, 'year_id' => $yearId, 'file' => $file] + $options);

        $out = ['status' => $response->getStatusCode(), 'location' => $response->headers->get('Location')];
        try {
            $e = $response->exception;
        } catch (\Throwable $ignored) {
            $e = null;
        }
        $out['exception'] = $e ? get_class($e) . ': ' . $e->getMessage() : null;
        $out['message'] = session('message');
        $out['errors'] = is_object(session('errors')) ? session('errors')->getBag('default')->toArray() : null;

        $importFile = DB::table('import_student_files')->where('id', '>', $maxFile)->first();
        if ($importFile) {
            $row = (array) $importFile;
            $path = public_path($row['file_path']);
            unset($row['id'], $row['created_at'], $row['updated_at'], $row['file_name'], $row['file_path']);
            $out['file'] = $row;
            $out['logs'] = DB::table('import_student_file_logs')->where('import_student_file_id', $importFile->id)->orderBy('id')->get()
                ->map(function ($r) { $r = (array) $r; unset($r['id'], $r['import_student_file_id'], $r['created_at'], $r['updated_at']); return $r; })->all();
            $out['file_errors'] = DB::table('import_student_file_errors')->where('import_student_file_id', $importFile->id)->orderBy('id')->get()
                ->map(function ($r) { $r = (array) $r; unset($r['id'], $r['import_student_file_id'], $r['created_at'], $r['updated_at']); return $r; })->all();
            if (is_file($path)) {
                unlink($path);
            }
        }
        $out['new_students'] = DB::table('students')->where('id', '>', $maxStudent)->orderBy('id')->get()
            ->map(function ($r) { return $this->clean((array) $r); })->all();
        $after = DB::table('students')->where('school_id', $school->id)->where('id_number', 'like', 'UPG-%')->orderBy('id')->get()
            ->map(function ($r) { return $this->clean((array) $r); })->all();
        $out['changed_students'] = $after === $before ? 'none' : $after;
        $this->flushSession();
        @unlink($tmp);

        return $out;
    }

    private function clean(array $r)
    {
        unset($r['id'], $r['created_at'], $r['updated_at'], $r['password'], $r['import_student_file_id'], $r['remember_token'], $r['abt_id']);
        // usernames end with random digits
        $r['email'] = preg_replace('/\d+(@|$)/', 'N$1', $r['email']);

        return $r;
    }
}
