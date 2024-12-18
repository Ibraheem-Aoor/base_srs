<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentEnroll;
use App\Rules\SubjectMustBeOfferedWithEnrollmentSession;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Grade;
use App\Models\Session;
use App\Services\Moodle\StudentEnrollService;
use Toastr;
use Auth;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class SubjectAddDropController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Module Data
        $this->title = trans_choice('module_subject_adddrop', 1);
        $this->route = 'admin.subject-adddrop';
        $this->view = 'admin.subject-adddrop';
        $this->path = 'student';
        $this->access = 'student-enroll';


        $this->middleware('permission:' . $this->access . '-adddrop');
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


        $data['students'] = Student::whereHas('currentEnroll')->where('status', '1')->orderBy('student_id', 'asc')->get();

        if (!empty($request->student) && $request->student != Null) {

            $data['selected_student'] = $request->student;

            // Student
            $student = Student::where('student_id', $request->student)->where('status', '1');
            $student->with('currentEnroll')->whereHas('currentEnroll', function ($query) {
                $query->where('status', '1');
            });
            $data['row'] = $row = $student->first();
            if(!isset($row))
            {
                Toastr::error(__('std_no_enroll_with_curr_session'), __('msg_error'));
                return back();
            }

            // Subjects
            $subjects = Subject::where('status', '1');
            $current_session =  Session::query()->where('current', 1)->first();
            $subjects->with('subjectEnrolls')->whereHas('subjectEnrolls', function ($query) use ($row, $current_session) {
                $query->where('program_id', $row?->program_id)->where('session_id', $current_session->id);
            });
            $data['subjects'] = $subjects->orderBy('code', 'asc')->get();


            // Current Enroll
            $data['curr_enr'] = StudentEnroll::where('student_id', $row?->id)
                ->where('status', '1')
                ->orderBy('id', 'desc')->first();

            $data['grades'] = Grade::where('status', '1')->orderBy('min_mark', 'desc')->get();
        } else {
            $data['selected_student'] = Null;
        }

        return view($this->view . '.index', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, StudentEnrollService $moodle_student_enroll_service)
    {
        // Field Validation
        $request->validate([
            'student' => 'required',
            'subjects' => ['required', new SubjectMustBeOfferedWithEnrollmentSession($request->student)],
        ]);


        $requested_subjects = $request->subjects;

        // Enroll Update
        $enroll = StudentEnroll::where('student_id', $request->student)
            ->where('status', '1')
            ->orderBy('id', 'desc')->first();
        try {
            DB::beginTransaction();
            $moodle_student_enroll_service->sync($enroll, $requested_subjects);
            $enroll->subjects()->sync($request->subjects);
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            logError(e: $e, method: __METHOD__, class: get_class($this));
            return back();
        }
        Toastr::success(__('msg_updated_successfully'), __('msg_success'));

        return redirect()->back();
    }
}
