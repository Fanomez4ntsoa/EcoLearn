<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * Generate JsonResponse success
     *
     * @param string|null $message
     * @param mixed $data
     * @param integer $httpCode
     * @return JsonResponse
     */
    public function success(string $message = null, mixed $data = null, int $httpCode = 200): JsonResponse
    {
        if (!$message) {
            $message = __('success.default');
        }

        $response['status'] = true;
        $response['message'] = $message;

        if($data) {
            $response['data'] = $data;
        }

        return new JsonResponse($response, $httpCode);
    }

    /**
     * Generate JsonResponse error
     *
     * @param string|null $message
     * @param mixed $data
     * @param integer $httpCode
     * @return JsonResponse
     */
    public function error(string $message = null, mixed $data = null, int $httpCode = 500): JsonResponse
    {
        if (!$message) {
            $message = __('error.default');
        }

        $response['status'] = false;
        $response['message'] = $message;

        if($data) {
            $response['data'] = $data;
        }

        return new JsonResponse($response, $httpCode);
    }

    /**
     * Send invalid error
     *
     * @param string|null $message
     * @param mixed $data
     * @return JsonResponse
     */
    public function invalid(string $message = null, mixed $data = null): JsonResponse
    {
        if(is_null($message)) {
            $message = __('error.request.invalid');
        }

        return $this->error($message, $data, 400);
    }

    /**
     * Send forbidden response
     *
     * @param string|null $message
     * @param mixed $data
     * @return JsonResponse
     */
    public function forbidden(string $message = null, mixed $data = null): JsonResponse
    {
        if(is_null($message)) {
            $message = __('error.access.forbidden');
        }

        return $this->error($message, $data, 403);
    }

    /**
     * Unprocessable entity
     *
     * @param mixed $data
     * @return JsonResponse
     */
    public function unprocessableEntityError(mixed $data): JsonResponse
    {
        return $this->error(__('error.validations'), $data, 422);
    }
}
