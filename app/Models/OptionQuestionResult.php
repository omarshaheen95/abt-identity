<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OptionQuestionResult extends Model
{
    use SoftDeletes;
    protected $fillable = ['student_id','student_term_id','question_id','option_id'];

}
