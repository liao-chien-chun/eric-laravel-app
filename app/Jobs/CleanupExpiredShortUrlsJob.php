<?php

namespace App\Jobs;

use App\Services\ShortUrlService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CleanupExpiredShortUrlsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 任務最多嘗試次數
     *
     * @var int
     */
    public $tries = 3;

    /**
     * 任務執行超時時間（秒）
     *
     * @var int
     */
    public $timeout = 300;

    /**
     * 任務失敗前的最大重試延遲（秒）
     *
     * @var int
     */
    public $backoff = 60;

    /**
     * Execute the job.
     */
    public function handle(ShortUrlService $shortUrlService): void
    {
        Log::info('[CleanupExpiredShortUrls] 開始清除已過期短網址');

        try {
            $deletedCount = $shortUrlService->cleanupExpired();

            Log::info('[CleanupExpiredShortUrls] 清除完成', [
                'deleted_count' => $deletedCount,
            ]);
        } catch (\Exception $e) {
            Log::error('[CleanupExpiredShortUrls] 清除失敗', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * 任務失敗時的處理
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('[CleanupExpiredShortUrls] 任務最終失敗', [
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);
    }
}
