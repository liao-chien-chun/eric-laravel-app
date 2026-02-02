<?php

namespace App\Services;

use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Client;
use Exception;
use Illuminate\Support\Facades\Log;

class ElasticsearchService
{
    private Client $client;
    private string $itemsIndex;

    public function __construct()
    {
        // 建立 Elasticsearch Client
        $config = config('elasticsearch.connections.default');

        // 將 hosts 陣列轉換為字串陣列格式
        $hosts = array_map(function ($host) {
            return sprintf(
                '%s://%s:%d',
                $host['scheme'] ?? 'http',
                $host['host'],
                $host['port']
            );
        }, $config['hosts']);

        $this->client = ClientBuilder::create()
            ->setHosts($hosts)
            ->setRetries($config['retries'])
            ->build();

        // 取得 items index 名稱
        $this->itemsIndex = config('elasticsearch.indices.items');
    }

    /**
     * 取得 Elasticsearch Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * 建立 items index 與 mapping
     *
     * @return bool
     */
    public function createItemsIndex(): bool
    {
        try {
            // 檢查 index 是否已存在
            if ($this->client->indices()->exists(['index' => $this->itemsIndex])->asBool()) {
                Log::info("Index {$this->itemsIndex} 已存在，跳過建立");
                return true;
            }

            // 定義 mapping (欄位類型定義)
            $params = [
                'index' => $this->itemsIndex,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'analysis' => [
                            'analyzer' => [
                                // 中文分詞器（使用 standard analyzer 作為基礎）
                                'default' => [
                                    'type' => 'standard'
                                ]
                            ]
                        ]
                    ],
                    'mappings' => [
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'item_no' => [
                                'type' => 'keyword',  // 精確匹配用
                            ],
                            'name' => [
                                'type' => 'text',     // 全文搜索用
                                'analyzer' => 'standard',
                                'fields' => [
                                    'keyword' => [    // 精確匹配用（排序、聚合）
                                        'type' => 'keyword',
                                        'ignore_above' => 256
                                    ]
                                ]
                            ],
                            'description' => [
                                'type' => 'text',
                                'analyzer' => 'standard',
                            ],
                            'price' => ['type' => 'integer'],
                            'stock' => ['type' => 'integer'],
                            'status' => ['type' => 'integer'],
                            'image' => ['type' => 'keyword'],
                            'user_id' => ['type' => 'integer'],
                            'category_id' => ['type' => 'integer'],
                            'brand_id' => ['type' => 'integer'],
                            'created_at' => ['type' => 'date', 'format' => 'yyyy-MM-dd HH:mm:ss'],
                            'updated_at' => ['type' => 'date', 'format' => 'yyyy-MM-dd HH:mm:ss'],
                        ]
                    ]
                ]
            ];

            $response = $this->client->indices()->create($params);
            Log::info("成功建立 Index: {$this->itemsIndex}", ['response' => $response->asArray()]);

            return true;
        } catch (Exception $e) {
            Log::error("建立 Index 失敗: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 同步單一商品到 ES
     *
     * @param array $item 商品資料
     * @return bool
     */
    public function indexItem(array $item): bool
    {
        try {
            $params = [
                'index' => $this->itemsIndex,
                'id' => $item['id'],  // 使用商品 ID 作為文檔 ID
                'body' => [
                    'id' => $item['id'],
                    'item_no' => $item['item_no'],
                    'name' => $item['name'],
                    'description' => $item['description'] ?? '',
                    'price' => $item['price'],
                    'stock' => $item['stock'],
                    'status' => $item['status'],
                    'image' => $item['image'] ?? '',
                    'user_id' => $item['user_id'],
                    'category_id' => $item['category_id'] ?? null,
                    'brand_id' => $item['brand_id'] ?? null,
                    'created_at' => $item['created_at'],
                    'updated_at' => $item['updated_at'],
                ]
            ];

            $this->client->index($params);

            return true;
        } catch (Exception $e) {
            Log::error("同步商品到 ES 失敗 (ID: {$item['id']}): " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 批次同步商品到 ES（使用 bulk API 提高效能）
     *
     * @param array $items 商品陣列
     * @return array ['success' => int, 'failed' => int]
     */
    public function bulkIndexItems(array $items): array
    {
        try {
            if (empty($items)) {
                return ['success' => 0, 'failed' => 0];
            }

            $params = ['body' => []];

            foreach ($items as $item) {
                // 每個商品需要兩行：action 和 document
                $params['body'][] = [
                    'index' => [
                        '_index' => $this->itemsIndex,
                        '_id' => $item['id'],
                    ]
                ];

                $params['body'][] = [
                    'id' => $item['id'],
                    'item_no' => $item['item_no'],
                    'name' => $item['name'],
                    'description' => $item['description'] ?? '',
                    'price' => $item['price'],
                    'stock' => $item['stock'],
                    'status' => $item['status'],
                    'image' => $item['image'] ?? '',
                    'user_id' => $item['user_id'],
                    'category_id' => $item['category_id'] ?? null,
                    'brand_id' => $item['brand_id'] ?? null,
                    'created_at' => $item['created_at'],
                    'updated_at' => $item['updated_at'],
                ];
            }

            $response = $this->client->bulk($params);
            $result = $response->asArray();

            // 計算成功與失敗數量
            $successCount = 0;
            $failedCount = 0;

            if (isset($result['items'])) {
                foreach ($result['items'] as $item) {
                    if (isset($item['index']['status']) && $item['index']['status'] >= 200 && $item['index']['status'] < 300) {
                        $successCount++;
                    } else {
                        $failedCount++;
                    }
                }
            }

            Log::info("批次同步商品完成", [
                'total' => count($items),
                'success' => $successCount,
                'failed' => $failedCount
            ]);

            return [
                'success' => $successCount,
                'failed' => $failedCount,
            ];
        } catch (Exception $e) {
            Log::error("批次同步商品到 ES 失敗: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 從 ES 刪除單一商品
     *
     * @param int $itemId
     * @return bool
     */
    public function deleteItem(int $itemId): bool
    {
        try {
            $params = [
                'index' => $this->itemsIndex,
                'id' => $itemId
            ];

            $this->client->delete($params);
            Log::info("從 ES 刪除商品成功 (ID: {$itemId})");

            return true;
        } catch (Exception $e) {
            Log::error("從 ES 刪除商品失敗 (ID: {$itemId}): " . $e->getMessage());
            return false;
        }
    }

    /**
     * 刪除整個 index（危險操作！僅用於測試或重建）
     *
     * @return bool
     */
    public function deleteItemsIndex(): bool
    {
        try {
            if (!$this->client->indices()->exists(['index' => $this->itemsIndex])->asBool()) {
                Log::info("Index {$this->itemsIndex} 不存在，無需刪除");
                return true;
            }

            $this->client->indices()->delete(['index' => $this->itemsIndex]);
            Log::warning("已刪除 Index: {$this->itemsIndex}");

            return true;
        } catch (Exception $e) {
            Log::error("刪除 Index 失敗: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 搜尋商品（基礎版本，未來可擴充）
     *
     * @param string $keyword 搜尋關鍵字
     * @param int $page 頁碼
     * @param int $perPage 每頁筆數
     * @return array
     */
    public function searchItems(string $keyword, int $page = 1, int $perPage = 20): array
    {
        try {
            $from = ($page - 1) * $perPage;

            $params = [
                'index' => $this->itemsIndex,
                'body' => [
                    'from' => $from,
                    'size' => $perPage,
                    'query' => [
                        'bool' => [
                            'must' => [
                                'multi_match' => [
                                    'query' => $keyword,
                                    'fields' => ['name^2', 'description', 'item_no'], // name 權重較高
                                ]
                            ],
                            'filter' => [
                                ['term' => ['status' => 2]] // 只搜尋上架商品
                            ]
                        ]
                    ]
                ]
            ];

            $response = $this->client->search($params);
            $result = $response->asArray();

            return [
                'total' => $result['hits']['total']['value'] ?? 0,
                'items' => array_map(fn($hit) => $hit['_source'], $result['hits']['hits'] ?? []),
            ];
        } catch (Exception $e) {
            Log::error("搜尋商品失敗: " . $e->getMessage());
            throw $e;
        }
    }
}
