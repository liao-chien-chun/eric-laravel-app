<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    /**
     * 折扣類型常數
     */
    const DISCOUNT_TYPE_FIXED = 1;      // 固定金額
    const DISCOUNT_TYPE_PERCENTAGE = 2; // 百分比

    /**
     * 狀態常數
     */
    const STATUS_DRAFT = 1;    // 草稿
    const STATUS_ACTIVE = 2;   // 進行中
    const STATUS_ENDED = 3;    // 已結束

    /**
     * 可被批量指派的欄位
     */
    protected $fillable = [
        'code',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'min_order_amount',
        'total_quantity',
        'remaining_quantity',
        'per_user_limit',
        'start_at',
        'end_at',
        'status',
    ];

    /**
     * 型別轉換
     */
    protected $casts = [
        'discount_value' => 'integer',
        'min_order_amount' => 'integer',
        'total_quantity' => 'integer',
        'remaining_quantity' => 'integer',
        'per_user_limit' => 'integer',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'status' => 'integer',
        'discount_type' => 'integer',
    ];

    /**
     * 優惠券被多個用戶領取
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userCoupons()
    {
        return $this->hasMany(UserCoupon::class);
    }

    /**
     * 領取此優惠券的用戶
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_coupons')
            ->withPivot(['claimed_at', 'used_at', 'status'])
            ->withTimestamps();
    }

    /**
     * 檢查優惠券是否可領取
     *
     * @return bool
     */
    public function isClaimable(): bool
    {
        $now = now();
        
        return $this->status === self::STATUS_ACTIVE
            && $this->remaining_quantity > 0
            && ($this->start_at === null || $now->gte($this->start_at))
            && ($this->end_at === null || $now->lte($this->end_at));
    }
}
