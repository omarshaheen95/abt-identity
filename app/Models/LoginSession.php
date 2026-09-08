<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\Request;

class LoginSession extends Model
{
    protected $fillable = ['model_id', 'model_type', 'data', 'status', 'guard', 'identifier', 'reason', 'ip', 'user_agent'];

    public function getAccountNameAttribute(): ?string
    {
        return optional($this->model)->name;
    }

    public function getAccountIdentifierAttribute(): ?string
    {
        return optional($this->model)->email ?: $this->identifier;
    }

    public static function morphTypes(): array
    {
        return [Manager::class, School::class, Student::class, Inspection::class];
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeFilter(Builder $query, Request $request): Builder
    {
        return $query->when($value = $request->get('model_id'), function (Builder $query)use ($value) {
            $query->where('model_id',$value) ;

        })->when($value = $request->get('name'), function (Builder $query)use ($value) {
            $query->whereHasMorph('model',self::morphTypes(),function ($query) use ($value){
                $query->where('name','LIKE','%'.$value.'%');
            }) ;
        })->when($value = $request->get('email'), function (Builder $query)use ($value) {
            $query->where(function (Builder $query) use ($value) {
                $query->whereHasMorph('model', self::morphTypes(), function ($query) use ($value) {
                    $query->where('email', $value);
                })->orWhere('identifier', $value);
            });
        })->when($value = $request->get('model_type'), function (Builder $query)use ($value) {
                if ($value === 'Unknown') {
                    $query->whereNull('model_type');
                } else {
                    $types = array_combine(array_map('class_basename', self::morphTypes()), self::morphTypes());
                    $query->where('model_type', $types[$value] ?? $value);
                }

        })->when($value = $request->get('status'), function (Builder $query) use ($value) {
            $query->where('status', $value);
        })->when($value = $request->get('login_guard'), function (Builder $query) use ($value) {
            $query->where('guard', $value);
        })->when($value = $request->get('ip'), function (Builder $query) use ($value) {
            $query->where('ip', 'LIKE', '%'.$value.'%');
        })->when($value = $request->get('identifier'), function (Builder $query) use ($value) {
            $query->where('identifier', 'LIKE', '%'.$value.'%');
        })->when($value = $request->get('row_id', []), function (Builder $query) use ($value) {
            $query->whereIn('id', (array) $value);
        })->when($value = $request->get('id'), function (Builder $query) use ($value) {
            $query->where('id', $value);
        })->when($value= $request->get('start_date',false),function (Builder $query) use ($value){
            $query->whereDate('created_at', '>=',$value);
        })->when($value= $request->get('end_date',false),function (Builder $query) use ($value){
            $query->whereDate('created_at', '<=',$value);
        });
    }

}
