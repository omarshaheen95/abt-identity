<?php

namespace App\Models;

use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Spatie\Activitylog\Traits\LogsActivity;

class Question extends Model
{
    use SoftDeletes,CascadeSoftDeletes, LogsActivity;
    protected static $logAttributes = ['term_id','type','content','image','subject_id','audio','mark','question_reader', 'formula'];
    protected static $recordEvents = ['updated', 'deleted'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;


    protected $fillable = ['term_id','type','content','image','subject_id','audio','mark','question_reader'];
    protected $cascadeDeletes = [
        'tf_question','tf_question_result',
        'option_question','option_question_result',
        'match_question','match_question_result',
        'sort_question','sort_question_result',
        'question_standard',
        ];

    /**
     * Questions types [id]
     * 1=> True or False
     * 2=> Choose Correct Answer
     * 3=> Matching
     * 4=> Sorting
     * 5=> Article
     * 6=> FillBlank
     */
    public static function getQuestionTypes(){
        return[
            ['name'=>t('True or False'),'value'=>'true_false'],
            ['name'=>t('Choose the correct answer'),'value'=>'multiple_choice'],
            ['name'=>t('Matching'),'value'=>'matching'],
            ['name'=>t('Sorting'),'value'=>'sorting'],
            ['name'=>t('Article'),'value'=>'article'],
            ['name'=>t('Fill Blank'),'value'=>'fill_blank'],
        ];
    }


    public function tf_question():HasOne{
        return $this->hasOne(TFQuestion::class,'question_id');
    }

    public function question_standard():HasOne{
        return $this->hasOne(QuestionStandard::class,'question_id');
    }
    public function option_question():HasMany{
        return $this->hasMany(OptionQuestion::class,'question_id');
    }

    public function fill_blank_question(): HasMany
    {
        return $this->hasMany(FillBlankQuestion::class, 'question_id');
    }

    public function match_question():HasMany{
        return $this->hasMany(MatchQuestion::class,'question_id');
    }

    public function sort_question():HasMany{
        return $this->hasMany(SortQuestion::class,'question_id');
    }
    public function tf_question_result():HasMany{
        return $this->hasMany(TFQuestionResult::class,'question_id');
    }
    public function option_question_result():HasMany{
        return $this->hasMany(OptionQuestionResult::class,'question_id');
    }
    public function match_question_result():HasMany{
        return $this->hasMany(MatchQuestionResult::class,'question_id');
    }
    public function sort_question_result():HasMany{
        return $this->hasMany(SortQuestionResult::class,'question_id');
    }
    public function article_question_result():HasMany{
        return $this->hasMany(ArticleQuestionResult::class,'question_id');
    }
    public function fill_blank_answer(): HasMany
    {
        return $this->hasMany(FillBlankAnswer::class, 'question_id');
    }
    public function term():BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    public function scopeSearch(Builder $query,Request $request): Builder
    {
        return $query->when($value = $request->get('content'),function (Builder $query) use ($value){
            $query->where('content','LIKE','%'.$value.'%');
        })->when($value = $request->get('term_name'),function (Builder $query) use ($value){
            $query->whereHas('term',function (Builder $query) use ($value){
                $query->where('name','LIKE','%'.$value.'%');
            });
        })->when($value = $request->get('year_id'),function (Builder $query) use ($value){
            $query->whereHas('term.level',function (Builder $query) use ($value){
                $query->where('year_id',$value);
            });
        })->when($value = $request->get('level_id'),function (Builder $query) use ($value){
            $query->whereHas('term',function (Builder $query) use ($value){
                $query->where('level_id',$value);
            });
        })->when($value = $request->get('term_id'),function (Builder $query) use ($value){
            $query->where('term_id',$value);
        })->when($value = $request->get('subject_id'),function (Builder $query) use ($value){
            $query->where('subject_id',$value);
        })->when($value = $request->get('type'),function (Builder $query) use ($value){
            $query->where('type',$value);
        })->when($value = $request->get('row_id',[]),function (Builder $query) use ($value){
            $query->whereIn('id', $value);
        });
    }


    public function getImageAttribute($value){
        if ($value){
            return asset($value);
        }
        return $value;
    }

    public function getAudioAttribute($value){
        if ($value){
        return asset($value);
        }
        return $value;
    }

    public function getQuestionReaderAttribute($value){
        if ($value){
            return asset($value);
        }
        return $value;
    }
}
