<?php

namespace App\Exports;

use App\Models\Student;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentsExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $students = Student::get(['student_id', 'admission_date', 'first_name', 'last_name', 'father_name', 'mother_name', 'email', 'gender', 'dob', 'phone', 'password_text', 'moodle_username', 'moodle_password']);
            // Decrypt the password_text for each student
        $students->transform(function ($student) {
            $student->password_text = Crypt::decryptString($student->password_text);
            $student->moodle_username = $student->moodle_username;
            $student->moodle_password = Crypt::decryptString($student->moodle_password);
            return $student;
        });

        return $students;
    }


    public function headings(): array
    {
        return ['student_id', 'admission_date', 'first_name', 'last_name', 'father_name', 'mother_name', 'email', 'gender', 'dob', 'phone', 'password' , 'moodle_username' , 'moodle_password'];
    }
}
