<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Upgrade harness (not part of the project): submits the main manager create /
 * update forms, the school's student and marking request forms, the profile and
 * password forms of each panel (and a JsValidator remote check), valid, empty
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
        \Illuminate\Support\Carbon::setTestNow('2026-10-22 10:00:00');
    }

    private $out = [];
    private $manager;

    public function test_forms()
    {
        Mail::fake();
        $this->manager = SmokeRoutesTest::manager();
        $school = SmokeRoutesTest::school();
        $inspection = SmokeRoutesTest::inspection();
        $yearId = $school->available_year_id;
        $level = DB::table('levels')->where('year_id', $yearId)->where('grade', 3)->whereNull('deleted_at')->orderBy('id')->first();
        $schoolIds = DB::table('schools')->whereNull('deleted_at')->orderBy('id')->limit(2)->pluck('id')->all();
        $password = 'Upgrade#Check2026';
        $name = ['ar' => 'فحص الترقية', 'en' => 'Upgrade Check'];

        // empty submissions: which fields fail and with what message
        foreach (['school', 'student', 'level', 'year', 'manager', 'inspection', 'term', 'marking_requests'] as $resource) {
            $this->send("$resource store (empty)", 'post', "/manager/$resource", []);
        }

        // school
        $school1 = [
            'name' => $name, 'email' => 'upgrade-school@example.invalid', 'password' => $password,
            'curriculum_type' => 'American', 'country' => 'uae', 'url' => 'https://example.invalid', 'mobile' => '0501234567',
            'available_year_id' => $yearId, 'certificate_mark' => 80, 'active' => 1, 'student_login' => 1,
            'proctoring_settings' => ['desktop_only' => 1, 'selfie' => 1],
        ];
        $this->send('school store', 'post', '/manager/school', $school1, 'schools', 'email', 'upgrade-school@example.invalid');
        $this->send('school store invalid', 'post', '/manager/school', [
            'name' => ['en' => 'Upgrade Check School'], 'email' => 'not-an-email', 'password' => '123', 'url' => 'not a url',
            'curriculum_type' => 'American', 'country' => 'uae', 'available_year_id' => $yearId, 'certificate_mark' => 20,
        ]);
        $this->send('school store duplicate', 'post', '/manager/school', $school1);
        $schoolId = DB::table('schools')->where('email', 'upgrade-school@example.invalid')->value('id');
        if ($schoolId) {
            $this->send('school update', 'put', "/manager/school/$schoolId", [
                'name' => ['ar' => 'فحص الترقية 2', 'en' => 'Upgrade Check 2'], 'email' => 'upgrade-school@example.invalid', 'password' => '',
                'curriculum_type' => 'British', 'country' => 'uae', 'available_year_id' => $yearId, 'certificate_mark' => 90,
                'active' => 0, 'proctoring_settings' => ['screenshot' => 1],
            ], 'schools', 'id', $schoolId);
            $this->send('school update password', 'put', "/manager/school/$schoolId", [
                'name' => $name, 'email' => 'upgrade-school@example.invalid', 'password' => $password . 'x',
                'curriculum_type' => 'British', 'country' => 'uae', 'available_year_id' => $yearId, 'certificate_mark' => 90,
            ], 'schools', 'id', $schoolId);
        }

        // student, from the manager panel
        $studentSchool = $schoolId ?: $schoolIds[0];
        $student = [
            'id_number' => 'UPG-77', 'name' => 'Upgrade Check Student', 'email' => 'upgradecheck77', 'nationality' => 'Emirati',
            'dob' => '2016-05-06', 'school_id' => $studentSchool, 'year_id' => $yearId, 'level_id' => $level->id,
            'grade_name' => '3A', 'gender' => 'girl', 'arab' => 1, 'sen' => 0, 'g_t' => 0, 'citizen' => 1, 'demo' => 0,
        ];
        $this->send('student store', 'post', '/manager/student', $student, 'students', 'email', 'upgradecheck77');
        $this->send('student store invalid', 'post', '/manager/student', [
            'name' => 'اسم عربي 123', 'email' => 'a b', 'school_id' => $studentSchool, 'year_id' => $yearId, 'level_id' => 0,
            'gender' => 'other', 'arab' => 3, 'sen' => 0, 'g_t' => 0, 'citizen' => 1, 'demo' => 1,
        ]);
        $this->send('student store duplicate', 'post', '/manager/student', $student);
        $studentId = DB::table('students')->where('email', 'upgradecheck77')->value('id');
        if ($studentId) {
            $this->send('student update', 'put', "/manager/student/$studentId", [
                'id_number' => 'UPG-78', 'name' => 'Upgrade Check Student Two', 'email' => 'upgradecheck77', 'nationality' => 'Egyptian',
                'dob' => '', 'school_id' => $studentSchool, 'year_id' => $yearId, 'level_id' => $level->id,
                'grade_name' => '4B', 'gender' => 'boy', 'arab' => 0, 'sen' => 1, 'g_t' => 1, 'citizen' => 0, 'demo' => 0,
            ], 'students', 'id', $studentId);
        }

        // student and marking request, from the school panel
        $as = ['school', $school];
        $this->send('school: student store', 'post', '/school/student', [
            'id_number' => 'UPG-79', 'name' => 'Upgrade Check Pupil', 'email' => 'upgradecheck78', 'year_id' => $yearId, 'level_id' => $level->id,
            'gender' => 'boy', 'arab' => 0, 'sen' => 0, 'g_t' => 0, 'citizen' => 1, 'grade_name' => '5C', 'nationality' => 'Indian',
        ], 'students', 'email', 'upgradecheck78', $as);
        $this->send('school: student store (empty)', 'post', '/school/student', [], null, null, null, $as);
        $pupilId = DB::table('students')->where('email', 'upgradecheck78')->value('id');
        if ($pupilId) {
            $this->send('school: student update', 'put', "/school/student/$pupilId", [
                'name' => 'Upgrade Check Pupil Two', 'email' => 'upgradecheck78', 'year_id' => $yearId, 'level_id' => $level->id,
                'gender' => 'girl', 'arab' => 1, 'sen' => 1, 'g_t' => 0, 'citizen' => 0, 'grade_name' => '6D',
            ], 'students', 'id', $pupilId, $as);
        }
        // a marking request needs uncorrected attempts of those grades and round
        $uncorrected = DB::table('student_terms')
            ->join('students', 'students.id', '=', 'student_terms.student_id')
            ->join('terms', 'terms.id', '=', 'student_terms.term_id')
            ->join('levels', 'levels.id', '=', 'terms.level_id')
            ->where('students.school_id', $school->id)->where('levels.year_id', $yearId)->whereIn('levels.grade', [3, 4])
            ->where('levels.arab', 1)->where('terms.round', 'may')->whereNull('student_terms.deleted_at')
            ->orderBy('student_terms.id')->limit(5)->pluck('student_terms.id');
        DB::table('student_terms')->whereIn('id', $uncorrected)->update(['corrected' => 0]);
        $this->send('school: marking request store', 'post', '/school/marking_requests', [
            'year_id' => $yearId, 'section' => 1, 'grades' => [3, 4], 'round' => 'may', 'email' => 'upgrade-marking@example.invalid',
            'notes' => 'upgrade check', 'confirm' => 1,
        ], 'marking_requests', 'email', 'upgrade-marking@example.invalid', $as);
        $this->send('school: marking request store (empty)', 'post', '/school/marking_requests', [], null, null, null, $as);
        $markingId = DB::table('marking_requests')->where('email', 'upgrade-marking@example.invalid')->value('id');
        if ($markingId) {
            $this->send('marking request update', 'put', "/manager/marking_requests/$markingId", [
                'school_id' => $school->id, 'year_id' => $yearId, 'section' => 1, 'grades' => [3, 4], 'round' => 'may',
                'email' => 'upgrade-marking@example.invalid', 'status' => 'Accepted', 'notes' => 'accepted',
            ], 'marking_requests', 'id', $markingId);
        }

        // level, year and term
        $this->send('level store', 'post', '/manager/level', ['name' => ['ar' => 'مستوى فحص', 'en' => 'Upgrade Level'], 'section' => 1, 'year_id' => $yearId, 'grade' => 4, 'active' => 1], 'levels', 'year_id', -1);
        $levelId = DB::table('levels')->where('name', 'like', '%Upgrade Level%')->value('id');
        $this->out['level store']['row'] = $levelId ? $this->row('levels', 'id', $levelId) : null;
        $this->send('level store bad section', 'post', '/manager/level', ['name' => $name, 'section' => 3, 'year_id' => $yearId, 'grade' => 13]);
        if ($levelId) {
            $this->send('level update', 'put', "/manager/level/$levelId", ['name' => ['ar' => 'مستوى فحص', 'en' => 'Upgrade Level'], 'section' => 2, 'year_id' => $yearId, 'grade' => 5], 'levels', 'id', $levelId);
            $this->send('term store', 'post', '/manager/term', ['name' => ['ar' => 'اختبار فحص', 'en' => 'Upgrade Term'], 'round' => 'may', 'duration' => 60, 'year_id' => $yearId, 'level_id' => $levelId, 'active' => 1], 'terms', 'level_id', $levelId);
            $this->send('term store bad round', 'post', '/manager/term', ['name' => $name, 'round' => 'june', 'duration' => 60, 'year_id' => $yearId, 'level_id' => $levelId]);
            $termId = DB::table('terms')->where('level_id', $levelId)->value('id');
            if ($termId) {
                $this->send('term update', 'put', "/manager/term/$termId", ['name' => ['ar' => 'اختبار فحص 2', 'en' => 'Upgrade Term 2'], 'round' => 'february', 'duration' => 90, 'year_id' => $yearId, 'level_id' => $levelId], 'terms', 'id', $termId);
            }
        }
        $this->send('year store', 'post', '/manager/year', ['name' => ['ar' => 'سنة فحص', 'en' => 'Upgrade Year'], 'slug' => '2090/2091'], 'years', 'slug', '2090/2091');
        $this->send('year store bad slug', 'post', '/manager/year', ['name' => $name, 'slug' => '2090-2091'], 'years', 'slug', '2090-2091');
        $newYear = DB::table('years')->where('slug', '2090/2091')->value('id');
        if ($newYear) {
            $this->send('year update', 'put', "/manager/year/$newYear", ['name' => ['ar' => 'سنة فحص 2', 'en' => 'Upgrade Year 2'], 'slug' => '2091/2092'], 'years', 'id', $newYear);
        }

        // manager and inspection
        $this->send('manager store', 'post', '/manager/manager', ['name' => 'Upgrade Manager', 'email' => 'upgrade-manager@example.invalid', 'password' => $password, 'approved' => 1], 'managers', 'email', 'upgrade-manager@example.invalid');
        $managerId = DB::table('managers')->where('email', 'upgrade-manager@example.invalid')->value('id');
        if ($managerId) {
            $this->send('manager update', 'put', "/manager/manager/$managerId", ['name' => 'Upgrade Manager 2', 'email' => 'upgrade-manager@example.invalid', 'password' => '', 'approved' => 1], 'managers', 'id', $managerId);
            $this->send('manager update password', 'put', "/manager/manager/$managerId", ['name' => 'Upgrade Manager 2', 'email' => 'upgrade-manager@example.invalid', 'password' => $password . 'x', 'approved' => 1], 'managers', 'id', $managerId);
        }
        $this->send('inspection store', 'post', '/manager/inspection', ['name' => 'Upgrade Inspection', 'email' => 'upgrade-inspection@example.invalid', 'password' => $password, 'active' => 1, 'schools_ids' => $schoolIds], 'inspections', 'email', 'upgrade-inspection@example.invalid');
        $inspectionId = DB::table('inspections')->where('email', 'upgrade-inspection@example.invalid')->value('id');
        if ($inspectionId) {
            $this->send('inspection update', 'put', "/manager/inspection/$inspectionId", ['name' => 'Upgrade Inspection 2', 'email' => 'upgrade-inspection@example.invalid', 'password' => $password, 'schools_ids' => [$schoolIds[0]]], 'inspections', 'id', $inspectionId);
            $this->out['inspection schools'] = DB::table('inspection_schools')->where('inspection_id', $inspectionId)->orderBy('school_id')->pluck('school_id')->all();
        }

        // the profile and password forms of each panel
        $known = 'Upgrade-Old-2026!';
        $accounts = ['manager' => [$this->manager, 'managers', '/manager/update-password'], 'school' => [$school, 'schools', '/school/update-password'], 'inspection' => [$inspection, 'inspections', '/inspection/update-password']];
        foreach ($accounts as $guard => [$user, $table, $url]) {
            DB::table($table)->where('id', $user->id)->update(['password' => Hash::make($known), 'password_changed_at' => now()->subDays(10)]);
            DB::table('password_histories')->where('model_type', get_class($user))->where('model_id', $user->id)->delete();
            $user->refresh();
            $as = [$guard, $user];
            $this->send("$guard: password wrong old", 'post', $url, ['old_password' => 'nope', 'password' => $password, 'password_confirmation' => $password], null, null, null, $as);
            $this->send("$guard: password weak", 'post', $url, ['old_password' => $known, 'password' => 'abc', 'password_confirmation' => 'abc'], null, null, null, $as);
            $this->send("$guard: password jsvalidation remote", 'post', $url, ['_jsvalidation' => 'password', '_jsvalidation_validate_all' => 'false', 'password' => 'abc'], null, null, null, $as);
            $this->send("$guard: password ok", 'post', $url, ['old_password' => $known, 'password' => $password, 'password_confirmation' => $password], $table, 'id', $user->id, $as);
            $this->out["$guard: password ok"]['password matches'] = Hash::check($password, DB::table($table)->where('id', $user->id)->value('password'));
            $this->out["$guard: password ok"]['history rows'] = DB::table('password_histories')->where('model_type', get_class($user))->where('model_id', $user->id)->count();
        }
        $this->send('manager: profile', 'post', '/manager/update-profile', ['name' => 'Upgrade Profile', 'email' => $this->manager->email], 'managers', 'id', $this->manager->id);

        // mails the forms sent
        $mails = [];
        $sent = Mail::sent(\App\Mail\MarkingRequestMail::class);
        // render them with the real mailer (nothing is sent by rendering)
        app()->forgetInstance('mailer');
        app()->forgetInstance('mail.manager');
        Mail::clearResolvedInstances();
        foreach ($sent as $mail) {
            $mails[] = ['to' => array_column($mail->to, 'address'), 'subject' => $mail->subject, 'html' => preg_replace("/[ \t]+\r?\n/", "\n", $mail->render())];
        }
        $this->out['mails'] = $mails;

        $json = json_encode($this->out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        // auto-increment ids are not rolled back, so they differ from run to run
        foreach (['school_id' => $schoolId, 'level_id' => $levelId ?? null, 'student_id' => $studentId, 'subject_id' => $studentId] as $key => $id) {
            if ($id) {
                $json = preg_replace('/"' . $key . '": "?' . $id . '"?\b/', '"' . $key . '": "NEW"', $json);
            }
        }
        $json = preg_replace('/(marking_requests|student|school|level|term|year|manager|inspection)\/\d+/', '$1/N', $json);
        $json = preg_replace('/"subject_id": \d+/', '"subject_id": "N"', $json);
        @mkdir(getenv('SNAPSHOT_DIR'), 0777, true);
        file_put_contents(getenv('SNAPSHOT_DIR') . '/forms.txt', $json);
        $this->assertTrue(true);
    }

    private function row($table, $column, $value)
    {
        $row = DB::table($table)->where($column, $value)->first();
        if ($row) {
            $row = (array) $row;
            foreach (['id', 'created_at', 'updated_at', 'password', 'password_changed_at', 'remember_token', 'abt_id', 'level_id', 'school_id', 'last_login'] as $k) {
                unset($row[$k]);
            }
        }

        return $row;
    }

    private function send($label, $method, $url, $data, $table = null, $column = null, $value = null, $as = null)
    {
        $maxActivity = (int) DB::table('activity_log')->max('id');
        [$guard, $user] = $as ?: ['manager', $this->manager];
        $this->actingAs($user, $guard);
        $response = $this->call(strtoupper($method), $url, $data, [], [], ['HTTP_REFERER' => 'http://localhost/back']);
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
        if (str_contains((string) $response->headers->get('Content-Type'), 'json')) {
            $entry['json'] = json_decode((string) $response->getContent(), true);
        }
        if ($table) {
            $entry['row'] = $this->row($table, $column, $value);
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
        ResponseSnapshotTest::resetAuth();
    }
}
