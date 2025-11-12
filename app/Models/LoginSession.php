<?php

namespace App\Models;

use App\Models\Inspection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class LoginSession extends Model
{
    protected $fillable = ['model_id','model_type','data'];

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeFilter(Builder $query, Request $request): Builder
    {
        return $query->when($value = $request->get('model_id'), function (Builder $query)use ($value) {
             $query->where('model_id',$value) ;

        })->when($value = $request->get('id',false),function (Builder $query) use ($value){
            $query->where('id', $value);
        })->when($value = $request->get('name'), function (Builder $query)use ($value) {
             $query->whereHasMorph('model',[Manager::class,School::class,Student::class,Inspection::class],function ($query) use ($value){
                 $query->where('name','LIKE','%'.$value.'%');
             }) ;
        })->when($value = $request->get('email'), function (Builder $query)use ($value) {
             $query->whereHasMorph('model',[Manager::class,School::class,Student::class,Inspection::class],function ($query) use ($value){
                 $query->where('email',$value);
             }) ;
        })->when($value = $request->get('model_type'), function (Builder $query)use ($value) {
            if ($value == 'Manager'){
                $query->where('model_type','=',Manager::class) ;
            }elseif ($value=='Student'){
                $query->where('model_type','=',Student::class) ;
            }elseif ($value=='School'){
                $query->where('model_type','=',School::class) ;
            }elseif ($value=='Inspection'){
                $query->where('model_type','=',Inspection::class) ;
            }
        })->when($value= $request->get('start_date',false),function (Builder $query) use ($value){
            $query->whereDate('created_at', '>=',$value);
        })->when($value= $request->get('end_date',false),function (Builder $query) use ($value){
            $query->whereDate('created_at', '<=',$value);
        });
    }
}
