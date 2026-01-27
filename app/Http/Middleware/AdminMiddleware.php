<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 檢查使用者是否已登入
        if (!auth('api')->check()) {
            return response()->json([
                'success' => false,
                'status' => 401,
                'message' => '請先登入',
                'data' => null,
            ], 401);
        }

        // 檢查使用者是否為管理者
        $user = auth('api')->user();

        if (!$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => '權限不足，僅限管理者使用',
                'data' => null,
            ], 403);
        }

        return $next($request);
    }
}
