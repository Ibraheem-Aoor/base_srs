<?php

namespace App\Models;

use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Model;

class EnrollSubject extends Model
{
    use HasStatus;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'program_id',
        'session_id',
        'section_id',
        'status',
    ];

    public $table = "enroll_subjects";

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function session()
    {
        return $this->belongsTo(Session::class, 'session_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'enroll_subject_subject', 'enroll_subject_id', 'subject_id');
    }
}
