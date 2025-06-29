<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImportStudentFileError extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'import_student_file_id', 'row_num', 'data', 'updated'
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function studentImportFile()
    {
        return $this->belongsTo(StudentImportFile::class);
    }

    public function scopeFilter(Builder $query): Builder
    {
        $request = request();
        return $query
            ->when($value = $request->get('id', false), function (Builder $query) use ($value) {
                $query->where('id', $value);
            })
            ->when($value = $request->get('row_id', []), function (Builder $query) use ($value) {
                $query->whereIn('id', $value);
            })
            ->when($value = $request->get('row_num', false), function (Builder $query) use ($value) {
                $query->where('row_num', $value);
            })
            ->when($created_at = $request->get('created_at', false), function (Builder $query) use ($created_at) {
                $query->whereDate('created_at', $created_at);
            });
    }
}
