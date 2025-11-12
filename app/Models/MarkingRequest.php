<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Spatie\Activitylog\Traits\LogsActivity;

class MarkingRequest extends Model
{
    use SoftDeletes, LogsActivity;
    protected static $logAttributes = ['school_id', 'status', 'year_id', 'grades', 'section', 'email'];
    protected static $recordEvents = ['updated', 'deleted'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    protected $fillable = [
        'school_id',
        'status',
        'year_id',
        'grades',
        'email',
        'notes',
        'round',
        'section'
    ];
    protected $casts = [
        'grades' => 'array'
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function year(): BelongsTo
    {
        return $this->belongsTo(Year::class);
    }

    public function scopeSearch(Builder $query, Request $request)
    {
        return $query
            ->when($value = $request->get('id', false), function (Builder $query) use ($value) {
                $query->where('id', $value);
            })->when($value = $request->get('id',false),function (Builder $query) use ($value){
                $query->where('id', $value);
            })->when($value = $request->get('year_id', false), function (Builder $query) use ($value) {
                $query->where('year_id', $value);
            })->when($value = $request->get('school_id', false), function (Builder $query) use ($value) {
                $query->where('school_id', $value);
            })->when($value = $request->get('status', false), function (Builder $query) use ($value) {
                $query->where('status', $value);
            })->when($value = $request->get('round', false), function (Builder $query) use ($value) {
                $query->where('round', $value);
            })->when($value = $request->get('row_id', []), function (Builder $query) use ($value) {
                $query->whereIn('id', $value);
            });
    }

    public function getActionButtonsAttribute()
    {
        $actions = [];
        if (\Request::is('manager/*') && \Auth::guard('manager')->check()) {
            $actions = [
                ['key' => 'edit', 'name' => t('Edit'), 'route' => route('manager.marking_requests.edit', $this->id), 'permission' => 'edit marking requests'],
                ['key' => 'delete', 'name' => t('Delete'), 'route' => $this->id, 'permission' => 'delete marking requests'],
            ];

        } else if (\Request::is('school/*') && \Auth::guard('school')->check()) {
            if ($this->status != 'Pending')
                return '';

            $actions = [
                ['key' => 'edit', 'name' => t('Edit'), 'route' => route('school.marking_requests.edit', $this->id)],
                ['key' => 'delete', 'name' => t('Delete'), 'route' => $this->id],
            ];

        }
        return view('general.action_menu')->with('actions', $actions);
    }

    public function getGradesNameAttribute()
    {
        $text = '';
        $i = 0;
        foreach($this->grades as $grade)
        {
            if($this->section == 1)
            {
                $text .= "Grade $grade Arabs". '<br/>';
            }elseif($this->section == 2)
            {
                $text .= "Grade $grade Non-arabs". '<br/>';
            }else{
                $text .= "Grade $grade (Arabs & Non-arabs)". '<br/>';
            }
            $i++;
            if ($i == 3 && count($this->grades) > 3)
            {
                $text .= "...";
                break;
            }
        }
        return $text;
    }

    public function getUncorrectedStudentTermsCountAttribute()
    {
        return StudentTerm::query()
            ->where('corrected', 0)
            ->whereHas('student', function ($query) {
                $query->whereHas('school', function ($query) {
                    $query->where('id', $this->school_id);
                })->when($this->section == 1, function ($query) {
                    $query->where('arab', 1);
                })->when($this->section == 2, function ($query) {
                    $query->where('arab', 0);
                });
            })->whereHas('term', function ($query) {
                $query->where('round', $this->round)
                    ->whereHas('level', function ($query) {
                        $query->where('year_id', $this->year_id)
                            ->whereIn('grade', $this->grades);
                    });
            })
            ->count();
    }
}
