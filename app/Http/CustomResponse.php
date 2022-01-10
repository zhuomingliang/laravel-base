<?php

namespace App\Http;

use Illuminate\Http\Response;

trait CustomResponse {
    /**
     * 201
     *
     * @param string $content
     * @return Response
     */
    protected function created($content = '') {
        return new Response($content, Response::HTTP_CREATED);
    }

    /**
     * 202
     *
     * @return Response
     */

    protected function accepted() {
        return new Response('', Response::HTTP_ACCEPTED);
    }

    /**
     * 204
     *
     * @return Response
     */
    protected function noContent() {
        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * 400
     *
     * @param $message
     * @param array $headers
     * @param int $options
     * @return \Illuminate\Http\JsonResponse
     */
    protected function badRequest($message, array $headers = [], $options = 0) {
        return response()->json([
            'message' => $message
        ], Response::HTTP_BAD_REQUEST, $headers, $options);
    }

    /**
     * 401
     *
     * @param string $message
     * @param array $headers
     * @param int $options
     * @return \Illuminate\Http\JsonResponse
     */
    protected function unauthorized($message = '', array $headers = [], $options = 0) {
        return response()->json([
            'message' => $message ? $message : 'Token Signature could not be verified.'
        ], Response::HTTP_UNAUTHORIZED, $headers, $options);
    }

    /**
     * 403
     *
     * @param string $message
     * @param array $headers
     * @param int $options
     * @return \Illuminate\Http\JsonResponse
     */
    protected function forbidden($message = '', array $headers = [], $options = 0) {
        return response()->json([
            'message' => $message ? $message : 'Insufficient permissions.'
        ], Response::HTTP_FORBIDDEN, $headers, $options);
    }

    /**
     * 422
     *
     * @param array $errors
     * @param array $headers
     * @param string $message
     * @param int $options
     * @return \Illuminate\Http\JsonResponse
     */

    protected function unprocessableEntity(array $errors = [], array $headers = [], $message = '', $options = 0) {
        return response()->json([
            'message' => $message ? $message : '422 Unprocessable Entity',
            'errors' => $errors
        ], Response::HTTP_UNPROCESSABLE_ENTITY, $headers, $options);
    }
}
