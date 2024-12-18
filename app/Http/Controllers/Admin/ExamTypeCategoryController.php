<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ClassTypeEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExamType;
use App\Models\ExamTypeCategory;
use Throwable;
use Toastr;

class ExamTypeCategoryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Module Data
        $this->title = trans_choice('module_exam_type_category', 1);
        $this->route = 'admin.exam-type-category';
        $this->view = 'admin.exam-type-category';
        $this->path = 'exam-type-category';
        $this->access = 'exam-type-category';


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

        $data['rows'] = ExamTypeCategory::orderBy('created_at', 'desc')->get();
        $data['class_types'] = ClassTypeEnum::getValues();

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
    public function store(Request $request)
    {
        // Field Validation
        $request->validate([
            'title' => 'required|max:191|unique:exam_type_categories,title',
            'class_type' => 'required',
        ]);
        $examTypeCategory = new ExamTypeCategory();
        $examTypeCategory->title = $request->title;
        $examTypeCategory->class_type = $request->class_type;
        $examTypeCategory->save();
        Toastr::success(__('msg_created_successfully'), __('msg_success'));

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ExamType $examType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(ExamType $examType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ExamTypeCategory $examTypeCategory)
    {
        // Field Validation
        $request->validate([
            'title' => 'required|max:191|unique:exam_type_categories,title,' . $examTypeCategory->id,
            'class_type' => 'required',
        ]);

        // Update Data
        $examTypeCategory->title = $request->title;
        $examTypeCategory->class_type = $request->class_type;
        $examTypeCategory->status = $request->status;
        $examTypeCategory->save();


        Toastr::success(__('msg_updated_successfully'), __('msg_success'));

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ExamTypeCategory $examTypeCategory)
    {
        // Delete Data
        $examTypeCategory->delete();

        Toastr::success(__('msg_deleted_successfully'), __('msg_success'));

        return redirect()->back();
    }


    public function copy(Request $request, ExamTypeCategory $exam_type_category)
    {
        $request->validate([
            'title' => 'required|max:191|unique:exam_type_categories,title',
        ]);
        try {
            $created_exam_type_category = ExamTypeCategory::create([
                'title' => $request->input('title'),
                'class_type' => $exam_type_category->class_type,
            ]);
            foreach ($exam_type_category->examsTypes as $exam_type) {
                $exam_type_copy = $exam_type->toArray();
                $exam_type_copy['exam_type_category_id'] = $created_exam_type_category->id;
                ExamType::create($exam_type_copy);
            }
            return returnSuccess($this->route.'.index');
        } catch (Throwable $e) {
            dd($e);
            logError($e, __METHOD__, get_class($this));
            return back();
        }
    }
}
