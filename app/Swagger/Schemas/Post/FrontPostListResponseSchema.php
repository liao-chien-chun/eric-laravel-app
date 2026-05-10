<?php

namespace App\Swagger\Schemas\Post;

/**
 * @OA\Schema(
 *     schema="FrontPostListResponse",
 *     title="前台文章列表回應格式",
 *     description="前台取得文章列表的回應格式（包含分頁資訊）",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="status", type="integer", example=200),
 *     @OA\Property(property="message", type="string", example="文章列表取得成功"),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(
 *             property="posts",
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/FrontPostResource")
 *         ),
 *         @OA\Property(
 *             property="pagination",
 *             type="object",
 *             @OA\Property(property="current_page", type="integer", example=1),
 *             @OA\Property(property="per_page", type="integer", example=15),
 *             @OA\Property(property="total", type="integer", example=50),
 *             @OA\Property(property="last_page", type="integer", example=4)
 *         )
 *     )
 * )
 */
class FrontPostListResponseSchema {}
