<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoodleClassRoutine extends Model
{
    use HasFactory;
    protected $fillable = [
        'class_routine_id',
        'session_id',
        'teacher_id',
        'subject_id',
    ];


    public function classRoutine()
    {
        return $this->belongsTo(ClassRoutine::class, 'class_routine_id');
    }

    public function session()
    {
        return $this->belongsTo(Session::class, 'session_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}
