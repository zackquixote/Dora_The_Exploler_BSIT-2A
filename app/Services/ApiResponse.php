<?php

/**
 * Description:  Standardized API response service for consistent JSON output
 * Author:       Security Hardening
 * Date Created: June 6, 2026
 */

namespace App\Services;

/**
 * ApiResponse
 *
 * Provides static helpers for returning consistent JSON API responses
 * throughout the system. All responses follow the format:
 *   { success: bool, message: string, data: mixed|null, timestamp: int }
 *
 * Usage:
 *   return ApiResponse::success($data, 'Record created.', 201);
 *   return ApiResponse::error('Validation failed.', $errors, 422);
 *   return ApiResponse::notFound('Resident not found.');
 *   return ApiResponse::unauthorized('Please log in.');
 */
class ApiResponse
{
    /**
     * Return a successful JSON response.
     *
     * @param mixed       $data       The payload to return (array, object, or null)
     * @param string      $message    Human-readable success message
     * @param int         $statusCode HTTP status code (default 200)
     */
    public static function success($data = null, string $message = 'Success', int $statusCode = 200)
    {
        return response()
            ->setStatusCode($statusCode)
            ->setJSON([
                'success'   => true,
                'message'   => $message,
                'data'      => $data,
                'timestamp' => time(),
            ]);
    }

    /**
     * Return a created (201) JSON response.
     *
     * @param mixed  $data    The newly created resource
     * @param string $message Human-readable message
     */
    public static function created($data = null, string $message = 'Created successfully')
    {
        return static::success($data, $message, 201);
    }

    /**
     * Return a generic error JSON response.
     *
     * @param string     $message    Human-readable error description
     * @param mixed      $errors     Validation errors or additional details (optional)
     * @param int        $statusCode HTTP status code (default 400)
     */
    public static function error(string $message = 'An error occurred', $errors = null, int $statusCode = 400)
    {
        return response()
            ->setStatusCode($statusCode)
            ->setJSON([
                'success'   => false,
                'message'   => $message,
                'errors'    => $errors,
                'timestamp' => time(),
            ]);
    }

    /**
     * Return a 404 Not Found JSON response.
     *
     * @param string $message Human-readable message
     */
    public static function notFound(string $message = 'Resource not found')
    {
        return static::error($message, null, 404);
    }

    /**
     * Return a 401 Unauthorized JSON response.
     *
     * @param string $message Human-readable message
     */
    public static function unauthorized(string $message = 'Unauthorized')
    {
        return static::error($message, null, 401);
    }

    /**
     * Return a 403 Forbidden JSON response.
     *
     * @param string $message Human-readable message
     */
    public static function forbidden(string $message = 'Access denied')
    {
        return static::error($message, null, 403);
    }

    /**
     * Return a 422 Unprocessable Entity response for validation failures.
     *
     * @param mixed  $errors  Validation errors (array keyed by field name)
     * @param string $message Human-readable message
     */
    public static function validationError($errors = null, string $message = 'Validation failed')
    {
        return static::error($message, $errors, 422);
    }

    /**
     * Return a 500 Internal Server Error response.
     *
     * @param string $message Human-readable message
     */
    public static function serverError(string $message = 'Internal server error')
    {
        return static::error($message, null, 500);
    }
}
