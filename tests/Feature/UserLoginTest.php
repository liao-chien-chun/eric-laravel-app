<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Role;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class UserLoginTest extends TestCase
{
    /**
     * RefreshDatabase trait 會在每個測試前重置資料庫
     * 確保測試之間互不影響
     */
    use RefreshDatabase;

    /**
     * 每個測試前執行
     * 建立必要的角色資料
     */
    protected function setUp(): void
    {
        parent::setUp();

        // 建立一般使用者角色
        Role::create([
            'name' => Role::USER,
            'display_name' => '一般使用者',
            'description' => '一般註冊使用者',
        ]);
    }

    /**
     * 測試：使用者可以成功登入
     * 
     * 情境：輸入所有正確欄位與資料
     * 預期：回傳 200 狀態碼，success 為 true，並取得 token
     */
    public function test_user_can_login_successfully(): void
    {
        // 先建立使用者（不要手動 hash，Model 的 casts 會自動處理）
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password123'  // 不要 Hash::make，讓 Model 自動處理
        ]);

        // Arrange 準備資料
        $loginData = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        // Act 執行
        $response = $this->postJson('/api/user/login', $loginData);

        // Assert 斷言
        $response->assertStatus(200) // 登入成功 200
            ->assertJson([
                'success' => true,
                'status' => 200,
                'message' => '登入成功',
                'data' => [
                    'token_type' => 'Bearer',
                    'expires_in' => 3600,
                    'user' => [
                        'email' => 'test@example.com'
                    ]
                ]
            ])
            ->assertJsonStructure([
                'success',
                'status',
                'message',
                'data' => [
                    'access_token',
                    'token_type',
                    'expires_in',
                    'user' => [
                        'id',
                        'name',
                        'email'
                    ]
                ]
            ]);

        // 確認 token 不為空
        $this->assertNotEmpty($response->json('data.access_token'));
    }

    /**
     * 測試: email 不存在時登入失敗
     * 
     * 情境：提供無效的 email 值
     * 預期：回傳 401 登入失敗
     */
    public function test_login_fails_with_email_not_exists()
    {
        // Arrange 準備登入資料
        $loginData = [
            'email' => 'notfound@example.com',
            'password' => 'password123'
        ];

        // Act 執行
        $response = $this->postJson('/api/user/login', $loginData);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'status' => 401,
                'message' => '帳號或密碼錯誤'
            ]);
    }

    /**
     * 測試：密碼錯誤時登入失敗
     * 
     * 情境：提供錯誤之密碼值
     * 預期：回傳 401 登入失敗
     */
    public function test_login_fails_with_wrong_password()
    {
        // 建立使用者（不要手動 hash）
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password123'  // Model 會自動 hash
        ]);

        $loginData = [
            'email' => 'test@example.com',
            'password' => '123456'
        ];

        $response = $this->postJson('/api/user/login', $loginData);

        // Assert 斷言
        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'status' => 401,
                'message' => '帳號或密碼錯誤'
            ]);
    }

    /**
     * 測試：缺少 email 時登入失敗
     * 情境：登入缺少 email 資料
     * 預期：回傳 422 錯誤
     */
    public function test_login_fails_without_email()
    {
        // Arrange
        $loginData = [
            'password' => 'password123'
        ];

        // Act
        $response = $this->postJson('/api/user/login', $loginData);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * 測試：缺少密碼時登入失敗
     * 情境：登入缺少密碼資料
     * 預期：回傳 422 錯誤
     */
    public function test_login_fails_without_password()
    {
        // Arrange
        $loginData = [
            'email' => 'test@example.com'
        ];

        // Act 
        $response = $this->postJson('/api/user/login', $loginData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * 測試：email 格式錯誤登入失敗
     * 情境：提供無效的 email 格式
     * 預期：回傳 422 錯誤
     */
    public function test_login_fails_with_invalid_email()
    {
        $loginData = [
            'email' => 'invalid-email',
            'password' => '123456'
        ];

        $response = $this->postJson('/api/user/login', $loginData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email'])
            ->assertJson([
                'success' => false,
                'status' => 422,
                'message' => '驗證失敗',
                'errors' => [
                    'email' => ['電子郵件格式不正確']
                ]
            ]);
    }

    /**
     * 測試：密碼太短時驗證失敗
     * 情境：輸入之密碼太短
     * 預期：回傳 422 錯誤
     */
    public function test_login_fails_with_short_password()
    {
        $loginData = [
            'email' => 'test@example.com',
            'password' => '12345' // 只有五個字元
        ];

        $response = $this->postJson('/api/user/login', $loginData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }
}
