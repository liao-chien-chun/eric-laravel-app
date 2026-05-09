<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // 指定允許的前端 URL（不能用 * 因為要支援 credentials）
    'allowed_origins' => [
        'http://localhost:3000',      // Vue 開發伺服器
        'http://127.0.0.1:3000',      // 另一種 localhost
        'http://localhost:5173',      // Vite 預設 port（備用）
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // 必須設為 true，配合前端的 withCredentials
    'supports_credentials' => true,

];
