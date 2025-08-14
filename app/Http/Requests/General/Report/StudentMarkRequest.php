<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Http\Requests\General\Report;

use Illuminate\Foundation\Http\FormRequest;

class StudentMarkRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'school_id' => 'required|exists:schools,id',
            'year_id' => 'required',
            'student_type' => 'required',
//            'ranges_type' => 'required',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [

        ];
    }
}
