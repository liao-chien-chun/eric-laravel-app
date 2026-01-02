<?php 

namespace App\Repositories;

use App\Models\Post;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class PostRepository
 * 
 * 處理與文章資料表(posts) 有關的資料存取邏輯
 */
class PostRepository
{
    /**
     * 儲存文章資料
     * 
     * @param array $data 要儲存之文章資料
     * @return \App\Models\Post 儲存後的文章模型
     */
    public function createPost(array $data): Post
    {
        return Post::create($data);
    }

    /**
     * 依 ID 取得單篇文章
     * 
     * @param int $id 
     * @return Post
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findPostById(int $id): Post
    {
        // return Post::findOrFail($id);

        try {
            return Post::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException("找不到該文章");
        }
    }

    /**
     * 更新文章
     *
     * @param Post $post
     * @param array $data
     * @return bool
     */
    public function updatePost(Post $post, array $data): bool
    {
        return $post->update($data);
    }

    /**
     * 取得使用者的文章列表（分頁）
     *
     * @param int $userId 使用者 ID
     * @param int $status 文章狀態 (1:草稿, 2:發布, 3:隱藏)
     * @param int $perPage 每頁筆數，預設 15
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getUserPosts(int $userId, int $status, int $perPage = 15)
    {
        return Post::where('user_id', $userId)
            ->where('status', $status)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}