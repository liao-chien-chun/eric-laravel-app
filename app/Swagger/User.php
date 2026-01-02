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
 * ),
 *
 * @OA\Get(
 *     path="/api/user/{user}/posts",
 *     summary="取得使用者的文章列表",
 *     tags={"User"},
 *     description="取得指定使用者的文章列表（分頁）。預設顯示已發佈文章（任何人可看），草稿和隱藏文章只有本人登入後可看。",
 *     operationId="getUserPosts",
 *     security={{}, {"bearerAuth":{}}},
 *
 *     @OA\Parameter(
 *         name="user",
 *         in="path",
 *         description="使用者 ID",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Parameter(
 *         name="status",
 *         in="query",
 *         description="文章狀態 (1:草稿, 2:發布, 3:隱藏)，預設為 2",
 *         required=false,
 *         @OA\Schema(type="integer", enum={1, 2, 3}, example=2)
 *     ),
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         description="每頁筆數，預設 15，最多 100",
 *         required=false,
 *         @OA\Schema(type="integer", example=15)
 *     ),
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="頁碼，預設 1",
 *         required=false,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="文章列表取得成功",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="文章列表取得成功"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(
 *                     property="posts",
 *                     type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="title", type="string", example="我的第一篇文章"),
 *                         @OA\Property(property="content", type="string", example="這是文章內容..."),
 *                         @OA\Property(property="status", type="integer", example=2, description="1:草稿, 2:發布, 3:隱藏"),
 *                         @OA\Property(
 *                             property="user",
 *                             type="object",
 *                             @OA\Property(property="id", type="integer", example=1),
 *                             @OA\Property(property="name", type="string", example="張三")
 *                         ),
 *                         @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-12 00:55:29"),
 *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-12 00:55:29")
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="pagination",
 *                     type="object",
 *                     @OA\Property(property="current_page", type="integer", example=1),
 *                     @OA\Property(property="per_page", type="integer", example=15),
 *                     @OA\Property(property="total", type="integer", example=50),
 *                     @OA\Property(property="last_page", type="integer", example=4)
 *                 )
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=403,
 *         description="無權限查看此內容（嘗試查看他人的草稿或隱藏文章）",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="status", type="integer", example=403),
 *             @OA\Property(property="message", type="string", example="你沒有權限查看此內容"),
 *             @OA\Property(property="data", type="null", example=null)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=422,
 *         description="驗證失敗",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="status", type="integer", example=422),
 *             @OA\Property(property="message", type="string", example="驗證失敗"),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 @OA\Property(
 *                     property="status",
 *                     type="array",
 *                     @OA\Items(type="string", example="狀態僅允許 1(草稿)、2(發布)、3(隱藏)")
 *                 ),
 *                 @OA\Property(
 *                     property="per_page",
 *                     type="array",
 *                     @OA\Items(type="string", example="每頁筆數最多為 100")
 *                 )
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=500,
 *         description="伺服器錯誤",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="message", type="string", example="伺服器錯誤，請稍後再試"),
 *             @OA\Property(property="data", type="null", example=null)
 *         )
 *     )
 * )
 */

class User {}