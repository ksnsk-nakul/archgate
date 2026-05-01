<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;

class RoleManagementService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createSubadminRole(array $attributes, User $actor): Role
    {
        return Role::query()->create([
            'name' => $attributes['name'],
            'slug' => Str::slug('subadmin '.$attributes['name'].' '.Str::random(6)),
            'description' => $attributes['description'] ?? null,
            'is_system' => false,
            'created_by' => $actor->id,
        ]);
    }

    /**
     * @param  array<int, int>  $permissionIds
     */
    public function updatePermissions(Role $role, array $permissionIds): Role
    {
        $validPermissionIds = Permission::query()
            ->whereIn('id', $permissionIds)
            ->pluck('id')
            ->all();

        $role->permissions()->sync($validPermissionIds);

        User::query()
            ->whereHas('roles', fn ($query) => $query->whereKey($role->id))
            ->get()
            ->each
            ->clearRolesCache();

        return $role->load('permissions');
    }
}
