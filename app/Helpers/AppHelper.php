<?php
namespace App\Helpers;

use Carbon\Carbon;

class AppHelper
{

    public static function humanFileSize($size) {
        if ($size >= 1073741824) { // Greater than or equal to 1 GB
            $fileSize = round($size / 1024 / 1024 / 1024, 1) . 'GB';
        } elseif ($size >= 1048576) { // Greater than or equal to 1 MB
            $fileSize = round($size / 1024 / 1024, 1) . 'MB';
        } elseif ($size >= 1024) { // Greater than or equal to 1 KB
            $fileSize = round($size / 1024, 1) . 'KB';
        } else {
            $fileSize = $size . ' bytes';
        }
        return $fileSize;
    }
}
