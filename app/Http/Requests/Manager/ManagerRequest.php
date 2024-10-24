<?php

namespace App\Http\Requests\Manager;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class ManagerRequest extends FormRequest
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
        ];
        if (Route::currentRouteName() == 'manager.manager.edit' || Route::currentRouteName() == 'manager.manager.update') {
            $manager = $this->route('manager');
            $rules['email'] = "required|email|unique:managers,email,$manager,id,deleted_at,NULL";
            $rules["password"] = 'nullable|min:6';
        }else{
            $rules['email'] = 'required|email|unique:managers,email,{$id},id,deleted_at,NULL';
            $rules["password"] = 'required|min:6';
        }
        return $rules;
    }
}
