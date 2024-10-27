<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Http\Requests\Manager;

use App\Helpers\Constant;
use Illuminate\Foundation\Http\FormRequest;

class ImportQuestionFileRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'level_id' => 'required|exists:levels,id',
            'term_id' => 'required|exists:terms,id',
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ];
        return $rules;
    }

    public function authorize(): bool
    {
        return true;
    }
}
