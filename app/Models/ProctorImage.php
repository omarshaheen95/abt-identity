<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProctorImage extends Model
{
    use SoftDeletes;

    protected $table = 'proctor_images';

    protected $fillable = [
        'student_term_id', 'type', 'file_path', 'capture_minute'
    ];

    protected static function boot()
    {
        parent::boot();

        // Delete the image file from disk when the record is force deleted
        static::deleting(function ($proctorImage) {
            if ($proctorImage->isForceDeleting() && $proctorImage->file_path && file_exists(public_path($proctorImage->file_path))) {
                unlink(public_path($proctorImage->file_path));
            }
        });
    }

    public function studentTerm()
    {
        return $this->belongsTo(StudentTerm::class);
    }
}
