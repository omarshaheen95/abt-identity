<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\MarkingRequest;
use App\Models\Student;
use App\Models\StudentTerm;
use App\Models\Year;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        MarkingRequest::query()->create($data);
        $school->update(['allow_reports' => 0]);
//         if (!is_null($data['email'])) {
//             Mail::to($data['email'])->send(new MarkingRequestMail($data));
//         }
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
