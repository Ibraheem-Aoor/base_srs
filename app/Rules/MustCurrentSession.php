<?php

namespace App\Rules;

use App\Models\Session;
use Illuminate\Contracts\Validation\Rule;

class MustCurrentSession implements Rule
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
        $session = Session::query()->findOrFail($value);
        return $session->current;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Selected Session Must Be running (Current)';
    }
}
