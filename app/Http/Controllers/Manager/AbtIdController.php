<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\SchoolAbtGroupRequest;
use App\Models\School;
use App\Models\Student;
use App\Models\Year;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class AbtIdController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:show abt grouping students')->only(['storeAbtSchoolGroup', 'createAbtSchoolGroup', 'abtStudents']);
        $this->middleware('permission:student link with abt id')->only(['studentLinkWithAbtId', 'studentUnlinkWithAbtId']);
    }

    public function abtStudents(Request $request)
    {
        if ($request->ajax()) {
            $students = Student::query()
                ->withCount(['student_terms'])
                ->with(['level.year', 'school'])
                ->has('level')
                ->has('school')
                ->search($request)->orderBy('name');

            return DataTables::make($students)
                ->escapeColumns([])
                ->addColumn('created_at', function ($student) {
                    return Carbon::parse($student->created_at)->toDateString();
                })
                ->addColumn('level', function ($student) {
                    $citizen = $student->citizen ? 'Citizen' : 'NonCitizen';
                    $sen = $student->sen ? 'Sen' : 'Normal';
                    $gender = $student->gender == 'boy' ? '<span style="color: dodgerblue">Boy</span>' : '<span style="color: mediumvioletred">Girl</span>';
                    return $student->level->short_name . '<br>' . $gender . ' - ' . $citizen . ' - ' . $sen;;
                })
                ->addColumn('school', function ($student) {
                    return "<a class='text-info' target='_blank' href='" . route('manager.school.edit', $student->school_id) . "'>" . $student->school->name . "</a>" . (is_null($student->id_number) ? '' : "<br><span class='text-danger'>" . t('SID Num') . ":</span> " . $student->id_number);

                })
                ->addColumn('check', function ($student) {
                    return $student->check;
                })
                ->make();
        }
        $schools = School::query()->latest()->get();
        $years = Year::query()->latest()->get();
        $title = t('Connect Students');
        return view('manager.abt_id.abt_index', compact('title', 'schools', 'years'));
    }

    public function studentLinkWithAbtId(Request $request)
    {
        $id = $request->get('user_id', false);
        if ($id) {
            if (is_array($id)) {
                $students = Student::query()->whereIn('id', $id)->get();
                $abt_student = $students->where('abt_id', '<>', null)->first();
                if ($abt_student) {
                    $abt_id = $abt_student->abt_id;
                } else {
                    $abt_id = Student::query()->max('abt_id');
                    if ($abt_id) {
                        $abt_id++;
                    } else {
                        $abt_id = 1000000;
                    }

                }
                foreach ($students as $student) {
                    $student->abt_id = $abt_id;
                    $student->timestamps = false;
                    $student->save();
                }
            }
        }

        return $this->sendResponse(null, t('Successfully link with abt id'));
    }

    public function studentUnlinkWithAbtId(Request $request)
    {
        $id = $request->get('user_id', false);
        if ($id) {
            if (is_array($id)) {
                $students = Student::query()->whereIn('id', $id)->get();
                foreach ($students as $student) {
                    $student->abt_id = null;
                    $student->timestamps = false;
                    $student->save();
                }

            }
        }

        return $this->sendResponse(null, t('Successfully Unlink with abt id'));
    }

    public function createAbtSchoolGroup()
    {
        $title = t('New School ABT Group');
        $schools = School::query()->where('active', 1)->orderBy('name')->get();
        $years = Year::get();
        return view('manager.abt_id.abt_group', compact('title', 'schools', 'years'));

    }

    public function storeAbtSchoolGroup(SchoolAbtGroupRequest $request)
    {
        $data = $request->validated();
        $link_type = $request->get('link_by_number', 1);
        $link_number = $request->get('link_number', 1);
        $schools = [$data['school_id']];
        foreach ($schools as $school) {
            $students = Student::query()
                ->where('school_id', $school)
                ->whereNotNull('id_number')
                ->whereHas('level', function (Builder $query) use ($data) {
                    $query->where('year_id', $data['primary_year']);
                })
                ->get();
            foreach ($students as $student) {
                if (!is_null($student->id_number)) {
                    //remove U letter and 12 from student id
//                    $student_id = str_replace(['U', '', ' ',], '', $student->id_number);
                    $student_id = $student->id_number;
                    if (!is_null($student->abt_id)) {
                        Student::query()
                            ->where('school_id', $school)
                            ->when($link_type == 1, function (Builder $query) use ($student_id) {
                                $query->where('id_number', $student_id);
                            })
                            ->when($link_type == 2, function (Builder $query) use ($student_id, $link_number) {
                                //get student_id string length according to link number
                                $student_id = substr($student_id, 0, $link_number);
                                $query->where('id_number', 'like', $student_id . '%');
                            })
                            //3 name
                            ->when($link_type == 3, function (Builder $query) use ($student) {
                                $query->where('name', $student->name);
                            })
                            ->whereHas('level', function (Builder $query) use ($data) {
                                $query->whereIn('year_id', $data['secondary_years']);
                                $query->where('year_id', '<>', $data['primary_year']);
                            })
                            ->update([
                                'abt_id' => $student->abt_id
                            ]);
                    } else {
                        $abt_id = Student::query()->max('abt_id');
                        if ($abt_id) {
                            $abt_id++;
                        } else {
                            $abt_id = 1000000;
                        }
                        Student::query()
                            ->where('school_id', $school)
                            ->when($link_type == 1, function (Builder $query) use ($student_id) {
                                $query->where('id_number', $student_id);
                            }, function (Builder $query) use ($student_id, $link_number) {
                                //get student_id string length according to link number
                                $student_id = substr($student_id, 0, $link_number);
                                $query->where('id_number', 'like', $student_id . '%');
                            })
                            ->whereHas('level', function (Builder $query) use ($data) {
                                $query->whereIn('year_id', $data['secondary_years']);
                                $query->where('year_id', '<>', $data['primary_year']);
                            })
                            ->update([
                                'abt_id' => $abt_id
                            ]);
                        $student->update([
                            'abt_id' => $abt_id
                        ]);
                    }
                }
            }
        }
        return redirect()->back()->with('message', t('ABT Grouped successfully'));
    }
}
