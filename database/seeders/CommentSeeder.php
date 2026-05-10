<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = Post::where('status', 2)->get(); // 只為已發布的文章建立留言
        $users = User::all();

        if ($posts->isEmpty() || $users->isEmpty()) {
            $this->command->warn('沒有足夠的文章或用戶，跳過留言建立');
            return;
        }

        $this->command->info('開始為文章建立留言...');

        $totalComments = 0;

        foreach ($posts as $post) {
            // 隨機決定每篇文章的留言數量（0-15 則）
            $commentCount = rand(0, 15);

            if ($commentCount > 0) {
                Comment::factory()
                    ->count($commentCount)
                    ->create([
                        'post_id' => $post->id,
                        'user_id' => $users->random()->id,
                    ]);

                $totalComments += $commentCount;
            }
        }

        $this->command->info("✅ 留言建立完成！總計：{$totalComments} 則留言");
    }
}
