<?php

namespace App\Swagger\Schemas\Coupon;

/**
 * @OA\Schema(
 *     schema="GetCouponsResponse",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="status", type="integer", example=200),
 *     @OA\Property(property="message", type="string", example="優惠券列表取得成功"),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(
 *             property="items",
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/CouponResource")
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
class GetCouponsResponseSchema {}
