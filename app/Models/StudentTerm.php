<?php

namespace App\Models;

use Carbon\Carbon;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\LogsActivity;

class StudentTerm extends Model
{
    use SoftDeletes,CascadeSoftDeletes, LogsActivity;

    protected static $logAttributes = ['student_id', 'term_id', 'corrected', 'subjects_marks', 'total'];
    protected static $recordEvents = ['updated', 'deleted'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    protected $fillable = ['student_id','term_id','corrected','total','notes','subjects_marks','dates_at'];

    protected $cascadeDeletes = ['tfResults','optionResults','matchResults','sortResults','articleResults','standards'];

    public function tfResults():HasMany{
        return $this->hasMany(TFQuestionResult::class,'student_term_id');
    }
    public function optionResults(): HasMany
    {
        return $this->hasMany(OptionQuestionResult::class,'student_term_id');
    }

    public function matchResults(): HasMany
    {
        return $this->hasMany(MatchQuestionResult::class,'student_term_id');
    }
    public function sortResults(): HasMany
    {
        return $this->hasMany(SortQuestionResult::class,'student_term_id');
    }
    public function articleResults(): HasMany
    {
        return $this->hasMany(ArticleQuestionResult::class,'student_term_id');
    }

    public function standards(): HasMany
    {
        return $this->hasMany(StudentTermStandard::class,'student_term_id');
    }




    public function term():BelongsTo{
        return $this->belongsTo(Term::class,'term_id');
    }
    public function student():BelongsTo{
        return $this->belongsTo(Student::class,'student_id');
    }

    protected function setDatesAtAttribute($value)
    {
        $this->attributes['dates_at'] = json_encode($value);
    }

    public function getDatesAtAttribute($value)
    {
        return json_decode($value, true);
    }
    protected function setSubjectsMarksAttribute($value)
    {
        $this->attributes['subjects_marks'] = json_encode($value);
    }

    public function getSubjectsMarksAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getActionDataAttribute()
    {

        if ($this->deleted_at && Auth::guard('manager')->user()->hasDirectPermission('restore deleted students terms')) {
                return '<button  onclick="restore(' . $this->id . ')" class="btn btn-warning d-flex justify-content-center align-items-center h-35px w-90px btn_restore">' . t('Restore') . '</button>';
        }

        if ($this->corrected){
            if (getGuard()=='manager'){
                $actions=[];
                $actions[] = ['key'=>'edit','name'=>t('Correct'),'route'=>route('manager.student_term.edit', $this->id),'permission'=>'edit students terms'];
                if ($this->total>=90){
                    $actions[] = ['key'=>'blank','name'=>t('Certificate'),'route'=>route(getGuard().'.student-term.certificate', $this->id)];
                }
                return view('general.action_menu')->with('actions',$actions);
            }else{
                if ($this->total>=90){
                    return '<a target="_blank" href="' . route(getGuard().'.student-term.certificate', $this->id) . '" class="btn btn-primary btn-sm">' . t('Certificate') . '</a>';
                }
            }
        }else if (Auth::guard('manager')->check() && Auth::guard('manager')->user()->hasDirectPermission('edit students terms')) {
                return '<a target="_blank" href="' . route('manager.student_term.edit', $this->id) . '" class="btn btn-success d-flex justify-content-center align-items-center h-35px w-90px">' . t('Correct') . '</a>';
        }

    }

    public function getExpectationAttribute()
    {
        $total = $this->total;
        if ($total >= 0 && $total <= 59) {
            return 'Below';
        } elseif ($total >= 60 && $total <= 79) {
            return 'Inline';
        } else {
            return 'Above';
        }
    }
    //search scope
    public function scopeSearch(Builder $query, Request $request)
    {
        return $query
            ->has('student')
            ->when($value = $request->get('orderBy', 'latest'), function (Builder $query) use ($value) {
                $query->when($value == 'latest', function (Builder $query) use ($value) {
                    $query->latest();
                })->when($value == 'name', function (Builder $query) use ($value) {
                    $query->
                    leftJoin('students', 'student_terms.student_id', '=', 'students.id')
                        ->select('student_terms.*', 'students.name as student_name')
                        ->orderBy('students.name');
                })->when($value == 'level', function (Builder $query) use ($value) {
                    $query->
                    leftJoin('students', 'student_terms.student_id', '=', 'students.id')
                        ->select('student_terms.*', 'students.level_id as student_level_id')
                        ->orderBy('students.level_id');
                })->when($value == 'section', function (Builder $query) use ($value) {
                    $query->
                    leftJoin('students', 'student_terms.student_id', '=', 'students.id')
                        ->select('student_terms.*', 'students.grade_name as student_grade_name')
                        ->orderBy('students.grade_name');
                });
            })
            ->when($name = $request->get('student_name', false), function (Builder $query) use ($name) {
                $query->whereHas('student',function (Builder $query) use ($name) {
                    $query->where(DB::raw('LOWER(name)'), 'like', '%' .  strtolower($name) . '%');
                });
            }) ->when($student_id = $request->get('student_id', false), function (Builder $query) use ($student_id) {
                $query->whereHas('student',function (Builder $query) use ($student_id) {
                    $query->where('id',$student_id);
                });
            }) ->when($student_id_number = $request->get('student_id_number', false), function (Builder $query) use ($student_id_number) {
                $query->whereHas('student',function (Builder $query) use ($student_id_number) {
                    $query->where('id_number',$student_id_number);
                });
            })->when($email = $request->get('email', false), function (Builder $query) use ($email) {
                $query->whereHas('student',function (Builder $query) use ($email) {
                    $query->where('email', $email);
                });
            })->when($grade_name = $request->get('grade_name', false), function (Builder $query) use ($grade_name) {
                $query->whereHas('student',function (Builder $query) use ($grade_name) {
                    $query->where('grade_name', $grade_name);
                });
            })->when($school_id = $request->get('school_id', false), function (Builder $query) use ($school_id) {
                $query->whereRelation('student.school',function (Builder $query) use ($school_id) {
                    $query->where('id', $school_id);
                });
            })->when($year_id = $request->get('year_id', false), function (Builder $query) use ($year_id) {
                $query->whereRelation('student.year',function (Builder $query) use ($year_id) {
                    $query->where('id', $year_id);
                });
            })->when($round = $request->get('round', false), function (Builder $query) use ($round) {
                $query->whereRelation('term', function ($query) use ($round){
                    $query->where('round',$round);
                });
            }) ->when($value = $request->get('grade', false), function ($query) use ($value) {
                $query->whereHas('term', function ($query) use ($value) {
                    $query->whereHas('level', function ($query) use ($value) {
                        is_array($value) ? $query->whereIn('grade', $value) : $query->where('grade', $value);
                    });
                });
            })->when($value = $request->get('level_id', false), function (Builder $query) use ($value) {
                $query->whereRelation('term.level', function ($query) use ($value){
                    $query->where('id',$value);
                });
            })->when($value = $request->get('row_id',[]),function (Builder $query) use ($value){
                $query->whereIn('id', $value);
            })
            ->when($value = $request->get('corrected', false), function (Builder $query) use ($value) {
                $query->where('corrected', $value!=2);

            })->when($value = $request->get('deleted_at',false),function (Builder $query) use ($value){
                if ($value == 1){
                    $query->whereNull('student_terms.deleted_at');
                }else{
                    $query->whereNotNull('student_terms.deleted_at')->withTrashed();
                }
            })->when($value = $request->get('start_date', false), function ($query) use ($value) {
                $query->whereDate('created_at', '>=', Carbon::parse($value));
            })->when($value = $request->get('end_date', false), function ($query) use ($value) {
                $query->whereDate('created_at', '<=', Carbon::parse($value));
            })
            ->when($duplicated = $request->get('duplicated', false) == 1, function (Builder $query){
                //get all student terms that have the same student_id and term_id
                $query->groupBy('student_id')
                    ->havingRaw('count(*) > 1');
            });
    }

    public function getExpectationsAttribute()
    {
        if ($this->total >= 0 && $this->total <= 49) {
            return 'Below';
        } else if ($this->total >= 50 && $this->total <= 69) {
            return 'Inline';
        } else {
            return 'Above';
        }
    }


}
