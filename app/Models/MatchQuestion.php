<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MatchQuestion extends Model
{
    use SoftDeletes;
    protected $fillable = ['uid','question_id','content','result','image'];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

}
