<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Prerequisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'prerequisit_id',
        'subject_id',
        'type',
    ];


    /**
     * Prerequisit
     */
    public function course(): hasOne
    {
        return $this->hasOne(Subject::class, 'prerequisit_id');
    }


    /**
     * The Subject That Owns This Prerequisit.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }



}
