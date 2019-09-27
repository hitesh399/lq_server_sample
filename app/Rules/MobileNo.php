<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class MobileNo implements Rule
{
    public static $rule = '/^\+([0-9]){1,4}(\-)([0-9]){6,12}$/';

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
        if (trim($value)) {
            return (preg_match(self::$rule, $value) === 1);
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Please enter a valid mobile number';
    }
}
