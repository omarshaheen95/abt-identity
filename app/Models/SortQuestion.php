<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SortQuestion extends Model
{
    use SoftDeletes;
    protected $fillable = ['uid','question_id','content','image','ordered'];

}
