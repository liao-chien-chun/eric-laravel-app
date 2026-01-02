<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

/**
 * 可選的身份驗證中間件
 *
 * 嘗試驗證 JWT token，但不強制要求
 * 如果有有效的 token，設定當前使用者；否則繼續執行
 */
class OptionalAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // 嘗試從請求中解析 JWT token
            if ($token = $request->bearerToken()) {
                // 嘗試驗證並取得使用者
                $user = JWTAuth::setToken($token)->authenticate();

                // 如果成功，設定當前使用者
                if ($user) {
                    auth()->setUser($user);
                }
            }
        } catch (JWTException $e) {
            // Token 無效或過期，不做任何事，繼續執行
            // 這樣未登入的使用者也能訪問
        }

        return $next($request);
    }
}
