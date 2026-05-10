<?php

namespace App\Swagger\Schemas\Comment;

/**
 * @OA\Schema(
 *     schema="FrontCommentListResponse",
 *     title="前台留言列表回應格式",
 *     description="前台取得文章留言列表的回應格式（包含分頁資訊）",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="status", type="integer", example=200),
 *     @OA\Property(property="message", type="string", example="留言列表取得成功"),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(
 *             property="comments",
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/FrontCommentResource")
 *         ),
 *         @OA\Property(
 *             property="pagination",
 *             type="object",
 *             @OA\Property(property="current_page", type="integer", example=1),
 *             @OA\Property(property="per_page", type="integer", example=20),
 *             @OA\Property(property="total", type="integer", example=100),
 *             @OA\Property(property="last_page", type="integer", example=5),
 *             @OA\Property(property="has_more", type="boolean", example=true, description="是否還有更多資料，方便前端判斷")
 *         )
 *     )
 * )
 */
class FrontCommentListResponseSchema {}
