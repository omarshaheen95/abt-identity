<?php

namespace App\Models;

use App\Notifications\StudentResetPassword;
use App\Traits\CleanString;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\LogsActivity;

class Student extends Authenticatable
{
    use Notifiable, SoftDeletes,CascadeSoftDeletes, LogsActivity, CleanString;
    protected static $logAttributes = ['id_number', 'name', 'email', 'school_id', 'level_id', 'year_id', 'grade_name',
        'gender', 'sen', 'g_t', 'arab', 'citizen','assessment_opened', 'abt_id'];
    protected static $recordEvents = ['updated', 'deleted'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    protected $fillable = [
        'name', 'email', 'password', 'school_id', 'year_id', 'level_id', 'nationality', 'grade_name',
        'arab', 'sen', 'g_t', 'gender', 'demo', 'demo_data','assessment_opened', 'dob', 'citizen', 'file_id', 'id_number', 'lang', 'last_login', 'last_login_info', 'abt_id'
    ];
    protected $cascadeDeletes = ['student_terms'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->cleanAttributes(['name', 'email', 'grade_name', 'nationality', 'id_number']);
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower($value);
    }

    //search scope
    public function scopeSearch(Builder $query, Request $request)
    {
        return $query
            ->when($value = $request->get('name', false), function (Builder $query) use ($value) {
                $query->where(DB::raw('LOWER(name)'), 'like', '%' .  strtolower($value) . '%');
            })->when($id = $request->get('id'), function (Builder $query) use ($id) {
                $query->where('id', $id);
            })->when($gender = $request->get('gender', false), function (Builder $query) use ($gender) {
                $query->where('gender', $gender);
            })->when($email = $request->get('email', false), function (Builder $query) use ($email) {
                $query->where('email', $email);
            })->when($id_number = $request->get('id_number', false), function (Builder $query) use ($id_number) {
                $query->where('id_number', $id_number);
            })->when($school_id = $request->get('school_id', false), function (Builder $query) use ($school_id) {
                is_array($school_id) ?
                    $query->whereIn('school_id', $school_id) :
                    $query->where('school_id', $school_id);
            })->when(!is_array($request->get('level_id', false)) ? $level_id = $request->get('level_id', false): $level_id = false, function (Builder $query) use ($level_id) {
                $query->where('level_id', $level_id);
            })->when(is_array($request->get('level_id', [])) ? $level_id = $request->get('level_id', []): $level_id = [], function (Builder $query) use ($level_id) {
                $query->whereIn('level_id', $level_id);
            })->when($year_id = $request->get('year_id', false), function (Builder $query) use ($year_id) {
                $query->whereRelation('level', 'year_id', $year_id);
            })->when($grade = $request->get('grade', false), function (Builder $query) use ($grade) {
                $query->whereHas('level',function (Builder $query) use ($grade) {
                    is_array($grade)?$query->whereIn('grade', $grade):$query->where('grade', $grade);
                });
            })->when($value = $request->get('has_abt_id', false), function (Builder $query) use ($value) {
                if ($value==1){
                    $query->whereNotNull('abt_id');
                }elseif ($value==2){
                    $query->whereNull('abt_id');
                }
            })->when($year_id = $request->get('student_year_id', false), function (Builder $query) use ($year_id) {
                $query->where('year_id', $year_id);
            })->when($value = $request->get('file_id', false), function (Builder $query) use ($value) {
                $query->where('file_id', $value);
            })->when($created_at = $request->get('created_at', false), function (Builder $query) use ($created_at) {
                $query->whereDate('created_at', $created_at);
            })->when($value = $request->get('row_id',[]),function (Builder $query) use ($value){
                $query->whereIn('id', $value);
            })->when($value = $request->get('class',false),function (Builder $query) use ($value){
                $query->whereRelation('level','grade','=',$value);
            })->when($value = $request->get('deleted_at',false),function (Builder $query) use ($value){
                if ($value == 1){
                    $query->whereNull('deleted_at');
                }else{
                    $query->whereNotNull('deleted_at')->withTrashed();
                }
            })->when($value = $request->get('orderBy', 'latest'), function (Builder $query) use ($value){
                $query->when($value == 'latest', function (Builder $query) use ($value){
                    $query->latest();
                })->when($value == 'name', function (Builder $query) use ($value){
                    $query->orderBy('name');
                })->when($value == 'level', function (Builder $query) use ($value){
                    $query->orderBy('level_id');
                })->when($value == 'section', function (Builder $query) use ($value){
                    $query->orderBy('grade_name');
                })->when($value == 'arab', function (Builder $query) use ($value){
                    $query->orderBy('arab')->orderBy('level_id');
                });
            })->when($value = $request->get('orderBy2', 'latest'), function (Builder $query) use ($value){
                $query->when($value == 'latest', function (Builder $query) use ($value){
                    $query->latest();
                })->when($value == 'name', function (Builder $query) use ($value){
                    $query->orderBy('name');
                })->when($value == 'level', function (Builder $query) use ($value){
                    $query->orderBy('level_id');
                })->when($value == 'section', function (Builder $query) use ($value){
                    $query->orderBy('grade_name');
                })->when($value == 'arab', function (Builder $query) use ($value){
                    $query->orderBy('arab')->orderBy('level_id');
                });
            })->when($value = $request->get('sen', false), function (Builder $query) use ($value) {
                $query->where('sen', $value!=2);
            })->when($value = $request->get('g_t', false), function (Builder $query) use ($value) {
                $query->where('g_t', $value!=2);
            })->when($value = $request->get('citizen', false), function (Builder $query) use ($value) {
                $query->where('citizen', $value!=2);
            })->when($value = $request->get('arab_status', false), function (Builder $query) use ($value) {
                $query->where('arab', $value!=2);
            })->when($value = $request->get('class_name') ?: $request->get('grades_names') ,function (Builder $query) use ($value){
                $query->whereIn('grade_name', $value);
            })->when($value = $request->get('grade_name',false),function (Builder $query) use ($value){
                $query->where('grade_name', $value);
            })->when($value= $request->get('start_date',false),function (Builder $query) use ($value){
                $query->whereDate('created_at', '>=',$value);
            })->when($value= $request->get('end_date',false),function (Builder $query) use ($value){
                $query->whereDate('created_at', '<=',$value);
            });
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new StudentResetPassword($token));
    }
    public function getDemoDataAttribute($value)
    {
        return json_decode($value);
    }
    protected function setDemoDataAttribute($value)
    {
        $this->attributes['demo_data'] = json_encode($value);
    }

    public function login_sessions(){
        return $this->morphMany(LoginSession::class,'model');
    }
    //actions buttons

    public function getActionButtonsAttribute()
    {
        $actions=[];
        if (\request()->is('manager/*')){
            if ($this->deleted_at && Auth::guard('manager')->user()->hasDirectPermission('restore deleted students')){
                    return '<button  onclick="restore('.$this->id.')" class="btn btn-warning d-flex justify-content-center align-items-center h-35px w-90px btn_restore">' . t('Restore') . '</button>';
            }else{
                $actions =  [
                    ['key'=>'edit','name'=>t('Edit'),'route'=>route('manager.student.edit', $this->id),'permission'=>'edit students'],
                    ['key' => 'login', 'name' => t('Card'), 'route' => route('manager.student-card', $this->id), 'permission' =>'export students cards'],
                    ['key'=>'login','name'=>t('Login'),'route'=>route('manager.student.student-login', $this->id),'permission'=>'student login'],
                    ['key'=>'login','name'=>t('Student Report'),'route'=>route('manager.student.student-report', $this->id),'permission'=>'student login'],
                    ['key'=>'login','name'=>t('Activity Records'),'route'=>route('manager.student.activity-records', $this->id), 'permission' => 'student login'],
                    ['key'=>'delete','name'=>t('Delete'),'route'=>$this->id,'permission'=>'delete students'],
                    ['key' => 'blank', 'name' => t('New Student Report'), 'route' => route('manager.report.student-report', $this->id), 'permission' => 'export students cards'],
                    ['key' => 'blank', 'name' => t('New Student Report Card'), 'route' => route('manager.report.student-report-card', $this->id), 'permission' => 'export students cards'],

                ];
                return view('general.action_menu')->with('actions',$actions);
            }
        }
        elseif (\request()->is('school/*')){
            $student_login = \Auth::guard('school')->user()->student_login;
            $allow_reports = auth()->guard('school')->user()->allow_reports;

            $actions =  [
                ['key'=>'edit','name'=>t('Edit'),'route'=>route('school.student.edit', $this->id)],
                ['key' => 'login', 'name' => t('Card'), 'route' => route('school.student-card', $this->id)],
                $allow_reports?['key' => 'student_report','target' => '_blank', 'name' => t('Student Report'), 'route' => route('school.student.student-report', $this->id)]:null,
                $allow_reports?['key' => 'student_card_report','target' => '_blank', 'name' => t('Student Report Card'), 'route' => route('school.student.report-card', $this->id)]:null,
                $student_login?['key' => 'login', 'name' => t('Login'), 'route' => route('school.student.student-login', $this->id)]:null,
                ['key'=>'login','name'=>t('Activity Records'),'route'=>route('school.student.activity-records', $this->id)],

            ];

        }elseif (\request()->is('inspection/*')){
            $actions =  [
                ['key'=>'login','name'=>t('Login'),'route'=>route('inspection.student.student-login', $this->id)],
            ];
        }
        return view('general.action_menu')->with('actions',$actions);

    }



    //relations
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function year()
    {
        return $this->belongsTo(Year::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function student_terms():HasMany{
        return $this->hasMany(StudentTerm::class,'student_id','id')->with('term');
    }


}
