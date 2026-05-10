<?php

namespace App\Swagger\Schemas\Comment;

/**
 * @OA\Schema(
 *     schema="FrontCommentResource",
 *     title="前台留言資源格式",
 *     description="前台公開展示的留言格式，包含留言者資訊",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="content", type="string", example="這是一則留言內容"),
 *     @OA\Property(
 *         property="user",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=5),
 *         @OA\Property(property="name", type="string", example="Eric Liao")
 *     ),
 *     @OA\Property(property="created_at", type="string", example="2025-05-21 10:30:00"),
 *     @OA\Property(property="updated_at", type="string", example="2025-05-21 10:40:00")
 * )
 */
class FrontCommentResourceSchema {}
