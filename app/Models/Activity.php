<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Contracts\Activity as ActivityContract;

class Activity extends Model implements ActivityContract
{
    public $guarded = [];

    protected $casts = [
        'properties' => 'collection',
    ];

    public function getActionButtonsAttribute()
    {
        return [
            ['key' => 'show', 'name' => t('Show'), 'route' => route('manager.activity-log.show', $this->id)],
            ['key' => 'delete', 'name' => t('Delete'), 'route' => $this->id]
        ];
    }

    //get action route according type of activity and subject
    public function getActionRouteAttribute()
    {
        if ($this->description == 'created' || $this->description == 'updated') {
            //check subject_type instance of model
            if (class_basename($this->subject_type) == 'Manager') {
                return route('manager.manager.edit', $this->subject_id);
            }elseif (class_basename($this->subject_type) == 'School') {
                return route('manager.school.edit', $this->subject_id);
            }elseif (class_basename($this->subject_type) == 'Inspection') {
                return route('manager.inspection.edit', $this->subject_id);
            }elseif (class_basename($this->subject_type) == 'Student') {
                return route('manager.student.edit', $this->subject_id);
            }elseif (class_basename($this->subject_type) == 'Year') {
                return route('manager.year.edit', $this->subject_id);
            }elseif (class_basename($this->subject_type) == 'Level') {
                return route('manager.level.edit', $this->subject_id);
            }elseif (class_basename($this->subject_type)  == 'Term') {
                return route('manager.term.edit', $this->subject_id);
            }elseif (class_basename($this->subject_type) == 'StudentTerm') {
                return route('manager.student_term.edit', $this->subject_id);
            }elseif (class_basename($this->subject_type) == 'MarkingRequest') {
                return route('manager.marking_requests.edit', $this->subject_id);
            } else {
                return null;
            }
        } else  {
            return null;
        }
    }

    public function __construct(array $attributes = [])
    {
        if (!isset($this->connection)) {
            $this->setConnection(config('activitylog.database_connection'));
        }

        if (!isset($this->table)) {
            $this->setTable(config('activitylog.table_name'));
        }

        parent::__construct($attributes);
    }

    public function subject(): MorphTo
    {
        if (config('activitylog.subject_returns_soft_deleted_models')) {
            return $this->morphTo()->withTrashed();
        }

        return $this->morphTo();
    }

    public function causer(): MorphTo
    {
        return $this->morphTo();
    }

    public function getExtraProperty(string $propertyName)
    {
        return Arr::get($this->properties->toArray(), $propertyName);
    }

    public function changes(): Collection
    {
        if (!$this->properties instanceof Collection) {
            return new Collection();
        }

        return $this->properties->only(['attributes', 'old']);
    }

    public function getChangesAttribute(): Collection
    {
        return $this->changes();
    }

    public function scopeInLog(Builder $query, ...$logNames): Builder
    {
        if (is_array($logNames[0])) {
            $logNames = $logNames[0];
        }

        return $query->whereIn('log_name', $logNames);
    }

    public function scopeCausedBy(Builder $query, Model $causer): Builder
    {
        return $query
            ->where('causer_type', $causer->getMorphClass())
            ->where('causer_id', $causer->getKey());
    }

    public function scopeForSubject(Builder $query, Model $subject): Builder
    {
        return $query
            ->where('subject_type', $subject->getMorphClass())
            ->where('subject_id', $subject->getKey());
    }

    public function scopeFilter(Builder $query)
    {
        $request = \request();
        return $query
            ->when($value = $request->get('causedByManager', false), function (Builder $query) use ($value) {
                $query->where('causer_type', Manager::class)
                    ->where('causer_id', $value);
            })
            ->when($value = $request->get('causedBySchool', false), function (Builder $query) use ($value) {
                $query->where('causer_type', School::class)
                    ->where('causer_id', $value);
            })
            ->when($value = $request->get('email', false), function (Builder $query) use ($value) {
            $query->where(function (Builder $query) use ($value){
                $query->whereHasMorph('causer', [Manager::class], function (Builder $query) use ($value) {
                    $query->where('email', 'like', "%{$value}%");
                })->orWhereHasMorph('causer', [School::class], function (Builder $query) use ($value) {
                    $query->where('email', 'like', "%{$value}%");
                })->orWhereHasMorph('causer', [Inspection::class], function (Builder $query) use ($value) {
                    $query->where('email', 'like', "%{$value}%");
                });
            });
        })->when($value = $request->get('name', false), function (Builder $query) use ($value) {
            $query->where(function (Builder $query) use ($value){
                $query->whereHasMorph('causer', [Manager::class], function (Builder $query) use ($value) {
                    $query->where('name', 'like', "%{$value}%");
                })->orWhereHasMorph('causer', [School::class], function (Builder $query) use ($value) {
                    $query->where('name', 'like', "%{$value}%");
                })->orWhereHasMorph('causer', [Inspection::class], function (Builder $query) use ($value) {
                    $query->where('name', 'like', "%{$value}%");
                });
            });
        })->when($value = $request->get('type', false), function ($query) use ($value) {
            $query->where('description', $value);
        })->when($value = $request->get('date_start', false), function ($query) use ($value) {
            $query->whereDate('created_at', '>=', Carbon::parse($value));
        })->when($value = $request->get('date_end', false), function ($query) use ($value) {
            $query->whereDate('created_at', '<=', Carbon::parse($value));
        })->when($value = $request->get('row_id', []), function (Builder $query) use ($value) {
            $query->whereIn('id', $value);
        });
    }
}
