<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class MoodlePassword implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Check the length
        if (strlen($value) < 8) {
            return false;
        }

        // Check for at least one uppercase letter
        if (!preg_match('/[A-Z]/', $value)) {
            return false;
        }

        // Check for at least one lowercase letter
        if (!preg_match('/[a-z]/', $value)) {
            return false;
        }

        // Check for at least one digit
        if (!preg_match('/[0-9]/', $value)) {
            return false;
        }

        // Check for at least one special character
        if (!preg_match('/[\W_]/', $value)) {
            return false;
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
        return 'The :attribute must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one digit, and one special character.';
    }
}
