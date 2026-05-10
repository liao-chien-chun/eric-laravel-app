<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Traits\FormatTimestamps;

/**
 * 前台留言資源
 *
 * 用於前台公開展示文章的留言，包含留言者資訊
 */
class FrontCommentResource extends JsonResource
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
            'content' => $this->content,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'created_at' => $this->formatDateTime($this->created_at),
            'updated_at' => $this->formatDateTime($this->updated_at),
        ];
    }
}
