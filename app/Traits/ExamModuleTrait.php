<?php

namespace App\Traits;

use App\Http\Controllers\FilterController;
use Illuminate\Http\Request;
use Image;
use File;

trait ExamModuleTrait
{

    /**
     * return the needed exam types for marks export and import
     */
    public function getExamTypes(Request $request, FilterController $filter)
    {
        $request['subject_id'] = $request->subject;
        $request['program_id'] = $request->program;
        return $filter->filterMarkDistribitionCategoryTypesBySubjectAndProgram($request)?->sortBy('created_at');
    }
}
