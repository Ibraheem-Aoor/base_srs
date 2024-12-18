<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FeesCategory;
use App\Models\FeesDiscount;
use App\Models\Session;
use App\Models\StatusType;
use Illuminate\Validation\Rule;
use Toastr;

class FeesDiscountController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Module Data
        $this->title = trans_choice('module_fees_discount', 1);
        $this->route = 'admin.fees-discount';
        $this->view = 'admin.fees-discount';
        $this->path = 'fees-discount';
        $this->access = 'fees-discount';


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

        $data['categories'] = FeesCategory::where('status', '1')->orderBy('title', 'asc')->get();
        $data['statusTypes'] = StatusType::where('status', '1')->orderBy('title', 'asc')->get();
        $data['rows'] = FeesDiscount::orderBy('start_date', 'desc')->get();
        $data['sessions'] = Session::query()->status(1)->get(['id', 'title']);
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
        //Field Validation
        $request->validate([
            'title' => 'required',
            'amount' => 'required|numeric',
            'type' => 'required|integer',
            'actual_type' => ['required', Rule::in(['discount', 'grant'])],
        ]);
        // Insert Data
        $feesDiscount = new FeesDiscount;
        $feesDiscount->title = $request->title;
        $feesDiscount->amount = $request->amount;
        $feesDiscount->type = $request->type;
        $feesDiscount->discount_type = $request->actual_type;
        $feesDiscount->save();


        Toastr::success(__('msg_created_successfully'), __('msg_success'));

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\FeesDiscount  $feesDiscount
     * @return \Illuminate\Http\Response
     */
    public function show(FeesDiscount $feesDiscount)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\FeesDiscount  $feesDiscount
     * @return \Illuminate\Http\Response
     */
    public function edit(FeesDiscount $feesDiscount)
    {
        //
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;
        $data['path'] = $this->path;
        $data['access'] = $this->access;

        $data['categories'] = FeesCategory::where('status', '1')->orderBy('title', 'asc')->get();
        $data['statusTypes'] = StatusType::where('status', '1')->orderBy('title', 'asc')->get();
        $data['rows'] = FeesDiscount::orderBy('start_date', 'desc')->get();
        $data['row'] = $feesDiscount;

        return view($this->view . '.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FeesDiscount  $feesDiscount
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //Field Validation
        $request->validate([
            'title' => 'required',
            'amount' => 'required|numeric',
            'type' => 'required|integer',
        ]);


        // Update Data
        $feesDiscount = FeesDiscount::findOrFail($id);
        $feesDiscount->title = $request->title;
        $feesDiscount->amount = $request->amount;
        $feesDiscount->type = $request->type;
        $feesDiscount->discount_type = $request->actual_type;
        $feesDiscount->save();



        Toastr::success(__('msg_updated_successfully'), __('msg_success'));

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FeesDiscount  $feesDiscount
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //Delete Attach
        $feesDiscount = FeesDiscount::findOrFail($id);
        $feesDiscount->feesCategories()->detach();
        $feesDiscount->statusTypes()->detach();

        //Delete Data
        $feesDiscount->delete();

        Toastr::success(__('msg_deleted_successfully'), __('msg_success'));

        return redirect()->back();
    }
}
