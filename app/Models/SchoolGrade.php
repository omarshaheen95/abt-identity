<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SchoolGrade extends Model
{
    use SoftDeletes, LogsActivity;
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['school_id','arab','grade', 'september', 'february', 'may'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
    protected static $recordEvents = ['updated', 'deleted'];
    protected $fillable = ['school_id','arab','grade','september','february','may'];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

}
