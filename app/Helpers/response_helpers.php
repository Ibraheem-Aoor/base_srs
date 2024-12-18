<?php
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

/**
 * Generate Response
 * @param  bool $status
 * @param string $redirect
 * @param string $modal_to_hide
 * @param  string $row_to_delete
 * @param  bool $reset_form
 */
if (!function_exists('generateResponse')) {
    function generateResponse(
        $status,
        $redirect = null,
        $modal_to_hide = null,
        $row_to_delete = null,
        $reset_form = false,
        $reload = false,
        $table_reload = false,
        $table = null,
        $is_deleted = false,
        $message = null,
        $extra_data = []
    ) {
        $response_message = !is_null($message) ? ($message) : ($status ? __('response.success') : __('response.error'));
        $base_response_data = [
            'status' => $status,
            'message' => $response_message,
            'redirect' => $redirect ? $redirect : null,
            'modal_to_hide' => $modal_to_hide,
            'row_to_delete' => $row_to_delete,
            'reset_form' => $reset_form,
            'code' => $status ? 200 : 500,
            'reload' => $reload,
            'reload_table' => $table_reload,
            'table' => $table,
            'is_deleted' => $is_deleted,
        ];
        $response = array_merge($base_response_data, ['data' => $extra_data]);
        return $response;
    }
}



/**
 * Generate Api Response
 * @param  bool $status
 * @param numeric $code
 * @param array $data
 */
if (!function_exists('generateApiResoponse')) {
    function generateApiResoponse($status, $code, $data = [], $message = null, $paginator = null)
    {
        $response = [
            'code' => $code,
            'status' => $status,
            'data' => $data,
        ];
        if ($message) {
            $response['message'] = $message;
        }
        if (isset($paginator)) {
            $response['meta'] = [
                'pagination' => [
                    'total' => $paginator->total(),
                    'count' => $paginator->count(),
                    'per_page' => $paginator->perPage(),
                    'current_page' => $paginator->currentPage(),
                    'total_pages' => $paginator->lastPage(),
                    'links' => [
                            'next' => $paginator->nextPageUrl(),
                            'prev' => $paginator->previousPageUrl(),
                        ],
                ],
            ];

        }
        return response()->json($response, $code);
    }
}



/**
 * Gather the meta data for the response.
 *
 * @param  LengthAwarePaginator  $paginated
 * @return array
 */
if (!function_exists('pagination')) {
    function pagination($paginated)
    {
        return [
            'current' => $paginated->currentPage(),
            'last' => $paginated->lastPage(),
            'base' => $paginated->url(1),
            'next' => $paginated->nextPageUrl(),
            'prev' => $paginated->previousPageUrl()
        ];
    }
}
