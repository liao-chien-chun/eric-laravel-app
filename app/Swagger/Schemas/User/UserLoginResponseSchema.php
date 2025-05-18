<?php

namespace App\Swagger\Schemas\User;

/**
 * @OA\Schema(
 *     schema="UserLoginResponse",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="status", type="integer", example=200),
 *     @OA\Property(property="message", type="string", example="登入成功"),
 *     @OA\Property(property="data", type="object",
 *         @OA\Property(property="access_token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."),
 *         @OA\Property(property="token_type", type="string", example="Bearer"),
 *         @OA\Property(property="expires_in", type="integer", example=3600),
 *         @OA\Property(property="user", type="object",
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="Eric"),
 *             @OA\Property(property="email", type="string", example="eric@example.com")
 *         )
 *     )
 * )
 */
class UserLoginResponseSchema
{
}