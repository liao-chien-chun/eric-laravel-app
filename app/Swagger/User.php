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
 * 
 * @OA\Post(
 *     path="/api/user/login",
 *     tags={"User"},
 *     summary="使用者登入",
 *     description="使用者登入 API ，回傳JWT token",
 *     operationId="loginUser",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/UserLoginRequest")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="登入成功",
 *         @OA\JsonContent(ref="#/components/schemas/UserLoginResponse")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="登入失敗（帳密錯誤）",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="status", type="integer", example=401),
 *             @OA\Property(property="message", type="string", example="帳號或密碼錯誤"),
 *             @OA\Property(property="data", type="string", example=null, nullable=true),
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="伺服器錯誤",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="message", type="string", example="Token 建立失敗"),
 *             @OA\Property(property="data", type="string", example=null, nullable=true),
 *         )
 *     )
 * )
 * 
 * @OA\Post(
 *     path="/api/user/logout",
 *     tags={"User"},
 *     summary="使用者登出",
 *     description="使用者登出，JWT token 失效處理",
 *     security={{"bearerAuth":{}}},
 *     operationId="logoutUser",
 *     
 *     @OA\Response(
 *         response=200,
 *         description="登出成功",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="登出成功"),
 *             @OA\Property(property="data", type="string", example=null, nullable=true) 
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="登出失敗",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="message", type="string", example="登出失敗"),
 *             @OA\Property(property="data", type="string", example=null, nullable=true)
 *         )
 *     )
 * )
 */

class User {}