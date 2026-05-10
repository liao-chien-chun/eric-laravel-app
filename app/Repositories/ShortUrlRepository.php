<?php 

namespace App\Repositories;

use App\Models\ShortUrl;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;

class ShortUrlRepository
{
    /**
     * 建立
     *
     * @param array{user_id:int,original_url:string,short_code:string,expired_at?:string|null} $data
     * @return ShortUrl
     * @throws QueryException
     */
    public function create(array $data): ShortUrl
    {
        return ShortUrl::create($data);
    }

    /**
     * 檢查短碼是否存在
     *
     * @param string $code 短碼
     * @return bool
     */
    public function existsCode(string $code): bool
    {
        return ShortUrl::where('short_code', $code)->exists();
    }

    /**
     * 取得使用者的短網址清單（分頁）
     *
     * @param int $userId 使用者 ID
     * @param int $perPage 每頁筆數
     * @return LengthAwarePaginator
     */
    public function getUserShortUrls(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return ShortUrl::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * 根據 ID 查找短網址
     *
     * @param int $id 短網址 ID
     * @return ShortUrl
     * @throws ModelNotFoundException
     */
    public function findById(int $id): ShortUrl
    {
        try {
            return ShortUrl::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException("找不到該短網址");
        }
    }

    /**
     * 刪除短網址
     *
     * @param ShortUrl $shortUrl
     * @return bool
     */
    public function delete(ShortUrl $shortUrl): bool
    {
        return $shortUrl->delete();
    }

    /**
     * 根據短碼查找短網址
     *
     * @param string $code 短碼
     * @return ShortUrl|null
     */
    public function findByCode(string $code): ?ShortUrl
    {
        return ShortUrl::where('short_code', $code)->first();
    }

    /**
     * 增加點擊次數（透過 ID）
     *
     * @param int $id 短網址 ID
     * @return bool
     */
    public function incrementClickCount(int $id): bool
    {
        return ShortUrl::where('id', $id)->increment('click_count') > 0;
    }

    /**
     * 分批處理已過期短網址
     *
     * @param int $chunkSize 每批筆數
     * @param callable(Collection<int, ShortUrl>): void $callback
     * @return bool
     */
    public function chunkExpiredShortUrls(int $chunkSize, callable $callback): bool
    {
        return ShortUrl::query()
            ->whereNotNull('expired_at')
            ->where('expired_at', '<', now())
            ->select(['id', 'short_code'])
            ->orderBy('id')
            ->chunkById($chunkSize, $callback);
    }

    /**
     * 批次刪除短網址
     *
     * @param Collection<int, int> $ids
     * @return int
     */
    public function deleteByIds(Collection $ids): int
    {
        if ($ids->isEmpty()) {
            return 0;
        }

        return ShortUrl::whereIn('id', $ids)->delete();
    }
}
