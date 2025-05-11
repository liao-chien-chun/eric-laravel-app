<?php 

namespace App\Services;

use App\Repositories\UserRepository;
use App\Models\User;
use Exception;

class UserService
{
    public function __construct(private UserRepository $userRepository) {}

    /**
     * 註冊新使用者
     * 
     * @param array{name: string, email: string, password: string, phone?: string|null} $data
     * @return User 
     * @throws Exception 註冊失敗拋出錯誤
     */
    public function register(array $data): User 
    {
        return $this->userRepository->createUser($data);
    }
}