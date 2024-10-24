<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class TFQuestion extends Model
{
    use SoftDeletes;

    protected $fillable = ['question_id','result'];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

}
