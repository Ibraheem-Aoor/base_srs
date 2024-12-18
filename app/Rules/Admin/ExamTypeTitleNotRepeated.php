<?php

namespace App\Rules\Admin;

use App\Models\ExamType;
use App\Models\ExamTypeCategory;
use Illuminate\Contracts\Validation\Rule;

class ExamTypeTitleNotRepeated implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(protected $title)
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
        if($updated_exam_type = request()->route('exam_type'))
        {
            return !ExamType::query()->where('title' , $this->title)->where('exam_type_category_id' , $value)->where('id' , '!=' , $updated_exam_type->id)->exists();
        }
        return !ExamType::query()->where('title' , $this->title)->where('exam_type_category_id' , $value)->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('field_exam_type_title_exsits_for_selected_category');
    }
}
