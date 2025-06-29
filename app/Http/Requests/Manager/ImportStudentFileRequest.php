<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Http\Requests\Manager;

use Illuminate\Foundation\Http\FormRequest;

class ImportStudentFileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'school_id' => 'required|exists:schools,id',
            'file' => 'required|file|mimes:xlsx,xls,csv',
            'year_id' => 'required|exists:years,id',
            'process_type' => 'required|in:create,update,delete',
            'status' => 'required|in:create,update,delete',
            'with_abt_id' => 'required_if:process_type,create|boolean',
            'username_type' => 'required|in:student_name,student_id',
            'delete_type' => 'required_if:status,delete|in:delete_assessments,delete_all',
            'rounds_deleted_assessments' => 'required_if:delete_type,delete_assessments|array',
            'rounds_deleted_assessments.*' => 'required|in:september,february,may',
            'search_by_column' => 'required_if:status,update,delete|in:student_id,username',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
