<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MatchQuestionResult extends Model
{
    use SoftDeletes;
    protected $fillable = ['student_id','student_term_id','question_id','match_id','match_question_answer_uid'];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function match_question_answer_uid(): BelongsTo
    {
        return $this->belongsTo(MatchQuestion::class, 'match_question_answer_uid', 'uid');
    }
}
