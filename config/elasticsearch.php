<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Elasticsearch Host Configuration
    |--------------------------------------------------------------------------
    |
    | 本機開發環境使用 localhost:9200
    | Docker 容器環境使用 elasticsearch:9200
    |
    */
    'host' => env('ES_HOST', 'elasticsearch'),

    'port' => env('ES_PORT', 9200),

    /*
    |--------------------------------------------------------------------------
    | Index Names
    |--------------------------------------------------------------------------
    |
    | 定義各個功能使用的 Index 名稱
    | 建議格式：{project}_{env}_{entity}
    |
    */
    'indices' => [
        'items' => env('ES_INDEX_ITEMS', 'laravel_items'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Connection Settings
    |--------------------------------------------------------------------------
    */
    'connections' => [
        'default' => [
            'hosts' => [
                [
                    'host' => env('ES_HOST', 'elasticsearch'),
                    'port' => env('ES_PORT', 9200),
                    'scheme' => env('ES_SCHEME', 'http'),
                ]
            ],

            // 重試設定
            'retries' => 2,

            // 連線逾時設定（秒）
            'timeout' => 10,
        ]
    ],
];
