<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Post $post): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * 判斷使用者是否可更新文章
     * 
     * @param User $user
     * @param Post $post 
     * @return bool
     */
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Post $post): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Post $post): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Post $post): bool
    {
        //
    }

    /**
     * 判斷使用者是否可以查看特定使用者的特定狀態文章
     * 草稿(1) 和隱藏(3) 只有本人可以查看
     *
     * @param User|null $user 當前登入使用者（可能為 null，未登入）
     * @param int $targetUserId 目標使用者 ID
     * @param int $status 文章狀態 (1:草稿, 2:發布, 3:隱藏)
     * @return bool
     */
    public function viewUserPosts(?User $user, int $targetUserId, int $status): bool
    {
        // 如果查看已發佈的文章(status=2)，任何人都可以看
        if ($status === 2) {
            return true;
        }

        // 如果查看草稿(1) 或隱藏(3)，只有本人可以看
        if ($status === 1 || $status === 3) {
            return $user && $user->id === $targetUserId;
        }

        // 其他情況不允許
        return false;
    }
}
