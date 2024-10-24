<?php

namespace App\Models;

use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArticleQuestionResult extends Model
{
    use SoftDeletes;

    protected $fillable = ['question_id', 'student_term_id', 'text_answer', 'answer_file_path','mark'];
}
