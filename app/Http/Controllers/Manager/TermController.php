<?php

namespace App\Http\Controllers\Manager;

use App\Exports\QuestionsExport;
use App\Exports\StandardExport;
use App\Exports\StudentNotSubmittedTermExport;
use App\Exports\TermExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\CopyTermRequest;
use App\Http\Requests\Manager\TermRequest;
use App\Models\Level;
use App\Models\Question;
use App\Models\QuestionStandard;
use App\Models\School;
use App\Models\Standard;
use App\Models\Student;
use App\Models\StudentTermStandard;
use App\Models\Subject;
use App\Models\Term;
use App\Models\Year;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class TermController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:show terms')->only('index');
        $this->middleware('permission:add terms')->only(['create','store']);
        $this->middleware('permission:edit terms')->only(['edit','update']);
        $this->middleware('permission:delete terms')->only('deleteTerm');
        $this->middleware('permission:terms activation')->only('activation');

        $this->middleware('permission:show terms questions')->only('termsQuestions');
        $this->middleware('permission:show terms questions')->only('preview');
        $this->middleware('permission:export terms questions')->only('termsQuestionsExport');
        $this->middleware('permission:show questions standards')->only('standards');
        $this->middleware('permission:edit questions standards')->only('editStandards');
        $this->middleware('permission:export questions standards')->only('standardExport');
        $this->middleware('permission:show students not submitted term')->only('studentsNotSubmittedTerms');
        $this->middleware('permission:export students not submitted term')->only('studentsNotSubmittedTermsExport');
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $rows = Term::query()->with(['level'])->search($request)->latest();
            return DataTables::make($rows)
                ->escapeColumns([])
                ->addColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('active', function ($row) {
                    if ($row->active == 1){
                        return '<a><span class="badge badge-success">'.t('Active').'</span></a>';
                    }
                    return '<a><span class="badge badge-danger">'.t('Inactive').'</span></a>';
                })
                ->addColumn('level', function ($row) {
                    return $row->level->name;
                })
                ->addColumn('actions', function ($row) {
                    return $row->action_buttons;
                })
                ->make();
        }
        $title = t('Assessments');
        $years = Year::query()->get();
        return view('manager.term.index', compact('title', 'years'));
    }

    public function create()
    {
        $title = t('Create Assessment');
        $years = Year::query()->get();
        return view('manager.term.edit', compact('title', 'years'));
    }

    public function store(TermRequest $request)
    {
        $data = $request->validated();
        $data['active'] = $request->get('active', false) ? 1 : 0;
        Term::query()->create($data);
        return redirect()->route('manager.term.index')->with('message', t('Successfully Created'));
    }

    public function edit($id)
    {
        $title = t('Edit Assessment');
        $term = Term::query()->findOrFail($id);
        $years = Year::query()->get();
        $levels = Level::query()->where('year_id', $term->level->year_id)->get();
        return view('manager.term.edit', compact('title', 'term', 'years', 'levels'));
    }

    public function update(TermRequest $request, $id)
    {
        $term = Term::query()->findOrFail($id);
        $data = $request->validated();
        $data['active'] = $request->get('active', false) ? 1 : 0;
        $term->update($data);
        return redirect()->route('manager.term.index')->with('message', t('Successfully Updated'));
    }

    public function deleteTerm(Request $request)
    {
        Term::destroy($request->get('row_id'));
        return $this->sendResponse(null, t('Successfully Deleted'));
    }
    public function export(Request $request)
    {
        return (new TermExport($request))->download('Assessments Details.xlsx');
    }

    public function preview($id){
        $term = Term::query()->where('id',$id)->first();
        $questions = Question::query()->with(
            [   'tf_question',
                'match_question',
                'sort_question',
                'option_question',
                'fill_blank_question'
            ])
            ->where(function ($query){
                $query->whereHas('tf_question')
                    ->orWhereHas('match_question')
                    ->orWhereHas('sort_question')
                    ->orWhereHas('option_question')
                    ->orWhereHas('fill_blank_question');
            })
            ->where('term_id', $id)
            ->get();



        //dd($main_questions->toArray());
        //dd($questions->toArray());
        $questions_count = count($questions);
        if ($questions_count>0){
            $subjects = Subject::all();
            $marks = 100;
            $preview_mode = true;

            $questions = $questions->groupBy(['subject_id','type']);


            return view('manager.student_term.term_preview.term_preview',compact('term','questions','questions_count','marks','subjects','preview_mode'));
        }
       return redirect()->route('manager.term.index')
           ->with('m-class','error')
           ->with('message', t('The Assessment questions content not entered'));
    }

    public function termsQuestions(Request $request)
    {
        if (request()->ajax()) {
            $question = Question::with('term.level.year')->search($request)
                ->has('term.level.year')
                ->latest();

            return DataTables::make($question)
                ->escapeColumns([])
                ->addColumn('term_name', function ($question) {
                    return $question->term->name;
                })
                ->addColumn('type', function ($question) {
                   return camelCaseText($question->type). ' - ' . t('subject') . ' : ' . $question->subject->name;

                })
                ->addColumn('level', function ($question) {
                    $year_name = $question->term->level->year->name;
                    $grade = $question->term->level->grade;
                    return $year_name . ' - ' . 'Grade ' . $grade ;
                })
                ->addColumn('actions', function ($question) {
                    if (\Auth::guard('manager')->user()->hasDirectPermission('edit questions content')){
                        return '<a class="btn btn-icon btn-sm btn-primary" href="'.route('manager.term.questions',$question->term->id).'">' .
                            '<i class="la la-edit"></i>' .
                            '</a>';
                    }
                })
                ->make();
        }
        $years = Year::query()->get();
        return view('manager.term.questions_index', compact('years'));
    }

    public function termsQuestionsExport(Request $request)
    {
        return (new QuestionsExport($request))->download('Assessments Questions Details.xlsx');
    }

    public function termsNames(Request $request, $level)
    {
        $terms = Term::query()->where('level_id', $level)->get();
        $html = '<option></option>';
        foreach ($terms as $term) {
            $html .= '<option value="' . $term->id . '">' . $term->name . '</option>';
        }
        return response()->json(['html' => $html]);
    }

    public function standards(Request $request)
    {
        if (request()->ajax()) {
            $standard = QuestionStandard::query()->has('question.term.level.year')->with('question.term.level.year')->search($request)->latest();

            return DataTables::make($standard)
                ->escapeColumns([])
                ->addColumn('type', function ($standard) {
                    return camelCaseText($standard->question->type). ' - ' . t('subject') . ' : ' . $standard->question->subject->name;
                })
                ->addColumn('question_content', function ($standard) {
                    return $standard->question->content;
                })->addColumn('term_name', function ($standard) {
                    return $standard->question->term->name;
                })
                ->addColumn('level', function ($standard) {
                    $year_name = $standard->question->term->level->year->name;
                    $grade = $standard->question->term->level->grade;
                    return $year_name . ' - ' . 'Grade ' . $grade ;
                })
                ->addColumn('actions', function ($standard) {
                    if (\Auth::guard('manager')->user()->hasDirectPermission('edit questions standards')){
                        return '<a class="btn btn-icon btn-sm btn-primary" href="'.route('manager.term.edit-standards',$standard->question->term->id).'">' .
                            '<i class="la la-edit"></i>' .
                            '</a>';
                    }
                })
                ->make();
        }
        $years = Year::query()->get();
        return view('manager.term.standards.index', compact('years'));
    }
    public function editStandards($id){
        $term = Term::query()->findOrFail($id);
        $title = t('Assessment Questions Standards').'/'.$term->name;
        $questions = Question::query()->with('question_standard')->where('term_id', $id)->get();
        $subjects = Question::getQuestionSubjects();
       return view('manager.term.standards.edit',compact('questions','subjects','title','term'));
    }
    public function updateTermStandards(Request $request){
        $request->validate(['standards'=>'required|array']);
       // dd($request->get('standards'));
        foreach ($request->get('standards') as $question_id=>$standard){
            if ($standard['standard']){
                QuestionStandard::query()->updateOrCreate(
                    ['question_id' => $question_id],
                    ['question_id' => $question_id,'standard'=>$standard['standard'],'mark' => $standard['mark']]);
            }
        }
       return redirect()->route('manager.term.standards')->with('message', t('Successfully Updated'));
    }

    public function standardExport(Request $request)
    {
        return (new StandardExport($request))->download('Question Standard Details.xlsx');
    }


    public function studentsNotSubmittedTerms(Request $request)
    {
        if (request()->ajax()) {
            $students = Student::with(['school', 'level', 'level.year'])->search($request)
                ->whereDoesntHave('student_terms', function (Builder $query) use ($request) {
                    $query->when($value = $request->get('round'), function (Builder $query) use ($value) {
                        $query->whereHas('term', function (Builder $query) use ($value) {
                            $query->where('round', $value);
                        });
                    });
                })->latest();

            return DataTables::make($students)
                ->escapeColumns([])
                ->addColumn('name', function ($student) {
                    return '<div class="d-flex flex-column"><span>'.$student->name.'</span><span class="text-danger cursor-pointer" data-clipboard-text="'.$student->email.'" onclick="copyToClipboard(this)">' . $student->email . '</span></div>';
                })
                ->addColumn('sid', function ($student) {
                    return '<div class="d-flex flex-column align-items-center"><span class="cursor-pointer" data-clipboard-text="'.$student->id.'" onclick="copyToClipboard(this)">' . $student->id . '</span></div>';
                })
                ->addColumn('school', function ($student) {
                    return "<a class='text-info' target='_blank' href='" . route('manager.school.edit', $student->school->id) . "'>" . $student->school->name . "</a>". (is_null($student->id_number) ? '' : "<br><span class='text-danger cursor-pointer' data-clipboard-text=".$student->id_number." onclick='copyToClipboard(this)' >" . t('SID Num') .': '.$student->id_number. "</span> " ) ;
                })

                ->addColumn('level', function ($student) {
                    if (!is_null($student->level)) {
                        $year_name = $student->level->year->name;
                        $grade = $student->grade;
                        return $year_name . ' - ' . 'Grade ' . $grade ;
                    } else {
                        return "<span class='text-danger'>" . t('not assigned to a level') . "</span>";
                    }

                })
                ->addColumn('actions', function ($term) {
                    // return view('general.action_menu')->with('actions',$term->action_buttons);
                })->make();
        }
        $years = Year::query()->get();
        $schools = School::query()->where('active', 1)->get();
        return view('manager.term.students_not_submitted_term', compact('years', 'schools'));
    }

    public function studentsNotSubmittedTermsExport(Request $request)
    {
        return (new StudentNotSubmittedTermExport($request))->download('Students Information.xlsx');
    }

    public function copyTermsView(Request $request)
    {
        $years = Year::query()->get();
        $title = t('Copy Assessments');
        $grades = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
        return view('manager.term.copy_terms', compact('years', 'title', 'grades'));
    }

    public function copyTerms(CopyTermRequest $request)
    {
        $data = $request->validated();
        $data['with_questions'] = $request->get('with_questions', 0);
        $data['with_standards'] = $request->get('with_standards', 0);
        $data['with_terms'] = $request->get('with_terms', 0);
        $from_terms = Term::query()
            ->with(['level'])
            ->whereHas('level', function (Builder $query) use ($data) {
                $query->where('year_id', $data['from_year'])
                    ->whereIn('grade', $data['grades']);
            })
            ->where('round', $data['from_round'])
            ->when($data['with_questions'] == 1, function (Builder $query) {
                $query->with(['question']);
            })
            ->when($data['with_standards'] == 1, function (Builder $query) {
                $query->with(['question.question_standard']);
            })

            ->get();
        if ($data['with_terms'] == 1) {
            foreach ($from_terms as $from_term) {
                //check is pre term
                $is_found = Term::query()
                    ->with(['level'])
                    ->whereHas('level', function (Builder $query) use ($data, $from_term) {
                        $query->where('year_id', $data['to_year'])
                            ->where('grade', $from_term->level->grade);
                    })
                    ->where('round', $data['to_round'])
                    ->first();
                if (!$is_found)
                {
                    $term_level = Level::query()
                        ->where('year_id', $data['to_year'])
                        ->where('grade', $from_term->level->grade)
                        ->first();
                    if ($term_level)
                    {
                        //replicate term with change round
                        $new_term = $from_term->replicate();
                        $new_term->round = $data['to_round'];
                        $new_term->level_id = $term_level->id;
                        $new_term->save();
                    }
                }
            }
        }
        $to_terms = Term::query()
            ->with(['level','question'])
            ->whereHas('level', function (Builder $query) use ($data) {
                $query->where('year_id', $data['to_year'])
                    ->whereIn('grade', $data['grades']);
            })
            ->where('round', $data['to_round'])
            ->when($data['with_questions'] == 1, function (Builder $query) {
                $query->with(['question']);
            })
            ->when($data['with_standards'] == 1, function (Builder $query) {
                $query->with(['question.question_standard']);
            })
            ->withCount('question')
            ->get();


        $from_terms->each(function ($term) use ($data, $to_terms) {
            $to_term = $to_terms->where('level.grade', $term->level->grade)
                ->where('level.arab', $term->level->arab)
                ->first();
            if ($to_term && $to_term->question_count == 0) {
                if ($data['with_questions'] == 1) {
                    foreach ($term->question as $question) {
                        $new_question = $question->replicate();
                        $new_question->term_id = $to_term->id;
                        $new_question->content = null;
                        $new_question->image = null;
                        $new_question->audio = null;
                        $new_question->question_reader = null;
                        $new_question->save();
                        if ($data['with_standards'] == 1) {
//                            foreach ($question->question_standard as $standard) {
                                $standard = $question->question_standard;
                                $new_standard = $standard->replicate();
                                $new_standard->question_id = $new_question->id;
                                $new_standard->save();
//                            }
                        }
                        switch ($question->type)
                        {
                            case 1:
                                $new_option = $question->tf_question->replicate();
                                $new_option->question_id = $new_question->id;
                                $new_option->save();
                                break;
                            case 2:
                                foreach ($question->option_question as $option) {
                                    $new_option = $option->replicate();
                                    $new_option->question_id = $new_question->id;
                                    $new_option->save();
                                }
                                break;
                            case 3:
                                foreach ($question->match_question as $match) {
                                    $new_option = $match->replicate();
                                    $new_option->question_id = $new_question->id;
                                    $new_option->uid = Str::uuid()->toString();
                                    $new_option->save();
                                }
                                break;
                            case 4:
                                foreach ($question->sort_question as $sort) {
                                    $new_option = $sort->replicate();
                                    $new_option->question_id = $new_question->id;
                                    $new_option->uid = Str::uuid()->toString();
                                    $new_option->save();
                                }
                                break;
                        }
                    }
                }
            }
        });

        return redirect()->back()->with('message', t('Assessments copied successfully'));

    }

    public function addGeneralTerms(Request $request)
    {
        $request->validate(['year_id' => 'required', 'round' => 'required']);

        $year_id = $request->get('year_id');
        $month = $request->get('round');

        $terms = Term::query()
            ->where('round', $month)
            ->whereHas('level', function (Builder $query) use ($year_id) {
                $query->where('year_id', $year_id);
            })->count();

        $year = Year::query()->findOrFail($year_id);

        if ($terms > 0) {
            return $this->sendError('Year: '.$year->name.' Round: '.$month.' assessments is already exist');
        }


        $years = explode('/', $year->name);
        $first_year = $years[0];
        $second_year = $years[1];

        if ($month == 'september') {
            $year_text = $first_year;
            $round_name = "Sept";
        } elseif ($month == 'february') {
            $round_name = "Feb";
            $year_text = $second_year;
        } else {
            $year_text = $second_year;
            $round_name = "May";
        }

        $grades = [1 => 'One', 2 => 'Tow', 3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve'];

        $levels = Level::query()->where('year_id', $year->id)->get();

        $terms_array = [];

        foreach ($grades as $grade => $name) {
            $level = $levels->where('grade', $grade)->where('arab', 0)->first();
            if ($level) {
                $terms_array[] = [
                    'name' => [
                            'ar' => "Grade $grade Identity - English Version - $round_name $year_text",
                            'en' => "Grade $grade Identity - English Version - $round_name $year_text",
                        ],
                    'level_id' => $level->id,
                    'active' => 0,
                    'duration' => 1,
                    'round' => $month,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        foreach ($terms_array as $term) {
            Term::query()->create($term);
        }

        $terms_array = [];
        foreach ($grades as $grade => $name) {
            $level = $levels->where('grade', $grade)->where('arab', 1)->first();
            if ($level) {
                $terms_array[] = [
                    'name' => [
                            'ar' => "Grade $grade Identity - Arabic Version - $round_name $year_text",
                            'en' => "Grade $grade Identity - Arabic Version - $round_name $year_text",
                        ],
                    'level_id' => $level->id,
                    'active' => 0,
                    'duration' => 1,
                    'round' => $month,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }



        return $this->sendResponse(null, t('Assessments added successfully').':'.count($terms_array));
    }

    public function activation(Request $request)
    {
        $request->validate(['year_id'=>'required','grades'=>'required|array','round'=>'required','active'=>'required|in:1,0']);
        Term::query()
            ->whereHas('level', function ($query) use ($request) {
                $query->whereIn('grade', $request->get('grades'))
                    ->where('year_id', $request->get('year_id'));
            })->where('round', $request->get('round'))
            ->update(['active' => $request->get('active')]);
        return $this->sendResponse(null,t('Assessments Status Updated Successfully to').': '.($request->get('active')?t('Active'):t('Non-Active')));
    }

}
