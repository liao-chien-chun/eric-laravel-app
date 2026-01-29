<?php

namespace App\Swagger\Schemas\Item;

/**
 * @OA\Schema(
 *     schema="CreateItemResponse",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="status", type="integer", example=201),
 *     @OA\Property(property="message", type="string", example="商品建立成功"),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="item_no", type="string", example="ITEM0000000001"),
 *         @OA\Property(property="name", type="string", example="iPhone 15 Pro"),
 *         @OA\Property(property="description", type="string", nullable=true, example="最新款 iPhone，256GB 空間"),
 *         @OA\Property(property="price", type="integer", example=35900),
 *         @OA\Property(property="stock", type="integer", example=100),
 *         @OA\Property(property="status", type="integer", example=2, description="1:草稿 2:上架 3:下架"),
 *         @OA\Property(property="image", type="string", nullable=true, example="/images/iphone15pro.jpg"),
 *         @OA\Property(
 *             property="user",
 *             type="object",
 *             @OA\Property(property="user_id", type="integer", example=5),
 *             @OA\Property(property="name", type="string", example="系統管理員")
 *         ),
 *         @OA\Property(property="category_id", type="integer", nullable=true, example=null),
 *         @OA\Property(property="brand_id", type="integer", nullable=true, example=null),
 *         @OA\Property(property="created_at", type="string", format="date-time", example="2026-01-29 23:55:58"),
 *         @OA\Property(property="updated_at", type="string", format="date-time", example="2026-01-29 23:55:58")
 *     )
 * )
 */

class CreateItemResponseSchema {}
