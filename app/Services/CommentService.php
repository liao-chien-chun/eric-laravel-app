<?php 

namespace App\Services;

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
}