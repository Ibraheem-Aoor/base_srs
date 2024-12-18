<?php

namespace App\Models;

use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamTypeCategory extends Model
{
    use HasFactory, HasStatus;
    protected $fillable = [
        'title',
        'class_type',
        'status',
    ];


    public function examsTypes(): HasMany
    {
        return $this->hasMany(ExamType::class, 'exam_type_category_id');
    }

    public function totalMarks()
    {
        return $this->examsTypes()->select(['id' , 'marks'])->sum('marks');
    }
}
