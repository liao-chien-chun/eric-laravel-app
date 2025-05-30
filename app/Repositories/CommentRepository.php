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
}