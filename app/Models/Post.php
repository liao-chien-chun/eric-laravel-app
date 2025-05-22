<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    /**
     * 可被批量指派的欄位
     */
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'status'
    ];

    /**
     * 一篇文章屬於一個使用者
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
