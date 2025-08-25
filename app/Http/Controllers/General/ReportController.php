<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Http\Controllers\General;


use App\Exports\NewExports\StudentAttainmentAndProgress;
use App\Exports\YearToYearProgressExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\General\Report\AttainmentReportRequest;
use App\Http\Requests\General\Report\ProgressReportRequest;
use App\Http\Requests\General\Report\StudentMarkRequest;
use App\Http\Requests\General\Report\TrendOverTimeReportRequest;
use App\Http\Requests\General\Report\YearToYearProgressReportRequest;
use App\Models\School;
use App\Models\Student;
use App\Models\Year;
use App\Reports\NewReports\AttainmentReport;

use App\Reports\NewReports\ProgressReport;
use App\Reports\NewReports\StudentReport;
use App\Reports\NewReports\YearToYearReport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    private function availableSchools()
    {
        if (guardIs('manager') || guardIs('inspection')){
            $schools = School::query()->where('active', 1)
                ->when(guardIs('inspection'), function (Builder $builder) {
                    $builder->whereIn('id', Auth::guard('inspection')->user()->schools->pluck('school_id'));
                })
                ->orderBy('name')
                ->get();
        }else{
            $schools = collect();
        }
        return $schools;
    }
    public function preAttainmentReport()
    {
        $title = re('Attainment Report');
        $schools = $this->availableSchools();
        $container_type = 'container-fluid';
        $years = Year::query()->latest()->get();
        return view('general.new_reports.attainment.pre-attainment-report', compact('title','schools', 'container_type', 'years'));
    }

    public function attainmentReport(AttainmentReportRequest $request)
    {
        $school_id = is_array($request->get('school_id')) ? $request->school_id : [$request->school_id];
        $report = new AttainmentReport($request,$school_id);
        if ($request->get('generated_report_type') === 'attainment') {
            return $report->report();
        } else {
            return $report->reportCombined();
        }
    }
    public function preProgressReport()
    {
        $title = re('Progress Within The Academic Year');
        $schools = $this->availableSchools();
        $container_type = 'container-fluid';
        $years = Year::query()->latest()->get();
        return view('general.new_reports.progress.pre-progress-report', compact('title','schools', 'container_type', 'years'));
    }

    public function progressReport(ProgressReportRequest $request)
    {
        $school_id = is_array($request->get('school_id')) ? $request->school_id : [$request->school_id];
        $report = new ProgressReport($request, $school_id);
        if ($request->get('generated_report_type') === 'attainment') {
            return  $report->report();
        } else {
            return $report->reportCombined();
        }
    }
    public function preYearToYearReport()
    {
        $title = re('Year To Year Progress Report');
        $schools = $this->availableSchools();
        $container_type = 'container-fluid';
        $years = Year::query()->orderBy('id')->get();
        $yearsCount = 2;
        return view('general.new_reports.year_to_year.pre-year-to-year-report', compact('title','schools', 'container_type', 'years', 'yearsCount'));
    }

    public function preTrendOverTimeReport()
    {
        $title = re('Trends Over Time Progress Report');
        $schools = $this->availableSchools();
        $container_type = 'container-fluid';
        $years = Year::query()->orderBy('id')->get();
        $yearsCount = 3;
        return view('general.new_reports.year_to_year.pre-year-to-year-report', compact('title','schools', 'container_type', 'years', 'yearsCount'));
    }

    public function yearToYearReport(YearToYearProgressReportRequest $request)
    {
        $school_id = is_array($request->get('school_id')) ? $request->school_id : [$request->school_id];
        $report = new YearToYearReport($request, $school_id);
        if ($request->get('generated_report_type') === 'attainment') {
            return  $report->report();
        } else {
            return $report->combinedReport();
        }
    }

    public function excelYearToYearReport(YearToYearProgressReportRequest $request)
    {
        return (new \App\Exports\NewExports\YearToYearProgressExport($request, [$request->get('school_id')]))
            ->download("Students progress over the years.xlsx");
    }
    public function preTrendsOverTimeReport()
    {
        $title = re('Trends Over Time Report');
        $schools = School::query()->where('isApprove', 1)
            ->orderBy('name')
            ->get();
        $container_type = 'container-fluid';
        $years = Year::query()->orderBy('id')->get();
        $yearsCount = 3;
        return view('general.new_reports.year_to_year.pre-year-to-year-report', compact('title','schools', 'container_type', 'years', 'yearsCount'));
    }

    public function excelTrendsOverTimeReport(TrendOverTimeReportRequest $request)
    {
        $round_num = $request->get('round', false);

        $school = School::query()->find($request->get('school_id'));
        if (!$school) {
            return redirect()->back()->withErrors(['school_id' => t('School not found')]);
        }

        $round_num = $request->get('round_num', false);

        $school = Auth::guard('school')->user();

        $years = $request->get('years', []);

        $last_year = count($years) == 3 ? $years[2] : $years[1];

        $student_section = $request->get('student_section', 0);
        $selected_grades = $request->get('grades', []);

        $selected_grades = $request->get('grades', []);
        $grades = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];


        $students_grades = [];


        foreach ($grades as $grade) {
            if (!in_array($grade, $selected_grades)) {
                continue;
            }
            $students_grades[$grade] = Student::query()
                ->where('school_id', $school->id)
                ->whereNotNull('abt_id')
                ->whereHas('level', function (Builder $query) use ($grade, $last_year, $student_section) {
                    $query->where('grade', $grade)
                        ->when($student_section == 1, function ($query) {
                            $query->where('arab', 1);
                        })
                        ->when($student_section == 2, function ($query) {
                            $query->where('arab', 0);
                        })
                        ->where('year_id', $last_year);
                })
                ->pluck('abt_id')->values()->all();


        }


        return (new YearsProgressExport($years, $students_grades, $school, $round_num))
            ->download("Students progress over the years.xlsx");
    }

    public function preStudentMarkReport()
    {
        $title = t('Students Marks');
        $schools = $this->availableSchools();
        $container_type = 'container-fluid';
        $years = Year::query()->latest()->get();

        return view('general.new_reports.student_marks.pre-students-marks', compact('title', 'schools', 'container_type', 'years'));
    }

    public function studentMarkReport(StudentMarkRequest $request)
    {
        $school_id = is_array($request->get('school_id')) ? $request->school_id : [$request->school_id];
        return (new StudentAttainmentAndProgress($request, $school_id))
            ->download('Students Terms Information.xlsx');
    }

    public function studentReport($id)
    {
        $studentReport = new \App\Reports\NewReports\StudentReport($id);
        return $studentReport->report();
    }

    public function studentReportCard($id)
    {
        $studentReport = new \App\Reports\NewReports\StudentReport($id);
        return $studentReport->reportCard();
    }


    //To Download Zip File of students reports ordering by sections
    public function pdfReports(Request $request)
    {
        $request->validate([
            'school_id' => 'required',
            'year_id' => 'required',
            'level_id' => 'required|array|min:1|max:1',
        ], [
            'year_id.required' => 'The year field is required.',
            'level_id.required' => 'The level field is required.',
            'level_id.min' => 'The level must be at least 1.',
            'level_id.max' => 'The level may not be greater than 1.',
        ]);

        $school_id = $request->get('school_id',false);

        $students = Student::with(['level.year','year'])
            ->where('school_id', $school_id)
            ->search($request)
            ->select(['id', 'name as student_name', 'id_number as std_id'])
            ->get()->values()->toArray();

        $client = new \GuzzleHttp\Client([
            'timeout'  => 36000,
        ]);

        $res = $client->request('POST', 'https://pdfservice.arabic-uae.com/getpdf.php', [
            'form_params' => [
                'platform' => 'abt-identity',
                'studentid' => $students,
            ],
        ]);
        $data = json_decode($res->getBody());
        $url = $data->url;
        $fileContent = file_get_contents($url);
        if ($fileContent === false) {
            throw new \Exception('Unable to download file');
        }else{
            return response($fileContent, 200, [
                'Content-Type' => 'application/zip',
                'Content-Disposition' => 'inline; filename="reports.zip"'
            ]);
        }
    }
    public function pdfReportsCards(Request $request)
    {
        $request->validate([
            'school_id' => 'required',
            'year_id' => 'required',
            'level_id' => 'required|array|min:1|max:1',
        ], [
            'year_id.required' => 'The year field is required.',
            'level_id.required' => 'The level field is required.',
            'level_id.min' => 'The level must be at least 1.',
            'level_id.max' => 'The level may not be greater than 1.',
        ]);

        $school_id = $request->get('school_id',false);

        $students = Student::with(['level.year','year'])
            ->where('school_id', $school_id)->search($request)
            ->select(['id', 'name as student_name', 'id_number as std_id'])->get()->values()->toArray();

        if (count($students) == 0) {
            return $this->sendError(t('No students found'), 404);
        }

        $client = new \GuzzleHttp\Client([
            'timeout'  => 36000,
        ]);

        $data = ['report_type' => 1, 'report_card' => true];
        $res = $client->request('POST', 'https://pdfservice.arabic-uae.com/getpdf.php', [
            'form_params' => [
                'platform' => 'abt-identity',
                'studentid' => $students,
                'data' => $data,
            ],
        ]);
        $data = json_decode($res->getBody());
        $url = $data->url;
        $fileContent = file_get_contents($url);
        if ($fileContent === false) {
            throw new \Exception('Unable to download file');
        }else{
            return response($fileContent, 200, [
                'Content-Type' => 'application/zip',
                'Content-Disposition' => 'inline; filename="reports.zip"'
            ]);
        }
    }


    //To Access For Student Report By QR
    public function studentQRReport(Request $request)
    {
        app()->setLocale('en');
        if ($request->has('token')) {
            $student_id = decryptStudentId($request->get('token', false));
            $student_report = new StudentReport($student_id);
            return $student_report->report();
        } else {
            return false;
        }
    }
    public function studentQRReportCard(Request $request)
    {
        app()->setLocale('en');
        if ($request->has('token')) {
            $student_id = decryptStudentId($request->get('token', false));
            $student_report = new StudentReport($student_id);
            return $student_report->reportCard();
        } else {
            return false;
        }
    }
}
