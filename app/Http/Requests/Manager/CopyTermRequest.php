<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Http\Requests\Manager;

use Illuminate\Foundation\Http\FormRequest;

class CopyTermRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'from_year' => 'required',
            'to_year' => 'required',
            'from_round' => 'required',
            'to_round' => 'required',
            'grades' => 'required|array',
//            'arab' => 'required|in:0,1,2',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
