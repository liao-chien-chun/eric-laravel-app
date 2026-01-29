<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            // 主鍵（內部使用，自動遞增）
            $table->id();

            // 商品編號（對外使用，系統自動生成）
            $table->string('item_no', 50)->unique()->nullable()->comment('商品編號 - 對外顯示與查詢使用（建立後自動生成）');

            // 基本資訊
            $table->string('name')->comment('商品名稱');
            $table->text('description')->nullable()->comment('商品描述');

            // 價格與庫存（價格為整數，不得小於0）
            $table->unsignedInteger('price')->default(0)->comment('商品價格（整數）');
            $table->integer('stock')->default(0)->comment('庫存數量');

            // 商品狀態：1=草稿, 2=上架, 3=下架
            $table->tinyInteger('status')->default(1)->comment('商品狀態 (1:草稿 2:上架 3:下架)');

            // 圖片（簡易版本，未來可擴充成圖片表）
            $table->string('image')->nullable()->comment('商品主圖片路徑');

            // 商品建立者
            $table->foreignId('user_id')->nullable()->comment('商品建立者 ID');

            // 預留未來關聯欄位
            $table->foreignId('category_id')->nullable()->comment('商品分類 ID (預留)');
            $table->foreignId('brand_id')->nullable()->comment('商品品牌 ID (預留)');

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();

            // 時間戳記
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
