<?php

namespace App\Livewire\Roles;

use App\Services\RoleService;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class RoleForm extends Component
{
    public ?int $roleId = null;

    public string $name = '';

    public array $permissions = [];

    public bool $selectAll = false;

    public function mount(?Role $role = null, ?RoleService $roles = null): void
    {
        abort_if(Gate::denies('access_user_management'), 403);

        if ($role && $role->exists) {
            $this->roleId = $role->id;
            $this->name = (string) $role->name;
            $this->permissions = ($roles ?? app(RoleService::class))->permissionNames($role);
        }
    }

    public function updatedSelectAll($value, RoleService $roles): void
    {
        $this->permissions = $value ? $roles->allPermissionNames() : [];
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'permissions' => 'required|array',
        ];
    }

    public function save(RoleService $roles)
    {
        abort_if(Gate::denies('access_user_management'), 403);

        $this->validate();

        if ($this->roleId) {
            $roles->update($this->roleId, $this->name, $this->permissions);

            session()->flash('success', trans('user.role-updated'));
        } else {
            $roles->create($this->name, $this->permissions);

            session()->flash('success', trans('user.role-created'));
        }

        return redirect()->route('roles.index');
    }

    public function render()
    {
        return view('livewire.roles.role-form');
    }
}
