<?php

namespace App\Swagger\Schemas\User;

/**
 * @OA\Schema(
 *     schema="UserLoginRequest",
 *     required={"email", "password"},
 *     @OA\Property(property="email", type="string", format="email", example="eric@example.com"),
 *     @OA\Property(property="password", type="string", format="password", example="secret123")
 * )
 */

class UserLoginRequestSchema
{
}