<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Http\Requests\Manager;

use App\Support\PasswordPolicy;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ManagerPasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'old_password' => 'required',
            'password' => PasswordPolicy::rules(),
        ];
    }

    public function messages()
    {
        return PasswordPolicy::messages();
    }

    /**
     * Server side only checks. JsValidator ignores these, so the browser still
     * gets the length/format rules above.
     */
    public function withValidator(Validator $validator)
    {
        $user = Auth::guard('manager')->user();

        PasswordPolicy::applyExtraChecks($validator, 'password', $user ? $user->password : null);
        PasswordPolicy::applyHistoryCheck($validator, 'password', $user);
    }
}
