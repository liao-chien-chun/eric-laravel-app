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
}