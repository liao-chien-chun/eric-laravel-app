<?php

namespace App\Swagger;

/**
 * @OA\Get(
 *     path="/api/coupons",
 *     summary="取得可領取的優惠券列表",
 *     tags={"Coupon"},
 *     description="前台取得可領取的優惠券列表（不需登入）",
 *     operationId="getCoupons",
 *
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         description="每頁筆數，預設 15",
 *         required=false,
 *         @OA\Schema(type="integer", default=15)
 *     ),
 *
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="頁碼，預設 1",
 *         required=false,
 *         @OA\Schema(type="integer", default=1)
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="優惠券列表取得成功",
 *         @OA\JsonContent(ref="#/components/schemas/GetCouponsResponse")
 *     ),
 *
 *     @OA\Response(
 *         response=500,
 *         description="伺服器錯誤",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="message", type="string", example="伺服器錯誤，請稍後再試"),
 *             @OA\Property(property="data", type="string", nullable=true, example=null)
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/coupons/{coupon}",
 *     summary="取得優惠券詳情",
 *     tags={"Coupon"},
 *     description="前台取得單一優惠券詳情（不需登入）",
 *     operationId="getCouponDetail",
 *
 *     @OA\Parameter(
 *         name="coupon",
 *         in="path",
 *         description="優惠券 ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="優惠券詳情取得成功",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="優惠券詳情取得成功"),
 *             @OA\Property(property="data", ref="#/components/schemas/CouponResource")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="找不到優惠券",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="status", type="integer", example=404),
 *             @OA\Property(property="message", type="string", example="找不到該優惠券"),
 *             @OA\Property(property="data", type="string", nullable=true, example=null)
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/coupons/{coupon}/claim",
 *     summary="搶優惠券",
 *     tags={"Coupon"},
 *     description="領取優惠券（需登入），使用 Redis 處理併發",
 *     operationId="claimCoupon",
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Parameter(
 *         name="coupon",
 *         in="path",
 *         description="優惠券 ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *
 *     @OA\Response(
 *         response=201,
 *         description="優惠券領取成功",
 *         @OA\JsonContent(ref="#/components/schemas/ClaimCouponResponse")
 *     ),
 *
 *     @OA\Response(
 *         response=401,
 *         description="尚未授權",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="status", type="integer", example=401),
 *             @OA\Property(property="message", type="string", example="尚未授權，請登入"),
 *             @OA\Property(property="data", type="string", nullable=true, example=null)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="找不到優惠券",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="status", type="integer", example=404),
 *             @OA\Property(property="message", type="string", example="找不到該優惠券"),
 *             @OA\Property(property="data", type="string", nullable=true, example=null)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=422,
 *         description="領取失敗",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="status", type="integer", example=422),
 *             @OA\Property(property="message", type="string", example="優惠券已被搶完"),
 *             @OA\Property(property="data", type="string", nullable=true, example=null)
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/coupons/my",
 *     summary="取得我的優惠券列表",
 *     tags={"Coupon"},
 *     description="取得當前用戶已領取的優惠券列表（需登入）",
 *     operationId="getMyCoupons",
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Parameter(
 *         name="status",
 *         in="query",
 *         description="狀態篩選 (1:未使用 2:已使用 3:已過期)",
 *         required=false,
 *         @OA\Schema(type="integer", enum={1, 2, 3})
 *     ),
 *
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         description="每頁筆數，預設 15",
 *         required=false,
 *         @OA\Schema(type="integer", default=15)
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="我的優惠券列表取得成功",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="我的優惠券列表取得成功"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(
 *                     property="items",
 *                     type="array",
 *                     @OA\Items(ref="#/components/schemas/UserCouponResource")
 *                 ),
 *                 @OA\Property(
 *                     property="pagination",
 *                     type="object",
 *                     @OA\Property(property="current_page", type="integer", example=1),
 *                     @OA\Property(property="per_page", type="integer", example=15),
 *                     @OA\Property(property="total", type="integer", example=5),
 *                     @OA\Property(property="last_page", type="integer", example=1)
 *                 )
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=401,
 *         description="尚未授權",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="status", type="integer", example=401),
 *             @OA\Property(property="message", type="string", example="尚未授權，請登入"),
 *             @OA\Property(property="data", type="string", nullable=true, example=null)
 *         )
 *     )
 * )
 */

class Coupon {}
