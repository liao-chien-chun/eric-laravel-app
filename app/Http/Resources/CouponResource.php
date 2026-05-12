<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Traits\FormatTimestamps;

class CouponResource extends JsonResource
{
    use FormatTimestamps;

    /**
     * 前台優惠券資源（用於展示可領取的優惠券）
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $isClaimed = (bool) ($this->is_claimed ?? false);
        $isAuthenticated = $request->user() !== null;
        $isClaimable = $this->isClaimable();

        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'discount_type' => $this->discount_type,
            'discount_type_text' => $this->getDiscountTypeText(),
            'discount_value' => $this->discount_value,
            'discount_display' => $this->getDiscountDisplay(),
            'min_order_amount' => $this->min_order_amount,
            'remaining_quantity' => $this->remaining_quantity,
            'per_user_limit' => $this->per_user_limit,
            'start_at' => $this->formatDateTime($this->start_at),
            'end_at' => $this->formatDateTime($this->end_at),
            'is_claimable' => $isClaimable,
            // 回傳欄位固定存在：未登入時永遠 false
            'is_claimed' => $isAuthenticated ? $isClaimed : false,
            // 前端可直接用 can_claim 來決定按鈕狀態
            'can_claim' => $isAuthenticated && $isClaimable && !$isClaimed,
        ];
    }

    /**
     * 取得折扣類型文字
     */
    private function getDiscountTypeText(): string
    {
        return match ($this->discount_type) {
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
        if ($this->discount_type === 1) {
            return '折抵 $' . number_format($this->discount_value);
        }
        return '打 ' . (100 - $this->discount_value) . ' 折';
    }
}
