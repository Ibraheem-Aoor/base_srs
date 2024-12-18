<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoodleSubjectSession extends Model
{
    use HasFactory;
    /**
     * Mid Table To Track  "single-uneditable" Subject On SRS System And Subject Per Session On Moodle.
     * This Calss Is Used because of the new naming rules of subject and duplicated subjects on moodle.
     */

    protected $fillable = [
        'subject_id',
        'session_id',
        'id_on_moodle'
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function session()
    {
        return $this->belongsTo(Session::class, 'session_id');
    }
}
