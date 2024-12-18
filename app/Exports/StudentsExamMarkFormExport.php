<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentsExamMarkFormExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function __construct(protected $students , protected $exam_types)
    {

    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->students;
    }


    public function headings(): array
    {
        $headings = ['student_id', 'first_name', 'last_name' , 'attendance'];
        foreach($this->exam_types as $exam_type)
        {
            $headings[] = $exam_type->title.'('.$exam_type->marks.')';
        }
        // $headings[] = 'note';
        return $headings;
    }
}
