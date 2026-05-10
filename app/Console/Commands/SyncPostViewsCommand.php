<?php

namespace App\Console\Commands;

use App\Services\PostViewService;
use Illuminate\Console\Command;

class SyncPostViewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:sync-views';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步 Redis 中的文章觀看次數到資料庫';

    public function __construct(
        private PostViewService $postViewService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('開始同步文章觀看次數...');

        try {
            $result = $this->postViewService->syncViewsToDatabase();

            $this->info($result['message']);
            $this->table(
                ['項目', '數值'],
                [
                    ['同步文章數', $result['synced_count']],
                    ['總觀看次數', $result['total_views']],
                ]
            );

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('同步失敗：' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
