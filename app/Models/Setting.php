<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends Model
{
    use SoftDeletes;
    //type : 'text', 'textarea', 'checkbox', 'password', 'file', 'color'
    protected $fillable = [
        'name', 'key', 'value', 'ordered', 'type'
    ];

    protected $table = 'settings';
}
