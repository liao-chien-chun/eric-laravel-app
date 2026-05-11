<?php

namespace App\Jobs;

use App\Models\Post;
use App\Services\PostSearchService;
use App\Services\PostViewService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncSinglePostToES implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 任務最多嘗試次數
     *
     * @var int
     */
    public $tries = 3;

    /**
     * 任務逾時時間（秒）
     *
     * @var int
     */
    public $timeout = 30;

    /**
     * 重試之間的等待時間（秒）
     *
     * @var int
     */
    public $backoff = 10;

    /**
     * Create a new job instance.
     *
     * @param int $postId 文章 ID
     * @param string $action 操作類型 (index: 新增/更新, delete: 刪除)
     */
    public function __construct(
        public int $postId,
        public string $action = 'index'
    ) {}

    /**
     * Execute the job.
     */
    public function handle(
        PostSearchService $postSearchService,
        PostViewService $postViewService
    ): void {
        try {
            if ($this->action === 'delete') {
                // 從 ES 刪除文章
                $postSearchService->deletePost($this->postId);
                Log::info("[SyncPostToES] 文章已從 ES 刪除", ['post_id' => $this->postId]);
            } else {
                // 從資料庫取得文章資料
                $post = Post::with('user:id,name')
                    ->withCount('comments')
                    ->find($this->postId);

                // 如果文章不存在，記錄警告並結束
                if (!$post) {
                    Log::warning("[SyncPostToES] 文章不存在，無法同步", ['post_id' => $this->postId]);
                    return;
                }

                // 合併 Redis 觀看次數
                $post->views_count = $postViewService->getViewsCount($this->postId, $post->views_count);

                // 準備同步資料
                $postData = [
                    'id' => $post->id,
                    'title' => $post->title,
                    'content' => $post->content,
                    'status' => $post->status,
                    'user_id' => $post->user_id,
                    'views_count' => $post->views_count ?? 0,
                    'comments_count' => $post->comments_count ?? 0,
                    'created_at' => $post->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $post->updated_at->format('Y-m-d H:i:s'),
                ];

                // 同步到 ES
                $postSearchService->indexPost($postData);
                Log::info("[SyncPostToES] 文章已同步到 ES", [
                    'post_id' => $this->postId,
                    'status' => $post->status,
                    'views_count' => $post->views_count,
                    'comments_count' => $post->comments_count
                ]);
            }
        } catch (Exception $e) {
            Log::error("[SyncPostToES] 同步失敗", [
                'post_id' => $this->postId,
                'action' => $this->action,
                'error' => $e->getMessage()
            ]);

            // 重新拋出異常以觸發重試機制
            throw $e;
        }
    }

    /**
     * 任務失敗時的處理
     */
    public function failed(Exception $exception): void
    {
        Log::error("[SyncPostToES] 任務最終失敗", [
            'post_id' => $this->postId,
            'action' => $this->action,
            'error' => $exception->getMessage()
        ]);
    }
}
