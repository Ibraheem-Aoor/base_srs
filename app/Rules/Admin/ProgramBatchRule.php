<?php

namespace App\Rules\Admin;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ProgramBatchRule implements Rule
{

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(protected $batch_id)
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
        return DB::table('batch_program')->where('batch_id', $this->batch_id)->where('program_id', $value)->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('program_not_available_for_batch');
    }
}
