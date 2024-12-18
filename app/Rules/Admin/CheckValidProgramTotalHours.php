<?php

namespace App\Rules\Admin;

use App\Models\Program;
use App\Models\Subject;
use Illuminate\Contracts\Validation\Rule;

class CheckValidProgramTotalHours implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(protected Program $program)
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
        $total_credit_hours = Subject::query()->whereIn('id', array_keys($value))->sum('credit_hour');
        return $total_credit_hours <= $this->program->default_total_hours;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('credt_hours_total_amount_invalid', ['program_total_hours' => $this->program->default_total_hours]);
    }
}
