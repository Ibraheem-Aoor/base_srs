<?php
namespace App\Services\Moodle;

use App\Models\MoodleSubjectSession;
use App\Models\Program;
use App\Models\Session;
use App\Models\StudentEnroll;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Throwable;

class StudentEnrollService extends BaseService
{
    protected $model = StudentEnroll::class;
    protected RoleService $role_service;
    public function __construct()
    {
        parent::__construct();
        $this->role_service = new RoleService();
    }

    /**
     * Create The Enroll On Moodle.
     */
    public function store(StudentEnroll $student_enroll)
    {
        $course_service = new CourseService();
        //bring the student role id  from moodle
        $student_role_id = $this->role_service->getStudentRoleId();
        // Create the data array for the POST request
        $enrolments = [];
        foreach ($student_enroll->subjects as $subject) {
            $subject_id_on_moodle = MoodleSubjectSession::query()->where('subject_id', $subject->id)->where('session_id', $student_enroll->session_id)->first()?->id_on_moodle;
            if (isset($subject_id_on_moodle)) {

                $enrolments[] = [
                    'roleid' => $student_role_id,
                    'courseid' => $subject_id_on_moodle,
                    'userid' => $student_enroll->student->id_on_moodle,
                    'timestart' => strtotime($student_enroll->session->start_date),
                    'timeend' => strtotime($student_enroll->session->end_date),
                    'suspend' => 0,
                ];
            }else{
                $session = Session::find($student_enroll->session_id);
                $created_course_on_moodle = $course_service->store($subject, $session);
                MoodleSubjectSession::query()->updateOrCreate([
                    'session_id' => $session->id,
                    'subject_id' => $subject->id,
                ], [
                    'session_id' => $session->id,
                    'subject_id' => $subject->id,
                    'id_on_moodle' => $created_course_on_moodle[0]['id'],
                ]);
                return $this->store($student_enroll);
            }
        }
        return $this->makeEnrollmentRequest($enrolments);
    }


    /**
     * Sync the student's enrollments on Moodle.
     */
    public function sync(StudentEnroll $student_enroll, array $requested_subjects)
    {
        // Retrieve the student role ID from Moodle
        $student_role_id = $this->role_service->getStudentRoleId();

        // Get currently enrolled subjects
        $currently_enrolled_subjects = $student_enroll->subjects()->pluck('id')->toArray();

        // Determine subjects to enroll in and subjects to drop
        $subjects_to_enroll = array_diff($requested_subjects, $currently_enrolled_subjects);
        $subjects_to_drop = array_diff($currently_enrolled_subjects, $requested_subjects);
        // Prepare enrolments data
        $enrolments = [];

        // Add enrolments for new subjects
        foreach ($subjects_to_enroll as $subject_id) {
            $subject = Subject::find($subject_id);
            $subject_id_on_moodle = MoodleSubjectSession::query()->where('subject_id', $subject->id)->where('session_id', $student_enroll->session_id)->first()->id_on_moodle;
            $enrolments[] = [
                'roleid' => $student_role_id,
                'courseid' => $subject_id_on_moodle,
                'userid' => $student_enroll->student->id_on_moodle,
                'timestart' => strtotime($student_enroll->session->start_date),
                'timeend' => strtotime($student_enroll->session->end_date),
                'suspend' => 0,
            ];
        }

        // Suspend enrolments for dropped subjects
        foreach ($subjects_to_drop as $subject_id) {
            $subject = Subject::find($subject_id);
            $subject_id_on_moodle = MoodleSubjectSession::query()->where('subject_id', $subject->id)->where('session_id', $student_enroll->session_id)->first()->id_on_moodle;
            $enrolments[] = [
                'roleid' => $student_role_id,
                'courseid' => $subject_id_on_moodle,
                'userid' => $student_enroll->student->id_on_moodle,
                'timestart' => strtotime($student_enroll->session->start_date),
                'timeend' => strtotime($student_enroll->session->end_date),
                'suspend' => 1,
            ];
        }
        return $this->makeEnrollmentRequest($enrolments);
    }



    /**
     * Bulk Suspend For Student Subjects.
     */
    public function bulkUnEnroll($id, $enrollment)
    {
        // Retrieve the student role ID from Moodle
        $student_role_id = $this->role_service->getStudentRoleId();
        foreach ($enrollment->subjects as $subject) {
            $subject_id_on_moodle = MoodleSubjectSession::query()->where('subject_id', $subject->id)->where('session_id', $enrollment->session_id)->first()->id_on_moodle;
            $enrolments[] = [
                'roleid' => $student_role_id,
                'courseid' => $subject_id_on_moodle,
                'userid' => $id,
                'suspend' => 1,
            ];
        }
        return $this->makeEnrollmentRequest($enrolments);
    }

    private function makeEnrollmentRequest($enrolments)
    {
        if (isset($enrolments) && !empty($enrolments)) {
            // Set the web service function name
            $query_params['wsfunction'] = 'enrol_manual_enrol_users';
            // Add enrolments to the query parameters
            $query_params['enrolments'] = $enrolments;
            // Send the enrolment data to Moodle
            return parent::update($query_params);
        }
    }




}
