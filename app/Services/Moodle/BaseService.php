<?php

namespace App\Services\Moodle;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Throwable;

class BaseService
{
    /**
     * Moodle Service To Handle Webservices connection with moodle
     * REST API Protocol
     */
    protected $configs, $params, $url, $http;
    public function __construct()
    {
        $this->configs = config('moodle');
        $this->params = [
            'wstoken' => $this->configs['token'],
            'moodlewsrestformat' => 'json',
        ];

        $this->url = $this->configs['host'] . '/' . $this->configs['uri'];

        $this->http = Http::timeout($this->configs['timeout'])->withoutVerifying();
    }



    /**
     * Update Resource On Moodle
     */
    public function create(array $query_params = [])
    {
        try {
            $query_params = array_merge($this->params, $query_params);
            // Build the full URL with the parameters
            $endpoint = $this->url . '?' . http_build_query($query_params);
            // Send the POST request with  parameters
            $response = $this->http->post(
                $endpoint
            )->json();
            // dd($endpoint);
            return isset($response) && !isset($response['exception'], $response['warnings']) ? $response : throw new \Exception($response['exception'] . '||Message: ' . $response['message']);
        } catch (Throwable $e) {
            // dd($e);
            logError(e: $e, method: __METHOD__, class: get_class($this));
        }
    }


    /**
     * Update The Program On Moodle
     * Most Update Requests return NULL.
     */
    public function update(array $query_params)
    {
        try {
            $query_params = array_merge($this->params, $query_params);
            $endpoint = $this->url . '?' . http_build_query($query_params);
            // Send the POST request with  parameters
            $response = $this->http->post(
                $endpoint
            )->json();
            // This Response Is Nullable.
            return !isset($response['exception']) ? $response : throw new \Exception($response['exception'] . '||Message: ' . $response['message']);
        } catch (Throwable $e) {
            logError(e: $e, method: __METHOD__, class: get_class($this), custom_message: __('Moodle_Error'));
            throw new \Exception($e);
        }
    }

    /**
     * Delete Resource From Moodle.
     */
    public function delete(array $query_params = [])
    {
        try {
            $query_params = array_merge($this->params, $query_params);
            $endpoint = $this->url . '?' . http_build_query($query_params);
            // Send the POST request with  parameters
            $response = $this->http->post(
                $endpoint
            )->json();
            // This Response Is Nullable.
            return !isset($response['exception']) ? $response : throw new \Exception($response['exception'] . '||Message: ' . $response['message']);
        } catch (Throwable $e) {
            logError(e: $e, method: __METHOD__, class: get_class($this), custom_message: __('Moodle_Error'));
            throw new \Exception($e);
        }
    }


    public function get($query_params = [])
    {
        try {
            $query_params = array_merge($this->params, $query_params);
            $endpoint = $this->url . '?' . http_build_query($query_params);
            // Send the GET request with  parameters
            $response = $this->http->get(
                $endpoint
            )->json();
            // This Response Is Nullable.
            return isset($response) && !isset($response['exception']) ? $response : throw new \Exception($response['exception'] . '||Message: ' . $response['message']);
        } catch (Throwable $e) {
            logError(e: $e, method: __METHOD__, class: get_class($this), custom_message: __('Moodle_Error'));
            throw new \Exception($e);
        }
    }

    /**
     * Find The Program By ID
     * @param string $id represnts the resource_mdl_id on of the resource on our DB.
     * @param string $function represnts the function to use from moodle webservices.
     */
    public function findById($id, string $function)
    {
        // dd($function);
        $query_params = $this->params;

        // Set the web service function name
        $query_params['wsfunction'] = $function;
        $query_params['criteria'] = [
            [
                'key' => 'id',
                'value' => $id
            ],
        ];
        $moodle_resource = $this->http->get($this->url, $query_params)->json();
        if (count($moodle_resource) != 1) {
            throw new \Exception('Found More Than One resource Or None');
        }
        return $moodle_resource[0];
    }
}
