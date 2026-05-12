<?php

namespace App\Swagger\Schemas\Coupon;

/**
 * @OA\Schema(
 *     schema="CouponResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="code", type="string", example="SAVE100"),
 *     @OA\Property(property="name", type="string", example="新用戶折扣券"),
 *     @OA\Property(property="description", type="string", nullable=true, example="新用戶首單折抵100元"),
 *     @OA\Property(property="discount_type", type="integer", example=1, description="1:固定金額 2:百分比"),
 *     @OA\Property(property="discount_type_text", type="string", example="固定金額"),
 *     @OA\Property(property="discount_value", type="integer", minimum=0, example=100),
 *     @OA\Property(property="discount_display", type="string", example="折抵 $100"),
 *     @OA\Property(property="min_order_amount", type="integer", minimum=0, example=500),
 *     @OA\Property(property="remaining_quantity", type="integer", example=95),
 *     @OA\Property(property="per_user_limit", type="integer", example=1),
 *     @OA\Property(property="start_at", type="string", format="date-time", nullable=true, example="2026-05-01 00:00:00"),
 *     @OA\Property(property="end_at", type="string", format="date-time", nullable=true, example="2026-05-31 23:59:59"),
 *     @OA\Property(property="is_claimable", type="boolean", example=true),
 *     @OA\Property(property="is_claimed", type="boolean", example=false, description="是否已領取（未登入時固定為 false）"),
 *     @OA\Property(property="can_claim", type="boolean", example=true, description="是否可領取（未登入時固定為 false）")
 * )
 */
class CouponResourceSchema {}
