<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CouponResource;
use App\Http\Resources\UserCouponResource;
use App\Services\CouponService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class CouponController extends Controller
{
    public function __construct(
        private CouponService $couponService
    ) {}

    /**
     * 取得可領取的優惠券列表（前台）
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 15);
            $viewerUserId = auth()->id();
            $coupons = $this->couponService->getActiveCoupons($perPage, $viewerUserId);

            return response()->json([
                'success' => true,
                'status' => Response::HTTP_OK,
                'message' => '優惠券列表取得成功',
                'data' => [
                    'items' => CouponResource::collection($coupons->items()),
                    'pagination' => [
                        'current_page' => $coupons->currentPage(),
                        'per_page' => $coupons->perPage(),
                        'total' => $coupons->total(),
                        'last_page' => $coupons->lastPage(),
                    ],
                ],
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('取得優惠券列表失敗', [
                'exception_code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => '伺服器錯誤，請稍後再試',
                'data' => null
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * 取得優惠券詳情（前台）
     *
     * @param int $coupon
     * @return JsonResponse
     */
    public function show(int $coupon): JsonResponse
    {
        try {
            $viewerUserId = auth()->id();
            $couponData = $this->couponService->getCouponDetail($coupon, $viewerUserId);

            return response()->json([
                'success' => true,
                'status' => Response::HTTP_OK,
                'message' => '優惠券詳情取得成功',
                'data' => new CouponResource($couponData),
            ], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'status' => Response::HTTP_NOT_FOUND,
                'message' => $e->getMessage(),
                'data' => null
            ], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            Log::error('取得優惠券詳情失敗', [
                'coupon_id' => $coupon,
                'exception_code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => '伺服器錯誤，請稍後再試',
                'data' => null
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * 搶優惠券（需登入）
     *
     * @param int $coupon
     * @return JsonResponse
     */
    public function claim(int $coupon): JsonResponse
    {
        try {
            $userCoupon = $this->couponService->claimCoupon($coupon);

            return response()->json([
                'success' => true,
                'status' => Response::HTTP_CREATED,
                'message' => '優惠券領取成功',
                'data' => new UserCouponResource($userCoupon),
            ], Response::HTTP_CREATED);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'status' => Response::HTTP_NOT_FOUND,
                'message' => $e->getMessage(),
                'data' => null
            ], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            // 業務錯誤（例如搶完、已達上限）維持 422；其餘一律視為系統錯誤
            if ((int) $e->getCode() === Response::HTTP_UNPROCESSABLE_ENTITY) {
                return response()->json([
                    'success' => false,
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'message' => $e->getMessage(),
                    'data' => null
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            Log::error('搶優惠券失敗', [
                'coupon_id' => $coupon,
                'exception_code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => '伺服器錯誤，請稍後再試',
                'data' => null
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * 取得我的優惠券列表（需登入）
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function myCoupons(Request $request): JsonResponse
    {
        try {
            $status = $request->input('status');
            $perPage = $request->input('per_page', 15);
            $userCoupons = $this->couponService->getUserCoupons($status, $perPage);

            return response()->json([
                'success' => true,
                'status' => Response::HTTP_OK,
                'message' => '我的優惠券列表取得成功',
                'data' => [
                    'items' => UserCouponResource::collection($userCoupons->items()),
                    'pagination' => [
                        'current_page' => $userCoupons->currentPage(),
                        'per_page' => $userCoupons->perPage(),
                        'total' => $userCoupons->total(),
                        'last_page' => $userCoupons->lastPage(),
                    ],
                ],
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('取得我的優惠券列表失敗', [
                'exception_code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => '伺服器錯誤，請稍後再試',
                'data' => null
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
