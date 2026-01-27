<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * 角色常數
     */
    const ADMIN = 'admin';  // 管理者
    const USER = 'user';    // 一般使用者

    /**
     * 可批量賦值的屬性
     */
    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];

    /**
     * 關聯：一個角色有多個使用者
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
