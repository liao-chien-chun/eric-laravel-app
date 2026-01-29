<?php

namespace App\Swagger\Schemas\Item;

/**
 * @OA\Schema(
 *     schema="CreateItemValidationError",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(property="status", type="integer", example=422),
 *     @OA\Property(property="message", type="string", example="驗證失敗"),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         @OA\Property(
 *             property="name",
 *             type="array",
 *             @OA\Items(type="string", example="商品名稱必填")
 *         ),
 *         @OA\Property(
 *             property="price",
 *             type="array",
 *             @OA\Items(type="string", example="商品價格必填")
 *         ),
 *         @OA\Property(
 *             property="stock",
 *             type="array",
 *             @OA\Items(type="string", example="庫存量為必填")
 *         ),
 *         @OA\Property(
 *             property="status",
 *             type="array",
 *             @OA\Items(type="string", example="商品狀態必填")
 *         )
 *     )
 * )
 */

class CreateItemValidationErrorSchema {}
