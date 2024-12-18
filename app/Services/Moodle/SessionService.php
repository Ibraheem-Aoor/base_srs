<?php

namespace App\Services\Moodle;

use App\Models\Session;
use Illuminate\Support\Facades\Http;
use Throwable;

class SessionService extends BaseService
{

    /**
     * Each Session In SRS Represents a Moodle Category.
     */
    protected $model = Session::class;

    /**
     * Create The session On Moodle.
     */
    public function store(Session $session)
    {
        $query_params['wsfunction'] = 'core_course_create_categories';
        // Create the data array for the POST request
        $categories = [
            [
                'name' => $session->title,
            ]
        ];
        //data to save sent along with the query parameters.
        $query_params['categories'] = $categories;
        return parent::create($query_params);
    }

    /**
     * Update The session On Moodle
     */
    public function edit(Session $session)
    {
        $moodle_session = $this->findById($session->id_on_moodle, 'core_course_get_categories');
        // Set the web service function name
        $query_params['wsfunction'] = 'core_course_update_categories';

        // Create the data array for the POST request
        $categories = [
            [
                'id' => (int) $moodle_session['id'],
                'name' => $session->title,
            ]
        ];

        //data to save sent along with the query parameters.
        $query_params['categories'] = $categories;
        return parent::update($query_params);
    }


    /**
     * Delete session On Moodle With All Contents
     */
    public function destroy(Session $session)
    {
        $moodle_session = $this->findById($session->id_on_moodle, 'core_course_get_categories');

        $query_params['wsfunction'] = 'core_course_delete_categories';

        // Create the data array for the POST req   uest
        $categories = [
            [
                'id' => (int) $moodle_session['id'],
                'recursive' => 0, //1: recursively delete all contents inside this category
            ]
        ];

        //data to save sent along with the query parameters.
        $query_params['categories'] = $categories;

        return parent::delete($query_params);
    }



    /**
     * Find The session By Searching for it's name
     * ### Deprecated for now ####
     */
    public function findByName($session_title)
    {
        // Set the web service function name
        $query_params['wsfunction'] = 'core_course_get_categories';
        $query_params['criteria'] = [
            [
                'key' => 'name',
                'value' => $session_title
            ],
        ];
        $moodle_program = $this->http->get($this->url, $this->params)->json();
        if (count($moodle_program) != 1) {
            throw new \Exception('Found More Than One Program Or None');
        }
        // Unset parameter to avoid exceptions
        unset($query_params['criteria']);
        return $moodle_program[0];
    }
}
