<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Http\Requests\General\Report;

use Illuminate\Foundation\Http\FormRequest;

class YearToYearProgressReportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'school_id' => 'required',
            'years' => 'required|array',
            'grades' => 'required|array',
            'student_type' => 'required',
            'round' => 'required',
            'generated_report_type' => 'required|in:attainment,combined',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
