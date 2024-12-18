<?php

return [
    'host'    => env('MOODLE_HOST'),
    'token'   => env('MOODLE_TOKEN'),
    'uri'     => env('MOODLE_WEBSERVICE_ENDPOINT'),
    'timeout' => env('MOODLE_TIMEOUT', 60), // in seconds
    'format' => 'json',
];
