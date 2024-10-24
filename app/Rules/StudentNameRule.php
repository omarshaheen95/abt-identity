<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class StudentNameRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $regex = '/^[a-zA-Z0-9_.\s-]*$/u';
        return preg_match($regex, $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return t('The name must be english');
    }
}
