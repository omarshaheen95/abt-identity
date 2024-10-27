<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentImportFile extends Model
{ use SoftDeletes;
    protected $fillable = ['school_id','year_id','original_file_name',
        'file_name','row_count','failed_row_count','path','status','delete_with_user','error','failures'];

    //search scope
    public function scopeSearch(Builder $query, Request $request)
    {
        return $query
            ->when($name = $request->get('name', false), function (Builder $query) use ($name) {
                $query->where(function (Builder $query) use ($name) {
                    $query->where(DB::raw('LOWER(original_file_name)'), 'like', '%' . $name . '%');
                });
            })->when($school_id = $request->get('school_id', false), function (Builder $query) use ($school_id) {
                $query->where('school_id', $school_id);
            })->when($year_id = $request->get('year_id', false), function (Builder $query) use ($year_id) {
                $query->where('year_id', $year_id);
            })->when($status = $request->get('status', false), function (Builder $query) use ($status) {
                $query->where('status', $status);
            });
    }
    public function school():BelongsTo{
        return $this->belongsTo(School::class,'school_id');
    }
    public function year():BelongsTo{
        return $this->belongsTo(Year::class,'year_id');
    }
public function logs()
    {
        return $this->hasMany(StudentImportFileLog::class);
    }

    public function getActionButtonsAttribute()
    {
        if ($this->status == 'Failures' || $this->status == 'Errors'|| $this->logs()->count() > 0) {
            $actions[]= ['key' => 'show', 'name' => t('Show Errors'), 'route' => route('manager.students_files_import.show_logs', [$this->id])];
        }
        $actions[]= ['key' => 'blank', 'name' => t('Students Cards'), 'route' => route('manager.students_files_import.export_cards',$this->id)];

        $actions[]= ['key' => 'excel', 'name' => t('Excel'), 'route' =>route('manager.students_files_import.export_excel',$this->id) ];

        $actions[]= ['key' => 'delete', 'name' => t('Delete'), 'route' => $this->id, 'permission' => 'delete students import'];


        return view('general.action_menu')->with('actions', $actions);
    }

}
