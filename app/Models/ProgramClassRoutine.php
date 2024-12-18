<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramClassRoutine extends Model
{
    use HasFactory;
    protected $fillable = [
        'class_routine_id',
        'program_id',
    ];
}
