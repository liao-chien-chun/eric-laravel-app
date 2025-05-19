<?php

namespace App\Swagger;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         title="Eric Laravel 架構模組 API 文件",
 *         version="1.0.0",
 *         description="這是 API 文件",
 *     ),
 *     @OA\Server(
 *         description="主要服務器",
 *         url="http://localhost:8080"
 *     )
 * )
 * 
 * @OA\SecurityScheme(
 *         name="Authorization",
 *         description="請輸入JWT token",
 *         securityScheme="bearerAuth",
 *         type="http",
 *         in="header",
 *         scheme="bearer",
 *         bearerFormat="JWT"
 *  )
 */

class Base
{
}