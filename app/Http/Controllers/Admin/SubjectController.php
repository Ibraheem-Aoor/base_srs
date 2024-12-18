<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ClassTypeEnum;
use App\Enums\SubjectTypeEnum;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SubjectsImport;
use App\Models\ExamType;
use App\Models\ExamTypeCategory;
use Illuminate\Http\Request;
use App\Models\Program;
use App\Models\Subject;
use App\Models\Faculty;
use App\Models\Prerequisit;
use App\Services\Moodle\CourseService;
use Toastr;
use Illuminate\Support\Facades\DB;
use Throwable;

class SubjectController extends Controller
{
    public $mark_distribution_systems;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Module Data
        $this->title = trans_choice('module_subject', 1);
        $this->route = 'admin.subject';
        $this->view = 'admin.subject';
        $this->path = 'subject';
        $this->access = 'subject';
        $this->mark_distribution_systems = ExamTypeCategory::query()
            ->status(1)
            ->orderByDesc('created_at')
            ->get(['id', 'title']);

        $this->middleware('permission:' . $this->access . '-view|' . $this->access . '-create|' . $this->access . '-edit|' . $this->access . '-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:' . $this->access . '-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:' . $this->access . '-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:' . $this->access . '-delete', ['only' => ['destroy']]);
        $this->middleware('permission:' . $this->access . '-import', ['only' => ['index', 'import', 'importStore']]);
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

        if (!empty($request->subject_type) || $request->subject_type != null) {
            $data['selected_subject_type'] = $subject_type = $request->subject_type;
        } else {
            $data['selected_subject_type'] = Null;
        }

        if (!empty($request->class_type) || $request->class_type != null) {
            $data['selected_class_type'] = $class_type = $request->class_type;
        } else {
            $data['selected_class_type'] = Null;
        }


        $data['faculties'] = Faculty::where('status', '1')->orderBy('title', 'asc')->get();
        if (!empty($request->faculty) || $request->faculty != '0') {
            $data['programs'] = Program::where('faculty_id', $request->faculty)->where('status', '1')->orderBy('title', 'asc')->get();
        }


        // Subject Search
        $subject = Subject::where('id', '!=', null);

        if (!empty($request->faculty) && $request->faculty != '0') {
            $subject->with('programs.faculty')->whereHas('programs.faculty', function ($query) use ($faculty) {
                $query->where('id', $faculty);
            });
        }
        if (!empty($request->program) && $request->program != '0') {
            $subject->with('programs')->whereHas('programs', function ($query) use ($program) {
                $query->where('id', $program);
            });
        }
        if (!empty($request->subject_type)) {
            if ($subject_type == 2) {
                $subject_type = 0;
            }
            $subject->where('subject_type', $subject_type);
        }
        if (!empty($request->class_type)) {
            $subject->where('class_type', $class_type);
        }

        $data['rows'] = $subject->orderBy('title', 'asc')->get();

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
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;

        $data['faculties'] = Faculty::where('status', '1')->orderBy('title', 'asc')->get();

        $data['courses'] = Subject::query()->status(1)->orderByDesc('created_at')->get(['id', 'title']);

        $data['class_types'] = ClassTypeEnum::getValues();
        $data['mark_distribution_systems'] = $this->mark_distribution_systems;
        return view($this->view . '.create', $data);
    }

    /**
     * Attach Programs To Each Subejct
     */
    public function attachPrograms(Subject $subject, Request $request)
    {
        $programs_to_sync = [];
        foreach ($request->programs as $program_id => $attributes) {
            if (isset($attributes['is_checked'])) {
                $programs_to_sync[$program_id] = [
                    'exam_type_category_id' => $request->exam_type,
                ];
            }
        }
        $subject->programs()->sync($programs_to_sync);
    }
    /**
     * Attach Prequisites To Each Subejct
     */
    public function attachPrerequisites(Subject $subject, Request $request)
    {
        foreach ($request->prerequisites as $prerequisite) {
            Prerequisit::create([
                'subject_id' => $subject->id,
                'prerequisit_id' => $prerequisite['prerequisit_id'],
                'type' => $prerequisite['type'],
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Field Validation
        $request->validate([
            'title' => 'required|max:191|unique:subjects,title',
            'code' => 'required|max:191|unique:subjects,code',
            'credit_hour' => 'required|numeric',
            'prerequisites' => 'nullable|array',
            'prerequisites.*' => 'required|array',
            'prerequisites.*.*' => 'required',
            'programs' => 'required|array',
            'programs.*' => 'required',
            'exam_type' =>'required|exists:exam_type_categories,id',
        ], [
            'prerequisites.array' => __('invalid_prerequisites'),
            'prerequisites.*.array' => __('invalid_prerequisites'),
            'prerequisites.*.required' => __('required_prerequisites'),
            'prerequisites.*.*.required' => __('required_prerequisites'),
            'programs.array' => __('field_program_invalid'),
            'programs.*.array' => __('field_program_invalid'),
        ]);
        try {
            DB::beginTransaction();
            // Insert Data
            $subject = new Subject;
            $subject->title = $request->title;
            $subject->code = $request->code;
            $subject->credit_hour = $request->credit_hour;
            $subject->total_marks = $request->total_marks;
            $subject->passing_marks = $request->passing_marks;
            $subject->description = $request->description;
            $subject->status = $request->status;
            $subject->save();

            // Attach Programs
            $this->attachPrograms(subject: $subject, request: $request);

            // Preqrequisites
            if (is_array($request->prerequisites)) {
                $this->attachPrerequisites(subject: $subject, request: $request);
            }
            $subject->save();
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            logError(e: $e, method: __METHOD__, class: get_class($this));
        }

        Toastr::success(__('msg_created_successfully'), __('msg_success'));

        return redirect()->route($this->route . '.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Subject $subject)
    {
        //
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;
        $data['path'] = $this->path;

        $data['row'] = $subject;

        return view($this->view . '.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Subject $subject, CourseService $course_service)
    {
        try {

            //
            $data['title'] = $this->title;
            $data['route'] = $this->route;
            $data['view'] = $this->view;
            $data['path'] = $this->path;
            $data['row'] = $subject;
            $data['faculties'] = Faculty::where('status', '1')->orderBy('title', 'asc')->get();
            $data['courses'] = Subject::query()->status(1)->where('id', '!=', $subject->id)->orderByDesc('created_at')->get(['id', 'title']);
            $data['mark_distribution_systems'] = $this->mark_distribution_systems;
            return view($this->view . '.edit', $data);
        } catch (Throwable $e) {
            logError(e: $e, method: __METHOD__, class: get_class($this));
            return back()->with('erro', __('msg_error'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Subject $subject, CourseService $course_service)
    {

        // Field Validation
        $request->validate([
            'title' => 'required|max:191|unique:subjects,title,' . $subject->id,
            'code' => 'required|max:191|unique:subjects,code,' . $subject->id,
            'credit_hour' => 'required|numeric',
            'prerequisites' => 'nullable|array',
            'prerequisites.*' => 'required|array',
            'prerequisites.*.*' => 'required',
            'programs' => 'required|array',
            'programs.*' => 'required',
            'exam_type' => 'required|exists:exam_type_categories,id',


        ], [
            'prerequisites.array' => __('invalid_prerequisites'),
            'prerequisites.*.array' => __('invalid_prerequisites'),
            'prerequisites.*.required' => __('required_prerequisites'),
            'prerequisites.*.*.required' => __('required_prerequisites'),
            'programs.array' => __('field_program_invalid'),
            'programs.*.array' => __('field_program_invalid')
        ]);

        try {
            DB::beginTransaction();
            // Update Data
            $subject->title = $request->title;
            $subject->code = $request->code;
            $subject->credit_hour = $request->credit_hour;
            $subject->total_marks = $request->total_marks;
            $subject->passing_marks = $request->passing_marks;
            $subject->description = $request->description;
            $subject->status = $request->status;
            $subject->save();
            $subjectMoodleData = MoodleSubjectSession::query()->where('subject_id', $subject->id)->where('session_id', '<>', ' ')->get();
            foreach ($subjectMoodleData as $data) {
                if ($data->session_id) {
                    $session_data = Session::query()->where('id', $data->session_id)->first();
                    $update_subject_on_moodle = $course_service->editSubjectNameForSession($subject, $session_data, $data->id_on_moodle);
                }
            }
            // Attach Update
            $this->attachPrograms(subject: $subject, request: $request);
            // Preqrequisites
            $subject->prerequisites()->delete();
            if (is_array($request->prerequisites)) {
                $this->attachPrerequisites(subject: $subject, request: $request);
            }

            DB::commit();
        } catch (Throwable $e) {
            dd($e);
            DB::rollBack();
            logError(e: $e, method: __METHOD__, class: get_class($this));
        }

        Toastr::success(__('msg_updated_successfully'), __('msg_success'));

        return redirect()->route('admin.subject.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subject $subject)
    {
        DB::beginTransaction();
        // Detach
        $subject->programs()->detach();

        // Delete Data
        $subject->delete();
        DB::commit();

        Toastr::success(__('msg_deleted_successfully'), __('msg_success'));

        return redirect()->back();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        //
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;
        $data['access'] = $this->access;

        return view($this->view . '.import', $data);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function importStore(Request $request)
    {
        // Field Validation
        $request->validate([
            'import' => 'required|file|mimes:xlsx',
        ]);


        // Passing Data
        Excel::import(new SubjectsImport, $request->file('import'));


        Toastr::success(__('msg_updated_successfully'), __('msg_success'));

        return redirect()->back();
    }
}
