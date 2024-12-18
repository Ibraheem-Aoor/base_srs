<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'program_id',
        'exam_type_category_id',
        'subject_type_id',
    ];

    public $table = 'program_subject';


    public function examTypeCategory(): BelongsTo
    {
        return $this->belongsTo(ExamTypeCategory::class, 'exam_type_category_id');
    }

    /**
     * Each Program Belongs To One Subject With Specific Type
     */
    public function subjectType(): BelongsTo
    {
        return $this->belongsTo(SubjectType::class , 'subject_type_id');
    }
}
