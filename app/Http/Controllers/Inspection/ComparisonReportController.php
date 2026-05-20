<?php

namespace App\Http\Controllers\Inspection;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inspection\GroupComparingReportRequest;
use App\Models\School;
use App\Reports\NewReports\GroupComparisonReport;
use App\Reports\SchoolProgressReport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ComparisonReportController extends Controller
{
    public function preGroupComparisonReport()
    {
        $user = Auth::guard('inspection')->user();
        $schools = School::query()
            ->whereIn('id', $user->schools()->pluck('school_id')->toArray())->where('active', 1)
            ->get();
        $title = t('GROUP COMPARING REPORT');
        $grades = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
        return view('inspection.reports.pre_group_comparison', compact('schools', 'title', 'grades'));
    }

    public function groupComparisonReport(GroupComparingReportRequest $request)
    {
        $user = Auth::guard('inspection')->user();
        $report = new GroupComparisonReport($request, $user);
        return $report->report();
    }
}
