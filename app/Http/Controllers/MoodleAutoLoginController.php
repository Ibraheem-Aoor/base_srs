<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use App\Helpers\MoodleCurl;

class MoodleAutoLoginController extends Controller
{
    public function login()
    {
        $username = auth()->user()->moodle_username;
        $userid = auth()->user()->id;
        $courseid = null;
        $modname = null;
        $activityid = null;
        $loginurl = $this->getloginurl($userid);

        // Check if login URL was retrieved successfully
        if ($loginurl) {
            return redirect($loginurl);
        }

        return redirect()->back()->with('error', 'Failed to retrieve Moodle login URL.');
    }

    function getloginurl($userid)
    {
        $token = env('MOODLE_TOKEN');
        $domainname = env('MOODLE_HOST');
        $functionname = 'auth_userkey_request_login_url';
        $serverurl = $domainname . '/webservice/rest/server.php?wstoken=' . $token . '&wsfunction=' . $functionname . '&moodlewsrestformat=json';

        $params = [
            'user' => [
                'idnumber' => $userid
            ]
        ];
        $curl = new MoodleCurl();


        $response = $curl->post($serverurl, $params);
        $response = json_decode($response);
        if (isset($response->loginurl)) {
            return $response->loginurl;
        } else {
            return null;
        }
    }
}
