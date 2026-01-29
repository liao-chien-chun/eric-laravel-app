<?php

namespace App\Swagger\Schemas\Item;

/**
 * @OA\Schema(
 *     schema="CreateItemRequest",
 *     required={"name", "price", "stock", "status"},
 *     @OA\Property(property="name", type="string", maxLength=100, example="iPhone 15 Pro", description="商品名稱"),
 *     @OA\Property(property="description", type="string", example="最新款 iPhone，256GB 空間", nullable=true, description="商品描述"),
 *     @OA\Property(property="price", type="integer", minimum=0, example=35900, description="商品價格（整數）"),
 *     @OA\Property(property="stock", type="integer", minimum=0, example=100, description="庫存數量"),
 *     @OA\Property(
 *         property="status",
 *         type="integer",
 *         enum={1, 2},
 *         example=2,
 *         description="商品狀態 (1:草稿 2:上架)"
 *     ),
 *     @OA\Property(property="image", type="string", example="/images/iphone15pro.jpg", nullable=true, description="商品主圖片路徑")
 * )
 */

class CreateItemRequestSchema {}
