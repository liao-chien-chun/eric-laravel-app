<?php

namespace App\Policies;

use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ShortUrlPolicy
{
    /**
     * 判斷使用者是否可刪除短網址
     *
     * @param User $user
     * @param ShortUrl $shortUrl
     * @return Response
     */
    public function delete(User $user, ShortUrl $shortUrl): Response
    {
        return $user->id === $shortUrl->user_id
            ? Response::allow()
            : Response::deny('無權限刪除該短網址');
    }
}
