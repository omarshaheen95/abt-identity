<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Http\Requests\General\Report;

use App\Helpers\Constant;
use Illuminate\Foundation\Http\FormRequest;

class AttainmentRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'year_id' => 'required|exists:years,id',
            'sections' => 'nullable|array',
            'student_type' => 'required',
            'grades' => 'required|array',
            'school_id' => 'required|exists:schools,id',
        ];
        return $rules;
    }

    public function authorize(): bool
    {
        return true;
    }
}
