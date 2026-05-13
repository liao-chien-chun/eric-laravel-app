<?php

namespace App\Repositories;

use App\Models\Coupon;
use App\Models\UserCoupon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

/**
 * Class CouponRepository
 *
 * 處理與優惠券資料表(coupons, user_coupons) 有關的資料存取邏輯
 */
class CouponRepository
{
    /**
     * 建立優惠券
     *
     * @param array $data 優惠券資料
     * @return Coupon
     */
    public function createCoupon(array $data): Coupon
    {
        return Coupon::create($data);
    }

    /**
     * 依 ID 取得優惠券
     *
     * @param int $id
     * @return Coupon
     * @throws ModelNotFoundException
     */
    public function findCouponById(int $id, ?int $viewerUserId = null): Coupon
    {
        try {
            $query = Coupon::query();

            if ($viewerUserId) {
                $query->leftJoin('user_coupons as uc', function ($join) use ($viewerUserId) {
                    $join->on('uc.coupon_id', '=', 'coupons.id')
                        ->where('uc.user_id', '=', $viewerUserId);
                })
                ->select('coupons.*')
                ->selectRaw('CASE WHEN uc.id IS NULL THEN 0 ELSE 1 END as is_claimed');
            } else {
                $query->select('coupons.*')
                    ->selectRaw('0 as is_claimed');
            }

            $coupon = $query->where('coupons.id', $id)->firstOrFail();

            return $coupon;
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException("找不到該優惠券");
        }
    }

    /**
     * 依代碼取得優惠券
     *
     * @param string $code
     * @return Coupon|null
     */
    public function findCouponByCode(string $code): ?Coupon
    {
        return Coupon::where('code', $code)->first();
    }

    /**
     * 更新優惠券
     *
     * @param Coupon $coupon
     * @param array $data
     * @return bool
     */
    public function updateCoupon(Coupon $coupon, array $data): bool
    {
        return $coupon->update($data);
    }

    /**
     * 取得可領取的優惠券列表（分頁）
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getActiveCoupons(int $perPage = 15, ?int $viewerUserId = null)
    {
        $now = now();

        $query = Coupon::query();

        if ($viewerUserId) {
            $query->leftJoin('user_coupons as uc', function ($join) use ($viewerUserId) {
                $join->on('uc.coupon_id', '=', 'coupons.id')
                    ->where('uc.user_id', '=', $viewerUserId);
            })
            ->select('coupons.*')
            ->selectRaw('CASE WHEN uc.id IS NULL THEN 0 ELSE 1 END as is_claimed');
        } else {
            $query->select('coupons.*')
                ->selectRaw('0 as is_claimed');
        }

        return $query->where('coupons.status', Coupon::STATUS_ACTIVE)
            ->where('coupons.remaining_quantity', '>', 0)
            ->where(function ($query) use ($now) {
                $query->whereNull('coupons.start_at')
                    ->orWhere('coupons.start_at', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('coupons.end_at')
                    ->orWhere('coupons.end_at', '>=', $now);
            })
            ->orderBy('coupons.created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * 取得所有優惠券列表（後台用，分頁）
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllCoupons(int $perPage = 15)
    {
        return Coupon::orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * 檢查用戶是否已領取該優惠券
     *
     * @param int $userId
     * @param int $couponId
     * @return bool
     */
    public function hasUserClaimedCoupon(int $userId, int $couponId): bool
    {
        return UserCoupon::where('user_id', $userId)
            ->where('coupon_id', $couponId)
            ->exists();
    }

    /**
     * 取得用戶領取某優惠券的數量
     *
     * @param int $userId
     * @param int $couponId
     * @return int
     */
    public function getUserCouponClaimCount(int $userId, int $couponId): int
    {
        return UserCoupon::where('user_id', $userId)
            ->where('coupon_id', $couponId)
            ->count();
    }

    /**
     * 建立用戶優惠券領取記錄
     *
     * @param int $userId
     * @param int $couponId
     * @return UserCoupon
     */
    public function createUserCoupon(int $userId, int $couponId): UserCoupon
    {
        return UserCoupon::create([
            'user_id' => $userId,
            'coupon_id' => $couponId,
            'claimed_at' => now(),
            'status' => UserCoupon::STATUS_UNUSED,
        ]);
    }

    /**
     * 扣減優惠券庫存（使用樂觀鎖）
     *
     * @param int $couponId
     * @return bool
     */
    public function decrementCouponQuantity(int $couponId): bool
    {
        $affected = Coupon::where('id', $couponId)
            ->where('remaining_quantity', '>', 0)
            ->decrement('remaining_quantity');

        return $affected > 0;
    }

    /**
     * 取得用戶的優惠券列表（分頁）
     *
     * @param int $userId
     * @param int|null $status
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getUserCoupons(int $userId, ?int $status = null, int $perPage = 15)
    {
        $query = UserCoupon::where('user_id', $userId)
            ->with('coupon')
            ->join('coupons', 'coupons.id', '=', 'user_coupons.coupon_id')
            ->select('user_coupons.*');

        if ($status !== null) {
            $query->where('user_coupons.status', $status);
        }

        return $query->orderByRaw('CASE WHEN coupons.end_at IS NULL THEN 1 ELSE 0 END ASC')
            ->orderBy('coupons.end_at', 'asc')
            ->orderBy('user_coupons.claimed_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * 使用資料庫交易領取優惠券（備用方案，無 Redis 時使用）
     *
     * @param int $userId
     * @param Coupon $coupon
     * @return UserCoupon
     * @throws \Exception
     */
    public function claimCouponWithTransaction(int $userId, Coupon $coupon): UserCoupon
    {
        return DB::transaction(function () use ($userId, $coupon) {
            // 使用悲觀鎖鎖定優惠券記錄
            $lockedCoupon = Coupon::where('id', $coupon->id)
                ->lockForUpdate()
                ->first();

            if (!$lockedCoupon || $lockedCoupon->remaining_quantity <= 0) {
                throw new \Exception('優惠券已被搶完', 422);
            }

            // 扣減庫存
            $lockedCoupon->decrement('remaining_quantity');

            // 建立領取記錄
            return $this->createUserCoupon($userId, $coupon->id);
        });
    }
}
