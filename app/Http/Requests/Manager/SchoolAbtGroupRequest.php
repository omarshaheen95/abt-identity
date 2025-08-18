<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Http\Requests\Manager;

use Illuminate\Foundation\Http\FormRequest;

class SchoolAbtGroupRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'school_id' => 'required|exists:schools,id',
            'primary_year' => 'required',
            'secondary_years' => 'required|array',
            'link_by_number' => 'required|in:1,2,3',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
