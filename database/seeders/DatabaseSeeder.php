<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. 先建立角色資料（基礎資料）
        $this->call(RolesTableSeeder::class);

        // 2. 再建立管理員帳號（依賴角色資料）
        $this->call(AdminUserSeeder::class);

        $this->command->info('所有種子資料建立完成！');
    }
}
