<?php 

namespace App\Repositories;

use App\Models\ShortUrl;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

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
}