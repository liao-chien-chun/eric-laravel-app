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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique()->comment('優惠券代碼');
            $table->string('name', 100)->comment('優惠券名稱');
            $table->string('description', 500)->nullable()->comment('優惠券描述');
            $table->unsignedTinyInteger('discount_type')->default(1)->comment('折扣類型 1:固定金額 2:百分比');
            $table->unsignedInteger('discount_value')->comment('折扣值（整數）');
            $table->unsignedInteger('min_order_amount')->default(0)->comment('最低訂單金額（整數）');
            $table->unsignedInteger('total_quantity')->comment('總數量');
            $table->unsignedInteger('remaining_quantity')->comment('剩餘數量');
            $table->unsignedInteger('per_user_limit')->default(1)->comment('每人領取上限');
            $table->timestamp('start_at')->nullable()->comment('開始時間');
            $table->timestamp('end_at')->nullable()->comment('結束時間');
            $table->unsignedTinyInteger('status')->default(1)->comment('狀態 1:草稿 2:進行中 3:已結束');
            $table->timestamps();

            $table->index(['status', 'start_at', 'end_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
