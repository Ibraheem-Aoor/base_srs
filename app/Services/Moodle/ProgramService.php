<?php
namespace App\Services\Moodle;

use App\Models\Program;
use Illuminate\Support\Facades\Http;
use Throwable;

class ProgramService extends BaseService
{
    protected $model = Program::class;

    /**
     * Create The Program On Moodle.
     */
    public function create(Program $program)
    {
        try {
            // Set the web service function name
            $this->params['wsfunction'] = 'core_course_create_categories';

            // Create the data array for the POST request
            $categories = [
                [
                    'name' => $program->title,
                    'idnumber' => $program->shortcode,
                ]
            ];

            //data to save sent along with the query parameters.
            $this->params['categories'] = $categories;

            // Build the full URL with the parameters
            $this->url = $this->url . '?' . http_build_query($this->params);

            // Send the POST request with  parameters
            $response = $this->http->post(
                $this->url
            )->json();
            return isset($response) && !isset($response['exception']) ? $response : throw new \Exception($response['exception']);
        } catch (Throwable $e) {
            logError(e: $e, method: __METHOD__, class: get_class($this));
            return back();
        }
    }

    /**
     * Update The Program On Moodle
     */
    public function update(Program $program, $title_before_update)
    {
        try {
            $moodle_program = $this->findById($program->id_on_moodle);


            // Set the web service function name
            $this->params['wsfunction'] = 'core_course_update_categories';

            // Create the data array for the POST request
            $categories = [
                [
                    'id' => (int) $moodle_program['id'],
                    'name' => $program->title,
                    'idnumber' => $program->shortcode,
                ]
            ];

            //data to save sent along with the query parameters.
            $this->params['categories'] = $categories;

            // Build the full URL with the parameters
            $this->url = $this->url . '?' . http_build_query($this->params);

            // Send the POST request with  parameters
            $response = $this->http->post(
                $this->url
            )->json();
            // This Response Is Nullable.
            return !isset($response['exception']) ? $response : throw new \Exception($response['exception']);
        } catch (Throwable $e) {
            // dd($e);
            logError(e: $e, method: __METHOD__, class: get_class($this), custom_message: __('Moodle_Error'));
            throw new \Exception($e);
        }
    }


    /**
     * Delete Program On Moodle With All Contents
     */
    public function delete(Program $program)
    {
        try {
            $moodle_program = $this->findById($program->id_on_moodle);

            // Set the web service function name
            $this->params['wsfunction'] = 'core_course_delete_categories';

            // Create the data array for the POST request
            $categories = [
                [
                    'id' => (int) $moodle_program['id'],
                    'recursive' => 1,//1: recursively delete all contents inside this category
                ]
            ];

            //data to save sent along with the query parameters.
            $this->params['categories'] = $categories;

            // Build the full URL with the parameters
            $this->url = $this->url . '?' . http_build_query($this->params);

            // Send the POST request with  parameters
            $response = $this->http->post(
                $this->url
            )->json();
            // This Response Is Nullable.
            return !isset($response['exception']) ? $response : throw new \Exception($response['exception']);
        } catch (Throwable $e) {
            // dd($e);
            logError(e: $e, method: __METHOD__, class: get_class($this), custom_message: __('Moodle_Error'));
            throw new \Exception($e);
        }
    }


    /**
     * Find The Program By Searching for it's name
     */
    public function findByName($program_title)
    {
        // Set the web service function name
        $this->params['wsfunction'] = 'core_course_get_categories';
        $this->params['criteria'] = [
            [
                'key' => 'name',
                'value' => $program_title
            ],
        ];
        $moodle_program = $this->http->get($this->url, $this->params)->json();
        if (count($moodle_program) != 1) {
            throw new \Exception('Found More Than One Program Or None');
        }
        // Unset parameter to avoid exceptions
        unset($this->params['criteria']);
        return $moodle_program[0];
    }
    /**
     * Find The Program By ID
     */
    public function findById($id)
    {
        // Set the web service function name
        $this->params['wsfunction'] = 'core_course_get_categories';
        $this->params['criteria'] = [
            [
                'key' => 'id',
                'value' => $id
            ],
        ];
        $moodle_program = $this->http->get($this->url, $this->params)->json();
        if (count($moodle_program) != 1) {
            throw new \Exception('Found More Than One Program Or None');
        }
        // Unset parameter to avoid exceptions
        unset($this->params['criteria']);
        return $moodle_program[0];
    }



    /**
     * get array of categories "Programs"
     */
    public function get()
    {
        $this->params['wsfunction'] = 'core_course_get_categories';
        $response = $this->http->get($this->url, $this->params);
        return $response->json();
    }
}
