<?php

namespace App\Http\Requests\Manager;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class InspectionRequest extends FormRequest
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
            'name' => 'required',
            'schools_ids' => 'required|array|exists:schools,id',
        ];
        if (Route::currentRouteName() == 'manager.inspection.edit' || Route::currentRouteName() == 'manager.inspection.update') {
            $inspection = $this->route('inspection');
            $rules['email'] = "required|email|unique:inspections,email,$inspection,id,deleted_at,NULL";
            $rules["password"] = 'nullable|min:6';
        }else{
            $rules['email'] = 'required|email|unique:inspections,email,{$id},id,deleted_at,NULL';
            $rules["password"] = 'required|min:6';
        }
        return $rules;
    }
}
