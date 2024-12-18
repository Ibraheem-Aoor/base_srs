<?php

namespace App\Models;

use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamType extends Model
{

    use HasStatus;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'marks',
        'contribution',
        'description',
        'status',
        'exam_type_category_id'
    ];

    public function exams()
    {
        return $this->hasMany(Exam::class, 'exam_type_id', 'id');
    }


    /**
     * Each Exam Type Belongs To A Category.
     * for example final exam '30'  marks belongs to a category of 'C' mark distribtion category.
     */
    public function category() : BelongsTo
    {
        return $this->belongsTo(ExamTypeCategory::class  , 'exam_type_category_id');
    }

    public function sibilings()
    {
        return $this->category->examsTypes;
    }

    public function scopeCategoryStatus($query , $status)
    {
        return $query->whereHas('category' , function($category)use($status){
            $category->status($status);
        });
    }
}
