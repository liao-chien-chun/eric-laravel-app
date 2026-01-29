<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    /**
     * 商品狀態常數
     */
    const STATUS_DRAFT = 1;       // 草稿
    const STATUS_PUBLISHED = 2;   // 上架
    const STATUS_UNPUBLISHED = 3; // 下架

    /**
     * 可批量賦值的屬性
     */
    protected $fillable = [
        'item_no',
        'name',
        'description',
        'price',
        'stock',
        'status',
        'image',
        'user_id',
        'category_id',
        'brand_id',
    ];

    /**
     * 屬性類型轉換
     */
    protected $casts = [
        'price' => 'integer',
        'stock' => 'integer',
        'status' => 'integer',
    ];

    /**
     * 模型啟動方法
     * 在商品建立後自動生成 item_no
     */
    protected static function boot()
    {
        parent::boot();

        // 在商品建立後自動生成 item_no
        static::created(function ($item) {
            if (empty($item->item_no)) {
                $item->item_no = self::generateItemNo($item->id);
                $item->save();
            }
        });
    }

    /**
     * 生成商品編號 item_no
     * 規則：ITEM + 10位數補零的 id
     * 例如：id=1 -> ITEM0000000001, id=999 -> ITEM0000000999
     *
     * @param int $id
     * @return string
     */
    public static function generateItemNo(int $id): string
    {
        return 'ITEM' . str_pad($id, 10, '0', STR_PAD_LEFT);
    }

    /**
     * 檢查商品是否為草稿狀態
     *
     * @return bool
     */
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * 檢查商品是否已上架
     *
     * @return bool
     */
    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    /**
     * 檢查商品是否已下架
     *
     * @return bool
     */
    public function isUnpublished(): bool
    {
        return $this->status === self::STATUS_UNPUBLISHED;
    }

    /**
     * 取得狀態文字
     *
     * @return string
     */
    public function getStatusText(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => '草稿',
            self::STATUS_PUBLISHED => '上架',
            self::STATUS_UNPUBLISHED => '下架',
            default => '未知',
        };
    }

    /**
     * 關聯：商品分類（預留）
     * 未來建立 Category Model 後取消註解
     */
    // public function category()
    // {
    //     return $this->belongsTo(Category::class);
    // }

    /**
     * 關聯：商品品牌（預留）
     * 未來建立 Brand Model 後取消註解
     */
    // public function brand()
    // {
    //     return $this->belongsTo(Brand::class);
    // }

    /**
     * 關聯：商品建立者
     * 一個商品屬於一個使用者（建立者）
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
