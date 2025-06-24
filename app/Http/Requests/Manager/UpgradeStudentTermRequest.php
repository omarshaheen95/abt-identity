<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Http\Requests\Manager;

use Illuminate\Foundation\Http\FormRequest;

class UpgradeStudentTermRequest extends FormRequest
{
    public function rules(): array
    {

        $rule= [
            'school_id' => 'required|exists:schools,id',
            'year_id' => 'required',
            'month' => 'required|in:september,february,may',
            'arab' => 'required|in:0,1,2',
            'grades' => 'required|array',
            'update_date' => 'nullable|date',
//            'update_operator' => 'required_if:update_date,!=,null',
            'from_total_result' => 'required|integer',
            'to_total_result' => 'required|integer',

            'process_type' => 'required|in:upgrade,downgrade',
            'mark' => 'required|integer',
            'range_mark' => 'nullable|integer',
        ];
        if($this->update_date != null) {
            $rule['update_operator'] = 'required';
        }
        return $rule;
    }

    public function authorize(): bool
    {
        return true;
    }
}
