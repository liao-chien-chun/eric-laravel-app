<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'user_id',
        'content',
    ];

    /**
     * 一個留言屬於一個使用者
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 一個留言屬於一篇文章
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
