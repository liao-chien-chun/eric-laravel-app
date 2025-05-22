<?php

namespace App\Swagger\Schemas\Post;

/**
 * @OA\Schema(
 *     schema="StorePostRequest",
 *     title="新增文章請求資料",
 *     description="用於新增文章的欄位",
 *     required={"title", "content", "status"},
 *     @OA\Property(property="title", type="string", example="我的第一篇文章"),
 *     @OA\Property(property="content", type="string", example="這是一篇範例內容。"),
 *     @OA\Property(property="status", type="integer", enum={1,2,3}, description="狀態：1=草稿, 2=發布, 3=隱藏", example=1)
 * )
 */
class StorePostRequestSchema {}