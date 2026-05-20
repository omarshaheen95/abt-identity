<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Http\Requests\Inspection;

use Illuminate\Foundation\Http\FormRequest;

class GroupComparingReportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'school_id' => 'required|array',
            'year_id' => 'required',
            'grades' => 'required|array',
            'student_type' => 'required',
            'round' => 'required',
            'sub_title' => 'nullable',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
