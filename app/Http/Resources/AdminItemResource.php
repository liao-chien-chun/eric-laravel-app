<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Traits\FormatTimestamps;

class AdminItemResource extends JsonResource
{
    use FormatTimestamps;

    /**
     * 後台管理者使用的商品資源
     * 包含完整的商品資訊，包括建立者、所有狀態等
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'item_no' => $this->item_no,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock,
            'status' => $this->status, // 1:草稿, 2:上架, 3:下架
            'status_text' => $this->getStatusText(),
            'image' => $this->image,
            'user' => [
                'user_id' => $this->user->id,
                'name' => $this->user->name
            ],
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'created_at' => $this->formatDateTime($this->created_at),
            'updated_at' => $this->formatDateTime($this->updated_at),
        ];
    }
}
