<?php 

namespace App\Repositories;

use App\Models\Post;

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
}