<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionFile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'term_id',
        'level_id',
        'original_file_name',
        'file_name',
        'created_rows_count',
        'updated_rows_count',
        'deleted_rows_count',
        'failed_rows_count',
        'file_path',
        'status',
        'process_type',
        'delete_with_rows',
        'error',
        'failures',
        'author_type',
        'author_id',
    ];


    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }


    public function getFailuresAttribute($value)
    {
        return is_null($value) ? [] : $value;
    }
    public function author()
    {
        return $this->morphTo();
    }

    public function getActionButtonsAttribute()
    {
        //check if guard name is admin or delegate

        $guard = getGuard();
        if ($this->status == 'Failed') {
            $actions = [
                ['key' => 'show', 'name' => t('Show Errors'), 'route' => route($guard.'.question-file.show', [$this->id]),'permission'=>'edit imported questions'],
                ['key' => 'delete', 'name' => t('Delete'), 'route' => $this->id,'permission'=>'delete imported questions'],
            ];
        } else {
            $actions = [
                ['key' => 'delete', 'name' => t('Delete'), 'route' => $this->id, 'permission'=>'delete imported questions'],
            ];
        }
        return view('general.action_menu')->with('actions',$actions);
    }

    public function scopeFilter(Builder $query,$request = null)
    {
        if (!$request){
            $request = \request();
        }
        return $query
            ->when($value = $request->get('id', false), function (Builder $query) use ($value) {
                $query->where('id', $value);
            })->when($value = $request->get('subject_id', false), function (Builder $query) use ($value) {
                $query->where('subject_id', $value);
            })->when($value = $request->get('grade', false), function (Builder $query) use ($value) {
                $query->where('grade', $value);
            })->when($value = $request->get('status', false), function (Builder $query) use ($value) {
                $query->where('status', $value);
            })->when($value = $request->get('process_type', false), function (Builder $query) use ($value) {
                $query->where('process_type', $value);
            })
            ->when(getGuard() == 'school', function (Builder $query) {
                $query->where(function (Builder $query) {
                    $query->whereHasMorph('author', [School::class], function (Builder $query) {
                        $query->where('id', auth()->user()->id)
                            ->where('author_type', School::class);
                    })->orWhereHasMorph('author', [Teacher::class], function (Builder $query) {
                        $query->whereHas('school', function (Builder $query) {
                            $query->where('id', auth()->user()->school_id);
                        });
                    });
                });
            })
            ->when(getGuard() == 'teacher', function (Builder $query) {
                $query->whereHasMorph('author', [Teacher::class], function (Builder $query) {
                    $query->where('id', auth()->user()->id)
                        ->where('author_type', Teacher::class);
                });
            });
        }
}
