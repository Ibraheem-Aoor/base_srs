<?php

namespace App\Http\Controllers;

use App\Enums\ProgramCategoryEnum;
use App\Models\Program;
use App\Models\Semester;
use App\Models\Session;
use App\Models\Student;
use App\Models\Subject;
use App\Services\Moodle\CourseService;
use App\Services\Moodle\SessionService;
use App\Services\Moodle\StudentEnrollService;
use App\Services\Moodle\StudentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Throwable;

class QuickSetupSemesterController extends Controller
{
    public function categorizePrograms()
    {
        Program::query()->chunkById(10, function ($programs) {
            foreach ($programs as $program) {
                $category = $this->getCategoryForProgram($program->title);
                if ($category) {
                    $program->category = $category;
                    $program->save();
                }
            }
        });
        dd('DONE SUCCESSFULLY');
    }

    private function getCategoryForProgram($program)
    {
        if (strpos($program, 'Doctor') !== false || strpos($program, 'DM') !== false) {
            return ProgramCategoryEnum::DOCTOR->value;
        }

        if (strpos($program, 'Master') !== false || strpos($program, 'MBA') !== false) {
            return ProgramCategoryEnum::MASTER->value;
        }

        if (strpos($program, 'Bachelor') !== false || strpos($program, 'BBA') !== false) {
            return ProgramCategoryEnum::BACHELOR->value;
        }

        return null;
    }
    public function setupSemestersDate()
    {
        $semesters = [
            'Spring_(2024)' => ['start_date' => '2024-03-02', 'end_date' => '2024-05-01'],
            'Summer_(2024)' => ['start_date' => '2024-05-02', 'end_date' => '2024-07-01'],
            'Fall_(2024)' => ['start_date' => '2024-01-01', 'end_date' => '2024-03-01'],
        ];

        foreach ($semesters as $semesterName => $dates) {
            $semester = Semester::where('title', $semesterName)->first();

            if ($semester) {
                $semester->start_date = $dates['start_date'];
                $semester->end_date = $dates['end_date'];
                $semester->save();
            }
        }
        dd('DONE SUCCESSFULLY');
    }


    public function setupSessionDates()
    {
        // Define semesters and their session date ranges
        $semesters = [
            'Spring_(2024)' => [
                'Spring_1' => ['start_date' => '2024-03-02', 'end_date' => '2024-04-01'],
                'Spring_2' => ['start_date' => '2024-04-02', 'end_date' => '2024-05-01'],
            ],
            'Summer_(2024)' => [
                'Summer_1' => ['start_date' => '2024-05-02', 'end_date' => '2024-06-01'],
                'Summer_2' => ['start_date' => '2024-06-02', 'end_date' => '2024-07-01'],
            ],
            'Fall_(2024)' => [
                'Fall_1' => ['start_date' => '2024-01-01', 'end_date' => '2024-02-01'],
                'Fall_2' => ['start_date' => '2024-02-02', 'end_date' => '2024-03-01'],
            ],
        ];

        // Loop through each semester and its sessions
        foreach ($semesters as $semesterName => $sessions) {
            $semester = Semester::where('title', $semesterName)->first();

            if ($semester) {
                foreach ($sessions as $sessionKey => $dates) {
                    $sessionName = "2024_{$sessionKey}";
                    $session = Session::where('title', 'like', '%' . $sessionName . '%')->first();

                    if ($session) {
                        // Link session to the semester
                        $session->semester_id = $semester->id;

                        // Update session title
                        $session->title = "2024 {$sessionKey}";

                        // Set start and end dates
                        $session->start_date = $dates['start_date'];
                        $session->end_date = $dates['end_date'];

                        // Save the session
                        $session->save();
                    }
                }
            }
        }
        dd('DONE SUCCESSFULLY');
    }


    public function setupStudentCurrentEnroll()
    {
        // Get The CURRENT SESSION
        $current_running_session = Session::query()->where('current', 1)->first();
        $s = Student::query()->chunkById(10, function ($students) use ($current_running_session) {
            foreach ($students as $student) {
                //!! Disable All Enrolls And Make The Current Enroll Where The Current Session !!
                $student->studentEnrolls()->update(['status' => 0]);
                $student->studentEnrolls()->where('session_id', $current_running_session->id)->update(['status' => 1]);
            }
        });
        dd('DONE SUCCESSFULLY', $s);
    }


    /**
     * This Function Delets All Moodel Students.
     */
    public function deleteMoodleDuplicatedStudents(StudentService $moodle_student_service)
    {
        try {
            set_time_limit(0);
            $query_params['wsfunction'] = 'core_user_get_users';
            $user_ids = [];
            $s = Student::query()->chunkById(10, function ($students) use ($query_params, $moodle_student_service, &$user_ids) {
                foreach ($students as $student) {
                    $query_params['criteria'] = [
                        [
                            'key' => 'email',
                            'value' => $student->email,
                        ]
                    ];
                    $moodle_students = $moodle_student_service->get($query_params);
                    foreach ($moodle_students['users'] as $std) {
                        array_push($user_ids, $std['id']);
                    }
                }
            });
            // dd($user_ids);
            // Create the data array for the POST request
            $params['userids'] = $user_ids;
            $params['wsfunction'] = 'core_user_delete_users';
            $moodle_student_service->delete($params);
            dd('Done Successfully', $user_ids);
        } catch (Throwable $e) {
            dd($e);
        }
    }


    /**
     * Sync Current Session Student Enrolls With Moodle.
     */
    public function ernollStudents()
    {
        set_time_limit(0);
        $current_session = Session::query()->where('current' , 1)->first();
        $student_enroll_service = new StudentEnrollService();
        $students = Student::query()->chunkById(10, function ($students)use($current_session , $student_enroll_service) {
            foreach ($students as $student) {
                $student_enrolls = $student->studentEnrolls()->where('session_id' , $current_session->id)->get();
                foreach($student_enrolls as $student_enroll){
                    $student_enroll_service->store($student_enroll);
                }
            }
        });
        dd('DONE SUCCESSFULLY');
    }

    public function updateStudentsCredits()
    {
        $s = Student::query()->chunkById(20 , function($students){
            foreach($students as $student){
                $student->update([
                    'email' => 'std'.$student->student_id.'@aeb.edu',
                    'password' => Hash::make('Summer2@2024'),
                    'password_text' => Crypt::encryptString('Summer2@2024'),
                ]);
            }
        });
        dd($s);
    }


}
