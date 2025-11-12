<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;


class Subject extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'mark', 'marks_range'];

    protected $casts = [
        'marks_range' => 'json',
    ];

    public function questions()
    {
        return $this->hasMany(Question::class, 'subject_id');
    }
    public function getCategoryForMark($mark)
    {
        foreach ($this->marks_range as $category => $range) {
            if ($mark >= $range['from'] && $mark <= $range['to']) {
                return $category;
            }
        }

        return null;
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
            })->when($value = $request->get('id',false),function (Builder $query) use ($value){
                $query->where('id', $value);
            });
    }

}
