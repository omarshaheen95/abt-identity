<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Contracts\Activity as ActivityContract;

/**
 * Spatie\Activitylog\Models\Activity.
 *
 * @property int $id
 * @property string|null $log_name
 * @property string $description
 * @property string|null $subject_type
 * @property int|null $subject_id
 * @property string|null $causer_type
 * @property int|null $causer_id
 * @property string|null $event
 * @property string|null $batch_uuid
 * @property \Illuminate\Support\Collection|null $properties
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $causer
 * @property-read \Illuminate\Support\Collection $changes
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $subject
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Spatie\Activitylog\Models\Activity causedBy(\Illuminate\Database\Eloquent\Model $causer)
 * @method static \Illuminate\Database\Eloquent\Builder|\Spatie\Activitylog\Models\Activity forBatch(string $batchUuid)
 * @method static \Illuminate\Database\Eloquent\Builder|\Spatie\Activitylog\Models\Activity forEvent(string $event)
 * @method static \Illuminate\Database\Eloquent\Builder|\Spatie\Activitylog\Models\Activity forSubject(\Illuminate\Database\Eloquent\Model $subject)
 * @method static \Illuminate\Database\Eloquent\Builder|\Spatie\Activitylog\Models\Activity hasBatch()
 * @method static \Illuminate\Database\Eloquent\Builder|\Spatie\Activitylog\Models\Activity inLog($logNames)
 * @method static \Illuminate\Database\Eloquent\Builder|\Spatie\Activitylog\Models\Activity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Spatie\Activitylog\Models\Activity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Spatie\Activitylog\Models\Activity query()
 */
class Activity extends Model implements ActivityContract
{
    public $guarded = [];

    protected $casts = [
        'properties' => 'collection',
    ];

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

    public function getExtraProperty(string $propertyName, $defaultValue = null): mixed
    {
        return Arr::get($this->properties->toArray(), $propertyName, $defaultValue);
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

    public function scopeForEvent(Builder $query, string $event): Builder
    {
        return $query->where('event', $event);
    }

    public function scopeHasBatch(Builder $query): Builder
    {
        return $query->whereNotNull('batch_uuid');
    }

    public function scopeForBatch(Builder $query, string $batchUuid): Builder
    {
        return $query->where('batch_uuid', $batchUuid);
    }

    public function getActionButtonsAttribute()
    {
        $actions = [
            ['key' => 'show', 'name' => t('Show'), 'route' => route('manager.activity-log.show', $this->id)],
            ['key' => 'delete', 'name' => t('Delete'), 'route' => $this->id]
        ];
        return view('general.action_menu')->with('actions', $actions);

    }

    //get action route according type of activity and subject
    public function getActionRouteAttribute()
    {
        if (in_array($this->description, ['created', 'updated'])) {
            // Get the model name without namespace
            $subjectType = \Str::plural(class_basename($this->subject_type));

            $routes  = [];
            // Convert to kebab case for route naming (e.g., UserProfile becomes user-profile)
            $routeSegment = \Str::kebab($subjectType);

            //All Routes Cases
            // Try the route with the exact plural form with kebab-case (with s)
            $routes[] = "manager.{$routeSegment}.edit";

            // Try kebab-case without (s)
            $routes[] = 'manager.' . substr($routeSegment, 0, strrpos($routeSegment, '-')) . substr($routeSegment, strrpos($routeSegment, '-')) . '.edit';

            // Try with simple lowercase plural with (s) like students
            $routes[] = 'manager.' . strtolower($subjectType) . '.edit';

            // Try with simple lowercase plural without (s) like student
            $routes[] = 'manager.' . strtolower(substr($subjectType, 0, -1)) . '.edit';

            //Try with snake_case plural (with s) like user_profiles
            $routes[] = 'manager.' . \Str::snake($subjectType) . '.edit';

            //Try with snake_case singular (without s) like user_profile
            $routes[] = 'manager.' . \Str::snake(substr($subjectType, 0, -1)) . '.edit';

            //Excluded routes

            foreach ($routes as $route) {
                if (\Route::has($route)) {
                    return route($route, $this->subject_id);
                }
            }


        }

        return null;
    }

    public function scopeFilter(Builder $query)
    {
        $request = \request();
        return $query
            ->when($value = $request->get('causer_type', false), function (Builder $query) use ($value) {
                $query->where('causer_type', $value);
            })->when($value = $request->get('causer_id', false), function (Builder $query) use ($value) {
                $query->where('causer_id', $value);
            })->when($value = $request->get('subject_type', false), function (Builder $query) use ($value) {
                $query->where('subject_type', $value);
            })->when($value = $request->get('subject_id', false), function (Builder $query) use ($value) {
                $query->where('subject_id', $value);
            })
            ->when($value = $request->get('email', false), function (Builder $query) use ($value) {
                $query->where(function (Builder $query) use ($value) {
                    $query->whereHasMorph('causer', [Manager::class], function (Builder $query) use ($value) {
                        $query->where('email', 'like', "%{$value}%");
                    })->orWhereHasMorph('causer', [School::class], function (Builder $query) use ($value) {
                        $query->where('email', 'like', "%{$value}%");
                    })->orWhereHasMorph('causer', [Inspection::class], function (Builder $query) use ($value) {
                        $query->where('email', 'like', "%{$value}%");
                    });
                });
            })->when($value = $request->get('name', false), function (Builder $query) use ($value) {
                $query->where(function (Builder $query) use ($value) {
                    $query->whereHasMorph('causer', [Manager::class], function (Builder $query) use ($value) {
                        $query->where('name', 'like', "%{$value}%");
                    })->orWhereHasMorph('causer', [School::class], function (Builder $query) use ($value) {
                        $query->where('name', 'like', "%{$value}%");
                    })->orWhereHasMorph('causer', [Inspection::class], function (Builder $query) use ($value) {
                        $query->where('name', 'like', "%{$value}%");
                    });
                });
            })->when($value = $request->get('type', false), function ($query) use ($value) {
                $query->when($value != 'other', function ($query) use ($value) {
                    $query->where('description', $value);
                })
                    ->when($value == 'other', function ($query) use ($value) {
                        $query->whereNotIn('description', ['created', 'updated', 'deleted']);
                    });
            })->when($value = $request->get('date_start', false), function ($query) use ($value) {
                $query->whereDate('created_at', '>=', Carbon::parse($value));
            })->when($value = $request->get('date_end', false), function ($query) use ($value) {
                $query->whereDate('created_at', '<=', Carbon::parse($value));
            })->when($value = $request->get('row_id', []), function (Builder $query) use ($value) {
                $query->whereIn('id', $value);
            });
    }
}
