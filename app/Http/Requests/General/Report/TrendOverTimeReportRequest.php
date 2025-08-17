<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Http\Requests\General\Report;

use Illuminate\Foundation\Http\FormRequest;

class TrendOverTimeReportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'school_id' => 'required|exists:schools,id',
            'years' => 'required|array|min:3',
            'grades' => 'required|array|min:1',
            'student_section' => 'required',
            'round_num' => 'required',
            'ranges_type' => 'required'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
