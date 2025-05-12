<?php

namespace App\Swagger\Schemas\User;

/**
 * 
 * @OA\Schema(
 *     schema="UserRegisterRequest",
 *     required={"name", "email", "password", "password_confirmation"},
 *     @OA\Property(property="name", type="string", example="Eric"),
 *     @OA\Property(property="email", type="string", format="email", example="eric@example.com"),
 *     @OA\Property(property="password", type="string", format="password", example="secret123"),
 *     @OA\Property(property="password_confirmation", type="string", example="secret123"),
 *     @OA\Property(property="phone", type="string", example="0912345678"),
 * )
 */

class UserRegisterRequestSchema {}