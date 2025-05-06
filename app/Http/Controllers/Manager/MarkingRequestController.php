<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\MarkingRequestRequest;
use App\Mail\MarkingRequestMail;
use App\Models\MarkingRequest;
use App\Models\School;
use App\Models\Year;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\DataTables;

class MarkingRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:show marking requests')->only('index');
        $this->middleware('permission:add marking requests')->only(['create','store']);
        $this->middleware('permission:edit marking requests')->only(['edit','update']);
        $this->middleware('permission:delete marking requests')->only('destroy');
    }

    public function index(Request $request)
    {
        if (request()->ajax()) {
            $rows = MarkingRequest::with(['school', 'year'])->search($request)->latest();
            return DataTables::make($rows)
                ->escapeColumns([])
                ->addColumn('school', function ($row) {
                    $data = [
                        'round' => $row->round,
                        'school_id' => $row->school->id,
                        'year_id' => $row->year->id,
                        'section' => $row->section,
                        'grades' =>implode(',', $row->grades),
                    ];
                    $url = route('manager.student_term.index','uncorrected').'?'.http_build_query($data);
                    return '<a href="'.$url.'" target="_blank">'.$row->school->name.'</a>';
                })
                ->addColumn('status', function ($row) {
                    $html = '<span>'.t($row->status).'</span> <br>';
                    $html .='<div class="d-flex mt-3">'.t('Assessments').': ' .'  <span class="badge badge-info ms-1">' . $row->uncorrected_student_terms_count . '</span></div>';
                    return $html;
                })->addColumn('year', function ($row) {
                    return $row->year->name;
                })
                ->addColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->toDateTimeString();
                })
                ->addColumn('actions', function ($row) {
                    return $row->action_buttons;
                })
                ->make();
        }
        $title = t('Marking Requests');
        $schools = School::query()->where('active', 1)->orderBy('name')->get();
        $years = Year::query()->get();
        return view('manager.marking_requests.index', compact('title', 'schools', 'years'));
    }

    public function create()
    {
        $title = t('Add New Marking Request');
        $schools = School::query()->where('active', 1)->orderBy('name')->get();
        $years = Year::query()->get();
        $grades = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
        return view('manager.marking_requests.edit', compact('title', 'schools', 'years', 'grades'));
    }

    public function store(MarkingRequestRequest $request)
    {
        $data = $request->validated();
        MarkingRequest::query()->create($data);
        $school = School::query()->findOrFail($data['school_id']);
        $school->update(['allow_reports' => 0]);
        return redirect()->route('manager.marking_requests.index')->with('message', t('Marking Request Created Successfully'));
    }

    public function show($id)
    {
        return redirect()->route('manager.marking_requests.index');
    }

    public function edit($id)
    {
        $marking_request = MarkingRequest::query()->findOrFail($id);
        $title = t('Edit Marking Request');
        $schools = School::query()->where('active', 1)->orderBy('name')->get();
        $years = Year::query()->get();
        $grades = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
        return view('manager.marking_requests.edit', compact('title', 'marking_request', 'schools', 'grades', 'years'));
    }

    public function update(MarkingRequestRequest $request, $id)
    {
        $data = $request->validated();
        $marking_request = MarkingRequest::query()->findOrFail($id);
        $marking_request->update($data);
        $marking_requests = MarkingRequest::query()->where('school_id', $data['school_id'])
            ->where('status', '<>', 'Completed')->where('status', '<>', 'Rejected')->count();
        if ($marking_requests > 0) {
            $marking_request->school->update([
                'allow_reports' => 0,
            ]);
        } else {
            $marking_request->school->update([
                'allow_reports' => 1,
            ]);
        }
        if ($marking_request->wasChanged('status')) {
            if (!is_null($marking_request->email)) {
                Mail::to($marking_request->email)->send(new MarkingRequestMail($marking_request));
            }
        }
        return redirect()->route('manager.marking_requests.index')->with('message', t('Marking Request Updated Successfully'));
    }

    public function destroy(Request $request)
    {
        $ids = $request->get('row_id');
        MarkingRequest::query()->whereIn('id',$ids)->delete();
        return $this->sendResponse(null,t('Marking Request Deleted Successfully'));
    }

}
