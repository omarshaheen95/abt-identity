<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Http\Requests\Manager;

use App\Rules\StudentNameRule;
use Illuminate\Foundation\Http\FormRequest;

class StudentImportLogDataRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'student_data_file_id' => 'required|exists:student_import_files,id',
            'student' => 'array|required',
            'student.*.row_num' => 'required',
            'student.*.name' => ['required','regex:/^[a-zA-Z0-9_.\s-]*$/u','max:255'],
            'student.*.student_id' => 'required|string|max:255',
            'student.*.nationality' => 'nullable',
            'student.*.date_of_birth' => 'required',
            'student.*.gender' => 'required|in:1,2',
            'student.*.sen' => 'required|in:1,0',
            'student.*.g&t' => 'required|in:1,0',
            'student.*.citizen' => 'required|in:1,0',
            'student.*.arab' => 'required|in:1,0',
            'student.*.grade_name' => 'nullable',
            'student.*.grade' => 'required',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
