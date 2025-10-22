<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Mail\MarkingRequestMail;
use App\Models\MarkingRequest;
use App\Models\Student;
use App\Models\StudentTerm;
use App\Models\Year;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\DataTables;

class MarkingRequestController extends Controller
{
    public function index(Request $request)
    {
        if (request()->ajax())
        {
            $marking = MarkingRequest::with(['year'])->search($request)->latest();
            return DataTables::make($marking)
                ->escapeColumns([])
                ->addColumn('year', function ($marking) {
                    return $marking->year->name;
                })->addColumn('submitted_at', function ($row) {
                    return $row->created_at->format('Y-m-d');
                })
                ->addColumn('actions', function ($row) {
                    return $row->action_buttons;
                })
                ->make();
        }
        $years = Year::query()->get();
        return view('school.marking_requests.index', compact( 'years'));
    }

    public function  create(){
        $years = Year::query()->get();
        return view('school.marking_requests.edit',compact('years'));
    }

    public function store(\App\Http\Requests\MarkingRequestRequest $request){
        $data = $request->validated();
        $school = Auth::guard('school')->user();
        $data['school_id'] = $school->id;
        $data['status'] = 'Pending';
        $school_id = $school->id;
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
//        $school->update(['allow_reports' => 0]);

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
                    $query->whereHas('round', function (Builder $query) use ($round){
                        $query->where('month', $round);
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

        return redirect()->route('school.marking_requests.index')->with('message', 'Successfully Added');
    }

    public function edit($id){
        $marking_request = MarkingRequest::query()->find($id);
        $years = Year::query()->get();
        return view('school.marking_requests.edit',compact('marking_request','years'));
    }

    public function update(\App\Http\Requests\MarkingRequestRequest $request, $id){
        $data = $request->validated();
        unset($data['confirm']);
        MarkingRequest::query()->where('id',$id)->update($data);
        return redirect()->route('school.marking_requests.index')->with('message', 'Successfully Added');
    }

    public function destroy(Request $request){
        $request->validate(['row_id'=>'required']);
        MarkingRequest::query()
            ->where('school_id',Auth::guard('school')->user()->id)
            ->whereIn('id',$request->get('row_id'))->delete();
        return $this->sendResponse(null, t('Successfully Deleted'));
    }

    public function getCompletedTermsTotal(Request $request){
        $request->validate(['round'=>'required']);

        $student =  Student::query()->whereHas('level', function ($query) use ($request) {
            $query->whereIn('grade', $request->get('grades',[]))
                ->where('year_id',$request->get('year_id'))
            ;
        })->where('school_id', Auth::guard('school')->user()->id)->count();

        $student_terms = StudentTerm::query()
            ->whereHas('student', function (Builder $query) {
                $query->where('school_id', Auth::guard('school')->user()->id);
            })->whereHas('term', function (Builder $query) use ($request) {
                $query->where('round', $request->get('round'))
                    ->whereHas('level', function ($query) use ($request) {
                        $query->whereIn('grade', $request->get('grades',[]))
                            ->where('year_id',$request->get('year_id'));
                    });
            })->count();

        $value = ($student_terms/$student)*100;
        $remind = $student-$student_terms;
        $remind_per = ($remind/$student)*100;
        return $this->sendResponse(['student_count'=>$student,'student_terms_count'=>$student_terms,'value'=>$value,'remind'=>$remind,'remind_per'=>$remind_per]);

    }
}
