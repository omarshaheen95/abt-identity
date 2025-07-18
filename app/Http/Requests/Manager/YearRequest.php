<?php

namespace App\Http\Requests\Manager;

use Illuminate\Foundation\Http\FormRequest;

class YearRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'slug' => 'required|regex:/^\d{4}\/\d{4}$/'
        ];
        foreach(\Config::get('app.languages') as $locale)
        {
            $rules["name.$locale"] = 'required';
        }
        return $rules;
    }
}
