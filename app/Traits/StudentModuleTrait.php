<?php
namespace App\Traits;

use App\Models\Fee;
use App\Models\PrintSetting;
use App\Models\Student;

trait StudentModuleTrait
{

    /**
     * This Trait Contains Common Student Module Functions
     */

    public function printFinancialAgreement(Student $student)
    {
        $data['title'] = trans_choice('module_fees_report', 1);
        $data['route'] = $this->route;
        $data['view'] = $this->view;
        $data['path'] = 'print-setting';
        // Getting all student fees regaring the enrollement "because enrollments changed according to sessions and we don't konow when the admin adding the fees".
        $student_enrollments = $student->studentEnrolls()->pluck('id')->toArray();
        // View
        $data['print'] = PrintSetting::where('slug', 'fees-receipt')->firstOrFail();
        $data['rows'] = Fee::query()->whereIn('student_enroll_id', array_values($student_enrollments))->get()->groupBy('assign_date');
        $data['student'] = $student;
        return view('admin.student.financial_agreement', $data);

    }

}
