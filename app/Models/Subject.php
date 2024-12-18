<?php

namespace App\Models;

use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasStatus;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'code',
        'credit_hour',
        'subject_type',
        'class_type',
        'total_marks',
        'passing_marks',
        'description',
        'status',
        'id_on_moodle',
    ];

    public function programs()
    {
        return $this->belongsToMany(Program::class, 'program_subject', 'subject_id', 'program_id')->withPivot(['subject_type_id', 'exam_type_category_id']);
    }

    public function subjectEnrolls()
    {
        return $this->belongsToMany(EnrollSubject::class, 'enroll_subject_subject', 'subject_id', 'enroll_subject_id');
    }

    public function studentEnrolls()
    {
        return $this->belongsToMany(StudentEnroll::class, 'student_enroll_subject', 'student_enroll_id', 'subject_id');
    }

    public function classes()
    {
        return $this->hasMany(ClassRoutine::class, 'subject_id', 'id');
    }

    public function attendances()
    {
        return $this->hasMany(StudentAttendance::class, 'subject_id', 'id');
    }

    public function examRoutines()
    {
        return $this->hasMany(ExamRoutine::class, 'subject_id', 'id');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class, 'subject_id', 'id');
    }

    public function subjectMarks()
    {
        return $this->hasMany(SubjectMarking::class, 'subject_id', 'id');
    }


    /**
     * Each Subject Might Have Prerequisites
     */
    public function prerequisites(): HasMany
    {
        return $this->hasMany(Prerequisit::class, 'subject_id');
    }



}
