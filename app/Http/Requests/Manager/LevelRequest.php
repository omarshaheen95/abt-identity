<?php

namespace App\Http\Requests\Manager;

use Illuminate\Foundation\Http\FormRequest;

class LevelRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'grade' => 'required',
            'slug' => 'nullable',
            'year_id' => 'required',
            'section' => 'required|in:1,2',
        ];
        foreach(\Config::get('app.languages') as $locale)
        {
            $rules["name.$locale"] = 'required';
        }
        return $rules;
    }
}
