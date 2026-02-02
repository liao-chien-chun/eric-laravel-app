<?php

namespace App\Console\Commands;

use App\Services\ElasticsearchService;
use Exception;
use Illuminate\Console\Command;

class ShowElasticsearchItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'es:show-items
                            {--search= : 搜尋關鍵字}
                            {--limit=20 : 顯示筆數}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '查詢 Elasticsearch 中的商品資料';

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
        try {
            $search = $this->option('search');
            $limit = (int) $this->option('limit');

            $this->info('正在查詢 Elasticsearch...');
            $this->newLine();

            if ($search) {
                // 搜尋模式
                $this->info("搜尋關鍵字: {$search}");
                $result = $this->esService->searchItems($search, 1, $limit);
                $items = $result['items'];
                $total = $result['total'];
            } else {
                // 顯示全部
                $result = $this->getAllItems($limit);
                $items = $result['items'];
                $total = $result['total'];
            }

            if (empty($items)) {
                $this->warn('找不到任何商品資料');
                return Command::SUCCESS;
            }

            // 顯示統計資訊
            $this->info("找到 {$total} 筆商品" . ($total > $limit ? "（顯示前 {$limit} 筆）" : ''));
            $this->newLine();

            // 轉換為表格資料
            $tableData = [];
            foreach ($items as $item) {
                $tableData[] = [
                    $item['id'],
                    $item['item_no'],
                    mb_substr($item['name'], 0, 30),  // 限制長度
                    '$' . number_format($item['price']),
                    $item['stock'],
                    $this->getStatusText($item['status']),
                    $item['created_at'],
                ];
            }

            // 顯示表格
            $this->table(
                ['ID', '商品編號', '商品名稱', '價格', '庫存', '狀態', '建立時間'],
                $tableData
            );

            return Command::SUCCESS;

        } catch (Exception $e) {
            $this->error('查詢失敗：' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * 取得所有商品（不使用搜尋）
     */
    private function getAllItems(int $limit): array
    {
        $params = [
            'index' => config('elasticsearch.indices.items'),
            'body' => [
                'size' => $limit,
                'sort' => [
                    ['created_at' => ['order' => 'desc']]
                ],
                'query' => [
                    'match_all' => new \stdClass()
                ]
            ]
        ];

        $response = $this->esService->getClient()->search($params);
        $result = $response->asArray();

        return [
            'total' => $result['hits']['total']['value'] ?? 0,
            'items' => array_map(fn($hit) => $hit['_source'], $result['hits']['hits'] ?? []),
        ];
    }

    /**
     * 取得狀態文字
     */
    private function getStatusText(int $status): string
    {
        return match ($status) {
            1 => '草稿',
            2 => '上架',
            3 => '下架',
            default => '未知',
        };
    }
}
