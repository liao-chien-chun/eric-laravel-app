<?php 

namespace App\Swagger;

/**
 * @OA\Post(
 *     path="/api/posts/{post}/comments",
 *     summary="對文章新增留言",
 *     tags={"Comment"},
 *     description="已登入之使用者才能對文章新增留言",
 *     security={{"bearerAuth":{}}},
 * 
 *     @OA\Parameter(
 *         name="post",
 *         in="path",
 *         description="文章 ID",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ), 
 * 
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"content"},
 *             @OA\Property(property="content", type="string", example="這是第一則留言") 
 *         )
 *     ),
 * 
 *     @OA\Response(
 *         response=201,
 *         description="新增留言成功",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="status", type="integer", example=201),
 *             @OA\Property(property="message", type="string", example="新增留言成功"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=10),
 *                 @OA\Property(property="content", type="string", example="這是一則留言"),
 *                 @OA\Property(property="user_id", type="integer", example=5),
 *                 @OA\Property(property="post_id", type="integer", example=1),
 *                 @OA\Property(property="created_at", type="string", example="2025-05-30 20:18:00"),
 *                 @OA\Property(property="updated_at", type="string", example="2025-05-30 20:18:00")
 *             )
 *         )
 *     ),
 * 
 *     @OA\Response(
 *         response=404,
 *         description="文章不存在，無法新增留言",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="status", type="integer", example=404),
 *             @OA\Property(property="message", type="string", example="文章不存在，無法新增留言"),
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
 *                     property="content",
 *                     type="array",
 *                     @OA\Items(type="string", example="留言內容不得為空")
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
 * ),
 * 
 *  * @OA\Put(
 *     path="/api/posts/{post}/comments/{comment}",
 *     summary="修改留言",
 *     description="使用者可修改自己對文章的留言，需通過權限與資料一致性驗證（該留言必須屬於該文章）",
 *     tags={"Comment"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="post",
 *         in="path",
 *         required=true,
 *         description="文章 ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Parameter(
 *         name="comment",
 *         in="path",
 *         required=true,
 *         description="留言 ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"content"},
 *             @OA\Property(property="content", type="string", example="這是我修改後的留言內容")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="留言修改成功",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="修改留言成功"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=3),
 *                 @OA\Property(property="content", type="string", example="這是我修改後的留言內容"),
 *                 @OA\Property(property="user_id", type="integer", example=5),
 *                 @OA\Property(property="post_id", type="integer", example=1),
 *                 @OA\Property(property="created_at", type="string", example="2025-05-30 12:00:00"),
 *                 @OA\Property(property="updated_at", type="string", example="2025-05-30 13:00:00")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="留言不屬於該文章或無權限修改留言",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="status", type="integer", example=403),
 *             @OA\Property(property="message", type="string", example="你沒有權限修改留言 或 此留言不屬於該文章，故無法更改"),
 *             @OA\Property(property="data", type="string", nullable=true, example=null)
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="找不到留言或文章",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="status", type="integer", example=404),
 *             @OA\Property(property="message", type="string", example="該留言不存在 或 找不到該文章"),
 *             @OA\Property(property="data", type="string", nullable=true, example=null)
 *         )
 *     ),
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
 *                     property="content",
 *                     type="array",
 *                     @OA\Items(type="string", example="留言內容不得為空")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="伺服器錯誤",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="message", type="string", example="伺服器錯誤，請稍後再試"),
 *             @OA\Property(property="data", type="string", nullable=true, example=null)
 *         )
 *     )
 * )
 * 
 */

class Comment {}