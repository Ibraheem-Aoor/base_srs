<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FilterController;
use App\Traits\ExamModuleTrait;
use Illuminate\Http\Request;
use App\Models\ExamType;
use App\Models\Semester;
use App\Models\Faculty;
use App\Models\Program;
use App\Models\Session;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Grade;
use App\Models\Exam;
use App\Models\ExamTypeCategory;
use App\Models\StudentEnroll;
use App\Models\SubjectMarking;
use App\User;
use Toastr;
use Auth;
use Throwable;

class ExamMarkingController extends Controller
{
    use ExamModuleTrait;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Module Data
        $this->title = trans_choice('module_exam_marking', 1);
        $this->route = 'admin.exam-marking';
        $this->view = 'admin.exam';
        $this->path = 'exam';
        $this->access = 'exam';

        $this->middleware('permission:' . $this->access . '-marking', ['only' => ['index', 'store']]);
        $this->middleware('permission:' . $this->access . '-result', ['only' => ['result']]);
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
            $data['selected_faculty'] = null;
        }

        if (!empty($request->program) || $request->program != null) {
            $data['selected_program'] = $program = $request->program;
        } else {
            $data['selected_program'] = null;
        }

        if (!empty($request->session) || $request->session != null) {
            $data['selected_session'] = $session = $request->session;
        } else {
            $data['selected_session'] = null;
        }

        if (!empty($request->semester) || $request->semester != null) {
            $data['selected_semester'] = $semester = $request->semester;
        } else {
            $data['selected_semester'] = null;
        }

        if (!empty($request->section) || $request->section != null) {
            $data['selected_section'] = $section = $request->section;
        } else {
            $data['selected_section'] = null;
        }

        if (!empty($request->subject) || $request->subject != null) {
            $data['selected_subject'] = $subject = $request->subject;
        } else {
            $data['selected_subject'] = null;
        }

        if (!empty($request->type) || $request->type != null) {
            $data['selected_type'] = $type = $request->type;
            $data['types'] = ExamType::query()->find($type)->sibilings();
        } else {
            $data['selected_type'] = null;
        }

        // Filter Search
        $data['faculties'] = Faculty::where('status', '1')->orderBy('title', 'asc')->get();

        if (!empty($request->faculty) && $request->faculty != '0') {
            $data['programs'] = Program::where('faculty_id', $faculty)->where('status', '1')->orderBy('title', 'asc')->get();
        }

        if (!empty($request->program) && $request->program != '0') {
            $sessions = Session::where('status', 1);
            $sessions->with('programs')->whereHas('programs', function ($query) use ($program) {
                $query->where('program_id', $program);
            });
            $data['sessions'] = $sessions->orderBy('id', 'desc')->get();
        }

        if (!empty($request->program) && $request->program != '0') {
            $semesters = Semester::where('status', 1);
            $semesters->with('programs')->whereHas('programs', function ($query) use ($program) {
                $query->where('program_id', $program);
            });
            $data['semesters'] = $semesters->orderBy('id', 'asc')->get();
        }

        if (!empty($request->program) && $request->program != '0' && !empty($request->semester) && $request->semester != '0') {
            $sections = Section::where('status', 1);
            $sections->with('semesterPrograms')->whereHas('semesterPrograms', function ($query) use ($program, $semester) {
                $query->where('program_id', $program);
                $query->where('semester_id', $semester);
            });
            $data['sections'] = $sections->orderBy('title', 'asc')->get();
        }

        if (!empty($request->program) && $request->program != '0' && !empty($request->session) && $request->session != '0') {
            // Access Data
            $teacher_id = Auth::guard('web')->user()->id;
            $user = User::where('id', $teacher_id)->where('status', '1');
            $user->with('roles')->whereHas('roles', function ($query) {
                $query->where('slug', 'super-admin');
            });
            $superAdmin = $user->first();

            // Filter Subject
            $subjects = Subject::where('status', '1');
            $subjects->with('classes')->whereHas('classes', function ($query) use ($teacher_id, $session, $superAdmin) {
                if (isset($session)) {
                    $query->where('session_id', $session);
                }
                if (!isset($superAdmin)) {
                    $query->where('teacher_id', $teacher_id);
                }
            });
            $subjects->with('programs')->whereHas('programs', function ($query) use ($program) {
                $query->where('program_id', $program);
            });
            $data['subjects'] = $subjects->orderBy('code', 'asc')->get();
        }
        try {
            // Exam Marking
            if (!empty($request->program) && !empty($request->session) && !empty($request->subject)) {

                // Check Subject Access
                $subject_check = Subject::where('id', $subject);
                $subject_check->with('classes')->whereHas('classes', function ($query) use ($teacher_id, $session, $superAdmin) {
                    if (isset($session)) {
                        $query->where('session_id', $session);
                    }
                    if (!isset($superAdmin)) {
                        $query->where('teacher_id', $teacher_id);
                    }
                })->firstOrFail();
                // User Enrollments For Easy Data Retrieval
                $exam_types = $this->getExamTypes($request, new FilterController())->pluck('title', 'id')->toArray();
                // Enrollments
                $enrollments = StudentEnroll::query()
                    ->where('program_id', $request->program)
                    ->where('session_id', $request->session)
                ->with('subjects' , 'exams')->whereHas('subjects' , function($q)use($subject){
                    $q->where('id' , $subject);
                });
                $rows = $enrollments->get();
                $exams = Exam::query()->whereIn('student_enroll_id' , array_values($enrollments->pluck('id')->toArray()))->where('subject_id', $request->subject)->get();
                if ($rows->isEmpty() || $exams->isEmpty()) {
                    return $this->createEmptyExamsForStudents($request, $exam_types);
                }
                // Array Sorting
                $data['rows'] = $rows;
            }
            return view($this->view . '.marking', $data);
        } catch (Throwable $e) {
            logError(e: $e, method: __METHOD__, class: get_class($this));
            return back();
        }

    }


    /**
     * Create Exams TO Enable GUI Marking Input.
     */

    protected function createEmptyExamsForStudents(Request $request, $exam_types)
    {
        $exam_types = ExamType::query()->whereIn('id', array_keys($exam_types))->get();
        $enrollments = StudentEnroll::query()->where('program_id', $request->program)
            ->where('session_id', $request->session)
            ->whereHas('subjects', function ($subjects) use ($request) {
                $subjects->where('id', $request->subject);
            })->get();

        foreach ($enrollments as $enrollment) {
            foreach ($exam_types as $exam_type) {
                Exam::create([
                    'student_enroll_id' => $enrollment->id,
                    'subject_id' => $request->subject,
                    'exam_type_id' => $exam_type->id,
                    'date' => now()->toDateString(),
                    'marks' => $exam_type->marks, //max marks allowed
                    'contribution' => $exam_type->contribution,
                    'attendance' => 1,
                    'achieve_marks' => null, // we made this because of the data format after validation.
                    // 'note' => $row['note'],
                    'created_by' => Auth::guard('web')->user()->id,
                ]);
            }
        }
        return $this->index($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validation_rules = [
            'marks' => 'required',
        ];
        foreach ($request->marks as $key => $value) {
            $validation_rules['marks.' . $key] = 'numeric|lte:' . Exam::query()?->find($key)?->type?->marks;
        }
        // Field Validation
        $request->validate($validation_rules);

        // Update Data
        foreach ($request->marks as $exam_id => $marks) {

            if ($marks == null || $marks == '') {
                $exam_mark = null;
            } else {
                $exam_mark = $marks;
            }
            $exam = Exam::find($exam_id);
            $exam->achieve_marks = $exam_mark;
            // $exam->note = $request->notes[$key];
            $exam->updated_by = Auth::guard('web')->user()->id;
            $exam->save();
            SubjectMarking::query()->updateOrCreate(
                [
                    'student_enroll_id' => $exam->student_enroll_id,
                    'subject_id' => $exam->subject_id
                ],
                [
                    'student_enroll_id' => $exam->student_enroll_id,
                    'subject_id' => $exam->subject_id,
                    'total_marks' => Exam::query()->where('student_enroll_id', $exam->student_enroll_id)->where('subject_id', $exam->subject_id)->sum('achieve_marks'),
                ]
            );
        }


        Toastr::success(__('msg_updated_successfully'), __('msg_success'));

        return redirect()->back();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function result(Request $request)
    {
        //
        $data['title'] = trans_choice('module_exam_result', 1);
        $data['route'] = $this->route;
        $data['view'] = $this->view;
        $data['path'] = $this->path;


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
        $data['selected_semester'] = 0;


        $data['selected_section'] = '0';

        if (!empty($request->subject) || $request->subject != null) {
            $data['selected_subject'] = $subject = $request->subject;
        } else {
            $data['selected_subject'] = '0';
        }


        if (!empty($request->type) || $request->type != null) {
            $data['selected_type'] = $type = $request->type;
            $data['types'] = ExamType::query()->find($type)->sibilings();
        } else {
            $data['selected_type'] = null;
        }



        // Filter Search
        $data['faculties'] = Faculty::where('status', '1')->orderBy('title', 'asc')->get();
        $data['types'] = ExamType::where('status', '1')->orderBy('title', 'asc')->get();
        $data['grades'] = Grade::where('status', '1')->orderBy('min_mark', 'desc')->get();

        if (!empty($request->faculty) && $request->faculty != '0') {
            $data['programs'] = Program::where('faculty_id', $faculty)->where('status', '1')->orderBy('title', 'asc')->get();
        }

        if (!empty($request->program) && $request->program != '0') {
            $sessions = Session::where('status', 1);
            $sessions->with('programs')->whereHas('programs', function ($query) use ($program) {
                $query->where('program_id', $program);
            });
            $data['sessions'] = $sessions->orderBy('id', 'desc')->get();
        }

        if (!empty($request->program) && $request->program != '0') {
            $semesters = Semester::where('status', 1);
            $semesters->with('programs')->whereHas('programs', function ($query) use ($program) {
                $query->where('program_id', $program);
            });
            $data['semesters'] = $semesters->orderBy('id', 'asc')->get();
        }

        // if (!empty($request->program) && $request->program != '0' && !empty($request->semester) && $request->semester != '0') {
        //     $sections = Section::where('status', 1);
        //     $sections->with('semesterPrograms')->whereHas('semesterPrograms', function ($query) use ($program, $semester) {
        //         $query->where('program_id', $program);
        //         $query->where('semester_id', $semester);
        //     });
        //     $data['sections'] = $sections->orderBy('title', 'asc')->get();
        // }

        if (!empty($request->program) && $request->program != '0' && !empty($request->session) && $request->session != '0') {
            // Access Data
            $teacher_id = Auth::guard('web')->user()->id;
            $user = User::where('id', $teacher_id)->where('status', '1');
            $user->with('roles')->whereHas('roles', function ($query) {
                $query->where('slug', 'super-admin');
            });
            $superAdmin = $user->first();

            // Filter Subject
            $subjects = Subject::where('status', '1');
            $subjects->with('classes')->whereHas('classes', function ($query) use ($teacher_id, $session, $superAdmin) {
                if (isset($session)) {
                    $query->where('session_id', $session);
                }
                if (!isset($superAdmin)) {
                    $query->where('teacher_id', $teacher_id);
                }
            });
            $subjects->with('programs')->whereHas('programs', function ($query) use ($program) {
                $query->where('program_id', $program);
            });
            $data['subjects'] = $subjects->orderBy('code', 'asc')->get();
        }


        // Exam Result
        if (!empty($request->program) && !empty($request->session) && !empty($request->subject) && !empty($request->type)) {

            // Check Subject Access
            $subject_check = Subject::where('id', $subject);
            $subject_check->with('classes')->whereHas('classes', function ($query) use ($teacher_id, $session, $superAdmin) {
                if (isset($session)) {
                    $query->where('session_id', $session);
                }
                if (!isset($superAdmin)) {
                    $query->where('teacher_id', $teacher_id);
                }
            })->firstOrFail();


            // Exams
            $exams = Exam::where('id', '!=', null);

            if (!empty($request->program) && !empty($request->session)) {

                $exams->with('studentEnroll')->whereHas('studentEnroll', function ($query) use ($program, $session) {
                    if ($program != '0') {
                        $query->where('program_id', $program);
                    }
                    if ($session != '0') {
                        $query->where('session_id', $session)->where('semester_id', Session::query()->find($session)->semester_id);
                    }
                });
            }
            if (!empty($request->subject) && $request->subject != '0') {
                $exams->where('subject_id', $subject);
            }
            if (!empty($request->type) && $request->type != '0') {
                $exams->where('exam_type_id', $type);
            }
            $exams->with('studentEnroll.student')->whereHas('studentEnroll.student', function ($query) {
                $query->orderBy('student_id', 'asc');
            });

            $rows = $exams->get();

            // Array Sorting
            $data['rows'] = $rows->sortBy(function ($query) {

                return $query->studentEnroll->student->student_id;

            })->all();
        }

        return view($this->view . '.result', $data);
    }
}
