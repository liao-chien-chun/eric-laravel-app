<?php 

namespace App\Repositories;

use App\Models\Item;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class ItemRepository
 * 
 * 處理商品相關資料表 (items) 有關的資料存取邏輯
 */
class ItemRepository
{
    /**
     * 新增商品
     * @param array $data 要新增之商品資料
     * @return \App\Models\Item 儲存後的商品模型
     */
    public function createItem(array $data): Item
    {
        return Item::create($data);
    }

    /**
     * 取得指定狀態的商品列表（分頁）
     * 用於後台管理者查詢
     *
     * @param int $status 商品狀態 (1:草稿, 2:上架, 3:下架)
     * @param int $perPage 每頁筆數，預設 15
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getItemsByStatus(int $status, int $perPage = 15)
    {
        return Item::where('status', $status)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}