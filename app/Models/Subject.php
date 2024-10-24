<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;


class Subject extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'mark'];

    public function questions()
    {
        return $this->hasMany(Question::class, 'subject_id');
    }

    public function scopeSearch(Builder $query, Request $request)
    {
        return $query
            ->when($name = $request->get('name', false), function (Builder $query) use ($name) {
//                $query->where(function (Builder $query) use ($name) {
//                    $query->where(DB::raw('LOWER(name->"$.ar")'), 'like', '%' . $name . '%')
//                        ->orWhere(DB::raw('LOWER(name->"$.en")'), 'like', '%' . $name . '%');
//                });
            })->when($value = $request->get('row_id',[]),function (Builder $query) use ($value){
                $query->whereIn('id', $value);
            });
    }

}
