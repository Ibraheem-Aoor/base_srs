<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ApplicationSetting;
use Illuminate\Http\Request;
use App\Traits\FileUploader;
use App\Models\Application;
use App\Models\Province;
use App\Models\Program;
use Carbon\Carbon;
use Toastr;
use DB;

class ApplicationController extends Controller
{
    use FileUploader;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Module Data
        $this->title = trans_choice('module_application', 1);
        $this->route = 'application';
        $this->view = 'admin.application';
        $this->path = 'student';
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


        $data['programs'] = Program::where('status', '1')->orderBy('title', 'asc')->get();
        $data['provinces'] = Province::where('status', '1')->orderBy('title', 'asc')->get();
        $data['applicationSetting'] = ApplicationSetting::where('slug', 'admission')->where('status', '1')->firstOrFail();
        return view($this->view.'.create', $data);
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
            'program' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:applications,email',
            'phone' => 'required',
            'gender' => 'required',
            'dob' => 'required|date',
            'photo' => 'nullable|image',
            'signature' => 'nullable|image',
            'grade_transcripts' => 'required|file|mimes:jpg,jpeg,png,gif,ico,svg,webp,pdf,doc,docx,txt,zip,rar,csv,xls,xlsx,ppt,pptx,mp3,avi,mp4,mpeg,3gp',
            'previous_certificates' => 'required|file|mimes:jpg,jpeg,png,gif,ico,svg,webp,pdf,doc,docx,txt,zip,rar,csv,xls,xlsx,ppt,pptx,mp3,avi,mp4,mpeg,3gp',
        ]);


        // Insert Data
        try{
            DB::beginTransaction();

            $student = new Application;
            $student->program_id = $request->program;
            $student->apply_date = Carbon::today();

            $student->first_name = $request->first_name;
            $student->last_name = $request->last_name;
            $student->father_name = $request->father_name;
            $student->mother_name = $request->mother_name;
            $student->father_occupation = $request->father_occupation;
            $student->mother_occupation = $request->mother_occupation;

            $student->country = $request->country;
            $student->present_province = $request->present_province;
            $student->present_district = $request->present_district;
            $student->present_village = $request->present_village;
            $student->present_address = $request->present_address;
            $student->permanent_province = $request->permanent_province;
            $student->permanent_district = $request->permanent_district;
            $student->permanent_village = $request->permanent_village;
            $student->permanent_address = $request->permanent_address;

            $student->gender = $request->gender;
            $student->dob = $request->dob;
            $student->email = $request->email;
            $student->phone = $request->phone;
            $student->emergency_phone = $request->emergency_phone;

            $student->religion = $request->religion;
            $student->caste = $request->caste;
            $student->mother_tongue = $request->mother_tongue;
            $student->marital_status = $request->marital_status;
            $student->blood_group = $request->blood_group;
            $student->nationality = $request->nationality;
            $student->national_id = $request->national_id;
            $student->passport_no = $request->passport_no;

            $student->school_name = $request->school_name;
            $student->school_exam_id = $request->school_exam_id;
            $student->school_graduation_year = $request->school_graduation_year;
            $student->school_graduation_point = $request->school_graduation_point;
            $student->collage_name = $request->collage_name;
            $student->collage_exam_id = $request->collage_exam_id;
            $student->collage_graduation_year = $request->collage_graduation_year;
            $student->collage_graduation_point = $request->collage_graduation_point;
            $student->photo = $this->uploadImage($request, 'photo', $this->path, 300, 300);
            $student->signature = $this->uploadImage($request, 'signature', $this->path, 300, 100);
            $student->grade_transcripts = $this->uploadMedia($request, 'grade_transcripts', 'applications/grade_transcripts');
            $student->previous_certificates = $this->uploadMedia($request, 'previous_certificates', 'applications/previous_certificates');
            $student->proof_master_in_en = $this->uploadMedia($request, 'proof_master_in_en', 'applications/proof_master_in_en');
            $student->id_or_passport = $this->uploadMedia($request, 'id_or_passport', 'applications/id_or_passport');
            $student->letter_of_intrest = $this->uploadMedia($request, 'letter_of_intrest', 'applications/letter_of_intrest');
            $student->certificate_of_recommendation = $this->uploadMedia($request, 'certificate_of_recommendation', 'applications/certificate_of_recommendation');
            $student->english_certificate = $this->uploadMedia($request, 'english_certificate', 'applications/english_certificate');
            $student->status = '1';
            $student->save();

            $student->registration_no = intval(10000000) + $student->id;
            $student->save();

            DB::commit();


            Toastr::success(__('msg_sent_successfully'), __('msg_success'));

            return redirect()->route($this->route.'.index')->with('success', __('msg_sent_successfully'));
        }
        catch(\Exception $e){

            Toastr::error(__('msg_created_error'), __('msg_error'));

            return redirect()->back();
        }
    }
}
