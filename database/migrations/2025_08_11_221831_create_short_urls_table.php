<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 建立 short_urls 資料表
     */
    public function up(): void
    {
        Schema::create('short_urls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete()
                ->comment('建立此短網址的使用者 ID');
            $table->text('original_url')->comment('原始網址');
            $table->string('short_code', 32)->unique()->comment('短網址(唯一值)');
            $table->unsignedBigInteger('click_count')->default(0)->comment('累積點擊次數(定期由 redis 回寫)');
            $table->timestamp('expired_at')->nullable()->index()->comment('到期時間(逾期及失效)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('short_urls');
    }
};
