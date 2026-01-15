<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\UserService;
use App\Repositories\UserRepository;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Exception;
use Mockery;

/**
 * UserService 單元測試
 *
 * 注意：這個測試雖然在 Unit 資料夾，但實際上是輕量級的整合測試
 * 因為它使用了真實的 Hash 和 JWTAuth，只 mock 了 Repository
 *
 * 為什麼不是純單元測試？
 * - 避免 Facade Mock 污染其他測試
 * - 保持測試的穩定性和可維護性
 * - UserService 的核心邏輯是與 Repository 的互動，這才是我們要測試的重點
 */
class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 測試：登入成功時應該回傳 token 和使用者資料
     *
     * 策略：只 mock Repository，其他用真實環境
     */
    public function test_login_returns_token_when_credentials_are_valid(): void
    {
        // === Arrange（準備）===

        // 1. 建立一個真實的使用者（存入測試資料庫）
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password123',  // Model 會自動 hash
        ]);

        // 2. Mock UserRepository - 只 mock Repository，不 mock Facade
        $mockRepository = Mockery::mock(UserRepository::class);
        $mockRepository->shouldReceive('findByEmail')
            ->once()
            ->with('test@example.com')
            ->andReturn($user);

        // 3. 建立 UserService，注入 mock 的 Repository
        $userService = new UserService($mockRepository);

        // === Act（執行）===
        $result = $userService->login([
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        // === Assert（斷言）===
        $this->assertIsArray($result);
        $this->assertArrayHasKey('token', $result);
        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('expires_in', $result);
        $this->assertNotEmpty($result['token']);  // token 不為空
        $this->assertEquals($user->id, $result['user']->id);
        $this->assertEquals(3600, $result['expires_in']);
    }

    /**
     * 測試：使用者不存在時應該拋出例外
     */
    public function test_login_throws_exception_when_user_not_found(): void
    {
        // Arrange
        $mockRepository = Mockery::mock(UserRepository::class);
        $mockRepository->shouldReceive('findByEmail')
            ->once()
            ->with('notfound@example.com')
            ->andReturn(null);

        $userService = new UserService($mockRepository);

        // Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('帳號或密碼錯誤');
        $this->expectExceptionCode(401);

        // Act
        $userService->login([
            'email' => 'notfound@example.com',
            'password' => 'password123',
        ]);
    }

    /**
     * 測試：密碼錯誤時應該拋出例外
     */
    public function test_login_throws_exception_when_password_is_wrong(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'correct-password',
        ]);

        $mockRepository = Mockery::mock(UserRepository::class);
        $mockRepository->shouldReceive('findByEmail')
            ->once()
            ->andReturn($user);

        $userService = new UserService($mockRepository);

        // Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('帳號或密碼錯誤');

        // Act
        $userService->login([
            'email' => 'test@example.com',
            'password' => 'wrong-password',  // 錯誤的密碼
        ]);
    }

    /**
     * 測試結束後清理 Mockery
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
