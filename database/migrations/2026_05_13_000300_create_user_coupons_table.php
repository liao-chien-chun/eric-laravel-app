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
        Schema::create('user_coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('使用者ID');
            $table->foreignId('coupon_id')->constrained()->onDelete('cascade')->comment('優惠券ID');
            $table->timestamp('claimed_at')->comment('領取時間');
            $table->timestamp('used_at')->nullable()->comment('使用時間');
            $table->unsignedTinyInteger('status')->default(1)->comment('狀態 1:未使用 2:已使用 3:已過期');
            $table->timestamps();

            $table->unique(['user_id', 'coupon_id']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_coupons');
    }
};
