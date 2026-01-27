<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 檢查是否已存在角色資料
        if (Role::count() > 0) {
            $this->command->warn('角色資料已存在，跳過建立。');
            return;
        }

        // 建立預設角色
        Role::create([
            'name' => Role::ADMIN,
            'display_name' => '管理者',
            'description' => '系統管理員，擁有所有權限',
        ]);

        Role::create([
            'name' => Role::USER,
            'display_name' => '一般使用者',
            'description' => '一般註冊使用者',
        ]);

        $this->command->info('角色資料建立成功！');
    }
}
