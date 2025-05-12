<?php

namespace App\Swagger\Schemas\User;

/**
 * @OA\Schema(
 *     schema="UserRegisterResponse",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="status", type="integer", example=201),
 *     @OA\Property(property="message", type="string", example="註冊成功"),
 *     @OA\Property(property="data", type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Eric"),
 *         @OA\Property(property="email", type="string", example="eric@example.com"),
 *         @OA\Property(property="phone", type="string", example="0912345678"),
 *         @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-12 12:00:00"),
 *         @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-12 12:00:00"),
 *     )
 * )
 */

class UserRegisterResponseSchema {}