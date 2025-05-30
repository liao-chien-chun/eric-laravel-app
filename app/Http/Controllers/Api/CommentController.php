<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Resources\CommentResource;
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
}
