<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SortQuestionResult extends Model
{
    use SoftDeletes;
    protected $fillable = ['student_id','student_term_id','question_id','sort_question_uid'];
    public function sort_question_uid(): BelongsTo
    {
        return $this->belongsTo(SortQuestion::class, 'sort_question_uid', 'uid');
    }
}
