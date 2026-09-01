<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Owns querying and persistence for roles and their permissions.
 */
class RoleService
{
    public function paginate(?string $search = null, int $perPage = 12): LengthAwarePaginator
    {
        return Role::query()
            ->with('permissions')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%');
            })
            ->latest()
            ->paginate($perPage);
    }

    public function findOrFail(int $id): Role
    {
        return Role::findOrFail($id);
    }

    /**
     * All permission names, used to power the "select all" toggle.
     *
     * @return array<int, string>
     */
    public function allPermissionNames(): array
    {
        return Permission::pluck('name')->all();
    }

    /**
     * Permission names attached to the given role.
     *
     * @return array<int, string>
     */
    public function permissionNames(Role $role): array
    {
        return $role->permissions->pluck('name')->all();
    }

    /**
     * @param  array<int, string>  $permissions
     */
    public function create(string $name, array $permissions): Role
    {
        $role = Role::create(['name' => $name]);
        $role->givePermissionTo($permissions);

        return $role;
    }

    /**
     * @param  array<int, string>  $permissions
     */
    public function update(int $id, string $name, array $permissions): Role
    {
        $role = Role::findOrFail($id);
        $role->update(['name' => $name]);
        $role->syncPermissions($permissions);

        return $role;
    }

    public function delete(int $id): void
    {
        Role::findOrFail($id)->delete();
    }
}
