<?php

namespace App\Swagger\Schemas\Post;

/**
 * @OA\Schema(
 *     schema="PostResource",
 *     title="文章資源格式",
 *     description="回傳單篇文章格式",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="我的第一篇文章"),
 *     @OA\Property(property="content", type="string", example="這是一篇文章內容"),
 *     @OA\Property(property="status", type="integer", description="狀態：1=草稿, 2=發布, 3=隱藏", example=1),
 *     @OA\Property(
 *         property="user",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=5),
 *         @OA\Property(property="name", type="string", example="Eric Liao"),
 *         @OA\Property(property="email", type="string", example="eric@example.com")
 *     ),
 *     @OA\Property(property="created_at", type="string", example="2025-05-21 10:30:00"),
 *     @OA\Property(property="updated_at", type="string", example="2025-05-21 10:40:00")
 * )
 */
class PostResourceSchema {}