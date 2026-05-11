<?php

namespace App\Listeners;

use App\Events\PostChanged;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ClearPostCache
{
    /**
     * Handle the event.
     */
    public function handle(PostChanged $event): void
    {
        $cacheKey = config('post_cache.key_prefix', 'post:published:') . $event->postId;

        Cache::forget($cacheKey);

        Log::info('[PostCache] 清除文章緩存', [
            'post_id' => $event->postId,
            'cache_key' => $cacheKey,
        ]);
    }
}
