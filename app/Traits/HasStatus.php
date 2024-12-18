<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Exception;

trait HasStatus
{
    /**
     * This Trait To Reuse With Models That Has Status Column
     */

    /**
     * Scope Status
     */
    public function scopeStatus($query, bool $status)
    {
        return $query->whereStatus($status);
    }
}
