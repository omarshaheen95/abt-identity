<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\MarkingRequestRequest;
use App\Mail\MarkingRequestMail;
use App\Models\MarkingRequest;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentTerm;
use App\Models\Year;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $data['status'] = 'Pending';
        $school_id = $request->get('school_id', false);
        $year = $request->get('year_id', false);
        $grades = $request->get('grades', []);
        $section = $request->get('section', false);
        $round = $request->get('round', false);
        $student_terms = StudentTerm::query()
            ->whereHas('student', function (Builder $query)use ($school_id) {
                $query->where('school_id', $school_id);
            })->whereHas('term', function (Builder $query) use ($request, $section, $year, $grades, $round) {
                $query->where('round', $round)
                    ->whereHas('level', function ($query) use ($request, $section, $year, $grades) {
                        $query->whereIn('grade', $grades)
                            ->where('year_id', $year)
                            ->when($section == 1, function ($query) {
                                $query->where('arab', 1);
                            })->when($section == 2, function ($query) {
                                $query->where('arab', 0);
                            });
                    });
            })->where('corrected', 0)->get();

        if ($student_terms->count() == 0) {
            return redirect()->back()->with('message', t('No students found for this request'))->with('m-class', 'error');
        }
        $marking_request = MarkingRequest::query()->create($data);
        $school = School::query()->findOrFail($school_id);

        $school->update(['allow_reports' => 0]);

        $total = Student::query()
            ->where('school_id', $school_id)
            ->when($year, function (Builder $query) use ($year) {
                $query->whereHas('level', function (Builder $query) use ($year){
                    $query->where('year_id', $year);
                });
            })
            ->when($grades,function (Builder $query) use ($grades){
                $query->whereHas('level', function (Builder $query) use ($grades){
                    $query->whereIn('grade', $grades);
                });
            })
            ->when($section,function (Builder $query) use ($section){
                $query->whereHas('level', function (Builder $query) use ($section) {
                    $query->when($section == 1, function ($query) {
                        $query->where('arab', 1);
                    });
                    $query->when($section == 2, function ($query) {
                        $query->where('arab', 0);
                    });
                });
            })->count();



        $not_started_rows = Student::query()
            ->where('school_id', $school_id)
            ->when($year, function (Builder $query) use ($year) {
                $query->whereHas('level', function (Builder $query) use ($year){
                    $query->where('year_id', $year);
                });
            })
            ->when($grades,function (Builder $query) use ($grades){
                $query->whereHas('level', function (Builder $query) use ($grades){
                    $query->whereIn('grade', $grades);
                });
            })
            ->when($section,function (Builder $query) use ($section){
                $query->whereHas('level', function (Builder $query) use ($section) {
                    $query->when($section == 1, function ($query) {
                        $query->where('arab', 1);
                    });
                    $query->when($section == 2, function ($query) {
                        $query->where('arab', 0);
                    });
                });
            })
            ->whereDoesntHave('student_terms', function (Builder $query) use ($round) {
                $query->when($round, function (Builder $query) use ($round) {
                    $query->whereHas('term', function (Builder $query) use ($round){
                        $query->where('round', $round);
                    });
                });
            })->count();

        $data['year'] = Year::query()->findOrFail($year)->name;
        $data['grades'] = implode(',', $grades);
        $data['section'] = $section == 1 ? 'Arabs': ($section == 2 ? 'Non-Arabs' : 'Arabs & Non-Arabs');
        $data['round'] = $round;
        $data['total'] = $total;
        $data['not_started'] = $not_started_rows;
        $data['not_started_per'] = $total > 0 ? number_format(($not_started_rows / $total) * 100, 2) : 0;
        $data['started'] = $student_terms->count();
        $data['started_per'] = $total > 0 ? number_format(($student_terms->count() / $total) * 100, 2) : 0;

        Mail::to($marking_request->email)->send(new MarkingRequestMail($marking_request, $data));
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
