<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class SchoolGrade extends Model
{
    use SoftDeletes, LogsActivity;
    protected static $logAttributes = ['school_id','arab','grade', 'september', 'february', 'may'];
    protected static $recordEvents = ['updated', 'deleted'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected $fillable = ['school_id','arab','grade','september','february','may'];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

}
