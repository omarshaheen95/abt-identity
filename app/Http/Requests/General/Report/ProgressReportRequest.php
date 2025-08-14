<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Http\Requests\General\Report;

use Illuminate\Foundation\Http\FormRequest;

class ProgressReportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'school_id' => 'required',
            'year_id' => 'required',
            'grades' => 'required|array|min:1',
            'student_type' => 'required',
            'grades_names' => 'nullable|array',
            'generated_report_type' => 'required|in:attainment,combined',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
