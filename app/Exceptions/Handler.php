<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;



class Handler extends ExceptionHandler
{
    public function invalidJson($request, ValidationException $exception): JsonResponse
    {
        return response()->json([
            'success' => false,
            'status' => 422,
            'message' => '驗證失敗',
            'errors' => $exception->errors()
        ], 422);
    }

    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
