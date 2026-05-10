<?php

namespace App\Jobs;

use App\Services\PostViewService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncPostViewsToDatabase implements ShouldQueue
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
     * Create a new job instance.
     */
    public function __construct()
    {
        // 可以指定使用特定的 queue
        // $this->onQueue('sync');
    }

    /**
     * Execute the job.
     */
    public function handle(PostViewService $postViewService): void
    {
        Log::info('[SyncPostViews] 開始同步 Redis 觀看次數到資料庫');

        try {
            $result = $postViewService->syncViewsToDatabase();

            Log::info('[SyncPostViews] 同步完成', [
                'synced_count' => $result['synced_count'],
                'total_views' => $result['total_views'],
                'message' => $result['message']
            ]);
        } catch (\Exception $e) {
            Log::error('[SyncPostViews] 同步失敗', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // 重新拋出異常，讓 Queue 系統處理重試
            throw $e;
        }
    }

    /**
     * 任務失敗時的處理
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('[SyncPostViews] 任務最終失敗', [
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);

        // 可以在這裡發送通知給管理員
        // Notification::route('mail', config('mail.admin'))
        //     ->notify(new SyncPostViewsFailedNotification($exception));
    }
}
