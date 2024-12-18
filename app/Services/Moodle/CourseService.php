<?php

namespace App\Services\Moodle;

use App\Models\MoodleSubjectSession;
use App\Models\Program;
use App\Models\Session;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Throwable;

class CourseService extends BaseService
{
    protected $model = Subject::class;


    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Create Subject On Moodle
     */
    public function store(Subject $subject, Session $session)
    {
        $category_id = $session->id_on_moodle;
        // dd($category_id);

        // Create the data array for the POST request
        $courses = [
            [
                'fullname' => $subject->title,
                'shortname' => $subject->code . '_' . $session->getShortTitleForMoodle(),
                'categoryid' => $category_id,
                'idnumber' => $subject->code . '_' . $session->getShortTitleForMoodle(),  // Optional
                // 'summary' => 'This is a dummy course.',  // Optional
                // 'summaryformat' => 1,  // Default to HTML
                // 'format' => 'topics',  // Default to topics
                // 'showgrades' => 1,  // Default to 1
                // 'newsitems' => 5,  // Default to 5
                'startdate' => strtotime($session->start_date),  // Optional, current timestamp
                'enddate' => strtotime($session->end_date),  // Optional, one week from now
                // 'numsections' => 10,  // Optional
                'maxbytes' => 55048576,  // MB
                // 'showreports' => 0,  // Default to 0
                'visible' => $subject->status,  // Optional, default to 1
                // 'hiddensections' => 0,  // Optional
                // 'groupmode' => 0,  // Default to 0
                // 'groupmodeforce' => 0,  // Default to 0
                // 'defaultgroupingid' => 0,  // Default to 0
                // 'enablecompletion' => 1,  // Optional, default to 1
                // 'completionnotify' => 0,  // Optional, default to 0
                'lang' => 'en',  // Optional
            ]
        ];

        $query_params['courses'] = $courses;
        $query_params['wsfunction'] = 'core_course_create_courses';
        return parent::create($query_params);
    }


    /**
     * Update Subject On Moodle
     */
    public function editSubjectNameForSession(Subject $subject, Session $session, $subject_id_on_moodle)
    {
        // Create the data array for the POST request
        $courses = [
            [
                'id' => $subject_id_on_moodle,
                'fullname' => $subject->title,
                'shortname' => $subject->code . '_' . $session->getShortTitleForMoodle(),
                'idnumber' => $subject->code . '_' . $session->getShortTitleForMoodle(),
                'startdate' => strtotime($session->start_date),  // Optional, current timestamp
                'enddate' => strtotime($session->end_date),  // Optional, one week from now
            ]
        ];

        $query_params['courses'] = $courses;
        $query_params['wsfunction'] = 'core_course_update_courses';
        return parent::update($query_params);
    }

    public function duplicateCourseForNewSession(Subject $subject, Session $session)
    {
        info('START DUPLICATE COURSE ON MOODLE FOR COURSE:  ' . $subject->code . ' IN SESSION: ' . $session->title . ' COURSE ID: ' . $subject->id);
        $subject_id_on_moodle_to_duplicate = MoodleSubjectSession::query()->where('subject_id', $subject->id)->orderByDesc('created_at')->first()->id_on_moodle;
        info('subject_id_on_moodle_to_duplicate: ' . $subject_id_on_moodle_to_duplicate);
        $query_params['wsfunction'] = 'core_course_duplicate_course';
        $query_params['courseid'] = $subject_id_on_moodle_to_duplicate;
        $query_params['fullname'] = $subject->title;
        $query_params['shortname'] = $subject->code . '_' . now();
        $query_params['categoryid'] = $session->id_on_moodle;
        info($query_params);
        $created_course = parent::create($query_params);
        info('CREATED COURSE');
        info($created_course);
        MoodleSubjectSession::query()->updateOrCreate([
            'session_id' => $session->id,
            'subject_id' => $subject->id,
        ], [
            'session_id' => $session->id,
            'subject_id' => $subject->id,
            'id_on_moodle' => $created_course['id'],//duplicated course
        ]);
        // $this->editSubjectNameForSession($subject, $session, $created_course['id']);
    }

    /**
     * delete Subject On Moodle
     */
    public function destroy(Subject $subject)
    {
        // $query_params['courseids'][] = $subject->id_on_moodle;
        // $query_params['wsfunction'] = 'core_course_delete_courses';
        // parent::delete($query_params);
    }
}
