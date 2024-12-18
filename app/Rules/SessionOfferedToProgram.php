<?php

namespace App\Rules;

use App\Models\Session;
use Illuminate\Contracts\Validation\Rule;

class SessionOfferedToProgram implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(protected $program_id)
    {

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
        $session_programs_ids = $session->programs()->pluck('id')->toArray();
        return in_array($this->program_id , array_values($session_programs_ids));
    }


    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('session_must_belong_to_selected_program');
    }
}
