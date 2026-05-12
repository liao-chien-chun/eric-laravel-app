<?php

namespace App\Swagger\Schemas\Coupon;

/**
 * @OA\Schema(
 *     schema="UserCouponResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(
 *         property="coupon",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="code", type="string", example="SAVE100"),
 *         @OA\Property(property="name", type="string", example="新用戶折扣券"),
 *         @OA\Property(property="description", type="string", nullable=true, example="新用戶首單折抵100元"),
 *         @OA\Property(property="discount_type", type="integer", example=1),
 *         @OA\Property(property="discount_type_text", type="string", example="固定金額"),
 *         @OA\Property(property="discount_value", type="integer", minimum=0, example=100),
 *         @OA\Property(property="discount_display", type="string", example="折抵 $100"),
 *         @OA\Property(property="min_order_amount", type="integer", minimum=0, example=500),
 *         @OA\Property(property="end_at", type="string", format="date-time", nullable=true, example="2026-05-31 23:59:59")
 *     ),
 *     @OA\Property(property="status", type="integer", example=1, description="1:未使用 2:已使用 3:已過期"),
 *     @OA\Property(property="status_text", type="string", example="未使用"),
 *     @OA\Property(property="claimed_at", type="string", format="date-time", example="2026-05-13 10:30:00"),
 *     @OA\Property(property="used_at", type="string", format="date-time", nullable=true, example=null)
 * )
 */
class UserCouponResourceSchema {}
