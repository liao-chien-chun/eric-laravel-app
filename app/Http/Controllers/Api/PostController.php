<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\GetUserPostsRequest;
use App\Http\Resources\PostResource;
use App\Services\PostService;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class PostController
 * 
 * 處理文章相關 API 請求
 */
class PostController extends Controller
{
    public function __construct(
        private PostService $postService
    ) {}

    /**
     * 建立新文章
     * 
     * @param StorePostRequest $request 驗證後的請求資料
     * @return JsonResponse 回傳 Json 格式的成功訊息與文章資料
     */
    public function store(StorePostRequest $request): JsonResponse
    {
        try {
            // 呼叫 service 層的方法並傳入驗證過的資料與目前登入之使用者
            $post = $this->postService->createPost($request->validated());

            return response()->json([
                'success' => true,
                'status' => Response::HTTP_CREATED,
                'message' => '文章建立成功',
                'data' => new PostResource($post)
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => '伺服器錯誤，請稍後再試',
                'data' => null
            ], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * 更新文章
     * 
     * @param StorePostRequest $request (共用 store 的 Request)
     * @param int $id 路由傳入之文章 ID
     * @return JsonResponse 
     */
    public function update(StorePostRequest $request, int $id): JsonResponse
    {
        try {
            $post = $this->postService->updatePost($id, $request->validated());

            return response()->json([
                'success' => true,
                'status' => Response::HTTP_OK,
                'message' => '修改文章成功',
                'data' => new PostResource($post)
            ], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'status' => Response::HTTP_NOT_FOUND,
                'message' => $e->getMessage(),
                'data' => null
            ], Response::HTTP_NOT_FOUND);
        } catch (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'status' => Response::HTTP_FORBIDDEN,
                'message' => '你沒有權限修改該文章',
                'data' => null
            ], Response::HTTP_FORBIDDEN);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
                'data' => null
            ], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * 取得指定使用者的文章列表（分頁）
     *
     * @param GetUserPostsRequest $request 驗證後的請求資料
     * @param int $userId 使用者 ID（路由參數）
     * @return JsonResponse
     */
    public function getUserPosts(GetUserPostsRequest $request, int $userId): JsonResponse
    {
        try {
            $validated = $request->validated();
            $status = $validated['status'] ?? 2; // 預設顯示已發佈的文章
            $perPage = $validated['per_page'] ?? 15; // 預設每頁 15 筆

            $posts = $this->postService->getUserPosts($userId, $status, $perPage);

            return response()->json([
                'success' => true,
                'status' => Response::HTTP_OK,
                'message' => '文章列表取得成功',
                'data' => [
                    'posts' => PostResource::collection($posts->items()),
                    'pagination' => [
                        'current_page' => $posts->currentPage(),
                        'per_page' => $posts->perPage(),
                        'total' => $posts->total(),
                        'last_page' => $posts->lastPage(),
                    ],
                ],
            ], Response::HTTP_OK);
        } catch (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'status' => Response::HTTP_FORBIDDEN,
                'message' => $e->getMessage(),
                'data' => null
            ], Response::HTTP_FORBIDDEN);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => '伺服器錯誤，請稍後再試',
                'data' => null
            ], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
