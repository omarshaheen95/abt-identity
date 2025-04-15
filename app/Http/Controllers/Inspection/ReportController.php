<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Http\Controllers\Inspection;

use App\Http\Controllers\Controller;
use App\Http\Requests\General\Report\AttainmentRequest;
use App\Models\School;
use App\Models\Subject;
use App\Models\Year;
use App\Reports\AttainmentCombinedReport;
use App\Reports\AttainmentReport;
use App\Reports\InspectionAttainmentReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function preAttainmentReport()
    {
        $title = t('Attainment Report');
        $years = Year::query()->get();
        $subjects = Subject::query()->get()->pluck('type')->unique()->values()->toArray();

        // Get schools that the inspection user has access to
        $inspection = Auth::guard('inspection')->user();
        $schools = $inspection->schools;

        return view('general.reports.attainment.new.setting', compact('title', 'years', 'subjects', 'schools'));
    }

    public function attainmentReport(AttainmentRequest $request)
    {
        // If combined report is requested for a specific school
//        if ($request->has('school_id') && !empty($request->get('school_id')) && $request->get('combined', false)) {
//            $report = new AttainmentCombinedReport($request);
//            return $report->report();
//        }

        if ($request->has('school_id')) {
            $schools_ids = $request->school_id;
        } else {
            $schools_ids = Auth::guard('inspection')->user()->schools->pluck('id')->toArray();
        }
        $report = new \App\Reports\Inspection\AttainmentReport($request,$schools_ids);
        return $report->report();
    }

}
