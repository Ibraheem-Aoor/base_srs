<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoutine;
use Illuminate\Http\Request;
use App\Models\PrintSetting;
use App\Models\ClassRoom;
use App\Models\EnrollSubject;
use App\Models\Semester;
use App\Models\Faculty;
use App\Models\MoodleClassRoutine;
use App\Models\Session;
use App\Models\Program;
use App\Models\Section;
use App\Models\Subject;
use App\Services\Moodle\TeacherEnrollService;
use App\Services\Moodle\TeacherService;
use App\User;
use Toastr;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class ClassRoutineController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Module Data
        $this->title = trans_choice('module_class_routine', 1);
        $this->route = 'admin.class-routine';
        $this->view = 'admin.class-routine';
        $this->path = 'class-routine';
        $this->access = 'class-routine';


        $this->middleware('permission:' . $this->access . '-view|' . $this->access . '-create|' . $this->access . '-print', ['only' => ['index', 'show']]);
        $this->middleware('permission:' . $this->access . '-create', ['only' => ['create', 'store', 'destroy']]);
        $this->middleware('permission:' . $this->access . '-teacher', ['only' => ['teacher']]);
        $this->middleware('permission:' . $this->access . '-print', ['only' => ['print']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;
        $data['path'] = $this->path;
        $data['access'] = $this->access;


        if (!empty($request->faculty) || $request->faculty != null) {
            $data['selected_faculty'] = $faculty = $request->faculty;
        } else {
            $data['selected_faculty'] = '0';
        }

        if (!empty($request->program) || $request->program != null) {
            $data['selected_program'] = $program = $request->program;
        } else {
            $data['selected_program'] = '0';
        }

        if (!empty($request->session) || $request->session != null) {
            $data['selected_session'] = $session = $request->session;
        } else {
            $data['selected_session'] = '0';
        }

        if (!empty($request->semester) || $request->semester != null) {
            $data['selected_semester'] = $semester = $request->semester;
        } else {
            $data['selected_semester'] = '0';
        }

        if (!empty($request->section) || $request->section != null) {
            $data['selected_section'] = $section = $request->section;
        } else {
            $data['selected_section'] = '0';
        }


        $data['print'] = PrintSetting::where('slug', 'class-routine')->first();

        // Search Filter
        $data['faculties'] = Faculty::where('status', '1')->orderBy('title', 'asc')->get();

        if (!empty($request->faculty) && !empty($request->program) && !empty($request->session)) {
            $data['programs'] = Program::where('faculty_id', $faculty)->where('status', '1')->orderBy('title', 'asc')->get();

            $sessions = Session::where('status', 1);
            $sessions->with('programs')->whereHas('programs', function ($query) use ($program) {
                $query->where('program_id', $program);
            });
            $data['sessions'] = $sessions->orderBy('id', 'desc')->get();

        }


        // Routine Filter
        if (!empty($request->program) && !empty($request->session)) {

            $routines = ClassRoutine::where('status', '1');
            if (!empty($request->program)) {
                $routines->where('program_id', $request->program);
            }
            if (!empty($request->session)) {
                $session = Session::query()->find($request->session);
                $routines->where('session_id', $session->id)->where('semester_id', $session->semester_id);
            }
            $data['rows'] = $routines->orderBy('start_time', 'asc')->get();
        }
        return view($this->view . '.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;
        $data['access'] = $this->access;


        if (!empty($request->faculty) || $request->faculty != null) {
            $data['selected_faculty'] = $faculty = $request->faculty;
        } else {
            $data['selected_faculty'] = '0';
        }

        if (!empty($request->program) || $request->program != null) {
            $data['selected_program'] = $program = $request->program;
        } else {
            $data['selected_program'] = '0';
        }

        if (!empty($request->session) || $request->session != null) {
            $data['selected_session'] = $session = $request->session;
        } else {
            $data['selected_session'] = '0';
        }

        if (!empty($request->semester) || $request->semester != null) {
            $data['selected_semester'] = $semester = $request->semester;
        } else {
            $data['selected_semester'] = '0';
        }

        if (!empty($request->section) || $request->section != null) {
            $data['selected_section'] = $section = $request->section;
        } else {
            $data['selected_section'] = '0';
        }

        // set session
        $session = Session::query()->find($request->session);

        // Search Filter
        $data['faculties'] = Faculty::where('status', '1')->orderBy('title', 'asc')->get();

        if (!empty($request->faculty) && !empty($request->program) && !empty($request->session)) {
            $data['programs'] = Program::where('faculty_id', $faculty)->where('status', '1')->orderBy('title', 'asc')->get();

            $sessions = Session::where('status', 1);
            $sessions->with('programs')->whereHas('programs', function ($query) use ($program) {
                $query->where('program_id', $program);
            });
            $data['sessions'] = $sessions->orderBy('id', 'desc')->get();


            $subjects = Subject::where('status', 1);
            $subjects->with('subjectEnrolls')->whereHas('subjectEnrolls', function ($query) use ($program, $session) {
                $query->where('program_id', $program)->where('session_id', $session->id);
            });
            $data['subjects'] = $subjects->orderBy('code', 'asc')->get();
        }


        $data['rooms'] = ClassRoom::where('status', '1')->orderBy('title', 'asc')->get();

        $teachers = User::where('status', '1');
        $teachers->with('roles')->whereHas('roles', function ($query) {
            $query->where('slug', 'teacher');
        });
        $data['teachers'] = $teachers->orderBy('staff_id', 'asc')->get();

        // Routine Filter
        if (!empty($request->program) && !empty($request->session)) {

            $routines = ClassRoutine::where('status', '1');
            if (!empty($request->program)) {
                $routines->where('program_id', $request->program);
            }
            if (!empty($request->session)) {
                $routines->where('session_id', $request->session)->where('semester_id', $session->semester_id);
            }
            $data['rows'] = $routines->orderBy('start_time', 'asc')->get();
        }

        return view($this->view . '.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, TeacherEnrollService $moodle_teacher_enroll_service)
    {
        // Field Validation
        $request->validate([
            'session' => 'required',
            'program' => 'required',
            'subject' => 'required',
            'teacher' => 'required',
            'room' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        DB::beginTransaction();

        if ($request->subject) {
            $data = $request->except('_token');
            $subject_count = count($data['subject']);
            $day = $request->day;
            $program = $request->program;
            $session = $request->session;
            $semester = Session::query()->find($session)->semester_id;
            $class_routine_ids = [];

            for ($j = 0; $j < $subject_count; $j++) {
                $start = $data['start_time'][$j];
                $end = $data['end_time'][$j];
                // Check Routine
                /*$check = ClassRoutine::where('subject_id', $data['subject'][$j])->where('teacher_id', $data['teacher'][$j])->where('session_id', $session)->where('program_id', $program)->where('semester_id', $semester)->where('section_id', $section)
                ->where('room_id', $data['room'][$j])->where('day', $day)
                ->whereBetween('start_time', [$start, $end])
                ->orwhereBetween('end_time', [$start, $end])
                ->first();*/

                //Teacher Check
                $teacher_check = ClassRoutine::where('teacher_id', $data['teacher'][$j])
                    ->where('session_id', $session)
                    ->where('start_time', $start)
                    ->where('day', $day)
                    ->first();

                //Room Check
                $room_check = ClassRoutine::where('room_id', $data['room'][$j])
                    ->where('session_id', $session)
                    ->where('start_time', $start)
                    ->where('day', $day)
                    ->first();

                //Period Check
                $period_check = ClassRoutine::where('session_id', $session)
                    ->where('program_id', $program)
                    ->where('semester_id', $semester)
                    ->where('start_time', $start)
                    ->where('day', $day)
                    ->first();

                //Subject Check
                $subject_check = ClassRoutine::where('subject_id', $data['subject'][$j])
                    ->where('session_id', $session)
                    ->where('teacher_id', $data['teacher'][$j])
                    ->where('program_id', $program)

                    ->where('start_time', $start)
                    ->where('day', $day)
                    ->first();

                if (!empty($data['routine_id'][$j])) {
                    // Update Routine
                    $classRoutine = ClassRoutine::find($data['routine_id'][$j]);
                    ClassRoutine::query()->where([
                        ['subject_id', $classRoutine->subject_id],
                        ['day', $classRoutine->day],
                        ['teacher_id', $classRoutine->teacher_id],
                        ['room_id', $classRoutine->room_id],
                        ['session_id', $classRoutine->session_id],
                        ['semester_id', $classRoutine->semester_id],
                        ['start_time', $classRoutine->start_time],
                        ['end_time', $classRoutine->end_time],
                    ])->update([
                                'subject_id' => $data['subject'][$j],
                                'teacher_id' => $data['teacher'][$j],
                                'room_id' => $data['room'][$j],
                                'session_id' => $session,
                                'semester_id' => $semester,
                                'start_time' => $data['start_time'][$j],
                                'end_time' => $data['end_time'][$j],
                                'day' => $day,
                            ]);

                    $class_routine_ids[] = $classRoutine->id;

                    Toastr::success(__('msg_updated_successfully'), __('msg_success'));
                } else {
                    // Create Routine
                    if (!empty($subject_check)) {
                        Toastr::error(__('msg_data_already_exists'), __('msg_error'));
                    } else {
                        $subject = Subject::query()->with('programs')->find($data['subject'][$j]);
                        foreach ($subject->programs as $subject_program) {
                            $enroll_subject = EnrollSubject::query()->where('program_id', $subject_program->id)
                                ->where('session_id', $session)->whereHas('subjects', function ($query) use ($subject) {
                                    $query->where('id', $subject->id);
                                })->exists();
                            if ($enroll_subject) {
                                $classRoutine = new ClassRoutine;
                                $classRoutine->subject_id = $subject->id;
                                $classRoutine->teacher_id = $data['teacher'][$j];
                                $classRoutine->room_id = $data['room'][$j];
                                $classRoutine->session_id = $session;
                                $classRoutine->program_id = $subject_program->id;
                                $classRoutine->semester_id = $semester;
                                $classRoutine->start_time = $data['start_time'][$j];
                                $classRoutine->end_time = $data['end_time'][$j];
                                $classRoutine->day = $day;
                                $classRoutine->save();
                                $class_routine_ids[] = $classRoutine->id;

                            }
                        }

                        Toastr::success(__('msg_updated_successfully'), __('msg_success'));
                    }

                }
            }

            // Delete Routine
            if (!empty($request->delete_routine) && isset($request->delete_routine)) {
                $delete_routine_count = count($data['delete_routine']);
                for ($i = 0; $i < $delete_routine_count; $i++) {
                    $classRoutine = ClassRoutine::find($data['delete_routine'][$i]);
                    $classRoutine->delete();
                    if(in_array($classRoutine->id, $class_routine_ids)) {
                        unset($class_routine_ids[array_search($classRoutine->id, $class_routine_ids)]);
                    }
                    Toastr::success(__('msg_deleted_successfully'), __('msg_success'));
                }
            }
        }
        $moodle_teacher_enroll_service->sync($class_routine_ids);
        DB::commit();


        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ClassRoutine $classRoutine)
    {
        // Delete Data
        $classRoutine->delete();

        Toastr::success(__('msg_deleted_successfully'), __('msg_success'));

        return redirect()->back();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function teacher(Request $request)
    {
        $data['title'] = trans_choice('module_teacher_routine', 1);
        $data['route'] = $this->route;
        $data['view'] = $this->view;
        $data['path'] = $this->path;
        $data['access'] = $this->access;
        $session = Session::where('status', '1')->where('current', '1')->first();
        $data['rows'] = ClassRoutine::where('status', '1')
            ->where('session_id', $session->id)
            ->where('teacher_id', Auth::id())
            ->orderBy('start_time', 'asc')
            ->get();
        if (Auth::user()->is_admin == 1) {
            // Teacher Filter
            $teachers = User::where('status', '1');
            $teachers->with('roles')->whereHas('roles', function ($query) {
                $query->where('slug', 'teacher');
            });
            $data['teachers'] = $teachers->orderBy('staff_id', 'asc')->get();


            if (!empty($request->teacher) && $request->teacher != Null) {

                $data['selected_staff'] = $request->teacher;


                if (isset($session)) {
                    $data['rows'] = ClassRoutine::where('status', '1')
                        ->where('session_id', $session->id)
                        ->where('teacher_id', $request->teacher)
                        ->orderBy('start_time', 'asc')
                        ->get();
                }
            } else {
                $data['selected_staff'] = Null;
            }
        }

        return view($this->view . '.teacher', $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function print(Request $request)
    {
        //
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;
        $data['path'] = 'print-setting';

        // View
        $data['print'] = PrintSetting::where('slug', 'class-routine')->firstOrFail();

        // Filter Routine
        if (!empty($request->program) && !empty($request->session) && !empty($request->semester) && !empty($request->section)) {

            $routines = ClassRoutine::where('status', '1');

            if (!empty($request->program)) {
                $routines->where('program_id', $request->program);
            }
            if (!empty($request->session)) {
                $routines->where('session_id', $request->session);
            }
            if (!empty($request->semester)) {
                $routines->where('semester_id', $request->semester);
            }
            if (!empty($request->section)) {
                $routines->where('section_id', $request->section);
            }
            $data['rows'] = $routines->orderBy('start_time', 'asc')->get();
        }


        return view($this->view . '.print', $data);
    }
}
