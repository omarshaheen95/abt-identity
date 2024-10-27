<?php

namespace App\Models;

use App\Notifications\ManagerResetPassword;
use App\Traits\Pathable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class Manager extends Authenticatable
{
    use Notifiable, SoftDeletes, HasRoles, LogsActivity;

    protected $fillable = [
        'name', 'email', 'password', 'avatar', 'lang', 'last_login', 'last_login_info', 'approved'
    ];
    protected $pathAttribute = [
        'avatar'
    ];
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected static $recordEvents = ['created', 'updated','deleted'];
    protected static $logAttributes = ['name', 'email', 'password', 'approved'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ManagerResetPassword($token));
    }

    public function getActionButtonsAttribute()
    {
        $actions = [
            ['key' => 'edit', 'name' => t('Edit'), 'route' => route('manager.manager.edit', $this->id), 'permission' => 'edit managers'],
            ['key' => 'edit_permissions', 'name' => t('Edit Permissions'), 'route' => route('manager.manager.edit-permissions', $this->id), 'permission' => 'edit managers permissions'],
            ['key'=>'track_logs','name'=>t('Track Activity'),'route'=>route('manager.activity-log.index', ['causedByManager'=>$this->id]),'permission'=>'show activity logs'],
            ['key' => 'delete', 'name' => t('Delete'), 'route' => $this->id, 'permission' => 'delete managers'],
        ];
        return view('general.action_menu')->with('actions', $actions);
    }
    public function login_sessions(){
        return $this->morphMany(LoginSession::class,'model');
    }

    public function scopeSearch(Builder $query, Request $request): Builder
    {
        return $query->when($value = $request->get('email'), function (Builder $query) use ($value) {
            $query->where('email', $value);
        })->when($value = $request->get('name'), function (Builder $query) use ($value) {
            $query->where('name', 'LIKE', '%' . $value . '%');
        })->when($value = $request->get('id', false), function (Builder $query) use ($value) {
            $query->where('id', $value);
        })->when($request->get('approved', false) == 1, function (Builder $query) {
                $query->where('approved', 1);
            })->when($request->get('approved', false) == 2, function (Builder $query) {
                $query->where('approved', 0);
            })->when($value = $request->get('row_id', []), function (Builder $query) use ($value) {
                $query->whereIn('id', $value);
            });
    }

}
