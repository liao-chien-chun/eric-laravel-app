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

    /**
     * 刪除文章
     *
     * @param Post $post
     * @return bool|null
     */
    public function deletePost(Post $post): ?bool
    {
        return $post->delete();
    }

    /**
     * 更新文章狀態
     *
     * @param Post $post
     * @param int $status 新的狀態 (1:草稿, 2:發布, 3:隱藏)
     * @return bool
     */
    public function updatePostStatus(Post $post, int $status): bool
    {
        return $post->update(['status' => $status]);
    }

    /**
     * 取得所有已發布的文章（前台用，分頁）
     *
     * @param int $perPage 每頁筆數，預設 15
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPublishedPosts(int $perPage = 15)
    {
        return Post::where('status', 2) // 只顯示已發布的文章
            ->with('user') // 預載使用者資訊
            ->withCount('comments') // 預載留言數量
            ->orderBy('created_at', 'desc') // 按時間排序，最新的在前
            ->paginate($perPage);
    }

    /**
     * 取得單一已發布的文章（前台用）
     *
     * @param int $id 文章 ID
     * @return Post
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findPublishedPostById(int $id): Post
    {
        try {
            return Post::where('id', $id)
                ->where('status', 2) // 只能查看已發布的文章
                ->with('user') // 預載使用者資訊
                ->withCount('comments') // 預載留言數量
                ->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException("找不到該文章或文章尚未發布");
        }
    }

    /**
     * 增加文章觀看次數（用於排程回寫）
     *
     * @param int $postId 文章 ID
     * @param int $incrementBy 增加的數量
     * @return bool
     */
    public function incrementViewsCount(int $postId, int $incrementBy): bool
    {
        return Post::where('id', $postId)->increment('views_count', $incrementBy);
    }
}