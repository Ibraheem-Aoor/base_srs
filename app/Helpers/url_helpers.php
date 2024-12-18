<?php

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

/*
 get The current Url
*/
if (!function_exists('currentUrl')) {
    function currentUrl()
    {
        return Route::getCurrentRequest()->url();
    }
}


/*
 get The previous Url
*/
if (!function_exists('previousUrl')) {
    function previousUrl()
    {
        return url()->previous();
    }
}
