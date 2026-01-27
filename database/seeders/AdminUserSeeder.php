<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 取得管理者角色
        $adminRole = Role::where('name', Role::ADMIN)->first();

        if (!$adminRole) {
            $this->command->error('請先執行 RolesTableSeeder！');
            return;
        }

        // 檢查是否已存在管理者帳號
        $existingAdmin = User::where('email', 'admin@example.com')->first();

        if ($existingAdmin) {
            $this->command->warn('管理者帳號已存在，跳過建立。');
            return;
        }

        // 建立管理者帳號
        User::create([
            'name' => '系統管理員',
            'email' => 'admin@example.com',
            'password' => 'admin123456',  // Model 會自動 hash
            'phone' => '0912345678',
            'role_id' => $adminRole->id,
        ]);

        $this->command->info('管理者帳號建立成功！');
        $this->command->info('Email: admin@example.com');
        $this->command->info('Password: admin123456');
    }
}
