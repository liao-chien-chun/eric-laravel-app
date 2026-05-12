<?php 

namespace App\Repositories;

use App\Models\Comment;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class CommentRepository
 * 
 */
class CommentRepository 
{
    /**
     * 新增留言
     * 
     * @param array $data
     * @return Comment
     */
    public function createComment(array $data): Comment 
    {
        return Comment::create($data);
    }

    /**
     * 依留言 ID  取得該留言
     * 
     * @param int $id
     * @return Comment
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findCommentByID(int $id): Comment
    {
        try {
            return Comment::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException("該留言不存在");
        }
    }

    /**
     * 修改留言
     *
     * @param Comment $comment
     * @param array $data
     * @return bool
     */
    public function updateComment(Comment $comment, array $data): bool
    {
        return $comment->update($data);
    }

    /**
     * 刪除留言
     * @param Comment $comment
     * @return bool|null
     */
    public function deleteComment(Comment $comment): ?bool
    {
        return $comment->delete();
    }

    /**
     * 取得文章的所有留言（分頁）
     *
     * @param int $postId 文章 ID
     * @param int $perPage 每頁筆數，預設 20
     * @param string $sortOrder 排序方式 (asc: 最舊的在前, desc: 最新的在前)，預設 asc
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPostComments(int $postId, int $perPage = 20, string $sortOrder = 'asc')
    {
        return Comment::where('post_id', $postId)
            ->with('user') // 預載使用者資訊，避免 N+1 查詢
            ->orderBy('created_at', $sortOrder) // 按時間排序
            ->paginate($perPage);
    }
}