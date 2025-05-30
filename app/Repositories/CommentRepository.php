<?php 

namespace App\Repositories;

use App\Models\Comment;

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
}