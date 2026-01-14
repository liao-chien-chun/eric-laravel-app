<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

/**
 * 使用者註冊 API 測試
 *
 * 這個測試類別測試 POST /api/user/register 端點
 */
class UserRegistrationTest extends TestCase
{
    /**
     * RefreshDatabase trait 會在每個測試前重置資料庫
     * 確保測試之間互不影響
     */
    use RefreshDatabase;

    /**
     * 測試：使用者可以成功註冊
     *
     * 情境：提供所有必填欄位和有效資料
     * 預期：回傳 201 狀態碼，success 為 true，並建立使用者
     */
    public function test_user_can_register_successfully(): void
    {
        // Arrange（準備）：準備測試資料
        $userData = [
            'name' => '測試使用者',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '0912345678',
        ];

        // Act（執行）：發送 POST 請求到註冊 API
        $response = $this->postJson('/api/user/register', $userData);

        // Assert（斷言）：驗證結果
        $response->assertStatus(201)  // 檢查 HTTP 狀態碼是 201
            ->assertJson([
                'success' => true,
                'status' => 201,
                'message' => '註冊成功',
                // 驗證回傳的資料內容
                'data' => [
                    'name' => '測試使用者',
                    'email' => 'test@example.com',
                    'phone' => '0912345678',
                ],
            ])
            ->assertJsonStructure([
                'success',
                'status',
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'phone',
                    'created_at',
                    'updated_at'
                ],
            ]);

        // 驗證資料庫中確實建立了使用者
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => '測試使用者',
            'phone' => '0912345678',
        ]);

        // 驗證密碼有被正確加密（不應該是明文）
        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotEquals('password123', $user->password);
    }

    /**
     * 測試：缺少必填欄位時註冊失敗
     *
     * 情境：沒有提供 name 欄位
     * 預期：回傳 422 驗證錯誤
     */
    public function test_registration_fails_without_required_fields(): void
    {
        // Arrange：準備缺少 name 的資料
        $userData = [
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // Act：發送請求
        $response = $this->postJson('/api/user/register', $userData);

        // Assert：驗證回傳 422 驗證錯誤
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);  // 驗證 name 欄位有錯誤訊息

        // 確認資料庫中沒有建立使用者
        $this->assertDatabaseMissing('users', [
            'email' => 'test@example.com',
        ]);
    }

    /**
     * 測試：email 格式錯誤時註冊失敗
     *
     * 情境：提供無效的 email 格式
     * 預期：回傳 422 驗證錯誤
     */
    public function test_registration_fails_with_invalid_email(): void
    {
        // Arrange 準備
        $userData = [
            'name' => '測試使用者',
            'email' => 'invalid-email',  // 錯誤的 email 格式
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // Act 執行
        $response = $this->postJson('/api/user/register', $userData);

        // Assert 斷言
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * 測試：重複的 email 無法註冊
     *
     * 情境：使用已存在的 email 註冊
     * 預期：回傳 422 驗證錯誤
     */
    public function test_registration_fails_with_duplicate_email(): void
    {
        // Arrange：先建立一個使用者
        User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        // 嘗試用相同 email 註冊
        $userData = [
            'name' => '新使用者',
            'email' => 'existing@example.com',  // 重複的 email
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // Act 執行
        $response = $this->postJson('/api/user/register', $userData);

        // Assert 斷言
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * 測試：密碼太短時註冊失敗
     *
     * 情境：密碼少於 6 個字元
     * 預期：回傳 422 驗證錯誤
     */
    public function test_registration_fails_with_short_password(): void
    {
        // Arrange 準備
        $userData = [
            'name' => '測試使用者',
            'email' => 'test@example.com',
            'password' => '12345',  // 只有 5 個字元
            'password_confirmation' => '12345',
        ];

        // Act 執行
        $response = $this->postJson('/api/user/register', $userData);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * 測試：密碼確認不一致時註冊失敗
     *
     * 情境：password 和 password_confirmation 不相同
     * 預期：回傳 422 驗證錯誤
     */
    public function test_registration_fails_when_password_confirmation_does_not_match(): void
    {
        // Arrange 準備
        $userData = [
            'name' => '測試使用者',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different_password',  // 不一致的密碼
        ];

        // Act 執行 
        $response = $this->postJson('/api/user/register', $userData);

        // Assert 斷言
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * 測試：phone 欄位是選填的
     *
     * 情境：不提供 phone 欄位
     * 預期：仍然可以成功註冊
     */
    public function test_phone_field_is_optional(): void
    {
        // Arrange：不包含 phone 欄位
        $userData = [
            'name' => '測試使用者',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // Act 執行
        $response = $this->postJson('/api/user/register', $userData);

        // Assert：應該成功註冊
        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'status',
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'phone',
                    'created_at',
                    'updated_at'
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }
}
