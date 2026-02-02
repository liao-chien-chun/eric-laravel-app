<?php

namespace App\Console\Commands;

use App\Models\Item;
use App\Services\ElasticsearchService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncItemsToElasticsearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'es:sync-items
                            {--recreate : 重建 index（會先刪除舊的）}
                            {--chunk=100 : 每次批次處理的筆數}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步上架商品到 Elasticsearch（每天 00:00 自動執行）';

    /**
     * 建構子
     */
    public function __construct(private ElasticsearchService $esService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('開始同步商品到 Elasticsearch...');
        $this->newLine();

        try {
            // 如果指定 --recreate，先刪除舊的 index
            if ($this->option('recreate')) {
                $this->warn('準備重建 Index...');
                $this->esService->deleteItemsIndex();
                $this->info('✓ 舊 Index 已刪除');
                $this->newLine();
            }

            // 建立 index（如果不存在）
            $this->info('檢查並建立 Index...');
            $this->esService->createItemsIndex();
            $this->info('✓ Index 準備完成');
            $this->newLine();

            // 取得所有上架商品（status = 2）
            $this->info('開始抓取上架商品...');
            $publishedItems = Item::where('status', Item::STATUS_PUBLISHED)
                ->with('user:id,name')  // 預載入使用者資料（如果需要）
                ->get();

            $totalCount = $publishedItems->count();

            if ($totalCount === 0) {
                $this->warn('目前沒有上架商品，無需同步');
                return Command::SUCCESS;
            }

            $this->info("✓ 找到 {$totalCount} 筆上架商品");
            $this->newLine();

            // 批次同步到 ES
            $chunkSize = (int) $this->option('chunk');
            $this->info("開始批次同步（每批 {$chunkSize} 筆）...");

            $bar = $this->output->createProgressBar($totalCount);
            $bar->start();

            $totalSuccess = 0;
            $totalFailed = 0;

            // 分批處理
            $publishedItems->chunk($chunkSize)->each(function ($chunk) use ($bar, &$totalSuccess, &$totalFailed) {
                // 轉換為陣列格式
                $itemsArray = $chunk->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'item_no' => $item->item_no,
                        'name' => $item->name,
                        'description' => $item->description,
                        'price' => $item->price,
                        'stock' => $item->stock,
                        'status' => $item->status,
                        'image' => $item->image,
                        'user_id' => $item->user_id,
                        'category_id' => $item->category_id,
                        'brand_id' => $item->brand_id,
                        'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                        'updated_at' => $item->updated_at->format('Y-m-d H:i:s'),
                    ];
                })->toArray();

                // 批次同步
                $result = $this->esService->bulkIndexItems($itemsArray);
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
                $this->warn('⚠ 部分商品同步失敗，請查看 log 檔案');
                return Command::FAILURE;
            }

            $this->info('✓ 所有商品同步成功');
            return Command::SUCCESS;

        } catch (Exception $e) {
            $this->error('同步失敗：' . $e->getMessage());
            $this->error('錯誤詳情請查看 log 檔案');

            return Command::FAILURE;
        }
    }
}
