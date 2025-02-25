<?php

namespace App\Http\Controllers;

use App\Helpers\Response;
use App\Models\Level;
use App\Models\StudentTerm;
use App\Models\Term;
use App\Reports\StudentReport;
use Illuminate\Http\Request;
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

    public function studentReport($id)
    {
        if (\request()->get('report_card', 1))
        {
            $report = new StudentReport($id);
            return $report->studentReportCard();
        }
        $report = new StudentReport($id);
        return $report->report();
    }

    function certificate(Request $request, $id)
    {
        $student_term = StudentTerm::with('student.level')
            ->where('id', $id)
            ->where('total', '>=', 90)
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
            ->where('total', '>=', 90)
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

}
