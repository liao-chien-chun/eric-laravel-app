<?php 

namespace App\Services;

use App\Models\Comment;
use App\Repositories\CommentRepository;
use App\Repositories\PostRepository;
use Illuminate\Support\Facades\Auth;
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
}