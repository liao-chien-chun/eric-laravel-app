<?php 

namespace App\Services;

use App\Repositories\PostRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;

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
}