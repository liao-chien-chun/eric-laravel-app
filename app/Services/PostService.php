<?php 

namespace App\Services;

use App\Models\Post;
use App\Repositories\PostRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate; // or use $this->authorize in Controller
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * 
 * Class PostService
 * 
 * 負責處理文章邏輯層的操作
 */
class PostService 
{
    public function __construct(private PostRepository $postRepository) {}

    /**
     * 建立文章
     * 
     * @param array $data 使用者輸入之文章資料
     * @return \App\Models\Post 新建立的文章資料
     */
    public function createPost(array $data): Post
    {
        // 加上目前使用者 id
        $data['user_id'] = Auth::id();

        $post = $this->postRepository->createPost($data);

        // 預載 user 關聯，避免 Resource 那邊變 null 或 lazy loading
        return $post->load('user');
    }

    /**
     * 更新文章
     * 
     * @param int $id 文章 ID
     * @param array $data 更新的資料
     * @return Post 
     * @throws AuthorizationException
     * @throws ModelNotFoundException
     * @throws \Exception 嘗試將已發布之文章改為草稿
     */
    public function updatePost(int $id, array $data): Post
    {
        // 查詢文章，找不到會自動拋出 ModelNotFoundException
        $post = $this->postRepository->findPostById($id);

        // 使用 Policy 檢查權限
        // 使用 Gate::authorize() 或 $this->authorize()
        // 當不通過時 Laravel 自動會拋出 AuthorizationException
        Gate::authorize('update', $post);

        // 限制 已發布之文章不能更改為草稿
        if ($post->status === 2 && isset($data['status']) && $data['status'] === 1) {
            throw new \Exception('已發布的文章不能更改為草稿狀態', 422);
        }

        $this->postRepository->updatePost($post, $data);

        return $post->load('user');
    }

    /**
     * 取得使用者的文章列表（分頁）
     *
     * @param int $userId 使用者 ID
     * @param int $status 文章狀態 (1:草稿, 2:發布, 3:隱藏)
     * @param int $perPage 每頁筆數，預設 15
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * @throws AuthorizationException 當沒有權限查看指定狀態的文章時
     */
    public function getUserPosts(int $userId, int $status, int $perPage = 15)
    {
        // 使用 Policy 檢查權限
        // 草稿(1) 和隱藏(3) 只有本人可以查看
        if (Gate::denies('viewUserPosts', [Post::class, $userId, $status])) {
            throw new AuthorizationException('你沒有權限查看此內容');
        }

        return $this->postRepository->getUserPosts($userId, $status, $perPage);
    }
}