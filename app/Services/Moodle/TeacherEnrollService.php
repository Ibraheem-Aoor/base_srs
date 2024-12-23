<?php
namespace App\Services\Moodle;

use App\Models\ClassRoutine;
use App\Models\MoodleClassRoutine;
use App\Models\MoodleSubjectSession;
use App\Models\Program;
use App\Models\Session;
use App\Models\StudentEnroll;
use App\Models\Subject;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Throwable;

class TeacherEnrollService extends BaseService
{
    protected $model = ClassRoutine::class;
    protected RoleService $role_service;
    public function __construct()
    {
        parent::__construct();
        $this->role_service = new RoleService();
    }

    /**
     * Create The Enroll On Moodle.
     */
    public function store(Builder|ClassRoutine $class_routine): mixed
    {

        $course_service = new CourseService();
        //bring the student role id  from moodle
        $teacher_role_id = $this->role_service->getRoleId('teacher');
        // Create the data array for the POST request
        $enrolments = [];
        $subject_id_on_moodle = MoodleSubjectSession::query()->where('subject_id', $class_routine->subject_id)->where('session_id', $class_routine->session_id)->first()?->id_on_moodle;
        if (isset($subject_id_on_moodle)) {

            $enrolments[] = [
                'roleid' => $teacher_role_id,
                'courseid' => $subject_id_on_moodle,
                'userid' => $class_routine->teacher->id_on_moodle,
                'timestart' => strtotime($class_routine->session->start_date),
                'timeend' => strtotime($class_routine->session->end_date),
                'suspend' => 0,
            ];
        } else {
            $session = Session::find($class_routine->session_id);
            $created_course_on_moodle = $course_service->store($class_routine->subject, $session);
            MoodleSubjectSession::query()->updateOrCreate([
                'session_id' => $session->id,
                'subject_id' => $class_routine->subject_id,
            ], [
                'session_id' => $session->id,
                'subject_id' => $class_routine->subject_id,
                'id_on_moodle' => $created_course_on_moodle[0]['id'],
            ]);
            return $this->store($class_routine);
        }
        return $this->makeEnrollmentRequest($enrolments);
    }


    public function sync(array $class_routine_ids)
    {

        $class_routines = $this->model::query()->find($class_routine_ids);
        $enrolments = [];
        $teacher_role_id = $this->role_service->getRoleId('editingteacher');

        if (empty($class_routine_ids)) {
            return true;
        }

        if (!is_array($class_routine_ids)) {
            throw new \InvalidArgumentException('Class routine IDs must be an array');
        }

        if ($class_routines->count() !== count($class_routine_ids)) {
            throw new \RuntimeException('Not all class routines were found in the database');
        }

        foreach ($class_routines as $class_routine) {
            $subject_id_on_moodle = MoodleSubjectSession::query()->where('subject_id', $class_routine->subject_id)->where('session_id', $class_routine->session_id)->first()?->id_on_moodle;

            $class_routine_on_moodle = MoodleClassRoutine::query()->where('class_routine_id', $class_routine->id)->first();
            // If the class routine not exists on Moodle, create it
            if (!$class_routine_on_moodle) {
                MoodleClassRoutine::create([
                    'class_routine_id' => $class_routine->id,
                    'session_id' => $class_routine->session_id,
                    'teacher_id' => $class_routine->teacher_id,
                    'subject_id' => $class_routine->subject_id,
                ]);
                $enrolments[] = [
                    'roleid' => $teacher_role_id,
                    'courseid' => $subject_id_on_moodle,
                    'userid' => $class_routine->teacher->id_on_moodle,
                    'timestart' => strtotime($class_routine->session->start_date),
                    'timeend' => strtotime($class_routine->session->end_date),
                    'suspend' => 0,
                ];

            }
            //if exists on moodle but incoming teacher is different from stored on. then the teacher has been changed.
            elseif ($class_routine_on_moodle && $class_routine->teacher_id != $class_routine_on_moodle->teacher_id) {
                $enrolments[] = [
                    'roleid' => $teacher_role_id,
                    'courseid' => $subject_id_on_moodle,
                    'userid' => $class_routine_on_moodle->teacher->id_on_moodle,
                    'timestart' => strtotime($class_routine->session->start_date),
                    'timeend' => strtotime($class_routine->session->end_date),
                    'suspend' => 1,
                ];
                $enrolments[] = [
                    'roleid' => $teacher_role_id,
                    'courseid' => $subject_id_on_moodle,
                    'userid' => $class_routine->teacher->id_on_moodle,
                    'timestart' => strtotime($class_routine->session->start_date),
                    'timeend' => strtotime($class_routine->session->end_date),
                    'suspend' => 0,
                ];
            }
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
