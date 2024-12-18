<?php

namespace App\Imports;

use Auth;
use App\Models\Exam;
use App\Models\ExamType;
use App\Models\StudentEnroll;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MarksImport implements ToCollection, WithHeadingRow
{
    protected $data;

    /**
     * @return void
     */
    public function __construct($data, protected $exam_types)
    {
        $this->data = $data;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {
        $validation_rules = [
            '*.student_id' => 'required',
            '*.attendance' => 'required',
        ];
        $validation_messages = [];
        foreach ($this->exam_types as $exam_type) {
            $column_name = $this->getFormatedColumnName(($exam_type->title) . ($exam_type->marks * 100));
            $validation_rules['*.' . $column_name] = 'nullable|numeric|lte:' . $exam_type->marks;
            $validation_messages['*.' . $column_name.'.numeric']  = __('Entered Mark Must Be Numeric  For Exam: '.$exam_type->title);
            $validation_messages['*.' . $column_name.'.lte']  = __('Entered Mark Must Less Than Or Equal Exam Total Mark For Exam: '.$exam_type->title);
        }
        Validator::make($rows->toArray(), $validation_rules , $validation_messages)->stopOnFirstFailure()->validate();


        foreach ($rows as $row) {
            if ($row['attendance'] == 'P') {
                $attendance = 1;
            } elseif ($row['attendance'] == 'A') {
                $attendance = 2;
            } else {
                $attendance = 2;
            }

            $student_id = $row['student_id'];
            $subject = $this->data['subject'];


            // Enrolls
            $enroll = StudentEnroll::where('program_id', $this->data['program'])->where('session_id', $this->data['session']);
            $enroll->with('student')->whereHas('student', function ($query) use ($student_id) {
                $query->where('student_id', $student_id);
            });
            $enroll->with('subjects')->whereHas('subjects', function ($query) use ($subject) {
                $query->where('subject_id', $subject);
            });
            $student = $enroll->first();



            if (isset($student) && isset($this->exam_types)) {
                // Attendance Update
                foreach ($this->exam_types as $exam_type) {
                    $column_name = $this->getFormatedColumnName(($exam_type->title) . ($exam_type->marks * 100));
                    Exam::updateOrCreate(
                        [
                            'student_enroll_id' => $student->id,
                            'subject_id' => $this->data['subject'],
                            'exam_type_id' => $exam_type->id,
                        ],
                        [
                            'student_enroll_id' => $student->id,
                            'subject_id' => $this->data['subject'],
                            'exam_type_id' => $exam_type->id,
                            'date' => $this->data['date'],
                            'marks' => $exam_type->marks,
                            'contribution' => $exam_type->contribution,
                            'attendance' => $attendance,
                            'achieve_marks' => $row[$column_name], // we made this because of the data format after validation.
                            // 'note' => $row['note'],
                            'created_by' => Auth::guard('web')->user()->id,
                        ]
                    );
                }
            }
        }
    }


    /**
     * Formatting to get the correct column name according to this package rules
     */

    private function getFormatedColumnName($column)
    {
        return str_replace(' ', '_', strtolower($column));
    }
}
