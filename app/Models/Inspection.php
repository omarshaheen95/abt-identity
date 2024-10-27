<?php

namespace App\Models;

use App\Notifications\InspectionResetPassword;
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

class Inspection extends Authenticatable
{
    use Notifiable, SoftDeletes, LogsActivity,CascadeSoftDeletes;

    protected $fillable = [
        'name', 'email', 'password','last_login', 'last_login_info', 'lang','active','image'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $cascadeDeletes = ['inspection_schools'];
    protected static $logAttributes = ['name', 'email', 'password'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new InspectionResetPassword($token));
    }

    public function inspection_schools():HasMany
    {
        return $this->hasMany(InspectionSchool::class);
    }
    public function login_sessions(){
        return $this->morphMany(LoginSession::class,'model');
    }
    public function scopeSearch(Builder $query, Request $request)
    {
        return $query
            ->when($name = $request->get('name', false), function (Builder $query) use ($name) {
                $query->where(function (Builder $query) use ($name) {
                    $query->where(DB::raw('LOWER(name)'), 'like', '%' . $name . '%');
                });
            })->when($email = $request->get('email', false), function (Builder $query) use ($email) {
                $query->where('email', $email);
            })->when($school_id = $request->get('school_id', false), function (Builder $query) use ($school_id) {
                $query->whereHas('inspection_schools',function (Builder $query)use ($school_id){
                    $query->where('school_id',$school_id);
                } );
            })->when($value = $request->get('row_id',[]),function (Builder $query) use ($value){
                $query->whereIn('id', $value);
            });
    }

    public function getActionButtonsAttribute()
    {
        $actions =  [
            ['key'=>'edit','name'=>t('Edit'),'route'=>route('manager.inspection.edit', $this->id),'permission'=>'edit inspections'],
            ['key'=>'login','name'=>t('Login'),'route'=>route('manager.login-inspection', $this->id),'permission'=>'inspection login'],
            ['key'=>'delete','name'=>t('Delete'),'route'=>$this->id,'permission'=>'delete inspections'],
        ];
        return view('general.action_menu')->with('actions',$actions);
    }

    static function getInspectionSchools(){
        $inspection =Auth::guard('inspection')->user();
        return InspectionSchool::with('school')->where('inspection_id',$inspection->id)
            ->get()->pluck('school');
    }

}
