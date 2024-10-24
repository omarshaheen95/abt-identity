<?php

namespace App\Models;

use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;

class Term extends Model
{
    use SoftDeletes, HasTranslations,CascadeSoftDeletes, LogsActivity;
    protected static $logAttributes = [ 'name', 'level_id', 'round', 'active', 'duration','show_calculator'];

    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    //round 'september', 'february', 'may'
    protected $fillable = [
        'name', 'level_id', 'round', 'active','demo', 'duration'
    ];
    public $translatable = ['name'];
    protected $cascadeDeletes = ['student_terms','question'];



    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function question():HasMany{
        return $this->hasMany(Question::class,'term_id');
    }

    public function student_terms(): HasMany
    {
        return $this->hasMany(StudentTerm::class);
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
                $query->whereHas('level', function ($query) use ($year_id) {
                    $query->where('year_id', $year_id);
                });
            })->when($round = $request->get('round', false), function (Builder $query) use ($round) {
                $query->where('round', $round);
            })->when($value = $request->get('row_id',[]),function (Builder $query) use ($value){
                $query->whereIn('id', $value);
            })->when($value = $request->get('id', false), function (Builder $query) use ($value) {
                $query->where('id', $value);
            })->when($value = $request->get('active', false), function (Builder $query) use ($value) {
                $query->where('active', !($value == '2'));
            });
    }


    public function getActionButtonsAttribute()
    {
        $actions = [];
        $actions [] = ['key' => 'edit', 'name' => t('Edit'), 'route' => route('manager.term.edit', $this->id), 'permission' => 'edit terms'];
        if ($this->question->count() > 0) {
            $actions [] = ['key' => 'questions', 'name' => t('Questions'), 'route' => route('manager.term.questions', $this->id), 'permission' => 'show questions content'];
            $actions [] = ['key' => 'preview', 'name' => t('Preview'), 'route' => route('manager.term.preview', $this->id), 'permission' => 'show questions content'];
        }
        $actions [] = ['key' => 'questions_structure', 'name' => t('Questions Structure'), 'route' => route('manager.term.questions-structure', $this->id), 'permission' => 'show questions structure'];

        $actions [] = ['key' => 'delete', 'name' => t('Delete'), 'route' => $this->id, 'permission' => 'delete terms'];

        return view('manager.settings.action_menu')->with('actions', $actions);
    }


}
