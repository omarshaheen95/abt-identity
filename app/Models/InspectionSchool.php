<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InspectionSchool extends Model
{
    use SoftDeletes;
    public $fillable = ['school_id','inspection_id'];

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
