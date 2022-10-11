<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Return success response
     * @method sendSuccessResponse
     * @param mixed $data
     * @param int $statusCode
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendSuccessResponse(mixed $data, int $statusCode = 200, string $message = '')
    {
        $response = [];
        if ((is_array($data) && (count($data) > 0)) || $data instanceof Collection || is_object($data)) {
            $response['data'] = $data;
        }
        if ($message == '') {
            if ($statusCode === 200) {
                $message = 'request_successful';
            } elseif ($statusCode === 201) {
                $message = 'creation_successful';
            } elseif ($statusCode === 202) {
                $message = 'acceptance_successful';
            } elseif ($statusCode === 204) {
                $message = 'deletion_successful';
            }
        }
        if ($message != '') {
            $response['message'] = trans("messages.{$message}");
            $response['result'] = true;
        }
        return response()->json($response, $statusCode);
    }

    /**
     * Return error response
     * @method sendErrorResponse
     * @param string|array $message
     * @param int $statusCode
     * @param array $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendErrorResponse(string|array $message, int $statusCode = 400, array $errors = [])
    {
        $response = [
            'result' => false,
            'message' => trans("messages.{$message}")
        ];
        if (is_array($message)) {
            $response = $message;
        }
        if (count($errors) > 0) {
            $response['errors'] = $errors;
        }
        return response()->json($response, $statusCode);
    }

    /**
     * Add pagination to query
     * @param Builder|QueryBuilder $query
     * @param FormRequest|Request $request
     * @param bool $checkForPaginateFlag
     * @return void
     */
    public function addPaginationToQuery(Builder|QueryBuilder &$query, FormRequest|Request &$request, bool $checkForPaginateFlag = true)
    {
        if ($checkForPaginateFlag) {
            if (!$request->paginate) {
                return;
            }
        }
        Validator::make($request->all(), [
            'offset' => 'nullable|integer|min:0',
            'limit' => 'nullable|integer|min:1'
        ])->validate();
        if ($request->has('offset')) {
            $query->skip($request->offset);
        }
        if ($request->has('limit')) {
            $query->take($request->limit);
        }
        return $query;
    }
}
