<?php 

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Exception;

class UserRepository
{
    /**
     * 建立使用者資料
     *
     * @param array{name: string, email: string, password: string, phone?: string|null, role_id?: int|null} $data
     * @return User
     * @throws Exception 建立失敗時拋出例外
     */
    public function createUser(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],
            'role_id' => $data['role_id'] ?? null,
        ]);
    }

    /**
     * 使用 email 查詢使用者
     * @param string $email
     * @return object|User|\Illuminate\Database\Eloquent\Model|null
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
}
