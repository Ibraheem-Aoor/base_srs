<?php

namespace App\Rules\Admin;

use App\Models\Semester;
use Illuminate\Contracts\Validation\Rule;

class SessionDateWithinSemesterPeriodRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(protected $start_date , protected $end_date)
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
    public function passes($attribute,  $value)
    {
        $semester = Semester::query()->select(['id' , 'start_date' , 'end_date'])->findOrFail($value);
        if($this->start_date >= $semester->start_date && $this->end_date <= $semester->end_date){
            return true;
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('field_session_period_invalid_for_semester');
    }
}
