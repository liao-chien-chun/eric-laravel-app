<?php 

namespace App\Services;

use App\Events\PostChanged;
use App\Models\Comment;
use App\Repositories\CommentRepository;
use App\Repositories\PostRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;

/**
 * Class CommentService
 * 
 * 負責處理留言相關邏輯
 *
 */
class CommentService 
{
    public function __construct(
        private CommentRepository $commentRepository,
        private PostRepository $postRepository
    ) {}

    /**
     * 對文章新增留言
     *
     * @param array $data
     * @param int $postId
     * @return Comment
     * @throws ModelNotFoundException
     */
    public function createComment(int $postId, array $data): Comment
    {
        $post = $this->postRepository->findPostById($postId);

        // 加上當前登入者id
        $data['user_id'] = Auth::id();
        // 加上文章id
        $data['post_id'] = $post->id;

        $comment = $this->commentRepository->createComment($data);

        PostChanged::dispatch($postId, 'index');

        return $comment;
    }

    /**
     * 修改留言
     *
     * @param array $data
     * @param int $postId
     * @param int $commentId
     * @return Comment
     * @throws ModelNotFoundException
     */
    public function updateComment(int $postId, int $commentId, array $data): Comment
    {
        // 檢查文章是否存在
        $post = $this->postRepository->findPostById($postId);
        // 檢查留言是否存在
        $comment = $this->commentRepository->findCommentById($commentId);

        // 確保留言屬於該文章
        if ($comment->post_id !== $post->id) {
            throw new AuthorizationException('此留言不屬於該文章，故無法更改');
        }

        // 使用 Policy 檢查權限
        if (Gate::denies('update', $comment)) {
            throw new AuthorizationException('你沒有權限修改留言');
        }

        $this->commentRepository->updateComment($comment, $data);

        return $comment;
    }

    /**
     * 取得文章的所有留言（前台用，不需登入）
     *
     * @param int $postId 文章 ID
     * @param int $perPage 每頁筆數，預設 20，限制在 1-50 之間
     * @param string $sortOrder 排序方式 (asc: 最舊的在前, desc: 最新的在前)，預設 asc
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * @throws ModelNotFoundException 當文章不存在或尚未發布時
     * @throws \Exception 當排序參數不合法時
     */
    public function getPostCommentsForFrontend(int $postId, int $perPage = 20, string $sortOrder = 'asc')
    {
        // 檢查文章是否存在且已發布（使用前台專用的方法）
        $this->postRepository->findPublishedPostById($postId);

        // 限制每頁筆數在 1-50 之間
        $perPage = max(1, min(50, $perPage));

        // 驗證排序參數
        $sortOrder = strtolower($sortOrder);
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            throw new \Exception('排序參數只能是 asc 或 desc', 422);
        }

        return $this->commentRepository->getPostComments($postId, $perPage, $sortOrder);
    }

}
