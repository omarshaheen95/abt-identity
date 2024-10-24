<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentTermStandard extends Model
{
    use SoftDeletes;
    protected $fillable = ['student_term_id','question_standard_id','mark'];
}
