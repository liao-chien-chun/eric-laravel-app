<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Traits\FormatTimestamps;

class UserCouponResource extends JsonResource
{
    use FormatTimestamps;

    /**
     * 用戶優惠券資源（已領取的優惠券）
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'coupon' => [
                'id' => $this->coupon->id,
                'code' => $this->coupon->code,
                'name' => $this->coupon->name,
                'description' => $this->coupon->description,
                'discount_type' => $this->coupon->discount_type,
                'discount_type_text' => $this->getDiscountTypeText(),
                'discount_value' => $this->coupon->discount_value,
                'discount_display' => $this->getDiscountDisplay(),
                'min_order_amount' => $this->coupon->min_order_amount,
                'end_at' => $this->formatDateTime($this->coupon->end_at),
            ],
            'status' => $this->status,
            'status_text' => $this->getStatusText(),
            'claimed_at' => $this->formatDateTime($this->claimed_at),
            'used_at' => $this->formatDateTime($this->used_at),
        ];
    }

    /**
     * 取得折扣類型文字
     */
    private function getDiscountTypeText(): string
    {
        return match ($this->coupon->discount_type) {
            1 => '固定金額',
            2 => '百分比',
            default => '未知',
        };
    }

    /**
     * 取得折扣顯示文字
     */
    private function getDiscountDisplay(): string
    {
        if ($this->coupon->discount_type === 1) {
            return '折抵 $' . number_format($this->coupon->discount_value);
        }
        return '打 ' . (100 - $this->coupon->discount_value) . ' 折';
    }

    /**
     * 取得狀態文字
     */
    private function getStatusText(): string
    {
        return match ($this->status) {
            1 => '未使用',
            2 => '已使用',
            3 => '已過期',
            default => '未知',
        };
    }
}
