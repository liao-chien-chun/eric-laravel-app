<?php

namespace App\Http\Resources\Traits;

use Illuminate\Support\Carbon;

trait FormatTimestamps
{
    /**
     * 將日期轉換為台北時間並格式化為字串
     *
     * @param \DateTimeInterface|string|null $datetime
     * @return string|null
     */
    protected function formatDateTime($datetime): ?string
    {
        if (!$datetime)
            return null;

        return Carbon::parse($datetime)
            ->timezone('Asia/Taipei')
            ->toDateTimeString(); 
    }
}