<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Traits\FormatTimestamps;

class ShortUrlResource extends JsonResource
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
            'short_code' => $this->short_code,
            'short_url' => url('/r/' . $this->short_code),
            'original_url' => $this->original_url,
            'click_count' => $this->click_count,
            'expired_at'   => $this->formatDateTime($this->expired_at),
            'created_at'   => $this->formatDateTime($this->created_at),
            'updated_at'   => $this->formatDateTime($this->updated_at),
        ];
    }
}
