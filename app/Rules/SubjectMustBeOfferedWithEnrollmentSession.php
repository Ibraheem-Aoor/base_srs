<?php

namespace App\Rules;

use App\Models\EnrollSubject;
use App\Models\Student;
use App\Models\StudentEnroll;
use App\Models\Subject;
use Illuminate\Contracts\Validation\Rule;

class SubjectMustBeOfferedWithEnrollmentSession implements Rule
{

    protected $student , $session_title , $course;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($student_id)
    {
        $this->student = Student::findOrFail($student_id);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    /**
     * Used For Course AddDrop.
     * We Must Make sure that the added or dropped course is offered to the target enroll's session.
     */
    public function passes($attribute, $value)
    {
        $subjects = $value;
        $enroll = $this->student->currentEnroll;
        $this->session_title = $enroll->session->title;
        $needed_session_enrolls = EnrollSubject::query()->where('session_id', $enroll->session_id)->get();
        $offered_subjects = [];
        foreach ($needed_session_enrolls as $needed_enroll) {
            $offered_subjects = array_merge($offered_subjects, $needed_enroll->subjects()->pluck('id')->toArray());
        }
        foreach ($subjects as $subject) {
            if (!in_array($subject, array_values(array_unique($offered_subjects)))) {
                $this->course = Subject::query()->select(['id' , 'title'])->find($subject);
                return false;
            }
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
        return __('courses_must_be_offered_to_enrollment_session' , ['session_title' => $this->session_title , 'course' => $this->course->title]);
    }
}
