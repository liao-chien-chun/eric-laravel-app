<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Traits\FormatTimestamps;

/**
 * 前台文章資源
 *
 * 用於前台公開展示已發布的文章，不包含敏感資訊如狀態
 */
class FrontPostResource extends JsonResource
{
    use FormatTimestamps;

    /**
     * 將資源轉換為陣列格式
     *
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name
            ],
            'comments_count' => $this->comments_count ?? 0, // 留言數量
            'views_count' => $this->views_count ?? 0, // 觀看次數（會在 Service 層合併 Redis 的值）
            'created_at' => $this->formatDateTime($this->created_at),
            'updated_at' => $this->formatDateTime($this->updated_at),
        ];
    }
}
