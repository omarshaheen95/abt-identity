<?php

namespace App\Models;

use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;

class Level extends Model
{
    use SoftDeletes, HasTranslations,CascadeSoftDeletes, LogsActivity;
    protected $fillable = [
        'name', 'grade', 'slug', 'year_id', 'active', 'arab'
    ];
    public $translatable = ['name'];
    protected $cascadeDeletes = ['terms'];

    protected static $logAttributes = [ 'name', 'year_id', 'grade', 'slug', 'active', 'arab'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;


    public function year()
    {
        return $this->belongsTo(Year::class);
    }

    public function terms()
    {
        return $this->hasMany(Term::class);
    }

    public function scopeSearch(Builder $query, Request $request)
    {
        return $query
            ->when($name = $request->get('name', false), function (Builder $query) use ($name) {
                $query->where(function (Builder $query) use ($name) {
                    $query->where(function (Builder $query) use ($name) {
                        $query->where(DB::raw('LOWER(name->"$.ar")'), 'like', '%' . $name . '%')
                            ->orWhere(DB::raw('LOWER(name->"$.en")'), 'like', '%' . $name . '%');
                    });
                });
            })->when($year_id = $request->get('year_id', false), function (Builder $query) use ($year_id) {
                $query->where('year_id', $year_id);
            })->when($value = $request->get('class',false),function (Builder $query) use ($value){
                $query->where('grade', $value);
            })->when($value = $request->get('row_id',[]),function (Builder $query) use ($value){
                $query->whereIn('id', $value);
            });
    }

    public function getActionButtonsAttribute()
    {
        $actions =  [
            ['key'=>'edit','name'=>t('Edit'),'route'=>route('manager.level.edit', $this->id),'permission'=>'edit levels'],
            ['key'=>'delete','name'=>t('Delete'),'route'=>$this->id,'permission'=>'delete levels'],
        ];
        return view('general.action_menu', compact('actions'));

    }

}
