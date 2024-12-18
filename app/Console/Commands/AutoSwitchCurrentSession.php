<?php

namespace App\Console\Commands;

use App\Models\EnrollSubject;
use App\Models\Session;
use App\Models\StudentEnroll;
use App\Services\Moodle\CourseService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Throwable;

class AutoSwitchCurrentSession extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'current-session:switch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto Switching For The Current Session According To The Date';

    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {

            $sessions = Session::query()->where('current', 0)->get();
            $today_date = Carbon::today()->toDateString();
            foreach ($sessions as $session) {
                if ($session->start_date == $today_date) {
                    Session::query()->where('current', 1)->update([
                        'current' => 0,
                    ]);
                    // $this->updateSessionOfferedCoursesOnMoodle($session);
                    $session->update(['current' => 1]);
                    StudentEnroll::query()->where('session_id', $session->id)->update([
                        'status' => 1,
                    ]);
                    StudentEnroll::query()->where('session_id', '!=' , $session->id)->update([
                        'status' => 0,
                    ]);
                    info("CURRENT SESSION AND ENROLLMENT UPDATED");
                }
            }
        } catch (Throwable $e) {
            logError(e: $e, method: __METHOD__, class: get_class($this), custom_message: __('Moodle_Error'));
        }
    }

    /**
     * Deperecated for now.
     */
    public function updateSessionOfferedCoursesOnMoodle($session)
    {
        $moodle_course_service = new CourseService();
        EnrollSubject::query()->where('session_id', $session->id)
            ->chunkById(10, function ($subject_enrolls) use ($moodle_course_service, $session) {
                foreach ($subject_enrolls as $subject_enroll) {
                    foreach ($subject_enroll->subjects as $subject) {
                        if (!isset($subject->id_on_moodle)) {
                            $created_course_on_moodle = $moodle_course_service->store($subject, $session->id_on_moodle);
                            $subject->id_on_moodle = $created_course_on_moodle[0]['id'];
                            $subject->save();
                        } else {
                            $moodle_course_service->edit($subject, $session->id_on_moodle);
                        }
                    }
                }
            });
    }


}
