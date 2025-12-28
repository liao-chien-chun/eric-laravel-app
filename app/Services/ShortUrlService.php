<?php 

namespace App\Services;

use App\Models\ShortUrl;
use App\Repositories\ShortUrlRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Exception;
use Illuminate\Database\Events\QueryExecuted;

/**
 * 短網址服務層
 */
class ShortUrlService
{
    public const CACHE_KEY_CODE_PREFIX = 'short:code:'; // 短碼 -> 短網址模型
    public const REDIS_KEY_CLICK_PREFIX = 'short:clicks:'; // 點擊累積（待回寫）

    public function __construct(private ShortUrlRepository $shortUrlRepository) {}

    /**
     * 建立短網址
     * 
     * @param int $userId 使用者 ID
     * @param array $payload original_url/short_code/expired_at
     * @return ShortUrl
     * @throws Exception
     */
    public function create(int $userId, array $payload): ShortUrl
    {
        $original = $payload['original_url'];
        $custom = $payload['short_code'] ?? null;
        $expired = $payload['expired_at'] ?? null;

        if ($custom !== null) {
            $this->assertCodeAllowed($custom);
            if ($this->shortUrlRepository->existsCode($custom)) {
                throw new Exception('該短碼已被使用', 422);
            }
            $code = $custom;
        } else {
            $code = $this->generateUniqueCode();
        }

        try {
            $short = DB::transaction(function () use ($userId, $original, $code, $expired) {
                return $this->shortUrlRepository->create([
                    'user_id'      => $userId,
                    'original_url' => $original,
                    'short_code'   => $code,
                    'click_count' => 0,
                    'expired_at'   => $expired,
                ]);
            });
        } catch (QueryException $e) {
            if ($custom === null) {
                // 碰撞重試一次
                $code = $this->generateUniqueCode();
                $short = $this->shortUrlRepository->create([
                    'user_id'      => $userId,
                    'original_url' => $original,
                    'short_code'   => $code,
                    'click_count' => 0,
                    'expired_at'   => $expired,
                ]);
            } else {
                throw new Exception('該短碼已被使用', 422);
            }
        }

        // 預先快取
        $this->cacheShort($short);

        return $short;
    }

    /**
     * 保留字檢查
     * 
     * @param string $code 短碼
     * @return void
     * @throws Exception
     */
    protected function assertCodeAllowed(string $code): void
    {
        $reserved = ['api', 'r', 'admin', 'login', 'logout'];
        if (in_array(strtolower($code), $reserved, true)) {
            throw new Exception('該短碼為系統保留字', 422);
        }
    }

    /**
     * 產生唯一短碼（base62）
     *
     * @return string
     */
    protected function generateUniqueCode(): string
    {
        for ($i = 0; $i < 5; $i++) {
            $code = $this->base62(random_bytes(6));
            $code = substr($code, 0, 8);
            if (!$this->shortUrlRepository->existsCode($code)) {
                return $code;
            }
        }
        return Str::random(10);
    }

    /**
     * Base62 編碼
     *
     * @param string $bytes 原始位元資料
     * @return string
     */
    protected function base62(string $bytes): string
    {
        $alphabet = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $num = 0;
        foreach (str_split($bytes) as $chr) {
            $num = ($num << 8) + ord($chr);
        }
        $out = '';
        while ($num > 0) {
            $out = $alphabet[$num % 62] . $out;
            $num = intdiv($num, 62);
        }
        return $out ?: '0';
    }

    /**
     * 取得我的短網址清單
     *
     * @param int $userId 使用者 ID
     * @param int $perPage 每頁筆數
     * @return LengthAwarePaginator
     */
    public function getMyShortUrls(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->shortUrlRepository->getUserShortUrls($userId, $perPage);
    }

    /**
     * 刪除短網址
     *
     * @param int $id 短網址 ID
     * @return void
     * @throws Exception
     */
    public function delete(int $id): void
    {
        $shortUrl = $this->shortUrlRepository->findById($id);

        Gate::authorize('delete', $shortUrl);

        $this->shortUrlRepository->delete($shortUrl);

        // 清除快取
        Cache::forget(self::CACHE_KEY_CODE_PREFIX . $shortUrl->short_code);
    }

    /**
     * 根據短碼重定向到原始 URL
     *
     * @param string $code 短碼
     * @return string 原始 URL
     * @throws Exception
     */
    public function redirect(string $code): string
    {
        $cacheKey = self::CACHE_KEY_CODE_PREFIX . $code;

        // 嘗試從快取取得
        $shortUrl = Cache::get($cacheKey);

        // 快取未命中
        if ($shortUrl === null) {
            // 使用鎖防止快取擊穿
            $lock = Cache::lock('lock:' . $cacheKey, 10);

            try {
                $lock->block(5);

                // 雙重檢查（其他執行緒可能已經寫入快取）
                $shortUrl = Cache::get($cacheKey);

                if ($shortUrl === null) {
                    $shortUrl = $this->shortUrlRepository->findByCode($code);

                    if ($shortUrl) {
                        // 找到資料，快取 1 小時
                        Cache::put($cacheKey, $shortUrl, 3600);
                    } else {
                        // 防止快取穿透：快取空值 5 分鐘
                        Cache::put($cacheKey, false, 300);
                    }
                }
            } finally {
                $lock->release();
            }
        }

        // 處理查不到的情況
        if ($shortUrl === false) {
            throw new Exception('短網址不存在', 404);
        }

        // 檢查是否過期
        if ($shortUrl->expired_at && now()->isAfter($shortUrl->expired_at)) {
            throw new Exception('短網址已過期', 410);
        }

        // 同步增加點擊次數
        $this->shortUrlRepository->incrementClickCount($shortUrl->id);

        return $shortUrl->original_url;
    }

    /**
     * 快取短碼對應資料
     *
     * @param ShortUrl $short 模型
     * @return void
     */
    protected function cacheShort(ShortUrl $short): void
    {
        Cache::put(self::CACHE_KEY_CODE_PREFIX . $short->short_code, $short, 3600);
    }
}