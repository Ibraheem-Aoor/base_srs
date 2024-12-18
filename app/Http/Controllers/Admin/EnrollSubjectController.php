<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\DuplicateMoodleCourseJob;
use App\Models\EnrollSubject;
use App\Models\MoodleSubjectSession;
use Illuminate\Http\Request;
use App\Models\Semester;
use App\Models\Program;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Faculty;
use App\Models\Session;
use App\Services\Moodle\CourseService;
use Illuminate\Support\Facades\DB;
use Throwable;
use Toastr;

class EnrollSubjectController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Module Data
        $this->title = trans_choice('module_enroll_subject', 1);
        $this->route = 'admin.enroll-subject';
        $this->view = 'admin.enroll-subject';
        $this->path = 'enroll-subject';
        $this->access = 'enroll-subject';


        $this->middleware('permission:' . $this->access . '-view|' . $this->access . '-create|' . $this->access . '-edit|' . $this->access . '-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:' . $this->access . '-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:' . $this->access . '-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:' . $this->access . '-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;
        $data['path'] = $this->path;
        $data['access'] = $this->access;

        $data['faculties'] = Faculty::where('status', '1')
            ->orderBy('title', 'asc')->get();
        $data['rows'] = EnrollSubject::orderBy('id', 'desc')->get();
        $data['sessions'] = Session::query()->status(1)->with('semester:id,title')->get(['id', 'title']);
        return view($this->view . '.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, CourseService $moodle_course_service)
    {
        // dd(Session::find($request->session));
        // Field Validation
        $request->validate([
            'program' => 'required',
            'session' => 'required|exists:sessions,id',
            'section' => 'nullable',
            'subjects' => 'required',
        ]);
        try {
            DB::beginTransaction();
            // Insert Data
            $enrollSubject = EnrollSubject::query()->firstOrCreate(
                ['program_id' => $request->program, 'session_id' => $request->session],
                ['program_id' => $request->program, 'session_id' => $request->session]
            );
            $this->syncWithMoodle($request, $moodle_course_service, $enrollSubject);
            // Attach Update
            $enrollSubject->subjects()->sync($request->subjects);

            DB::commit();
            Toastr::success(__('msg_updated_successfully'), __('msg_success'));
        } catch (Throwable $e) {
            // dd($e);
            DB::rollBack();
            logError(e: $e, method: __METHOD__, class: get_class($this));
        }


        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(EnrollSubject $enrollSubject)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(EnrollSubject $enrollSubject)
    {
        //
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;
        $data['path'] = $this->path;
        $data['access'] = $this->access;


        $data['row'] = $enrollSubject;
        $data['rows'] = EnrollSubject::orderBy('id', 'desc')->get();

        $data['faculties'] = Faculty::where('status', '1')->orderBy('title', 'asc')->get();
        $data['programs'] = Program::where('faculty_id', $enrollSubject->program->faculty_id)->where('status', '1')->orderBy('title', 'asc')->get();

        $semesters = Semester::where('status', 1);
        $semesters->with('programs')->whereHas('programs', function ($query) use ($enrollSubject) {
            $query->where('program_id', $enrollSubject->program_id);
        });
        $data['semesters'] = $semesters->orderBy('id', 'asc')->get();

        $sections = Section::where('status', 1);
        $sections->with('semesterPrograms')->whereHas('semesterPrograms', function ($query) use ($enrollSubject) {
            $query->where('program_id', $enrollSubject->program_id);
            $query->where('semester_id', $enrollSubject->semester_id);
        });
        $data['sections'] = $sections->orderBy('title', 'asc')->get();

        $subjects = Subject::where('status', 1);
        $subjects->with('programs')->whereHas('programs', function ($query) use ($enrollSubject) {
            $query->where('program_id', $enrollSubject->program_id);
        });
        $data['subjects'] = $subjects->orderBy('code', 'asc')->get();
        $data['sessions'] = Session::query()->status(1)->with('semester:id,title')->get(['id', 'title']);


        return view($this->view . '.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EnrollSubject $enrollSubject, CourseService $moodle_course_service)
    {
        // Field Validation
        $request->validate([
            'program' => 'required',
            'session' => 'required',
            'section' => 'nullable',
            'subjects' => 'required',
        ]);
        $enroll = EnrollSubject::where('id', '!=', $enrollSubject->id)->where('program_id', $request->program)->where('session_id', $request->session)->first();

        try {
            if (isset($enroll)) {
                Toastr::error(__('msg_data_already_exists'), __('msg_error'));
            } else {
                DB::beginTransaction();
                // Update Data
                $enrollSubject->program_id = $request->program;
                $enrollSubject->session_id = $request->session;
                $enrollSubject->section_id = $request->section;
                $enrollSubject->save();
                $this->syncWithMoodle($request, $moodle_course_service, $enrollSubject);
                // Attach Update
                $enrollSubject->subjects()->sync($request->subjects);
                DB::commit();
                Toastr::success(__('msg_updated_successfully'), __('msg_success'));
            }
        } catch (Throwable $e) {
            // dd($e);
            DB::rollBack();
            logError(e: $e, method: __METHOD__, class: get_class($this));
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function destroy(EnrollSubject $enrollSubject)
    // {
    //     Toastr::error('Not ALlowed', __('msg_error'));
    //     return back();
    //     // Detach
    //     $enrollSubject->subjects()->detach();

    //     // Delete Data
    //     $enrollSubject->delete();

    //     Toastr::success(__('msg_deleted_successfully'), __('msg_success'));

    //     return redirect()->route($this->route . '.index');
    // }
        public function destroy(EnrollSubject $enrollSubject)
    {
        // dd($enrollSubject);
        if (is_null($enrollSubject->semester_id) && is_null($enrollSubject->section_id)) {
            // Detach
            $enrollSubject->subjects()->detach();
            // Delete Data
            $enrollSubject->delete();
            Toastr::success(__('msg_deleted_successfully'), __('msg_success'));
        } else {
            Toastr::error('Not ALlowed', __('msg_error'));
            return back();
        }


        return redirect()->route($this->route . '.index');
    }



    /**
     * Sync The Subjects with Moodle
     * Create Unexising Subjects on moodle
     * Assign Subjects To Proper Sessions "categories" on moodle
     * The Core Key here is session_id because each session might have different subject from different programs.
     * so we need to make sure that the syncing is constrained to the session enrollments including all the session programs.
     */
    /**
     * Sync the subjects with Moodle.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Services\Moodle\CourseService  $moodle_course_service
     * @param  \App\Models\EnrollSubject|null  $enrollSubject
     * @return void
     */
    public function syncWithMoodle(Request $request, CourseService $moodle_course_service, $enrollSubject = null)
    {
        $subjects = Subject::find($request->subjects);
        $session = Session::query()->findOrFail($request->session);
        $this->syncSubjectsWithMoodle($subjects, $session, $moodle_course_service);
        // $this->removeUnenrolledSubjects($subjects, $session, $enrollSubject, $moodle_course_service);
    }

    /**
     * Sync the given subjects with Moodle.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $subjects
     * @param  \App\Models\Session  $session
     * @param  \App\Services\Moodle\CourseService  $moodle_course_service
     * @return void
     */
    private function syncSubjectsWithMoodle($subjects, $session, CourseService $moodle_course_service)
    {
        foreach ($subjects as $subject) {
            $is_subject_exists_on_moodle_at_all = MoodleSubjectSession::query()->where('subject_id', $subject->id)->exists();
            $is_sujbect_exists_for_session = MoodleSubjectSession::query()->where('session_id', $session->id)->where('subject_id', $subject->id)->exists();
            if (!$is_subject_exists_on_moodle_at_all) {
                $created_course_on_moodle = $moodle_course_service->store($subject, $session);
                MoodleSubjectSession::query()->updateOrCreate([
                    'session_id' => $session->id,
                    'subject_id' => $subject->id,
                ], [
                    'session_id' => $session->id,
                    'subject_id' => $subject->id,
                    'id_on_moodle' => $created_course_on_moodle[0]['id'],
                ]);
                $subject->save();
            } elseif (!$is_sujbect_exists_for_session) {

                DuplicateMoodleCourseJob::dispatch($subject, $session);
            }
        }
    }

    /**
     * Remove subjects that are no longer enrolled in the session.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $subjects
     * @param  \App\Models\Session  $session
     * @param  \App\Models\EnrollSubject|null  $enrollSubject
     * @param  \App\Services\Moodle\CourseService  $moodle_course_service
     * @return void
     */
    private function removeUnenrolledSubjects($subjects, $session, $enrollSubject, CourseService $moodle_course_service)
    {
        // if (isset($enrollSubject)) {
        //     foreach ($enrollSubject->subjects as $subject) {

        //         $is_course_still_with_session = in_array($subject->id, array_values($subjects->pluck('id')->toArray())) &&
        //             EnrollSubject::query()
        //                 ->where('session_id', $session->id)
        //                 ->whereHas('subjects', function ($query) use ($subjects) {
        //                     $query->whereIn('id', array_values($subjects->pluck('id')->toArray()));
        //                 })
        //                 ->exists();
        //         if (!$is_course_still_with_session && $subject->id_on_moodle) {
        //             $moodle_course_service->destroy($subject);
        //             $subject->id_on_moodle = null;
        //             $subject->save();
        //         }
        //     }
        // }
    }
}
