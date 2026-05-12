<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\FrontCommentResource;
use App\Services\CommentService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class CommentController
 * 
 * 處理留言相關 API 請求
 */
class CommentController extends Controller
{
    public function __construct(
        private CommentService $commentService
    ) {}

    /**
     * 取得文章的所有留言（前台用，不需登入）
     *
     * @param Request $request
     * @param int $postId 文章 ID
     * @return JsonResponse
     */
    public function index(Request $request, int $postId): JsonResponse
    {
        try {
            $perPage = (int) $request->query('per_page', 20);
            $sortOrder = $request->query('sort', 'asc'); // asc: 最舊的在前, desc: 最新的在前

            $comments = $this->commentService->getPostCommentsForFrontend($postId, $perPage, $sortOrder);

            return response()->json([
                'success' => true,
                'status' => Response::HTTP_OK,
                'message' => '留言列表取得成功',
                'data' => [
                    'comments' => FrontCommentResource::collection($comments->items()),
                    'pagination' => [
                        'current_page' => $comments->currentPage(),
                        'per_page' => $comments->perPage(),
                        'total' => $comments->total(),
                        'last_page' => $comments->lastPage(),
                        'has_more' => $comments->hasMorePages(), // 方便前端判斷是否還有更多資料
                    ],
                ],
            ], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'status' => Response::HTTP_NOT_FOUND,
                'message' => $e->getMessage(),
                'data' => null
            ], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage() ?: '伺服器錯誤，請稍後再試',
                'data' => null
            ], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * 新增留言
     *
     * @param StoreCommentRequest $request
     * @param int $postId
     * @return JsonResponse
     */
    public function store(StoreCommentRequest $request, int $postId): JsonResponse
    {
        try {
            $comment = $this->commentService->createComment($postId, $request->validated());

            return response()->json([
                'success' => true,
                'status' => Response::HTTP_CREATED,
                'message' => '新增留言成功',
                'data' => new CommentResource($comment)
            ], Response::HTTP_CREATED);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'status' => Response::HTTP_NOT_FOUND,
                'message' => '文章不存在，無法新增留言',
                'data' => null
            ], Response::HTTP_NOT_FOUND);
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
     * 修改文章留言
     * 
     * @param StoreCommentRequest $request
     * @param int $postId
     * @param int $commentId
     * @return JsonResponse
     */
    public function update(StoreCommentRequest $request, int $postId, int $commentId): JsonResponse
    {
        try {
            $comment = $this->commentService->updateComment($postId, $commentId, $request->validated());

            return response()->json([
                'success' => true,
                'status' => Response::HTTP_OK,
                'message' => '修改留言成功',
                'data' => new CommentResource($comment)
            ], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'status' => Response::HTTP_NOT_FOUND,
                'message' => $e->getMessage(),
                'data' => null
            ], Response::HTTP_NOT_FOUND, );
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

    /**
     * 刪除留言
     * 
     * @param int $postId
     * @param int $commentId
     * @return JsonResponse
     */
    public function destroy(int $postId, int $commentId): JsonResponse
    {
        try {
            $this->commentService->deleteComment($postId, $commentId);

            return response()->json([
                'success' => true,
                'status' => Response::HTTP_OK,
                'message' => '留言刪除成功',
                'data' => null
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

