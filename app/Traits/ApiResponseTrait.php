<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    /**
     * Respuesta exitosa estandarizada.
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    protected function successResponse(mixed $data, string $message = 'Operación realizada con éxito', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'error' => null
        ], $code);
    }

    /**
     * Respuesta de error estandarizada.
     *
     * @param string $message
     * @param string $errorCode
     * @param int $code
     * @return JsonResponse
     */
    protected function errorResponse(string $message, string $errorCode = 'INTERNAL_ERROR', int $code = 500): JsonResponse
    {
        return response()->json([
            'success' => false,
            'data' => null,
            'error' => [
                'code' => $errorCode,
                'message' => $message
            ]
        ], $code);
    }
}