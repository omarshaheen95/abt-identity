<?php

namespace App\Models;

use App\Models\Scopes\ApprovedScope;
use App\Notifications\SchoolResetPassword;
use App\Traits\Pathable;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;

class School extends Authenticatable
{
    use Notifiable, SoftDeletes, HasTranslations,CascadeSoftDeletes, LogsActivity;
    protected static $logAttributes = ['name', 'email', 'password', 'logo', 'curriculum_type', 'country', 'active', 'available_year_id'];
    protected static $recordEvents = ['updated', 'deleted'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    protected $fillable = [
        'name', 'email', 'password', 'logo', 'url', 'mobile', 'country', 'curriculum_type', 'last_login', 'lang', 'active','student_login', 'last_login_info',
        'available_year_id'
        ];
    protected $cascadeDeletes = ['students','school_grades', 'inspections_school'];

    public $translatable = ['name'];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new SchoolResetPassword($token));
    }

    public function getLogoAttribute($value)
    {
        return is_null($value) ? null : asset($value);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function inspections_school()
    {
        return $this->hasMany(InspectionSchool::class);
    }
    public function inspections()
    {
        return $this->belongsToMany(Inspection::class, 'inspection_schools', 'school_id', 'inspection_id')->whereNull('inspection_schools.deleted_at');
    }
    public function login_sessions(){
        return $this->morphMany(LoginSession::class,'model');
    }
    public function scopeSearch(Builder $query, Request $request)
    {
        return $query
            ->when($name = $request->get('name', false), function (Builder $query) use ($name) {
                $query->where(function (Builder $query) use ($name) {
                    $query->where(DB::raw('LOWER(name->"$.ar")'), 'like', '%' . $name . '%')
                        ->orWhere(DB::raw('LOWER(name->"$.en")'), 'like', '%' . $name . '%');
                });
            })->when($email = $request->get('email', false), function (Builder $query) use ($email) {
                $query->where('email', $email);
            })->when($mobile = $request->get('mobile', false), function (Builder $query) use ($mobile) {
                $query->where('mobile', $mobile);
            })->when($curriculum_type = $request->get('curriculum_type', false), function (Builder $query) use ($curriculum_type) {
                $query->where('curriculum_type', $curriculum_type);
            })->when($country = $request->get('country', false), function (Builder $query) use ($country) {
                $query->where('country', $country);
            })->when($request->get('active',false) == 1,function (Builder $query){
                $query->where('active', 1);
            })->when($request->get('active',false) == 2,function (Builder $query){
                $query->where('active', 0);
            })->when($value = $request->get('row_id',[]),function (Builder $query) use ($value){
                $query->whereIn('id', $value);
            });
    }

    //active Scope
    public function scopeActive(Builder $query)
    {
        return $query->where('active', 1);
    }

    public function getActionButtonsAttribute()
    {
        $actions=[];
        if (\request()->is('manager/*')){
            $actions =  [
                ['key'=>'edit','name'=>t('Edit'),'route'=>route('manager.school.edit', $this->id),'permission'=>'edit schools'],
                ['key'=>'login','name'=>t('School Login'),'route'=>route('manager.school-login', $this->id),'permission'=>'school login'],
                ['key'=>'terms_scheduling','name'=>t('Assessments Scheduling'),'route'=>route('manager.school.scheduling.index', $this->id),'permission'=>'school terms scheduling'],
                ['key'=>'delete','name'=>t('Delete'),'route'=>$this->id,'permission'=>'delete schools'],
            ];
        }else if (\request()->is('inspection/*')){
            $actions =  [
                ['key'=>'login','name'=>t('School Login'),'route'=>route('inspection.school-login', $this->id)],
            ];
        }

        return view('general.action_menu')->with('actions',$actions);

    }

    public function school_grades(): HasMany
    {
        return $this->hasMany(SchoolGrade::class);
    }

    public function male_student(): HasMany
    {
        return $this->hasMany(Student::class)->where('gender','boy');
    }
    public function female_student(): HasMany
    {
        return $this->hasMany(Student::class)->where('gender','girl');
    }





}
