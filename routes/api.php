<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PostController;
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

    // 使用者登出
    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [UserController::class, 'logout']);
    });
});

// 文章
Route::middleware('auth:api')->prefix('posts')->group(function () {
    // 新增文章
    Route::post('/', [PostController::class, 'store']);
    // 更新文章
    Route::PUT('/{id}', [PostController::class, 'update']);
});