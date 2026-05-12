<?php

namespace App\Swagger\Schemas\Coupon;

/**
 * @OA\Schema(
 *     schema="ClaimCouponResponse",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="status", type="integer", example=201),
 *     @OA\Property(property="message", type="string", example="優惠券領取成功"),
 *     @OA\Property(
 *         property="data",
 *         ref="#/components/schemas/UserCouponResource"
 *     )
 * )
 */
class ClaimCouponResponseSchema {}
