<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Upgrade harness (not part of the project): submits the main manager create /
 * update forms, the school's student and marking request forms, valid, empty
 * and invalid, and writes the responses, the validation errors, the saved rows,
 * the activity log entries and the mails to SNAPSHOT_DIR/forms.txt. Everything
 * is rolled back and no mail leaves (Mail::fake).
 */
class FormsSnapshotTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        // Reported exceptions are posted to Telegram by this job (sync queue).
        // Never send them from a test run.
        \Illuminate\Support\Facades\Bus::fake([\App\Jobs\LogMessageJob::class]);
    }

    private $out = [];
    private $manager;

    public function test_forms()
    {
        Mail::fake();
        $this->manager = SmokeRoutesTest::manager();
        $school = SmokeRoutesTest::school();
        $yearId = $school->available_year_id;
        $level = DB::table('levels')->where('year_id', $yearId)->whereNull('deleted_at')->orderBy('id')->first();
        $schoolIds = DB::table('schools')->whereNull('deleted_at')->orderBy('id')->limit(2)->pluck('id')->all();
        $nationality = DB::table('nationalities')->orderBy('id')->value('id');
        $password = 'Upgrade#Check2026';

        // empty submissions: which fields fail and with what message
        foreach (['school', 'student', 'level', 'year', 'manager', 'inspection', 'term'] as $resource) {
            $this->send("$resource store (empty)", 'post', "/manager/$resource", []);
        }

        // school
        $this->send('school store', 'post', '/manager/school', [
            'name' => 'Upgrade Check School', 'email' => 'upgrade-school@example.invalid', 'password' => $password,
            'type' => 'American', 'country' => 'uae', 'website' => 'https://example.invalid', 'phone' => '0501234567',
            'address' => 'Dubai', 'available_year_id' => $yearId, 'approved' => 1, 'student_login' => 1,
            'proctoring_settings' => ['desktop_only' => 1, 'selfie' => 1],
        ], 'schools', 'email', 'upgrade-school@example.invalid');
        $this->send('school store invalid', 'post', '/manager/school', [
            'name' => 'Upgrade Check School', 'email' => 'not-an-email', 'password' => '123', 'website' => 'not a url',
            'type' => 'American', 'country' => 'uae', 'available_year_id' => $yearId,
        ]);
        $schoolId = DB::table('schools')->where('email', 'upgrade-school@example.invalid')->value('id');
        if ($schoolId) {
            $this->send('school update', 'put', "/manager/school/$schoolId", [
                'name' => 'Upgrade Check School 2', 'email' => 'upgrade-school@example.invalid', 'password' => '',
                'type' => 'British', 'country' => 'uae', 'available_year_id' => $yearId,
                'approved' => 0, 'proctoring_settings' => ['screenshot' => 1],
            ], 'schools', 'id', $schoolId);
        }

        // student, from the manager panel
        $studentSchool = $schoolId ?: $schoolIds[0];
        $student = [
            'id_number' => 'UPG-77', 'name' => 'Upgrade Check Student', 'email' => 'upgradecheck77', 'nationality_id' => $nationality,
            'grade' => 3, 'reading_level' => 3, 'learning_years' => 2, 'dob' => '2016-05-06', 'school_id' => $studentSchool,
            'year_id' => $yearId, 'grade_section' => '3A', 'gender' => 'girl', 'arab' => 1, 'sen' => 0, 'g_t' => 0, 'uae_student' => 1, 'demo' => 0,
        ];
        $this->send('student store', 'post', '/manager/student', $student, 'students', 'email', 'upgradecheck77');
        $this->send('student store invalid', 'post', '/manager/student', [
            'name' => 'اسم عربي', 'email' => 'ab', 'grade' => 3, 'school_id' => $studentSchool, 'year_id' => $yearId,
            'gender' => 'other', 'arab' => 0, 'sen' => 0, 'g_t' => 0, 'uae_student' => 1, 'demo' => 1,
        ]);
        $this->send('student store duplicate', 'post', '/manager/student', $student);
        $studentId = DB::table('students')->where('email', 'upgradecheck77')->value('id');
        if ($studentId) {
            $this->send('student update', 'put', "/manager/student/$studentId", [
                'id_number' => 'UPG-78', 'name' => 'Upgrade Check Student Two', 'email' => 'upgradecheck77', 'nationality_id' => $nationality,
                'grade' => 4, 'reading_level' => 5, 'learning_years' => 3, 'dob' => '', 'school_id' => $studentSchool, 'year_id' => $yearId,
                'grade_section' => '4B', 'gender' => 'boy', 'arab' => 0, 'sen' => 1, 'g_t' => 1, 'uae_student' => 0, 'demo' => 0,
            ], 'students', 'id', $studentId);
        }

        // student and marking request, from the school panel
        $as = ['school', $school];
        $this->send('school: student store', 'post', '/school/student', [
            'name' => 'Upgrade Check Pupil', 'email' => 'upgradecheck78', 'grade' => 5, 'reading_level' => 4, 'learning_years' => 3,
            'year_id' => $yearId, 'gender' => 'boy', 'arab' => 0, 'sen' => 0, 'g_t' => 0, 'uae_student' => 1, 'grade_section' => '5C',
        ], 'students', 'email', 'upgradecheck78', $as);
        $this->send('school: student store (empty)', 'post', '/school/student', [], null, null, null, $as);
        $pupilId = DB::table('students')->where('email', 'upgradecheck78')->value('id');
        if ($pupilId) {
            $this->send('school: student update', 'put', "/school/student/$pupilId", [
                'name' => 'Upgrade Check Pupil Two', 'email' => 'upgradecheck78', 'grade' => 6, 'reading_level' => 6, 'learning_years' => 0,
                'year_id' => $yearId, 'gender' => 'girl', 'arab' => 1, 'sen' => 1, 'g_t' => 0, 'uae_student' => 0, 'grade_section' => '6D',
            ], 'students', 'id', $pupilId, $as);
        }
        $this->send('school: marking request store', 'post', '/school/marking_request', [
            'year_id' => $yearId, 'section' => 1, 'grades' => [3, 4], 'month' => 'May', 'email' => 'upgrade-marking@example.invalid',
            'notes' => 'upgrade check', 'confirm' => 1,
        ], 'marking_requests', 'email', 'upgrade-marking@example.invalid', $as);
        $this->send('school: marking request store (empty)', 'post', '/school/marking_request', [], null, null, null, $as);

        // level, year and term
        $this->send('level store', 'post', '/manager/level', ['name' => 'Upgrade Level', 'arab' => 1, 'year_id' => $yearId, 'grade' => 4, 'active' => 1], 'levels', 'name', 'Upgrade Level');
        $this->send('level store bad grade', 'post', '/manager/level', ['name' => 'Upgrade Level X', 'arab' => 1, 'year_id' => $yearId, 'grade' => 13]);
        $levelId = DB::table('levels')->where('name', 'Upgrade Level')->value('id');
        if ($levelId) {
            $this->send('level update', 'put', "/manager/level/$levelId", ['name' => 'Upgrade Level', 'arab' => 0, 'year_id' => $yearId, 'grade' => 5], 'levels', 'id', $levelId);
            $this->send('term store', 'post', '/manager/term', ['name' => 'Upgrade Term', 'month' => 'May', 'hours' => 1, 'level_id' => $levelId, 'active' => 1], 'terms', 'name', 'Upgrade Term');
            $this->send('term store bad month', 'post', '/manager/term', ['name' => 'Upgrade Term X', 'month' => 'June', 'hours' => 1, 'level_id' => $levelId]);
            $termId = DB::table('terms')->where('name', 'Upgrade Term')->value('id');
            if ($termId) {
                $this->send('term update', 'put', "/manager/term/$termId", ['name' => 'Upgrade Term 2', 'month' => 'February', 'hours' => 2, 'level_id' => $levelId], 'terms', 'id', $termId);
            }
        }
        $this->send('year store', 'post', '/manager/year', ['name' => 'Upgrade Year', 'slug' => '2090/2091'], 'years', 'slug', '2090/2091');
        $this->send('year store bad slug', 'post', '/manager/year', ['name' => 'Upgrade Year', 'slug' => '2090-2091'], 'years', 'slug', '2090-2091');
        $newYear = DB::table('years')->where('slug', '2090/2091')->value('id');
        if ($newYear) {
            $this->send('year update', 'put', "/manager/year/$newYear", ['name' => 'Upgrade Year 2', 'slug' => '2091/2092'], 'years', 'id', $newYear);
        }

        // manager and inspection
        $this->send('manager store', 'post', '/manager/manager', ['name' => 'Upgrade Manager', 'email' => 'upgrade-manager@example.invalid', 'password' => $password, 'approved' => 1], 'managers', 'email', 'upgrade-manager@example.invalid');
        $managerId = DB::table('managers')->where('email', 'upgrade-manager@example.invalid')->value('id');
        if ($managerId) {
            $this->send('manager update', 'put', "/manager/manager/$managerId", ['name' => 'Upgrade Manager 2', 'email' => 'upgrade-manager@example.invalid', 'password' => '', 'approved' => 1], 'managers', 'id', $managerId);
            $this->send('manager update password', 'put', "/manager/manager/$managerId", ['name' => 'Upgrade Manager 2', 'email' => 'upgrade-manager@example.invalid', 'password' => $password . 'x', 'approved' => 1], 'managers', 'id', $managerId);
        }
        $this->send('inspection store', 'post', '/manager/inspection', ['name' => 'Upgrade Inspection', 'email' => 'upgrade-inspection@example.invalid', 'password' => $password, 'approved' => 1, 'schools' => $schoolIds], 'inspections', 'email', 'upgrade-inspection@example.invalid');
        $inspectionId = DB::table('inspections')->where('email', 'upgrade-inspection@example.invalid')->value('id');
        if ($inspectionId) {
            $this->send('inspection update', 'put', "/manager/inspection/$inspectionId", ['name' => 'Upgrade Inspection 2', 'email' => 'upgrade-inspection@example.invalid', 'password' => $password, 'schools' => [$schoolIds[0]]], 'inspections', 'id', $inspectionId);
            $this->out['inspection schools'] = DB::table('inspection_schools')->where('inspection_id', $inspectionId)->orderBy('school_id')->pluck('school_id')->all();
        }

        // mails the forms sent (only the marking request sends one)
        $mails = [];
        $sent = Mail::sent(\App\Mail\MarkingRequestMail::class);
        // render them with the real mailer (nothing is sent by rendering)
        app()->forgetInstance('mailer');
        app()->forgetInstance('mail.manager');
        Mail::clearResolvedInstances();
        foreach ($sent as $mail) {
            $mails[] = ['to' => array_column($mail->to, 'address'), 'html' => preg_replace("/[ \t]+\r?\n/", "\n", $mail->render())];
        }
        $this->out['mails'] = $mails;

        $json = json_encode($this->out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        // auto-increment ids are not rolled back, so they differ from run to run
        foreach (['school_id' => $schoolId, 'level_id' => $levelId ?? null] as $key => $id) {
            if ($id) {
                $json = preg_replace('/"' . $key . '": "?' . $id . '"?\b/', '"' . $key . '": "NEW"', $json);
            }
        }
        $json = preg_replace('/(marking_request|student|school|level|term|year|manager|inspection)\/\d+/', '$1/N', $json);
        @mkdir(getenv('SNAPSHOT_DIR'), 0777, true);
        file_put_contents(getenv('SNAPSHOT_DIR') . '/forms.txt', $json);
        $this->assertTrue(true);
    }

    private function send($label, $method, $url, $data, $table = null, $column = null, $value = null, $as = null)
    {
        $maxActivity = (int) DB::table('activity_log')->max('id');
        [$guard, $user] = $as ?: ['manager', $this->manager];
        $this->actingAs($user, $guard);
        $response = $this->call(strtoupper($method), $url, $data);
        try {
            $e = $response->exception;
        } catch (\Throwable $ignored) {
            $e = null;
        }
        $errors = session('errors');
        if (is_object($errors)) {
            $errors = $errors->getBag('default')->toArray();
        } elseif (is_array($errors)) {
            $errors = $errors['default']['messages'] ?? $errors;
        }
        if (is_array($errors)) {
            ksort($errors);
        }
        $entry = [
            'status' => $response->getStatusCode(),
            'location' => $response->headers->get('Location'),
            'exception' => $e && !($e instanceof \Illuminate\Validation\ValidationException) ? get_class($e) . ': ' . strtok($e->getMessage(), "\n") : null,
            'message' => session('message'),
            'errors' => $errors,
        ];
        if ($table) {
            $row = DB::table($table)->where($column, $value)->first();
            if ($row) {
                $row = (array) $row;
                foreach (['id', 'created_at', 'updated_at', 'password', 'password_changed_at', 'remember_token', 'abt_id'] as $k) {
                    unset($row[$k]);
                }
            }
            $entry['row'] = $row;
        }
        $entry['activity'] = DB::table('activity_log')->where('id', '>', $maxActivity)->orderBy('id')->get()
            ->map(function ($a) {
                $properties = json_decode($a->properties, true);
                // timestamps of the saved row
                array_walk_recursive($properties, function (&$v, $k) {
                    if (in_array($k, ['created_at', 'updated_at', 'password_changed_at'], true)) {
                        $v = 'T';
                    }
                });
                return ['log' => $a->log_name, 'description' => $a->description, 'subject_type' => $a->subject_type,
                    'causer_type' => $a->causer_type, 'properties' => $properties];
            })->all();
        $this->out[$label] = $entry;
        $this->flushSession();
    }
}
