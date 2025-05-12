<?php

namespace App\Swagger;

/**
 * @OA\Post(
 *     path="/api/user/register",
 *     tags={"User"},
 *     summary="使用者註冊", 
 *     description="使用者註冊 API ，提供name, email, password, phone",
 *     operationId="registerUser",
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/UserRegisterRequest")
 *     ),
 *
 *     @OA\Response(
 *         response=201,
 *         description="註冊成功",
 *         @OA\JsonContent(ref="#/components/schemas/UserRegisterResponse")
 *     ),
 *
 *     @OA\Response(
 *         response=422,
 *         description="驗證失敗",
 *         @OA\JsonContent(ref="#/components/schemas/UserRegisterValidationError")
 *     ),
 * 
 *     @OA\Response(
 *         response=500,
 *         description="伺服器錯誤",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="message", type="string", example="註冊失敗：內部伺服器錯誤"),
 *             @OA\Property(property="data", type="string", nullable=true, example=null)
 *         )
 *     )
 * )
 */

class User {}