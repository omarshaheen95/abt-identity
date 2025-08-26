<?php

namespace App\Http\Controllers;

use App\Helpers\Response;
use App\Models\Level;
use App\Models\LoginSession;
use App\Models\Student;
use App\Models\StudentTerm;
use App\Models\Term;
use App\Reports\NewReports\StudentReport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class GeneralController extends Controller
{
    public function levelsByYear(Request $request, $id)
    {
        $rows = Level::query()->where('year_id', $id)->get();
        if ((bool)request()->get('multiple', false)) {
            $html = '';
        } else {
            $html = '<option><option/>';
        }
        foreach ($rows as $row) {
            $html .= '<option value="' . $row->id . '">' . $row->name . '</option>';
        }
        return Response::respondSuccess(Response::SUCCESS, $html);
    }

    public function termsByLevel(Request $request, $id)
    {
        $rows = Term::query()->where('level_id', $id)->get();
        if ((bool)request()->get('multiple', false)) {
            $html = '';
        } else {
            $html = '<option><option/>';
        }
        foreach ($rows as $row) {
            $html .= '<option value="' . $row->id . '">' . $row->name . '</option>';
        }
        return Response::respondSuccess(Response::SUCCESS, $html);
    }


    function certificate(Request $request, $id)
    {
        $student_term = StudentTerm::with('student.level')
            ->where('id', $id)
            ->whereHas('student.school', function ($query) {
                $query->whereRaw('student_terms.total >= schools.certificate_mark');
            })
            ->search($request)->firstOrFail();
        $name = $student_term->student->name;
        $grade = $student_term->student->level->grade;
        $mark = $student_term->total;
        return view('general.certificate.certificate', compact('name', 'grade', 'mark'));

    }

    public function pdfCertificates(Request $request)
    {
        if (getGuard() == 'manager') {
            $request->validate([
                'year_id' => 'required',
                'level_id' => 'required',
                'school_id' => 'required',
            ], [
                'school_id.required' => 'The school field is required.',
                'year_id.required' => 'The year field is required.',
                'level_id.required' => 'The assessment field is required.'
            ]);
        } else {
            $request->validate([
                'year_id' => 'required',
                'level_id' => 'required',
            ], [
                'year_id.required' => 'The year field is required.',
                'level_id.required' => 'The assessment field is required.'
            ]);
        }

        $students_term = StudentTerm::query()
            ->with(['student'])
            ->whereHas('student.school', function ($query) {
                $query->whereRaw('student_terms.total >= schools.certificate_mark');
            })
            ->search($request)
            ->latest()
            ->get()
            ->map(function ($studentTerm) {
                return [
                    'id' => $studentTerm->id,
                    'student_name' => $studentTerm->student->name ?? null, // Avoid errors if no student
                    'std_id' => $studentTerm->student->id_number ?? null, // Avoid errors if no student
                    'section' => $studentTerm->student->grade_name,
                ];
            })
            ->toArray();

        if (count($students_term) == 0) {
            return $this->sendError(t('No students found'), 404);
        }

        $urls = [];
        foreach ($students_term as $student_term) {
            $url = '/user/' . $student_term['id'] . '/certificate';
            $urls[] = (object)[
                'url' => $url,
                'student' => $student_term
            ];
        }
        $client = new \GuzzleHttp\Client([
            'timeout' => 36000,
        ]);

        $data = [];
        $res = $client->request('POST', 'https://pdfservice.arabic-uae.com/getpdf.php', [
            'form_params' => [
                'platform' => 'abt-identity',
                'certificatesUrl' => $urls,
                'language' => app()->getLocale(),
            ],
        ]);

        $data = json_decode($res->getBody());
        $url = $data->url;
        $fileContent = file_get_contents($url);
        if ($fileContent === false) {
            throw new \Exception('Unable to download file');
        } else {
            return response($fileContent, 200, [
                'Content-Type' => 'application/zip',
                'Content-Disposition' => 'inline; filename="certificates.zip"'
            ]);
        }
        return redirect($data->url);
    }

    public function studentActivityRecords(Request $request, $id)
    {
        $student = Student::query()
            ->when($request->get('school_id'),function ($query) use ($request){
                $query->where('school_id',$request->get('school_id'));
            })
            ->findOrFail($id);

        if ($request->ajax()) {
            $perPage = $request->get('per_page', 20);
            $page = $request->get('page', 1);

            $query = LoginSession::with([
                'model.student_terms' => function ($query) {
                    $query->with('term');
                }
            ])
                ->where('model_type', Student::class)
                ->where('model_id', $id)
                ->when($start_date = $request->get('start_date', false), function (Builder $query) use ($start_date) {
                    $query->whereDate('created_at', '>=', $start_date);
                })->when($end_date = $request->get('end_date', false), function (Builder $query) use ($end_date) {
                    $query->whereDate('created_at', '<=', $end_date);
                })->latest();

            // Get total count for pagination
            $total = $query->count();

            // Apply pagination
            $loginSessions = $query->offset(($page - 1) * $perPage)
                ->limit($perPage)
                ->get();

            // Process the results
            $processedData = $loginSessions->map(function ($loginSession) {
                $loginDate = Carbon::parse($loginSession->created_at)->format('Y-m-d');
                $student = $loginSession->model;

                if (!$student) {
                    return null;
                }

                // Filter the already loaded student terms using collection filtering
                $studentTerms = $student->student_terms->filter(function ($studentTerm) use ($loginDate) {
                    if (!$studentTerm->dates_at) {
                        return false;
                    }

                    $dates = $studentTerm->dates_at;
                    $startedDates = [
                        $dates['started_at'] ?? null,
                    ];

                    foreach ($startedDates as $startedDate) {
                        if ($startedDate && Carbon::parse($startedDate)->format('Y-m-d') === $loginDate) {
                            return true;
                        }
                    }

                    return false;
                })->values();

                // Process steps data for each student term
                $processedStudentTerms = $studentTerms->map(function ($studentTerm) use ($loginDate) {
                    $dates = $studentTerm->dates_at ?? [];
                    $stepsOnLoginDate = [];

                    // Check each step (1-3) to see if it was started on the login date
                    $startedKey = "started_at";
                    $submittedKey = "submitted_at";

                    if (isset($dates[$startedKey]) && $dates[$startedKey]) {
                        $startedDate = Carbon::parse($dates[$startedKey])->format('Y-m-d');

                        if ($startedDate === $loginDate) {
                            $isCompleted = isset($dates[$submittedKey]) && $dates[$submittedKey];

                            $stepsOnLoginDate[] = [
                                'step' => 1,
                                'started_at' => $dates[$startedKey],
                                'submitted_at' => $dates[$submittedKey] ?? null,
                                'is_completed' => $isCompleted,
                                'status' => $isCompleted ? 'completed' : 'started'
                            ];
                        }
                    }


                    return [
                        'id' => $studentTerm->id,
                        'corrected' => $studentTerm->corrected,
                        'corrected_at' => $dates['corrected_at'] ?? null,
                        'dates_at' => $studentTerm->dates_at,
                        'steps_on_login_date' => $stepsOnLoginDate, // New field with processed steps
                        'has_steps_on_login_date' => count($stepsOnLoginDate) > 0, // Helper boolean
                        'term' => [
                            'id' => $studentTerm->term->id,
                            'name' => $studentTerm->term->name,
                            'round' => $studentTerm->term->round,
                        ]
                    ];
                });

                return [
                    'login_session' => [
                        'id' => $loginSession->id,
                        'created_at' => $loginSession->created_at,
                        'data' => (function () use($loginSession) {
                            $text = str_replace('IP : ', '', $loginSession->data);
                            [$ip, $rest] = explode('-', $text, 2);

                            $parts = explode(',', $rest, 3);

                            $browser = trim($parts[0] ?? null);
                            $browserVersion = trim($parts[1] ?? null);
                            $userAgent = trim($parts[2] ?? null);

                            return [
                                'ip' => trim($ip),
                                'browser' => $browser,
                                'browser_version' => $browserVersion,
                                'user_agent' => $userAgent,
                                'row_data'=>$loginSession->data
                            ];
                        })(),
                        'student_terms' => $processedStudentTerms,
                    ],
                    'login_date' => $loginDate,
                ];
            })->filter()->values(); // Remove null entries

            return response()->json([
                'data' => $processedData,
                'total' => $total,
                'current_page' => $page,
                'per_page' => $perPage,
                'last_page' => ceil($total / $perPage)
            ]);
        }


        $title = t('Student Activity Records') . ' | ' . $student->name;

        $studentId = $id;
        return view('general.student_activity_records', compact('title', 'student', 'studentId'));
    }

}
