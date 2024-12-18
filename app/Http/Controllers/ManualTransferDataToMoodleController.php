<?php

namespace App\Http\Controllers;

use App\Models\EnrollSubject;
use App\Models\Session;
use App\Models\Student;
use App\Models\Subject;
use App\Services\Moodle\CourseService;
use App\Services\Moodle\SessionService;
use App\Services\Moodle\StudentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Throwable;

class ManualTransferDataToMoodleController extends Controller
{
    /**
     * Qucik add to srs program on moodle instead of manual typing them there.
     * This function is not synicing or saving any id's coming from moodle response.
     * Session In SRS are repesented as categories in moodle.
     */
    public function transferPrograms(SessionService $moodle_session_service)
    {
        try {

            $sessions = Session::get();
            foreach ($sessions as $session) {
                $moodle_session_service->store($session);
            }
            dd('Done Successfully');
        } catch (Throwable $e) {
            dd($e);
        }
    }
    /**
     * Qucik add to srs program on moodle instead of manual typing them there.
     * This function is not synicing or saving any id's coming from moodle response.
     * Session In SRS are repesented as categories in moodle.
     */
    public function transferCourses(CourseService $moodle_course_service)
    {
        try {
            $this->setSemestersWithEnrollSubject();
            $current_session = Session::query()->where('current', 1)->first();
            $subjects = Subject::get();
            foreach ($subjects as $subject) {
                $subject_enroll_in_current_session = EnrollSubject::query()->whereHas('subjects', function ($subjects) use ($subject) {
                    $subjects->where('id', '=', $subject->id);
                })->whereHas('session', function ($session) {
                    $session->where('current', 1);
                })->first();
                $subject_next_enroll = EnrollSubject::query()->whereHas('subjects', function ($subjects) use ($subject, $current_session) {
                    $subjects->where('id', '=', $subject->id);
                })->whereHas('session', function ($session) use ($current_session) {
                    $session->whereDate('start_date', '>=', $current_session->start_date);
                })->first();
                $subject_current_enroll = EnrollSubject::query()->whereHas('subjects', function ($subjects) use ($subject, $current_session) {
                    $subjects->where('id', '=', $subject->id);
                })->first();
                if (isset($subject_enroll_in_current_session)) {
                    $category_id = $subject_enroll_in_current_session->session->id_on_moodle;
                } elseif (isset($subject_next_enroll)) {
                    $category_id = $subject_next_enroll->session->id_on_moodle;
                } elseif (isset($subject_current_enroll)) {
                    $category_id = $subject_current_enroll->session->id_on_moodle;
                } else {
                    $category_id = Session::query()->where('current', 0)->first()->id;
                }
                $created_moodle_course = $moodle_course_service->store($subject, $category_id);
                // $subject->id_on_moodle = $created_moodle_course[0]['id'];
                // $subject->save();
            }
            dd('Done Successfully' , count($subjects));
        } catch (Throwable $e) {
            dd($e);
        }
    }


    protected function setSemestersWithEnrollSubject()
    {
        EnrollSubject::query()->chunkById(500, function ($enroll_subjects) {
            foreach ($enroll_subjects as $enroll_subject) {
                $enroll_subject->update([
                    'session_id' => Session::query()->where('semester_id', $enroll_subject->semester_id)->first()->id,
                ]);
            }
        });
    }



    public function taransferUsers(StudentService $moodle_student_service)
    {
        try{
            set_time_limit(0);
            $students = Student::get();
            $i= 0;
            foreach($students as $student)
            {
                $i++;
                $password = generate_moodle_password();
                $moodle_created_student = $moodle_student_service->store($student, $password);
                $student->update([
                    'password' => Hash::make($password),
                    'password_text' => Crypt::encryptString($password),
                    'id_on_moodle' => $moodle_created_student[0]['id'],
                    'moodle_password' => Crypt::encryptString($password),
                    'moodle_username' =>  generate_moodle_username($student->first_name , $student->last_name),
                ]);
            }
            dd('Done Successfully', $i);
        }catch(Throwable $e)
        {
            dd($e);
        }
    }
}
