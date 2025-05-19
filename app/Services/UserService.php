<?php 

namespace App\Services;

use App\Repositories\UserRepository;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
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

    /**
     * 驗證使用者帳號密碼，回應 token 與 user
     * 
     * @param array{email: string, password: string} $credentials
     * @return array{
     *      token: string, 
     *      expires_in: int, 
     *      user: \App\Models\User
     * }
     * 
     * @throws Exception 驗證失敗拋出錯誤
     */
    public function login(array $credentials): array
    {
        // 根據 email 取出使用者資料
        $user = $this->userRepository->findByEmail($credentials['email']);

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new Exception('帳號或密碼錯誤', 401);
        }

        try {
            //  建立 JWT Token
            $token = JWTAuth::fromUser($user);
        } catch (JWTException $e) {
            throw new Exception('Token 建立失敗', 500);
        }

        return [
            'token' => $token,
            'expires_in' => auth('api')->factory()->getTTL() * 60, // 1 小時
            'user' => $user,
        ];
    }

    /**
     * 登出使用者
     * 
     * @return void 
     * @throws Exception 登出失敗拋出錯誤
     */
    public function logout(): void
    {
        try {
            auth()->logout(); 
        } catch (JWTException $e) {
            throw new Exception('Token 登出失敗');
        }
    }
}