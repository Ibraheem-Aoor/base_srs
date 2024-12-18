<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\UploadFeesProveRequest;
use App\Models\StudentEnroll;
use Illuminate\Http\Request;
use App\Models\FeesCategory;
use App\Models\Student;
use App\Models\Fee;
use App\Models\Session;
use App\Notifications\Admin\ProvePaymentUplaoded;
use App\Traits\FileUploader;
use App\Traits\StudentModuleTrait;
use App\User;
use Auth;
use Illuminate\Support\Facades\Notification;
use Throwable;

class FeesController extends Controller
{
    use FileUploader, StudentModuleTrait;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Module Data
        $this->title = trans_choice('module_fees_report', 1);
        $this->route = 'student.fees';
        $this->view = 'student.fees';
        $this->path = 'fees';
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

        $data['user'] = $user = Student::where('id', Auth::guard('student')->user()->id)->firstOrFail();

        $data['sessions'] = StudentEnroll::where('student_id', $user->id)->groupBy('session_id')->get();
        $data['semesters'] = StudentEnroll::where('student_id', $user->id)->groupBy('semester_id')->get();
        $data['categories'] = FeesCategory::where('status', '1')->orderBy('title', 'asc')->get();

        $session=null;
        if (!empty($request->session) || $request->session != null) {
            $data['selected_session'] = $r_session = $request->session;
            $session = Session::query()->find($r_session);
        } else {
            $data['selected_session']  = '0';
        }

        $category = null;
        if (!empty($request->category) || $request->category != null) {
            $data['selected_category'] = $category = $request->category;
        } else {
            $data['selected_category'] = '0';
        }

        $fees = Fee::with('studentEnroll')->whereHas('studentEnroll', function ($query) use ($user, $session, $category) {
            // Ensure the results are related to the authenticated user
            $query->where('student_id', $user->id);

            // Apply session-related filters if session is set
            if (isset($session)) {
                $query->where(function ($query) use ($session) {
                    $query->where('session_id', $session->id)
                        ->where('semester_id', $session->semester_id)
                        ->orWhereDate('assign_date', $session->start_date ?? null)
                        ->orWhereDate('due_date', $session->end_date ?? null)
                        ->orWhereBetween('pay_date', [$session->start_date ?? null, $session->end_date ?? null]);
                });
            }

            // Apply category-related filter if category is set
            if (isset($category)) {
                $query->where('category_id', $category);
            }

            // Apply status filter
            $query->where('status', '<=', 1);
        });

        // Retrieve and order the data
        $data['rows'] = $fees->orderBy('assign_date', 'desc')->get();

        // Filter Assignment
        $data['auth_student'] = getAuthUser('student');
        return view($this->view . '.index', $data);
    }

    public function store(UploadFeesProveRequest $request)
    {
        try {
            $fee = Fee::query()->with([
                'category:id,title',
                'studentEnroll:id,student_id',
                'studentEnroll.student:id,student_id'
            ])->findOrFail(decrypt($request->validated('fee_id')));

            // Check IF The Fee Payment Status Is Pending
            if ($fee->status == 0 && !isset($fee->prove_file_path)) {
                $fee->prove_file_path = $this->uploadMedia($request, 'receipt', 'student_receipts/' . getAuthUser('student')->id . '/');
                $fee->save();
                $this->sendProvePaymentUploadedNotification($fee);
                return returnSuccess($this->route . '.index');
            }

            return returnError();
        } catch (Throwable $e) {
            dd($e);
            return returnError();
        }
    }

    protected function sendProvePaymentUploadedNotification($fee)
    {
        $all_admins = User::query()->where('is_admin', true)->whereHas('roles', function ($roles) {
            $roles->where('name', 'super admin');
        })->get();
        $data = [
            'id' => $fee->id,
            'title' => __('fee_prove_uploaded'),
            'type' => 'prove_fees_uploaded',
            'student_id' => $fee->studentEnroll?->student?->student_id
        ];
        Notification::send($all_admins, new ProvePaymentUplaoded($data));
    }

}
