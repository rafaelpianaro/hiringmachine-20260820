<?php

namespace App\Traits;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;

trait ApiResponser
{
    /**
     * Return a success JSON response.
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    protected function successResponse($data = null, string $message = 'Sucesso', int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $this->normalizeResponseData($data),
        ], $code);
    }

    protected function normalizeResponseData(mixed $data): mixed
    {
        if ($data instanceof DateTimeInterface) {
            return Carbon::instance($data)->format('Y-m-d H:i:s');
        }

        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        }

        if ($data instanceof \Traversable) {
            $data = iterator_to_array($data);
        }

        if (is_array($data)) {
            $normalized = [];

            foreach ($data as $key => $value) {
                if (is_array($value) || $value instanceof Arrayable || $value instanceof \Traversable) {
                    $normalized[$key] = $this->normalizeResponseData($value);
                    continue;
                }

                if ($value instanceof DateTimeInterface) {
                    $normalized[$key] = Carbon::instance($value)->format('Y-m-d H:i:s');
                    continue;
                }

                $normalized[$key] = $value;
            }

            foreach (['created_at', 'updated_at', 'deleted_at'] as $dateField) {
                if (array_key_exists($dateField, $normalized)) {
                    $normalized[$dateField . '_formatted'] = $this->formatDateValue($normalized[$dateField]);
                }
            }

            return $normalized;
        }

        return $data;
    }

    protected function formatDateValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof DateTimeInterface) {
            return Carbon::instance($value)->format('d/m/Y H:i');
        }

        try {
            return Carbon::parse($value)->format('d/m/Y H:i');
        } catch (\Throwable) {
            return (string) $value;
        }
    }

    /**
     * Return an error JSON response.
     *
     * @param string $message
     * @param int $code
     * @param mixed $errors
     * @return JsonResponse
     */
    protected function errorResponse(string $message = 'Erro', int $code = 400, $errors = null): JsonResponse
    {
        $response = [
            'status' => 'error',
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Return a validation error JSON response.
     *
     * @param mixed $errors
     * @param string $message
     * @return JsonResponse
     */
    protected function validationErrorResponse($errors, string $message = 'Erro de validação'): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
        ], 422);
    }

    /**
     * Return a not found JSON response.
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function notFoundResponse(string $message = 'Resource not found'): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], 404);
    }

    /**
     * Return an unauthorized JSON response.
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function unauthorizedResponse(string $message = 'Não autorizado'): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], 401);
    }

    /**
     * Return a forbidden JSON response.
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function forbiddenResponse(string $message = 'Acesso negado'): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], 403);
    }
}
