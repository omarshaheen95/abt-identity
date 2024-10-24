<?php

namespace App\Http\Requests\School;

use App\Rules\StudentEmailRule;
use App\Rules\StudentNameRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class StudentRequest extends FormRequest
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
            'name'=>['required', new StudentNameRule()],
            'grade_name' => 'nullable|string',
            'year_id' => 'required|exists:years,id',
            'level_id' => 'required|exists:levels,id',
            'nationality' => 'nullable',
            'dob' => 'nullable',
            'gender' => 'required|in:boy,girl',
            'sen' => 'required|in:1,0',
            'g_t' => 'required|in:1,0',
            'arab' => 'required|in:1,0',
            'citizen' => 'required|in:1,0',

        ];

        if (Route::currentRouteName() == 'school.student.edit' || Route::currentRouteName() == 'school.student.update') {
            $student = $this->route('student');
            $rules['email'] = ['required',"unique:students,email,$student,id,deleted_at,NULL",new StudentEmailRule()];
            ;
        }else{
            $rules['email'] = ['required','unique:students,email,{$id},id,deleted_at,NULL',new StudentEmailRule()];
            $rules['id_number']='required|unique:students,id_number';

        }
        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => t('The Name is required'),
            'grade.required' => t('The grade is required'),
            'year_id.required' => t('The year is required'),
            'level_id.required' => t('The level is required'),
            'gender.required' => t('The gender is required'),
            'sen.required' => t('The sen is required'),
            'arab.required' => t('The arab is required'),
        ];


    }
}
