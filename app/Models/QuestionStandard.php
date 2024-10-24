<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Spatie\Activitylog\Traits\LogsActivity;

class QuestionStandard extends Model
{
    use SoftDeletes, LogsActivity;
    protected static $logAttributes = ['standard', 'question_id', 'mark'];
    protected static $recordEvents = ['updated', 'deleted'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected $fillable = ['standard','question_id','mark'];
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
    public function scopeSearch(Builder $query,Request $request): Builder
    {
        return $query->when($value = $request->get('standard'),function (Builder $query) use ($value){
            $query->where('standard','LIKE','%'.$value.'%');
        })->when($value = $request->get('question_content'),function (Builder $query) use ($value){
            $query->whereHas('question',function (Builder $query) use ($value){
                $query->where('content','LIKE','%'.$value.'%');
            });
        })->when($value = $request->get('term_name'),function (Builder $query) use ($value){
            $query->whereHas('question.term',function (Builder $query) use ($value){
                $query->where('name',$value);
            });
        })->when($value = $request->get('year_id'),function (Builder $query) use ($value){
            $query->whereHas('question.term.level',function (Builder $query) use ($value){
                $query->where('year_id',$value);
            });
        })->when($value = $request->get('level_id'),function (Builder $query) use ($value){
            $query->whereHas('question.term',function (Builder $query) use ($value){
                $query->where('level_id',$value);
            });
        })->when($value = $request->get('term_id'),function (Builder $query) use ($value){
            $query->whereHas('question.term',function (Builder $query) use ($value){
                $query->where('id',$value);
            });
        })->when($value = $request->get('subject'),function (Builder $query) use ($value){
            $query->whereHas('question',function (Builder $query) use ($value){
                $query->where('subject',$value);
            });
        })->when($value = $request->get('row_id',[]),function (Builder $query) use ($value){
            $query->whereIn('id', $value);
        });
    }

}
