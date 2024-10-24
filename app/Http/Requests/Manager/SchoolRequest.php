<?php

namespace App\Http\Requests\Manager;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class SchoolRequest extends FormRequest
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
            'logo' => 'nullable',
            'url' => 'nullable|url',
            'mobile' => 'nullable',
            'country' => 'required',
            'curriculum_type' => 'required',
            'available_year_id' => 'required',
        ];
        if (Route::currentRouteName() == 'manager.school.edit' || Route::currentRouteName() == 'manager.school.update') {
            $school = $this->route('school');
            $rules['email'] = "required|email|unique:schools,email,$school,id,deleted_at,NULL";
            $rules["password"] = 'nullable|min:6';
        }else{
            $rules['email'] = 'required|email|unique:schools,email,{$id},id,deleted_at,NULL';
            $rules["password"] = 'required|min:6';
        }
        foreach(\Config::get('app.languages') as $locale)
        {
            $rules["name.$locale"] = 'required';
        }
        return $rules;
    }
}
