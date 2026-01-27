<?php

namespace App\Repositories;

use App\Models\Role;

class RoleRepository
{
    /**
     * 根據角色名稱查詢角色
     *
     * @param string $name
     * @return Role|null
     */
    public function findByName(string $name): ?Role
    {
        return Role::where('name', $name)->first();
    }

    /**
     * 取得所有角色
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllRoles()
    {
        return Role::all();
    }

    /**
     * 建立角色
     *
     * @param array $data
     * @return Role
     */
    public function createRole(array $data): Role
    {
        return Role::create($data);
    }
}
