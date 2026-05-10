<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 確保至少有一些用戶存在
        if (User::count() === 0) {
            $this->command->warn('沒有找到用戶，先創建一些用戶...');
            // User::factory(5)->create();
        }

        // 指定 user
        $user = User::find(7);

        // $users = User::all();

        $this->command->info('開始建立文章種子資料...');

        // 1. 建立 50 篇已發布的文章
        $this->command->info('建立 50 篇已發布的文章...');
        Post::factory()
            ->count(50)
            ->published()
            ->create([
                'user_id' => $user->id,
            ]);

        // 2. 建立 10 篇熱門文章（高觀看數）
        $this->command->info('建立 10 篇熱門文章...');
        Post::factory()
            ->count(10)
            ->published()
            ->popular()
            ->create([
                'user_id' => $user->id,
            ]);

        // 3. 建立 15 篇草稿
        // $this->command->info('建立 15 篇草稿...');
        // Post::factory()
        //     ->count(15)
        //     ->draft()
        //     ->recycle($users)
        //     ->create();

        // // 4. 建立 8 篇隱藏的文章
        // $this->command->info('建立 8 篇隱藏的文章...');
        // Post::factory()
        //     ->count(8)
        //     ->hidden()
        //     ->recycle($users)
        //     ->create();

        // 5. 建立 20 篇最近發布的文章
        // $this->command->info('建立 20 篇最近發布的文章...');
        // Post::factory()
        //     ->count(20)
        //     ->published()
        //     ->recent()
        //     ->recycle($users)
        //     ->create();

        // 建立一些特定的文章（方便測試）
            $this->command->info("為用戶 {$user->name} 建立測試文章...");

            // 一篇草稿
            // Post::factory()->draft()->create([
            //     'user_id' => $user->id,
            //     'title' => '【測試】我的第一篇草稿',
            //     'content' => '這是一篇測試用的草稿文章，尚未發布。',
            // ]);

            // 一篇已發布的文章
            Post::factory()->published()->create([
                'user_id' => $user->id,
                'title' => '【測試】Laravel 開發心得分享',
                'content' => '這是一篇關於 Laravel 開發的文章，包含了許多實用的技巧和經驗分享。',
                'views_count' => 500,
            ]);

            // 一篇熱門文章
            Post::factory()->published()->popular()->create([
                'user_id' => $user->id,
                'title' => '【熱門】如何成為優秀的後端工程師',
                'content' => '本文將分享成為優秀後端工程師所需的技能、心態和學習路徑。',
                'views_count' => 10000,
            ]);

        $totalPosts = Post::count();
        $publishedCount = Post::where('status', 2)->count();
        $draftCount = Post::where('status', 1)->count();
        $hiddenCount = Post::where('status', 3)->count();

        $this->command->info('');
        $this->command->info('✅ 文章種子資料建立完成！');
        $this->command->info("總計：{$totalPosts} 篇文章");
        $this->command->info("  - 已發布：{$publishedCount} 篇");
        $this->command->info("  - 草稿：{$draftCount} 篇");
        $this->command->info("  - 隱藏：{$hiddenCount} 篇");
    }
}
