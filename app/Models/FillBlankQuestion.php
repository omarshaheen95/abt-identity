<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FillBlankQuestion extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'uid',
        'question_id',
        'content',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
