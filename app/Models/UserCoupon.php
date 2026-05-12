<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCoupon extends Model
{
    use HasFactory;

    /**
     * 狀態常數
     */
    const STATUS_UNUSED = 1;   // 未使用
    const STATUS_USED = 2;     // 已使用
    const STATUS_EXPIRED = 3;  // 已過期

    /**
     * 可被批量指派的欄位
     */
    protected $fillable = [
        'user_id',
        'coupon_id',
        'claimed_at',
        'used_at',
        'status',
    ];

    /**
     * 型別轉換
     */
    protected $casts = [
        'claimed_at' => 'datetime',
        'used_at' => 'datetime',
        'status' => 'integer',
    ];

    /**
     * 所屬用戶
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 所屬優惠券
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}
