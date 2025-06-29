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
{
    use SoftDeletes;

    protected $fillable = [
        'school_id', 'year_id', 'original_file_name',
        'updated_row_count', 'deleted_row_count', 'failed_row_count',
        'file_name', 'row_count', 'path', 'status', 'delete_with_user', 'error', 'failures', 'process_type', 'data', 'with_abt_id'
    ];

    protected $casts = [
        'error' => 'array',
        'failures' => 'array',
        'data' => 'array',
    ];

    //search scope
    public function scopeSearch(Builder $query, Request $request)
    {
        return $query
            ->when($request->filled('name'), function (Builder $query) use ($request) {
                $name = strtolower($request->get('name'));
                $query->whereRaw('LOWER(original_file_name) LIKE ?', ["%{$name}%"]);
            })
            ->when($request->filled('school_id'), function (Builder $query) use ($request) {
                $query->where('school_id', $request->get('school_id'));
            })
            ->when($request->filled('status'), function (Builder $query) use ($request) {
                $query->where('status', $request->get('status'));
            })
            ->when($request->filled('process_type'), function (Builder $query) use ($request) {
                $query->where('process_type', $request->get('process_type'));
            })
            ->when($request->get('has_logs') == 1, function (Builder $query) {
                $query->has('logs');
            })
            ->when($request->get('has_logs') == 2, function (Builder $query) {
                $query->doesntHave('logs');
            })
            ->when($request->get('has_errors') == 1, function (Builder $query) {
                $query->has('logErrors');
            })
            ->when($request->get('has_errors') == 2, function (Builder $query) {
                $query->doesntHave('logErrors');
            })
            //start_date and end_date;
            ->when($request->filled('start_date') && $request->filled('end_date'), function (Builder $query) use ($request) {
                $startDate = $request->get('start_date');
                $endDate = $request->get('end_date');
                $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
            });
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function year(): BelongsTo
    {
        return $this->belongsTo(Year::class, 'year_id');
    }

    public function logs()
    {
        return $this->hasMany(StudentImportFileLog::class);
    }

    public function logErrors()
    {
        return $this->hasMany(ImportStudentFileError::class);
    }

    public function getActionButtonsAttribute()
    {
        if ($this->status == 'Failures' || $this->status == 'Errors' || $this->logs()->count() > 0) {
            $actions[] = ['key' => 'show', 'name' => t('Show Errors'), 'route' => route('manager.students_files_import.show_logs', [$this->id])];
        }
        $actions[] = ['key' => 'blank', 'name' => t('Students Cards'), 'route' => route('manager.students_files_import.export_cards', $this->id)];

        $actions[] = ['key' => 'excel', 'name' => t('Excel'), 'route' => route('manager.students_files_import.export_excel', $this->id)];

        $actions[] = ['key' => 'delete', 'name' => t('Delete'), 'route' => $this->id, 'permission' => 'delete students import'];


        return view('general.action_menu')->with('actions', $actions);
    }

}
