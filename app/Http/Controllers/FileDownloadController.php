<?php

namespace App\Http\Controllers;

use App\Traits\FileDownloader;
use Illuminate\Http\Request;

class FileDownloadController extends Controller
{
    use FileDownloader;

    public function downloadFile(Request $request)
    {
        return $this->download($request);
    }
}
