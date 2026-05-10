<?php

namespace App\Services;

use App\Repositories\PostRepository;
use Illuminate\Support\Facades\Redis;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class PostViewService
 *
 * 負責處理文章觀看次數相關邏輯（使用 Redis 緩存）
 */
class PostViewService
{
    // Redis key 前綴
    private const REDIS_VIEW_KEY_PREFIX = 'post:views:';
    private const REDIS_VIEW_RECORD_PREFIX = 'post:view_record:';

    // 同一 IP 重複觀看的冷卻時間（秒）
    private const VIEW_COOLDOWN = 300; // 5 分鐘

    public function __construct(
        private PostRepository $postRepository
    ) {}

    /**
     * 記錄文章觀看（增加 Redis 中的計數）
     *
     * @param int $postId 文章 ID
     * @param string|null $ipAddress 使用者 IP（用於防刷）
     * @return bool
     * @throws ModelNotFoundException
     */
    public function recordView(int $postId, ?string $ipAddress = null): bool
    {
        // 檢查文章是否存在且已發布
        $this->postRepository->findPublishedPostById($postId);

        // 防刷機制：檢查同一 IP 是否在冷卻時間內
        if ($ipAddress && $this->isRecentlyViewed($postId, $ipAddress)) {
            return false; // 不重複計數
        }

        // 增加 Redis 中的觀看次數
        $viewKey = self::REDIS_VIEW_KEY_PREFIX . $postId;
        Redis::incr($viewKey);

        // 記錄此次觀看（用於防刷）
        if ($ipAddress) {
            $this->recordViewTimestamp($postId, $ipAddress);
        }

        return true;
    }

    /**
     * 取得文章的觀看次數（DB 基礎值 + Redis 增量值）
     *
     * @param int $postId 文章 ID
     * @param int $dbViewsCount 資料庫中的觀看次數
     * @return int
     */
    public function getViewsCount(int $postId, int $dbViewsCount = 0): int
    {
        $viewKey = self::REDIS_VIEW_KEY_PREFIX . $postId;
        $redisIncrement = (int) Redis::get($viewKey) ?: 0;

        return $dbViewsCount + $redisIncrement;
    }

    /**
     * 取得所有文章的觀看次數（批次處理，用於列表頁）
     *
     * @param array $posts 文章陣列（包含 id 和 views_count）
     * @return array 文章 ID => 總觀看次數的對應
     */
    public function getViewsCountForPosts(array $posts): array
    {
        $result = [];

        foreach ($posts as $post) {
            $postId = $post->id;
            $dbViewsCount = $post->views_count ?? 0;
            $result[$postId] = $this->getViewsCount($postId, $dbViewsCount);
        }

        return $result;
    }

    /**
     * 同步 Redis 觀看次數到資料庫（用於排程任務）
     *
     * @return array 包含同步結果的陣列
     */
    public function syncViewsToDatabase(): array
    {
        $pattern = '*' . self::REDIS_VIEW_KEY_PREFIX . '*';  // 使用通配符包含 Laravel prefix
        $syncedCount = 0;
        $totalViews = 0;

        // 取得 Laravel Redis prefix
        $laravelPrefix = config('database.redis.options.prefix');

        // 取得原生 Redis 客戶端（phpredis）
        $redis = Redis::connection()->client();

        // 使用 phpredis 原生 SCAN 方法，避免阻塞 Redis
        $iterator = null;

        while ($keys = $redis->scan($iterator, $pattern, 100)) {
            foreach ($keys as $fullKey) {
                // 從完整 key 中提取文章 ID
                // 完整 key 格式：laravel_database_post:views:123
                if (preg_match('/post:views:(\d+)$/', $fullKey, $matches)) {
                    $postId = (int) $matches[1];

                    // 移除 Laravel prefix，得到應用層的 key
                    // 完整 key：laravel_database_post:views:123
                    // 應用層 key：post:views:123
                    $appKey = str_replace($laravelPrefix, '', $fullKey);

                    // 取得 Redis 中的增量值（使用應用層 key）
                    $incrementBy = (int) $redis->get($appKey);

                    if ($incrementBy > 0) {
                        try {
                            // 增加資料庫中的觀看次數
                            $this->postRepository->incrementViewsCount($postId, $incrementBy);

                            // 清空 Redis 中的計數（使用應用層 key）
                            $redis->del($appKey);

                            $syncedCount++;
                            $totalViews += $incrementBy;
                        } catch (\Exception $e) {
                            // 記錄錯誤但繼續處理其他 key（避免單筆失敗影響整體同步）
                            \Log::error("同步文章 {$postId} 觀看次數失敗: " . $e->getMessage());
                        }
                    }
                }
            }

            // 當 iterator 為 0 時表示遍歷完成
            if ($iterator === 0) {
                break;
            }
        }

        return [
            'synced_count' => $syncedCount,
            'total_views' => $totalViews,
            'message' => $syncedCount > 0
                ? "成功同步 {$syncedCount} 篇文章的觀看次數，共 {$totalViews} 次觀看"
                : '沒有需要同步的觀看次數'
        ];
    }

    /**
     * 檢查同一 IP 是否最近已觀看過該文章
     *
     * @param int $postId 文章 ID
     * @param string $ipAddress IP 位址
     * @return bool
     */
    private function isRecentlyViewed(int $postId, string $ipAddress): bool
    {
        $recordKey = self::REDIS_VIEW_RECORD_PREFIX . $postId . ':' . $ipAddress;
        return Redis::exists($recordKey) > 0;
    }

    /**
     * 記錄觀看時間戳（用於防刷）
     *
     * @param int $postId 文章 ID
     * @param string $ipAddress IP 位址
     * @return void
     */
    private function recordViewTimestamp(int $postId, string $ipAddress): void
    {
        $recordKey = self::REDIS_VIEW_RECORD_PREFIX . $postId . ':' . $ipAddress;
        Redis::setex($recordKey, self::VIEW_COOLDOWN, time());
    }
}
