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
            'certificate_mark'=> 'required|numeric|min:40|max:100',
        ];
        if (Route::currentRouteName() == 'manager.school.edit' || Route::currentRouteName() == 'manager.school.update') {
            $school = $this->route('school');
            $rules['email'] = "required|email|unique:schools,email,$school,id,deleted_at,NULL";
            $rules["password"] = 'nullable|min:6';
            $rules['central_uid'] = "nullable|email|unique:schools,central_uid,$school,id,deleted_at,NULL";
        }else{
            $rules['email'] = 'required|email|unique:schools,email,{$id},id,deleted_at,NULL';
            $rules["password"] = 'required|min:6';
            $rules['central_uid'] = 'nullable|email|unique:schools,central_uid,{$id},id,deleted_at,NULL';
        }
        if (\Auth::guard('manager')->user()->hasDirectPermission('edit reports status')) {
            $rules['allow_reports'] = 'nullable|boolean';
        }
        foreach(\Config::get('app.languages') as $locale)
        {
            $rules["name.$locale"] = 'required';
        }
        return $rules;
    }
}
