<?php

namespace App\Http\Controllers;

use App\Models\ExamTypeCategory;
use Illuminate\Http\Request;
use App\Models\Semester;
use App\Models\Session;
use App\Models\Program;
use App\Models\ProgramSubject;
use App\Models\Subject;
use App\Models\Section;
use Carbon\Carbon;
use App\User;
use Auth;
use DB;
use Throwable;

class FilterController extends Controller
{
    public function filterBatch(Request $request)
    {
        $data = $request->all();

        $rows = Program::where('status', 1);
        $rows->with('batches')->whereHas('batches', function ($query) use ($data) {
            $query->where('batch_id', $data['batch']);
        });
        $programs = $rows->orderBy('title', 'asc')->get();

        return response()->json($programs);
    }

    public function filterProgram(Request $request)
    {
        //
        $data = $request->all();

        $programs = Program::where('faculty_id', $data['faculty'])->where('status', 1)->orderBy('title', 'asc')->get();

        return response()->json($programs);
    }

    public function filterSession(Request $request)
    {
        //
        $data = $request->all();
        $rows = Session::where('status', 1);
        $rows->with('programs')->whereHas('programs', function ($query) use ($data) {
            if (is_array($data['program'])) {
                $query->whereIn('program_id', $data['program']);
            } else {
                $query->where('program_id', $data['program']);
            }
        });
        $sessions = $rows->orderBy('id', 'desc')->get();
        return response()->json($sessions);
    }

    public function filterSemester(Request $request)
    {
        //
        $data = $request->all();

        $rows = Semester::where('status', 1);
        $rows->with('programs')->whereHas('programs', function ($query) use ($data) {
            if (is_array($data['program'])) {
                $query->whereIn('program_id', $data['program']);
            } else {
                $query->where('program_id', $data['program']);
            }
        });
        $semesters = $rows->orderBy('id', 'asc')->get();

        return response()->json($semesters);
    }

    public function filterSection(Request $request)
    {
        //
        $data = $request->all();

        $rows = Section::where('status', 1);
        $rows->with('semesterPrograms')->whereHas('semesterPrograms', function ($query) use ($data) {
            $query->where('program_id', $data['program']);
            $query->where('semester_id', $data['semester']);
        });
        $sections = $rows->orderBy('title', 'asc')->get();

        return response()->json($sections);
    }

    public function filterSubject(Request $request)
    {
        //
        $data = $request->all();

        $rows = Subject::where('status', 1);
        $rows->with('programs')->whereHas('programs', function ($query) use ($data) {
            $query->where('program_id', $data['program']);
        });
        $subjects = $rows->orderBy('code', 'asc')->get();

        return response()->json($subjects);
    }

    public function filterEnrollSubject(Request $request)
    {
        $data = $request->all();
        $rows = Subject::where('status', 1);
        $rows->with('subjectEnrolls')->whereHas('subjectEnrolls', function ($query) use ($data) {
            $query->when(@$data['program'], function ($subject) use ($data) {
                $subject->where('program_id', @$data['program']);
            });
            $query->when(@$data['semester'], function ($subject) use ($data) {
                $subject->where('semester_id', @$data['semester']);
            });
            $query->when(@$data['session'], function ($subject) use ($data) {
                $subject->where('session_id', @$data['session']);
            });
            $query->when(@$data['section_id'], function ($subject) use ($data) {
                $subject->where('section_id', @$data['section']);
            });
        });
        $subjects = $rows->orderBy('code', 'asc')->get();

        return response()->json($subjects);
    }

    public function filterStudentSubject(Request $request)
    {
        //
        $data = $request->all();

        $subjects = DB::table('subjects')->select('subjects.*')->join('student_enroll_subject', 'student_enroll_subject.subject_id', 'subjects.id')->join('student_enrolls', 'student_enrolls.id', 'student_enroll_subject.student_enroll_id')->where('student_enrolls.program_id', $data['program'])->where('student_enrolls.session_id', $data['session'])->where('student_enrolls.semester_id', $data['semester'])->where('student_enrolls.section_id', $data['section'])->where('student_enrolls.status', '1')->where('subjects.status', '1')->orderBy('subjects.code', 'asc')->get();

        return response()->json($subjects);
    }

    public function filterTecherSubject(Request $request)
    {
        //
        $data = $request->all();

        // Access Data
        $session = $data['session'];

        $teacher_id = Auth::guard('web')->user()->id;
        $user = User::where('id', $teacher_id)->where('status', '1');
        $user->with('roles')->whereHas('roles', function ($query) {
            $query->where('slug', 'super-admin');
        });
        $superAdmin = $user->first();


        // Filter Subject
        $rows = Subject::where('status', '1');
        $rows->with('classes')->whereHas('classes', function ($query) use ($teacher_id, $session, $superAdmin) {
            if (isset ($session)) {
                $query->where('session_id', $session);
            }
            if (!isset ($superAdmin)) {
                $query->where('teacher_id', $teacher_id);
            }
        });
        if (isset($data['program'])) {
            $rows->with('programs')->whereHas('programs', function ($query) use ($data) {
                $query->where('program_id', $data['program']);
            });
        }
        $subjects = $rows->orderBy('code', 'asc')->get();

        return response()->json($subjects);
    }


    /**
     * Filter the Mark Distribution Category Types.
     *
     * @param Request $request The request data
     * @throws Throwable description of exception
     * @return JsonResponse
     */
    public function filterMarkDistribitionCategoryTypes(Request $request)
    {
        try {
            $mark_category = ExamTypeCategory::query()->findOrFail($request->mark_category_id);
            return response()->json($mark_category->examsTypes);
        } catch (Throwable $e) {
            info('ERROR IN ' . __METHOD__);
            info($e);
            return response()->json([]);
        }
    }
    /**
     * Filter the Mark Distribution Category Types.
     *
     * @param Request $request The request data
     * @throws Throwable description of exception
     * @return JsonResponse
     */
    public function filterMarkDistribitionCategoryTypesBySubjectAndProgram(Request $request)
    {
        try {
            $progam_subject = ProgramSubject::query()
                ->whereSubjectId($request->subject_id)
                ->whereProgramId($request->program_id)
                ->with([
                    'examTypeCategory:id',
                    'examTypeCategory.examsTypes' => function ($examsTypes) {
                        $examsTypes->status(1);
                    }
                ])
                ->first();
            $data = $progam_subject->examTypeCategory?->examsTypes ?? collect([]);
            return $request->wantsJson() ?  response()->json($data) : $data;
        } catch (Throwable $e) {
            info('ERROR IN ' . __METHOD__);
            info($e);
            return response()->json([]);
        }
    }
}
