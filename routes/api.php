<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\ShortUrlController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('user')->group(function () {
    // 使用者註冊
    Route::post('/register', [UserController::class, 'register']);
    // 使用者登入
    Route::post('/login', [UserController::class, 'login']);

    // 取得使用者的文章列表（公開，任何人可訪問已發佈文章，草稿和隱藏需要本人登入）
    Route::get('/{user}/posts', [PostController::class, 'getUserPosts'])
        ->middleware('optional.auth');

    // 使用者登出
    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [UserController::class, 'logout']);
    });
});

// 要驗證的
Route::middleware('auth:api')->group(function () {
    // 文章
    Route::prefix('posts')->group(function () {
        // 新增文章
        Route::post('/', [PostController::class, 'store']);
        // 更新文章
        Route::PUT('/{post}', [PostController::class, 'update']);
        // 刪除文章
        Route::delete('/{post}', [PostController::class, 'destroy']);
        // 更新文章狀態
        Route::patch('/{post}/status', [PostController::class, 'updateStatus']);

        // 對文章新增留言
        Route::post('/{post}/comments', [CommentController::class, 'store']);
        // 修改留言
        Route::PUT('/{post}/comments/{comment}', [CommentController::class, 'update']);
    });

    // 短網址
    Route::prefix('short-urls')->group(function () {
        // 取得我的短網址清單
        Route::get('/', [ShortUrlController::class, 'index']);
        // 建立短網址(可自訂 short_code)
        Route::post('/', [ShortUrlController::class, 'store']);
        // 刪除(只能刪除自己的)
        Route::delete('/{id}', [ShortUrlController::class, 'destroy']);
    });
});
