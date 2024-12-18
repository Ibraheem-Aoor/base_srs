<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Web\Feature;
use App\Models\Language;

class FeatureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        // Course
        $data['feature'] = Feature::where('slug', $slug)
                            ->where('status', '1')
                            ->firstOrFail();

        return view('web.feature-single', $data);
    }
}
