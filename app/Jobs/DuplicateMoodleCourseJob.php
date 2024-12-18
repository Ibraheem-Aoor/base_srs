<?php

namespace App\Jobs;

use App\Models\Session;
use App\Models\Subject;
use App\Services\Moodle\CourseService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class DuplicateMoodleCourseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $maxExceptions = 4;

    protected $subject, $session;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Subject $subject, Session $session)
    {
        $this->subject = $subject;
        $this->session = $session;
        $this->queue = 'moodle';
    }

    /**
     * Determine number of times the job may be attempted.
     */
    public function tries(): int
    {
        return 4;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $moodle_course_service = new CourseService();
            $moodle_course_service->duplicateCourseForNewSession($this->subject, $this->session);
            info("DUPLICATE COURSE ON MOODLE SUCCESS WITH  FOR COURSE:  " . $this->subject->code . ' IN SESSION: ' . $this->session->title);
        } catch (Throwable $e) {
            info("DUPLICATE COURSE ON MOODLE FAILED WITH ERROR FOR COURSE:  " . $this->subject->code . ' IN SESSION: ' . $this->session->title);
            logError(e: $e, method: __METHOD__, class: get_class($this));
        }
    }
}
