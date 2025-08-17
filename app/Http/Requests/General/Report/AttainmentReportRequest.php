<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Http\Requests\General\Report;

use Illuminate\Foundation\Http\FormRequest;

class AttainmentReportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'school_id' => 'required',
            'year_id' => 'required',
            'grades' => 'required|array|min:1',
//            'ranges_type' => 'required',
            'student_type' => 'required', //0 non-arabs, 1 arabs, 2 all
            'grades_names' => 'nullable|array',
            'generated_report_type' => 'required|in:attainment,combined',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
