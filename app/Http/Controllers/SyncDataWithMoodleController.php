<?php

namespace App\Http\Controllers;

use App\Models\EnrollSubject;
use App\Models\MoodleSubjectSession;
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

class SyncDataWithMoodleController extends Controller
{
    /**
     * This Class assumes that we have data already exisits on moodle. so we need to bring the ids to link with our srs.
     */


    #=-----------
    public function syncSessions(SessionService $moodle_session_service)
    {
        try {
            $query_params['wsfunction'] = 'core_course_get_categories';
            $moodle_sesssions = $moodle_session_service->get($query_params);
            foreach ($moodle_sesssions as $moodle_session) {
                if ($srs_session = Session::query()->where('title', $moodle_session['name'])->first()) {
                    $srs_session->update(['id_on_moodle' => $moodle_session['id']]);
                }
            }
            dd('Done Successfully');
        } catch (Throwable $e) {
            dd($e);
        }
    }
    public function syncCourses(CourseService $moodle_course_service)
    {
        try {
            Subject::query()->update(['id_on_moodle' => null]);
            $query_params['wsfunction'] = 'core_course_get_courses';
            $moodle_courses = $moodle_course_service->get($query_params);
            $moodle_synced_subjects = MoodleSubjectSession::query()->pluck('subject_id')->toArray();
            $db_subjects = Subject::query()->whereNotIn('id' , array_values($moodle_synced_subjects))->get();
            $updated_subjects = [];
            foreach ($moodle_courses as $moodle_course) {
                foreach ($db_subjects as $subject) {
                    if (
                        strpos($moodle_course['shortname'], $subject->code) !== false ||
                        strpos($moodle_course['shortname'], strtoupper($subject->code)) !== false ||
                        strpos($moodle_course['shortname'], strtolower($subject->code)) !== false ||
                        strpos($moodle_course['fullname'], $subject->code) !== false ||
                        strpos($moodle_course['fullname'], strtoupper($subject->code)) !== false ||
                        strpos($moodle_course['fullname'], strtolower($subject->code)) !== false
                    ) {
                        $session = Session::query()->where('id_on_moodle', $moodle_course['categoryid'])->first();
                        if ($session) {
                            MoodleSubjectSession::query()->updateOrCreate([
                                'session_id' => $session->id,
                                'subject_id' => $subject->id,
                            ], [
                                'session_id' => $session->id,
                                'subject_id' => $subject->id,
                                'id_on_moodle' => $moodle_course['id'],
                            ]);
                            $updated_subjects[$subject->code] = $moodle_course['shortname'];
                        }
                    }
                }
            }
            // $this->updateSessionEnrollsOnSrs();
            dd('Done Successfully', $updated_subjects);
        } catch (Throwable $e) {
            dd($e);
        }
    }

    public function updateSessionEnrollsOnSrs()
    {
        $sessions_with_synced_subjects_on_moodle = MoodleSubjectSession::query()->pluck('session_id')->toArray();
        EnrollSubject::query()->whereIn('session_id' , array_values($sessions_with_synced_subjects_on_moodle))->delete();
        MoodleSubjectSession::query()->chunkById(10 , function($session_subjects){
            foreach($session_subjects as $session_subject)
            {
                EnrollSubject::query()->create([
                    'session_id' => $session_subject->session_id,
                ]);
            }
        });
    }


    public function syncStudents(StudentService $moodle_student_service)
    {
        try {
            $query_params['wsfunction'] = 'core_user_get_users';
            $s = Student::query()->chunkById(10, function ($students) use ($query_params, $moodle_student_service) {
                foreach ($students as $student) {
                    $query_params['criteria'] = [
                        [
                            'key' => 'email',
                            'value' => $student->email,
                        ]
                    ];
                    $moodle_students = $moodle_student_service->get($query_params);
                    $moodle_username = generate_moodle_username($student->first_name, $student->last_name);
                    $moodle_password = generate_moodle_password();
                    $student->update([
                        'id_on_moodle' => $moodle_students['users'][0]['id'],
                        'moodle_username' => $moodle_username,
                        'moodle_password' => Crypt::encryptString($moodle_password),
                        'password_text' => Crypt::encryptString($moodle_password),
                        'password' => Hash::make($moodle_password),
                    ]);
                    $moodle_student_service->changePassword($student , $moodle_password);
                }
            });
            dd('Done Successfully', $s);
        } catch (Throwable $e) {
            dd($e);
        }
    }

    // public function syncStudents(StudentService $moodle_student_service)
    // {
    //     try {
    //         set_time_limit(0);
    //         $query_params['wsfunction'] = 'core_user_get_users';
    //         $multi_students = [];
    //         $s = Student::query()->chunkById(10, function ($students) use ($query_params, $moodle_student_service , &$multi_students) {
    //             foreach ($students as $student) {
    //                 $query_params['criteria'] = [
    //                     [
    //                         'key' => 'email',
    //                         'value' => $student->email,
    //                     ]
    //                 ];
    //                 $moodle_students = $moodle_student_service->get($query_params);
    //                 if(count($moodle_students['users']) > 1)
    //                 {
    //                     foreach($moodle_students['users']  as $std)
    //                     {
    //                         $multi_students[] = [
    //                             'username' => $std['username'],
    //                             'email' => $std['email'],
    //                         ];
    //                     }
    //                 }
    //                 // $student->update([
    //                 //     'id_on_moodle' => $moodle_students['users'][0]['id'],
    //                 // ]);
    //             }
    //         });
    //         dd('Done Successfully', $s , $multi_students);
    //     } catch (Throwable $e) {
    //         dd($e);
    //     }
    // }
}
