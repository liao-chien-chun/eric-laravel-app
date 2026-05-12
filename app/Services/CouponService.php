<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\UserCoupon;
use App\Repositories\CouponRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

/**
 * Class CouponService
 *
 * 負責處理優惠券業務邏輯，包含併發搶購處理
 */
class CouponService
{
    /**
     * Redis key 前綴
     */
    const REDIS_COUPON_STOCK_PREFIX = 'coupon:stock:';
    const REDIS_COUPON_CLAIMED_PREFIX = 'coupon:claimed:';

    public function __construct(
        private CouponRepository $couponRepository
    ) {}

    /**
     * 建立優惠券（排程使用）
     *
     * @param array $data
     * @return Coupon
     */
    public function createCoupon(array $data): Coupon
    {
        $data['remaining_quantity'] = $data['total_quantity'];
        $coupon = $this->couponRepository->createCoupon($data);

        // 初始化 Redis 庫存
        $this->initializeCouponStock($coupon);

        return $coupon;
    }

    /**
     * 初始化優惠券的 Redis 庫存
     *
     * @param Coupon $coupon
     * @return void
     */
    public function initializeCouponStock(Coupon $coupon): void
    {
        $stockKey = self::REDIS_COUPON_STOCK_PREFIX . $coupon->id;
        Redis::set($stockKey, $coupon->remaining_quantity);
    }

    /**
     * 搶優惠券（核心方法，使用 Redis 處理併發）
     *
     * @param int $couponId
     * @return UserCoupon
     * @throws \Exception
     */
    public function claimCoupon(int $couponId): UserCoupon
    {
        $userId = Auth::id();
        $coupon = $this->couponRepository->findCouponById($couponId);

        // 1. 基本驗證
        $this->validateCouponClaimable($coupon);

        // 2. 使用 Redis 原子操作扣減庫存（含每人上限判斷）
        $claimed = $this->claimWithRedis($userId, $coupon);

        if (!$claimed) {
            throw new \Exception('優惠券已被搶完', 422);
        }

        return $claimed;
    }

    /**
     * 驗證優惠券是否可領取
     */
    private function validateCouponClaimable(Coupon $coupon): void
    {
        if ($coupon->status !== Coupon::STATUS_ACTIVE) {
            throw new \Exception('優惠券活動未開始或已結束', 422);
        }

        $now = now();

        if ($coupon->start_at && $now->lt($coupon->start_at)) {
            throw new \Exception('優惠券活動尚未開始', 422);
        }

        if ($coupon->end_at && $now->gt($coupon->end_at)) {
            throw new \Exception('優惠券活動已結束', 422);
        }
    }

    /**
     * 使用 Redis 原子操作領取優惠券
     */
    private function claimWithRedis(int $userId, Coupon $coupon): ?UserCoupon
    {
        $stockKey = self::REDIS_COUPON_STOCK_PREFIX . $coupon->id;
        $claimedKey = self::REDIS_COUPON_CLAIMED_PREFIX . $coupon->id . ':' . $userId;

        // claimedKey 需要過期：
        // - 有 end_at：過期時間設為 end_at + 1 小時
        // - 無 end_at：預設保留 365 天，避免永久累積
        $claimedTtlSeconds = $coupon->end_at
            ? max(0, $coupon->end_at->diffInSeconds(now()) + 3600)
            : 60 * 60 * 24 * 365;

        // 使用 Lua 腳本確保原子性
        $luaScript = <<<'LUA'
            local stockKey = KEYS[1]
            local claimedKey = KEYS[2]
            local perUserLimit = tonumber(ARGV[1])
            local claimedTtl = tonumber(ARGV[2])
            
            -- 檢查用戶領取次數
            local claimedCount = tonumber(redis.call('GET', claimedKey) or '0')
            if claimedCount >= perUserLimit then
                return -1 -- 已達領取上限
            end
            
            -- 檢查並扣減庫存
            local stock = tonumber(redis.call('GET', stockKey) or '0')
            if stock <= 0 then
                return 0 -- 庫存不足
            end
            
            -- 扣減庫存
            redis.call('DECR', stockKey)
            -- 增加用戶領取計數
            redis.call('INCR', claimedKey)

            -- 設定領取紀錄過期時間（避免永久累積）
            if claimedTtl and claimedTtl > 0 then
                redis.call('EXPIRE', claimedKey, claimedTtl)
            end
            
            return 1 -- 成功
        LUA;

        $result = Redis::eval($luaScript, 2, $stockKey, $claimedKey, $coupon->per_user_limit, $claimedTtlSeconds);

        if ($result === -1) {
            throw new \Exception('您已達到此優惠券的領取上限', 422);
        }

        if ($result === 0) {
            return null;
        }

        try {
            // Redis 操作成功，寫入資料庫
            $this->couponRepository->decrementCouponQuantity($coupon->id);
            $userCoupon = $this->couponRepository->createUserCoupon($userId, $coupon->id);

            return $userCoupon->load('coupon');
        } catch (\Exception $e) {
            // 資料庫操作失敗，回滾 Redis（避免 claimedKey 變成負數）
            $rollbackLua = <<<'LUA'
                local stockKey = KEYS[1]
                local claimedKey = KEYS[2]

                redis.call('INCR', stockKey)

                local v = tonumber(redis.call('GET', claimedKey) or '0')
                if v <= 1 then
                    redis.call('DEL', claimedKey)
                else
                    redis.call('DECR', claimedKey)
                end

                return 1
            LUA;

            Redis::eval($rollbackLua, 2, $stockKey, $claimedKey);

            Log::error('優惠券領取資料庫寫入失敗', [
                'user_id' => $userId,
                'coupon_id' => $coupon->id,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception('領取失敗，請稍後再試', 500);
        }
    }

    /**
     * 取得可領取的優惠券列表
     */
    public function getActiveCoupons(int $perPage = 15, ?int $viewerUserId = null)
    {
        return $this->couponRepository->getActiveCoupons($perPage, $viewerUserId);
    }

    /**
     * 取得用戶的優惠券列表
     */
    public function getUserCoupons(?int $status = null, int $perPage = 15)
    {
        $userId = Auth::id();
        return $this->couponRepository->getUserCoupons($userId, $status, $perPage);
    }

    /**
     * 取得優惠券詳情
     */
    public function getCouponDetail(int $couponId, ?int $viewerUserId = null): Coupon
    {
        return $this->couponRepository->findCouponById($couponId, $viewerUserId);
    }
}
