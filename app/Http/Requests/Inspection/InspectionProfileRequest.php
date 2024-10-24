<?php

namespace App\Http\Requests\Inspection;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class InspectionProfileRequest extends FormRequest
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
        $user = Auth::guard('inspection')->user()->id;
        $rules['email'] = "required|email|unique:inspections,email,$user,id,deleted_at,NULL";
        return $rules;
    }
}
