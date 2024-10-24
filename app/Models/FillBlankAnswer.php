<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FillBlankAnswer extends Model
{
    use SoftDeletes;
    protected $fillable = [
      'student_term_id','question_id','fill_blank_question_id','answer_fill_blank_question_uid'
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function fillBlankQuestion(): BelongsTo
    {
        return $this->belongsTo(FillBlankQuestion::class);
    }
    public function answerFillBlankQuestionUid(): BelongsTo
    {
        return $this->belongsTo(FillBlankQuestion::class, 'answer_fill_blank_question_uid', 'uid');
    }

}
