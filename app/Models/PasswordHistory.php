<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * One previously used password hash. Written on every password change so the
 * expiry policy cannot be defeated by setting the same password again.
 */
class PasswordHistory extends Model
{
    public $timestamps = false;

    protected $fillable = ['model_type', 'model_id', 'password', 'created_at'];

    protected $casts = ['created_at' => 'datetime'];

    protected $hidden = ['password'];

    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
