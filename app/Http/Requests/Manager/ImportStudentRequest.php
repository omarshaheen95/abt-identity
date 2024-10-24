<?php

namespace App\Http\Requests\Manager;

use Illuminate\Foundation\Http\FormRequest;

class ImportStudentRequest extends FormRequest
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
        return [
           'school_id'=>'required|exists:schools,id',
           'year_id'=>'required|exists:years,id',
           'students_file'=>'required|file|mimes:xlsx',
           'update'=>'nullable'
        ];
    }
    public function messages()
    {
        return [
            'school_id' => t('You must select school'),
            'year_id' => t('You must select year'),
            'students.required' => t('Students file is required'),
            'students.mimes' => t('The student file format must be one of these formats') . ' : xlsx',
        ];
    }
}
