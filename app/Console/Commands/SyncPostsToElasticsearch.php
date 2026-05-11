<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Services\PostSearchService;
use App\Services\PostViewService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class SyncPostsToElasticsearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'es:sync-posts
                            {--recreate : 重建 index（會先刪除舊的）}
                            {--chunk=100 : 每次批次處理的筆數}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步已發布文章到 Elasticsearch';

    /**
     * 建構子
     */
    public function __construct(
        private PostSearchService $postSearchService,
        private PostViewService $postViewService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('開始同步文章到 Elasticsearch...');
        $this->newLine();

        try {
            // 如果指定 --recreate，先刪除舊的 index
            if ($this->option('recreate')) {
                $this->warn('準備重建 Index...');
                $this->postSearchService->deletePostsIndex();
                $this->info('✓ 舊 Index 已刪除');
                $this->newLine();
            }

            // 建立 index（如果不存在）
            $this->info('檢查並建立 Index...');
            $this->postSearchService->createPostsIndex();
            $this->info('✓ Index 準備完成');
            $this->newLine();

            // 取得所有已發布文章
            $this->info('開始抓取已發布文章...');
            $publishedPosts = Post::where('status', 2)
                ->with('user:id,name')
                ->withCount('comments')
                ->get();

            $totalCount = $publishedPosts->count();

            if ($totalCount === 0) {
                $this->warn('目前沒有已發布文章，無需同步');
                return Command::SUCCESS;
            }

            $this->info("✓ 找到 {$totalCount} 篇已發布文章");
            $this->newLine();

            // 合併 Redis 中的觀看次數
            $this->info('合併 Redis 觀看次數...');
            $viewsCounts = $this->postViewService->getViewsCountForPosts($publishedPosts);
            foreach ($publishedPosts as $post) {
                $post->views_count = $viewsCounts[$post->id] ?? $post->views_count;
            }
            $this->info('✓ 觀看次數合併完成');
            $this->newLine();

            // 批次同步到 ES
            $chunkSize = (int) $this->option('chunk');
            $this->info("開始批次同步（每批 {$chunkSize} 筆）...");

            $bar = $this->output->createProgressBar($totalCount);
            $bar->start();

            $totalSuccess = 0;
            $totalFailed = 0;

            // 分批處理
            $publishedPosts->chunk($chunkSize)->each(function ($chunk) use ($bar, &$totalSuccess, &$totalFailed) {
                // 轉換為陣列格式
                $postsArray = $chunk->map(function ($post) {
                    return [
                        'id' => $post->id,
                        'title' => $post->title,
                        'content' => $post->content,
                        'status' => $post->status,
                        'user_id' => $post->user_id,
                        'views_count' => $post->views_count ?? 0,
                        'comments_count' => $post->comments_count ?? 0,
                        'created_at' => $post->created_at->format('Y-m-d H:i:s'),
                        'updated_at' => $post->updated_at->format('Y-m-d H:i:s'),
                    ];
                })->toArray();

                // 批次同步
                $result = $this->postSearchService->bulkIndexPosts($postsArray);
                $totalSuccess += $result['success'];
                $totalFailed += $result['failed'];

                $bar->advance($chunk->count());
            });

            $bar->finish();
            $this->newLine(2);

            // 顯示同步結果
            $this->info('同步完成！');
            $this->newLine();
            $this->table(
                ['項目', '數量'],
                [
                    ['總計', $totalCount],
                    ['成功', $totalSuccess],
                    ['失敗', $totalFailed],
                ]
            );

            if ($totalFailed > 0) {
                $this->warn('⚠ 部分文章同步失敗，請查看 log 檔案');
                return Command::FAILURE;
            }

            $this->info('✓ 所有文章同步成功');
            return Command::SUCCESS;

        } catch (Exception $e) {
            $this->error('同步失敗：' . $e->getMessage());
            $this->error('錯誤詳情請查看 log 檔案');

            return Command::FAILURE;
        }
    }
}
