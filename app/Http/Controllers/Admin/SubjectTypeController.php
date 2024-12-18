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
use App\Models\SubjectType;
use Toastr;
use DB;

class SubjectTypeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Module Data
        $this->title = trans_choice('module_subject_type', 1);
        $this->route = 'admin.subject-type';
        $this->view = 'admin.subject_type';
        $this->path = 'subject type';
        $this->access = 'subject';
        $this->subject_types = SubjectTypeEnum::getValues();

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
        $data['rows'] = SubjectType::query()->latest()->get(['id', 'title', 'status']);
        return view($this->view . '.index', $data);
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
            'title' => 'required|max:191|unique:subject_types,title',
        ]);


        DB::beginTransaction();
        // Insert Data
        SubjectType::query()->create([
            'title' => $request->title,
        ]);
        DB::commit();

        Toastr::success(__('msg_created_successfully'), __('msg_success'));

        return redirect()->route($this->route . '.index');
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Subject $subject)
    {
        //
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;
        $data['path'] = $this->path;

        $data['row'] = $subject;
        $data['faculties'] = Faculty::where('status', '1')->orderBy('title', 'asc')->get();
        $data['courses'] = Subject::query()->status(1)->where('id', '!=', $subject->id)->orderByDesc('created_at')->get(['id', 'title']);
        $data['mark_distribution_systems'] = $this->mark_distribution_systems;
        $data['subject_types'] = $this->subject_types;

        return view($this->view . '.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SubjectType $subject_type)
    {
        // Field Validation
        $request->validate([
            'title' => 'required|max:191|unique:subject_types,title,'.$subject_type->id,
        ]);

        DB::beginTransaction();
        // Update Data
        $subject_type->title = $request->title;
        $subject_type->status = $request->status;
        $subject_type->save();
        DB::commit();

        Toastr::success(__('msg_updated_successfully'), __('msg_success'));

        return redirect()->back();
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(SubjectType $subject_type)
    {
        DB::beginTransaction();
        $subject_type->delete();
        DB::commit();
        Toastr::success(__('msg_deleted_successfully'), __('msg_success'));

        return redirect()->back();
    }

}
