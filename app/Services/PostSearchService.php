<?php

namespace App\Services;

use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Client;
use Exception;
use Illuminate\Support\Facades\Log;

class PostSearchService
{
    private Client $client;
    private string $postsIndex;

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

        // 取得 posts index 名稱
        $this->postsIndex = config('elasticsearch.indices.posts');
    }

    /**
     * 建立 posts index 與 mapping
     *
     * @return bool
     */
    public function createPostsIndex(): bool
    {
        try {
            // 檢查 index 是否已存在
            if ($this->client->indices()->exists(['index' => $this->postsIndex])->asBool()) {
                Log::info("Index {$this->postsIndex} 已存在，跳過建立");
                return true;
            }

            // 定義 mapping
            $params = [
                'index' => $this->postsIndex,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'analysis' => [
                            'analyzer' => [
                                'default' => [
                                    'type' => 'standard'
                                ]
                            ]
                        ]
                    ],
                    'mappings' => [
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'title' => [
                                'type' => 'text',
                                'analyzer' => 'standard',
                                'fields' => [
                                    'keyword' => [
                                        'type' => 'keyword',
                                        'ignore_above' => 256
                                    ]
                                ]
                            ],
                            'content' => [
                                'type' => 'text',
                                'analyzer' => 'standard',
                            ],
                            'status' => ['type' => 'integer'],
                            'user_id' => ['type' => 'integer'],
                            'views_count' => ['type' => 'long'],
                            'comments_count' => ['type' => 'long'],
                            'created_at' => ['type' => 'date', 'format' => 'yyyy-MM-dd HH:mm:ss'],
                            'updated_at' => ['type' => 'date', 'format' => 'yyyy-MM-dd HH:mm:ss'],
                        ]
                    ]
                ]
            ];

            $response = $this->client->indices()->create($params);
            Log::info("成功建立 Index: {$this->postsIndex}", ['response' => $response->asArray()]);

            return true;
        } catch (Exception $e) {
            Log::error("建立 Index 失敗: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 同步單一文章到 ES
     *
     * @param array $post 文章資料
     * @return bool
     */
    public function indexPost(array $post): bool
    {
        try {
            $params = [
                'index' => $this->postsIndex,
                'id' => $post['id'],
                'body' => [
                    'id' => $post['id'],
                    'title' => $post['title'],
                    'content' => $post['content'] ?? '',
                    'status' => $post['status'],
                    'user_id' => $post['user_id'],
                    'views_count' => $post['views_count'] ?? 0,
                    'comments_count' => $post['comments_count'] ?? 0,
                    'created_at' => $post['created_at'],
                    'updated_at' => $post['updated_at'],
                ]
            ];

            $this->client->index($params);
            Log::info("同步文章到 ES 成功 (ID: {$post['id']})");

            return true;
        } catch (Exception $e) {
            Log::error("同步文章到 ES 失敗 (ID: {$post['id']}): " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 批次同步文章到 ES
     *
     * @param array $posts 文章陣列
     * @return array ['success' => int, 'failed' => int]
     */
    public function bulkIndexPosts(array $posts): array
    {
        try {
            if (empty($posts)) {
                return ['success' => 0, 'failed' => 0];
            }

            $params = ['body' => []];

            foreach ($posts as $post) {
                $params['body'][] = [
                    'index' => [
                        '_index' => $this->postsIndex,
                        '_id' => $post['id'],
                    ]
                ];

                $params['body'][] = [
                    'id' => $post['id'],
                    'title' => $post['title'],
                    'content' => $post['content'] ?? '',
                    'status' => $post['status'],
                    'user_id' => $post['user_id'],
                    'views_count' => $post['views_count'] ?? 0,
                    'comments_count' => $post['comments_count'] ?? 0,
                    'created_at' => $post['created_at'],
                    'updated_at' => $post['updated_at'],
                ];
            }

            $response = $this->client->bulk($params);
            $result = $response->asArray();

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

            Log::info("批次同步文章完成", [
                'total' => count($posts),
                'success' => $successCount,
                'failed' => $failedCount
            ]);

            return [
                'success' => $successCount,
                'failed' => $failedCount,
            ];
        } catch (Exception $e) {
            Log::error("批次同步文章到 ES 失敗: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 從 ES 刪除單一文章
     *
     * @param int $postId
     * @return bool
     */
    public function deletePost(int $postId): bool
    {
        try {
            $params = [
                'index' => $this->postsIndex,
                'id' => $postId
            ];

            $this->client->delete($params);
            Log::info("從 ES 刪除文章成功 (ID: {$postId})");

            return true;
        } catch (Exception $e) {
            Log::error("從 ES 刪除文章失敗 (ID: {$postId}): " . $e->getMessage());
            return false;
        }
    }

    /**
     * 搜尋文章
     *
     * @param array $searchParams 搜尋參數 ['keyword', 'sort_by', 'order', 'per_page']
     * @param int $page 頁碼
     * @return array ['total' => int, 'post_ids' => array]
     */
    public function searchPosts(array $searchParams, int $page = 1): array
    {
        try {
            $keyword = $searchParams['keyword'] ?? null;
            $sortBy = $searchParams['sort_by'] ?? 'created_at';
            $order = $searchParams['order'] ?? 'desc';
            $perPage = $searchParams['per_page'] ?? 15;

            $from = ($page - 1) * $perPage;

            // 建立查詢條件
            $query = [
                'bool' => [
                    'filter' => [
                        ['term' => ['status' => 2]] // 只搜尋已發布文章
                    ]
                ]
            ];

            // 如果有關鍵字，加入全文搜索
            if (!empty($keyword)) {
                $query['bool']['must'] = [
                    'multi_match' => [
                        'query' => $keyword,
                        'fields' => ['title^3', 'content'], // title 權重較高
                        'type' => 'best_fields',
                        'operator' => 'or',
                    ]
                ];
            }

            // 建立排序條件
            $sort = [];
            if ($sortBy === 'created_at') {
                $sort[] = ['created_at' => ['order' => $order]];
            } elseif ($sortBy === 'views_count') {
                $sort[] = ['views_count' => ['order' => $order]];
                $sort[] = ['created_at' => ['order' => 'desc']]; // 次要排序
            } elseif ($sortBy === 'comments_count') {
                $sort[] = ['comments_count' => ['order' => $order]];
                $sort[] = ['created_at' => ['order' => 'desc']]; // 次要排序
            }

            $params = [
                'index' => $this->postsIndex,
                'body' => [
                    'from' => $from,
                    'size' => $perPage,
                    'query' => $query,
                    'sort' => $sort,
                    '_source' => ['id'], // 只返回 ID，實際資料從資料庫讀取
                ]
            ];

            $response = $this->client->search($params);
            $result = $response->asArray();

            $postIds = array_map(fn($hit) => $hit['_source']['id'], $result['hits']['hits'] ?? []);

            return [
                'total' => $result['hits']['total']['value'] ?? 0,
                'post_ids' => $postIds,
            ];
        } catch (Exception $e) {
            Log::error("搜尋文章失敗: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 刪除整個 posts index（危險操作！僅用於測試或重建）
     *
     * @return bool
     */
    public function deletePostsIndex(): bool
    {
        try {
            if (!$this->client->indices()->exists(['index' => $this->postsIndex])->asBool()) {
                Log::info("Index {$this->postsIndex} 不存在，無需刪除");
                return true;
            }

            $this->client->indices()->delete(['index' => $this->postsIndex]);
            Log::warning("已刪除 Index: {$this->postsIndex}");

            return true;
        } catch (Exception $e) {
            Log::error("刪除 Index 失敗: " . $e->getMessage());
            throw $e;
        }
    }
}
