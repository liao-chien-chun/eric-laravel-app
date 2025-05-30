<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Traits\FormatTimestamps;

class CommentResource extends JsonResource
{
    use FormatTimestamps;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'user_id' => $this->user_id,
            'post_id' => $this->post_id,
            'created_at' => $this->formatDateTime($this->created_at),
            'updated_at' => $this->formatDateTime($this->updated_at),
        ];
    }
}
