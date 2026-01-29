<?php 

namespace App\Services;

use App\Models\Item;
use App\Repositories\ItemRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\gate;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;


/**
 * Class ItemService
 * 負責處裡商品相關邏輯層操作
 */
class ItemService
{
    public function __construct(
        private ItemRepository $itemRepository
    ) {}
    

    /**
     * 建立商品
     *
     * @param array $data
     * @return \App\Models\Item
     */
    public function createItem(array $data): Item
    {
        // 加入當前登入的管理員 ID
        // 明確指定的方式
        $data['user_id'] = Auth::guard('api')->id();

        // 建立商品
        $item = $this->itemRepository->createItem($data);

        // 預載建立者資訊
        return $item->load('user');
    }
}