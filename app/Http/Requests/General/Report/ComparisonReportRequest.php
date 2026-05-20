<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Http\Requests\General\Report;

use Illuminate\Foundation\Http\FormRequest;

class ComparisonReportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'school_id' => 'required',
            'year_id' => 'required',
            'round' => 'required',
            'gender' => 'required',
            'student_type' => 'required',
            'curriculums' => 'required|array',
//            'countries' => 'required|array',
            'grades' => 'required|array',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
