<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Http\Requests\General\Report\AttainmentRequest;
use App\Models\Subject;
use App\Models\Year;
use App\Reports\AttainmentCombinedReport;
use App\Reports\AttainmentReport;

class ReportController extends Controller
{


    public function preAttainmentReport()
    {
        $title = t('Attainment Report');
        $years = Year::query()->get();
        $subjects = Subject::query()->get()->pluck('type',)->unique()->values()->toArray();
        return view('general.reports.attainment.setting', compact('title', 'years', 'subjects'));
    }

    public function attainmentReport(AttainmentRequest $request)
    {
        $data = $request->validated();
        if ($request->get('combined', false))
        {
            $report = new AttainmentCombinedReport($request);
        } else{
            $report = new AttainmentReport($request);
        }
        return $report->report();
    }
}
