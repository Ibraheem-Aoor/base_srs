<?php

namespace App\Rules\Admin;

use App\Models\Student;
use Illuminate\Contracts\Validation\Rule;

class CheckValidInstallmentsAmount implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(protected Student $student)
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
        $total_installments_amount = array_reduce($value , function($carry , $item){
            return $carry + @$item['amount'] ?? 0;
        }  , 0);
        return $total_installments_amount <= $this->student->program->default_total_fees;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('installments_total_amount_invalid' , ['program_total_fees' => $this->student->program->default_total_fees]);
    }
}
