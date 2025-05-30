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
 * )
 */

class Comment {}