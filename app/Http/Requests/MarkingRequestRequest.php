<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarkingRequestRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'year_id' => 'required|exists:years,id',
            'grades' => ['required'],
            'email' => ['required', 'email', 'max:254'],
            'notes' => ['nullable'],
            'round' => ['required'],
            'section' => 'required|in:0,1,2',

        ];


        if (\Request::is('manager/*')&&\Auth::guard('manager')->user()){
            $rules['school_id'] = ['required', 'integer'];
            $rules['status'] = ['required', 'in:Pending,Accepted,In Progress,Completed,Rejected'];
        }else{
            $rules['confirm']='required';
        }
        return $rules;
    }

    public function authorize(): bool
    {
        return true;
    }
}
