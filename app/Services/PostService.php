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
        // 簡潔，Laravel 會自動選擇當前 guard
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

    /**
     * 刪除文章
     *
     * @param int $id 文章 ID
     * @return bool
     * @throws AuthorizationException
     * @throws ModelNotFoundException
     */
    public function deletePost(int $id): bool
    {
        $post = $this->postRepository->findPostById($id);

        // 使用 Policy 檢查權限
        if (Gate::denies('delete', $post)) {
            throw new AuthorizationException('你沒有權限刪除該文章');
        }

        return $this->postRepository->deletePost($post);
    }

    /**
     * 更新文章狀態
     *
     * @param int $id 文章 ID
     * @param int $status 新的狀態
     * @return Post
     * @throws AuthorizationException
     * @throws ModelNotFoundException
     * @throws \Exception 當狀態轉換不合法時
     */
    public function updatePostStatus(int $id, int $status): Post
    {
        $post = $this->postRepository->findPostById($id);

        // 使用 Policy 檢查權限（使用 update 權限）
        if (Gate::denies('update', $post)) {
            throw new AuthorizationException('你沒有權限修改該文章');
        }

        // 驗證狀態轉換規則
        $currentStatus = $post->status;

        // 不允許狀態保持不變
        if ($currentStatus === $status) {
            throw new \Exception('新狀態與目前狀態相同', 422);
        }

        // 狀態轉換規則：
        // 草稿(1) → 只能變成發布(2)
        // 發布(2) → 只能變成隱藏(3)
        // 隱藏(3) → 只能變成發布(2)
        $validTransitions = [
            1 => [2], // 草稿只能變成發布
            2 => [3], // 發布只能變成隱藏
            3 => [2], // 隱藏只能變成發布
        ];

        if (!in_array($status, $validTransitions[$currentStatus])) {
            $statusNames = [1 => '草稿', 2 => '發布', 3 => '隱藏'];
            throw new \Exception(
                "不允許將文章從「{$statusNames[$currentStatus]}」狀態變更為「{$statusNames[$status]}」狀態",
                422
            );
        }

        $this->postRepository->updatePostStatus($post, $status);

        return $post->fresh()->load('user');
    }
}