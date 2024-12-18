<?php

namespace App\Models;

use App\Traits\HasStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Session extends Model
{
    use HasStatus;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'start_date',
        'end_date',
        'current',
        'status',
        'semester_id',
        'id_on_moodle'
    ];

    public function programs()
    {
        return $this->belongsToMany(Program::class, 'program_session', 'session_id', 'program_id');
    }

    public function studentEnrolls()
    {
        return $this->hasMany(StudentEnroll::class, 'session_id');
    }

    public function classes()
    {
        return $this->hasMany(ClassRoutine::class, 'session_id', 'id');
    }

    public function contents()
    {
        return $this->hasMany(Content::class, 'session_id', 'id');
    }


    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    /**
     * Get Session Title Short Name for moodle logic
     */
    public function getShortTitleForMoodle()
    {
        return str_replace([' ', '-', '_'], '', Carbon::parse(substr($this->title, 0, 4))->format('y') . '' . substr($this->title, 4, 4) . '' . substr($this->title, -1));
    }
}
