<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Image;
use Illuminate\Support\Facades\File as FacadesFile;

trait FileDownloader {

    /**
     * @param Request $request
     */
    public function download(Request $request) {

        $filePath = public_path($request->query('file'));
        if (FacadesFile::exists($filePath)) {
            return response()->download($filePath);
        }

        return abort(404, 'File not found.');
    }


}
