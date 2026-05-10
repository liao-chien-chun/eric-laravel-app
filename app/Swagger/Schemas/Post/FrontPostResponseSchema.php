<?php

namespace App\Swagger\Schemas\Post;

/**
 * @OA\Schema(
 *     schema="FrontPostResponse",
 *     title="前台單一文章回應格式",
 *     description="前台取得單一文章的回應格式",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="status", type="integer", example=200),
 *     @OA\Property(property="message", type="string", example="文章取得成功"),
 *     @OA\Property(
 *         property="data",
 *         ref="#/components/schemas/FrontPostResource"
 *     )
 * )
 */
class FrontPostResponseSchema {}
