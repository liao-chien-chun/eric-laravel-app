<?php

namespace App\Swagger\Schemas\User;

/**
 * @OA\Schema(
 *     schema="UserRegisterValidationError",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(property="status", type="integer", example=422),
 *     @OA\Property(property="message", type="string", example="驗證失敗"),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         @OA\Property(property="email", type="array", @OA\Items(type="string", example="該 email 已經被註冊過了")),
 *         @OA\Property(property="password", type="array", @OA\Items(type="string", example="密碼至少6碼")),
 *     )
 * )
 */

class UserRegisterValidationErrorSchema {}